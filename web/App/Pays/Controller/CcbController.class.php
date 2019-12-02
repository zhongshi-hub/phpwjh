<?php
namespace Pays\Controller;
use ZipArchive;
use Think\Controller;

define("JAVA_DEBUG", true); //调试设置
define("JAVA_HOSTS", "127.0.0.1:8081"); //设置javabridge监听端口
define("JAVA_LOG_LEVEL", 3); //java.log_level: 0-6
class CcbController extends Controller
{

    public function _initialize()
    {
        $this->mch=M('MchSeller')->where(array('id'=>'939'))->find();

    }


    public function calls($urls,$datas,$file) {
        if($file){
            //$datas['picFile']='@'.$file; //php5.4 语法
            $datas['picFile']=new \CURLFile(realpath($file)); //php5.6 语法
        }
        //rwlog('datas',$datas);
        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $urls);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type'=>'multipart/form-data',
        ));
        // 执行操作
        $res = curl_exec($ch);
        if ($res == NULL) {
            $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $res;
    }


    public  function mch_zip($id){
        $mch=M('MchSeller')->where(array('id'=>$id))->find();
        $files_to_zip=array(
            $mch['mch_img_bank'],
            $mch['mch_img_z'],
            $mch['mch_img_p'],
            $mch['mch_img_s'],
        );
        $file=array();
        foreach ($files_to_zip as $val){
            if (preg_match('/(http:\/\/)/i', $val)) {
                $file_data=self::_NetDown($val);
                if($file_data['save_path']){
                    $file[]=getcwd().$file_data['save_path'];
                }
            }else{
                $file[]=getcwd().$val;
            }
        }
        create_zip($file,'mch_'.$mch['id'].'.zip');
        return getcwd().'/Upload/mch_zip/mch_'.$mch['id'].'.zip';
    }


    public function _NetDown($url){
        $savePath =  './Upload/data_tmp/'.date('Ymd').'/';// 设置附件上传目录
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].$savePath)||!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)){
            mkdir($_SERVER['DOCUMENT_ROOT'].$savePath,0777);
        }
        $resp = _getImage($url,$savePath);
        return $resp;
    }


    #商户进件
    public function mch_in()
    {
        $wx_arr=array('203','208','210','158','204','205');
        $wx_Type=rand_one($wx_arr);
        #支付宝经营类目ID
        $ali_arr=array('2015050700000022','2015050700000037','2015091000058486','2015091000060134','2015062600009243');
        $ali_Type= rand_one($ali_arr);

        $data = array(
            'expanderCd' => '0199980080', #拓展商号
            'merchantName' => $this->mch['mch_name'].rand('111','999'),#商户名称
            'merchantShortName' => $this->mch['mch_name'].rand('111','999'),#商户简称
            'merchantType' => '8650', #商户类型
            'merchantLevel' => '2',#商户级别
            //'parentCustomerId' => '',#上级商户名
            'openType' => '1',#开户类型 1-个人、C-企业
            //'gszcName' => '',#工商注册名
            'qualificationInfos' => array( #资质信息
                array(
                    'legalIdType' => '1',#法人证件类型
                    'legalIdName' => $this->mch['mch_card_name'],#法人姓名
                    'legalIdNumber' => $this->mch['mch_card_id'],#法人证件号码
                    //'legalIdInfo' => sft_imgToBase64('.'.$this->mch['mch_img_z']),#图片 base64
                    //'filename' => 'pic_'.$this->mch['id'].'.jpg',#图片名称
                )
            ),
            'manageOrgId' => '1600', #管理机构
            'merchantAddr' =>$this->mch['mch_address'], #商户地址
            'province' => ccb_area($this->mch['mch_bank_provice']),#省份
            'city' => ccb_area($this->mch['mch_citys']),#城市
            'county' => ccb_area($this->mch['mch_district']),#区县
            'accountType' => '2',#账户类型
          //  'account' => $this->mch['mch_bank_cid'],#账号
            'account' => '0301012830004401',#账号
            'accountName' => '测试2007024021',#账户名
            //'accountName' => $this->mch['mch_bank_name'],#账户名
            'banckCode' =>'102100042008', #开户行号
            //'banckCode' =>reload_bank_number($this->mch['mch_bank_list']), #开户行号
            'bankName' => reload_bank($this->mch['mch_bank_list']), #开户行名
            'openBranch' => $this->mch['mch_linkbnk'], #开户网点 联行号
            'merchantConsacts' => $this->mch['mch_card_name'],#联系人姓名
            'telephone' => $this->mch['mch_tel'], #联系人电话
            'payChennel' => 'PC0000000000002',#支付通道
            'payServices' => array( #支付服务
                array('payService' => 'WEIXIN', #支付服务  WEIXIN-微信、 QQ-手机QQ、 ZFB-支付宝、 WEIXIN-GZH-微信千6、 WEIXIN-APP-微信千1、 WEIXIN-SK-微信0费率、 JD-京东、BD-百度
                    'isOpen' => 'Y', #Y或N，默认是Y-开通
                    'scale' => '6', #单位:千分之，例如千6费率填6 非银行卡支付服务必填 费率(‰)
                    'countRole' => '垫资方', #结算主体 本商户、上级商户、不参与、垫资方 D0垫资模式结算主体必须为垫资方，           微信公众号模式结算主体必须为不参与
                    'debitCardPate' => '6', #借记卡费率( ‰)
                    'creditCardRate' => '6', #贷记卡费率( ‰)
                    'tradeType' => '207', #行业类型
                    'supply'=>sft_imgToBase64('.'.$this->mch['mch_img_z']),#图片 base64
                    'supplyname'=>'pic_'.$this->mch['id'].'.jpg',#图片名称
                ),
                array('payService' => 'ZFB', #支付服务  WEIXIN-微信、 QQ-手机QQ、 ZFB-支付宝、 WEIXIN-GZH-微信千6、 WEIXIN-APP-微信千1、 WEIXIN-SK-微信0费率、 JD-京东、BD-百度
                    'isOpen' => 'Y', #Y或N，默认是Y-开通
                    'scale' => '6', #单位:千分之，例如千6费率填6 非银行卡支付服务必填 费率(‰)
                    'countRole' => '垫资方', #结算主体 本商户、上级商户、不参与、垫资方
                    'debitCardPate' => '6', #借记卡费率( ‰)
                    'creditCardRate' => '6', #贷记卡费率( ‰)
                    'tradeType' => '2015050700000011', #行业类型
                ),

            ),

            'businessModel'=>'4', #业务模式 1-普通,2-企业内收支两线,3-微信公众号,4-D0垫资
            'repaidPerson'=>'传化支付有限公司', #垫资方
            'repaidRate'=>'0.2', #垫资费率
            'minAmount'=>'10', #单笔最低交易金额
            'poundage'=>'0', #单笔代付手续费金
            'minRepaidAmount'=>'0.2', #保底垫付手续费
            'thdSysNme'=>'cposdemo',#第三方应用名称
            'timestamp'=> ccb_timestamp(),
            'encryptedSign'=>self::sign($data, true), #签名串(密文)
            'summaryPlain'=>self::sign($data, false), #摘要明文



        );



        $url = 'http://111.205.207.144:48080/tbm-server/mcht/UploadsMerchant.json';
        $res = ccb_curl_calls($url, $data, false);
        dump($data);
        dump($res);
    }


    #签名
    public function sign($data, $type = true)
    {
        require_once('/home/java/ccb_jks/Java.inc');
        $System = new \Java("com.tesla.tunguska.cuppay.util.CipherSignUtil");
        $password = "cposdemo";
        $alias = "cposdemo";
        $file = "/home/java/ccb_jks/demo.jks";
        $encryptedSign = $System->paramSignAndEncryptBase64String($data, $password, $alias, $file);
        $summaryPlain = $System->paramMap2SHA1Base64String($data);
        $encryptedSign = sprintf($encryptedSign);
        $summaryPlain = sprintf($summaryPlain);
        if ($type) {
            return $encryptedSign;
        } else {
            return $summaryPlain;
        }
    }


    #支付宝预下单
    public function ali_pay(){
        $url="http://140.206.72.238:10011/scanpay-api/api/unifiedOrder20";
        $this->ccb_notify='http://www.xunmafu.com/Api/ccb_notify_url';
        $order_id=date('YmdHis').rand('11111','99999');
        $data=array(
            'merNo'=>'999102115200003',#商户号
            'orderNo'=>$order_id,#商户订单号
            'channelFlag'=>'01',#支付通道 00微信 01支付宝 02百付包 03翼支付 04qq 05京东
            'amount'=>'1',#订单金额 单位为分
            'reqId'=>$order_id,#请求交易的流水号
            'reqTime'=>date('YmdHis'),#请求时间
            'notifyUrl'=>$this->ccb_notify,#异步通知url
            'goodsName'=>'Test',#商品名称
            'alipayUserId'=>'2088102170292985',#支付宝uerid
        );
        $data['signIn']=self::pay_sign($data,'611d28c87f1d4c61ab0c5042071effc3');#签名
        $res = curl_calls($url, $data);
        dump($data);
        dump($res);


    }


    #支付签名
    public function pay_sign($data,$key){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "mac" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=rtrim($signPars,'&').$key;
        $sign = strtoupper(md5($sign_data));
        return $sign;
    }





}