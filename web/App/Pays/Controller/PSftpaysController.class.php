<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class PSftpaysController extends Alleys_initBaseController {

    public function _initialize()
    {
        $this->api_data=unserialize($this->Mdata['api_rel']);
        #网关信息
        #https://mpospro.shengpay.com/
        $ApiUrl='https://mpospro.shengpay.com';
        $this->ApiUrl=array(
             'Query'=>$ApiUrl.'/mpos-runtime/command/merchant/account/query', //账户查询
             'OpenAccount'=>$ApiUrl.'/mpos-runtime/command/openAccount', //开户
             'StatusQuery'=>$ApiUrl.'/mpos-runtime/command/openAccount/statusQuery', //开户状态
             'Withdraw'=>$ApiUrl.'/mpos-runtime/command/merchant/withdraw',//实时出款
             'WithdrawStatus'=>$ApiUrl.'/mpos-runtime/command/merchant/withdraw/statusQuery',//出款状态查询
             'WithdrawInfoSet'=>$ApiUrl.'/mpos-runtime/command/merchant/withdraw/infoSet', //设置提现银行卡
             'MicroPay'=>$ApiUrl.'/mpos-runtime/command/pay/micropay', //扫码刷卡支付
             'QrCodePay' =>$ApiUrl.'/mpos-runtime/command/pay/qrCodePay',  //公众号支付
             'OrderQuery' =>$ApiUrl.'/mpos-runtime/command/pay/orderquery',  //支付订单查询
             'FeeChange' => $ApiUrl.'/mpos-runtime/command/merchant/feeChange', //费率变更
        );
        #服务商配置
        $this->api=array(
            'channelID'=>'11209799',
            'MacKey'=>'LXQZ20171109aeieqlmce381eqllcX',
        );
        $this->backUrl='http://' . $_SERVER['HTTP_HOST'] . '/SApi/return_url';



    }


    #微信公众号支付
    public function pay_wx_jsapi(){
        #临时处理
        $this->error('通道维护!请登录商户端切换到其他通道使用!');
        $order_id=$this->orderNum;
        $time=time();
        #返回给支付界面的参数
        $arr=array(
            'subMerchantNo'=>$this->api_data['mposMerchantNo'], //sub商户号
            'terminalId'=>$this->api_data['terminalId'], //商户终端号
            'totalAmount'=>sprintf("%.2f", $this->data['total']), //支付金额
            'txnTime'=>date('YmdHis',$time), //交易时间
            'signType'=>'MD5', //签名类型
            'channelID'=>$this->api['channelID'],
            'charSet'=>'utf8',
            'outOrderNo'=>$order_id,
            'backUrl'=>$this->backUrl.'/order_id/'.$order_id,
            'showPage'=>0, //是否展示确认页面 1：展示(默认),0:不展示
            'autoPay'=>1, //是否自动提交 1：自动，0：不自动(默认)
            //'cssUrl'=>urlencode('https://www.xunmafu.com/Source/api_pay.css'),
        );
        $arr['sign']=self::MacSign($arr);
        $data=array();
        foreach ($arr as $key=>$val){
            $data[]=array(
                'name'=>$key,
                'val'=>$val,
            );
        }

        $array=array(
            'mid'=>$this->data['sid'],
            'store_id'=>$this->data['id'],
            'agent_id'=>GetMchAid($this->data['sid']),
            'new' => serialize($arr),
            'data'=>serialize($this->data),
            'rel'=>json_encode($data,JSON_UNESCAPED_UNICODE),
            'createtime'=>$time,
            'mch_rate'=>$this->mch_rate,
            'mch_id'=>$this->Mdata['mch_id'],
            'service'=>'wx_jsapi',
            'out_trade_no'=>$order_id,
            'body'=>$this->Sdata['name'],
            'total_fee'=>sprintf("%.2f", $this->data['total']), //存数据库按照分进行统计
            'mch_create_ip'=>Get_Clienti_Ips(),
            'sub_openid'=>$this->data['openid'],
            'type'=>'T0',
            'alleys'=>'Sftpays',
            'domain_auth'=>domain_auth(),
            'is_raw'=>1,
        );
        $rel = M('mch_orders')->add($array);
        if($rel) {
            #存储以上订单信息到数据库
            $pay_data = array(
                'msg' => '订单创建成功',
                'type' => 'form',
                'url' => $this->ApiUrl['QrCodePay'],
                'data' => json_encode($data),
            );
            //rwlog('sft_wx', $arr);
            $this->success($pay_data);
        }else{
            $this->error('支付预下单失败!请重试!');
        }
    }

    #支付宝支付
    public function pay_ali_jsapi(){
        #临时处理
        $this->error('通道维护!请登录商户端切换到其他通道使用!');
        $order_id=$this->orderNum;
        $time=time();
        #返回给支付界面的参数
        $arr=array(
            'subMerchantNo'=>$this->api_data['mposMerchantNo'], //sub商户号
            'terminalId'=>$this->api_data['terminalId'], //商户终端号
            'totalAmount'=>sprintf("%.2f", $this->data['total']), //支付金额
            'txnTime'=>date('YmdHis',$time), //交易时间
            'signType'=>'MD5', //签名类型
            'channelID'=>$this->api['channelID'],
            'charSet'=>'utf8',
            'outOrderNo'=>$order_id,
            'backUrl'=>$this->backUrl.'/order_id/'.$order_id,
            'showPage'=>0, //是否展示确认页面 1：展示(默认),0:不展示
            'autoPay'=>1, //是否自动提交 1：自动，0：不自动(默认)
           // 'cssUrl'=>urlencode('https://www.xunmafu.com/Source/api_pay.css'),
        );
        $arr['sign']=self::MacSign($arr);
        //rwlog('ali_data',$arr);
        $data=array();
        foreach ($arr as $key=>$val){
            $data[]=array(
                'name'=>$key,
                'val'=>$val,
            );
        }

        $array=array(
            'mid'=>$this->data['sid'],
            'store_id'=>$this->data['id'],
            'agent_id'=>GetMchAid($this->data['sid']),
            'new' => serialize($arr),
            'data'=>serialize($this->data),
            'rel'=>json_encode($data,JSON_UNESCAPED_UNICODE),
            'createtime'=>$time,
            'mch_rate'=>$this->mch_rate,
            'mch_id'=>$this->Mdata['mch_id'],
            'service'=>'ali_jsapi',
            'out_trade_no'=>$order_id,
            'body'=>$this->Sdata['name'],
            'total_fee'=>sprintf("%.2f", $this->data['total']), //存数据库按照分进行统计
            'mch_create_ip'=>Get_Clienti_Ips(),
            'sub_openid'=>$this->data['openid'],
            'type'=>'T0',
            'alleys'=>'Sftpays',
            'domain_auth'=>domain_auth(),
            'is_raw'=>1,
        );
        $rel = M('mch_orders')->add($array);
        if($rel) {
            #存储以上订单信息到数据库
            $pay_data = array(
                'msg' => '订单创建成功',
                'type' => 'form',
                'url' => $this->ApiUrl['QrCodePay'],
                'data' => json_encode($data),
            );
            //rwlog('sft_wx', $arr);
            $this->success($pay_data);
        }else{
            $this->error('支付预下单失败!请重试!');
        }
    }

    #发起提现
    public function mch_withdraw(){
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        $api_rel=unserialize($alleys['api_rel']);
        $total=sprintf("%.2f", $this->data['tx_total']);
        $arr=array(
            'channelID'=>$this->api['channelID'], //客户编号+
            'merchantNo'=>$api_rel['sdpMerchantNo'], //登录账号 手机号
            'loginName'=> $alleys['mch_tel'],
            'orderNo'=>$this->orderNum,
            'withdrawType'=>1,
            'amount'=>$total,
            'callBackUrl'=>'http://www.xunmafu.com/SApi/withdraw_notify'
        );
        $arr['mac']=self::MacSign($arr);
        $res=curl_calls($this->ApiUrl['Withdraw'],$arr);
        $res=json_decode($res,true);
        if($res) {
            $db = M('MchSftTx');
            $order_arr = array(
                'mch_id' => $alleys['mch_id'],
                'cid' => $this->data['cid'],
                'tx_order' => $arr['orderNo'],
                'tx_total' => $total,
                'tx_data' => json_encode($arr, JSON_UNESCAPED_UNICODE),
                'tx_rel' => json_encode($res, JSON_UNESCAPED_UNICODE),
                'alleys' => 'Sftpays',
                'domain_auth' => $alleys['domain_auth'],
                'ctime' => date('Y-m-d H:i:s'),
                'status' => $res['errorCode'],
            );
            #是否存在
            $map = array(
                'cid' => $this->data['cid'],
                'tx_order' => $arr['orderNo'],
            );
            $count = $db->where($map)->count();
            if ($count) {
                $db->where($map)->save($order_arr);
            } else {
                $db->add($order_arr);
            }
            $this->success('提现结果:'.$res['errorMsg']);
        }else{
            $this->error('提现通信失败!请联系管理员!');
        }
    }


    #获取账户余额
    public function mch_balance(){
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->getField('api_rel');
        $api_rel=unserialize($alleys);
        $arr=array(
            'tradeType'=>'10000003',
            'channelID'=>$this->api['channelID'], //客户编号+
            'merchantId'=>$api_rel['sdpMerchantNo'], //登录账号 手机号
        );
        $arr['mac']=self::MacSign($arr);
        $res=curl_calls($this->ApiUrl['Query'],$arr);
        $res=json_decode($res,true);
        if($res['result']==true){
             $data=json_decode($res['data'],true);
             if($this->data['show']!=1) {
                 unset($data['channelFeelevel']);
                 unset($data['channelID']);
                 unset($data['merchantId']);
             }
             $this->success($data);
        }else{
            $this->error('获取失败!错误码:' . $res['errorCode'] . "   提示" . $res['errorMsg']);
        }
    }

    #开户进件
    public function mch_in(){
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys['rate']){
            $this->error('系统进行二次监测,此商户的('.$this->data['alleys'].')通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        }else {
            #获取费率标签
            $rate=SftRate($alleys['rate']);
            if($rate['status']==1) {
                $arr = array(
                    'msgType' => '10000004',
                    'channelID' => $this->api['channelID'], //客户编号+
                    //'loginName' => '17719860824', //登录账号 手机号
                    'loginName' => $alleys['mch_tel'], //登录账号 手机号
                    'realName' => $alleys['mch_card_name'], //注册实名
                    'idNumber' => $alleys['mch_card_id'],//身份证号
                    'idType' => '2', //1：一代身份证 2：二代身份证 3：临时身份证
                    'withdrawCardNo' => $alleys['mch_bank_cid'],//银行卡号
                    'idFrontImgStr' => sft_imgToBase64('.' . $alleys['mch_img_z']), //身份证正面照(base64)
                    'idBackImgStr' => sft_imgToBase64('.' . $alleys['mch_img_p']),//身份证背面照(base64)
                    'idHandImgStr' => sft_imgToBase64('.' . $alleys['mch_img_s']),//手持身份证照片(base64)
                    'witthdrawCardProvience' => sft_area($alleys['mch_bank_provice']), //提现银行卡省份编码
                    'witthdrawCardCity' => sft_area($alleys['mch_bank_citys']), //提现银行卡市编码
                    'bankCode' => reload_bank_area($alleys['mch_bank_list']), //提现银行卡银行编码
                    'bankName' => reload_bank($alleys['mch_bank_list']), //提现银行卡银行名称
                    'witthdrawCardBranch' => $alleys['mch_linkbnk'], //提现银行卡支行编码
                    'witthdrawCardBranchName' => reload_banks($alleys['mch_linkbnk']), //提现银行卡支行名称
                    'registerProvience' => sft_area($alleys['mch_bank_provice']), //注册省份编码
                    'registerCity' => sft_area($alleys['mch_citys']), //注册市编码
                    'registerSquare' => sft_area($alleys['mch_district']), //注册县/区编码
                    'address' => $alleys['mch_address'],//详细地址
                    //'extention' => '{"ZFB01":"TTF01","TX01":"TTF01"}',
                    'extention' => json_encode(array(
                        'ZFB01' => $rate['data'],
                        'TX01' => $rate['data']
                    ), JSON_UNESCAPED_UNICODE),//json格式，渠道编码：费率档，如{"BANKCARD":"SG01","TX01":"SG01"},如果为空默认开通BANKCARD渠道，如果不为空，开通的渠道列表以此为准
                    'storeName' => $alleys['mch_name'],#商户名称
                );
                $arr['mac'] = self::MacSign($arr);
                //rwlog('sft_mch_in',$arr);
                $res = curl_calls($this->ApiUrl['OpenAccount'], $arr);
                $res = json_decode($res, true);
                //rwlog('sft_mch_in',$res);
                if ($res) {
                    if ($res['errorCode'] == '00' || $res['errorCode'] == '04' || $res['errorCode'] == '12') {
                        $res_data = json_decode($res['data'], true);
                        $save = array(
                            'mch_id' => $res_data['mposMerchantNo'],
                            'load_status' => 1,
                            'status' => 1,
                            'api_rel' => serialize($res_data),
                        );
                        M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                        $this->success('商户进件开户成功!');
                    } else {
                        $this->error('进件失败!错误码:' . $res['errorCode'] . "   提示" . $res['errorMsg']);
                    }
                } else {
                    $this->error('进件接口通信失败!请重新发起进件!');
                }
            }else{
                $this->error($rate['msg']);
            }
        }
    }
    public function alter_bank(){
        $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        $api_data=unserialize($alleys['api_rel']);
        $arr = array(
            'msgType'=>'10000006',
            'channelID' => $this->api['channelID'],//渠道名称
            'loginName'=>$alleys['mch_tel'],//渠道编码
            'merchantID'=>$api_data['sdpMerchantNo'],#盛付通商户号
            'cardNumber'=>$this->data['mch_bank_cid'],#新银行卡号
            'witthdrawCardProvience' => sft_area($this->data['mch_bank_provice']), //提现银行卡省份编码
            'witthdrawCardCity' => sft_area($this->data['mch_bank_citys']), //提现银行卡市编码
            'bankCode' => reload_bank_area($this->data['mch_bank_list']), //提现银行卡银行编码
            'bankName' => reload_bank($this->data['mch_bank_list']), //提现银行卡银行名称
            'witthdrawCardBranch' => $this->data['mch_linkbnk'], //提现银行卡支行编码
            'witthdrawCardBranchName' => reload_banks($this->data['mch_linkbnk']), //提现银行卡支行名称
        );
        $arr['mac'] = self::MacSign($arr);
        $res = curl_calls($this->ApiUrl['WithdrawInfoSet'], $arr);
        $res = json_decode($res, true);
        if($res['errorCode']=='26'){
            $this->alter_bank_log(1,$res);
            #成功后 信息更新数据
            $save=array(
                'mch_bank_list'=>$this->data['mch_bank_list'],
                'mch_bank_cid'=>$this->data['mch_bank_cid'],
                'mch_bank_provice'=>$this->data['mch_bank_provice'],
                'mch_bank_citys'=>$this->data['mch_bank_citys'],
                'mch_linkbnk'=>$this->data['mch_linkbnk'],
                'mch_bank_tel'=>$this->data['mch_bank_tel'],
            );
            M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->save($save);
            $this->success('结算信息变更成功!新结算信息立即生效!',U('api_way',array('id'=>$this->data['cid'])));
        }else{
            $this->error('变更结算卡失败!错误码:' . $res['errorCode'] . "   提示" . $res['errorMsg']);
        }
    }

    #商户资料变更接口-变更费率
    public function alter_rate(){
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        #通道限制每天只允许变更一次
        $alter_rate_log=self::alters_rate_log(1,0);
        if($alter_rate_log){
            $this->error('每个商户每天只允许变更费率一次!此商户今天已变更一次!请明天再变更');
        }else {
            $old_rate=SftRate($this->data['old_rates']);
            $new_rate=SftRate($this->data['rates']);
            if($old_rate['status']==1&&$new_rate['status']==1) {
                $arr = array(
                    'channelID' => $this->api['channelID'],//渠道名称
                    'loginName' => $alleys['mch_tel'],//商户注册手机号
                    'feeChangelList' => json_encode(array(
                        'ZFB01' => $old_rate['data'].','.$new_rate['data'],
                        'TX01' => $old_rate['data'].','.$new_rate['data']
                    ), JSON_UNESCAPED_UNICODE),
                );
                $arr['mac'] = self::MacSign($arr);
                $res = curl_calls($this->ApiUrl['FeeChange'], $arr);
                $res = json_decode($res, true);
                if ($res['errorCode'] == '00') {
                       self::alters_rate_log(0,$res);
                       $this->alter_rate_log(1,$res);
                       $this->success($res['errorMsg']);
                }else{
                       $this->alter_rate_log(0,$res);
                       $this->error($res['errorMsg']);
                }
            }else{
                if($new_rate['status']!=1){
                    $info='新费率提示:'.$new_rate['msg'];
                }elseif ($old_rate['status']!=1){
                    $info='旧费率提示:'.$old_rate['msg'];
                }else{
                    $info='新费率提示:'.$new_rate['msg'].'旧费率提示:'.$old_rate['msg'];
                }
                $this->error('修改费率失败! '.$info);
            }
        }
    }



    #增加费率修改日志  第二天生效 $type=1 查询
    public function alters_rate_log($type,$rel){
        $db=M('AlterRateLog');
        if($type==1){
            $map=array(
              'alleys'=>$this->data['alleys'],
              'cid'=>$this->data['cid'],
              'date'=> date('Ymd')
            );
            $res=$db->where($map)->count();
        }else {
            $arr = array(
                'alleys' => $this->data['alleys'],
                'cid' => $this->data['cid'],
                'old_rate' => $this->data['old_rates'],
                'new_rate' => $this->data['rates'],
                'date' => date('Ymd'),
                'ctime' => date('YmdHis'),
                'status' => 0,
                'domain_auth' => domain_auth(),
                'rel'=>json_encode($rel,JSON_UNESCAPED_UNICODE)
            );
            $res=$db->add($arr);
        }
        if($res){
            return true;
        }else{
            return false;
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
        //rwlog('sft_sign',$sign_data);
        $sign = strtoupper(md5($sign_data));
        return $sign;
    }





}