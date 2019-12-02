<?php
namespace Pays\Controller;
use Think\Controller;
class SftNotifyController extends Controller
{
    public function _initialize()
    {
        $this->Notify = I('post.');
        #网关信息
        $ApiUrl='https://mpospro.shengpay.com';
        $this->ApiUrl=array(
            'Query'=>$ApiUrl.'/mpos-runtime/command/merchant/account/query', //账户查询
            'Withdraw'=>$ApiUrl.'/mpos-runtime/command/merchant/withdraw',//实时出款
            'WithdrawStatus'=>$ApiUrl.'/mpos-runtime/command/merchant/withdraw/statusQuery',//出款状态查询
            'OrderQuery' =>$ApiUrl.'/mpos-runtime/command/pay/orderquery',  //支付订单查询
        );
        #服务商配置
        $this->api=array(
            'channelID'=>'0000000',
            'MacKey'=>'LXQZ20171109aeieqlmce381eqllcX',
        );
        $this->ext_data=json_decode(htmlspecialchars_decode($this->Notify['extData']),true);
    }



    #异步
    public function notify_url(){
        if ($this->Notify['respCode']=='00'){
            $notify_data=array(
                'Notify'=>$this->Notify,
                'ExtData'=>$this->ext_data
            );
            $array = array(
                'notify_time'=>time(),
                'notify_data'=>json_encode($notify_data,JSON_UNESCAPED_UNICODE),
                'transaction_id'=>$this->ext_data['authRef']
            );
            M('MchOrders')->where(array('out_trade_no'=>$this->Notify['merOrderId']))->save($array);
            $res=self::order_rel($this->Notify['merOrderId']);
            if($res=='ok') {
                self::sendTemplateMessage($this->Notify['merOrderId']);
                die('SUCCESS');
            }else{
                $rel=self::order_rel($this->Notify['merOrderId']);
                if($rel=='ok') {
                    self::sendTemplateMessage($this->Notify['merOrderId']);
                    die('SUCCESS');
                }else{
                    die($rel);
                }
            }
        }else{
            die('ERROR');
        }
    }


    #异步订单数据验签
    public function notify_data_sign(){

      $data=array(
          'txnType'=>$this->Notify['txnType'],
          'cur'=>$this->Notify['cur'],
          'amt'=>$this->Notify['amt'],
          'merchantId'=>$this->Notify['merchantId'],
          'terminalId'=>$this->Notify['terminalId'],
          'traceNo'=>$this->Notify['traceNo'],
          'batchNo'=>$this->Notify['batchNo'],
          'orderId'=>$this->Notify['orderId'],
          'txnTime'=>$this->Notify['txnTime'],
          'txnRef'=>$this->Notify['txnRef'],
          'respCode'=>$this->Notify['respCode'],
          'merOrderId'=>$this->Notify['merOrderId'],
          'shortPan'=>$this->Notify['shortPan'],
          'extData'=>htmlspecialchars_decode($this->Notify['extData']),
          'origTxnRef'=>$this->Notify['origTxnRef'],
      );
      rwlog('notify_data_sign',$data);

    }
    #出款提现结果-手动处理
    public function withdraw_tx(){
            $res=self::mch_money_status(I('get.order_id'));
            if($res['errorCode']=='18'){
                $order_id=json_decode($res['data'],true);
                #根据提现ID取订单流水号
                self::sendTxTemplateMessage($order_id['orderNo']);
                $save_data = array(
                    'status' => $res['errorCode'],
                    'tx_rel' => json_encode($res,JSON_UNESCAPED_UNICODE),
                );
                $map = array(
                    'tx_order' => $order_id['orderNo'],
                );
                M('MchSftTx')->where($map)->save($save_data);
                die(json_encode(array('status'=>'SUCCESS'),JSON_UNESCAPED_UNICODE));
            }else{
                die(json_encode($res,JSON_UNESCAPED_UNICODE));
            }
    }

    #出款提现结果
    public function withdraw_notify(){
        $mac=self::MacSign($this->Notify);
        if($this->Notify['mac']==$mac){
            $res=self::mch_money_status($this->Notify['orderNo']);
            if($res){
                $order_id=json_decode($res['data'],true);
                #根据提现ID取订单流水号
                self::sendTxTemplateMessage($order_id['orderNo']);
                $save_data = array(
                    'status' => $res['errorCode'],
                    'tx_rel' => json_encode($res,JSON_UNESCAPED_UNICODE),
                );
                $map = array(
                    'tx_order' => $order_id['orderNo'],
                );
                M('MchSftTx')->where($map)->save($save_data);
                die('SUCCESS');
            }
        }else{
            die('ERROR');
        }
        //rwlog('sft_withdraw_notify',$this->Notify);
    }


    public function mch_money_status($order){
        #根据提现订单号 取配置新
        $Tx=M('MchSftTx')->where(array('tx_order'=>$order))->find();
        $map=array(
          'cid'=>$Tx['cid'],
          'alleys_type'=>'Sftpays',
          'domain_auth'=>$Tx['domain_auth']
        );
        $seller=M('MchSellerAlleys')->where($map)->find();
        $api_rel=unserialize($seller['api_rel']);
        $arr=array(
            'channelID'=>$this->api['channelID'], //客户编号+
            'merchantNo'=>$api_rel['sdpMerchantNo'], //登录账号 手机号
            'loginName'=>$seller['mch_tel'],
            'orderNo'=>$Tx['tx_order'],
            'withdrawType'=>1,
        );
        $arr['mac']=self::MacSign($arr);
        $res=curl_calls($this->ApiUrl['WithdrawStatus'],$arr);
        $res=json_decode($res,true);
        return $res;
    }


    #测试
    public function test(){
        //self::sendTxTemplateMessage('201711141545461540979956979');
        //$res=self::order_rel('201711111459161884525697971');
        /*$res=array (
            'errorCode' => '18',
            'errorMsg' => '出款成功',
            'merchantNo' => '11245360',
            'orderNo' => '201711131051181932545198989',
            'withdrawType' => '1',
            'mac' => '08B0675D454438DEF0A54834B9AC324B',
        );
        $mac=self::MacSign($res);
        dump($mac);
        dump($res);*/

    }


    public function return_url(){
        $out_trade_no=I('get.order_id');
        $rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        $store=Get_Store($rel['store_id']);
        #点击完成到支付页面
        $codes=M('MchCodes')->where(array('store_id' => $rel['store_id'],'mch_id'=>$rel['mid']))->getField('codes');
        $res=self::order_rel($out_trade_no);
        $assign=array(
            'status'=>$res,
            'total'=>number_format($rel['total_fee'],2),
            'mch_name'=>$store['name'],
            'time'=>date('Y-m-d H:i:s',$rel['createtime']),
            'order_id'=>$rel['out_trade_no'],
            'url'=>C('MA_DATA_URL').'/'.$codes
        );
        $this->assign($assign);
        $this->display('Notify/new_result');
    }






    #订单查询
    public function order_rel($out_trade_no){
        $order=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        if(!$order){
            $info='无此支付订单信息!';
        }else {
            $arr=array(
                'signType'=>'MD5',
                'channelID'=>$this->api['channelID'], //客户编号+
                'charSet'=>'utf8',
                'outOrderNo'=>'S'.rand_out_trade_no(),#外部订单号
                'txnTime'=>date('YmdHis'),#交易时间
                'origOutOrderNo'=>$order['out_trade_no'],#原交易外部订单号
            );
            $arr['sign']=self::MacSign($arr);
            $res=curl_calls($this->ApiUrl['OrderQuery'],$arr);
            $res=json_decode($res,true);
            //rwlog('sft_res',$res);
            if($res) {
                if ($res['errorCode'] == '00' && $res['result'] == true) {
                    $res_data = json_decode($res['data'], true);
                    $save_data = array(
                        'status' => 1,
                        'trade_type' => $res_data['walletType'],
                        'total_fee' => $res_data['totalAmount'],
                        'time_end' => strtotime($res_data['txnTime']),
                    );
                    $map = array(
                        'out_trade_no' => $out_trade_no,
                    );
                    M('MchOrders')->where($map)->save($save_data);
                    $info = 'ok';
                } else {
                    $info = '提示:' . $res['errorMsg'] . '  错误码:' . $res['errorCode'];
                }
            }else{
                self::order_rel($out_trade_no);
            }
        }
        return $info;
    }


    #发送模板消息
    public function sendTemplateMessage($order_id){
        $data = array(
            'mc' => 'SendTemplate', #模块
            'ac' => 'Message' #方法
        );
        $res = ali_mns($data);
        if ($res['status'] == 1) {
            $arr=array(
                'order_id'=>$order_id,
            );
            $_data=array(
                'task_data'=>serialize($arr),
                'auth_code'=>domain_auth(),
                'rel'=>serialize($res)
            );
            $where['messageId']=$res['messageId'];
            $where['id']=$res['msn_id'];
            M('alimsn')->where($where)->save($_data);
        }
    }

    #发送到账模板消息
    public function sendTxTemplateMessage($order_id){
        $data = array(
            'mc' => 'SendTemplate', #模块
            'ac' => 'Message' #方法
        );
        $res = ali_mns($data);
        if ($res['status'] == 1) {
            $arr=array(
                'order_id'=>$order_id,
                'type'=>'sft_tx'
            );
            $_data=array(
                'task_data'=>serialize($arr),
                'auth_code'=>domain_auth(),
                'rel'=>serialize($res)
            );
            $where['messageId']=$res['messageId'];
            $where['id']=$res['msn_id'];
            M('alimsn')->where($where)->save($_data);
        }
    }


    public function MacSign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if(isset($v) && "mac" != $k && "idFrontImgStr" != $k && "idBackImgStr" != $k && "idHandImgStr" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=rtrim($signPars,'&').$this->api['MacKey'];
        $sign = strtoupper(md5($sign_data));
        return $sign;
    }





}