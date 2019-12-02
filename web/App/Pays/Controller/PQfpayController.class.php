<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
#前方好进通道
#D1接口
class PQfpayController extends Alleys_initBaseController
{
    protected $QfConfig;
    protected $url;
    protected $api;
    public function _initialize()
    {
        #配置参数
        $this->QfConfig=array(
            'Appcode'=>'1783D1FAF82A40E889444B46EB956278',
            'SignKey'=>'B48002D897154DF3A8E3E5ED63B46231',
        );
       // $this->AppCode='E08CED37E7AE4C4B95345CA41B850EBC';
       // $this->SignKey='975EAC2D546F4356967CC131FA3FC3CF';
        $this->url='https://openapi.qfpay.com/';
        $this->api=array(
            'mch_in'=>'https://openapi-test.qfpay.com/mch/v1/signup',
            'mch_status'=>'https://openapi-test.qfpay.com/mch/v1/query',
            'pay'=>$this->url.'trade/v1/payment',
            'test_code'=>$this->url.'tool/v1/get_weixin_oauth_code',
            'test_openid'=>$this->url.'tool/v1/get_weixin_openid',
        );

    }


    #测试获取openid
    public function wx_test(){
        if(I('get.code')){
            $arr = array(
                'code' => I('get.code'),
            );
            //$arr['sign'] =self::data_sign($arr);
            $url=$this->api['test_openid'].'?'.http_build_query($arr);

            $res=self::get_curl_calls($url,self::data_sign($arr),true);
            rwlog('qf_openid',json_decode($res,true));
            dump($res);
            //redirect($url);
        }else {
            $arr = array(
                'app_code' => $this->AppCode,
                'redirect_uri' => 'http://www.xunmafu.com/Pays/PQfpay/wx_test',
            );
            $arr['sign'] =self::data_sign($arr);
            $url=$this->api['test_code'].'?'.http_build_query($arr);
            redirect($url);
            //dump($arr);
            //dump($url);
        }

    }



    #微信支付
    public function pay_wx_jsapi(){

        $order_id=$this->orderNum;
        $arr=array(
            'txamt'=>$this->data['total'] * 100,#金额
            'txcurrcd'=>'CNY',#币种 港币：HKD ；人民币：CNY
            'pay_type'=>'800207',#支付类型  微信 800207
            'out_trade_no'=>$order_id,#订单号
            'txdtm'=>date('Y-m-d H:i:s'),
            'sub_openid'=>$this->data['openid'],#用户OPENID
            'goods_name'=>$this->Sdata['name'],#商品名称
            'mchid'=>$this->Mdata['mch_id'],#子商户号，标识子商户身份，由钱方统一分配
        );

        $res=qf_curl_calls($this->api['pay'],$arr,null,true,$this->QfConfig);
        $res=json_decode($res,true);
        if($res['respcd']=='0000') {
            #跳转支付参数
            $test_arr = array(
                'mchntnm' => $this->Sdata['name'],
                'txamt' => $res['txamt'],#金额
                'goods_name' => $this->Sdata['name'],#商品名称
                'redirect_url' => 'http://'.$_SERVER['HTTP_HOST'].'/Api/qfpay_return_url?order_id=' . $res['out_trade_no'],#完成后跳转页面
                'package' => $res['pay_params']['package'],
                'timeStamp' => $res['pay_params']['timeStamp'],
                'signType' => $res['pay_params']['signType'],
                'paySign' => $res['pay_params']['paySign'],
                'appId' => $res['pay_params']['appId'],
                'nonceStr' => $res['pay_params']['nonceStr'],
            );
            #拼接跳转url
            $test_url = 'https://o2.qfpay.com/q/direct?' . http_build_query($test_arr);
            $array=array(
                'trade_type'=>$res['pay_type'],
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($arr),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'wx_jsapi',
                'out_trade_no'=>$arr['out_trade_no'],
                'body'=>$this->Sdata['name'],
                'total_fee'=>$this->data['total'], //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'sub_openid'=>$this->data['openid'],
                'type'=>'D1',
                'alleys'=>'Qfpay',
                'domain_auth'=>domain_auth(),
                'is_raw'=>1,
            );
            $rel=M('mch_orders')->add($array);
            if($rel){
                $pay_data=array(
                    'msg'=>'订单创建成功',
                    'type'=>'hurl',
                    'localurl'=>$test_url,
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else{
            $this->error($res['resperr'].'['.$res['respcd'].']');
        }
    }

    #支付宝支付
    public function pay_ali_jsapi(){
        $order_id=$this->orderNum;
        $arr=array(
            'txamt'=>$this->data['total'] * 100,#金额
            'txcurrcd'=>'CNY',#币种 港币：HKD ；人民币：CNY
            'pay_type'=>'800107',#支付类型  微信 800207
            'out_trade_no'=>$order_id,#订单号
            'txdtm'=>date('Y-m-d H:i:s'),
            'openid'=>$this->data['openid'],#用户OPENID
            'goods_name'=>$this->Sdata['name'],#商品名称
            'mchid'=>$this->Mdata['mch_id'],#子商户号，标识子商户身份，由钱方统一分配
        );
        $res=qf_curl_calls($this->api['pay'],$arr,null,true,$this->QfConfig);
        $res=json_decode($res,true);
        //rwlog('res',$res);
        //rwlog('res',$arr);
        if($res['respcd']=='0000'){
            $array=array(
                'trade_type'=>$res['pay_type'],
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($arr),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'ali_jsapi',
                'out_trade_no'=>$arr['out_trade_no'],
                'body'=>$this->Sdata['name'],
                'total_fee'=>$this->data['total'], //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'sub_openid'=>$this->data['openid'],
                'type'=>'D1',
                'alleys'=>'Qfpay',
                'domain_auth'=>domain_auth(),
                'is_raw'=>1,
            );
            $rel=M('mch_orders')->add($array);
            if($rel){
                $pay_data=array(
                    'msg'=>'订单创建成功',
                    'type'=>'js',
                    'pay_info'=>array(
                        'tradeNO'=>$res['pay_params']['tradeNO']
                    ),
                    'out_trade_no'=>$res['out_trade_no'],
                    'result'=>'/Api/qfpay_return_url/order_id/'.$res['out_trade_no'],
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else{
            $this->error($res['resperr'].'['.$res['respcd'].']');
        }
    }



    #商户状态查询
    public function mch_status(){
        /*$arr=array(
               'mchid'=>$this->data['mch_id']
        );
        $res = qf_curl_calls($this->api['mch_status'], $arr, false, true, $this->QfConfig);
        dump($res);*/
        $this->error('通道方暂不支持此查询');
    }

    #商户进件
    public function mch_in(){
        $qf_sale=qf_sale();
        if(!$qf_sale){
            $this->error('本渠道未配置业务ID,获取信息失败!请联系平台!');
        }else {
            $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
            if ($alleys) {
                /*if ($alleys['mch_bus_type'] == '个人') {
                    $this->error('当前通道不支持个人进件!目前只支持有营业执照企业或个体户!个人请用银联快捷!');
                }*/


                $bankData = self::QfBanks($alleys['mch_linkbnk']);
                if (!$bankData) {
                    $this->error('您选择的支行信息通道端不存在、请直接选择上一级分行或选择其它支行试试!');
                } else {
                    if(!$alleys['mch_industry']){
                        $this->error('当前商户未配置行业,请编辑商户行业信息后再试');
                    }

                    if(!$alleys['mch_img_m1']){
                        $this->error('门头照片不能为空');
                    }
                    if(!$alleys['mch_img_m2']){
                        $this->error('门店内景照片不能为空');
                    }
                    if(!$alleys['mch_img_bank']){
                        $this->error('银行卡照片不能为空');
                    }

                    if ($alleys['mch_bus_type'] == '个人') {
                        $UserType = 1;
                        $QyNumber = '';
                        $IdName = $alleys['mch_card_name'];
                        $IdNumber = $alleys['mch_card_id'];
                        $legalperson = $alleys['mch_card_name'];
                        //$type_id = array('324', '282', '244', '235', '210');
                        //$type_id = rand_one($type_id);
                        $type_id=$alleys['mch_industry'];
                    } elseif ($alleys['mch_bus_type'] == '企业') {
                        $UserType = 3;
                        $QyNumber = $alleys['qy_cid'];
                        $IdName = $alleys['qy_name'];
                        $IdNumber = $alleys['qy_fr_cid'];
                        $legalperson = $alleys['qy_fr_name'];
                        $type_id=$alleys['mch_industry'];
                        if($alleys['mch_bank_type']!='个人账户'){
                            $this->error('暂不支持企业结算账户');
                        }
                        //if($alleys['qy_fr_name']!=$alleys['mch_bank_name']){//法人和收款人不一致
                            if(!$alleys['mch_img_sqh']){
                                $this->error('授权函照片不能为空');
                            }
                            if(!$alleys['mch_img_auth_z']){
                                $this->error('授权人身份证照片(正面)不能为空');
                            }
                            if(!$alleys['mch_img_auth_p']){
                                $this->error('授权人身份证照片(反面)不能为空');
                            }
                       // }


                    } else {//个体户
                        $UserType = 2;
                        $QyNumber = $alleys['qy_cid'];
                        $IdName = $alleys['qy_name'];
                        $IdNumber = $alleys['qy_fr_cid'];
                        $legalperson = $alleys['qy_fr_name'];
                        $type_id=$alleys['mch_industry'];
                        if($alleys['qy_fr_name']!=$alleys['mch_bank_name']){//法人和收款人不一致
                            if(!$alleys['mch_img_sqh']){
                                $this->error('授权函照片不能为空');
                            }
                            if(!$alleys['mch_img_auth_z']){
                                $this->error('授权人身份证照片(正面)不能为空');
                            }
                            if(!$alleys['mch_img_auth_p']){
                                $this->error('授权人身份证照片(反面)不能为空');
                            }
                        }

                    }

                    switch ($alleys['mch_bank_provice']){
                        case '北京':
                            $mch_bank_provice='北京市';
                            break;
                        case '天津':
                            $mch_bank_provice='天津市';
                            break;
                        default:
                            $mch_bank_provice=$alleys['mch_bank_provice'];
                            break;
                    }



                    $rate = bcdiv($alleys['rate'], '1000', 4);
                    $arr = array(
                        'username' => $alleys['mch_tel'],#用户手机号
                        'usertype' => $UserType, //商户类型
                        'licensenumber' => $QyNumber,//企业个体工商号
                        'idnumber' => $IdNumber,#身份证号
                        'legalperson' => $legalperson,#用户姓名
                        'name' => $IdName,
                        'province' => $alleys['mch_provice'],#省份
                        'city' => $alleys['mch_citys'],#城市
                        'address' => $alleys['mch_district'] . $alleys['mch_address'],#地址
                        'shopname' => $alleys['mch_name'],#商户名称
                        'headbankname' => reload_bank($alleys['mch_bank_list']),#总行名
                        'bankuser' => $alleys['mch_bank_name'],#开户名
                        'bankaccount' => $alleys['mch_bank_cid'],#开户银行账户
                        'bankprovince' => $mch_bank_provice,#银行所属省
                        'bankcity' => $alleys['mch_bank_citys'],#银行所属市
                        'bankname' => $bankData['address'],#银行名称
                        'bankcode' => $bankData['banking'],#联行号
                        'banktype' => 1,#对公还是对私 银行卡类型，1为对私，2为对公
                        'shoptype_id' => $type_id,#行业ID
                        'bankmobile' => $alleys['mch_bank_tel'],#预留手机号
                        'tenpay_ratio' => $rate,#微信费率
                        'alipay_ratio' => $rate,#支付宝费率
                        'qqpay_ratio' => '0.0038',
                        'jdpay_ratio' => '0.006',
                        'debit_ratio' => '0.005',
                        'credit_ratio' => '0.006',
                        'salesman_mobile' => $qf_sale,#业务员手机号
                    );
                    rwlog('qf_mch_in',$arr);

                    if ($alleys['mch_bus_type'] == '个人') {
                        $file = array(
                            'idcardfront' => $alleys['mch_img_z'],#身份证正面
                            'idcardback' => $alleys['mch_img_p'],#身份证反面
                            'idcardinhand' => $alleys['mch_img_s'],#法人或申请人近期生活照
                            'goodsphoto' => $alleys['mch_img_m2'],#图片，所售商品/经营场所内景照片（公司内办公区）
                            'shopphoto' => $alleys['mch_img_m1'],#图片，经营场所，商户店面正门照（前台门头）
                            'authbankcardfront' => $alleys['mch_img_bank']#银行卡照片
                        );
                    } else {
                        if(!$alleys['mch_img_yyzz']){
                            $this->error('营业执照照片不能为空');
                        }
                        $file = array(
                            'idcardfront' => $alleys['mch_img_z'],#法人身份证正面
                            'idcardback' => $alleys['mch_img_p'],#法人身份证反面
                            'idcardinhand' => $alleys['mch_img_s'],#法人或申请人近期生活照
                            'goodsphoto' => $alleys['mch_img_m2'],#图片，所售商品/经营场所内景照片（公司内办公区）
                            'shopphoto' => $alleys['mch_img_m1'],#图片，经营场所，商户店面正门照（前台门头）
                            'authbankcardfront' => $alleys['mch_img_bank'],#银行卡照片
                            'licensephoto' => $alleys['mch_img_yyzz'],#营业执照
                            'authcertphoto' => $alleys['mch_img_sqh'],  //授权书照片
                            'authedcardfront' => $alleys['mch_img_auth_z'], //被授权法人身份证正面
                            'authedcardback' => $alleys['mch_img_auth_p'], //被授权法人身份证反面
                        );
                    }
                    //dump($arr);
                    //dump($file);


                    $res = qf_curl_calls($this->api['mch_in'], $arr, $file, true, $this->QfConfig);
                    if ($res) {
                        $res = json_decode($res, true);
                        if ($res['respcd'] == '0000' && $res['data']['mchid']) {
                            $save = array(
                                'mch_id' => $res['data']['mchid'],
                                'load_status' => 1,
                                'status' => 1,
                                'api_rel' => serialize($res),
                            );
                            M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                            $this->success('商户进件成功!-等待通道方审核!');
                        } else {
                            $this->error('进件失败!提示:'.$res['respmsg'].'['.$res['respcd'].']');
                        }
                    } else {
                        //rwlog('qf_mchin_err', $res);
                        $this->error('通信失败!请联系管理员处理!');
                    }
                }
            } else {

            }
        }
    }



    #是否存在分行
    public function QfBanks($id){
        $res=M('MchQfBanks')->where(array('banking'=>$id))->find();
        if($res){
            return $res;
        }else{
            return false;
        }

    }





    /*function get_curl_calls($curl, $sign, $https = true)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $httpHeaders = array(
            //'Content-Type: application/json; charset=utf-8',
            //"Accept: application/json",
            "X-QF-APPCODE:".$this->AppCode,
            'X-QF-SIGN:'.$sign,
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }


    function _curl_calls($curl, $data, $file=null, $https = true)
    {
        if($file) {
            $_file=array();
            foreach ($file as $key => $val) {
                if ($val) {
                    $_file[] = array('key'=>$key,'url'=>$val);
                }
            }
            foreach ($_file as $k=>$v){
                $data['file'][] =new \CURLFile(getcwd().ltrim(ImgToLocalUrl($v['url']),'.'),'image/jpeg',$v['key']);
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        //$data = json_encode($data, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type' => 'multipart/form-data',
            'X-QF-APPCODE:'.$this->AppCode,
            'X-QF-SIGN:'.self::data_sign($data),
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }*/
}