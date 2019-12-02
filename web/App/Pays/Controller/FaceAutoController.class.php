<?php
/**
 * 微信支付合一支付
 * auth: chencunlong
 * email:chencunlong@126.com
 */
namespace Pays\Controller;
use Think\Controller;
use think\Log;

class FaceAutoController extends Controller
{
	protected  $config;
	protected  $aop;
	public function _initialize(){
		Vendor('ali_dmf.AopSdk');
		Vendor('xun_wxpay.WxPayApi');
		Vendor('xun_wxpay.MicroPay');
		Vendor('xun_wxpay.WxPayNotify');
		$config=M('MchPayConfig')->where(['domain_auth'=>domain_auth()])->find();
		//基础配置信息
		$this->config=[
			'pid'=>$config['ali_isv_pid'],
			'appid'=>$config['ali_isv_appid'],
			'rsaPublicKey'=>$config['ali_isv_public_key'],
			'rsaPrivateKey'=>$config['ali_isv_private_key'],
			'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Pays/FaceAuto/aliNotify',
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


	/**
	 * 支付入口
	 * @param $data
	 * @return array
	 */
	public function pay($data){
		if(!array_key_exists('total',$data)||empty($data['total'])){
			return ['code'=>103,'msg'=>'total不可为空'];
		}
		if(!array_key_exists('authCode',$data)||empty($data['authCode'])){
			return ['code'=>103,'msg'=>'authCode不可为空'];
		}
		//判断是微信还是支付宝
		$payType=$this->payType($data['authCode']);
		$payType=explode('_',$payType);
		//判断商户目前使用的通道
		$type=$payType[0]=='wxPay'?'wx':'ali';
		$alleys = M('MchSeller')->where(array('id' => $data['store_data']['sid']))->getField($type.'_alleys');
		if(empty($alleys)||$alleys=='Aliisv'||$alleys=='WxPay'){
			return $this->$payType[0]($data,$payType[1]);
		}else{
			$data['alley'] = self::mchAlley($alleys,$data['store_data']['sid']);
			//兼容三方通道
			$module = A('Pays/P' . $alleys);
			$modules = method_exists($module, $type.'FaceApi');
			if ($modules) {
				return R('Pays/P' . $alleys . '/'.$type.'FaceApi',[$data,$payType[1]]);
			} else {
				//当前通道为支持此方法 返回错误提示
				return ['code'=>103,'msg'=>"Fail(".strtoupper($alleys.$type).")"];
			}
		}
	}


	/**
	 * 订单撤销
	 * @param $data
	 * @return array
	 */
	public function orderCancel($data){
		$order=M('MchOrders')->where(['out_trade_no'=>$data['out_trade_no']])->find();
		if($order){
			if($order['alleys']=='Aliisv'){
				$res=$this->aliOrderCancel($order);
				if($res['status']){
					return ['code'=>100,'msg'=>$res['msg'],'data'=>[
						'out_trade_no'=>$data['out_trade_no'],
					]];
				}
				return ['code'=>400,'msg'=>$res['msg']];
			}elseif($order['alleys']=='WxPay'){
				$res=$this->wxOrderCancel($order);
				if($res['status']){
					return ['code'=>100,'msg'=>$res['msg'],'data'=>[
						'out_trade_no'=>$data['out_trade_no'],
					]];
				}
				return ['code'=>400,'msg'=>$res['msg']];
			}
			return ['code'=>400,'msg'=>'非设备交易单号,请登录管理平台查询'];
		}
		return ['code'=>400,'msg'=>'订单号无效'];
	}

	/**
	 * 退款入口
	 * @param $data
	 * @return array
	 */
	public function orderRefund($data){
		$order=M('MchOrders')->where(['mid'=>$data['store_data']['sid'],'out_trade_no'=>$data['out_trade_no']])->find();
		if($order){
			$service=explode('_',$order['service']);
			if($service[1]=='refund'){
				return ['code' => 400, 'msg' =>'订单号非交易订单'];
			}else {
				$order['api_data'] = $data;
				if ($order['alleys'] == 'Aliisv') {
					$res = $this->aliOrderRefund($order);
					if ($res['status']) {
						return ['code' => 100, 'msg' => $res['msg'], 'data' => [
							'out_trade_no' => $data['out_trade_no'],
							'refund_total' => $res['data']['send_back_fee'] * 100,//转换为分单位
							'pay_type' => explode('_', $order['service'])[0]
						]];
					}
					return ['code' => 400, 'msg' => $res['msg']];
				} elseif ($order['alleys'] == 'WxPay') {
					$res = $this->wxOrderRefund($order);
					if ($res['status']) {
						return ['code' => 100, 'msg' => $res['msg'], 'data' => [
							'out_trade_no' => $data['out_trade_no'],
							'refund_total' => $res['data']['refund_fee'],
							'pay_type' => explode('_', $order['service'])[0]
						]];
					}
					return ['code' => 400, 'msg' => $res['msg']];
				}
			}
			return ['code' => 400, 'msg' => '非设备交易单号,请登录管理平台查询'];
		}
		return ['code'=>400,'msg'=>'订单号无效'];
	}

	/**
	 * 微信订单撤销
	 * @param $order
	 * @return array
	 */
	protected function wxOrderCancel($order){
       $input=new \WxPayReverse();
		$input->SetOut_trade_no($order['out_trade_no']);
		$input->SetSubMch_id($order['mch_id']);
		$result = \WxPayApi::reverse($input);
		if($result['return_code']=='SUCCESS'){
			if($result['result_code']=='SUCCESS'){
				$this->wxOrderResult($order['out_trade_no']);
				return ['status'=>true,'msg'=>'订单已撤销','data'=>$result];
			}
			return ['status'=>false,'msg'=>$result['err_code_des']];
		}
		return ['status'=>false,'msg'=>$result['return_msg']];
	}

	/**
	 * 支付宝订单撤销
	 * @param $order
	 * @return array
	 */
	protected function aliOrderCancel($order){
		// 如果查询结果不为成功，则调用撤销
		$request = new \AlipayTradeCancelRequest();
		$request->setBizContent(json_encode(['out_trade_no'=>$order['out_trade_no']],JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$order['mid']);
		if($result['status']){
			$result=$result['data'];
			if ($result['code'] == '10000') {
				$this->aliOrderResult($order['out_trade_no']);
				return ['status' => true,'msg'=>'订单已撤销','data' => $result];
			}
			return ['status' => false, 'msg' => $result['sub_msg']];
		}
		return ['status' => false, 'msg' => $result['msg']];
	}

	/**
	 * 支付宝退款
	 * @param array $order
	 * @return array
	 */
	protected function aliOrderRefund($order=[]){
		//查询当前订单已退金额
		$refund_total_sum=M('MchOrders')->where(['status'=>1,'out_transaction_id'=>$order['out_trade_no']])->sum('total_fee');
		//查询还可退
		$end_total=bcsub($order['total_fee'],$refund_total_sum,2);
		if($end_total<0.01){
			return ['status'=>false,'msg'=>'无可退款金额'];
		}
		$api_data_total=$order['api_data']['total'];
		$total=$api_data_total?($api_data_total/100):$end_total; //单位元
		$refund_no='T'.$this->getOrderId();
		$refund_desc=$order['api_data']['refund_desc']?$order['api_data']['refund_desc']:'正常退款';
		$arr=[
		  'out_trade_no'=>$order['out_trade_no'],
		  'refund_amount'=>$total,
		  'out_request_no'=>$refund_no,
		  'refund_reason'=>$refund_desc,
		];
		$request=new \AlipayTradeRefundRequest();
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$order['mid']);
		rwlog('aliOrderRefund',$result);
	    if($result['status']) {
			$result=$result['data'];
			if ($result['code'] == '10000') {
				//增加退款记录
				$array=array(
					'device_info'=>$order['api_data']['appid'],
					'trade_type'=>'refund',
					'mid' => $order['mid'],
					'store_id' => $order['store_id'],
					'agent_id' =>$order['agent_id'],
					'createtime'=>time(),
					'mch_rate' => $order['mch_rate'],
					'mch_id' => $order['mch_id'],
					'service' => 'ali_refund',
					'out_trade_no'=>$refund_no,
					'body'=>'退款',
					'total_fee'=>$result['send_back_fee'], //存数据库按照分进行统计
					'mch_create_ip'=>Get_Clienti_Ips(),
					'type'=>'D1',
					'alleys'=>'Aliisv',
					'domain_auth'=>$order['domain_auth'],
					'is_raw'=>1,
					'time_end'=>strtotime($result['gmt_refund_pay']),
					'transaction_id'=>$result['trade_no'],
					'out_transaction_id'=>$order['out_trade_no'],
					'status'=>1,
					'goods_tag'=>$refund_desc
				);
				M('mchOrders')->add($array);
				//$this->aliOrderResult($refund_no);
				return ['status' => true, 'msg' => '退款申请提交成功', 'data' => $result];
			}
			return ['status' => false, 'msg' => $result['sub_msg']];
		}
		return ['status' => false, 'msg' => $result['msg']];
	}

	/**
	 * 微信退款
	 * @param array $order
	 * @return array
	 */
	protected function wxOrderRefund($order=[]){
		//查询当前订单已退金额
		$refund_total_sum=M('MchOrders')->where(['status'=>1,'out_transaction_id'=>$order['out_trade_no']])->sum('total_fee');
		//查询还可退
		$end_total=bcsub($order['total_fee'],$refund_total_sum,2);
		if($end_total<0.01){
			return ['status'=>false,'msg'=>'无可退款金额'];
		}
		$api_data_total=$order['api_data']['total'];
		$total=$api_data_total?$api_data_total:($end_total*100);
		$refund_no='T'.$this->getOrderId();
		$refund_desc=$order['api_data']['refund_desc']?$order['api_data']['refund_desc']:'正常退款';
		$input=new \WxPayRefund();
		$input->SetOut_trade_no($order['out_trade_no']);
		$input->SetSubMch_id($order['mch_id']);
		$input->SetOut_refund_no($refund_no);
		$input->SetTotal_fee($order['total_fee']*100);
		$input->SetRefund_fee($total);
		$input->SetRefund_desc($refund_desc);
		$input->Set('notify_url','http://'.$_SERVER['HTTP_HOST'].'/Pays/FaceAuto/wxRefundNotify');
		$result = \WxPayApi::refund($input);
		if($result['return_code']=='SUCCESS'){
			if($result['result_code']=='SUCCESS'){
				//增加退款记录
				$array=array(
					'device_info'=>$order['api_data']['appid'],
					'trade_type'=>'refund',
					'mid' => $order['mid'],
					'store_id' => $order['store_id'],
					'agent_id' =>$order['agent_id'],
					'createtime'=>time(),
					'mch_rate' => $order['mch_rate'],
					'mch_id' => $order['mch_id'],
					'service' => 'wx_refund',
					'out_trade_no'=>$refund_no,
					'body'=>'退款',
					'total_fee'=>$total/100, //存数据库按照分进行统计
					'mch_create_ip'=>Get_Clienti_Ips(),
					'type'=>'D1',
					'alleys'=>'WxPay',
					'domain_auth'=>$order['domain_auth'],
					'is_raw'=>1,
					'goods_tag'=>$refund_desc
				);
				M('mchOrders')->add($array);
				return ['status'=>true,'msg'=>'退款申请提交成功','data'=>$result];
			}
			return ['status'=>false,'msg'=>$result['err_code_des']];
		}
		return ['status'=>false,'msg'=>$result['return_msg']];
	}


	/**
	 * 微信退款异步
	 */
	public function wxRefundNotify(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notifyData=$this->xmlToArray($xml);
		$data=$this->wxRefundInfo($notifyData['req_info']);
		$data=$this->xmlToArray($data);
		if(is_array($data)){
           $res= $this->wxRefundQuery($data['out_refund_no']);
           if($res['status']==1){
			   die('SUCCESS');
		   }else{
			   die('error');
		   }
		}
		die('error');
	}


	/**
	 * 微信退款查询
	 * @param $oid
	 * @return array
	 */
	public function wxRefundQuery($oid){
		$order=M('MchOrders')->where(['out_trade_no'=>$oid])->find();
		if($order) {
			$map['cid']=$order['mid'];
			$map['alleys_type']=$order['alleys'];
			$SellerAlleys=M('MchSellerAlleys')->where($map)->field('mch_id,mch_key,mch_appid')->find();
			$input = new \WxPayRefundQuery();
			$input->SetSubMch_id($SellerAlleys['mch_id']);
			$input->SetOut_refund_no($order['out_trade_no']);
			$res = \WxPayApi::refundQuery($input);
			if($res['return_code']=='SUCCESS'){
				if($res['result_code']=='SUCCESS'){
					//查询成功
					switch ($res['refund_status_0']){
						case 'SUCCESS': //退款成功
							$status=1;
							$msg='退款成功';
							break;
						case 'REFUNDCLOSE'://退款关闭
							$status=0;
							$msg='退款关闭';
							break;
						case 'PROCESSING'://退款处理中
							$status=0;
							$msg='退款处理中';
							break;
						case 'CHANGE'://退款异常
							$status=0;
							$msg='退款异常';
							break;
						default:
							$status=0;
							$msg=$res['refund_status_0'];
							break;
					}
					//更新数据库结果
					$save=[
						'status'=>$status,
						'time_end'=>$res['refund_success_time_0']?strtotime($res['refund_success_time_0']):time(),
						'total'=>$res['refund_fee_0']/100,//分单位转换为元
						'transaction_id'=>$res['transaction_id'],
						'out_transaction_id'=>$res['out_trade_no'],
					];
					M('MchOrders')->where(['out_trade_no'=>$res['out_refund_no_0']])->save($save);
					$return=['status'=>1,'res_status'=>$status,'result'=>$res,'msg'=>$msg];
				}else{
					$return=['status'=>0,'msg'=>$res['err_code_des']];
				}
			}else{
				rwlog('wxRefundQuery',$res);
				$return=['status'=>0,'msg'=>$res['return_msg']];
			}

		}else{
			$return=['status'=>false,'msg'=>'订单信息获取失败'];
		}
		return $return;

	}

	/**
	 * 微信退款异步信息解密
	 * @param $data
	 * @return string
	 */
	public function wxRefundInfo($data){
		$key=GetPayConfigs('xun_wxpay_key');
		$decrypt = base64_decode($data, true);
		return openssl_decrypt($decrypt , 'aes-256-ecb', md5($key), OPENSSL_RAW_DATA);
	}


	/**
	 * 统一查询
	 * @param $data
	 * @return array
	 */
	public function orderQuery($data){
		rwlog('orderQuery',$data);
		$order=M('MchOrders')->where(['out_trade_no|transaction_id'=>$data['out_trade_no']])->find();
		//dump($data);
		if($order){
			if($order['alleys']=='Aliisv'){
				$res=$this->aliOrderResult($data['out_trade_no']);
				$res['result']=$res['result']['data'];
				switch ($res['result']['trade_status']){
					case 'WAIT_BUYER_PAY':
						$status=3;
						$msg='需要用户输入支付密码';
						break;
					case 'TRADE_SUCCESS':
						$status=1;
						$msg='支付成功';
						break;
					case 'TRADE_CLOSED':
						$status=2;
						$msg='未付款交易超时关闭，或支付完成后全额退款';
						break;
					default:
						$status=0;
						$msg='交易结束';
						break;
				}
				$d=[
					'out_trade_no'=>$data['out_trade_no'],
					'status'=>$status,
					'msg'=>$msg,
					'payType'=>'ali',
					'total'=>$order['total_fee']*100, //分输出
				];
				if($status==1){
					$d['transaction_id']=$res['result']['trade_no'];
					$d['time_end']=date('Y-m-d H:i:s',strtotime($res['result']['send_pay_date']));
				}
				return ['code'=>100,'msg'=>'查询成功','data'=>$d];
			}elseif ($order['alleys']=='WxPay'){
				$res=$this->wxOrderResult($data['out_trade_no']);
				switch ($res['result']['trade_state']){
					case 'USERPAYING':
						$status=3;
						$msg='需要用户输入支付密码';
						break;
					case 'SUCCESS':
						$status=1;
						$msg='支付成功';
						break;
					case 'REFUND':
						$status=2;
						$msg='转入退款';
						break;
					default:
						$status=0;
						$msg='支付失败或订单已撤销';
						break;
				}
				$d=[
					'out_trade_no'=>$data['out_trade_no'],
					'status'=>$status,
					'msg'=>$msg,
					'payType'=>'wx',
					'total'=>$order['total_fee']*100, //分输出
				];
				if($status==1){
					$d['transaction_id']=$res['result']['transaction_id'];
					$d['time_end']=date('Y-m-d H:i:s',strtotime($res['result']['time_end']));
				}
				return ['code'=>100,'msg'=>'查询成功','data'=>$d];
			}else{
				$data['alley'] = self::mchAlley($order['alleys'],$data['store_data']['sid']);
				//兼容三方通道
				$module = A('Pays/P' . $order['alleys']);
				$modules = method_exists($module, 'orderQueryFaceApi');
				if ($modules) {
					return R('Pays/P' . $order['alleys'] . '/'.'orderQueryFaceApi',[$data]);
				} else {
					//当前通道为支持此方法 返回错误提示
					return ['code'=>400,'msg'=>"Fail(".strtoupper($order['alleys'].'query').")"];
				}

			}
			//return ['code'=>400,'msg'=>'非设备交易单号,请登录管理平台查询'];
		}
		return ['code'=>400,'msg'=>'订单号无效'];
	}


	/**
	 * 微信支付
	 * @param $data
	 * @param $type
	 * @return array
	 */
	public function wxPay($data,$type){
		$data['alley_data'] = self::mchAlley('WxPay',$data['store_data']['sid']);
		$oid=$this->getOrderId("{$data['store_data']['id']}");
		$body=$data['remark']?$data['remark']:$data['store_data']['name'];
		$input=new \WxPayMicroPay();
		$input->SetSubMch_id($data['alley_data']['mch_id']);
		$input->SetBody($body);
		$input->SetOut_trade_no($oid);
		$input->SetTotal_fee($data['total']);
		$input->SetAuth_code($data['authCode']);
		$result = \WxPayApi::micropay($input);
		if($result['return_code']=='SUCCESS'){
			//保存数据
			$array=array(
				'goods_tag' => $data['remark']?$data['remark']:'',
				'device_info'=>$data['appid'],
				'trade_type'=>'FACEPAY',
				'mid' => $data['store_data']['sid'],
				'store_id' => $data['store_data']['id'],
				'agent_id' => GetMchAid($data['store_data']['sid']),
				'createtime'=>time(),
				'mch_rate' => $data['alley_data']['rate'],
				'mch_id' => $data['alley_data']['mch_id'],
				'service' => $type=='face'?'wx_face':'wx_scan',
				'out_trade_no'=>$oid,
				'body'=>$body,
				'total_fee'=>$data['total']/100, //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'type'=>'D1',
				'alleys'=>'WxPay',
				'domain_auth'=>$data['alley_data']['domain_auth'],
				'is_raw'=>1,
			);
			M('mchOrders')->add($array);
			if($result['result_code']=='SUCCESS'){
				//更新数据库结果
				$save=[
					'status'=>1,
					'time_end'=>strtotime($result['time_end']),
					'total'=>$result['total_fee']/100,//分单位转换为元
					'transaction_id'=>$result['transaction_id'],
				];
				M('MchOrders')->where(['out_trade_no'=>$oid])->save($save);
				//发送模板消息
				R('Pays/Notify/sendTemplateMessage', array($oid)); //发送收款成功模板消息
				return ['code'=>100,'msg'=>'支付成功','data'=>[
					'out_trade_no'=>$oid,
					'transaction_id'=>$result['transaction_id'],
					'status'=>1,
					'payType'=>'wx',
                    'payTime'=>date('Y-m-d H:i:s'),
					'total'=>$result['total_fee'], //分输出
				]];
			}elseif($result['err_code']=='USERPAYING'||$result['err_code']=='SYSTEMERRO'){
				if($data['is_loop']){
					$loop=$this->loopOrderResult($oid,'wx');
					if($loop){
						return ['code' => 100, 'msg' => $result['err_code_des'], 'data' => [
							'out_trade_no' => $oid,
							'status' => $loop['res_status'],
							'payType' => 'wx',
							'payTime' => date('Y-m-d H:i:s'),
							'total' => $data['total'], //转换为分输出
						]];
					}else{
						//超时进行关单处理
						$input=new \WxPayReverse();
						$input->SetOut_trade_no($oid);
						$input->SetSubMch_id($data['alley_data']['mch_id']);
						$result = \WxPayApi::reverse($input);
						if($result['return_code']=='SUCCESS'){
							if($result['result_code']=='SUCCESS'){
								$this->wxOrderResult($oid);
								return ['code'=>400,'msg'=>'支付超时,订单已撤销'];
							}
							return ['code'=>400,'msg'=>$result['err_code_des']];
						}
						return ['code'=>400,'msg'=>$result['return_msg']];

					}
				}else {
					return ['code' => 100, 'msg' => $result['err_code_des'], 'data' => [
						'out_trade_no' => $oid,
						'status' => 3,
						'payType' => 'wx',
						'payTime' => date('Y-m-d H:i:s'),
						'total' => $data['total'], //转换为分输出
					]];
				}
			}
			return ['code'=>400,'msg'=>$result['err_code_des'],'data'=>$result];
		}
		return ['code'=>400,'msg'=>$result['return_msg']];
	}


	/**
	 * 支付宝条码-刷脸收银台
	 * @param $data
	 * @param $type
	 * @return array
	 */
	public function aliPay($data,$type){
		$data['alley_data'] = self::mchAlley('Aliisv',$data['store_data']['sid']);
		$scene=($type=='face')?'security_code':'bar_code';
		$body=$data['remark']?$data['remark']:$data['store_data']['name'];
		$arr=[
			'out_trade_no'=>$this->getOrderId("{$data['store_data']['id']}"),
			'scene'=>$scene,
			'auth_code'=>$data['authCode'],
			'total_amount'=>$data['total']/100,//订单金额
			'subject'=>$body,//订单标题
			'body'=>'移动支付',
			'product_code'=>'FACE_TO_FACE_PAYMENT',
			'extend_params'=>[
				'sys_service_provider_id'=>$this->config['pid']
			],
		];
		$request=new \AlipayTradePayRequest();
		$request->setNotifyUrl($this->config['notifyUrl']);
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result=$this->setAop($request,true,$data['store_data']['sid']);
		if($result['status']){
			$array=array(
				'goods_tag' => $data['remark']?$data['remark']:'',
				'device_info'=>$data['appid'],
				'trade_type'=>'FACEPAY',
				'mid' => $data['store_data']['sid'],
				'store_id' => $data['store_data']['id'],
				'agent_id' => GetMchAid($data['store_data']['sid']),
				'createtime'=>time(),
				'mch_rate' => $data['alley_data']['rate'],
				'mch_id' => $data['alley_data']['mch_id'],
				'service' => $type=='face'?'ali_face':'ali_scan',
				'out_trade_no'=>$arr['out_trade_no'],
				'body'=>$arr['body'],
				'total_fee'=>$data['total']/100, //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'type'=>'D1',
				'alleys'=>'Aliisv',
				'domain_auth'=>$data['alley_data']['domain_auth'],
				'is_raw'=>1,
			);
			if($result['data']['code']=='10000') {
				$status=1;
				$array['status']=1;
				$array['sub_openid']=$result['data']['buyer_user_id'];
				$array['time_end']=strtotime($result['data']['gmt_payment']);
				$array['transaction_id']=$result['data']['trade_no'];
				$array['buyer_logon_id']=$result['data']['buyer_logon_id'];
				$msg='支付成功 金额:'.$result['data']['total_amount'].'元';
				$rel=M('mch_orders')->add($array);
				if($rel){
					//发送模板消息
					R('Pays/Notify/sendTemplateMessage', array($arr['out_trade_no'])); //发送收款成功模板消息
					return ['code'=>100,'msg'=>$msg,'data'=>[
						'out_trade_no'=>$arr['out_trade_no'],
						'transaction_id'=>$result['data']['trade_no'],
						'status'=>$status?$status:0,
						'payType'=>'ali',
                        'payTime'=>date('Y-m-d H:i:s'),
						'total'=>$result['data']['total_amount']*100, //转换为分输出
					]];
				}
				return ['code'=>400,'msg'=>'订单创建失败'];
			}elseif ($result['data']['code']=='10003'){
				$status=3;
				$msg='等待用户输入密码';
				M('mch_orders')->add($array);
				if($data['is_loop']){
					$loop=$this->loopOrderResult($arr['out_trade_no'],'ali');
					if($loop){
						return ['code'=>100,'msg'=>$msg,'data'=>[
							'out_trade_no'=>$arr['out_trade_no'],
							'transaction_id'=>$result['data']['trade_no'],
							'status'=>$loop['res_status'],
							'payType'=>'ali',
							'payTime'=>date('Y-m-d H:i:s'),
							'total'=>$result['data']['total_amount']*100, //转换为分输出
						]];
					}else{
						//超时 进行关单处理
						$request = new \AlipayTradeCancelRequest();
						$request->setBizContent(json_encode(['out_trade_no'=>$arr['out_trade_no']],JSON_UNESCAPED_UNICODE));
						$result=$this->setAop($request,true,$data['store_data']['sid']);
						if($result['status']){
							$result=$result['data'];
							if ($result['code'] == '10000') {
								$this->aliOrderResult($arr['out_trade_no']);
								return ['code'=>400,'msg'=>'支付超时,订单已撤销'];
							}
							return ['code'=>400,'msg'=>$result['sub_msg']];
						}
						return ['code'=>400,'msg'=>$result['msg']];
					}
				}else{
					return ['code'=>100,'msg'=>$msg,'data'=>[
						'out_trade_no'=>$arr['out_trade_no'],
						'transaction_id'=>$result['data']['trade_no'],
						'status'=>$status?$status:0,
						'payType'=>'ali',
						'payTime'=>date('Y-m-d H:i:s'),
						'total'=>$result['data']['total_amount']*100, //转换为分输出
					]];
				}
			}else{
				return ['code'=>400,'msg'=>$result['data']['sub_msg']];
			}
		}
		return ['code'=>400,'msg'=>$result['msg']];
	}



	public function loopOrderResult($out_trade_no,$type='wx'){
		$fit=$type.'OrderResult';
		//轮询确认支付是否成功 查询10次  间隔5秒
		for ($i=0;$i<10;$i++){
			try{
				sleep(5);
			}catch (\Exception $e){
				rwlog('loopQueryResult',$e->getMessage());
				exit();
			}
			$queryResult = $this->$fit($out_trade_no);
			rwlog('loopQueryResultLog',['number'=>$fit.'-'.$i.'-'.$out_trade_no,'res'=>$queryResult]);
			if($queryResult['status']){
				$res=$queryResult['result'];
				  //微信查询
				if($type=='wx'){
					if($res['trade_state']!='USERPAYING'){
						return $queryResult;
					}elseif($res['trade_state']=='PAYERROR'){
						//支付出错 调用撤销
						return false;
					}
				}else{
					if("TRADE_FINISHED"==$res['data']['trade_status']||
						"TRADE_SUCCESS"==$res['data']['trade_status']||
						"TRADE_CLOSED"==$res['data']['trade_status']){
						return $queryResult;
					}
				}
			}
		}
		//循环后没结果返回给前端处理
		return false;
	}






	/**
	 * 订单查询
	 * @param $oid
	 * @return array
	 */
	public function wxOrderResult($oid){
		$order=M('MchOrders')->where(['out_trade_no'=>$oid])->find();
		if($order){
			$map['cid']=$order['mid'];
			$map['alleys_type']=$order['alleys'];
			$SellerAlleys=M('MchSellerAlleys')->where($map)->field('mch_id,mch_key,mch_appid')->find();
			//利用交易接口查询
			$input = new \WxPayOrderQuery();
			$input->SetSubMch_id($SellerAlleys['mch_id']);
			$input->SetOut_trade_no($order['out_trade_no']);
			$res = \WxPayApi::orderQuery($input);
			if($res['return_code']=='SUCCESS'){
				if($res['result_code']=='SUCCESS'){
					//查询成功
					switch ($res['trade_state']){
						case 'SUCCESS':
							$status=1;
							break;
						case 'REFUND':
							$status=2;
							break;
						default:
							$status=0;
							break;
					}
					//更新数据库结果
					$save=[
						'sub_openid'=>$res['data']['openid'],
						'status'=>$status,
						'time_end'=>strtotime($res['time_end']),
						'total'=>$res['total_fee']/100,//分单位转换为元
						'transaction_id'=>$res['transaction_id'],
					];
					M('MchOrders')->where(['out_trade_no'=>$res['out_trade_no']])->save($save);
					if($status==1) {
						R('Pays/Notify/sendTemplateMessage', array($res['out_trade_no'])); //发送收款成功模板消息
					}
					$return=['status'=>1,'res_status'=>$status,'result'=>$res,'msg'=>'查询成功'];
				}else{
					$return=['status'=>0,'msg'=>$res['err_code_des']];
				}
			}else{
				$return=['status'=>0,'msg'=>$res['return_msg']];
			}
		}else{
			$return=['status'=>false,'msg'=>'订单信息获取失败'];
		}
		return $return;
	}


	/**
	 * 查询订单
	 * @param $oid
	 * @return array
	 */
	public function aliOrderResult($oid){
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
					$return = ['status' => false, 'msg' => $result['data']['sub_msg']?$result['data']['sub_msg']:$result['data']['msg']];
				}
			}else{
				$return = ['status' => false, 'msg' => $result['msg']];
			}
		}else{
			$return = ['status' => false, 'msg' => '未知订单'];
		}
		return $return;
	}


	/**
	 * 支付宝异步处理
	 */
	public function aliNotify(){
		$notifyData = file_get_contents("php://input");
		parse_str($notifyData, $notifyData);
		$oid=$notifyData['out_trade_no'];
		$ret=$this->aliOrderResult($oid);
		if($ret['status']){
			die('SUCCESS');
		}
		die('error');
	}


	/**
	 * 微信异步处理
	 */
	public function wxNotify(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notifyData=$this->xmlToArray($xml);
		$oid=$notifyData['out_trade_no'];
		$ret=$this->wxOrderResult($oid);
		if($ret['status']){
			die('SUCCESS');
		}
		die('error');
	}


	/**
	 * 付款条形码支付类型
	 * @param $data
	 * @return string
	 */
	public function payType($data){
		$str=substr($data,0,2);
		$wxType=[10,11,12,13,14,15];//微信类型10-15
		$aliType=[25,26,27,28,29,30];//支付宝付款码25-30
		if(in_array($str,$wxType)){//微信条形码
			$type='wxPay';
		}elseif(in_array($str,$aliType)){ //支付宝条形码
			$type='aliPay_code';
		}else{ //20开头为支付宝刷脸付接口
			$type='aliPay_face';
		}
		return $type;
	}


	/**
	 * 通信
	 * @param $request
	 * @return array
	 */
	protected function setAop($request,$token=false,$mid=''){
		try{
			if($token){
				$app_auth_token=aliIsvToken($mid);
				$result = $this->aop->execute($request,null,$app_auth_token);
			}else{
				$result = $this->aop->execute($request);
			}
			rwlog('aliSetAop_'.date('Ymd'),[
				'time'=>date('YmdHis'),
				'token'=>$token,
				'mid'=>$mid,
				'app_auth_token'=>$app_auth_token,
			]);
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
	protected function getOrderId($prefix = '')
	{
		return date('ymd').(date('y') + date('d')).sprintf('%03d', rand(0, 999)).str_pad((time() - strtotime(date('Y-m-d'))), 3, 0, STR_PAD_LEFT) . substr(microtime(), 2, 5);
	}


	/**
	 * 商户通道信息
	 * @param $type
	 * @return mixed
	 */
	public function mchAlley($type,$id){
		$db = M('mchSellerAlleys');
		$res=$db->where(['alleys_type'=>$type,'cid'=>$id])->field('id,cid,rate,mch_id,mch_key,mch_appid,agent_id,domain_auth,api_rel')->find();
		$res['api_rel']=unserialize($res['api_rel']);
		return $res['mch_id']?$res:false;
	}

	/**
	 * 将xml转为array
	 * @param string $xml
	 * return array
	 */
	public function xmlToArray($xml)
	{
		if (!$xml) {
			return false;
		}
		//将XML转为array
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $data;
	}



}