<?php
namespace Pays\Controller;
use Think\Controller;
class FaceWxController extends Controller
{
	protected  $post;

	public function _initialize(){
		Vendor('xun_wxpay.WxPayApi');
		Vendor('xun_wxpay.JsApiPay');
		Vendor('xun_wxpay.WxPayNotify');
		Vendor("xun_wxpay.NativePay");
		$this->post=I('post.');
	}



	/**
	 * 提交支付
	 * @param $data
	 * @return array
	 */
	public function facePay($data){
		if(!array_key_exists('total',$data)||empty($data['total'])){
			return ['code'=>103,'msg'=>'total不可为空'];
		}
		if(!array_key_exists('openId',$data)||empty($data['openId'])){
			return ['code'=>103,'msg'=>'openId不可为空'];
		}
		if(!array_key_exists('faceCode',$data)||empty($data['faceCode'])){
			return ['code'=>103,'msg'=>'faceCode不可为空'];
		}
		if(!array_key_exists('out_trade_no',$data)||empty($data['out_trade_no'])){
			return ['code'=>103,'msg'=>'out_trade_no不可为空'];
		}
		$input=new \FacePay();
		$input->Set('sub_mch_id',$data['alley_data']['mch_id']);
		$input->Set('body',$data['body']?$data['body']:$data['store_data']['name']);
		$input->Set('out_trade_no',$data['out_trade_no']);
		$input->Set('total_fee',$data['total']);
		$input->Set('spbill_create_ip',Get_Clienti_Ips());
		$input->Set('openid',$data['openId']);
		$input->Set('face_code',$data['faceCode']);
		$res=\WxPayApi::facePay($input);
		rwlog('facePay',$res);
		if($res['return_code']=='SUCCESS'){
			//增加交易数据记录
			$array = array(
				'device_info'=>$data['appid'],
				'trade_type'=>'FACEPAY',
				'mid' => $data['store_data']['sid'],
				'store_id' => $data['store_data']['id'],
				'agent_id' => GetMchAid($data['store_data']['sid']),
				'new' => serialize($input),
				'data' => serialize($data),
				'rel' => serialize($res),
				'createtime' => time(),
				'mch_rate' => $data['alley_data']['rate'],
				'mch_id' => $data['alley_data']['mch_id'],
				'service' => 'wx_face',
				'out_trade_no'=>$data['out_trade_no'],
				'transaction_id'=>$res['transaction_id'],
				'body' =>$data['body']?$data['body']:$data['store_data']['name'],
				'total_fee' =>$data['total']/100, //数据库按照单位元 存储
				'mch_create_ip' => Get_Clienti_Ips(),
				'type' => 'D1',
				'alleys' => 'WxPay',
				'domain_auth' => $data['alley_data']['domain_auth'],
				'is_raw' => 1,
			);
			M('mchOrders')->add($array);
			if($res['result_code']=='SUCCESS'){
				$result=$this->orderResult(['out_trade_no'=>$res['out_trade_no']]);
				$test=[
					'code'=>100,
					'msg'=>'成功',
					'data'=>[
						'appid'=>$result['data']['appid'],
						'mch_id'=>$result['data']['mch_id'],
						'sub_mch_id'=>$result['data']['sub_mch_id'],
						'total_fee'=>$result['data']['total_fee'],
						'transaction_id'=>$result['data']['transaction_id'],
						'out_trade_no'=>$result['data']['out_trade_no'],
						'time_end'=>$result['data']['time_end'],
						'status'=>1,
					]
		     	];
				rwlog('orderFaceResult',[$result,$test]);
				//R('Pays/Notify/sendTemplateMessage', array($res['out_trade_no'])); //发送收款成功模板消息
				//成功后的数据
				return [
					'code'=>100,
					'msg'=>'成功',
					'data'=>[
						'appid'=>$res['appid'],
						'mch_id'=>$res['mch_id'],
						'sub_mch_id'=>$res['sub_mch_id'],
						'total_fee'=>$res['total_fee'],
						'transaction_id'=>$res['transaction_id'],
						'out_trade_no'=>$res['out_trade_no'],
						'time_end'=>$res['time_end'],
						'status'=>1,
					]
				];
			}elseif ($res['err_code']=='SYSTEMERROR'){
				//为了接口正常 延迟5秒
				sleep(5);
				//未知支付状态 利用查询接口查询支付结果
				$result=$this->orderResult(['out_trade_no'=>$data['out_trade_no']]);
				if($result['code']=='100'){
                    if($result['data']['status']==1&&$result['data']['api_status']==1){
						return [
							'code'=>100,
							'msg'=>'成功',
							'data'=>[
								'appid'=>$result['data']['appid'],
								'mch_id'=>$result['data']['mch_id'],
								'sub_mch_id'=>$result['data']['sub_mch_id'],
								'total_fee'=>$result['data']['total_fee'],
								'transaction_id'=>$result['data']['transaction_id'],
								'out_trade_no'=>$result['data']['out_trade_no'],
								'time_end'=>$result['data']['time_end'],
								'status'=>1,
							]
						];
					}
					return ['code'=>400,'msg'=>$result['data']['msg']];
				}
				return ['code'=>400,'msg'=>"{$result['msg']}"];

			}
            return ['code'=>400,'msg'=>"{$res['err_code_des']}"];
		}
		return ['code'=>400,'msg'=>"{$res['return_msg']}"];
	}

	/**
	 * 获取authInfo
	 * @param $data
	 * @return array
	 */
	public function faceAuthInfo($data){
		rwlog('faceAuthInfo',$data);
		if(!array_key_exists('rawdata',$data)||empty($data['rawdata'])){
            return ['code'=>103,'msg'=>'rawdata不可为空'];
		}
		$input=new \GetAuthInfo();
		$input->SetStore_id($data['store_data']['id']);
		$input->SetStore_name($data['store_data']['name']);
		$input->SetDevice_id('D'.$data['appid']);
		$input->SetRawdata($data['rawdata']);
		$input->SetSubMch_id($data['alley_data']['mch_id']);
		$res=\WxPayApi::authInfo($input);
		if($res['return_code']=='SUCCESS'){
			return [
				'code'=>100,
				'msg'=>'成功',
				'data'=>[
					'appid'=>$res['appid'],
					'mch_id'=>$res['mch_id'],
					'sub_mch_id'=>$res['sub_mch_id'],
					'store_id'=>$data['store_data']['id'],
					'store_name'=>$data['store_data']['name'],
					'device_id'=>'D'.$data['appid'],
					'auth_info'=>$res['authinfo'],
					'expires_in'=>$res['expires_in'],
					'out_trade_no'=>$this->getOrderId("{$data['store_data']['id']}")
				]
			];
		}else{
			return ['code'=>400,'msg'=>"{$res['return_msg']}({$res['return_code']})"];
		}
	}


	public function orderResult($data){
		$order=M('mchOrders')->where(['out_trade_no'=>$data['out_trade_no']])->find();
		if($order) {
			$input = new \FaceOrderQuery();
			$input->Set('out_trade_no', $order['out_trade_no']);
			$input->Set('sub_mch_id', $order['mch_id']);
			$res = \WxPayApi::faceOrderQuery($input);
			if($res['return_code']=='SUCCESS'){
				if($res['result_code']=='SUCCESS'){
					//查询成功后的数据
					switch ($res['trade_state']){
						case 'SUCCESS':
							$status=1;
							$api_status=1;
							$msg='支付成功';
							break;
						case 'REFUND':
							$status=2;
							$api_status=2;
							$msg='已退款';
							break;
						case 'ORDER_PAYING':
							$status=0;
							$api_status=3;
							$msg='支付中';
							break;
						case 'PAYERROR':
							$status=0;
							$api_status=0;
							$msg='支付失败';
							break;
						case 'REVOKED':
							$status=0;
							$api_status=4;
							$msg='已撤销';
							break;
						default:
							$status=0;
							$api_status=$res['trade_state'];
							$msg='已撤销';
							break;
					}
					$save=[
						'sub_openid'=>$res['openid'],
						'status'=>$status,
						'time_end'=>strtotime($res['time_end']),
						'total'=>$res['total_fee']/100,//分单位转换为元
						'transaction_id'=>$res['transaction_id'],
					];
					M('MchOrders')->where(['out_trade_no'=>$res['out_trade_no']])->save($save);
					if($status==1) {
						R('Pays/Notify/sendTemplateMessage', array($res['out_trade_no'])); //发送收款成功模板消息
					}
					return [
						'code'=>100,
						'msg'=>'查询成功',
						'data'=>[
							'status'=>$status,
							'api_status'=>$api_status,
							'msg'=>$msg,
							'appid'=>$res['appid'],
							'mch_id'=>$res['mch_id'],
							'sub_mch_id'=>$res['sub_mch_id'],
							'total_fee'=>$res['total_fee'],
							'transaction_id'=>$res['transaction_id'],
							'out_trade_no'=>$res['out_trade_no'],
							'time_end'=>$res['time_end']
						]
					];
				}
				return ['code'=>400,'msg'=>"{$res['err_code_des']}"];
			}
			return ['code'=>400,'msg'=>"{$res['return_msg']}"];
		}else{
			return ['code'=>400,'msg'=>'订单不存在'];
		}

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