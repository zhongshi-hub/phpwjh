<?php
namespace Pays\Controller;
use Think\Controller;
class PStarposController extends Controller
{


    protected $config;

    public function _initialize(){
        $this->config=[
			'orgNo'=>'66545',
			'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Pays/PStarpos/notifyUrl',
			'apiUrl'=>'https://gateway.starpos.com.cn/adpweb/ehpspos3/',
			'version'=>'V1.0.1',
			'mch_apiUrl'=>'https://gateway.starpos.com.cn/emercapp',
			'mch_orgKey'=>'DD3765B75639811D4D643482072CA2F1',
        ];
    }


	public function api_wx_native($data=[]){
		$subject=urlencode('移动支付买单');
		//通道数据
		$arr = [
			'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
			'characterSet' => '00-GBK',
			'orgNo' => $this->config['orgNo'],
			'mercId' =>$data['alley_data']['mch_id'],//商户号
			'trmNo' => $data['alley_data']['mch_appid'],//设备号
			'tradeNo' => rand_out_trade_no(),//查询单号
			'txnTime' => date('YmdHis'),//设备端交易时间
			'signType' => 'MD5',//签名方式
			'version' => $this->config['version'],
			'amount' => $data['total'],//金额
			'total_amount'=>$data['total'],
			'payChannel'=>'WXPAY',
			'subject'=>$subject,
		];
		$arr['signValue'] = self::getSign($arr, $data['alley_data']['mch_key']);
		$res=curl_calls($this->config['apiUrl'].'sdkBarcodePosPay.json',json_encode($arr),'',true);
		$res=json_decode(urldecode($res),true);
		if($res['returnCode']=='000000'){
			$array = array(
				'trade_type'=>$arr['trmNo'],
				'attach'=>$data['appid'],//此项纪录终端号 必填
				'mid' => $data['store_data']['sid'],
				'store_id' => $data['store_data']['id'],
				'agent_id' => GetMchAid($data['store_data']['sid']),
				'new' => serialize($arr),
				'data' => serialize($data),
				'rel' => serialize($res),
				'createtime' => time(),
				'mch_rate' => $data['alley_data']['rate'],
				'mch_id' => $data['alley_data']['mch_id'],
				'service' => 'wx_scan',
				'out_trade_no'=>$arr['tradeNo'],
				'transaction_id'=>$res['logNo'],
				'body' => $data['store_data']['name'],
				'total_fee' =>$data['total']/100, //数据库按照单位元 存储
				'mch_create_ip' => Get_Clienti_Ips(),
				'type' => 'D1',
				'alleys' => 'Starpos',
				'domain_auth' => $data['alley_data']['domain_auth'],
				'is_raw' => 1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){
				//返回数据给客户端
				return ['code'=>100,'data'=>['result_code'=>'0000','result_msg'=>'预下单成功','method'=>'wx_native','out_trade_no'=>$array['out_trade_no'],'code_url'=>$res['payCode'],'total'=> $array['total_fee'],'create_time'=>date('YmdHis'),'nonce_str'=>uniqid()]];
			}else{
				return ['code'=>400,'data'=>['result_code'=>01,'result_msg'=>'订单创建失败']];
			}
		}else{
			return ['code'=>400,'data'=>['result_code'=>$res['returnCode'],'result_msg'=>$res['message']]];
		}
	}



	public function pay_wx_scan(){
		//通道数据
		$arr = [
			'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
			'characterSet' => '00-GBK',
			'orgNo' => $this->config['orgNo'],
			'mercId' => $this->Mdata['mch_id'],//商户号
			'trmNo' => $this->Mdata['mch_appid'],//设备号
			'tradeNo' => rand_out_trade_no(),//查询单号
			'txnTime' => date('YmdHis'),//设备端交易时间
			'signType' => 'MD5',//签名方式
			'version' => $this->config['version'],
			'amount' => $this->data['total']*100,//金额
			'total_amount'=>$this->data['total']*100,
			'payChannel'=>'WXPAY',
			'subject'=>urlencode($this->Mdata['mch_name'].'移动支付'),
			'authCode'=>$this->data['code_data'],
		];
		$arr['signValue'] = self::getSign($arr, $this->Mdata['mch_key']);
		$res=curl_calls($this->config['apiUrl'].'sdkBarcodePay.json',json_encode($arr),'',true);
		//rwlog('spos_wx_scan',$res);
		$res=json_decode(urldecode($res),true);
		//rwlog('spos_wx_scan',$res);
		//dump($res);
		if(($res['returnCode']=='000000'&&$res['result']=='S')||($res['returnCode']=='000000'&&$res['result']=='A')){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['trmNo'],
				'transaction_id'=>$res['logNo'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'wx_scan',
				'out_trade_no'=>$arr['tradeNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Starpos',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			if($res['result']=='A'){ //等待授权
				$type='loading'; //支付中
				$msg='用户支付确认中...';
				$rel = M('mch_orders')->add($array);
			}elseif ($res['result']=='S'){ //交易成功
				$type='success'; //支付成功
				$array['status']=1;
				$msg='收款成功 金额:'.$this->data['total'];
				$rel = M('mch_orders')->add($array);
			}
			if ($rel) {
				if($type=='success'){
					R('Pays/Notify/sendTemplateMessage',array($arr['tradeNo'])); //接口无异步 这里直接给商户发送收款成功模板消息
				}
				$pay_data = array(
					'msg' => $msg,
					'type' => $type,
					'api' => 'Starpos',
					'out_trade_no' => $arr['tradeNo'],
				);
				$this->success($pay_data);
			} else {
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['message'].'['.$res['returnCode'].']');
		}

	}




	public function pay_ali_scan(){
		//通道数据
		$arr = [
			'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
			'characterSet' => '00-GBK',
			'orgNo' => $this->config['orgNo'],
			'mercId' => $this->Mdata['mch_id'],//商户号
			'trmNo' => $this->Mdata['mch_appid'],//设备号
			'tradeNo' => rand_out_trade_no(),//查询单号
			'txnTime' => date('YmdHis'),//设备端交易时间
			'signType' => 'MD5',//签名方式
			'version' => $this->config['version'],
			'amount' => $this->data['total']*100,//金额
			'total_amount'=>$this->data['total']*100,
			'payChannel'=>'ALIPAY',
			'subject'=>urlencode($this->Mdata['mch_name'].'移动支付'),
			'authCode'=>$this->data['code_data'],
		];
		$arr['signValue'] = self::getSign($arr, $this->Mdata['mch_key']);
		//dump($arr);
		$res=curl_calls($this->config['apiUrl'].'sdkBarcodePay.json',json_encode($arr),'',true);
		//rwlog('spos_ali_scan',$res);
		$res=json_decode(urldecode($res),true);
		//rwlog('spos_ali_scan',$res);
		//dump($res);
		if(($res['returnCode']=='000000'&&$res['result']=='S')||($res['returnCode']=='000000'&&$res['result']=='A')){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['trmNo'],
				'transaction_id'=>$res['logNo'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'ali_scan',
				'out_trade_no'=>$arr['tradeNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Starpos',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			if($res['result']=='A'){ //等待授权
				$type='loading'; //支付中
				$msg='用户支付确认中...';
				$rel = M('mch_orders')->add($array);
			}elseif ($res['result']=='S'){ //交易成功
				$type='success'; //支付成功
				$array['status']=1;
				$msg='收款成功 金额:'.$this->data['total'];
				$rel = M('mch_orders')->add($array);
			}
			if ($rel) {
				if($type=='success'){
					R('Pays/Notify/sendTemplateMessage',array($arr['tradeNo'])); //接口无异步 这里直接给商户发送收款成功模板消息
				}
				$pay_data = array(
					'msg' => $msg,
					'type' => $type,
					'api' => 'Starpos',
					'out_trade_no' => $arr['tradeNo'],
				);
				$this->success($pay_data);
			} else {
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['message'].'['.$res['returnCode'].']');
		}

	}

	


	public function pay_ali_code(){
		$subject=urlencode($this->Mdata['mch_name'].'主扫买单');
		$arr = [
			'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
			'characterSet' => '00-GBK',
			'orgNo' => $this->config['orgNo'],
			'mercId' => $this->Mdata['mch_id'],//商户号
			'trmNo' => $this->Mdata['mch_appid'],//设备号
			'tradeNo' => rand_out_trade_no(),//查询单号
			'txnTime' => date('YmdHis'),//设备端交易时间
			'signType' => 'MD5',//签名方式
			'version' => $this->config['version'],
			'amount' => $this->data['total']*100,//金额
			'total_amount'=>$this->data['total']*100,
			'payChannel'=>'ALIPAY',
			'subject'=>$subject,
		];
		$arr['signValue'] = self::getSign($arr, $this->Mdata['mch_key']);
		$res=curl_calls($this->config['apiUrl'].'sdkBarcodePosPay.json',json_encode($arr),'',true);
		$res=json_decode(urldecode($res),true);
		if($res['returnCode']=='000000'){
			$array=array(
				'trade_type'=>$arr['trmNo'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'ali_code',
				'out_trade_no'=>$arr['tradeNo'],
				'transaction_id'=>$res['logNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'type'=>'D1',
				'alleys'=>'Starpos',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){
				//返回数据给客户端
				$pay_data=array(
					'msg'=>'预下单成功',
					'qrcode'=>$res['payCode'],
					'api'=>'Starpos',
					'out_trade_no'=>$res['tradeNo'],
				);
				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!');
			}
		}else{
			$this->error($res['message'].'['.$res['returnCode'].']');
		}
	}


	public function pay_wx_code(){
		$subject=urlencode($this->Mdata['mch_name'].'主扫买单');
		//通道数据
		$arr = [
			'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
			'characterSet' => '00-GBK',
			'orgNo' => $this->config['orgNo'],
			'mercId' => $this->Mdata['mch_id'],//商户号
			'trmNo' => $this->Mdata['mch_appid'],//设备号
			'tradeNo' => rand_out_trade_no(),//查询单号
			'txnTime' => date('YmdHis'),//设备端交易时间
			'signType' => 'MD5',//签名方式
			'version' => $this->config['version'],
			'amount' => $this->data['total']*100,//金额
			'total_amount'=>$this->data['total']*100,
			'payChannel'=>'WXPAY',
			'subject'=>$subject,
		];
		$arr['signValue'] = self::getSign($arr, $this->Mdata['mch_key']);
		$res=curl_calls($this->config['apiUrl'].'sdkBarcodePosPay.json',json_encode($arr),'',true);
		$res=json_decode(urldecode($res),true);
		if($res['returnCode']=='000000'){
			$array=array(
				'trade_type'=>$arr['trmNo'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'wx_code',
				'out_trade_no'=>$arr['tradeNo'],
				'transaction_id'=>$res['logNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'type'=>'D1',
				'alleys'=>'Starpos',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){
				//返回数据给客户端
				$pay_data=array(
					'msg'=>'预下单成功',
					'qrcode'=>$res['payCode'],
					'api'=>'Starpos',
					'out_trade_no'=>$res['tradeNo'],
				);
				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!');
			}
		}else{
			$this->error($res['message'].'['.$res['returnCode'].']');
		}
	}


	/**
	 * 支付宝服务窗
	 */
	public function pay_ali_jsapi(){
		$order_id = $this->orderNum;
		$arr=[
			'orgNo'=>$this->config['orgNo'],//机构号
			'mercId'=>$this->Mdata['mch_id'],//商户号
			'trmNo'=>$this->Mdata['mch_appid'],//设备号
			'txnTime'=>date('YmdHis'),
			'version'=>$this->config['version'],
			'amount'=>$this->data['total']*100,// 实付金额以分为单位
			'total_amount'=>$this->data['total']*100,// 订单总金额
			'tradeNo'=>$order_id,//订单号
			'ali_user_id'=>$this->data['openid'],//openid
			'subject'=>urlencode($this->Mdata['mch_name'].'买单'),
			'signType'=>'MD5',
		];
		$arr['signValue']=self::getSign($arr,$this->Mdata['mch_key']);
		$res=curl_calls($this->config['apiUrl'].'aliServicePay.json',json_encode($arr),'',true);
		$res=json_decode(urldecode($res),true);
		if($res['returnCode']=='000000'){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['trmNo'],
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
				'out_trade_no'=>$arr['tradeNo'],
				'transaction_id'=>$res['logNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Starpos',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){
				$pay_data=array(
					'msg'=>'订单创建成功',
					'type'=>'js',
					'pay_info'=>array(
						'tradeNO'=>$res['PrepayId']
					),
					'out_trade_no'=>$arr['tradeNo'],
				);

				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['message'].'['.$res['returnCode'].']');
		}
	}

	/**
	 * 微信支付JSAPI
	 */
	public function pay_wx_jsapi()
	{
		$order_id = $this->orderNum;
		self::getWxApp();
		$arr=[
			'orgNo'=>$this->config['orgNo'],//机构号
			'mercId'=>$this->Mdata['mch_id'],//商户号
			'trmNo'=>$this->Mdata['mch_appid'],//设备号
			'txnTime'=>date('YmdHis'),
			'version'=>$this->config['version'],
			'amount'=>$this->data['total']*100,// 实付金额以分为单位
			'total_amount'=>$this->data['total']*100,// 订单总金额
			'selOrderNo'=>$order_id,//订单号
			'openid'=>$this->data['openid'],//openid
			'signType'=>'MD5',
		];
		$arr['signValue']=self::getSign($arr,$this->Mdata['mch_key']);
		$res=curl_calls($this->config['apiUrl'].'pubSigPay.json',json_encode($arr),'',true);
		$res=json_decode(urldecode($res),true);
		//rwlog('pos_wx_js',[$arr,$res]);
		if($res['returnCode']=='000000'){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['trmNo'],
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
				'out_trade_no'=>$arr['selOrderNo'],
				'transaction_id'=>$res['logNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Starpos',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){
				//mch_flow_order($array['mid'], $array['out_trade_no'], $array['total_fee'], 'wx', 0, 2);
				$apiRes=[
					'appId'=>$res['apiAppid'],
					'timeStamp'=>$res['apiTimestamp'],
					'nonceStr'=>$res['apiNoncestr'],
					'package'=>$res['apiPackage'],
					'signType'=>$res['apiSigntype'],
					'paySign'=>$res['apiPaysign'],
				];
				$pay_data=array(
					'msg'=>'订单创建成功',
					'type'=>'js',
					'pay_info'=>$apiRes,
					'out_trade_no'=>$arr['selOrderNo']
				);
				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['message'].'['.$res['returnCode'].']');
		}
	}


	/**
	 * 页面同步回调
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
	 * 异步订单处理
	 */
	public function notifyUrl(){
		$notifyData=file_get_contents("php://input");
		$notifyData=json_decode($notifyData,true);
		//rwlog('xdl_notify',$notifyData);
		$oid=M('MchOrders')->where(['transaction_id'=>$notifyData['logNo']])->getField('out_trade_no');
		if($oid) {
			$array = array(
				'notify_time' => time(),
				'notify_data' => json_encode($notifyData, JSON_UNESCAPED_UNICODE),
				'time_end' => strtotime($notifyData['TxnDate'] . ' ' . $notifyData['TxnTime']),
			);
			$rel = M('MchOrders')->where(array('out_trade_no' => $oid))->save($array);
			//使用订单查询结果更新支付订单状态
			if ($rel) {
				$res = self::orderResult($oid);
				if ($res['status'] == true) { //订单支付成功
					$this->mchDraw($oid);
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
	 * 订单查询
	 * @param $oid
	 * @return array
	 */
	public function orderResult($oid)
	{
		$order = M('MchOrders')->where(['out_trade_no' => $oid])->find();
		if($order) {
			//配置信息先查找轮询列表 如果在轮询列表直接返回
			$map['cid'] = $order['mid'];
			$map['alleys_type'] = $order['alleys'];
			$SellerAlleys = M('MchSellerAlleys')->where($map)->field('mch_id,mch_key,mch_appid')->find();
			//通道数据
			$arr = [
				'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
				'characterSet' => '00-GBK',
				'orgNo' => $this->config['orgNo'],
				'mercId' => $order['mch_id'],//商户号
				'trmNo' => $order['trade_type'],//设备号
				'tradeNo' => rand_out_trade_no(),//查询单号
				'txnTime' => date('YmdHis'),//设备端交易时间
				'signType' => 'MD5',//签名方式
				'version' => $this->config['version'],
				'qryNo' => $order['transaction_id'],//交易单号
			];
			$arr['signValue'] = self::getSign($arr, $SellerAlleys['mch_key']);
			$res = curl_calls($this->config['apiUrl'] . 'sdkQryBarcodePay.json', json_encode($arr), '', true);
			$res = json_decode(urldecode($res), true);
			//rwlog('xdl_orderResult',$res);
			if ($res['returnCode'] == '000000') {
				//查询成功
				switch ($res['result']) {
					case 'S':
						$status = $res['message']=='交易已退款'?2:1;
						break;
					default:
						$status = 0;
						break;
				}
				//更新数据库结果
				$save = [
					'status' => $status,
					'time_end' => strtotime($res['sysTime']),
					'total' => $res['amount'] / 100,//分单位转换为元
					'out_transaction_id' => $res['orderNo'],
				];
				M('MchOrders')->where(['out_trade_no' =>$oid])->save($save);
				if ($status == 1) {
					//发送模板消息通知
					R('Pays/Notify/sendTemplateMessage', array($oid)); //发送收款成功模板消息
				}
				//更新流量订单结果
				//mch_flow_order_update($oid,$status);
				$return = ['status' => true, 'res_status' => $status, 'msg' => '查询成功'];
			} else {
				$return = ['status' => false, 'msg' => $res['message'] . '[' . $res['returnCode'] . ']'];
			}
		}else{
			$return = ['status' => false, 'msg' => '未知订单'];
		}
		if(I('get.Debug')==2){
			dump([$arr,$res,$return]);
		}else{
			return $return;
		}
	}




	/**
	 * 退款操作 D0商户,无法退款!
	 * @param $oid
	 */
	public function refund($oid){
		$order = M('MchOrders')->where(['out_trade_no' => $oid])->find();
		if($order) {
			//配置信息先查找轮询列表 如果在轮询列表直接返回
			$map['cid'] = $order['mid'];
			$map['alleys_type'] = $order['alleys'];
			$SellerAlleys = M('MchSellerAlleys')->where($map)->field('mch_id,mch_key,mch_appid')->find();
			$arr = [
				'opSys' => 3,//0:ANDROID sdk 1:IOS sdk 2:windows sdk 3: 直连
				'characterSet' => '00-GBK',
				'orgNo' => $this->config['orgNo'],
				'mercId' => $order['mch_id'],//商户号
				'trmNo' => $order['trade_type'],//设备号
				'tradeNo' => rand_out_trade_no(),//查询单号
				'txnTime' => date('YmdHis'),//设备端交易时间
				'signType' => 'MD5',//签名方式
				'version' => $this->config['version'],
				'orderNo' => $order['out_transaction_id'],//交易单号
			];
			$arr['signValue'] = self::getSign($arr, $SellerAlleys['mch_key']);
			$res = curl_calls($this->config['apiUrl'] . 'sdkRefundBarcodePay.json', json_encode($arr), '', true);
			$res = json_decode(urldecode($res), true);
			if($res['returnCode']&&$res['result']=='S'){
				$save = [
					'status' => 2,
				];
				M('MchOrders')->where(['out_trade_no' =>$oid])->save($save);
				$return = ['status' => true, 'res_status' => 2, 'msg' => '退款成功'];
			}else{
				$return = ['status' => false, 'msg' => $res['message'] . '[' . $res['returnCode'] . ']'];
			}
		}else{
			$return = ['status' => false, 'msg' => '未知订单'];
		}
		if(I('get.Debug')==2){
			dump([$arr,$res,$return]);
		}else{
			return $return;
		}
	}

	/**
	 * 获取微信配置参数
	 * 首次配置后利用缓存查询一次配置参数
	 * @return mixed
	 */
	public function getWxApp(){
		$arr=[
			'orgNo'=>$this->config['orgNo'],//机构号
			'mercId'=>$this->Mdata['mch_id'],//商户号
			'trmNo'=>$this->Mdata['mch_appid'],//设备号
			'txnTime'=>date('YmdHis'),
			'signType'=>'MD5',
			'version'=>$this->config['version'],
		];
		$arr['signValue']=self::getSign($arr,$this->Mdata['mch_key']);
		$cache='pos_load_wx1_'.date('YmdHis').'_'.$this->Mdata['mch_id'].'_'.$this->Mdata['mch_appid'];
		//	rwlog($cache, $arr);
		if(!S($cache)) {
			$res = curl_calls($this->config['apiUrl'].'pubSigQry.json', json_encode($arr), '', true);
			$res = json_decode(urldecode($res), true);
			//rwlog($cache, $res);
			if($res['returnCode']=='000000') {
				S($cache, [$arr, $res]);
			}else{
				//	rwlog($cache, $res);
			}
			return $res;
		}else{
			return S($cache);
		}
	}


	/**
	 * 商户进件
	 */
	public function mch_in(){
		$alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
		if(!$alleys){
			$this->error('获取参数失败！请重新登录后再试！');
		}
		if(!$alleys['pos_mcc']){
			$this->error('获取新大陆MCC码失败,请先编辑商户资料更新对应的行业MCC码后再进件！');
		}
		$stoe_area_cod=ccb_area($alleys['mch_district'],$alleys['mch_citys']);
		if(!$stoe_area_cod){
			$this->error('获取省市区编码失败,请更换商户地址所在区信息后重试');
		}
		$arr=[
			'version'=>'V1.0.5',
			'wx_pay_catalog'=>'http://'.$_SERVER['HTTP_HOST'].'/Pay/',//授权目录
			'stl_oac'=>$alleys['mch_bank_cid'],//结算账户
			'bnk_acnm'=>$alleys['mch_bank_name'],//户名
			'wc_lbnk_no'=>$alleys['mch_linkbnk'],//联行行号 12 位数字 校验正确性
			'stoe_nm'=>$alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_name'],//签购单名称= 省市+门店名 8-20 个数字、 字母、汉字 不能全为数字
			'stoe_cnt_nm'=>$alleys['mch_card_name'],//联系人名称
			'stoe_cnt_tel'=>$alleys['mch_tel'],//联系人手机 号
			'mcc_cd'=>$alleys['pos_mcc'],//MCC 码，校验合 法性
			'stoe_area_cod'=>$stoe_area_cod,//地区码
			'stoe_adds'=>$alleys['mch_district'] . $alleys['mch_address'],//商户地址
			'alipay_flg'=>'Y',//Y-选择 N-不选择 最少选一个产品
			'yhkpay_flg'=>'N',//Y-选择 N-不选择 最少选一个产品
			'trm_scan'=>'1',//扫码设备终 端范围在 0-10(有扫码 产品，才能有 扫码设备终 端为空时)
			'mailbox'=>$alleys['mch_tel'].'@163.com',//联系人邮箱
			'fee_rat_scan'=>bcdiv($alleys['rate'], '10', 2),//微信费率(%)
			'fee_rat3_scan'=>bcdiv($alleys['rate'], '10', 2),//支付宝(%)
		];
//		if($alleys['mch_bank_type']=='企业账户'){
		$arr['stl_typ']=1;//1 T+1 2 D+1(对公账户不能 选择 D+1)
//		}else{
//			$arr['stl_typ']=2;//1 T+1 2 D+1(对公账户不能 选择 D+1)
//			$arr['service_fee']=0.02;//D+1 服务费率
//		}
		if($alleys['mch_bus_type']=='企业'){
			$arr['mercNm']=$alleys['mch_name'];//商户经营名称
			$arr['incom_type']=2;//1小微 2企业 3 快速
			$arr['bus_lic_no']=$alleys['qy_cid'];//营业执照号
			$arr['bse_lice_nm']=$alleys['qy_name'];//营业执照名
			$arr['crp_nm']=$alleys['qy_fr_name'];//法人姓名
			$arr['mercAdds']=$alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_district'] . $alleys['mch_address'];//营业执照地 址
			$arr['bus_exp_dt']=$alleys['qy_time']?$alleys['qy_time']:'9999-12-31';//营业执照有 限期(永久 9999-12-3 1)
			$arr['crp_id_no']=$alleys['qy_fr_cid'];//法人身份证
			$arr['crp_exp_dt']=$alleys['card_time']?$alleys['card_time']:'9999-12-31';//法人身份证 有限期(永久 9999-12-3 1)
			if($alleys['mch_bank_type']=='企业账户'){
				$arr['stl_sign']=0;//1-对私 0-对公(快速进 件,小微对私;)
			}else{
				$arr['stl_sign']=1;//1-对私 0-对公(快速进 件,小微对私;)
				$arr['icrp_id_no']=$alleys['mch_card_id'];//结算人身份 证号
				$arr['crp_exp_dt_tmp']=$alleys['card_time']?$alleys['card_time']:'9999-12-31';//结算标志为 1- 对私必输 对私:输入格式 1999-12-31 对公:默认法人 身份证到期日
			}
		}elseif ($alleys['mch_bus_type']=='小微'){
			$arr['mercNm']=$alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_name'];//商户经营名称
			$arr['incom_type']=1;//1小微 2企业 3 快速
			$arr['stl_sign']=1;//1-对私 0-对公(快速进 件,小微对私;)
			$arr['icrp_id_no']=$alleys['mch_card_id'];//结算人身份 证号
			$arr['mercAdds']=$alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_district'] . $alleys['mch_address'];//营业执照地 址
			$arr['crp_exp_dt']=$alleys['card_time']?$alleys['card_time']:'9999-12-31';//法人身份证 有限期(永久 9999-12-3 1)
			$arr['bus_exp_dt']=$alleys['card_time']?$alleys['card_time']:'9999-12-31';//营业执照有 限期(永久 9999-12-3 1)
			$arr['crp_id_no']=$alleys['mch_card_id'];//法人身份证
			$arr['crp_nm']=$alleys['mch_card_name'];//法人姓名
			$arr['bus_lic_no']=$alleys['mch_card_id'];//营业执照号
			$arr['bse_lice_nm']=$alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_name'];//营业执照名
			$arr['crp_exp_dt_tmp']=$alleys['card_time']?$alleys['card_time']:'9999-12-31';//结算标志为 1- 对私必输 对私:输入格式 1999-12-31 对公:默认法人 身份证到期日
		}else{
			$arr['mercNm']=$alleys['mch_name'];//商户经营名称
			$arr['incom_type']=3;//1小微 2企业 3 快速
			$arr['stl_sign']=1;//1-对私 0-对公(快速进 件,小微对私;)
			$arr['icrp_id_no']=$alleys['mch_card_id'];//结算人身份 证号
			$arr['crp_exp_dt_tmp']=$alleys['card_time']?$alleys['card_time']:'9999-12-31';//结算标志为 1- 对私必输 对私:输入格式 1999-12-31 对公:默认法人 身份证到期日
		}
		//dump([$arr,$upImg]);
		$res = $this->mch_send('6060601',$arr);
		if($res['msg_cd']=='000000'){
			M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save(['status'=>1,'mch_id'=>$res['mercId']]);
			//上传商户图片
			$upImg=$this->mch_upImg($alleys,$res);
			//提交商户入驻
			$end=$this->mch_end($res['mercId'],$res['log_no']);
			//配置默认通道为这个
			M('mchSeller')->where(['id'=>$this->data['cid']])->save(['wx_alleys'=>'Starpos','ali_alleys'=>'Starpos']);
			if($end['msg_cd']=='000000'){
				switch ($end['check_flag']){
					case 1:
						$msg='通过';
						break;
					case 2:
						$msg='驳回';
						break;
					case 3:
						$msg='转人工审核';
						break;
					default:
						$msg='FLAG:'.$end['check_flag'];
						break;
				}
				$this->success('初审通过,终审结果:'.$msg);
			}else{
				$this->error('初审通过,终审失败,请前往通道平台补充资料。终审结果:'.$end['msg_dat'].'('.$end['msg_cd'].')');
			}
		}else{
			$this->error($res['msg_dat'].'('.$res['msg_cd'].')');
		}
	}



	/**
	 * 图片上传
	 * @param $data
	 * @param $rel
	 * @return array
	 */
	public function mch_upImg($data,$rel){
		$imgArr=[
			[
				'name'=>'card1.jpg',
				'type'=>4,
				'url'=>$data['mch_img_z'],
			],
			[
				'name'=>'card2.jpg',
				'type'=>5,
				'url'=>$data['mch_img_p'],
			]
		];

		if($data['mch_bus_type']=='企业'){
			$imgArr[]=[
				'name'=>'yyzz.jpg',
				'type'=>1,
				'url'=>$data['mch_img_yyzz'],
			];
			$imgArr[]=[
				'name'=>'mtz.jpg',
				'type'=>6,
				'url'=>$data['mch_img_m1'],
			];
			$imgArr[]=[
				'name'=>'cjz.jpg',
				'type'=>7,
				'url'=>$data['mch_img_m2'],
			];
			$imgArr[]=[
				'name'=>'syt.jpg',
				'type'=>8,
				'url'=>$data['mch_img_m3'],
			];
			$imgArr[]=[
				'name'=>'nulls.jpg',
				'type'=>13,
				'url'=>'',
			];
			if($data['mch_img_m4']){
				$imgArr[]=[
					'name'=>'shxy.jpg',
					'type'=>14,
					'url'=>$data['mch_img_m4'],
				];
			}
			if($data['mch_img_m5']){
				$imgArr[]=[
					'name'=>'shxx.jpg',
					'type'=>15,
					'url'=>$data['mch_img_m5'],
				];
			}

			if($data['mch_bank_type']=='企业账户'){
				$imgArr[]=[
					'name'=>'khxkz.jpg',
					'type'=>12,
					'url'=>$data['mch_img_bank'],
				];
			}else{
				$imgArr[]=[
					'name'=>'bank.jpg',
					'type'=>11,
					'url'=>$data['mch_img_bank'],
				];
				if($data['mch_bank_type_s']==1){
					$imgArr[] = [
						'name' => 'authcard3.jpg',
						'type' => 9,
						'url' => $data['mch_img_auth_z'],
					];
					$imgArr[] = [
						'name' => 'authcard4.jpg',
						'type' => 10,
						'url' => $data['mch_img_auth_p'],
					];
					$imgArr[] = [
						'name' => 'authbanksq.jpg',
						'type' => 16,
						'url' => $data['mch_img_sqh'],
					];
				}else {
					$imgArr[] = [
						'name' => 'card3.jpg',
						'type' => 9,
						'url' => $data['mch_img_z'],
					];
					$imgArr[] = [
						'name' => 'card4.jpg',
						'type' => 10,
						'url' => $data['mch_img_p'],
					];
				}
			}
		}elseif ($data['mch_bus_type']=='小微'){
			$imgArr[]=[
				'name'=>'nulls.jpg',
				'type'=>1,
				'url'=>'',
			];
			$imgArr[]=[
				'name'=>'mtz.jpg',
				'type'=>6,
				'url'=>$data['mch_img_m1'],
			];
			$imgArr[]=[
				'name'=>'cjz.jpg',
				'type'=>7,
				'url'=>$data['mch_img_m2'],
			];
			$imgArr[]=[
				'name'=>'syt.jpg',
				'type'=>8,
				'url'=>$data['mch_img_m3'],
			];
			$imgArr[]=[
				'name'=>'card3.jpg',
				'type'=>9,
				'url'=>$data['mch_img_z'],
			];
			$imgArr[]=[
				'name'=>'card4.jpg',
				'type'=>10,
				'url'=>$data['mch_img_p'],
			];
			$imgArr[]=[
				'name'=>'bank.jpg',
				'type'=>11,
				'url'=>$data['mch_img_bank'],
			];
			$imgArr[]=[
				'name'=>'cardsc.jpg', //手持
				'type'=>13,
				'url'=>$data['mch_img_s'],
			];

			if($data['mch_img_m4']){
				$imgArr[]=[
					'name'=>'shxy.jpg',
					'type'=>14,
					'url'=>$data['mch_img_m4'],
				];
			}
			if($data['mch_img_m5']){
				$imgArr[]=[
					'name'=>'shxx.jpg',
					'type'=>15,
					'url'=>$data['mch_img_m5'],
				];
			}
		}else{
			$imgArr[]=[
				'name'=>'bank.jpg',
				'type'=>11,
				'url'=>$data['mch_img_bank'],
			];
			$imgArr[]=[
				'name'=>'card3.jpg',
				'type'=>9,
				'url'=>$data['mch_img_z'],
			];
			$imgArr[]=[
				'name'=>'card4.jpg',
				'type'=>10,
				'url'=>$data['mch_img_p'],
			];
		}
		$ret=[];
		foreach ($imgArr as $k=>$v) {
			$arr = [
				'version'=>'V1.0.1',
				'mercId' => $rel['mercId'],
				'log_no' =>$rel['log_no'],
				'stoe_id' =>$rel['stoe_id'],
				'imgTyp' => $v['type'],//9-结算人身份 证正面照 10-结算人身份 证反面照 11-银行卡照
				'imgNm' => $v['name'],
				'imgFile' => $this->imgData($v['url']),
			];
			$res = $this->mch_send('6060606',$arr);
			$ret[]=$res;
		}
		return $ret;
	}


	/**
	 * 图片转16进制
	 * @param $img_file
	 * @return string
	 */
	public function imgData($img_file){

		if (preg_match('/(http:\/\/)|(https:\/\/)/i', $img_file)) {
			#网络图片
			$url=$img_file;
		}else {
			$url='http://'.$_SERVER['HTTP_HOST'].ltrim($img_file,'.');
		}
		$content = file_get_contents($url);
		return bin2hex($content);
	}


	/**
	 * 商户提交
	 * @param $mchId
	 * @param $log_no
	 * @return array
	 */
	public function mch_end($mchId,$log_no){
		$arr=[
			'mercId'=>$mchId,
			'log_no'=>$log_no,

		];
		$res = $this->mch_send('6060603',$arr);
		if($res['msg_cd']=='000000'&&$res['check_flag']==1){

			$save=[
				'mch_key'=>$res['key'],
				'mch_appid'=>$res['REC'][0]['trmNo'],
				'load_status'=>1
			];
			M('MchSellerAlleys')->where(array('mch_id' => $arr['mercId'], 'alleys_type' =>'Starpos'))->save($save);
		}
		return $res;
	}

	/**
	 * 商户状态审核查询
	 * @param $mchId
	 */
	public function mch_status($mchId=null){
		$arr=[
			'mercId'=>$mchId?$mchId:$this->data['mch_id'],
		];
		$res = $this->mch_send('6060300',$arr);
		if($res['msg_cd']=='000000'&&$res['check_flag']==1){
			$save=[
				'mch_key'=>$res['key'],
				'mch_appid'=>$res['REC'][0]['trmNo'],
				'load_status'=>1
			];
			M('MchSellerAlleys')->where(array('mch_id' => $arr['mercId'], 'alleys_type' =>'Starpos'))->save($save);
			$this->success('状态更新成功','',888);
		}else{
			$this->error($res['msg_dat'].'('.$res['msg_cd'].')','',888);
		}
	}


	/**
	 * 获取mcc
	 */
	public function get_mcc(){
		$res = $this->mch_send('6060203',[]);
		$cache_name='starpos_mcc';
		if(S($cache_name)){
			$data=S($cache_name);
		}else {
			if ($res['msg_cd'] == '000000') {
				$data = ['status' => 1, 'msg' => '获取mcc成功', 'data' => $res['REC']];
				S($cache_name,$data);
			} else {
				$data = ['status' => 0, 'msg' => '获取mcc失败' . $res['msg_dat'] . '(' . $res['msg_cd'] . ')'];
			}
		}
		if(I('get.json')) {
			header('Content-Type:application/json; charset=utf-8');
			die(json_encode($data, JSON_UNESCAPED_UNICODE));
		}else{
			if($data['status']==1){
				$end=[];
				foreach ($data['data'] as $v){
					$end[]=[
						'name'=>$v['mcc_nm'],
						'mcc'=>$v['mcc_cd'],
					];
				}
				$data['data']=$end;
			}
			return $data;
		}
	}

	/**
	 * 进件提交方法
	 * @param $serviceId
	 * @param $arr
	 * @return array
	 */
	public function mch_send($serviceId,$arr){
		$arr['serviceId']=$serviceId;
		if(!array_key_exists('version',$arr)) {
			$arr['version'] = $this->config['version'];
		}
		$arr['orgNo']=$this->config['orgNo'];
		$arr['signValue'] = self::getSign($arr, $this->config['mch_orgKey']);
		$res = $this->curl_calls($this->config['mch_apiUrl'],$arr);
		return $res;
	}


	public  function curl_calls($url, $d)
	{

		$data=iconv('utf-8','gbk',json_encode($d,JSON_UNESCAPED_SLASHES));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 200);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// 获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (1 == strpos("$".$url, "https://"))
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=GBK',
			"Accept: application/json",
		));
		$res = curl_exec($ch);
		if ($res == NULL) {
			$res = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
		}
		curl_close($ch);
		$res=json_decode(iconv('gbk','utf-8',$res),true);
		//记录日志
		//	rwlog('xdl_pos_curl_'.date('Ymd'),['提交数据'=>$data,'返回数据'=>$res]);
		return $res; //转换为UTF8格式
	}



	/**
	 * 签名
	 * @param $arr
	 * @param $key
	 * @return string
	 */
	public function getSign($arr,$key,$type=false)
	{
		ksort($arr);
		$temp_arr = '';
		$no_sign_arr=['service_fee','wx_pay_catalog','signValue','prov_nm','city_nm','mercNm','icrp_id_no','crp_exp_dt_tmp','trm_scan','fee_rat_scan','fee_rat3_scan','imgFile','bus_lic_no','bse_lice_nm','crp_nm','mercAdds','bus_exp_dt','crp_id_no','crp_exp_dt'];
		foreach($arr as $k=>$val){
			if(in_array($k,$no_sign_arr))continue;
			$temp_arr = $temp_arr.$val;
		}
		$signValue = md5($temp_arr.$key);
		//rwlog('xdl_pos_sign_'.date('Ymd'),['提交数据'=>$temp_arr.$key,'返回签名'=>$signValue]);
		return $type?$temp_arr.$key:$signValue;
	}

	public function mchDraw($oid){
		$order = M('MchOrders')->where(['out_trade_no' => $oid,'status'=>1])->find();
		if($order) {
			$ret = $this->getDrawData($order['mch_id']);
			if ($ret['repCode'] == '000000') {
				$setting=getXdlDrawSetting($order['mid'],$order['mch_id']);
				if($setting >=10) { //设置的大于10的进入提现列表 10以下无效
					//获取当前ID的
					foreach ($ret['respBody'] as $v) {
						$balance = $v['balance'] / 100;
						$fee = round(($balance * $v['service_fee']) / 10000, 2);
						$fee = $fee < 0.5 ? 0.5 : $fee;
						if ($balance >= $setting && $v['flag'] == 0) { //只有大于设置的提现金额且开通提现权限 才自动发起提现
							$this->sendDraw($v['merc_id'], $v['stoe_id'], $v['balance'], $fee);
						}
					}
				}
			}
		}
	}

	/**
	 * 商户端商户门店信息汇总入口
	 * @param $id
	 * @param bool $is_mch_id
	 * @return array
	 */
	public function getMchDraw($id,$is_mch_id=false){
		//获取当前商户下的商户号
		if($is_mch_id){
			$mch_id=$id;
		}else {
			$mch_id = M('MchSellerAlleys')->where(array('cid' => $id, 'alleys_type' => 'Starpos'))->getField('mch_id');
		}
		if($mch_id) {
			$arr = [
				'mercId' => $mch_id,
			];
			$res = $this->mch_send('6060300', $arr);
			if ($res['msg_cd'] == '000000' && $res['check_flag'] == 1) {
				//通过审核 然后查询提现状态
				$ret = $this->getDrawData($mch_id);
				if ($ret['repCode'] == '000000') {
					$return = ['status' => 1, 'msg' => '获取提现信息成功', 'mch_id' => $mch_id, 'data' => $ret['respBody']];
				} elseif ($ret['repCode'] == '000002') {
					//没有开通提现权限
					$r = [];
					foreach ($res['REC'] as $v) {
						$r[] = [
							'merc_id' => $mch_id,
							'stoe_id' => $v['stoe_id'],
							'flag' => 1, //提现标志 1关闭 0开通
						];
					}
					$return = ['status' => 1, 'msg' => '获取提现信息成功', 'mch_id' => $mch_id, 'data' => $r];
				} else {
					$return = ['status' => 0, 'msg' => $ret['repMsg'], 'mch_id' => $mch_id, 'store' => $res['REC']];
				}
			} else {
				$return = ['status' => 0, 'msg' => $res['msg_dat'], 'mch_id' => $mch_id, 'data' => $res];
			}
		}else{
			$return = ['status' => 0, 'msg' => '未开通当前通道'];
		}
		return $return;
	}

	/**
	 * 获取提现信息
	 * @param $mchId
	 * @return array
	 */
	public function getDrawData($mchId){
		$arr=[
			'version'=>'V1.0.0',
			'merc_id'=>$mchId,
			'signType'=>'MD5'
		];
		$res = $this->mch_send('6060661',$arr);
		$res['repMsg']=urldecode($res['repMsg']);
		$res['respBody']=json_decode(urldecode($res['respBody']),true);
		return $res;
	}

	/**
	 * 启用提现权限
	 * @param $mchId
	 * @param $stoeId
	 * @return array
	 */
	public function openDraw($mchId,$stoeId){
		$arr=[
			'version'=>'V1.0.0',
			'signType'=>'MD5',
			'reqBody'=>(string)json_encode([
				[
					'merc_id'=>$mchId,
					'stoe_id'=>$stoeId
				]
			]),
		];
		$res = $this->mch_send('6060660',$arr);
		$res['repMsg']=urldecode($res['repMsg']);
		$res['respBody']=json_decode(urldecode($res['respBody']),true);
		if($res['repCode']=='000000'&&$res['respBody'][0]['sts']==0){
			$return=['status'=>1,'msg'=>'提现权限已成功启用','res'=>$res];
		}else{
			$return=['status'=>0,'msg'=>'提现权限启用失败-'.$res['repMsg'],'res'=>$res];
		}
		return $return;
	}

	/**
	 * 关闭提现权限
	 * @param $mchId
	 * @param $stoeId
	 * @return array
	 */
	public function closeDraw($mchId,$stoeId){
		$arr=[
			'version'=>'V1.0.0',
			'signType'=>'MD5',
			'reqBody'=>(string)json_encode([
				[
					'merc_id'=>$mchId,
					'stoe_id'=>$stoeId
				]
			]),
		];
		$res = $this->mch_send('6060664',$arr);
		$res['repMsg']=urldecode($res['repMsg']);
		$res['respBody']=json_decode(urldecode($res['respBody']),true);
		if($res['repCode']=='000000'&&$res['respBody'][0]['sts']==0){
			$return=['status'=>1,'msg'=>'提现权限已成功关闭','res'=>$res];
		}else{
			$return=['status'=>0,'msg'=>'提现权限关闭失败'.$res['repMsg'],'res'=>$res];
		}
		return $return;
	}

	/**
	 * 发起提现
	 * @param $mchId商户号
	 * @param $stoeId门店号
	 * @param $txn_amt提现金额单位分
	 * @param $pre_fee提现手续费单位分
	 * @return array
	 */
	public function sendDraw($mchId,$stoeId,$txn_amt,$pre_fee){
		$arr=[
			'version'=>'V1.0.1',
			'signType'=>'MD5',
			'tot_amt'=>$txn_amt,//以分为单位
			'tot_fee'=>$pre_fee,//以分为单位
			'reqBody'=>(string)json_encode([
				[
					'merc_id'=>$mchId,
					'stoe_id'=>$stoeId,
					'txn_amt'=>$txn_amt,//以分为单位
					'pre_fee'=>$pre_fee,//以分为单位
				]
			]),
		];
		$res = $this->mch_send('6060662',$arr);
		$res['repMsg']=urldecode($res['repMsg']);
		$res['respBody']=json_decode(urldecode($res['respBody']),true);
		//	rwlog('xdl_pos_draw_'.date('Ymd'),['提交数据'=>$arr,'返回数据'=>$res]);
		if($res['repCode']=='000000'){
			$return=['status'=>1,'msg'=>'提现发起成功','res'=>$res];
		}else{
			$return=['status'=>0,'msg'=>$res['repMsg'],'res'=>$res];
		}
		return $return;
	}

	/**
	 * 获取提现记录
	 * @param $mchId
	 * @return array
	 */
	public function drawOrder($mchId){
		$arr=[
			'version'=>'V1.0.0',
			'signType'=>'MD5',
			'merc_id'=>$mchId,
		];
		$res = $this->mch_send('6060663',$arr);
		$res['repMsg']=urldecode($res['repMsg']);
		$res['respBody']=json_decode(urldecode($res['respBody']),true);
		if($res['repCode']=='000000'){
			$return=['status'=>1,'msg'=>'提现流水获取成功','res'=>$res['respBody']];
		}else{
			$return=['status'=>0,'msg'=>$res['repMsg'],'res'=>$res];
		}
		return $return;
	}



}
