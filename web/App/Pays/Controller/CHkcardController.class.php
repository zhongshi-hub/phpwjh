<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_CardinitBaseController;
class CHkcardController extends Alleys_CardinitBaseController
{
    protected $ApiUrl;
    protected $Api;
    public function _initialize()
    {
        parent::_initialize();
        $returnUrl = "http://" . $_SERVER['HTTP_HOST'] . '/CardApi/return_url/alley/Hk';
        $this->ApiUrl=array(
            'submit_data'=>'http://113.108.195.242:29993/hicardpay/order/create',
            'mch_in'=>'http://113.108.195.242:25166/InterfaceChangeServers/toMain.do'
        );
        $this->Api=array(
          'organNo'=>'12999000',
          'MerchNo'=>'104401569102119',
          'Sign'=>'036da89986cd49bcae58821af4c3156a',
          'frontEndUrl'=>$returnUrl,
          'backEndUrl'=>'http://www.xunmafu.com/CardApi/HkNotifyUrl',
          'morganNo'=>'99999999',
          'mSign'=>'1a861edc704121753369894d7afc9596',
        );


    }


    #新版页面 支付跳转
    public function pay_submit_data(){
        $order_id=$this->orderNum;
        $arr=array(
          'version'=>'V003',
          'organNo'=>$this->Api['organNo'], #机构号
          'hicardMerchNo'=>$this->Api['MerchNo'], #商户号
          'payType'=>'034',#支付类型
          'bizType'=>'812',#业务类型
          'goodsName'=>$this->Sdata['name'],#商品名称 可控
          'merchOrderNo'=>$order_id,#商户订单号
          'showPage'=>(string)1,#是否显示收银台  默认0
          'amount'=>(string)($this->data['total']*100),#交易金额
          'frontEndUrl'=>$this->Api['frontEndUrl'],#前台回调
          'backEndUrl'=>$this->Api['backEndUrl'],#后台回调Url
        );
        $arr['sign']=self::PaySign($arr);#签名
        $res = self::hk_curl_calls($this->ApiUrl['submit_data'], $arr,false);
        $res =json_decode($res,true);
        if($res['respCode']=='00'&&$res['html']){
            $array = array(
                'mid' => $this->data['sid'],
                'store_id' => $this->data['id'],
                'agent_id' => GetMchAid($this->data['sid']),
                'new' => serialize($res),
                'data' => serialize($this->data),
                'rel' => serialize($res),
                'createtime' => time(),
                'mch_rate' => $this->mch_rate,
                'mch_id' => $this->Mdata['mch_id'],
                'service' => 'card_api',
                'out_trade_no' => $res['merchOrderNo'],
                'body' => $this->Sdata['name'],
                'total_fee' => $res['amount'] / 100, //存数据库按照分进行统计
                'mch_create_ip' => Get_Clienti_Ips(),
                'type' => 'D0',
                'alleys' => 'HKcard',
                'domain_auth' => domain_auth(),
            );
            $rel = M('mch_orders')->add($array);
            $mc=explode("'", $res['html']);
            $url=$mc[1];
            if($rel) {
                $this->success('通信成功', $url);
            }else {
                $this->error('订单创建失败!');
            }
        }else{
            $this->error('通信失败'.$res['respMsg'].'('.$res['respCode'].')');
        }
    }



    #商户进件
    public function card_mch_in()
    {

        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        if (!$alleys['rate']) {
            $this->error('系统进行二次监测,此商户的(' . $this->data['alleys'] . ')通道D0通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        } else {
            #根据系统的城市数据取汇卡的数据
            $HkCityData=M('HkCityData')->where(array('city_name'=>$alleys['mch_citys']))->getField('areacode');
            if(!$HkCityData){$this->error('获取当前通道所在城市数据失败!请咨询相关技术人员!');}
            #进件数据
            $appHead = array();
            $appHead['Version'] = '100';
            $appHead['TransType'] = 'S1050';
            $appHead['SerialNo'] = uniqid();
            $appHead['DataTime'] = date('YmdHis');
            // 报文体
            $appBody = array();
            $appBody['institutionNo'] = $this->Api['morganNo'];
            $appBody['merName'] = $alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_district'].$alleys['mch_name'];
            $appBody['merNameShort'] = $alleys['mch_name'];
            $appBody['merAddress'] = $alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_district'].$alleys['mch_address'];
            $appBody['merLegalName'] = $alleys['mch_card_name'];
            $appBody['merLegalNo'] = $alleys['mch_card_id'];
            $appBody['merLegalMobilePhone'] = $alleys['mch_tel'];
            $appBody['posAreaCode'] = $HkCityData;
            $appBody['mbiBankNo'] = $alleys['mch_linkbnk'];
            $appBody['mbiBankName'] = reload_bank($alleys['mch_bank_list']);
            $appBody['mbiAccountUser'] = $alleys['mch_bank_name'];
            $appBody['mbiAccountNo'] = $alleys['mch_bank_cid'];
           // $appBody['mbiType'] = '';
            $appBody['busiNo'] = '0311,812';
            $appBody['rateId'] = '5602,5614,3203';
            $appBody['extMerNo'] = 'XMF'.date('YmdHis').$alleys['id'];
            $appBody['liquidateModle'] = '1';
            //$appBody['defaultWalletAccount'] = '0';
            $appBody['sign'] = self::MchSign($appBody);

            $appMap = array();
            $appMap['AppHead'] = $appHead;
            $appMap['AppBody'] = $appBody;

             //self::send($this->ApiUrl['mch_in'],$appMap);
            $rel=self::hk_curl_calls($this->ApiUrl['mch_in'],$appMap);
            $rel=json_decode($rel,true);
            if($rel['RetCode']=='S1050_00'){
                $Body=$rel['AppBody'];
                if($Body['RetCode']=='S1050_00'){
                    $save = array(
                        'mch_id' => $Body['merNo'],
                        'load_status' => 1,
                        'status' => 1,
                        'api_rel' => serialize($rel),
                    );
                    M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                    $this->success('商户无卡快捷 入网成功!');
                }else{
                    $this->error('入网失败  '.$Body['RetMessage'].'('.$Body['RetCode'].')');
                }

            }else{
              $this->error('通信失败  '.$rel['RetMessage'].'('.$rel['RetCode'].')');
            }
        }
    }



    #支付签名
    public function PaySign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v && "sign" != $k) {
                $this->OutData .= $k . "=" . $v . "&";
            }
        }
        $signPars .=$this->OutData.$this->Api['Sign'];
        rwlog('hk_curl_calls_signPars',$signPars);
        $sign =md5($signPars); //加密
        return $sign;
    }

    #进件签名
    public function MchSign($data){
        ksort($data);
        $tmp = '';
        foreach ( $data as $k => $v ) {
            // 不参与签名校验的字段
            if($k == 'mbiType' || $k == 'extMerNo' || $k == 'sign' || $k == 'liquidateModle') continue;
            // 拼装待签字段
            $tmp .= $k . '=' . $v . '&';
        }
        $tmp .= 'key=' . $this->Api['mSign'];
        rwlog('hk_curl_calls_MchSign',$tmp);
        return strtoupper(md5($tmp));
    }


    function hk_curl_calls($curl, $data, $https = true)
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
        $data = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        rwlog('hk_curl_calls',$data);
        $httpHeaders = array(
            'Content-Type: application/json; charset=utf-8',
            "Accept: application/json",
            'Content-Length: ' . strlen($data)
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }
    
}