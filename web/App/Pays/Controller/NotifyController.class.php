<?php
namespace Pays\Controller;
use Think\Controller;
class NotifyController extends Controller
{
    protected  $NotifyJson;
    protected  $QfConfig;
    protected  $UConfig;
    public function _initialize()
    {
        $this->NotifyJson=json_decode(file_get_contents("php://input"),true);
        $this->Notify = $_REQUEST;
        #威付通异步回调
        $this->WftNotify = $this->xmlctojson($GLOBALS['HTTP_RAW_POST_DATA']);
        #根据商户号 查询商户查询KEY
        $api=M('MchSellerAlleys')->where(array('mch_id'=>$this->Notify['r1_merchantNo']))->limit(1)->getField('api_rel');
        $this->api_rel=unserialize($api);
        $this->QueryKey=$this->api_rel['queryKey'];


        #威富通函数扩展
        Vendor('wft_sdk.Utils','','.class.php');
        Vendor('wft_sdk.RequestHandler','','.class.php');
        Vendor('wft_sdk.PayHttpClient','','.class.php');
        Vendor('wft_sdk.ClientResponseHandler','','.class.php');
        $this->resHandler = new \ClientResponseHandler();
        $this->reqHandler = new \RequestHandler();
        $this->pay = new \PayHttpClient();
        $this->Utils = new \Utils();

        #支付网关
        $GateUrl='https://pay.swiftpass.cn/pay/gateway';
        $this->reqHandler->setGateUrl($GateUrl);

        #富友机构参数
        $this->inst=array(
            'no'=>'30600001',
            'key'=>'780e821e750b43b3b24a74a1c8a50abf',
        );
        #富友网关
        $api_url='https://pay.lcsw.cn/lcsw/';
        $this->FyApi=array(
            'mch_ye'=>$api_url.'/merchant/100/withdraw/query',
            'mch_tx_fee'=>$api_url.'/merchant/100/withdraw/queryfee',
            'mch_tx'=>$api_url.'/merchant/100/withdraw/apply',
        );

        #前方好进配置信息
        $this->QfConfig=array(
            'Appcode'=>'1783D1FAF82A40E889444B46EB956278',
            'SignKey'=>'B48002D897154DF3A8E3E5ED63B46231',
        );

        #网联通道配置信息
        $this->UConfig=array(
            'orgId'=>'000165',
            'url'=>'http://all.buybal.com/api-front/unionpay.do',
        );
    }


    #网联异步
    public function u_notify_url(){
        rwlog('u_notify_url',$this->NotifyJson);
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->NotifyJson,JSON_UNESCAPED_UNICODE),
            'time_end' => strtotime($this->NotifyJson['payTime']),
        );
        $out_trade_no=$this->NotifyJson['orderNo'];
        $set=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->save($array);
        if($set){
            $res=self::u_order_rel($out_trade_no);
            if($res=='ok') {
                self::sendTemplateMessage($out_trade_no);
                die('SUCCESS');
            }else{
                $log=array(
                    'type'=>'u_notify_url',
                    'out_trade_no'=>$out_trade_no,
                    'msg'=>$res,
                );
                data_log('u_notify_url',$log);
            }
        }else{
            die('error');
        }
    }
    #网联同步
    public function u_return_url(){
        $out_trade_no=I('get.order_id');
        $rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        $store=Get_Store($rel['store_id']);
        #点击完成到支付页面
        $codes=M('MchCodes')->where(array('store_id' => $rel['store_id'],'mch_id'=>$rel['mid']))->getField('codes');
        $res=self::u_order_rel($out_trade_no);
        $assign=array(
            'status'=>$res,
            'total'=>number_format($rel['total_fee'],2),
            'mch_name'=>$store['name'],
            'time'=>date('Y-m-d H:i:s',$rel['createtime']),
            'order_id'=>$rel['out_trade_no'],
            'url'=>C('MA_DATA_URL').'/'.$codes
        );
        $this->assign($assign);
        $this->display('new_result');

    }


    #前方好进订单查询
    public function u_order_rel($oid){
        $order=M('MchOrders')->where(array('out_trade_no'=>$oid))->find();
        if(!$order){
            $info='无此支付订单信息';
        }else {
            #根据订单号查询
            $arr = array(
                'mchntId' => $order['mch_id'],#商户号
                'outOrderNo' => $order['out_trade_no'],
                'funCode' => '0006',
                'orgId' => $this->UConfig['orgId'],
            );
            $arr['signature'] = Upay_sign($arr);
            $data['reqJson'] = urlencode(json_encode($arr));
            //rwlog('reqJson', $data);
            $res = curl_calls($this->UConfig['url'], $data, false, false);
            $res = json_decode($res, true);
            if ($res['respCode'] == '200') {
                if ($res['tradeStatus'] == 'SUCCESS') {
                    #订单查询成功 根据结果更新数据库
                    $save_data = array(
                        'transaction_id' => $res['bankOrderNo'],
                        'status' => 1,
                        'trade_type' => $res['payType'],
                        'total_fee' => $res['transAmt'] / 100,
                        'time_end' => $order['createtime']+rand(1,9),
                    );
                    $map = array(
                        'out_trade_no' => $order['out_trade_no'],
                    );
                    M('MchOrders')->where($map)->save($save_data);
                    $info = 'ok';
                } else {
                    switch ($res['tradeStatus']) {
                        case 'WAIT_PAY':
                            $sInfo = 'WAIT_PAY：订单等待支付';
                            break;
                        case 'FAIL':
                            $sInfo = 'FAIL：订单支付失败';
                            break;
                        case 'REFUNDED':
                            $sInfo = 'REFUNDED：订单已退款';
                            break;
                        case 'CANCELED':
                            $sInfo = 'CANCELED：订单已撤销';
                            break;
                        default:
                            $sInfo = $res['tradeStatus'];
                            break;
                    }
                    $info = $sInfo;
                }
            } else {
                $info = '代码:' . $res['respCode'] . '提示:' . $res['respDesc'];
            }
        }
        return $info;
    }


    #前方好进异步
    public function qfpay_notify_url(){
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->NotifyJson,JSON_UNESCAPED_UNICODE),
        );
        $out_trade_no=$this->NotifyJson['out_trade_no'];
        $set=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->save($array);
        if($set){
            $res=self::qfpay_order_rel($out_trade_no);
            if($res=='ok') {
                self::sendTemplateMessage($out_trade_no);
                if(IsApiOrder($out_trade_no)){
                    send_notify($out_trade_no,'SUCCESS');
                }else {
                    die('SUCCESS');
                }
            }else{
                $log=array(
                    'type'=>'qfpay_notify_url',
                    'out_trade_no'=>$out_trade_no,
                    'msg'=>$res,
                );
                data_log('qfpay_notify_url',$log);
            }
        }else{
            die('error');
        }

    }

    #前方好进异步
    public function qfpay_return_url(){
        $out_trade_no=I('get.order_id');
        $rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        $store=Get_Store($rel['store_id']);
        #点击完成到支付页面
        $codes=M('MchCodes')->where(array('store_id' => $rel['store_id'],'mch_id'=>$rel['mid']))->getField('codes');
        $res=self::qfpay_order_rel($out_trade_no);
        $assign=array(
            'status'=>$res,
            'total'=>number_format($rel['total_fee'],2),
            'mch_name'=>$store['name'],
            'time'=>date('Y-m-d H:i:s',$rel['createtime']),
            'order_id'=>$rel['out_trade_no'],
            'url'=>C('MA_DATA_URL').'/'.$codes
        );
        $this->assign($assign);
        $this->display('new_result');
    }

    #前方好进订单查询
    public function qfpay_order_rel($oid){
        #根据订单号查询
        $order=M('MchOrders')->where(array('out_trade_no'=>$oid))->find();
        if(!$order){
            $info='无此支付订单信息!';
        }else {
            $arr = array(
                'out_trade_no' => $oid,#订单号
                'mchid' => $order['mch_id'],
            );
            $res = qf_curl_calls('https://openapi.qfpay.com/trade/v1/query', $arr, null, true, $this->QfConfig);
            $res = json_decode($res, true);
            $resData = $res['data'][0];
            if ($res['respcd'] == '0000') {
                if($resData) {
                    if ($resData['respcd'] == '0000') {
                        #订单查询成功 根据结果更新数据库
                        $save_data = array(
                            'transaction_id' => $resData['syssn'],
                            'status' => 1,
                            'trade_type' => $resData['pay_type'],
                            'total_fee' => $resData['txamt'] / 100,
                            'time_end' => strtotime($resData['txdtm']),
                        );
                        $map = array(
                            'out_trade_no' => $resData['out_trade_no'],
                        );
                        M('MchOrders')->where($map)->save($save_data);
                        $info = 'ok';
                    } else {
                        $info = '代码:' . $resData['respcd'] . '提示:' . $resData['resperr'];
                    }
                }else{
                    $info = '未知订单!请联系管理员!';
                }
            } else {
                $info = '代码:' . $res['respcd'] . '提示:' . $res['resperr'];
            }
        }
        return $info;
    }







    #富友商户异步
    public function fy_mch_notify(){
        $rel=json_decode(file_get_contents('php://input'),true);
        if($rel['result_code']=='01'&&$rel['return_code']=='01'){
            $arr=array(
                'load_status'=>1,
                'loading'=>$rel['return_msg'],
            );
        }else{
            $arr=array(
                'load_status'=>3,
                'loading'=>$rel['return_msg'],
            );
        }
        $where['mch_id']=$rel['merchant_no'];
        M('MchSellerAlleys')->where($where)->save($arr);
    }
    #富友异步
    public function fy_notify_url(){
        $rel=json_decode(file_get_contents('php://input'),true);
        //rwlog('fy_notify2_url', json_decode(file_get_contents('php://input'),true));
        $order_id=$rel['terminal_trace'];
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($rel,JSON_UNESCAPED_UNICODE),
        );
        $out_trade_no=$order_id;
        $set=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->save($array);
        if($set){
            $res=self::fy_order_rel($out_trade_no);
            if($res=='ok') {
                self::sendTemplateMessage($out_trade_no);
                if(fy_tx_time()) { #只有在这个时间内 09:00-22:00
                    self::fy_tx($out_trade_no);
                }
                $echo=array(
                    'return_code'=>'01',
                    'return_msg'=>'处理成功',
                );
                die(json_encode($echo,JSON_UNESCAPED_UNICODE));
            }else{
                $log=array(
                    'type'=>'fy_notify_url',
                    'out_trade_no'=>$out_trade_no,
                    'msg'=>$res,
                );
                data_log('ccb_notify_url',$log);
            }
        }else{
            $echo=array(
                'return_code'=>'02',
                'return_msg'=>'获取查询失败',
            );
            die(json_encode($echo,JSON_UNESCAPED_UNICODE));
        }
    }

    public function FyTx(){
        $out_trade_no=I('param.order_id');
        if($out_trade_no) {
            if (fy_tx_time()) { #只有在这个时间内 09:00-22:00
                $info = self::fy_tx($out_trade_no);
            } else {
                $info = '不在提现范围';
            }
        }
        dump($info);
    }
    #富友提现
    public function fy_tx($id){
        #根据订单号查询
        $order=M('MchOrders')->where(array('out_trade_no'=>$id,'status'=>1))->find();
        rwlog('fy_tx_data', $order);
        if($order) {
            $ye = self::fx_ye($order['mch_id']); //未结算余额
            //$total = $order['total_fee'] * 100;
            $total=$ye['not_settle_amt'];
            rwlog('fy_tx_data', $ye);
            //dump($ye);
            if ($ye && $ye['not_settle_amt'] >= 1000) { #成功获取余额后切余额大于10元进行下一步
                $tx_fee = self::fx_tx_fee($order['mch_id'], $total); #提现手续费
                rwlog('fy_tx_data', $tx_fee);
                #获取提现费成功
                if($tx_fee){
                    #发起提现
                    $data=array(
                        'inst_no' => $this->inst['no'], #机构编号，扫呗分配
                        'trace_no' => create_uuid(), #请求流水号，不带“_”的uuid
                        'merchant_no'=>$order['mch_id'],
                        'amt'=>$total,#提现金额
                        'fee_amt'=>$tx_fee['fee_amt'],#提现手续费
                        'apply_type'=>1,
                    );
                    $data['key_sign'] = self::fy_tx_sign($data); //签名
                    $res = ccb_curl_calls($this->FyApi['mch_tx'], $data);
                    $res = json_decode($res, true);
                    if($res['return_code']=='01'&&$res['result_code']=='01'){
                        $status=1;
                    }else {
                        $status = 0;
                    }
                     #提现操作数据
                    $tx_data = array(
                            'mch_id' => $order['mch_id'],
                            'out_trade_no' => $id,
                            'tx_order' => $data['trace_no'],
                            'tx_total' => $total / 100,
                            'tx_amt' => $tx_fee['fee_amt'] / 100,
                            'tx_ye' => json_encode($ye, JSON_UNESCAPED_UNICODE),
                            'tx_fee' => json_encode($tx_fee, JSON_UNESCAPED_UNICODE),
                            'tx_rel' => json_encode($res, JSON_UNESCAPED_UNICODE),
                            'tx_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                            'status' => $status,
                            'alleys' => 'FYpays',
                            'domain_auth' => $order['domain_auth'],
                            'ctime' => date('Y-m-d H:i:s'),
                    );

                    M('MchOrdersTx')->add($tx_data);

                    rwlog('fy_tx_data', $tx_data);



                }
            }
        }

    }
    #富友余额查询
    public function fx_ye($mch_id){
        $data=array(
            'inst_no' => $this->inst['no'], #机构编号，扫呗分配
            'trace_no' => create_uuid(), #请求流水号，不带“_”的uuid
            'merchant_no'=>$mch_id,
        );
        $data['key_sign'] = self::fy_tx_sign($data); //签名
        $res = ccb_curl_calls($this->FyApi['mch_ye'], $data);
        rwlog('fy_ye',$res);
        $res = json_decode($res, true);
        if($res['return_code']=='01'&&$res['result_code']=='01'){
            return $res;
        }else{
            return false;
        }
    }

    #富友提现费查询
    public function fx_tx_fee($mch_id,$amt){
        $data=array(
            'inst_no' => $this->inst['no'], #机构编号，扫呗分配
            'trace_no' => create_uuid(), #请求流水号，不带“_”的uuid
            'merchant_no'=>$mch_id,
            'amt'=>$amt,
        );
        $data['key_sign'] = self::fy_tx_sign($data); //签名
        $res = ccb_curl_calls($this->FyApi['mch_tx_fee'], $data);
        //rwlog('fx_tx_fee',$res);
        $res = json_decode($res, true);
        if($res['return_code']=='01'&&$res['result_code']=='01'){
            return $res;
        }else{
            return false;
        }

    }

    #富友提现操作接口
    public function fy_tx_sign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "artif_nm" != $k && "key_sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=$signPars.'key='.$this->inst['key'];
        $sign = md5($sign_data);
        return $sign;
    }


    #民生异步
    public function NewCcb_notify_url(){
         rwlog('NewCcb_notify_url',$this->Notify);
    }
    #民生异步
    public function ccb_notify_url(){
       // rwlog('ccb_notify_url',$this->Notify);
        //rwlog('ccb_notify_url',$this->WftNotify);

        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->Notify,JSON_UNESCAPED_UNICODE),
        );
        $out_trade_no=$this->Notify['orderNo'];
        $set=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->save($array);
        if($set){
            $res=self::ccb_order_rel($out_trade_no,'MSpays');
            if($res=='ok') {
                self::sendTemplateMessage($out_trade_no);
                die('SUCCESS');
            }else{
                $log=array(
                    'type'=>'ccb_notify_url',
                    'out_trade_no'=>$out_trade_no,
                    'msg'=>$res,
                );
                data_log('ccb_notify_url',$log);
            }
        }else{
            die('error');
        }

    }

    #异步回调  盛付通
    public function sft_notify_url(){
        rwlog('sft_notify_url',$this->Notify);
        rwlog('sft_notify_url',$this->WftNotify);
    }

    #异步回调 此处也要验证
    public function wlb_notify_url(){
        data_log('wlb_notify_url',$this->Notify);
        $order_id=$this->Notify['r2_orderNumber'];
        $data=array(
            'trxType'=>'OnlineQuery',#接口类型
            'r1_merchantNo'=>$this->Notify['r1_merchantNo'],#商户编号
            'r2_orderNumber'=>$order_id,
        );
        $data['sign']=$this->OuerySign($data);
        $res=$this->curl_res('http://real.izhongyin.com/middlepaytrx/online/query',$data,1);
        if($res){
            if($res['retCode']=='0000'&&$res['r8_orderStatus']=='SUCCESS'){
                $array = array(
                    'trade_type'=>$res['r5_business'],
                    'status' => 1,
                    'total_fee'=>$res['r3_amount'],
                    'time_end'=>strtotime($res['r7_completeDate']),
                    'notify_time'=>time(),
                    'notify_data'=>json_encode($this->Notify,JSON_UNESCAPED_UNICODE),
                );
                $set=M('MchOrders')->where(array('out_trade_no'=>$res['r2_orderNumber']))->save($array);
                if($set){
                    self::sendTemplateMessage($res['r2_orderNumber']);
                    die('SUCCESS');
                }else{
                    die('error');
                }
            }
        }else{
            die('error');
        }

    }


    #异步回调 此处也要验证
    public function e_notify_url(){
        data_log('epay_notify_url',$this->Notify);
        $order_id=$this->Notify['r2_orderNumber'];
        $data=array(
            'trxType'=>'OnlineQuery',#接口类型
            'r1_merchantNo'=>$this->Notify['r1_merchantNo'],#商户编号
            'r2_orderNumber'=>$order_id,
        );
        $data['sign']=$this->OuerySign($data);
        $res=$this->curl_res('http://real.izhongyin.com/middlepaytrx/online/query',$data,1);
        if($res){
            if($res['retCode']=='0000'&&$res['r8_orderStatus']=='SUCCESS'){
                $array = array(
                    'trade_type'=>$res['r5_business'],
                    'status' => 1,
                    'total_fee'=>$res['r3_amount'],
                    'time_end'=>strtotime($res['r7_completeDate']),
                    'notify_time'=>time(),
                    'notify_data'=>json_encode($this->Notify,JSON_UNESCAPED_UNICODE),
                );
                $set=M('MchOrders')->where(array('out_trade_no'=>$res['r2_orderNumber']))->save($array);
                if($set){
                    self::sendTemplateMessage($res['r2_orderNumber']);
                    die('SUCCESS');
                }else{
                    die('error');
                }
            }
        }else{
            die('error');
        }

    }


    #
    public function bos_notify_url(){
        //rwlog('wlb_notify_url',$this->WftNotify);
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->WftNotify,JSON_UNESCAPED_UNICODE),
        );
        $out_trade_no=$this->WftNotify['out_trade_no'];
        $set=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->save($array);
        if($set){
            $res=self::order_rel($out_trade_no,'Bospay');
            if($res=='ok') {
                self::sendTemplateMessage($out_trade_no);
                die('SUCCESS');
            }else{
                $log=array(
                  'type'=>'bos_notify_url',
                  'out_trade_no'=>$out_trade_no,
                  'msg'=>$res,
                );
                data_log('bos_notify_url',$log);
            }
        }else{
            die('error');
        }

    }


    #认证会员订单查询
    public function auth_bos_result(){
        $out_trade_no=I('get.out_trade_no');
        $domain_auth=M('MchUserAuth')->where(array('out_trade_no'=>$out_trade_no))->getField('domain_auth');
        #根据品牌获取mch信息
        $system=M('SystemConfig')->where(array('domain_auth'=>$domain_auth))->find();
        $auth=unserialize($system['auth_data']);
        //rwlog('auth',$auth);
        //rwlog('system',$system);
        //rwlog('domain',$domain_auth);
        $data_array = array(
            'service' => 'unified.trade.query',
            'out_trade_no' => $out_trade_no,
        );
        $this->reqHandler->setReqParams($data_array, array('method'));
        $this->reqHandler->setKey($auth['auth_mch_key']); //商户key
        $this->reqHandler->setParameter('mch_id', $auth['auth_mch_id']);//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = $this->Utils->toXml($this->reqHandler->getAllParameters());
        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);

        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            $res = $this->resHandler->getAllParameters();
            if ($this->resHandler->isTenpaySign()) {
                #查询订单成功
                if ($res['result_code'] == 0 && $res['status'] == 0) {
                    #订单查询成功 根据结果更新数据库
                    switch ($res['trade_state']) {
                        case 'SUCCESS':
                            $status = 1;
                            break;
                        case 'REFUND':
                            $status = 2;
                            break;
                        default:
                            $status = 0;
                            break;
                    }

                    $save_data = array(
                        'transaction_id' => $res['transaction_id'],
                        'out_transaction_id' => $res['out_transaction_id'],
                        'status' => $status,
                        'total' => $res['total_fee'] / 100,
                        'time_end' => strtotime($res['time_end']),
                    );
                    $map = array(
                        'out_trade_no' => $res['out_trade_no'],
                    );
                    M('MchUserAuth')->where($map)->save($save_data);
                    redirect(U('Pays/Reg/index'));
                } else {
                    $info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                    $this->error($info);
                }
            } else {
                $info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                $this->error($info);
            }
        } else {
            $info = '错误代码:' . $this->pay->getResponseCode() . '错误消息' . $this->pay->getErrInfo();
            $this->error($info);
        }
    }


    #认证会员异步回调
    public function auth_bos_notify_url(){
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->WftNotify,JSON_UNESCAPED_UNICODE),
        );
        rwlog('bos',$this->WftNotify);
        $out_trade_no=$this->WftNotify['out_trade_no'];
        $set=M('MchUserAuth')->where(array('out_trade_no'=>$out_trade_no))->save($array);
        if($set){
            #根据异步回调查询订单
            $domain_auth=M('MchUserAuth')->where(array('out_trade_no'=>$out_trade_no))->getField('domain_auth');
            #根据品牌获取mch信息
            $system=M('SystemConfig')->where(array('domain_auth'=>$domain_auth))->find();
            $auth=unserialize($system['auth_data']);
            //rwlog('auth',$auth);
            //rwlog('system',$system);
            //rwlog('domain',$domain_auth);
            $data_array = array(
                'service' => 'unified.trade.query',
                'out_trade_no' => $out_trade_no,
            );
            $this->reqHandler->setReqParams($data_array, array('method'));
            $this->reqHandler->setKey($auth['auth_mch_key']); //商户key
            $this->reqHandler->setParameter('mch_id', $auth['auth_mch_id']);//必填项，商户号，由威富通分配
            $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
            $this->reqHandler->createSign();//创建签名
            $data = $this->Utils->toXml($this->reqHandler->getAllParameters());
            $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);

            if ($this->pay->call()) {
                $this->resHandler->setContent($this->pay->getResContent());
                $this->resHandler->setKey($this->reqHandler->getKey());
                $res = $this->resHandler->getAllParameters();
                rwlog('bos',$res);

                if ($this->resHandler->isTenpaySign()) {
                    #查询订单成功
                    if ($res['result_code'] == 0 && $res['status'] == 0) {
                        #订单查询成功 根据结果更新数据库
                        switch ($res['trade_state']) {
                            case 'SUCCESS':
                                $status = 1;
                                break;
                            case 'REFUND':
                                $status = 2;
                                break;
                            default:
                                $status = 0;
                                break;
                        }

                        $save_data = array(
                            'transaction_id' => $res['transaction_id'],
                            'out_transaction_id' => $res['out_transaction_id'],
                            'status' => $status,
                            'total' => $res['total_fee'] / 100,
                            'time_end' => strtotime($res['time_end']),
                        );
                        $map = array(
                            'out_trade_no' => $res['out_trade_no'],
                        );
                        M('MchUserAuth')->where($map)->save($save_data);
                        die('SUCCESS');
                    } else {
                        $info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                        rwlog('not',$info);
                    }
                } else {
                    $info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                    rwlog('not1',$info);
                }
            } else {
                $info = '错误代码:' . $this->pay->getResponseCode() . '错误消息' . $this->pay->getErrInfo();
                rwlog('not2',$info);
            }
        }else{
            die('error');
        }
    }


    public function sendTemplateMessage($order_id){
		$cache_id='ac_'.$order_id;
		if(S($cache_id)!=true) {
			S($cache_id, true, 3600);
			R('Tasks/SendTemplate/sendTempMsg', [$order_id]);
		}
    }

    public function wlb_call_url(){
        dump(I('get.'));
    }


    #威富通订单页面回调信息
    public function result(){
        $out_trade_no=I('get.out_trade_no');
        $rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        //通道支付是否自定义返回页数据
        $module = A('Pays/P' .$rel['alleys']);
        $modules = method_exists($module,'ResultData');
        if ($modules) { //如果通道文件内存至此方法 直接使用通道方法
            R('Pays/P' .$rel['alleys']. '/ResultData');
        } else {
            $store = Get_Store($rel['store_id']);
            #点击完成到支付页面
            $codes = M('MchCodes')->where(array('store_id' => $rel['store_id'], 'mch_id' => $rel['mid']))->getField('codes');
            $res = self::order_rel($out_trade_no, 'Bospay');
            $assign = array(
                'status' => $res,
                'total' => number_format($rel['total_fee'], 2),
                'mch_name' => $store['name'],
                'time' => date('Y-m-d H:i:s', $rel['createtime']),
                'order_id' => $rel['out_trade_no'],
                'url' => C('MA_DATA_URL') . '/' . $codes
            );
            $this->assign($assign);
            $this->display('new_result');
        }
    }



    #订单查询接口
    public function order_rel($out_trade_no,$alley){
        #根据订单号查询
        $mid=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->getField('mid');
        if(!$mid){
            $info='无此支付订单信息!';
        }else {
            #根据mid取商户号和key
            $alleys = M('MchSellerAlleys')->where(array('cid' => $mid, 'alleys_type' => $alley))->field('mch_id,mch_key')->find();
            $data_array = array(
                'service' => 'unified.trade.query',
                'out_trade_no' => $out_trade_no,
            );
            $this->reqHandler->setReqParams($data_array, array('method'));
            $this->reqHandler->setKey($alleys['mch_key']); //商户key
            $this->reqHandler->setParameter('mch_id', $alleys['mch_id']);//必填项，商户号，由威富通分配
            $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
            $this->reqHandler->createSign();//创建签名
            $data = $this->Utils->toXml($this->reqHandler->getAllParameters());
            $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);

            if ($this->pay->call()) {
                $this->resHandler->setContent($this->pay->getResContent());
                $this->resHandler->setKey($this->reqHandler->getKey());
                $res = $this->resHandler->getAllParameters();
                if ($this->resHandler->isTenpaySign()) {
                    #查询订单成功
                    if ($res['result_code'] == 0 && $res['status'] == 0) {
                        #订单查询成功 根据结果更新数据库
                        switch ($res['trade_state']) {
                            case 'SUCCESS':
                                $status = 1;
                                break;
                            case 'REFUND':
                                $status = 2;
                                break;
                            default:
                                $status = 0;
                                break;
                        }

                        $save_data = array(
                            'transaction_id' => $res['transaction_id'],
                            'out_transaction_id' => $res['out_transaction_id'],
                            'status' => $status,
                            'trade_type' => $res['trade_type'],
                            'total_fee' => $res['total_fee'] / 100,
                            'time_end' => strtotime($res['time_end']),
                        );
                        $map = array(
                            'mch_id' => $res['mch_id'],
                            'out_trade_no' => $res['out_trade_no'],
                        );
                        M('MchOrders')->where($map)->save($save_data);
                        $info = 'ok';
                    } else {
                        $info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                    }
                } else {
                    $info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                }
            } else {
                $info = '错误代码:' . $this->pay->getResponseCode() . '错误消息' . $this->pay->getErrInfo();
            }
        }

        return $info;
    }


    #民生订单页面回调信息
    public function ccb_result(){
        /*$out_trade_no=I('get.out_trade_no');
        $total_fee=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->getField('total_fee');
        #根据订单号取结果
        $res=self::ccb_order_rel($out_trade_no,'MSpays');
        if($res=='ok'){
            $data_info='付款成功';
            $data_status='success';
        }else{
            $data_info=$res;
            $data_status='info';
        }
        if($total_fee){
            $total=$total_fee;
        }else{
            $total='00.00';
        }
        $assign=array(
            'out_trade_no'=>$out_trade_no,
            'data_info'=>$data_info,
            'data_status'=>$data_status,
            'total'=>$total
        );
        $this->assign($assign);
        $this->display('result');*/
        $out_trade_no=I('get.out_trade_no');
        $rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        $store=Get_Store($rel['store_id']);
        #点击完成到支付页面
        $codes=M('MchCodes')->where(array('store_id' => $rel['store_id'],'mch_id'=>$rel['mid']))->getField('codes');
        $res=self::ccb_order_rel($out_trade_no,'MSpays');
        $assign=array(
            'status'=>$res,
            'total'=>number_format($rel['total_fee'],2),
            'mch_name'=>$store['name'],
            'time'=>date('Y-m-d H:i:s',$rel['createtime']),
            'order_id'=>$rel['out_trade_no'],
            'url'=>C('MA_DATA_URL').'/'.$codes
        );
        $this->assign($assign);
        $this->display('new_result');
       // dump($res);
    }

    #民生订单页面回调信息
    public function fy_result(){
        $out_trade_no=I('get.out_trade_no');
        $total_fee=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->getField('total_fee');
        #根据订单号取结果
        $res=self::fy_order_rel($out_trade_no);
        if($res=='ok'){
            $data_info='付款成功';
            $data_status='success';
        }else{
            $data_info=$res;
            $data_status='info';
        }
        if($total_fee){
            $total=$total_fee;
        }else{
            $total='00.00';
        }
        $assign=array(
            'out_trade_no'=>$out_trade_no,
            'data_info'=>$data_info,
            'data_status'=>$data_status,
            'total'=>$total
        );
        $this->assign($assign);
        $this->display('result');
        // dump($res);
    }

    #富友查询接口
    public function fy_order_rel($out_trade_no){
        #根据订单号查询
        $mid=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        //dump($mid);
        if(!$mid){
            $info='无此支付订单信息!';
        }else {
            #根据mid取商户号和key
            $alleys = M('MchSellerAlleys')->where(array('cid' => $mid['mid'], 'alleys_type' => 'FYpays'))->field('mch_id,api_rel')->find();
            $api_rel=json_decode($alleys['api_rel'],true);
            $data_array = array(
                'pay_ver' =>'100',
                'pay_type'=>$mid['trade_type'],
                'service_id'=>'020',
                'merchant_no'=>$alleys['mch_id'],
                'terminal_id'=>$api_rel['terminal_id'],
                'terminal_trace'=>rand_out_trade_no(),
                'terminal_time'=>date('YmdHis'),
                'out_trade_no' => $mid['out_transaction_id'],
            );
            $data_array['key_sign'] = self::fy_pay_sign($data_array,$api_rel['access_token']);#签名
            $res = ccb_curl_calls('https://pay.lcsw.cn/lcsw/pay/100/query',$data_array,true);
            //rwlog('fy_query_data',$data_array);
            //rwlog('fy_query_res',$res);
            $res = json_decode($res, true);
            if($res['return_code']=='01'&&$res['result_code']=='01'){
                switch ($res['trade_state']) {
                    case 'SUCCESS':
                        $status = 1;
                        break;
                    case 'REFUND':
                        $status = 2;
                        break;
                    default:
                        $status = 0;
                        break;
                }
                $save_data = array(
                    'transaction_id' => $res['channel_trade_no'],
                    'out_transaction_id' => $res['out_trade_no'],
                    'status' => $status,
                    'trade_type' => $res['pay_type'],
                    'total_fee' => $res['total_fee'] / 100,
                    'time_end' => strtotime($res['end_time']),
                );
                $map = array(
                    'out_trade_no' => $out_trade_no,
                );
                M('MchOrders')->where($map)->save($save_data);
                $info = 'ok';
            }else{
                $info = '提示:'.$res['return_msg'];
            }
        }
        return $info;
    }


    #民生订单查询接口
    public function ccb_order_rel($out_trade_no,$alley){
        #根据订单号查询
        $mid=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->getField('mid');
        if(!$mid){
            $info='无此支付订单信息!';
        }else {
            #根据mid取商户号和key
            $alleys = M('MchSellerAlleys')->where(array('cid' => $mid, 'alleys_type' => $alley))->field('mch_id')->find();
            $data_array = array(
                'merNo' => $alleys['mch_id'],
                'orderNo' => $out_trade_no,
            );
            $data_array['signIn'] = self::ccb_pay_sign($data_array);#签名
            $res = curl_calls('http://scp.yufu99.com/scanpay-api/api/orderQuery20',$data_array);
            $res = json_decode($res, true);
            if($res['result']=='0000'){
                switch ($res['transStatus']) {
                    case 0:
                        $status = 1;
                        break;
                    case 5:
                        $status = 2;
                        break;
                    default:
                        $status = 0;
                        break;
                }

                $save_data = array(
                    'transaction_id' => $res['outOrderNo'],
                    'out_transaction_id' => $res['orgTransId'],
                    'status' => $status,
                    'trade_type' => $res['trade_type'],
                    'total_fee' => $res['amount'] / 100,
                    'time_end' => strtotime($res['payTime']),
                );
                $map = array(
                    //'mch_id' => $res['mch_id'],
                    'out_trade_no' => $res['orderNo'],
                );
                M('MchOrders')->where($map)->save($save_data);
                $info = 'ok';
            }else{
                $info = '错误代码'.$res['result'].'提示:'.$res['desc'];
            }

        }
        return $info;
    }


    #民生支付签名
    public function ccb_pay_sign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "signIn" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=rtrim($signPars,'&').'aeab358778b311e8acfdf0c3b670876d';
        $sign = strtoupper(md5($sign_data));
        return $sign;
    }

    #富友签名
    public function fy_pay_sign($data,$key){
        $signPars = "";
        // ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "access_token" != $k && "key_sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=$signPars.'access_token='.$key;
        $sign = md5($sign_data);
        return $sign;
    }


    //解析XML
    public function xmlctojson($str){
        $obj = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON,true);
        return $dJSON;
    }



    /*查询签名*/
    public function OuerySign($data){
        $signPars = "";
        foreach($data as $k => $v) { //拼接
            if("" != $v && "sign" != $k) {
                $this->out_data .=  $v . "#";
            }
        }
        $signPars .='#'.$this->out_data.$this->QueryKey ;
        $sign = md5($signPars); //加密
        return $sign;
    }



    public function m_in(){
        $wxType1=array('203','208','210','158','204','205');
        $wxType=rand_one($wxType1);
        #支付宝经营类目ID
        $alipayType1=array('2015050700000022','2015050700000037','2015091000058486','2015091000060134','2015062600009243');
        $alipayType= rand_one($alipayType1);
        $data=array(
            'channelName'=>'郑州讯龙软件科技有限公司',//渠道名称
            'channelNo'=>'C2534318996',//渠道编码
            'merchantName'=>'皮皮玩具店',//商户名称
            'merchantBillName'=>'皮皮玩具店',//签购单显示名称
            'installProvince'=>'河南省',//安装归属省
            'installCity'=>'郑州市',//安装归属市
            'installCounty'=>'金水区',//安装归属县（区）
            'operateAddress'=>'园田路25号',//经营地址
            'merchantType'=>'PERSON',//商户类型 ENTERPRISE -企业商户 INSTITUTION -事业单位商户 INDIVIDUALBISS -个体工商户  PERSON -个人商户
            'businessLicense'=>'',//营业执照号码
            'legalPersonName'=>'陈存龙',//法人姓名
            'legalPersonID'=>'41092219930201493X',//法人身份证号
            'merchantPersonName'=>'陈存龙',  //商户联系人姓名
            'merchantPersonPhone'=>'18637162652',//商户联系人电话
            'wxType'=>$wxType,//微信经营类目
            'wxT1Fee'=>'0.0025',//微信商户手T1续费
            'wxT0Fee'=>'0.0027',//微信商户手T0续费
            'alipayType'=>$alipayType,//支付宝经营类目
            'alipayT1Fee'=>'0.0025',//支付宝商户手T1续费
            'alipayT0Fee'=>'0.0027',//支付宝商户手T0续费
            'bankType'=>'TOPRIVATE',//结算账户性质 对公-TOPUBLIC 对私-TOPRIVATE
            'accountName'=>'陈存龙',//开户人姓名
            //'accountNo'=>'6227002430160277786',//开户账号
            'accountNo'=>self::encode('6227002430160277786'),//开户账号
            'bankName'=>'建设银行',//开户银行名（大行全称）
            'bankProv'=>'河南省',//开户行省
            'bankCity'=>'郑州市',//开户行市
            'bankBranch'=>'中国建设银行郑州文化路支行',//开户银行名称（精确到支行）
            'bankCode'=>'105491000410',//联行号
            //'creditCardNo'=>'',//信用卡认证
        );
        $data['sign']=$this->signs($data);
        $res=$this->curl_res('http://real.izhongyin.com/middlepayportal/merchant/in',$data);
        dump(serialize($res));
    }

    //数据加密 卡号类
    public function encode($data){
        return encrypt($data,'25F9w1Z2Yrw2atP21619V3Io');
    }

    //入网签名
    public function signs($data){
        $signPars = "";
        $data=array_filter($data);
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= 'UuLUr3P5ogN8f853bMqEA4K1Q0YJ59m1';
        $sign = strtoupper(md5($signPars)); //加密
        //rwlog('signs',$signPars);
        return $sign;
    }

    //提交给服务端
    public  function curl_res($url,$data,$type=null){
        ##过滤空数组
        $data=array_filter($data);
        ksort($data);
        if($type==1){ //GET形式提交
            $datas = self::datato($data);
            $set=$url.'?'.$datas;
            $res=curl_calls($set);
        }else {//POST
            $datas = json_encode($data, JSON_UNESCAPED_UNICODE);
            $res=curl_calls($url,$datas);
        }

        $res=json_decode(trim($res,chr(239).chr(187).chr(191)),true);
        return $res;
    }

    //数组拼接函数
    public function datato($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v) {
                $outdata .= $k . "=" . urlencode($v) . "&";
            }
        }
        $signPars .=substr($outdata,0,strlen($outdata)-1); //去除最后&
        return $signPars;
    }


    public function data_rel(){
        /*$data='a:7:{s:6:"desKey";s:24:"3YaTNFkUkWUnti9e7D5Kv2aw";s:10:"merchantNo";s:10:"B101064244";s:8:"queryKey";s:32:"rkEsRkJpWnpsxzukSD3rMN0V5r441QoC";s:8:"respCode";s:4:"0000";s:7:"respMsg";s:6:"成功";s:4:"sign";s:32:"DE2A22A946897BE5E202422010DF1A73";s:7:"signKey";s:32:"ChLVH6m7CMViG2hxXtveaYDipAsh8u7D";}';
        dump(unserialize($data));*/
        $array=array(
            "desKey"=> "s3Zu0BSVpRUOjglw4ZZ3vAS4",
            "merchantNo" => "B101659084",
            "queryKey" => "mESo8bszNNuRWuOgmlMHjHq5wEyVBU6q",
            "respCode" =>"0000",
            "respMsg" => "成功",
            "sign"=> "67C18D0315968D2B71E275AD0DBCA2DA",
           "signKey" => "GQQciJFqObr6ZUUgmWnUmxCyxUnIrUuQ",
        );
        dump(serialize($array));
    }


}