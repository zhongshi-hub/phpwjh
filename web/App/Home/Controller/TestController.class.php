<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 测试项目
 */
class TestController extends HomeBaseController
{
    public function _initialize()
    {
        parent::_initialize();
        ##测试基本信息
        /*$this->Cname='贰叁伍';//渠道名称
        $this->Cno='89566532270';//渠道编码
        $this->merchantNo='B100001893';#商户编号
        ##测试密钥信息
        $this->Deskey='Rd7WX98JWSwAZghYf81czuRp';//加密密钥
        $this->Signkey='X1w8LsNHoluMlnF8jxG3J2Mw0ZTR5qeb';//签名密钥
        ##网关信息
        $G_Url='http://api.izhongyin.com'; //网关地址
        */
        $this->Cname='河南贰叁伍软件科技有限公司';//渠道名称
        $this->Cno='C2534398892';//渠道编码
        $this->merchantNo='B101064244';#商户编号
        $this->orderNum=date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);#商户订单号
        ##密钥信息
        $this->Deskey='qW6I9F7z3583Z8QnranP8906';//加密密钥
        $this->Signkey='1hN83L3108S7xAN7vXxm984va04z5890';//签名密钥
        ##网关信息
        $G_Url='http://real.izhongyin.com'; //网关地址
        ##网关信息
        $this->Api_Url=array(
               'M_In'=>$G_Url.'/middlepayportal/merchant/in',  #进件网关
               'M_Query'=>$G_Url.'/middlepayportal/merchant/query',#商户查询
               'WX_Scan'=> $G_Url.'/middlepaytrx/wx/scanCode',#微信扫码支付
               'WX_Common'=> $G_Url.'/middlepaytrx/wx/scanCommonCode', #微信公众号
               'WX_Passive'=> $G_Url.'/middlepaytrx/wx/passivePay', #微信终端扫码
               'ALI_Scan'=> $G_Url.'/middlepaytrx/alipay/scanCode',#支付宝扫码支付
               'ALI_Common'=> $G_Url.'/middlepaytrx/alipay/scanCommonCode',#支付宝公众号支付
               'ALI_Passive'=> $G_Url.'/middlepaytrx/alipay/passivePay',#支付宝终端扫码
               'N_Query'=>$G_Url.'/middlepaytrx/online/query',#订单查询
        );




    }



    /*测试*/
    public function tests(){
        $data='{"desKey":"3YaTNFkUkWUnti9e7D5Kv2aw","merchantNo":"B101064244","queryKey":"rkEsRkJpWnpsxzukSD3rMN0V5r441QoC","respCode":"0000","respMsg":"成功","sign":"DE2A22A946897BE5E202422010DF1A73","signKey":"ChLVH6m7CMViG2hxXtveaYDipAsh8u7D"}';
        $data=json_decode($data,true);
        $data=serialize($data);
        dump($data);
    }

    /*商户入网测试*/
    public function in(){

      $data=array(
        'channelName'=>$this->Cname,//渠道名称
        'channelNo'=>$this->Cno,//渠道编码
        'merchantName'=>'娟娟鲜花店',//商户名称
        'merchantBillName'=>'娟娟鲜花店',//签购单显示名称
        'installProvince'=>'河南',//安装归属省
        'installCity'=>'郑州',//安装归属市
        'installCounty'=>'金水区',//安装归属县（区）
        'operateAddress'=>'园田路50号',//经营地址
        'merchantType'=>'PERSON',//商户类型 ENTERPRISE -企业商户 INSTITUTION -事业单位商户 INDIVIDUALBISS -个体工商户  PERSON -个人商户
        //'businessLicense'=>'',//营业执照号码
        'legalPersonName'=>'陈存龙',//法人姓名
        'legalPersonID'=>'41092219930201493X',//法人身份证号
        'merchantPersonName'=>'陈存龙',  //商户联系人姓名
        'merchantPersonPhone'=>'18637162652',//商户联系人电话
        'wxType'=>'203',//微信经营类目
        'wxT1Fee'=>'0.0026',//微信商户手T1续费
        'wxT0Fee'=>'0.0027',//微信商户手T0续费
        'alipayType'=>'2015091000060134',//支付宝经营类目
        'alipayT1Fee'=>'0.0026',//支付宝商户手T1续费
        'alipayT0Fee'=>'0.0027',//支付宝商户手T0续费
        'bankType'=>'TOPRIVATE',//结算账户性质 对公-TOPUBLIC 对私-TOPRIVATE
        'accountName'=>'陈存龙',//开户人姓名
        //'accountNo'=>'6227002430160277786',//开户账号
        'accountNo'=>self::encode('6227002430160277786'),//开户账号
        'bankName'=>'中国建设银行',//开户银行名（大行全称）
        'bankProv'=>'河南',//开户行省
        'bankCity'=>'郑州',//开户行市
        'bankBranch'=>'中国建设银行股份有限公司郑州丰庆路支行',//开户银行名称（精确到支行）
        'bankCode'=>'105491001041',//联行号
        //'creditCardNo'=>'',//信用卡认证
      );
      $data['sign']=self::signs($data);
      $res=self::curl_res($this->Api_Url['M_In'],$data);
      dump($res);

      //dump($data['sign']);

    }



    //商户入网查询接口
    public function Mquery(){
        $data=array(
            'channelName'=>$this->Cname,//渠道名称
            'channelNo'=>$this->Cno,//渠道编码
            'merchantNo'=>$this->merchantNo,
        );
        $data['sign']=self::signs($data);
        $res=self::curl_res($this->Api_Url['M_Query'],$data);
        dump($res);
    }

    //微信扫码下单
    public function WX_Scan(){
        $data=array(
          'trxType'=>'Alipay_SCANCODE',#接口类型
          'merchantNo'=>$this->merchantNo,#商户编号
          'orderNum'=>$this->orderNum,#商户订单号
          'amount'=>'1',#金额 元
          'goodsName'=>'娟娟鲜花买单',#订单描述
          'callbackUrl'=>'',#页面回调
          'serverCallbackUrl'=>'',#异步回调
          'orderIp'=>Get_Clienti_Ips(),#用户IP
          'toibkn'=>'105491001041',#T0联行号
          'cardNo'=>self::encrypt('6227002430160277786','3YaTNFkUkWUnti9e7D5Kv2aw'),#T0入账卡号
          'idCardNo'=>self::encrypt('41092219930201493X','3YaTNFkUkWUnti9e7D5Kv2aw'),#T0入帐卡对应身份证号
          'payerName'=>self::encrypt('41092219930201493X','陈存龙'),#T0入帐卡对应姓名
          'encrypt'=>'T0',#T0/T1标识，若此项为T0，对应的10,11,12,13必填
          //'authCode'=>'',#授权code码  小额支付
        );
        $data['sign']=self::OrderSign($data);
        $res=self::curl_res($this->Api_Url['ALI_Scan'],$data,1);
        dump($res);
    }

    //



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

        return $res;
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

    //交易签名
    public function OrderSign($data){
        $signPars = "";
        //ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v && "sign" != $k) {
                $outdata .=  $v . "#";
            }
        }
        $signPars .='#'.$outdata.'ChLVH6m7CMViG2hxXtveaYDipAsh8u7D' ;
        $sign = md5($signPars); //加密
        return $sign;

        /*$datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= 'ChLVH6m7CMViG2hxXtveaYDipAsh8u7D';
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;*/



    }

    //入网签名
    public function signs($data){
        $signPars = "";
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= $this->Signkey;
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }

    //数据加密 卡号类
    public function encode($data){
       return self::encrypt($data,$this->Deskey);
    }


    public function encrypt($strinfo,$desKey){//数据加密
        $size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
        $strinfo = $this->pkcs5_pad($strinfo, $size);
        $key = str_pad($desKey,24,'0');
        $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $strinfo);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        //    $data = base64_encode($this->PaddingPKCS7($data));
        $data = base64_encode($data);
        return $data;
    }
    public function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }


}