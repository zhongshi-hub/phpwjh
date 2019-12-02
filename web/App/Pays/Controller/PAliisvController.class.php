<?php
/**
 * 支付宝官方ISV通道
 */
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class PAliisvController extends Alleys_initBaseController {

	protected $config;
	protected $aop;
	protected $oid;

	public function _initialize(){
		Vendor('ali_dmf.AopSdk');
        $config=M('MchPayConfig')->where(['domain_auth'=>domain_auth()])->find();
		//基础配置信息
		$this->config=[
			'pid'=>$config['ali_isv_pid'],
			'appid'=>$config['ali_isv_appid'],
			'rsaPublicKey'=>$config['ali_isv_public_key'],
			'rsaPrivateKey'=>$config['ali_isv_private_key'],
			'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Pays/PAliisv/notifyUrl',
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
		$this->oid=rand_out_trade_no();

	}





	/**
	 * 商户授权链接
	 */
	public function getOauthUrl(){
		$seller=M('mchSeller')->where(['domain_auth'=>domain_auth(),'id'=>I('get.id')])->find();
		if($seller) {
			$domain = urlencode('http://' . $_SERVER['HTTP_HOST'] . '/aliOauth?mchId=' . $seller['id']);
			$url = "https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id={$this->config['appid']}&redirect_uri={$domain}";
			redirect($url);
		}else{
			$this->error('未找到当前商户信息,请先入网后再试','',888);
		}
	}

	/**
	 * 商户授权
	 */
	public function mchOauth(){
		$data=I('get.');
		$code=$data['app_auth_code'];
		//换取app_auth_token
		$request=new \AlipayOpenAuthTokenAppRequest();
		$request->setBizContent(json_encode(['grant_type'=>'authorization_code','code'=>$code]));
		$result=$this->setAop($request);
		if($result['status']){
             if($result['data']['code']=='10000'&&$result['data']['msg']=='Success'){//授权成功
                 $this->setMchToken($data['mchId'],$result['data']);
			 }else{
				 $this->error($result['data']['sub_msg']."({$result['data']['msg']}|{$result['data']['sub_code']})",'',888);
			 }
		}else{
			$this->error($result['msg'],'',888);
		}
	}

	/**
	 * 新增或更新授权信息
	 * @param $mid
	 * @param $data
	 */
	public function setMchToken($mid,$data){
		$db=M('isvToken');
		$app_id=I('get.app_id');
		$map=[
			'mid'=>$mid,
			'domain_auth'=>domain_auth(),
		];
		if($db->where($map)->count()){
			$save=[
				'app_auth_token'=>$data['app_auth_token'],
				'user_id'=>$data['user_id'],
				'appid'=>$app_id,
				'time'=>date('Y-m-d H:i:s'),
			];
			$res=$db->where($map)->save($save);
		}else{
			$map['time']=date('Y-m-d H:i:s');
			$map['app_auth_token']=$data['app_auth_token'];
			$map['user_id']=$data['user_id'];
			$map['appid']=$app_id;
			$res=$db->add($map);
		}
        if($res){
			$this->success('商户授权成功','',888);
		}else{
			$this->error('商户授权失败,请重新授权!','',888);
		}
	}

	/**
	 * 获取商户授权的权限信息
	 * @param string $auth
	 */
	public function getOauthApi($auth){
		$this->aop->app_auth_token=$auth;
		$request=new \AlipayOpenAuthTokenAppQueryRequest();
		$request->setBizContent(json_encode(['app_auth_token'=>$auth]));
		$result=$this->setAop($request);
		dump($result);
	}

	/**
	 * 会员卡充值
	 * @param $data
	 * @return array
	 */
	public function memberPay($data){
		$arr=[
			'out_trade_no'=>$this->oid,
			'total_amount'=>$data['total'],//订单金额
			'subject'=>"会员卡充值({$data['phone']})",//订单标题
			'body'=>$data['phone'].'会员充值',//交易描述
			'buyer_id'=>$data['openid'],//买家用户ID
			'extend_params'=>[
				'sys_service_provider_id'=>$this->config['pid']
			],
		];
		$request=new \AlipayTradeCreateRequest();
		$request->setNotifyUrl($this->config['notifyUrl']);
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$data['mid']);
		if($result['status']){
			if($result['data']['code']=='10000') {
				$array = array(
					'goods_tag' => $data['remark']?$data['remark']:"{$data['phone']}会员充值",
					'mid'=>$data['mid'],
					'store_id'=>$data['pay_store_id'],
					'agent_id'=>GetMchAid($data['mid']),
					'createtime' => time(),
					'mch_rate'=>AlleysGetRate('Aliisv',$data['mid'],'rate'),
					'mch_id' => 'member',
					'service' => 'ali_jsapi',
					'out_trade_no' => $arr['out_trade_no'],
					'body' => '会员充值',
					'total_fee' => $data['total'], //存数据库按照分进行统计
					'mch_create_ip' => Get_Clienti_Ips(),
					'sub_openid' => $data['openid'],
					'type' => 'D0',
					'alleys' => 'Aliisv',
					'domain_auth' => domain_auth(),
					'is_raw' => 1,
				);
				$rel = M('mch_orders')->add($array);
				if ($rel) {
					$pay_data = array(
						'msg' => '订单创建成功',
						'type' => 'ali',
						'pay_info' => array(
							'tradeNO' => $result['data']['trade_no']
						),
						'out_trade_no' => $arr['out_trade_no'],
						'total'=>$data['total'],
						'openid'=>$data['openid']
					);
					return ['status'=>true,'msg'=>'success','data'=>$pay_data];
				} else {
					return ['status'=>false,'msg'=>'订单创建失败'];
				}
			}else{
				return ['status'=>false,'msg'=>$result['data']['sub_msg']];
			}
		}else{
			return ['status'=>false,'msg'=>$result['msg']];
		}

	}


	/**
	 * 支付宝服务窗支付
	 */
	public function pay_ali_jsapi(){
		$arr=[
			'out_trade_no'=>$this->oid,
			'total_amount'=>$this->data['total'],//订单金额
			'subject'=>$this->Mdata['mch_name'].'买单',//订单标题
			'body'=>$this->Mdata['mch_name'].'移动支付',//交易描述
			'buyer_id'=>$this->data['openid'],//买家用户ID
			'extend_params'=>[
				'sys_service_provider_id'=>$this->config['pid']
			],
		];
		$request=new \AlipayTradeCreateRequest();
		$request->setNotifyUrl($this->config['notifyUrl']);
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$this->data['sid']);
		//rwlog('aliISV_js',[$arr,$result]);
		if($result['status']){
			if($result['data']['code']=='10000') {
				$array = array(
					'goods_tag' => $this->data['remark'],
					'mid' => $this->data['sid'],
					'store_id' => $this->data['id'],
					'agent_id' => GetMchAid($this->data['sid']),
					'new' => serialize($arr),
					'data' => serialize($this->data),
					'rel' => serialize($result['data']),
					'createtime' => time(),
					'mch_rate' => $this->mch_rate,
					'mch_id' => $this->Mdata['mch_id'],
					'service' => 'ali_jsapi',
					'out_trade_no' => $arr['out_trade_no'],
					'body' => $this->Sdata['name'],
					'total_fee' => $this->data['total'], //存数据库按照分进行统计
					'mch_create_ip' => Get_Clienti_Ips(),
					'sub_openid' => $this->data['openid'],
					'type' => 'D1',
					'alleys' => 'Aliisv',
					'domain_auth' => domain_auth(),
					'is_raw' => 1,
				);
				$rel = M('mch_orders')->add($array);
				if ($rel) {
					$pay_data = array(
						'msg' => '订单创建成功',
						'type' => 'js',
						'pay_info' => array(
							'tradeNO' => $result['data']['trade_no']
						),
						'out_trade_no' => $arr['out_trade_no'],
					);
					$this->success($pay_data);
				} else {
					$this->error('订单创建失败!请重新支付!');
				}
			}else{
				$this->error($result['data']['sub_msg']."({$result['data']['msg']}|{$result['data']['sub_code']})");
			}
		}else{
			$this->error($result['msg']);
		}
	}


	/**
	 * 支付宝条码支付
	 */
	public function pay_ali_scan(){
		$arr=[
			'out_trade_no'=>$this->oid,
			'scene'=>'bar_code',
			'auth_code'=>$this->data['code_data'],
			'total_amount'=>$this->data['total'],//订单金额
			'subject'=>'移动支付',//订单标题
			'extend_params'=>[
				'sys_service_provider_id'=>$this->config['pid']
			],
		];
		$request=new \AlipayTradePayRequest();
		$request->setNotifyUrl($this->config['notifyUrl']);
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$this->data['sid']);
		//rwlog('aliIsvScan',[$arr,$result]);
		if($result['status']){
			$array=array(
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($result),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'ali_scan',
				'out_trade_no'=>$arr['out_trade_no'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Aliisv',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			if($result['data']['code']=='10000') {
				$type='success'; //支付成功
				$array['status']=1;
				$array['sub_openid']=$result['data']['buyer_user_id'];
				$array['time_end']=strtotime($result['data']['gmt_payment']);
				$array['transaction_id']=$result['data']['trade_no'];
				$array['buyer_logon_id']=$result['data']['buyer_logon_id'];
				$msg='收款成功 金额:'.$result['data']['total_amount'];
				//发送模板消息
				R('Pays/Notify/sendTemplateMessage', array($arr['out_trade_no'])); //发送收款成功模板消息
			}elseif ($result['data']['code']=='10003'){
				$type='loading'; //支付中
				$msg='用户支付确认中...';
                $this->loopQueryResult($arr['out_trade_no']);//服务器开启轮询订单
			}else{
				$this->error($result['data']['sub_msg']."({$result['data']['msg']}|{$result['data']['sub_code']})");
			}
			$rel=M('mch_orders')->add($array);
			if($rel){
				$pay_data=array(
					'msg'=>$msg,
					'type'=>$type,
					'api'=>'Aliisv',
					'out_trade_no'=>$arr['out_trade_no'],
				);
				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($result['msg']);
		}

	}



	// 轮询查询订单支付结果
	protected function loopQueryResult($oid){
		$queryResult = NULL;
		for ($i=1;$i<0;$i++){
			try{
				sleep(3);
			}catch (\Exception $e){
				rwlog('aliIsvLoopQueryResult',$e->getMessage());
				exit();
			}
			$queryResponse = $this->orderResult($oid);
			if(!empty($queryResponse['status'])){
				if($this->stopQuery($queryResponse['result']['data'])){
					return $queryResponse;
				}
				$queryResult = $queryResponse;
			}
		}
		return $queryResult;
	}

	// 判断是否停止查询
	protected function stopQuery($response){
		if("10000"==$response['code']){
			if("TRADE_FINISHED"==$response['trade_status']||
				"TRADE_SUCCESS"==$response['trade_status']||
				"TRADE_CLOSED"==$response['trade_status']){
				return true;
			}
		}
		return false;
	}



	/**
	 * 生成付款码
	 */
	public function pay_ali_code(){
		$arr=[
		   'out_trade_no'=>$this->oid,
		   'total_amount'=>$this->data['total'],//订单金额
		   'subject'=>'移动支付',//订单标题
		   'extend_params'=>[
		   	 'sys_service_provider_id'=>$this->config['pid']
		   ],
		];
		$request=new \AlipayTradePrecreateRequest();
		$request->setNotifyUrl($this->config['notifyUrl']);
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$this->data['sid']);
		if($result['status']){
			if($result['data']['code']=='10000') {
				$array = array(
					'goods_tag' => $this->data['remark'],
					'mid' => $this->data['sid'],
					'store_id' => $this->data['id'],
					'agent_id' => GetMchAid($this->data['sid']),
					'new' => serialize($arr),
					'data' => serialize($this->data),
					'rel' => serialize($result['data']),
					'createtime' => time(),
					'mch_rate' => $this->mch_rate,
					'mch_id' => $this->Mdata['mch_id'],
					'service' => 'ali_code',
					'out_trade_no' => $arr['out_trade_no'],
					'body' => $this->Sdata['name'],
					'total_fee' => $this->data['total'], //存数据库按照分进行统计
					'mch_create_ip' => Get_Clienti_Ips(),
					'sub_openid' => $this->data['openid'],
					'type' => 'D1',
					'alleys' => 'Aliisv',
					'domain_auth' => domain_auth(),
					'is_raw' => 1,
				);
				$rel = M('mch_orders')->add($array);
				if ($rel) {
					$pay_data=array(
						'msg'=>'预下单成功',
						'qrcode'=>$result['data']['qr_code'],
						'api'=>'Aliisv',
						'out_trade_no'=>$result['data']['out_trade_no'],
					);
					$this->success($pay_data);
				} else {
					$this->error('订单创建失败!请重新支付!');
				}
			}else{
				$this->error($result['data']['sub_msg']."({$result['data']['msg']}|{$result['data']['sub_code']})");
			}
		}else{
			$this->error($result['msg']);
		}

	}


	#订单查询接口
	public function pay_getOrderStatus($oid){
		$res=self::orderResult($oid);
		if($res['status'] == true&& $res['res_status']==1){
			$pay_data=array(
				'msg'=>'收款成功',
				'type'=>'success',
			);
			$this->success($pay_data);
		}else{
			$this->error($res);
		}
	}


	/**
	 * 异步回调
	 */
	public function notifyUrl(){
		$notifyData=file_get_contents("php://input");
		parse_str($notifyData,$notifyData);
		unset($notifyData['sign']);
		rwlog('aliIsvNotifyUrl',$notifyData);
		$oid=M('MchOrders')->where(['out_trade_no'=>$notifyData['out_trade_no']])->getField('out_trade_no');
		if($oid) {
			$array = array(
				'notify_time' => time(),
				'notify_data' => json_encode($notifyData, JSON_UNESCAPED_UNICODE),
			);
			$rel = M('MchOrders')->where(array('out_trade_no' => $oid))->save($array);
			//使用订单查询结果更新支付订单状态
			if ($rel) {
				$res = self::orderResult($oid);
				if ($res['status'] == true) { //订单支付成功
					die('SUCCESS');
				}
			} else {// 告诉异步结果处理失败
				die('error');
			}
		}else{
			die('error no order');
		}
	}


	/**
	 * 同步页面回调
	 */
	public function ResultData(){
		$out_trade_no=I('get.out_trade_no');
		$rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
		$store = Get_Store($rel['store_id']);
		#点击完成到支付页面
		$codes = M('MchCodes')->where(array('store_id' => $rel['store_id'], 'mch_id' => $rel['mid']))->getField('codes');
		$res = self::orderResult($out_trade_no);
		switch ($res['status']){
			case 1:
				$status='ok'; //结果为ok 视图结果为支付成功
				break;
			default:
				$status=$res['msg'];
				break;
		}
		$assign = array(
			'status' => $status,
			'total' => number_format($rel['total_fee'], 2),
			'mch_name' => $store['name'],
			'time' => date('Y-m-d H:i:s', $rel['createtime']),
			'order_id' => $rel['out_trade_no'],
			'url' => C('MA_DATA_URL') . '/' . $codes
		);
		$this->assign($assign);
		$this->display('Notify/new_result'); //视图页
	}

	/**
	 * 退款
	 * @param $oid
	 */
//	public function tk($oid){
//		$order = M('MchOrders')->where(['out_trade_no' => $oid])->find();
//		$arr = [
//			'out_trade_no' => $oid,
//			'refund_amount'=>$order['total_fee']
//		];
//		$request = new \AlipayTradeRefundRequest();
//		$request->setBizContent(json_encode($arr, JSON_UNESCAPED_UNICODE));
//		$result = $this->setAop($request, true,$order['mid']);
//		dump($result);
//	}

	/**
	 * 查询订单
	 * @param $oid
	 * @return array
	 */
	public function orderResult($oid){
		$order = M('MchOrders')->where(['out_trade_no' => $oid])->find();
		if($order) {
			$arr = [
				'out_trade_no' => $oid,
			];
			$request = new \AlipayTradeQueryRequest();
			$request->setBizContent(json_encode($arr, JSON_UNESCAPED_UNICODE));
			$result = $this->setAop($request, true, $order['mid']);
			if($result['status']){
				if($result['data']['code']=='10000') {
					//接口通信成功
					//交易状态：
					//WAIT_BUYER_PAY（交易创建，等待买家付款）、
					//TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）
					//TRADE_SUCCESS（交易支付成功）、
					//TRADE_FINISHED（交易结束，不可退款）
					switch ($result['data']['trade_status']){
						case 'TRADE_SUCCESS':
							$status=1;
							break;
						case 'TRADE_CLOSED':
							$status=2;
							break;
						default:
							$status=0;
							break;
					}
					//更新数据库结果
					$save = [
						'status' => $status,
						'time_end' => strtotime($result['data']['send_pay_date']),
						'total' => $result['data']['total_amount'],//分单位转换为元
						'transaction_id' => $result['data']['trade_no'],
						'buyer_logon_id'=>$result['data']['buyer_logon_id'],
					];
					M('MchOrders')->where(['out_trade_no' =>$oid])->save($save);
					if ($status == 1) {
						//发送模板消息通知
						R('Pays/Notify/sendTemplateMessage', array($oid)); //发送收款成功模板消息
					}
					$return = ['status' => true, 'res_status' => $status,'result'=>$result, 'msg' => '查询成功'];
				}else{
					$return = ['status' => false, 'msg' => $result['data']['sub_msg']."({$result['data']['msg']}|{$result['data']['sub_code']})"];
				}
			}else{
				$return = ['status' => false, 'msg' => $result['msg']];
			}
		}else{
			$return = ['status' => false, 'msg' => '未知订单'];
		}
		if(I('get.Debug')==2){
			dump([$arr,$result,$return]);
		}else{
			return $return;
		}
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

}