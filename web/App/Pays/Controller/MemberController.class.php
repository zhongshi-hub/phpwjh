<?php
namespace Pays\Controller;
use Think\Controller;
use think\Exception;

class MemberController extends Controller {


	protected  $openid;

	public function _initialize(){
		if(!session('member.openid')){
			$openid=$this->getUserId();
			if(empty($openid)){
				 $this->error('获取会员信息失败');
			}else{
				session('member.openid',$openid);
			}
		}

		//存在商户ID 初始化session
		if(I('get.mid')){
			//获取充值门店信息
			$conf=R('Common/Member/getTempConf',[I('get.mid')]);
			session('member.mid',$conf['mid']);
			if(empty(session('member.store_id'))) {
				session('member.store_id', $conf['pay_store']);
			}
			session('member.pay_store_id', $conf['pay_store']);
			$memberUser=memberUser($conf['mid'],session('member.openid'));
			if($memberUser){
				session('member.user', $memberUser);
			}else {
				if(ACTION_NAME!='reg') {
					//没有找到会员信息 进行注册
					redirect(U('reg'));
				}

			}
		}
		if(empty(session('member.mid'))||empty(session('member.store_id'))){
			$this->error('非法操作');
		}else{
			$conf=R('Common/Member/getTempConf',[session('member.mid')]);
			session('member.pay_store_id', $conf['pay_store']);
		}
		$this->openid= session('member.openid');
		$this->assign([
			'session_member'=>session('member'),
			'openid'=>$this->openid,
			'openid_type'=>USER_AGENT(),
			'mid'=>session('member.mid'),
			'store_id'=>session('member.store_id'),
			'pay_store_id'=>session('member.pay_store_id')
		]);
	}


	
	/**
	 * 清除session  测试用
	 */
	public function clearCache(){
		session('member',null);
		echo 'clearCache ok';
	}

	/**
	 * 推荐人效验
	 */
	public function tjrCheck(){
		if(IS_POST) {
			$data = I('post.');
			$where = [
				'phone' => $data['phone'],
				'mid' => $data['mid']
			];
			$count = M('mchMemberUser')->where($where)->count();
			if ($count) {
				$this->success('有效推荐人');
			} else {
				$this->error('推荐人不存在');
			}
		}else{
			$this->error('非法请求');
		}
	}



	/**
	 * 发送验证码
	 */
	public function sendSms(){
		$data=I('post.');
		$code=RandStr(6);
		$cacheId=$data['phone'].'_member';
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
				$this->success('发送成功');
			} else {
				$this->error($re['Message']);
			}
		}else {
			$re = $aliSms->sms_send($sms_data);
			if ($re['err_code'] == 0 && $re['success'] == true) {
				S($cacheId,$code,1800);
				$this->success('发送成功');
			} else {
				$this->error($re['sub_msg']);
			}
		}
	}


	/**
	 * 效验验证码
	 * @param $phone
	 * @param $verify
	 * @return bool
	 */
	public function checkVerify($phone,$verify){
		$cacheId=$phone.'_member';
		return S($cacheId)==$verify;
	}

	/**
	 *
	 * 会员注册激活
	 */
	public function reg(){
		if(IS_POST){
			$data=I('post.');
			//效验验证码
			$isVerify=$this->checkVerify($data['phone'],$data['verify']);
			if(false == $isVerify){
				$this->error('验证码无效');
			}
			//检验商户ID
			if(empty($data['mid'])){
				$this->error('非法操作');
			}
			if($data['phone']==$data['tjr']){
				$this->error('推荐人不能与注册手机号一致');
			}
			//验证是否注册
			$where=['mid'=>$data['mid'],'phone'=>$data['phone']];
			$count=M('mchMemberUser')->where($where)->find();
			if(!empty($count[$data['openid_type']])){
				$this->error('您已是本店会员了');
			}
			if($count){
				//已有会员信息
				M('mchMemberUser')->where($where)->save(['birthday' => $data['birthday'],'name' => $data['name'],$data['openid_type']=>$data['openid']]);
				$msg='会员卡绑定成功';
			}else {//新会员
				$arr = [
					'num' => $this->randNum(),
					'mid' => $data['mid'],
					'store_id' => $data['store_id'],//注册入口
					'phone' => $data['phone'],//
					'name' => $data['name'],
					'birthday' => $data['birthday'],
					'create_time' => time(),
					'tjr'=>$data['tjr'],
					'domain_auth'=>domain_auth()
				];
				$arr[$data['openid_type']] = $data['openid'];
				$member_id=M('mchMemberUser')->add($arr);
				//激活成功 赠送xxx
				$list = R('Common/MemberActivity/getDataArr', [$data['mid'], $data['store_id'], 'jh']);
				if (!empty($list['total'])) {
					$addArr = [
						'out_trade_no'=>$this->randNum(),
						'user_id' => $member_id,
						'total' => $list['total'],
						'type' => 'jh',
						'desc' => "激活送{$list['total']}",
						'create_time' => time(),
						'mid' => $data['mid'],
						'store_id' => $data['store_id'],
						'total_type' => 1,
						'domain_auth'=>domain_auth()
					];
					M('mchMemberOrder')->add($addArr);
				}
				$msg='会员卡开卡成功';
			}
			$memberUser=memberUser($data['mid'],$data['openid']);
			session('member.user',$memberUser);

			//开卡成功
            $url=U('card',['mid'=>$data['mid'],'memberNo'=>$member_id]);
			$this->success($msg,$url);
		}else {
			$this->display();
		}
	}


	public function pays(){
		if(IS_POST){
		   $data=I('post.');
		   //余额效验
		   $memberTotal=memberOrderTotal($data['member']);
		   if($data['total']>$memberTotal){
		     	$this->error('余额不足');
		   }else {
			   //增加消费记录
			   $arr = [
				   'out_trade_no' => $this->randNum(),
				   'user_id' => $data['member'],
				   'total' => '-' . $data['total'],
				   'type' => 'xf',
				   'desc' => "会员卡消费{$data['total']}元",
				   'create_time' => time(),
				   'mid' => $data['mid'],
				   'store_id' => $data['store_id'],
				   'total_type' => 2,
				   'status' => 1,
				   'pay_type' => $data['type'],
				   'domain_auth' => domain_auth()
			   ];
			   M('mchMemberOrder')->add($arr);
			   $url = U('result', ['oid' => $arr['out_trade_no']]);
			   R('Tasks/SendTemplate/sendMemberTemp', array($arr['out_trade_no'])); //发送收款成功模板消息
			   //是否会员卡消费返
			   $xf=memberActivityList($data['mid'],$data['store_id'],'xf');
			   if($xf){
			   	 if($xf['hyk_xf']=='on'){
			   	 	 //是否限制消费返的次数
					 $count= M('mchMemberOrder')->where(['desc'=>['like','%消费返%'],'user_id'=>$data['member'],'mid'=>$data['mid'],'status'=>1])->count();
					 if(empty($xf['xff_number'])||$count<$xf['xff_number']) {
						 //获取本次消费返的金额
						 $xfFan = memberXfFan($data['total'], $data['mid'], $data['store_id']);
						 if ($xfFan) {
							 $arrs = [
								 'out_trade_no' => $arr['out_trade_no'],
								 'user_id' => $data['member'],
								 'total' => $xfFan,
								 'type' => 'xf_s',
								 'desc' => "会员卡消费返{$xfFan}",
								 'create_time' => time(),
								 'mid' => $data['mid'],
								 'store_id' => $data['store_id'],
								 'total_type' => 1,
								 'status' => 1,
								 'pay_type' => $data['type'],
								 'domain_auth' => domain_auth()
							 ];
							 M('mchMemberOrder')->add($arrs);
						 }
					 }
				 }
			   }
			   $this->success('会员卡支付成功', $url);
		   }
		}
	}

	/**
	 * 充值
	 */
	public function paySend(){
		$data=I('post.');
		$data['phone']=tel_replace(session('member.user')['phone']);
		try {
			$type = $data['type'] == 'wx' ? 'PWxPay' : 'PAliisv';
			$ret = R('Pays/' . $type . '/memberPay', [$data]);
			if($ret['status']==true){
				$cz_total=$this->getTotalMoney((string)$data['total']); //充值送
				//存储充值数据
				$this->czOrderSet($data,$ret,$cz_total?$cz_total:false);
				$this->success($ret['data']);
			}else{
				$this->error($ret['msg']);
			};
		}catch (\Exception $e){
			$this->error('通信失败N');
		}
	}

	/**
	 * 根据金额获取充值送的金额
	 * @param $total
	 * @return bool
	 */
	protected function getTotalMoney($total){
		$czData=R('Common/MemberActivity/getDataArr',[session('member.mid'), session('member.store_id'), 'cz']);
		if($czData) {
			$money = $czData['cz_money'];
			$cz_total=$czData['cz_total'];
			$key = array_search($total, $money);
			return $cz_total[$key];
		}
		return false;
	}

	/**
	 * 获取推荐送信息
	 * @return bool
	 */
	protected function getTjData(){
		$tjData=R('Common/MemberActivity/getDataArr',[session('member.mid'), session('member.store_id'), 'tj']);
		return $tjData?$tjData:false;
	}


	/**
	 * 增加充值记录
	 * @param $data
	 * @param $res
	 */
	public function czOrderSet($data,$res,$s_total=false){
		#获取费率
		$mchRate=M('mchOrders')->where(['out_trade_no'=>$res['data']['out_trade_no']])->getField('mch_rate');
		//计算手续费
		$sxf=round(($res['data']['total']*$mchRate)/1000,2);
		//实收
		$money=$res['data']['total']-$sxf;
		$db=M('mchMemberOrder');
		//增加充值记录
		$addArr = [
			'out_trade_no'=>$res['data']['out_trade_no'],
			'user_id' => session('member.user')['id'],
			'total' => $res['data']['total'],
			'type' => 'cz',
			'create_time' => time(),
			'mid' => $data['mid'],
			'store_id' => $data['store_id'],
			'total_type' => 1,
			'sxf'=>$sxf,
			'money'=>$money,
			'status'=>0,
			'pay_type'=>$res['data']['type'],
			'domain_auth'=>domain_auth()
		];
		$db->add($addArr);
		//推荐送
		$tj=$this->getTjData();
		if($tj) {
			if ($data['total'] >= $tj['cz_total']) {
				$tjTotal = $tj['tjr_total'];
				//是否首次充值
				$isOne = $db->where(['user_id' => session('member.user')['id'],'type'=>'cz', 'status' => 1, 'mid' => $data['mid']])->find();
				if (empty($isOne)) {
					//首次充值 是否有推荐人
					$phone = M('mchMemberUser')->where(['id' => session('member.user')['id'], 'mid' => $data['mid']])->getField('tjr');
					if ($phone) {
						//获取推荐人ID
						$tjrUserId = M('mchMemberUser')->where(['phone' => $phone, 'mid' => $data['mid']])->getField('id');
						if ($tjrUserId) {
							//增加推荐激活奖励
							$tj1Arr = [
								'out_trade_no' => $res['data']['out_trade_no'],
								'user_id' => session('member.user')['id'],
								'total' => $tjTotal,
								'type' => 'tj',
								'desc' => "推荐赠送{$tjTotal}",
								'create_time' => time(),
								'mid' => $data['mid'],
								'store_id' => $data['store_id'],
								'total_type' => 1,
								'status' => 0,
								'pay_type' => $res['data']['type'],
								'domain_auth' => domain_auth()
							];
							$db->add($tj1Arr);
							//推荐人所得
							$tj2Arr = [
								'out_trade_no' => $res['data']['out_trade_no'],
								'user_id' => $tjrUserId,
								'total' => $tjTotal,
								'type' => 'tj',
								'desc' => "推荐赠送{$tjTotal}",
								'create_time' => time(),
								'mid' => $data['mid'],
								'store_id' => $data['store_id'],
								'total_type' => 1,
								'status' => 0,
								'pay_type' => $res['data']['type'],
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
				'out_trade_no'=>$res['data']['out_trade_no'],
				'user_id' => session('member.user')['id'],
				'total' => $s_total,
				'type' => 'cz_s',
				'desc' =>"充值赠送{$s_total}",
				'create_time' => time(),
				'mid' => $data['mid'],
				'store_id' => $data['store_id'],
				'total_type' => 1,
				'status'=>0,
				'pay_type'=>$res['data']['type'],
				'domain_auth'=>domain_auth()
			];
			$db->add($addArr);
		}
	}

	/**
	 * 交易订单详情
	 */
	public function payResult(){
		$id=I('get.id');
		if(!empty($id)){
			$data = M('mchMemberOrder')->where(['id' => $id, 'mid' => session('member.mid')])->find();
		}else {
			$oid = I('get.oid');
			$order = M('mchOrders')->where(['out_trade_no' => $oid])->field('status,alleys,mid')->find();
			if ($order['status'] != 1) { //不是成功状态 利用查询接口
				$ret = R('Pays/' . $order['alleys'] . '/orderResult', [$oid]);
				if ($ret['res_status'] == 1) { //订单支付成功
					M('mchMemberOrder')->where(['out_trade_no' => $oid, 'mid' => $order['mid']])->save(['status' => 1]);
				}else {
					sleep(3); //防止订单记录没更新 延迟3秒
				}
			}
			//查看当前订单记录
			$data = M('mchMemberOrder')->where(['out_trade_no' => $oid, 'mid' => $order['mid']])->find();
		}
		$this->assign($data);
		$this->display();
	}


	/**
	 * 消费结果
	 */
	public function result(){
		$oid=I('get.oid');
		if(empty($oid)){
			$this->error('非法操作');
		}
		$data=M('mchMemberOrder')->where(['out_trade_no'=>$oid])->find();
		if(!$data){
			$this->error('未找到对应的消费记录');
		}
		$this->assign($data);
		$this->display();
	}


	/**
	 * 获取记录
	 */
	public function getOrderList(){
		if(IS_POST) {
			$page = I('post.page');
			$limit = I('post.size');
			$type = I('post.type');
			$db = M('mchMemberOrder');
			$where = [
				'user_id' => session('member.user')['id'],
				'total_type' => $type ? $type : 1,
				'status'=>1
			];
			$count = $db->where($where)->count();
			$order = $db->where($where)->page($page . ',' . $limit)->order('id desc')->field('out_trade_no,id,total,type,desc,mid,store_id,create_time')->select();
			$countPage = ceil($count / $limit);
			$list = [];
			foreach ($order as $k => $v) {
				$store = Get_Store($v['store_id']);
				$store_name = $store['name'];
				$list[$k] = [
					'id' => $v['id'],
					'total' => $v['total'],
					'type' => $v['type'],
					'desc' => $v['desc'] . '元',
					'mid' => $v['mid'],
					'oid'=>$v['out_trade_no'],
					'store_id' => $store_name,
					'create_time' => date('Y-m-d H:i:s', $v['create_time'])
				];
			}
			header('Content-Type:application/json');
			exit(json_encode(['count' => $countPage, 'data' => $list], JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * 会员卡号生成
	 * @return string
	 */
	public function randNum(){
		return date('ymd').(date('y') + date('d')).sprintf('%03d', rand(0, 9)).str_pad((time() - strtotime(date('Y-m-d'))), 1, 0, STR_PAD_LEFT) . substr(microtime(), 2, 4);
	}



	/**
	 * 会员卡界面
	 */
	public function card(){
		$this->display();
	}

	/**
	 * 会员活动
	 */
	public function cardActivity(){
		$this->display();
	}


	/**
	 * 充值记录
	 */
	public function cardCzOrder(){
		$this->display();
	}

	/**
	 * 消费记录
	 */
	public function cardXfOrder(){
		$this->display();
	}

	/**
	 * 使用说明
	 */
	public function cardHelp(){
		$this->display();
	}

	/**
	 * 会员卡充值
	 */
	public function cardPay(){
		//获取近30天的交易
		$time=time()-(30*24*3600);
		$where=[
		   'user_id'=>session('member.user')['id'],
		   'create_time'=>['egt',$time]
		];
	    $order=M('mchMemberOrder')->where($where)->order('id desc')->limit(5)->select();
		$czData=R('Common/MemberActivity/getDataArr',[session('member.mid'), session('member.store_id'), 'cz']);
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
	    $assign=[
	       'total'=>$total,
	       'order'=>$order
		];
	    $this->assign($assign);
		$this->display();
	}



	/**
	 * 获取用户标识
	 * @return bool
	 */
	public function getUserId(){
		$userAgent=USER_AGENT();
		if($userAgent=='ali') {
			$app_id=GetPayConfigs('ali_appid');
			$auth_code=I('get.auth_code');
			if (!$auth_code){
				$url="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=".$app_id."&scope=auth_base&redirect_uri=".urlencode(get_url());
				redirect($url);
			}else {
				Vendor('alipay_sdk.AlipayAop');
				$Ali_Aop = new \AlipayAop();
				$msg = $Ali_Aop->Alipay_oauth($auth_code,$app_id);
				return $msg;
			}
		}elseif ($userAgent=='wx'){
			$oauth = &load_wechat('Oauth',GetWxId('m'));
			$callback = get_url();
			$state = 'member';
			if (!I('get.code')) {
				$scope = 'snsapi_base';
				$result = $oauth->getOauthRedirect($callback, $state, $scope);
				if ($result === FALSE) {
					return false;
				} else {
					redirect($result);
				}
			}else{
				$Token = $oauth->getOauthAccessToken();
				if ($Token === FALSE) {
					return false;
				} else {
					return $Token['openid'];
				}
			}
		}else{
			return $this->error('请用微信或支付宝扫码','',888);
		}
	}
}