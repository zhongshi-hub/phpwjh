<?php
namespace Pays\Controller;
use Think\Controller;
class FaceAliController extends Controller
{
	protected  $config;
	protected  $aop;
	public function _initialize(){
		Vendor('ali_dmf.AopSdk');
		$config=M('MchPayConfig')->where(['domain_auth'=>domain_auth()])->find();
		//基础配置信息
		$this->config=[
			'pid'=>$config['ali_isv_pid'],
			'appid'=>$config['ali_isv_appid'],
			'rsaPublicKey'=>$config['ali_isv_public_key'],
			'rsaPrivateKey'=>$config['ali_isv_private_key'],
			'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Pays/FaceAli/notifyUrl',
		];
		//公共
		$aop = new \AopClient ();
		$aop->appId              = $this->config['appid'];//应用AppId
		$aop->rsaPrivateKey      = $this->config['rsaPrivateKey']; //工具生成私钥 公钥配置到平台查看应用公钥
		$aop->alipayrsaPublicKey = $this->config['rsaPublicKey']; //对应平台查看支付宝公钥
		$aop->apiVersion         = '1.0';
		$aop->signType           = 'RSA2';
		$aop->postCharset        = 'UTF-8';
		$aop->format             = 'json';
		$this->aop=$aop;

	}


	public function facePay($data){
		if(!array_key_exists('total',$data)||empty($data['total'])){
			return ['code'=>103,'msg'=>'total不可为空'];
		}
		if(!array_key_exists('auth_code',$data)||empty($data['auth_code'])){
			return ['code'=>103,'msg'=>'auth_code不可为空'];
		}
		$arr=[
			'out_trade_no'=>$this->getOrderId("FA{$data['store_data']['id']}"),
			'scene'=>'security_code',
			'auth_code'=>$data['auth_code'],
			'total_amount'=>$data['total'],//订单金额
			'subject'=>'刷脸支付',//订单标题
			'extend_params'=>[
				'sys_service_provider_id'=>$this->config['pid']
			],
		];
		$request=new \AlipayTradePayRequest();
		$request->setNotifyUrl($this->config['notifyUrl']);
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$data['store_data']['sid']);
		return ['code'=>400,'msg'=>'测试接口','data'=>['提交数据'=>$arr,'返回数据'=>$result]];

	}


	public function ftokenQuery(){
		$arr=[
			'ftoken'=>'fp0593e8d5c136277f13fd5bc36c13a7db7',
			'biz_type'=>"1",
			'ext_info'=>[
				'query_type'=>1
			]
		];
		$request=new \ZolozAuthenticationCustomerFtokenQueryRequest();
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,14);
		dump($result);
	}



	public function notifyUrl()
	{
		$notifyData = file_get_contents("php://input");
		rwlog('faceAliNotify',$notifyData);
		parse_str($notifyData, $notifyData);
		rwlog('faceAliNotify',$notifyData);
	}


	/**
	 * 通信
	 * @param $request
	 * @return array
	 */
	public function setAop($request,$token=false,$mid=''){
		try{
			if($token){
				$app_auth_token=aliIsvToken($mid);
				$result = $this->aop->execute($request,null,$app_auth_token);
			}else{
				$result = $this->aop->execute($request);
			}
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$result=object_to_array($result->$responseNode);
		}catch (\Exception $e) {
			$result=$e->getMessage();
			return ['status'=>0,'msg'=>$result];
		}
		return ['status'=>1,'msg'=>'接口通信正常','data'=>$result];
	}

	/**
	 * 生成订单号
	 * @param string $prefix
	 * @return string
	 */
	public function getOrderId($prefix = '')
	{
		return date('ymd').(date('y') + date('d')).sprintf('%03d', rand(0, 999)).str_pad((time() - strtotime(date('Y-m-d'))), 3, 0, STR_PAD_LEFT) . substr(microtime(), 2, 5);
	}
	
}