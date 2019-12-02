<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/*
 * 支付基类控制器
 * Author: CCL
 * EndTime: 2017-03-09 18:00
 * */

class PayBaseController extends BaseController{
    /*private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;*/
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
        dump($GLOBALS["HTTP_RAW_POST_DATA"]);
        //dump($rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        /*全局支付方法*/
        //new \wft\Utils();
        //$this->resHandler = new \wft\ClientResponseHandler();
        //$this->reqHandler = new \wft\RequestHandler();
        //$this->pay = new \wft\PayHttpClient();
        //dump(C('T0XY_mch'));
        /*if(IS_POST) {
            dump($GLOBALS["HTTP_RAW_POST_DATA"]);

            /*$str = file_get_contents("php://input");
            $this->data = $this->xmlctojson($str);



            //全局二次修改异步回调地址
            $ndata=$this->data;
            unset($ndata['notify_url']);
            unset($ndata['sign']);
            $ndata['notify_url']='http://cch.1dianfu.com/api/notify_url';
            $this->newdata=$ndata;
            //rwlog('newdata',$this->newdata);

            //全局判断必填项
            $this->valnotempty();
            //判断是什么模式
            if ($this->data['sign_agentno']) {//渠道模式
                //渠道模式判断渠道是否存在
                $db = M('mch_parent_api');
                $this->place = $db->where(array('placenum' => $data['sign_agentno']))->find();
                if ($this->place) {//存在
                    if ($this->place['status'] == 'false') {
                        $this->wftmsg('S404', '合作伙伴信息被禁用');
                    }
                } else {//不存在
                    $this->wftmsg('S404', '合作伙伴信息不存在');
                }
                //渠道模式签名key
                $this->signkey=$this->place['placekey'];
            }elseif ($this->data['groupno']) {//大商户模式 //只针对交易
                $this->wftmsg('S404', '大商户模式接口暂未开通!');
            }else {//普通模式
                //普通模式判断商户号是否在系统存在
                $db = M('mch_merchant');
                $this->mch_merchant = $db->where(array('merchantId' =>$this->data['mch_id']))->find();
                //普通商户模式签名key
                $this->signkey=$this->mch_merchant['merchantkey'];
                if (!$this->mch_merchant) {//商户号不存在
                    $this->wftmsg('S404', '商户号('.$this->data['mch_id'].')服务端不存在!如有疑问!请联系一点付!');
                }
                if(!$this->signkey){
                    $this->wftmsg('S404', '此商户号密钥服务端未配置!请联系一点付工作人员配置!');
                }
            }
            //验证签名
            $this->wftsign_check();
        }else{
            $this->wftmsg('S404', '非法的请求方法');
        }*/
	}




	//签名验证
    public function wftsign_check(){
         //签名验证
        $data=$this->data;
        $sign=$this->signs_check($data);
        if($data['sign']!=$sign){
            $msg='数字签名验证失败!';
            $this->wftmsg('S404', $msg);
        }
    }

	//不能为空选项
    public function valnotempty(){
        if (empty($this->data['service'])) {
            $this->wftmsg('S404', '接口类型不能为空');
        }
        if (empty($this->data['mch_id'])){
            $this->wftmsg('S404', '商户号不能为空');
        }
        if (empty($this->data['out_trade_no'])) {
            $this->wftmsg('S404', '商户订单号不能为空');
        }
        if (empty($this->data['body'])) {
            $this->wftmsg('S404', '商品描述不能为空');
        }
        if (empty($this->data['sub_openid'])) {
            $this->wftmsg('S404', '用户openid不能为空');
        }
        if (empty($this->data['total_fee'])) {
            $this->wftmsg('S404', '交易总金额不能为空');
        }
        if (empty($this->data['mch_create_ip'])) {
            $this->wftmsg('S404', '终端IP不能为空');
        }
        if (empty($this->data['notify_url'])) {
            $this->wftmsg('S404', '通知地址不能为空');
        }
        if (empty($this->data['nonce_str'])) {
            $this->wftmsg('S404', '随机字符串不能为空');
        }
        if (empty($this->data['sign'])) {
            $this->wftmsg('S404', '签名不能为空');
        }
    }

	//默认错误提示
    public function wftmsg($status,$message){
      $array=array(
        'version'=>'2.0',
        'charset'=>'UTF-8',
        'status'=>$status,
        'message'=>$message
      );
      self::show_xml($array);
    }

	//解析XML
    public function xmlctojson($str){
        $obj = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON,true);
        return $dJSON;
    }

    //数组转XML
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";

        }
        $xml.="</xml>";
        return $xml;
    }

    //数组转XML 输出结果
    public function show_xml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        header('Content-Type:text/xml');
        echo $xml;
        exit;
    }


    //本服务端签名算法 验证签名
    public function signs_check($data){

        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->signkey;
        //rwlog('sign',$signPars);
        $sign = strtoupper(md5($signPars));
        return $sign;
    }


    //威富通签名算法
    public function signwft($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->wft_keys;
        //rwlog('sign',$signPars);
        $sign = strtoupper(md5($signPars));
        return $sign;
    }







}

