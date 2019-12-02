<?php
namespace Pays\Controller;
use Think\Controller;
class CardNotifyController extends Controller
{
    public function _initialize()
    {
        $this->Notify = I('post.');
        $this->NotifyJson=json_decode(file_get_contents("php://input"),true);
        #测试
        /*$this->ApiUrl="http://121.201.111.67:9999/payment-gate-web/gateway/api/backTransReq";
        $this->merNo='800440054111002';*/
        #正式
        $this->ApiUrl="https://gateway.chinacardpos.com/payment-gate-web/gateway/api/backTransReq";
        //$this->merNo='800410048162075';
        if($_GET['alley']){
            switch ($_GET['alley']){
                case 'Q1':
                    $this->merNo ='8004100481620001';
                    break;
                case 'Q2':
                    $this->merNo ='8004100481620002';
                    break;
            }
        }else{
            if($_GET['mer']){
                $this->merNo =$_GET['mer'];
            }else {
                $this->merNo = $this->Notify['merNo'];
            }
        }
        if($_GET['tid']){
            $this->productId=$_GET['tid'];
        }else{
            $this->productId='0203';
        }
        //0201-非垫资(T1),0203-垫资(D0)
        $this->orderNo=rand_out_trade_no();


        ####YF通道参数###
        $this->yf_api=array(
            'url'=>'http://wx.maibei365.com/xlzf_wx/api/merOrderQuery',
            'sign'=>'12f1f0935be5cc9f0b4899cb583866e1',
        );

    }


    #星洁快捷支付
    public function XJNotifyUrl(){
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->NotifyJson,JSON_UNESCAPED_UNICODE),
        );
        M('MchOrders')->where(array('out_trade_no'=>$this->NotifyJson['agentOrderNo']))->save($array);
        $res=self::xj_order($this->NotifyJson['agentOrderNo']);
        if($res=='ok') {
            self::sendTemplateMessage($this->NotifyJson['agentOrderNo']);
            die('success');
        }else{
            die($res);
        }
    }
    #星洁快捷支付
    public function XJTest(){
        $order=self::xj_order('201803101756491135494851565');
        dump($order);
    }

    #订单查询接口
    public function xj_order($out_trade_no){
        $order=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        if(!$order){
            $info='无此支付订单信息!';
        }else {
            $appId='11058970';
            $arr = array(
                "appId"=>$appId,
                "agentOrderNo"=>$out_trade_no,
            );
            $res=curl_calls('http://47.96.171.202:8010/api/v1.0/agent/'.$appId.'/order/'.$out_trade_no,json_encode($arr),1,true);
            $res=json_decode($res,true);
            if($res['data'][0]['state']==2||$res['data'][0]['state']==3||$res['data'][0]['state']==4){
                $status=1;
            }else{
                $status=$res['data']['state'];
            }
            if($res['isSuccess']=='true'&&$res['data']){
                $save_data = array(
                    'transaction_id'=>$res['data'][0]['orderNo'],
                    'trade_type'=>$res['data'][0]['state'],
                    'status' => $status,
                    'total_fee' => $res['data'][0]['totalFee'] / 100,
                    'time_end'=>strtotime($res['data'][0]['tradeTime']),
                    'attach'=>$res['data'][0]['stateMsg'],
                );
                $map = array(
                    'out_trade_no' => $out_trade_no,
                );
                M('MchOrders')->where($map)->save($save_data);
                $info = 'ok';
            }else{
                if(!$res['data']){
                    $info = '未知订单支付结果';
                }else {
                    $info = $res['message'] . '(' . $res['code'] . $res['isSuccess'] . ')';
                }
            }
        }
        return $info;
    }



    #汇卡支付
    public function HkNotifyUrl(){
        rwlog('HkNotifyUrl',$this->Notify);
        dump('HkNotifyUrl');
    }

    #通道订单显示界面r
    public function return_url(){
        $data=I('get.');
        #订单号
        $order_id=$data['oid'];
        $alley=$data['alley'];
        if($alley){
            switch ($alley){
                case 'Q1':
                    $mothed ='q_order_rel';
                    break;
                case 'Q2':
                    $mothed ='q_order_rel';
                    break;
                case 'YF':
                    $mothed ='yf_order';
                    break;
                case 'HK':
                    $mothed ='hk_order';
                    break;
                case 'XJ':
                    $mothed ='xj_order';
                    break;
            }
            $module = A('Pays/CardNotify');
            $modules = method_exists($module,$mothed);
            if(!$modules){
                $this->error('Alley参数有误!');
            }else {
                $res = $this->$mothed($order_id);
                $rel=M('MchOrders')->where(array('out_trade_no'=>$order_id))->find();
                $store=Get_Store($rel['store_id']);
                #点击完成到支付页面
                $codes=M('MchCodes')->where(array('store_id' => $rel['store_id'],'mch_id'=>$rel['mid']))->getField('codes');
                $assign=array(
                  'status'=>$res,
                  'total'=>number_format($rel['total_fee'],2),
                  'mch_name'=>$store['name'],
                  'time'=>date('Y-m-d H:i:s',$rel['createtime']),
                  'order_id'=>$rel['out_trade_no'],
                  'url'=>C('MA_DATA_URL').'/'.$codes
                );
                $this->assign($assign);
                //dump($res);
                $this->display();
            }
        }else{
            $this->error('参数出错!未获取类型!');
        }
    }

    #Q通道手动发起提现
    public function test(){
        $res=self::q_fee_tx(I('get.order_id'));
        die('OK');

    }


    #Q通道重新出款
    public function q_xun_rel_tx(){
        $oid=I('get.order_id');
        $res=M('MchOrdersTx')->where(array('out_trade_no'=>$oid,'status'=>array('in',array('1','P000'))))->count();
        if(!$res){//不存在则直接出款
            self::q_fee_tx($oid);
            $this->success('重新出款请求已成功发出!请查看当前代付提现列表最新数据结果');
            //dump($res);
        }else{
            $this->error('当前订单已存在代付中或代付成功的提现订单。无需再次提现');
        }

        //
    }


    /***YF 通道开始**/
    #异步
    public function yf_notifyUrl(){
       // rwlog('yf_notifyUrl',$this->Notify);
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($this->Notify,JSON_UNESCAPED_UNICODE),
        );
        M('MchOrders')->where(array('out_trade_no'=>$this->Notify['mer_order_no']))->save($array);
        $res=self::yf_order($this->Notify['mer_order_no']);
        if($res=='ok') {
            self::sendTemplateMessage($this->Notify['mer_order_no']);
            die('SUCCESS');
        }else{
            die($res);
        }
    }




    #前台通知
    public function yf_ReturnUrl(){
        //$res=self::yf_order('201710181546291781531011025');
        //dump($res);
        $data=I('get.');
        if($data['state']=='2'){
            $data_info='付款成功';
            $data_status='success';
        }else{
            $data_info=$data['msg'];
            $data_status='info';
        }
        if($data['money']){
            $total=number_format($data['money']/100,2);
        }else{
            $total='00.00';
        }
        $assign=array(
            'data_info'=>$data_info,
            'data_status'=>$data_status,
            'total'=>$total
        );
        $this->assign($assign);
        $this->display();
    }


    #订单查询接口
    public function yf_order($out_trade_no){
        $order=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        if(!$order){
            $info='无此支付订单信息!';
        }else {
            $arr = array(
                "mer_no"=>$order['mch_id'], #商户号
                "serial_no"=>date('YmdHis').rand('1111','9999'),
                "mer_order_no"=>$order['out_trade_no'], #订单号
                "is_order_list"=>'1', #是否查询子订单
            );
            $arr['sign']=self::yf_sign($arr);
            $res=curl_calls($this->yf_api['url'].'?'.http_build_query($arr));
            $res=json_decode($res,true);
            $order_data=json_decode($res['order_list'],true);
            if($res['resp_code']=='0000'&&$order_data[0]['status']=='2') { #表示待支付 验证码下发成功
                $save_data = array(
                    'transaction_id'=>$order_data[0]['order_no'],
                    'trade_type'=>$order_data[0]['channel'],
                    'status' => 1,
                    'total_fee' => $order_data[0]['amount'] / 100,
                    'time_end'=>time(),
                    'attach'=>$order_data[0]['remark'],
                );
                $map = array(
                    'out_trade_no' => $out_trade_no,
                );
                M('MchOrders')->where($map)->save($save_data);
                $info = 'ok';
            }else{
                $info='提示:'.$res['resp_msg'].'错误码'.$res['resp_code'];
            }
        }
        return $info;
    }

    #签名
    public function yf_sign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v && "sign" != $k) {
                $this->OutData .= $k . "=" . $v . "&";
            }
        }
        $signPars .=$this->OutData.'key='.$this->yf_api['sign'];
        //rwlog('yf_order',$signPars);
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }







    public function q_notifyUrl(){
        $signature=self::card_sign($this->Notify);
        if($this->Notify['signature']==$signature){
            $res=self::q_order_rel($this->Notify['orderNo']);
            if($res=='ok') {
                self::sendTemplateMessage($this->Notify['orderNo']);
                if(Qcard_tx_time()) { #只有在这个时间内 09:00-22:00
                    self::q_fee_tx($this->Notify['orderNo']);
                }
                die('SUCCESS');
            }else{
                die($res);
            }
        }else{
            die('Sign Error!');
        }
    }


    public function q_returnUrl(){
        die('test');
    }



    #提现结果异步通知
    public function TxNotifyUrl(){
        $order_id=$_POST['orderNo'];
        //rwlog('tx_notify_order',$order_id);
        #根据ID查询提现是否成功
        $arr = array(
            "requestNo"=>date('YmdHis').rand('1111','9999'),
            "version"=>"V1.0",
            "transId"=>"04",
            "merNo"=>$this->merNo,
            "orderDate"=>substr($order_id,0,8),
            "orderNo"=>$order_id,
        );
        $arr['signature']=self::card_sign($arr);
        $res=card_curl_post($this->ApiUrl,http_build_query($arr));
        parse_str($res,$res);
        //rwlog('tx_notify_res',$res);
        if($res['respCode']=='0000'&& $res['origRespCode'] == '0000') { #表示待支付 验证码下发成功
            $save_data = array(
                'status' => 1,
                'tx_fee' => json_encode($res,JSON_UNESCAPED_UNICODE),
            );
            $map = array(
                'tx_order' => $order_id,
            );
            M('MchOrdersTx')->where($map)->save($save_data);
            #根据提现ID取订单流水号
            $orderNo=M('MchOrdersTx')->where($map)->getField('out_trade_no');
            self::sendTxTemplateMessage($orderNo);
            $info = 'SUCCESS';
        }else{
            $info='ERROR 提示:'.$res['respDesc'].'错误码'.$res['respCode'];
        }

        die($info);
    }


    public function XunTxNotifyUrl(){
        rwlog('XunTxNotifyUrl',$_POST);
    }
    #讯分润
    public function q_fee_xun(){
        $ye=self::q_fee();
        $notifyUrl='http://www.xunmafu.com/CardApi/XunTxNotifyUrl';
        $arr = array(
            "requestNo" => date('YmdHis') . rand('1111', '9999'),
            "version" => "V1.0",
            'productId' =>  '0201',//0201-非垫资(T1),0203-垫资(D0)
            "transId" => "07",
            "merNo" => $this->merNo,
            "orderDate" => date('Ymd'),
            "orderNo" => $this->orderNo,
            'notifyUrl' => $notifyUrl,
            'transAmt' => '147700',
            'isCompay' => '0',
            //'phoneNo' => $seller['mch_bank_tel'],//代付银行手机号
            'customerName' => '陈存龙',//代付账户名
            'cerdType' => '01',
            'cerdId' => '41092219930201493X',#身份证号
            'accBankNo' => '105491000410',#代付开户行号
            'accBankName' => reload_banks('105491000410'),#代付开户行名称
            'acctNo' => '6227002430160277786',#代付账号
            //'note'=>'讯码付',#代付摘要
        );
        $arr['signature'] = self::card_sign($arr);
        $res = card_curl_post($this->ApiUrl, http_build_query($arr));
        //rwlog('tx_res',$res);
        parse_str($res, $res);
        dump($ye);
        dump($arr);
        dump($res);
    }

    #提现操作 代付
    public function q_fee_tx($id){
        $order=M('MchOrders')->where(array('out_trade_no'=>$id,'status'=>1))->find();
        $notifyUrl='http://www.xunmafu.com/CardApi/TxNotifyUrl';
        #判断是否代付过此订单
        $TX=M('MchOrdersTx')->where(array('out_trade_no'=>$id))->find();
        if($order&&$TX['status']!='P000'&&$TX['status']!='1') {
            $NoMid=array('14','28','939','3309');
            #计算金额
            $be=($order['total_fee']*$order['mch_rate'])/1000;
            #手续费
            #金额-手续费=最终金额
            if(in_array($order['mid'],$NoMid)){
                $tx_amt='1';
            }else {
                $tx_amt='2';
            }
            $transAmt = (bcsub($order['total_fee'], $be, 2) - $tx_amt) * 100;
            #根据商户ID取结算信息
            $map['alleys_type']=$order['alleys'];
            $map['cid']=$order['mid'];
            $map['domain_auth']=$order['domain_auth'];
            $seller=M('MchSellerCardAlleys')->where($map)->find();
            #余额是否够
            $ye=self::q_fee();
            //rwlog('ye',$ye);
            if($ye['status']==1&&$ye['total'] >=$transAmt) {
                $arr = array(
                    "requestNo" => date('YmdHis') . rand('1111', '9999'),
                    "version" => "V1.0",
                    'productId' =>  $this->productId,//0201-非垫资(T1),0203-垫资(D0)
                    "transId" => "07",
                    "merNo" => $this->merNo,
                    "orderDate" => date('Ymd'),
                    "orderNo" => $this->orderNo,
                    'notifyUrl' => $notifyUrl,
                    'transAmt' => $transAmt,
                    'isCompay' => '0',
                    //'phoneNo' => $seller['mch_bank_tel'],//代付银行手机号
                    'customerName' => $seller['mch_bank_name'],//代付账户名
                    'cerdType' => '01',
                    'cerdId' => $seller['mch_card_id'],#身份证号
                    'accBankNo' => $seller['mch_linkbnk'],#代付开户行号
                    'accBankName' => reload_banks($seller['mch_linkbnk']),#代付开户行名称
                    'acctNo' => $seller['mch_bank_cid'],#代付账号
                    //'note'=>'讯码付',#代付摘要
                );
                $arr['signature'] = self::card_sign($arr);
                $res = card_curl_post($this->ApiUrl, http_build_query($arr));
                //rwlog('tx_res',$res);
                parse_str($res, $res);
            }
            #提现操作数据
            $tx_data = array(
                'mch_id' => $order['mch_id'],
                'out_trade_no' => $id,
                'tx_order' => $arr['orderNo'],
                'tx_total' => $transAmt / 100,
                'tx_amt' => $tx_amt+$be,
                'tx_ye' => json_encode($ye, JSON_UNESCAPED_UNICODE),
                'tx_fee' => '',
                'tx_rel' => json_encode($res, JSON_UNESCAPED_UNICODE),
                'tx_data' => json_encode($arr, JSON_UNESCAPED_UNICODE),
                'status' => $res['respCode'],
                'alleys' => $order['alleys'],
                'domain_auth' => $order['domain_auth'],
                'ctime' => date('Y-m-d H:i:s'),
            );
            M('MchOrdersTx')->add($tx_data);
        }
    }


    #余额查询 1成功 0 失败  单位:分
    public function q_fee(){
        $arr = array(
            "requestNo"=>date('YmdHis').rand('1111','9999'),
            "version"=>"V1.0",
            "transId"=>"09",
            "merNo"=>$this->merNo,
        );
        $arr['signature']=self::card_sign($arr);
        $res=card_curl_post($this->ApiUrl,http_build_query($arr));
        parse_str($res,$res);
        if($res['respCode']=='0000'){
            $info=array(
                'status'=>1,
                'msg'=>$res['respDesc'],
                'total'=>$res['avaBal'],
            );
        }else{
            $info=array(
                'status'=>0,
                'msg'=>$res['respDesc']
            );
        }
        return $info;
    }


    #


    #查询代付订单
    public function q_tx_order_rel(){
        $Db=M('MchOrdersTx');
        $orderNo=I('get.order_id');
        $arr = array(
            "requestNo"=>date('YmdHis').rand('1111','9999'),
            "version"=>"V1.0",
            "transId"=>"04",
            "merNo"=>$this->merNo,
            "orderDate"=>substr($orderNo,0,8),
            "orderNo"=>$orderNo,
        );
        $arr['signature']=self::card_sign($arr);
        $res=card_curl_post($this->ApiUrl,http_build_query($arr));
        parse_str($res,$res);
        $Status= $Db->where(array('tx_order'=>$orderNo))->getField('status');
        if($res['respCode']=='0000'){
            if($res['origRespCode'] == '0000'){//交易成功
                if($Status!=1){
                    $Db->where(array('tx_order'=>$orderNo))->save(array('status'=>1));
                }
                $code=1;
                $info=$res['origRespDesc'].'['.$res['origRespCode'].']';
            }elseif($res['origRespCode']=='P000'){
                $code=0;
                $info='当前订单出款中。。。通道方返回结果:'.$res['origRespDesc'].'['.$res['origRespCode'].']';
            }else{//交易失效或额度不足
                if($Status!=1&&$res['origRespCode']!='P000'){
                    $Db->where(array('tx_order'=>$orderNo))->save(array('status'=>'404','tx_fee'=>json_encode($res,JSON_UNESCAPED_UNICODE)));
                }
                $code=1;
                $info=$res['origRespDesc'].'['.$res['origRespCode'].']';
            }
        }else{
            $code=0;
            $info=$res['respDesc'].'['.$res['respCode'].']';
        }
        header('Content-type:text/json');
        echo json_encode(array('code'=>$code,'msg'=>'查询结果:'.$info),JSON_UNESCAPED_UNICODE);
        //dump($res);
    }
    


    #查询订单
    public function q_order_rel($out_trade_no){
        $order=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        if(!$order){
            $info='无此支付订单信息!';
        }else {
            $arr = array(
                "requestNo"=>date('YmdHis').rand('1111','9999'),
                "version"=>"V1.0",
                "transId"=>"04",
                "merNo"=>$this->merNo,
                "orderDate"=>substr($order['out_trade_no'],0,8),
                "orderNo"=>$order['out_trade_no'],
            );
            $arr['signature']=self::card_sign($arr);
            $res=card_curl_post($this->ApiUrl,http_build_query($arr));
            parse_str($res,$res);
            if($res['respCode']=='0000'&& $res['origRespCode'] == '0000') { #表示待支付 验证码下发成功
                $save_data = array(
                    'status' => 1,
                    'total_fee' => $res['transAmt'] / 100,
                    'time_end'=>time()
                );
                $map = array(
                    'out_trade_no' => $out_trade_no,
                );
                M('MchOrders')->where($map)->save($save_data);
                $info = 'ok';
            }else{
                $info='提示:'.$res['respDesc'].'错误码'.$res['respCode'];
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
                'type'=>'tx_card'
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


    public function card_sign($data) {
        unset($data['signature']);
        $data=self::datato($data);
        //读取私钥文件
        $priKey = file_get_contents(getcwd().'/Cert/QCARD/'.$this->merNo.'_prv.pem');
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);
        //释放资源
        openssl_free_key($res);
        return base64_encode($sign);
    }

    //数组拼接函数
    public function datato($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v) {
                $outdata .= $k . "=" . $v . "&";
            }
        }
        $signPars .=substr($outdata,0,strlen($outdata)-1); //去除最后&
        return $signPars;
    }







}