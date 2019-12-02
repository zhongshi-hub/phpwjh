<?php
/**
 * 会员卡信息接口
 * auth: chencunlong
 * email:chencunlong@126.com
 */
namespace Pays\Controller;
use Think\Controller;
use think\Log;

class FaceMemberController extends Controller
{
	protected $config;
	protected $db;

	public function _initialize()
	{
        $this->db=[
              'user'=>M('mchMemberUser'),
			  'order'=>M('mchMemberOrder'),
		];
	}


	/**
	 * 获取会员卡状态是否开启
	 * @param $data
	 * @return array
	 */
	public function status($data){
		$temp=memberTemp($data['store_data']['sid']);
		$status=($temp&&$temp['status']==1)?1:0;
        return ['code'=>100,'msg'=>'success','data'=>[
        	'mid'=>$data['store_data']['sid'],
			'store_id'=>$data['store_data']['id'],
			'status'=>$status]
		];
	}

	/**
	 * 充值活动规则
	 * @param $data
	 * @return array
	 */
	public function getRechargeTotal($data){
		$czData=R('Common/MemberActivity/getDataArr',[$data['store_data']['sid'],$data['store_data']['id'], 'cz']);
		switch (count($czData['cz_money'])){
			case 1:
				$total=[
					[
						'total'=>30
					],
					[
						'total'=>50
					],
					[
						'total'=>$czData['cz_money'][0],
						'money'=>$czData['cz_total'][0]
					]
				];
				break;
			case 2:
				$total=[
					[
						'total'=>30
					],
					[
						'total'=>$czData['cz_money'][0],
						'money'=>$czData['cz_total'][0]
					],
					[
						'total'=>$czData['cz_money'][1],
						'money'=>$czData['cz_total'][1]
					]
				];
				break;
			case 3:
				$total=[
					[
						'total'=>$czData['cz_money'][0],
						'money'=>$czData['cz_total'][0]
					],
					[
						'total'=>$czData['cz_money'][1],
						'money'=>$czData['cz_total'][1]
					],
					[
						'total'=>$czData['cz_money'][2],
						'money'=>$czData['cz_total'][2]
					]
				];
				break;
			default:
				$total=[
					[
						'total'=>30
					],
					[
						'total'=>50
					],
					[
						'total'=>100
					]
				];
				break;
		}
		return ['code'=>100,'msg'=>'获取充值规则成功','data'=>['time'=>date('Y-m-d H:i:s'),'count'=>count($czData['cz_money']),'list'=>$total]];
	}


	/**
	 * 会员充值
	 * @param $data
	 * @return array
	 */
	public function recharge($data){
		if(empty($data['phone'])&&empty($data['user_id'])){
			return ['code'=>400,'msg'=>'手机号和用户标识不能同时为空'];
		}
		if(!in_array($data['type'],['wx','ali'])){
			return ['code'=>400,'msg'=>'终端类型无效'];
		}
		if(empty($data['total'])){
			return ['code'=>400,'msg'=>'支付金额不可为空'];
		}
		if(empty($data['authCode'])){
			return ['code'=>400,'msg'=>'支付授权码不可为空'];
		}
		//会员是否存在
		$user_id=$data['user_id']?$data['user_id']:$data['phone'];
		$where=['mid'=>$data['store_data']['sid'],'wx_id|ali_id|phone'=>$user_id];
		$count=M('mchMemberUser')->where($where)->find();
		if(empty($count)){
			return ['code'=>400,'msg'=>'会员信息获取失败'];
		}
		//充值操作
		$data['member_user']=$count;
		$data['remark']=tel_replace($count['phone']).'会员充值';
		try {
			//获取code类型
			$types=R('Pays/FaceAuto/payType',[$data['authCode']]);
			$type=explode('_',$types)[0];
			$data['authCodeType']=($type=='wxPay')?'wx':'ali';
			$alleys = M('MchSeller')->where(array('id' => $data['store_data']['sid']))->getField($data['authCodeType'].'_alleys');
			if(empty($alleys)||$alleys=='Aliisv'||$alleys=='WxPay'){
				$payRes=R('Pays/FaceAuto/'.$type,[$data,$types[1]]);
			}else{
				$payRes=R('Pays/FaceAuto/'.$type,[$data,$types[1]]);
			}
			if($payRes['code']=='100'){
				$cz_total=$this->getTotalMoney([
					'mid'=>$data['store_data']['sid'],
					'store_id'=>$data['store_data']['id']
				],$data['total']/100); //充值送
				//存储充值数据
				$this->czOrderSet($data,$payRes['data'],$cz_total?$cz_total:false);
				//返回结果
				return ['code'=>100,'msg'=>$payRes['msg'],'data'=>[
					'total'=>$data['total'],//分
					'money'=>$cz_total*100,//分
					'phone'=>$count['phone'],
					'time_end'=>date('Y-m-d H:i:s'),
					'status'=>$payRes['data']['status'],
					'out_trade_no'=>$payRes['data']['out_trade_no']
				]];
			};
			return ['code'=>400,'msg'=>$payRes['msg']];
		}catch (\Exception $e){
			rwlog('faceMember_recharge',$e->getMessage());
			return ['code'=>400,'msg'=>'Fail recharge'];
		}
	}

	/**
	 * 会员充值结果查询
	 * @param $data
	 * @return array
	 */
	public function rechargeOrderQuery($data){
		if(empty($data['out_trade_no'])){
			return ['code'=>400,'msg'=>'订单号不能为空'];
		}
		try {
		    $payRes=R('Pays/FaceAuto/orderQuery',[$data]);
		    if($payRes['code']=='100'){
		    	if($payRes['data']['status']==1){
					M('mchMemberOrder')->where(['out_trade_no' => $payRes['data']['out_trade_no']])->save(['status'=>1]);
				}
				return $payRes;
			};
		    return ['code'=>400,'msg'=>$payRes['msg']];
		}catch (\Exception $e){
			rwlog('faceMember_rechargeOrderQuery',$e->getMessage());
			return ['code'=>400,'msg'=>'Fail rechargeOrderQuery'];
		}

	}



	/**
	 * 增加充值记录
	 * @param $data
	 * @param $res
	 */
	public function czOrderSet($data,$res,$s_total=false){
		$total=$res['total']/100; //分转元
		#获取费率
		$mchRate=M('mchOrders')->where(['out_trade_no'=>$res['out_trade_no']])->getField('mch_rate');
		//计算手续费
		$sxf=round(($total*$mchRate)/1000,2);
		//实收
		$money=$res['data']['total']-$sxf;
		$db=M('mchMemberOrder');
		//增加充值记录
		$addArr = [
			'out_trade_no'=>$res['out_trade_no'],
			'user_id' => $data['member_user']['id'],
			'total' => $total,
			'type' => 'cz',
			'create_time' => time(),
			'mid' => $data['store_data']['sid'],
			'store_id' => $data['store_data']['id'],
			'total_type' => 1,
			'sxf'=>$sxf,
			'money'=>$money,
			'status'=>0,
			'pay_type'=>$res['payType'],
			'domain_auth'=>domain_auth()
		];
		$db->add($addArr);
		//推荐送
		$tj=$this->getTjData([
			'mid'=>$data['store_data']['sid'],
			'store_id'=>$data['store_data']['id']
		]);
		if($tj) {
			if ($total >= $tj['cz_total']) {
				$tjTotal = $tj['tjr_total'];
				//是否首次充值
				$isOne = $db->where(['user_id' => $data['member_user']['id'],'type'=>'cz', 'status' => 1, 'mid' => $data['store_data']['sid']])->find();
				if (empty($isOne)) {
					//首次充值 是否有推荐人
					$phone = M('mchMemberUser')->where(['id' => $data['member_user']['id'], 'mid' => $data['store_data']['sid']])->getField('tjr');
					if ($phone) {
						//获取推荐人ID
						$tjrUserId = M('mchMemberUser')->where(['phone' => $phone, 'mid' => $data['store_data']['sid']])->getField('id');
						if ($tjrUserId) {
							//增加推荐激活奖励
							$tj1Arr = [
								'out_trade_no' =>$res['out_trade_no'],
								'user_id' => $data['member_user']['id'],
								'total' => $tjTotal,
								'type' => 'tj',
								'desc' => "推荐赠送{$tjTotal}",
								'create_time' => time(),
								'mid' => $data['store_data']['sid'],
								'store_id' => $data['store_data']['id'],
								'total_type' => 1,
								'status' => 0,
								'pay_type'=>$res['payType'],
								'domain_auth' => domain_auth()
							];
							$db->add($tj1Arr);
							//推荐人所得
							$tj2Arr = [
								'out_trade_no' => $res['out_trade_no'],
								'user_id' => $tjrUserId,
								'total' => $tjTotal,
								'type' => 'tj',
								'desc' => "推荐赠送{$tjTotal}",
								'create_time' => time(),
								'mid' => $data['store_data']['sid'],
								'store_id' => $data['store_data']['id'],
								'total_type' => 1,
								'status' => 0,
								'pay_type'=>$res['payType'],
								'domain_auth' => domain_auth()
							];
							$db->add($tj2Arr);
						}
					}
				}
			}
		}
		//充值送
		if($s_total){
			$addArr = [
				'out_trade_no' => $res['out_trade_no'],
				'user_id' => $data['member_user']['id'],
				'total' => $s_total,
				'type' => 'cz_s',
				'desc' =>"充值赠送{$s_total}",
				'create_time' => time(),
				'mid' => $data['store_data']['sid'],
				'store_id' => $data['store_data']['id'],
				'total_type' => 1,
				'status'=>0,
				'pay_type'=>$res['payType'],
				'domain_auth'=>domain_auth()
			];
			$db->add($addArr);
		}
		//订单状态如果是成功 充值等相关状态更新
		if($res['status']==1){
			$db->where(['out_trade_no' => $res['out_trade_no']])->save(['status'=>1]);
		}
	}


	/**
	 * 获取推荐送信息
	 * @return bool
	 */
	protected function getTjData($data){
		$tjData=R('Common/MemberActivity/getDataArr',[$data['mid'], $data['store_id'], 'tj']);
		return $tjData?$tjData:false;
	}

	/**
	 * 根据金额获取充值送的金额
	 * @param $total
	 * @return bool
	 */
	protected function getTotalMoney($data,$total){
		$czData=R('Common/MemberActivity/getDataArr',[$data['mid'], $data['store_id'], 'cz']);
		if($czData) {
			$money = $czData['cz_money'];
			$cz_total=$czData['cz_total'];
			$key = array_search($total, $money);
			return $cz_total[$key];
		}
		return false;
	}



	/**
	 * 在线消费支付
	 * @param $data
	 * @return array
	 */
	public function pay($data){
		if(empty($data['type'])){
			return ['code'=>400,'msg'=>'终端类型不可为空'];
		}
		if(!in_array($data['type'],['wx','ali'])){
			return ['code'=>400,'msg'=>'终端类型无效'];
		}
		if(empty($data['total'])){
			return ['code'=>400,'msg'=>'支付金额不可为空'];
		}
		if(empty($data['user_id'])){
			return ['code'=>400,'msg'=>'会员标识不可为空'];
		}
		//获取用户信息
		$member=memberUser($data['store_data']['sid'],$data['user_id']);
		if($member){
			//余额效验
			$memberTotal=memberOrderTotal($member['id']);
			if($data['total']>$memberTotal){
				return ['code'=>400,'msg'=>'余额不足'];
			}else{
				//增加消费记录
				$arr = [
					'out_trade_no' => $this->randNum(),
					'user_id' => $member['id'],
					'total' => '-' . $data['total'],
					'type' => 'xf',
					'desc' => "会员卡消费{$data['total']}元",
					'create_time' => time(),
					'mid' => $data['store_data']['sid'],
					'store_id' =>$data['store_data']['id'],
					'total_type' => 2,
					'status' => 1,
					'pay_type' => $data['type'],
					'domain_auth' => domain_auth()
				];
				$this->db['order']->add($arr);
				R('Tasks/SendTemplate/sendMemberTemp', array($arr['out_trade_no'])); //发送收款成功模板消息
				$msg=[
					'out_trade_no'=>$arr['out_trade_no'],
					'time'=>date('Y-m-d H:i:s'),
					'pay_type'=>'member',
					'pay_type_txt'=>'会员支付',
					'number'=>$member['num'],
					'phone'=>$member['phone'],
					'total'=>$data['total'],
					'balance'=>memberOrderTotal($member['id'])
				];
				$xf=memberActivityList($data['store_data']['sid'],$data['store_data']['id'],'xf');
				if($xf){
					if($xf['hyk_xf']=='on'){
						//是否限制消费返的次数
						$count= $this->db['order']->where(['desc'=>['like','%消费返%'],'user_id'=>$member['id'],'mid'=>$data['store_data']['sid'],'status'=>1])->count();
						if(empty($xf['xff_number'])||$count<$xf['xff_number']) {
							//获取本次消费返的金额
							$xfFan = memberXfFan($data['total'],$data['store_data']['sid'], $data['store_data']['id']);
							if ($xfFan) {
								$arrs = [
									'out_trade_no' => $arr['out_trade_no'],
									'user_id' => $member['id'],
									'total' => $xfFan,
									'type' => 'xf_s',
									'desc' => "会员卡消费返{$xfFan}",
									'create_time' => time(),
									'mid' => $data['store_data']['sid'],
									'store_id' => $data['store_data']['id'],
									'total_type' => 1,
									'status' => 1,
									'pay_type' => $data['type'],
									'domain_auth' => domain_auth()
								];
								$this->db['order']->add($arrs);
								$msg['xff_total']=$xfFan;
								$msg['xff_total_txt']="会员卡消费返{$xfFan}元";
								$msg['balance']=memberOrderTotal($member['id']); //更新后的余额
							}
						}
					}
				}
				return ['code'=>100,'msg'=>'支付成功','data'=>$msg];
			}
		}else{
			return ['code'=>400,'msg'=>'非会员,获取会员信息失败'];
		}
	}


	/**
	 * 会员卡注册
	 * @param $data
	 * @return array
	 */
	public function reg($data){
		if(empty($data['type'])){
			return ['code'=>400,'msg'=>'终端类型不可为空'];
		}
		if(!in_array($data['type'],['wx','ali'])){
			return ['code'=>400,'msg'=>'终端类型无效'];
		}
		if($data['phone']==$data['invite_phone']){
			return ['code'=>400,'msg'=>'推荐人不能与注册手机号一致'];
		}
        //效验验证码
		$isVerify=$this->checkVerify($data);
		if(false == $isVerify){
			return ['code'=>400,'msg'=>'验证码无效'];
		}
		//验证是否注册
		$where=['mid'=>$data['store_data']['sid'],'phone'=>$data['phone']];
		$count=M('mchMemberUser')->where($where)->find();
		if(!empty($count[$data['type'].'_id'])){
			return ['code'=>400,'msg'=>'当前手机号已是本店会员了'];
		}
		$member_id=$count['id'];
		$msg=[];
		if($count){
			//已有会员信息
			$this->db['user']->where($where)->save([$data['type'].'_id'=>$data['user_id']]);
			$msg['one']=0;
			$msg['msg']='会员绑卡成功';
		}else {//新会员
			$arr = [
				'num' => $this->randNum(),
				'mid' => $data['store_data']['sid'],
				'store_id' => $data['store_data']['id'],//注册入口
				'phone' => $data['phone'],//
				'name' => $data['name'],
				'birthday' => $data['birthday'],
				'create_time' => time(),
				'tjr'=>$data['invite_phone'],
				'domain_auth'=>domain_auth()
			];
			$arr[$data['type'].'_id'] = $data['user_id'];
			$member_id=$this->db['user']->add($arr);
			//激活成功 赠送xxx
			$list = R('Common/MemberActivity/getDataArr', [$data['store_data']['sid'],$data['store_data']['id'], 'jh']);
			$msg['one']=1;
			$msg['msg']='会员开卡成功';
			if (!empty($list['total'])) {
				$addArr = [
					'out_trade_no'=>$this->randNum(),
					'user_id' => $member_id,
					'total' => $list['total'],
					'type' => 'jh',
					'desc' => "激活送{$list['total']}",
					'create_time' => time(),
					'mid' => $data['store_data']['sid'],
					'store_id' => $data['store_data']['id'],
					'total_type' => 1,
					'domain_auth'=>domain_auth()
				];
				$this->db['order']->add($addArr);
				$msg['jh_total']=$list['total'];
				$msg['jh_text']=$addArr['desc'].'元';
			}
		}
		$msg['total']=memberOrderTotal($member_id['id']);
		$msg['phone']=$data['phone'];
		return ['code'=>100,'msg'=>$msg['msg'],'data'=>$msg];
	}


	/**
	 * 效验验证码
	 * @param $phone
	 * @param $verify
	 * @return bool
	 */
	public function checkVerify($data){
		$cacheId=$data['store_data']['sid'].'_'.$data['phone'].'_member';
		return S($cacheId)==$data['verify'];
	}

	/**
	 * 推荐人查询
	 * @param $data
	 * @return array
	 */
	public function invitePhone($data){
		if(empty($data['phone'])){
			return ['code'=>400,'msg'=>'推荐人手机号不可为空'];
		}
		$where=[
			'mid'=>$data['store_data']['sid'],
			'phone'=>$data['phone'],
			'domain_auth'=>domain_auth()
		];
		$res=$this->db['user']->where($where)->find();
		if($res){
			return ['code'=>100,'msg'=>'推荐人存在'];
		}else{
			return ['code'=>400,'msg'=>'推荐人不存在'];
		}
	}

	/**
	 * 注册-发送验证码
	 * @param $data
	 * @return array
	 */
	public function regSms($data){
		if(empty($data['type'])){
			return ['code'=>400,'msg'=>'终端类型不可为空'];
		}
		$where=['mid'=>$data['store_data']['sid'],'phone'=>$data['phone']];
		$count=M('mchMemberUser')->where($where)->find();
		if(!empty($count[$data['type'].'_id'])){
			return ['code'=>400,'msg'=>'当前手机号已是本店会员了'];
		}
		$code=RandStr(6);
		$cacheId=$data['store_data']['sid'].'_'.$data['phone'].'_member';
		$sms=ALI_SMS();
		$aliSms = new \Think\Alisms($sms);
		$sms_data=array(
			'mobile'=> $data['phone'], #接收手机号
			'code'  => $sms['sms_member'],#验证码模板ID
			'sign'  => $sms['sms_sign'], #模板签名 必需审核通过
			'param' =>json_encode(array(
				'code'=>$code, #验证码
			))
		);
		if (sms_api() == 1) { #用阿里云通信接口
			$re = new_ali_sms($sms_data);
			if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
				S($cacheId,$code,1800);
				return ['code'=>100,'msg'=>'发送成功'];
			} else {
				return ['code'=>400,'msg'=>$re['Message']];
			}
		}else {
			$re = $aliSms->sms_send($sms_data);
			if ($re['err_code'] == 0 && $re['success'] == true) {
				S($cacheId,$code,1800);
				return ['code'=>100,'msg'=>'发送成功'];
			} else {
				return ['code'=>400,'msg'=>$re['sub_msg']];
			}
		}
	}


	/**
	 * 会员信息查询接口
	 * @param $data
	 * @return array
	 */
	public function user($data){
		if(empty($data['user_id'])){
			return ['code'=>400,'msg'=>'获取用户信息失败'];
		}
		$where=[
		   'mid'=>$data['store_data']['sid'],
		   'wx_id|ali_id'=>$data['user_id'],
		   'domain_auth'=>domain_auth()
		];
		$res=$this->db['user']->where($where)->find();
		if($res){
			$seller=Get_Seller($data['store_data']['sid']);
			return ['code'=>100,'msg'=>'获取会员信息成功','data'=>[
				'is_member'=>1,
				'number'=>$res['num'],
				'phone'=>$res['phone'],
				'name'=>$res['name'],
				'total'=>memberOrderTotal($res['id']),
				'user_id'=>$data['user_id'],
				'mch_id'=>$data['store_data']['sid'],
				'mch_name'=>$seller['mch_name'],
				'reg_store_id'=>$res['store_id'],
				'reg_store_name'=>$data['store_data']['name'],
			]];
		}else{
			return ['code'=>100,'msg'=>'获取会员信息失败','data'=>[
				'is_member'=>0,
				'user_id'=>$data['user_id'],
				'mid'=>$data['store_data']['sid']
			]];
		}
	}


	/**
	 * 会员卡号生成
	 * @return string
	 */
	public function randNum(){
		return date('ymd').(date('y') + date('d')).sprintf('%03d', rand(0, 9)).str_pad((time() - strtotime(date('Y-m-d'))), 1, 0, STR_PAD_LEFT) . substr(microtime(), 2, 4);
	}
}