<?php

header("Content-type:text/html;charset=utf-8");


/**
 * 兼容异步-队列写入
 * 只支持一级
 * @param $name  /队列名称
 * @param $value /队列参数
 * @return mixed
 */
function queueSet($name,$value){
	$cache=$name;
	$cache_value=S($cache);
	$arr=[];
	if($cache_value){
		if(is_array($cache_value)){
			$arr=$cache_value;
		}else{
			$arr[]=$cache_value;
		}
		//在缓存里去重插入
		if(false==array_search($value,$arr)){
			array_push($arr,$value);
		}
	}else{
		$arr[]=$value;
	}
	return S($cache,$arr);
}

/**
 * 删除队列指定参数值
 * @param $name /队列名称
 * @param $key  /队列参数
 * @return mixed
 */
function queueRm($name,$key){
	$cache=S($name);
	if($key=='clear'){
		S($name, null);
	}else {
		$key = array_search($key, $cache);
		if ($key !== false) {
			array_splice($cache, $key, 1);
			S($name, $cache);
		}
		return $cache;
	}
}


/**
 * 获取公告
 * @param string $type 默认全部  2代理 3商户
 * @param string $limit
 * @return mixed
 */
function noticeList($type='',$limit=''){
	$map['domain_auth']=domain_auth();
	if($type==2){ //全部+代理
		$map['type']=['in','1,2'];
	}elseif ($type==3){//全部+商户
		$map['type']=['in','1,3'];
	}
	if($limit){
		$data = M('noticeList')->order('sort desc')->where($map)->limit($limit)->select();
	}else {
		$data = M('noticeList')->order('sort desc')->where($map)->select();
	}
	return $data;
}

/**
 * 公告类型
 * @param string $type
 * @return array|mixed
 */
function noticeType($type=''){
	$arr=[
		'1'=>'全部',
		'2'=>'代理',
		'3'=>'商户',
	];
	return $type?$arr[$type]:$arr;
}


/**
 * 是否是超级管理员
 * @return bool
 */
function is_admin(){
	if(C('is_agent_type')) {
		return $_SESSION['user']['is_admin'] ? true : false;
	}else{
		return false;
	}
}

/**
 * 计算代理的汇总信息
 * @param $aid
 * @return array
 */
function inviteBefitTotalDraw($aid){
	//已结算金额
	$drawTotal=M('inviteDrawList')->where(['aid'=>$aid])->sum('total');
	//总金额
	$total=M('inviteBefitCount')->where(['aid'=>$aid])->getField('total');
	//可结算基恩
	$draw=bcsub($total,$drawTotal,2);
	$arr=[
		'draw'=>$draw,
		'drawTotal'=>$drawTotal?$drawTotal:0,
		'total'=>$total,
	];
	return $arr;
}


/**
 * 根据code获取直推和间推的ID
 * @param $code
 * @return array
 */
function inviteCodeId($code){
	//获取直推的信息
	$db=M('mchAgent');
	$ztId=$db->where(['invite_code'=>$code])->getField('id',true);
	//根据ID获取直推者的推荐码
	$jtCode=M('inviteCode')->where(['pid'=>['in',$ztId]])->getField('code',true);
	//获取间推的ID
	$jtId=$db->where(['invite_code'=>['in',$jtCode]])->getField('id',true);
	return [
		'zt'=>$ztId, //直推ID
		'jt'=>$jtId, //间推ID
		'merge'=>array_merge($ztId?$ztId:[],$jtId?$jtId:[]) //合并
	];
}

/**
 * 获取分成比例
 * @param $type直推还是间推
 * @param $a1邀请人级别
 * @param $a2被邀人级别
 * @return array
 */
function inviteRate($type,$a1,$a2){
	if($type=='zt'){
		$ide=(($a1==1)?'zt_a':'zt_b').$a2;
	}else{
		$ide='jt_a'.$a2;
	}
	//获取比例
	$rate=inviteSetting($ide);
	return [
		'ide'=>$ide,
		'rate'=>$rate,
	];
}

/**
 * 获取代理推荐链接
 * @param $code
 * @return string
 */
function inviteUrl($code){
	return getDomainUrl().'/invites/'.$code;
}
/**
 * 等级信息
 * @param $id
 * @return string
 */
function gradeName($id){
	switch ($id){
		case 1:
			$name='服务商';
			break;
		case 2:
			$name='省代';
			break;
		case 3:
			$name='市代';
			break;
		case 4:
			$name='区代';
			break;
		default:
			$name=''.$id;
			break;
	}
	return $name;
}

/**
 * 获取推荐码配置信息
 * @param string $field
 * @return mixed
 */
function inviteSetting($field=''){
	$setting=M('inviteSetting')->where(['domain_auth'=>domain_auth()])->getField('data');
	$data=json_decode($setting,true);
	return $field?$data[$field]:$data;
}

/**
 * 初始化没有生成代理的生成推荐码
 * @return bool
 */
function inviteInit(){
	$list=M('mchAgent')->field('id,domain_auth')->select();
	foreach ($list as $k=>$v){
		$code=M('inviteCode')->where(['pid'=>$v['id']])->getField('code');
		if(empty($code)){
			$arr=[
				'code'=>inviteCode(),
				'pid'=>$v['id'],
				'create_time'=>time(),
				'status'=>1,
				'domain_auth'=>$v['domain_auth']
			];
			M('inviteCode')->add($arr);
		}
	}
	return true;
}

/**
 * 新增代理或系统推荐码
 * @param int $pid
 * @return mixed
 */
function inviteSet($pid=0){
	$arr=[
		'code'=>inviteCode(),
		'pid'=>$pid,
		'create_time'=>time(),
		'status'=>1,
		'domain_auth'=>domain_auth()
	];
	return M('inviteCode')->add($arr);
}

/**
 * 根据邀请码获取代理信息
 * @param $code
 * @param string $field
 * @return string
 */
function inviteCodeAg($code,$field=''){
	$id=M('inviteCode')->where(['code'=>$code])->getField('pid');
	if($id){
        $ret=M('mchAgent')->where(['id'=>$id])->find();
        return $field?$ret[$field]:$ret;
	}else{
		return '直属平台';
	}

}

/**
 * 根据代理ID获取邀请码
 * @param $id
 * @return mixed
 */
function getInviteCode($id){
	$code=M('inviteCode')->where(['pid'=>$id])->getField('code');
	return $code;
}

/**
 * 生成推荐码
 * @param int $len
 * @return string
 */
function inviteCode($len=6){
   $code=domain_auth_code($len);
   //是否存在
   $count=M('inviteCode')->where(['code'=>$code])->count();
   if($count){inviteCode($len);}
   return $code;
}

/**
 * 会员卡充值类型
 * @param string $type
 * @return array|mixed
 */
function memberCzOrderType($type=''){
	$arr=[
		'cz'=>'常规充值',
		'cz_s'=>'充值满送',
		'xf_s'=>'消费返现',
		'jh'=>'激活奖励',
		'tj'=>'推荐奖励',
		'xj'=>'现金充值',
		'xj_s'=>'现金满送'
	];
	return $type?$arr[$type]:$arr;
}

/**
 * 增加消费返记录-订单模式
 * @param $oid
 */
function memberXffOrderSet($oid){
	$order=M('mchOrders')->where(['out_trade_no'=>$oid,'status'=>1])->find();
	if($order){
		//根据openid获取会员user_id
		$user_id=M('mchMemberUser')->where(['wx_id|ali_id'=>$order['sub_openid']])->getField('id');
		if($user_id){
			$xf=memberActivityList($order['mid'], $order['store_id'],'xf');
			if($xf) {
				//是否有限制返的规则
				$count = M('mchMemberOrder')->where(['desc' => ['like', '%消费返%'], 'user_id' => $user_id, 'mid' => $order['mid'], 'status' => 1])->count();
				if (empty($xf['xff_number']) || $count < $xf['xff_number']) {
					//是会员 获取系统是否有消费返的金额
					$xfFan = memberXfFan($order['total_fee'], $order['mid'], $order['store_id']);
					if ($xfFan) {
						//防止订单重复
						$cacheId=$order['out_trade_no'].'_xff';
						if(empty(S($cacheId))) {
							$arr = [
								'out_trade_no' => $order['out_trade_no'],
								'user_id' => $user_id,
								'total' => $xfFan,
								'type' => 'xf_s',
								'desc' => "订单消费返{$xfFan}",
								'create_time' => time(),
								'mid' => $order['mid'],
								'store_id' => $order['store_id'],
								'total_type' => 1,
								'status' => 1,
								'pay_type' => explode('_', $order['service'])[0],
								'domain_auth' => domain_auth()
							];
							M('mchMemberOrder')->add($arr);
							S($cacheId,'ok');
						}
					}
				}
			}
		}
	}
}

/**
 * 活动规则详情
 * @param $mid
 * @param $store_id
 * @param string $type
 * @return mixed
 */
function memberActivityList($mid,$store_id,$type='xf'){
	return  R('Common/MemberActivity/getDataArr', [$mid,$store_id, $type]);
}

/**
 * 消费返规则
 * 根据消费金额取返佣金额
 * @param $total
 * @param $mid
 * @param $store_id
 * @return bool|int|string
 */
function memberXfFan($total,$mid,$store_id){
	$list = memberActivityList($mid,$store_id, 'xf');
	if($list){
		$xf_total=0;
		if($list['fx_type']==1){
			$array = $list['xf_money'];
			foreach ($array as $i) {
				if($i>$total)continue;
				$smallest[$i] = abs($i - $total);
			}
			asort($smallest);
			$key=array_search(key($smallest),$array);
			$xf_money=$array[$key];
			if($total>=$xf_money){
				$xf_total=$list['xf_total'][$key];
			}
		}elseif ($list['fx_type']==3){
			$b=$list['xf3_money']/100;
			$xf_total=bcmul($total,$b,2);
		}
		return $xf_total?$xf_total:false;
	}
	return false;
}
/**
 * 会员卡余额
 * @param $user_id
 * @return int
 */
function memberOrderTotal($user_id){
	$res=M('mchMemberOrder')->where(['user_id'=>$user_id,'status'=>1])->sum('total');
	return $res?$res:0;
}

/**
 * 获取会员模板配置
 * @param $mid
 * @return mixed
 */
function memberTemp($mid){
   $res=M('mchMemberTemp')->where(['mid'=>$mid])->find();
   return $res;
}

/**
 * 获取会员信息
 * @param $mid
 * @param $openid
 * @param bool $field
 * @return mixed
 */
function memberUser($mid,$openid,$field=false){
	$res=M('mchMemberUser')->where(['mid'=>$mid,'wx_id|ali_id'=>$openid])->find();
	return $field?$res[$field]:$res;
}

/**
 * 根据会员ID 获取会员信息
 * @param $user_id
 * @param bool $field
 * @return mixed
 */
function memberUserData($user_id,$field=false){
	$res=M('mchMemberUser')->where(['id'=>$user_id])->find();
	return $field?$res[$field]:$res;
}

/**
 * 获取代理等级层次
 * @param $aid
 * @return int
 */
function agentGrade($aid,$grade=1){
	$data=M('mchAgent')->where(['id'=>$aid])->getField('pid');
	if($data!=0){
		return agentGrade($data,$grade+1);
	}else{
		return $grade;
	}
}

/**
 * 获取代理的字段数据
 * @param $aid
 * @param $field
 * @return mixed
 */
function agentField($aid,$field){
	$data=M('mchAgent')->where(['id'=>$aid])->getField($field);
	return $data;
}

/**
 * 支付宝服务商ISV通道获取授权商户Token
 * @param $mid
 * @return bool
 */
function aliIsvToken($mid,$type=false){
	$field=$type?$type:'app_auth_token';
	$app_auth_token=M('isvToken')->where(['mid'=>$mid])->getField($field);
	return $app_auth_token?$app_auth_token:false;
}

/**
 * 城市编码转换为名称
 * @param $id
 * @return mixed
 */
function getCityName($id){
	$res=M('ccbCityData')->where(['mid'=>$id])->getField('name');
	return $res?$res:$id;
}

/**
 * 新大陆通道提现配置
 * @param $mid
 * @param $mch_id
 * @return int
 */
function getXdlDrawSetting($mid,$mch_id){
	$res=M('mchXdlDrawSetting')->where(['mid'=>$mid,'mch_id'=>$mch_id])->getField('total');
	return $res?$res:0;
}
/**
 * 发送流量预警
 * @param $balance
 * @param $mid
 * @param null $domain_auth
 */
function send_flow_sms($balance,$mid,$domain_auth=null){
    //全局是否启用短信提醒功能
	$flowConfig=M('flowConfig');
	$sms_id=$flowConfig->where(['domain_auth'=>$domain_auth?$domain_auth:domain_auth()])->getField('sms_id');
	//如果配置了则为启用
	if($sms_id){
		//获取商户是否配置了短信提醒手机号
		$phone = mch_flow_config($mid, 'sms_phone');
		if($phone){
			//是否是正确的手机号
			if(preg_match("/^1[3456789]{1}\d{9}$/",$phone)){
				//判断是否有缓存 有缓存则发送间隔时间太短
				$cache=$phone.'_flow_cache';
				$cache_time=7200; //两个小时内有效期  两个小时只发送一次
				if(!S($cache)){
				   //发送提醒
					$sms = ALI_SMS();
					$sms_data = array(
						'mobile' => $phone, #接收手机号
						'code' => $sms_id, #模板ID
						'sign' => $sms['sms_sign'], #模板签名 必需审核通过
						'param' => json_encode(array(
							'balance' => $balance.'元', #余额
						)),
					);
					if (sms_api() == 1) { #用阿里云通信接口
						$re = new_ali_sms($sms_data);
						if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
							S($cache,'is_ok_sms1',$cache_time);
						}
					} else {
						//发送验证码
						$AliSms = new \Think\Alisms($sms);
						$re = $AliSms->sms_send($sms_data);
						if ($re['err_code'] == 0 && $re['success'] == true) {
							S($cache,'is_ok_sms2',$cache_time);
						}
					}
				}
			}
		}
	}
}

/**
 * 系统接口类型转换API接口类型
 * @param $data
 * @return string
 */
function api_service($data){
	$service=explode('_',$data);
	$type=$service[1];
	switch ($type){
		case 'jsapi':
			$api='js';
			break;
		case 'code':
			$api='native';
			break;
		case 'scan':
			$api='micropay';
			break;
		default:
			break;
	}
	return $service[0].'_'.$api;
}
/**
 * 获取轮询配置商户规则
 * @param $mid商户ID
 * @param $type支付类型
 * @param $total支付金额
 * @return array
 */
function getMchPoll($mid,$type,$total){
	$dbFlowPoll=M('flowPoll');
	$map=[];
	$map['mid']=$mid;
	$map['status']=1;
	//是否启用轮询
	$status=getFlowStatus($mid);
	//启用轮询后查看规则
	if($status){
		//是否有轮询参数
		if(($dbFlowPoll->where($map)->count())>0?true:false){
			//包含轮询参数 根据轮询参数获取配置信息
			$where=[];
			$where[$type]=1;
			$where['_string'] = 'alleys_total=0 OR alleys_total<='.$total;
			//根据条件金额筛选不限制金额或大于通道设置最低金额的规则
			$pollList=$dbFlowPoll->where($map)->where($where)->order('level desc')->select();
			if($pollList){
				//完整匹配对应规则 根据规则计算单日额度是否用完
				foreach ($pollList as $k=>$v){
					$is=getMchDaySumTotal($v['mid'],$v['mch_id'],($v['day_total']?:0),$total);
					//超出今日金额 将本规则剔出
					if(!$is){
						unset($pollList[$k]);
					}
				}
				//规则数量
				$count=count($pollList);
				if($count<1){
					$end=['status'=>0,'code'=>'004','data'=>json_encode([$mid,$type,$total]),'msg'=>'规则额度已用完,暂无可用规则'];
				}elseif($count==1) {
					//只有一个规则
					$configData=array_values($pollList)[0];
					$end=['status'=>1,'code'=>'000','data'=>json_encode([$mid,$type,$total]),'msg'=>'规则配置获取成功(1)','config_count'=>$count,'config'=>$configData];
				}else{
					$cacheName='cache_'.$mid.$type;
					$cache=S($cacheName);
					//有多个规则  随机轮询可用规则
					$arrKey = array_rand($pollList);
					if($cache){
						//有缓存,是否和上次KEY一致 如一致 重新生成key
						if($arrKey==$cache){
							if($count==($arrKey+1)){//最后一个峰值KEY 则减一
								$arrKey--;
							}elseif($arrKey==0&&$count>1){
								$arrKey++;
							}else{
								$arrKey = array_rand($pollList);
							}
						}
					}
					S($cacheName,$arrKey);
					$configData=$pollList[$arrKey];
					if(empty($configData)){
						$configData=$pollList[array_rand($pollList)];
					}
					$end=['status'=>1,'code'=>'000','data'=>json_encode([$mid,$type,$total]),'msg'=>'规则配置获取成功','config_count'=>$count,'config'=>$configData];
				}
			}else{
				$end=['status'=>0,'code'=>'003','data'=>json_encode([$mid,$type,$total]),'msg'=>'未找到对应的支付规则参数F','map'=>$map,'w'=>$where];
			}
		}else{
			$end=['status'=>0,'code'=>'002','data'=>json_encode([$mid,$type,$total]),'msg'=>'获取参数失败,无可用参数'];
		}
	}else{
		$end= ['status'=>0,'code'=>'001','data'=>json_encode([$mid,$type,$total]),'msg'=>'未启用轮询'];
	}
	if(I('get.Debug')==2){dump($end);}else{return $end;}
}

/**
 * 统计当前商户号下今日总交易额
 * @param $mid
 * @param $mchId
 * @param $dayTotal
 * @param $total
 * @return bool
 */
function getMchDaySumTotal($mid,$mchId,$dayTotal,$total){
	$today=strtotime(date('Y-m-d 00:00:00'));
	$map=[
		'mid'=>$mid,
		'mch_id'=>$mchId,
		'status'=>1,
		'createtime'=>array('egt',$today) //今日
	];
	if($dayTotal==0){
		return true;
	}else {
		$sum = M('mchOrders')->where($map)->sum('total_fee');
		if (round(($sum?:0) + $total, 2) > $dayTotal) {
			return false;
		} else {
			return true;
		}
	}
}

/**
 * 商户是否启用轮询
 * @param $mid
 * @return bool
 */
function getFlowStatus($mid){
	$dbFlowPollConfig=M('flowPollConfig');
	$status=($dbFlowPollConfig->where(['mid'=>$mid])->getField('status'))?true:false;
	return $status;
}

/**
 * 获取配置信息并转义对应格式
 * @param $mid
 * @param $mchId
 * @return array
 */
function getPollData($mid,$mchId){
	$dbFlowPoll=M('flowPoll');
	$where=[
		'mid'=>$mid,
		'mch_id'=>$mchId
	];
	$ret=$dbFlowPoll->where($where)->find();
	$data=[
		'mid'=>$ret['mid']?:$mid,
		'mch_name'=>$ret['name'],
		'mch_id'=>$ret['mch_id'],
		'mch_key'=>$ret['mch_key'],
		'mch_appid'=>$ret['mch_k1'],
		'mch_k2'=>$ret['mch_k2'],
		'mch_k3'=>$ret['mch_k3'],
	];
	return $data;
}


/**
 * 本次交易查询流量余额是否充足
 * @param $mid
 * @param $total
 * @return array
 */
function mch_flow_is_pay($mid,$total){
	$rateFee=mch_total_rate($mid,$total);//当前手续费
	if($rateFee==0){ //计算的手续费为0则不限制直接TRUE
		return ['status' => true, 'msg' => '费率0不计算手续费'];
	}else {
		$balance = flow_balance($mid);
		if ($rateFee <= $balance) {
			return ['status' => true, 'msg' => '未超出流量预充值费用','pay_fee'=>$rateFee,'balance'=>$balance];
		} else {
			return ['status' => false, 'msg' => '流量余额不足本次交易','pay_fee'=>$rateFee,'balance'=>$balance];
		}
	}
}


/**
 * 流量手续费计算
 * @param $mid
 * @param $total
 * @return float|int
 */
function mch_total_rate($mid,$total){
	$rate=mch_flow_config($mid,'rate');
	$fee=($total*$rate)/1000;
	$fee=round($fee,2);
	if($rate!=0&&$fee<0.01){$fee=0.01;} //如果费率设置的0 则不扣手续费 否则按照最低0.01收取
	return $fee;
}

/**
 * 商户是否启用流量功能
 * @param $mid
 * @return bool
 */
function mch_is_flow($mid,$domain_auth=null){
	//全局是否启用流量功能
	$flowConfig=M('flowConfig');
	$sys=$flowConfig->where(['domain_auth'=>$domain_auth?$domain_auth:domain_auth()])->find();
	if($sys['status']==1) {
		//判断商户是否启用流量功能
		$status = mch_flow_config($mid, 'status');
		if($status){
			//流量是否到达预警金额提醒
            $balance=flow_balance($mid);//可用余额
            $flow_sms_balance=$sys['sms_total'];//系统告警金额
            if($balance<=$flow_sms_balance){
				send_flow_sms($balance,$mid,$domain_auth?$domain_auth:domain_auth());
			}
		}
		return $status ? true : false;
	}else{
		return false;
	}
}

/**
 * 商户流量配置规则
 * @param $mid
 * @param null $type
 * @return mixed
 */
function mch_flow_config($mid,$type=null){
	$dbFlowPollConfig=M('mchFlowConfig');
	$data=$dbFlowPollConfig->where(['mid'=>$mid])->find();
	return $type?$data[$type]:$data;
}

/**
 * 流量订单数据操作
 * @param $mid商户ID
 * @param $oid订单ID
 * @param $total交易金额
 * @param string $payType 支付类型 微信OR支付宝
 * @param $status交易状态
 * @param int $type 操作类型 1充值 2扣款
 */
function mch_flow_order($mid,$oid,$total,$payType='wx',$status=0,$type=1,$auth=null){
	$db=M('mchFlowOrder');
	$setTotal=($type==1)?$total:(mch_total_rate($mid,$total));
	$arr=[
		'mid'=>$mid,
		'type'=>($type==1)?1:2,
		'time'=>time(),
		'oid'=>$oid,
		'oid_total'=>$total,
		'total'=>$setTotal,
		'status'=>$status?:0,
		'pay_type'=>($payType=='wx'?'微信':'支付宝').(($type==1)?'扫码充值':'交易扣费'),
		'domain_auth'=>$auth?$auth:domain_auth()
	];
	$ret=$db->where(['mid'=>$mid,'oid'=>$oid])->count();
	if($ret<1) {
		$db->add($arr);
	}else{
		$db->where(['mid'=>$mid,'oid'=>$oid])->save($arr);
	}
}

/**
 * 更新订单状态
 * @param $mid
 * @param $oid
 * @param $status
 * @return mixed
 */
function mch_flow_order_update($oid,$status){
	$db=M('mchFlowOrder');
	$count=$db->where(['oid'=>$oid])->count();
	if($count) {
		$ret = $db->where(['oid' => $oid])->save(['status' => $status]);
		return $ret;
	}
	return false;
}

/**
 * 流量功能-余额查询
 * @param $id
 * @return int
 */
function flow_balance($id){
	$db=M('mchFlowOrder');
	//充值金额
	$map=[
		'mid'=>$id,
	];
	$cz=$db->where($map)->where(['type'=>1,'status'=>1])->sum('total');
	$xf=$db->where($map)->where(['type'=>2,'status'=>1])->sum('total');
	$ret=round($cz-$xf,2);
	return $ret?:0;
}


/**
 * 云喇叭播报
 * @param $id 门店ID
 * @param $total消费金额
 * @param int $pt 支付类型
 * @return array
 */
function sendSpeaker($id,$total,$pt=1,$doamin_auth=''){
    Vendor('ylb');
    $where['sid'] = $id; //门店ID
    $where['domain_auth'] = $doamin_auth?:domain_auth();
    $rel = M('StoreSpeaker')->where($where)->find();
    if($rel&&$rel['status']==1){
        if($rel['appid']&&$rel['appkey']){
            $ylb=new \ylb($rel['appid'],$rel['appkey'],C('YLB.uid'));
        }else{
            $ylb=new \ylb(C('YLB.appid'),C('YLB.appkey'),C('YLB.uid'));
        }
        return $ylb->speaker($rel['vid'],$rel['num'],$total*100,$pt);
    }
    return ['status'=>0,'msg'=>'配置信息不存在或状态未开启'];
}

//获取等级详细数据
function extensionGrade($id){
    $res=M('extensionGrade')->where(['domain_auth'=>domain_auth()])->where(['id'=>$id])->find();
    return $res;
}

//获取当前用户等级ID
//条件数组  字段名  all所有
function extensionMch($where=[],$field){
    $res=M('extensionMch')->where(['domain_auth'=>domain_auth()])->where($where)->find();
    if($field=='all'){
        return $res;
    }else{
        return $res[$field];
    }
}

//营销设置数据
function extensionSetting($fid){
    $fid=$fid?$fid:'id';//默认取id值
    return M('extensionSetting')->where(['domain_auth'=>domain_auth()])->getField($fid);
}


//生成代理邀请码链接
function agentCode($id){
    return U('Mch/Plugs/AgVite',array('Code'=>Xencode(date('YmdHis')."Xun_".$id)));
}

//是否是接口订单 接口订单号第一个字母已A区分
function IsApiOrder($oid){
    $res=substr($oid,0,1);
    if($res=='A'){
        return true;
    }else{
        return false;
    }
}

/**
 * 根据OID获取异步数据发送的数据
 * @param $oid
 * @param $field
 * @return mixed
 */
function get_api_notify($oid,$field){
	$db=M('ApiNotify');
	$data=$db->where(['out_trade_no'=>$oid])->find();
	return $field?$data[$field]:$data;
}

/**
 * 移动支付通道是否有退款接口
 * @param $mod
 * @return bool
 */
function is_method_refund($mod){
	$module = A('Pays/P' . $mod);
	$modules = method_exists($module, 'refund');
	return $modules?true:false;
}


/**
 * 附加推送  通道处不要回复success 根据下级返回处理  切记
 * 异步推送任务处理 采用通道方异步处理 是否成功按照通道方要求 默认结果采用数据库处理
 * 订单ID 成功后处理(默认输出success)  失败处理
 * 使用方法 在通道异步处理处增加方法
 * send_notify($out_trade_no,'SUCCESS'，'ERROR');
 * @param $oid
 * @param null $success
 * @param null $error
 */
function send_notify($oid,$success=null,$error=null,$type=false,$json=false){
    #判断此订单是否异步发送给下级
    $db=M('ApiNotify');
    $ApiNotify=$db->where(['out_trade_no'=>$oid])->find();
    if($ApiNotify) {
		//if(!$ApiNotify){$msg='error';}
		$notify_url = M('MchTerminal')->where(['appid' => $ApiNotify['appid']])->field('appid,appkey,notify_url')->find();
		$url = $ApiNotify['notify_url'] ? $ApiNotify['notify_url'] : $notify_url['notify_url'];
		if ((true==$type)||($ApiNotify['send_status'] != 1)) {
			if($url) {
				#没有收到下级异步通知成功 将POST JSON给信息
				$res = M('MchOrders')->where(['out_trade_no' => $oid])->find();
				if($res) {
					$notifyData = [
						'appid' => $ApiNotify['appid'],
						'method' => $ApiNotify['pay_type'],
						'status' => $res['status'], //订单状态 0未支付/支付中 1支付成功 2退款成功
						'out_trade_no' => $res['out_trade_no'], //订单号
						'transaction_id' => $res['transaction_id'], //官方或三方订单号
						'total_fee' => $res['total_fee'] * 100, //交易金额 转换为分单位
						'create_time' => date('Y-m-d H:i:s', $res['createtime']), //创建时间
						'nonce_str' => uniqid(), //随机字符串
					];
					$notifyData['sign'] = ApiSign($notifyData, $notify_url['appkey']); //签名
					#将字符串转换为Json格式发送
					$curl = curl_calls($url, json_encode($notifyData, JSON_UNESCAPED_UNICODE), '', true);
					$arr = [
						'notify_url' => $url,
						'notify_data' => json_encode($notifyData, JSON_UNESCAPED_UNICODE),
						'send_time' => date('Y-m-d H:i:s'),
						'send_rel' => $curl,
						'send_status' => (strtolower($curl) == 'success') ? 1 : 0
					];
					$db->where(['out_trade_no' => $oid])->save($arr); //保存数据
					$db->where(['out_trade_no' => $oid])->setInc('send_num'); //增加异步次数
					if ($type) {
						$msg = ['status' => 1, 'msg' => '下发异步成功', 'rel' => $curl];
					} else {
						if (strtolower($curl) == 'success') {
							$msg = ['status' => 1, 'msg' => $success ? $success : 'success'];
						} else {
							$msg = ['status' => 0, 'msg' => $error ? $error : 'error data'];
						}
					}
				}else{
					$msg =['status'=>0,'msg'=>'未找到相应的订单,请检查订单号是否正确'];
				}
			}else{
				$msg =['status'=>0,'msg'=>'未获取到异步参数URL地址,发送失败'];
			}
		} else {
			$msg =['status'=>1,'msg'=> $success?$success:'success'];
		}
	}else{
		$msg =['status'=>0,'msg'=>'当前订单非接口订单,无法发送异步参数'];
	}
    if(true==$type){return $msg;}else{if($json){header('Content-type: application/json');}die($msg['msg']);}
}




function ApiSign($data=array(),$key,$type=null){
    ksort($data);
    $tmp = '';
    foreach ($data as $k => $v ) {
        if($k == 'sign' ||$k == 'store_data'|| $v == '' || $v == null) continue;
        $tmp .= $k . '=' . $v . '&';
    }
    $tmp .= 'key=' . $key;
    if($type){ //测试使用 字符串
        return $tmp;
    }else {
        return strtoupper(md5($tmp));
    }
}


#睿付接口方法
function rfpay_sdk(){
    Vendor('rfpay_sdk.BaseService');
    $api=new \BaseService(C('rfpay_config'));
    return $api;
}


#新版阿里云云通信
function new_ali_sms($data){
    Vendor('Alisms.SendSms');
    $api=new \SendSms(ALI_SMS());
    $res=$api->sendSms($data);
    return json_decode(json_encode(object_to_array($res)),true);
}


#全局判断短信接口类型
#$type 1查询所有数据  其它参数或空为接口类型 1:阿里云短信 0或空为阿里大于(旧版)
function sms_api($type){
    $res=ALI_SMS();
    return $res['sms_api'];

}


#发送商户模板消息
function sendMchTemplateMessage($mch_id, $type,$ext)
{
    $data = array(
        'mc' => 'SendMchTemplate', #模块
        'ac' => 'Message' #方法
    );
    $res = ali_mns($data);
    if ($res['status'] == 1) {
        $arr = array(
            'order_id' => $mch_id,
            'type' => $type,
            'ext' => $ext,
        );
        $_data = array(
            'task_data' => serialize($arr),
            'auth_code' => domain_auth(),
            'rel' => serialize($res)
        );
        $where['messageId'] = $res['messageId'];
        $where['id'] = $res['msn_id'];
        M('alimsn')->where($where)->save($_data);
        return true;
    }else{
        return false;
    }
}

#盛付通提现结果
function  Get_Sft_total_end($type){
    switch ($type){
        case  18:
            $info='提现成功';
            break;
        case  19:
            $info='提现失败';
            break;
        case  20:
            $info='提现处理中';
            break;
        case 99:
            $info='系统异常';
            break;
        default:
            $info='错误代码:'.$type;
            break;
    }
    return $info;
}

#盛付通费率标签
function SftRate($rate){
    $res = M('RateData')->where(array('rate'=>$rate))->getField('sft_rate');
    if($res){
       $rel=array('status'=>1,'data'=>$res);
    }else{
       $rel=array('status'=>0,'msg'=>'未找到当前费率('.$rate.')标签,请联系管理员或更换费率!');
    }
    return $rel;
}

#银行卡鉴权加日志
#数据 鉴权类型 鉴权商户/二维码Code  通道类型
function card_validate_calls($care_data,$name,$mid,$alley){
    #判断此卡信息是否存在 如系统已存在鉴权成功数据 则直接进行成功返回
    $arr=array(
        'card'=>$care_data['cardNo'],
        'cert'=>$care_data['certNo'],
        'name'=>$care_data['name'],
        'status'=>1
    );
    $Log=M('CardValidateLog')->where($arr)->count();
    if($Log){
        $rel = array('status' => 1, 'msg' => '验证通过');
    }else {
        if ((DomainAuthData('auth_card') - auth_card_count(domain_auth())) <= 0) {
            $rel = array('status' => 0, 'msg' => '系统鉴权次数已用完!请联系平台管理!');
        } else {
            $res = card_validate($care_data);
            if ($res) {
                $res = json_decode($res, true);
                if ($res['respCode'] == '0000') {
                    card_validate_log($care_data, array('status' => 1, 'msg' => $res['respMsg']), $name, $mid, $alley);
                    $rel = array('status' => 1, 'msg' => '验证通过');
                } else {
                    card_validate_log($care_data, array('status' => 0, 'msg' => $res['respMsg']), $name, $mid, $alley);
                    switch ($res['respCode']) {
                        case 1001:
                            $res['respMsg'] = '结算信息鉴权不通过';
                            break;
                    }
                    $rel = array('status' => 0, 'msg' => $res['respMsg']);
                }
            } else {
                $rel = array('status' => 0, 'msg' => '鉴权接口通信失败!请联系管理员!');
            }
        }
    }
    return $rel;
}


#银行卡鉴权
function card_validate($data){
    $host = "http://verifycard.market.alicloudapi.com/Verification4";
    $AppCode = C('CARD_AUTH_APP_CODE');
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $AppCode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $url = $host;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl, CURLOPT_HEADER, true);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, datato($data));
    $res = curl_exec($curl);
    return $res;
}

#鉴权日志
function card_validate_log($data,$res,$source,$id,$alley){
    if($id){
        $mid=$id;
    }else{
        $mid=$_SESSION['mch']['id'];
    }

    if(MODULE_NAME=='System'){
        $op_id=$_SESSION['system']['id'];
        $system=1;
    }elseif(MODULE_NAME=='Admin'){
        $op_id=$_SESSION['user']['id'];
        $system=0;
    }else{
        $op_id='999999';
        $system=0;
    }
    if($alley){
        $alleys=$alley;
    }else{
        $alleys='mch_add_card';
    }

    #根据CID取授权信息
    $arr=array(
        'alleys'=>$alleys,
        'mid'=>$mid,
        'card'=>$data['cardNo'],
        'cert'=>$data['certNo'],
        'name'=>$data['name'],
        'phone'=>$data['phone'],
        'op_id'=> $op_id,
        'sys'=>$system,
        'time'=>date('Y-m-d H:i:s'),
        'domain_auth'=>domain_auth(),
        'status'=>$res['status'],
        'msg'=>$res['msg'],
        'source'=>$source,
        'data'=>json_encode($data,JSON_UNESCAPED_UNICODE),
        'rel'=> json_encode($res,JSON_UNESCAPED_UNICODE),
    );
    M('CardValidateLog')->add($arr);
}

//数组拼接函数
function datato($data){
    $signPars = "";
    ksort($data);
    foreach($data as $k => $v) { //拼接
        if("" != $v) {
            $outdata .= $k . "=" . $v . "&";
        }
    }
    $signPars .=substr($outdata,0,strlen($outdata)-1); //去除最后&
    return $signPars;
}

#获取通道的费率计算规则
function AlleyFrType($alleys,$type)
{
    $res = M('MchAlleys')->where(array('type' => $alleys))->getField('fr_type');
    if($type==1){
        if($res==1){
            return '单笔计算';
        }else{
            return  '金额计算';
        }
    }else {
        return $res;
    }
}

/**
 * 获取广告数据 如多条随机一条
 * @param string $type
 * @return array
 */
function ad_time_id($type='pay_success'){
    $db=M('systemAd');
    $map=[
        'status'=>1,
        'domain_auth'=>domain_auth(),
        'type'=>$type
    ];
    $list=$db->where($map)->select();
    $listId=[];
    foreach ($list as $k=>$v){
        $isTime=ad_time($v['start_time'],$v['end_time']);
        if($isTime){
            $listId[]=$v['id'];
        }
    }
    //随机取一条
    $rand_keys = array_rand($listId);
    $id=$listId[$rand_keys];
    $res=$db->where(['id'=>$id])->find();
    if($res){
        return ['status'=>1,'msg'=>'数据查询成功','data'=>$res];
    }else{
        return ['status'=>0,'msg'=>'暂无数据','data'=>$res];
    }
}

/**
 *
 * @param $start
 * @param $end
 */
function ad_time($startTime,$endTime){
    $start = strtotime($startTime);
    $end = strtotime($endTime); //当前时间 
    $now = time();
    if($now >=$start && $now<=$end){
        return true;
    }else{
        return false;
    }
}

#广告数据
function ad_data($type){
    $user_agent=USER_AGENT();
    if(domain_auth()=='zUG7DegfCx') { #讯码付
        switch ($type) {
            case 'order_result': #订单返回页面
                $status = 1;
                $url = 'https://www.baidu.com/';
                $img = 'http://file.xunmafu.com/Upload/attachment/2017-10-23/B896SF7KJLTEIEPEMRX0.png';
                break;
            case 'mch_login': #商户登录页面
                $status = 1;
                $url = 'https://www.baidu.com/';
                $img = 'http://file.xunmafu.com/Upload/attachment/2017-10-23/B896SF7KJLTEIEPEMRX0.png';
                break;
            default:
                $status = 1;
                $url = 'https://www.baidu.com/';
                $img = 'http://file.xunmafu.com/Upload/attachment/2017-10-23/B896SF7KJLTEIEPEMRX0.png';
                break;
        }
    }else{
        $status = 0;
        $url = 'https://www.baidu.com/';
        $img = 'http://file.xunmafu.com/Upload/attachment/2017-10-23/B896SF7KJLTEIEPEMRX0.png';
    }
    if($user_agent=='ali'){
        $rand_url=array('https://qr.alipay.com/c1x00572tk4rmd3ay2aev15');
        $rand_url=rand_one($rand_url);
        return array('status' => $status, 'url' => $rand_url, 'img' => 'http://file.xunmafu.com/Upload/attachment/2017-12-14/RT5B3HWOMTJJ1FXUENL7.png');
    }else {
        return array('status' => 0, 'url' => $url, 'img' => $img);
    }
}

#是否开启代理邀请码
function sys_agent_status(){
    $db=M('SystemConfig');
    $map['domain_auth']=domain_auth();
    $res=$db->where($map)->getField('agent_yq');
    return $res;
}
#代理链接二维码
function ag_ad_qr($id,$type){
    $url='http://www.xunmafu.com/Plugs/Qr/code/data/';
    if($type==1){
        $data=$url.Xencode(ag_ad_data($id,1));
    }else{
        $data=$url.Xencode(ag_ad_data($id,2));
    }
    return $data;
}
#根据代理链接提取
function ag_ad_data($id,$type){
    $map['domain_auth']=domain_auth();
    $map['id']=$id;
    $res=M('MchAgent')->where($map)->find();
    if($type==1){
       if($res['ad1']){
           $data=$res['ad1'];
       }else{
           $data=sys_ad_data('xyk');
       }
    }else{
        if($res['ad2']){
            $data=$res['ad2'];
        }else{
            $data=sys_ad_data('dk');
        }
    }
    return $data;

}

#获取AD信息
function sys_ad_data($type){
    $db=M('SystemConfig');
    $map['domain_auth']=domain_auth();
    $ad_data=$db->where($map)->find();
    $ad=unserialize($ad_data['ad_data']);
    if($ad) {
        switch ($type) {
            case 'xyk':
                $data = $ad['ad_xyk'];
                break;
            case 'dk':
                $data = $ad['ad_dk'];
                break;
            default:
                $data = $ad['ad_status'];
                break;
        }
    }else{
        $data=0;
    }
    return $data;
}


#获取已用鉴权次数
function auth_card_count($do){
    $res=M('CardValidateLog')->where(array('domain_auth'=>$do))->count();
    return $res;
}

#根据通道 ID 取费率
function AlleysGetRate($type,$id,$field='rate'){
    $map['cid']=$id;
    $map['alleys_type']=$type;
    $alley=M('MchSellerAlleys')->where($map)->getField($field);
    return $alley;
}
#根据快捷通道 ID 取费率
function CardAlleysGetRate($type,$id){
    $map['cid']=$id;
    $map['alleys_type']=$type;
    $alley=M('MchSellerCardAlleys')->where($map)->getField('rate');
    return $alley;
}

#根据通道选择对应数据
function AlleysGetData($type,$name){
    $res=M('MchAlleys')->where(array('type'=>$type))->getField($name);
    return $res;
}
#手机号隐藏
function tel_replace($data){
    return substr_replace($data,'*****',3,5);
}

#身份证号隐藏
function card_id_replace($data){
    return substr_replace($data,'**********',5,10);
}

#截取卡号后四位
function card_replace($data){
    return substr($data, -4);
}

#前海亿联 快捷 银行编码
function Qcard_bank_bm($id){
    $res=M('MchBankList')->where(array('bnkcd'=>$id))->getField('qcard_area');
    return $res;
}
#前海亿联支付时间
function Qcard_JSpay_time()
{
    $checkDayStr = date('Y-m-d ',time());
    $timeBegin1 = strtotime($checkDayStr."09:00".":00");
    $timeEnd1 = strtotime($checkDayStr."22:15".":00");
    $curr_time = time();
    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
    {
        return 1;
    }else {
        return 0;
    }
}

#前海亿联支付时间
function Qcard_pay_time()
{
    $checkDayStr = date('Y-m-d ',time());
    $timeBegin1 = strtotime($checkDayStr."09:00".":00");
    $timeEnd1 = strtotime($checkDayStr."22:10".":00");
    $curr_time = time();
    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
    {
        return true;
    }else {
        return false;
    }
}

#前海亿联提现时间
function Qcard_tx_time()
{
    $checkDayStr = date('Y-m-d ',time());
    $timeBegin1 = strtotime($checkDayStr."09:00".":00");
    $timeEnd1 = strtotime($checkDayStr."22:20".":00");
    $curr_time = time();
    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
    {
        return true;
    }else {
        return false;
    }
}

#前海亿联
function card_curl_post($url, $data)
{
    $ch = curl_init();
    $timeout = 300;
    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        "content-type: application/x-www-form-urlencoded;charset=UTF-8"
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, "https://payment.chinacardpos.com/mpos/api/transMsg/transDirConsume");   //构造来路
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $handles = curl_exec($ch);
    curl_close($ch);
    return $handles;
}

#富友提现时间
function fy_tx_time()
{
    $checkDayStr = date('Y-m-d ',time());
    $timeBegin1 = strtotime($checkDayStr."09:00".":00");
    $timeEnd1 = strtotime($checkDayStr."22:00".":00");
    $curr_time = time();
    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
    {
        return true;
    }else {
        return false;
    }
}

#富友图片地址
function _img_data_url($img_file)
{
    if($img_file) {
        if (preg_match('/(http:\/\/)|(https:\/\/)/i', $img_file)) {
            #网络图片
            $img_url = $img_file;
        } else {
            #本地图片
            $img_data = ltrim($img_file, '.');
            #取品牌商的网址
            $domain = M('DomainAuth')->where(array('web_authcode' => domain_auth()))->getField('main_domain');
            $img_url = 'http://' . $domain . '/' . $img_data;
        }
    }else{
        $img_url='';
    }

    return $img_url; //返回图片地址
}

#富友城市代码
function fy_area($type,$name,$pid){
    switch ($type){
        case 1;
            $area_id = M('FyCityData')->where(array('p_name' => array('like', '%' . $name . '%')))->getField('pid');
            break;
        case 2:
            $area_id = M('FyCityData')->where(array('c_name' => array('like', '%' . $name . '%'),'p_name'=>array('like', '%' . $pid . '%')))->getField('cid');
            break;
        case 3:
            $area_id = M('FyCityData')->where(array('b_name' => array('like', '%' . $name . '%'),'c_name'=>array('like', '%' . $pid . '%')))->getField('bid');
            break;
        default:
            $area_id ='';
            break;
    }
    return $area_id;
}

#生成UUID
function create_uuid($prefix = ""){    //可以指定前缀
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,8);
    $uuid .= substr($str,8,4);
    $uuid .= substr($str,12,4);
    $uuid .= substr($str,16,4);
    $uuid .= substr($str,20,12);
    return $prefix . $uuid;
}

#民生 获取集团商户号
function fy_parent($t){
    if($t=='test'){
        return '910000207000001';
    }else if($t=='email'){
        $res = M('Domain_auth')->where(array('web_authcode' => domain_auth()))->getField('web_domain');
        if ($res) {
            return RandStr(10,1).'@'.$res;
        } else {
            return false;
        }
    }else {
        $res = M('Domain_auth')->where(array('web_authcode' => domain_auth()))->getField('fy_mch');
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }
}


#民生 获取集团商户号
function ccb_parent(){
    $res= M('Domain_auth')->where(array('web_authcode'=>domain_auth()))->getField('ccb_mch');
    if($res){
        return $res;
    }else{
        return false;
    }
}

#民生 获取集团 APPID
function ccb_appid(){
    $res= M('Domain_auth')->where(array('web_authcode'=>domain_auth()))->getField('ccb_appid');
    if($res){
        return $res;
    }else{
        return false;
    }
}

#前方好进获取业务员
function qf_sale(){
    $res= M('Domain_auth')->where(array('web_authcode'=>domain_auth()))->getField('qf_sale');
    if($res){
        return $res;
    }else{
        return false;
    }
}

#民生商户照片生成压缩包
function create_zip($files = array(),$destination = '',$overwrite = false) {
    if(file_exists($destination) && !$overwrite) { return false; }
    $valid_files = array();
    if(is_array($files)) {
        foreach($files as $file) {
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    if(count($valid_files)) {
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        foreach($valid_files as $file) {
            $file_name = date('YmdHis') . rand('11111', '99999') . '.jpg';
            $zip->addFile($file, $file_name);
        }
        $zip->close();
        $path='/home/wwwroot/xun/domain/xunmafu.com/web/Upload/mch_zip/'.$destination;
        copy($destination,'/home/wwwroot/xun/domain/xunmafu.com/web/Upload/mch_zip/'.$destination); //拷贝到新目录
        unlink($destination);
        return file_exists($path);
    }
    else
    {
        return false;
    }
}



#保存图片到本地临时文件夹
function _getImage($url,$save_dir='',$filename='',$type=0){
    if(trim($url)==''){
        return array('file_name'=>'','save_path'=>'','error'=>1,'msg'=>'图片地址不能为空');
    }
    if(trim($save_dir)==''){
        $save_dir='./';
    }
    /*if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }*/
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return array('file_name'=>'','save_path'=>'','error'=>5,'msg'=>'目录错误'.$save_dir);
    }
    $ch=curl_init();
    $timeout=5;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    //curl_setopt($hander,CURLOPT_TIMEOUT,600);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    $img=curl_exec($ch);
    //获取资源大小
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    //获取文件类型
    $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    //判断类型
    $ext = '';
    switch ($type){
        case 'application/x-jpg':
        case 'image/jpeg':
            $ext = 'jpg';
            break;
        case 'image/png':
            $ext = 'png';
            break;
        case 'image/gif':
            $ext = 'gif';
            break;
        default:
            return array('file_name'=>'','save_path'=>'','error'=>3,'msg'=>'提取资源失败, 资源文件类型错误.仅支持图片提取');
            break;
    }
    $max_size = 5000*1000;//系统最大允许大小
    if ($size >= $max_size) {
        return array('file_name'=>'','save_path'=>'','error'=>4,'msg'=>'上传文件大小超过限制');
        //$msgs="上传文件大小超过限制";
    }
    $filename='Net'.uniqid().".".$ext;//文件名
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$img);
    fclose($fp2);
    unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>ltrim($save_dir,'.').$filename,'error'=>0,'size'=>$size,'type'=>$ext,'msg'=>'网络图片保存本地成功');
}

#民生银行专用
function ccb_timestamp(){
    list($t1, $t2) = explode(' ', microtime());
    $millionTime = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    $timestamp = (string)($millionTime + 1*60*1000);
    return $timestamp;
}

#民生进件curl
#url地址  数据(数组)  是否启用https
function ccb_curl_calls($curl, $data, $https = true)
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
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);

    //rwlog('ccb_mch_in',$data);
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


function ccb_curl_calls_file($curl, $data, $file, $https = true)
{

    $data=array(
      'file'=>new CURLFile(realpath($file)),//curl_file_create(basename($file),'application/zip',$file)//new CURLFile(realpath($file)),
    );
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
    $httpHeaders = array(
        'Content-type'=>'multipart/form-data',
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


#民生城市代码
function ccb_area($name,$pid){
    if($pid) {
        $_pid = M('CcbCityData')->where(array('name' => array('like', '%' . $pid . '%')))->getField('mid');
        $area_id = M('CcbCityData')->where(array('pid' => $_pid, 'name' => array('like', '%' . $name . '%')))->getField('mid');
    }else{
        $area_id = M('CcbCityData')->where(array('name' => array('like', '%' . $name . '%')))->getField('mid');
    }
    return $area_id;
}


#获取素材二维码地址
function sc_codes($codes){
        Vendor('Codesdown');
        $code = new  \Codesdown();
        #先判断收款码是否存在
        $res = M('MchCodes')->where(array('codes' => $codes))->count();
        if ($res) {
            if(!file_exists('./Source/QrBg/'.domain_auth().'.png')) {
                return '/Upload/Code/'.$codes.'.png';
            }else {
                $imgs = explode(',', $codes);
                return ltrim($code->downsucai($imgs, '', $codes), '.');
            }
        } else {
            return false;
        }
}

#收款码是否被认证
function code_auth($code){
    $res=M('MchUserAuth')->where(array('codes'=>$code,'status'=>1))->count();
    if($res){
        return '<span style="color: #097cff">已认证</span>';
    }else{
        return '未认证';
    }
}
#支付类型颜色
function pay_type_color($type){
	$key=explode('_',$type);
    switch ($key[0]) {
        case 'wx':
            $color = "#10b423";
            break;
        case 'ali':
            $color = "#0480cf";
            break;
		case 'wx_face':
			$color = "#09bc4a";
			break;
		case 'ali_face':
			$color = "#088ee3";
			break;
        default:
            $color = "#8bc34a";
            break;
    }
    return $color;
}

#获取通道颜色
function alleys_color($name){
    $res=M('MchAlleys')->where(array('type'=>$name))->getField('color');
    if($res){
        return '#'.$res;
    }else{
        return '#ef5350';
    }
}
#获取省市区编码  盛付通
function sft_area($name){
    $area_id=M('AreaSftData')->where(array('area_name'=>array('like','%'.$name.'%')))->getField('area_id');
    return $area_id;
}


#获取客户端入口类型
function USER_AGENT()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
        return 'ali';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return 'wx';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false) {
        return 'qq';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Baidu') !== false) {
        return 'baidu';
    } else {
        return false;
    }
}

#每天0点到早6点维护  微联宝
function get_curr_time_section_wh()
{
    $checkDayStr = date('Y-m-d ',time());
    $timeBegin1 = strtotime($checkDayStr."22:30".":00");
    $timeEnd1 = strtotime($checkDayStr."00:30".":00");

    $curr_time = time();

    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
    {
        return 0;
    }

    return -1;
}


#每天0点到早6点维护  微联宝
function get_curr_time_section()
{
    $checkDayStr = date('Y-m-d ',time());
    $timeBegin1 = strtotime($checkDayStr."22:15".":00");
    $timeEnd1 = strtotime($checkDayStr."23:59".":59");

    $curr_time = time();

    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
    {
        return 0;
    }
    return -1;
}

#限制时间 周一至周五 14点至24点
function Check_time_w(){
    $date = date('w');
    if ($date == 6 ||$date == 0) {
        #如果是星期六或星期天 返回false
        return false;
    }else {
        $checkDayStr = date('Y-m-d ',time());
        $timeBegin1 = strtotime($checkDayStr."14:01".":00");
        $timeEnd1 = strtotime($checkDayStr."23:59".":00");
        $curr_time = time();
        if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
        {
            return true;
        }else {
            return false;
        }
    }
}
#获取操作者姓名
function sys_op_id($id,$type){
    if($type==1){
        return '总系统-'.$id;
    }else{
        $name=M('Users')->where(array('id'=>$id))->getField('name');
        return $name;

    }
}
#去除空格
function TriMall($str)
{
    $qian = array(" ", "　", "\t", "\n", "\r");
    $hou = array("", "", "", "", "");
    return str_replace($qian, $hou, $str);
}

#单位 千逗号分割  保留小数点2位
function Rand_total($total)
{
    return number_format($total, 2);
}

#根据商户ID获取代理ID
function GetMchAid($id)
{
    $aid = M('MchSeller')->where(array('id' => $id))->getField('agent_id');
    return $aid;
}

#生成唯一订单号
function rand_out_trade_no()
{
	$order=date('ymd').(date('y') + date('d')).sprintf('%03d', rand(0, 999)).str_pad((time() - strtotime(date('Y-m-d'))), 3, 0, STR_PAD_LEFT) . substr(microtime(), 2, 5);
    if(S($order)){
    	return rand_out_trade_no();
	}
	S($order,1,60);
	return $order;
}

#商户通道详情
function alleys_data($type, $id)
{
    //dump($type.'-'.$id);
    $where['cid'] = $id;
    $where['alleys'] = $type;
    $rel = M('mch_seller_alleys')->where($where)->field('mch_id,status,loading,rate')->select();
    return $rel;
}

#根据通道标识 获取通道名称
function alleys_name($data)
{
    $res = M('MchAlleys')->where(array('type' => $data))->getField('name');
    if ($res) {
        return $res;
    } else {
        return '未配置通道';
    }
}

#获取品牌LOGO
function GetPlogo()
{
    $data = M('DomainAuth')->where(array('web_domain' => domain_rel()))->getField('brand_logo');
    if ($data) {
        $logo = $data;
    } else {
        $logo = '/Source/statics/img/profile-photos/default.png';
    }
    return $logo;
}

#获取品牌Ico
function GetPico()
{
    $data = M('DomainAuth')->where(array('web_domain' => domain_rel()))->getField('brand_ico');
    if ($data) {
        $ico = $data;
    } else {
        $ico = '/favicon.ico';
    }
    return $ico;
}

//金额单位转换
function show_total($total)
{
    $b = 1000;//千
    $c = 10000;//万
    $hits = $total;
    if ($hits > $b) {
        if ($hits < $c) {
            return round($hits / $b) . 'K';
        } else {
            return (floor(($hits / $c) * 10) / 10) . 'W';
        }
    } else {
        return $hits . '元';
    }
}

//金额单位转换  汉子
function show_total_han($total)
{
    $b = 1000;//千
    $c = 10000;//万
    $hits = $total;
    if ($hits > $b) {
        if ($hits < $c) {
            return round($hits / $b) . '千';
        } else {
            return (floor(($hits / $c) * 10) / 10) . '万';
        }
    } else {
        return $hits . '元';
    }
}

//结算金额费率计算
function accounts_rate($data)
{
    //(终端价-成本价)*金额/1000
    $total = (($data['term'] - $data['cost']) * $data['fee']) / 1000;
    return floor($total * 100) / 100;
}

#分润 保留小数点后两位
function money_data($data){
    return floor($data * 100) / 100;
}


//支付类型
function ApiCodes($val)
{
    switch ($val) {
        case 'pay.weixin.jspay':
            $name = '微信-公众账号(兴业2新)';
            break;
        case 'pay.weixin.native':
            $name = '微信-线下扫码(兴业2新)';
            break;
        case 'pay.weixin.micropay':
            $name = '微信-线下小额(兴业2新)';
            break;
        case 'pay.alipay.jspayv3':
            $name = '支付宝-JS支付(兴业总行2)';
            break;
        case 'pay.alipay.nativev3':
            $name = '支付宝-扫码支付(兴业总行2)';
            break;
        case 'pay.alipay.micropayv3':
            $name = '支付宝-小额支付(兴业总行2)';
            break;
        default:
            $name = '未知类型';
            break;
    }
    return $name;
}

//行业类型
function Industrid($val)
{
    $res = M('mch_industry')->where(array('pid' => $val))->getField('name');
    if ($res) {
        $rel = $res;
    } else {
        $rel = '未找到此行业类型';
    }
    return $rel;
}

//商户类型
function MchDealType($val)
{
    switch ($val) {
        case 1:
            $name = '实体';
            break;
        case 2:
            $name = '虚拟';
            break;
        default:
            $name = '未知商户类型';
            break;
    }
    return $name;

}

//商户照片类型
function Mch_Pic_Types($type)
{
    switch ($type) {
        case 1:
            $name = '身份证信息';
            break;
        case 2:
            $name = '营业执照信息';
            break;
        case 3:
            $name = '组织机构证件照信息';
            break;
        case 4:
            $name = '商户协议照信息';
            break;
        default:
            $name = '未知商户图片类型';
            break;
    }
    return $name;
}

/*支付状态*/
function pays_status($val)
{
    switch ($val) {
        case 0:
            $name = "未支付";
            break;
        case 1:
            $name = "成功";
            break;
        case 2:
            $name = "已退款";
            break;
        case 444:
            $name = "已关单";
            break;
        default:
            $name = "未知状态";
            break;
    }
    return $name;
}

function pays_types($val,$type=false){
	$exp=explode('_',$val);
	$key=$type?$exp[1]:$exp[0];
	switch ($key) {
		case 'wx':
			$name = "微&nbsp;&nbsp;&nbsp;信";
			break;
		case 'ali':
			$name = "支付宝";
			break;
		case 'scan':
			$name = "条码/付款码";
			break;
		case 'jsapi':
			$name = "公众号/服务窗";
			break;
		case 'face':
			$name = "刷脸";
			break;
		case 'code':
			$name = "被扫/固定二维码";
			break;
		default:
			$name = "未知类型";
			break;
	}
	return $name;
}

/*支付类型*/
function pays_type($val, $type)
{
    if ($type == 1) {
        switch ($val) {
            case 'wx_jsapi':
                $name = "微信";
                break;
            case 'ali_jsapi':
                $name = "支付宝";
                break;
            case 'ali_scan':
                $name = "支付宝条码";
                break;
            case 'wx_scan':
                $name = "微信条码";
                break;
			case 'wx_face':
				$name = "微信刷脸";
				break;
			case 'ali_face':
				$name = "支付宝刷脸";
				break;
            case 'ali_code':
                $name = "支付宝被扫";
                break;
            case 'wx_code':
                $name = "微信被扫";
                break;
            case 'card_api':
                $name = "无卡快捷";
                break;
            case 'repay_hk':
                $name = "信用卡还款";
                break;
            case 'repay_xf':
                $name = "信用卡消费";
                break;
            default:
                $name = "未知类型";
                break;
        }
    } else {
        switch ($val) {
            case 'wx_jsapi':
                $name = "微&nbsp;&nbsp;&nbsp;信";
                break;
            case 'ali_jsapi':
                $name = "支付宝";
                break;
            case 'ali_scan':
                $name = "支付宝条码";
                break;
            case 'wx_scan':
                $name = "微信条码";
                break;
			case 'wx_face':
				$name = "微信刷脸";
				break;
			case 'ali_face':
				$name = "支付宝刷脸";
				break;
            case 'ali_code':
                $name = "支付宝被扫";
                break;
            case 'wx_code':
                $name = "微信被扫";
                break;
            case 'card_api':
                $name = "无卡快捷";
                break;
            case 'repay_hk':
                $name = "卡还款";
                break;
            case 'repay_xf':
                $name = "卡消费";
                break;
            default:
                $name = "未知类型";
                break;
        }
    }
    return $name;
}

/*生成日志*/
function rwlog($name, $data)
{
    file_put_contents($name . '.txt', var_export($data, TRUE), FILE_APPEND);
}

#数据库日志
function data_log($type, $data)
{
    $arr = array(
        'type' => $type,
        'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
        'ctime' => date('Y-m-d H:i:s'),
    );
    M('PayLog')->add($arr);
}

#根据代理ID取费率
function rate_data_pid($id, $type, $set)
{
    $where['id'] = $id;
    //$where['domain_auth']=domain_auth();
    $res = M('MchAgent')->where($where)->getField('rate');
    $rate = unserialize($res);
    if ($set == 1) {
        $data = $rate[$type . '_cost'];
        if ($data) {
            $info = $data;
        } else {
            $info = '未配置';
        }
        return $info;

    } elseif ($set == 2) {
        return rate_datas($rate[$type . '_term']);
    } elseif ($type == 'Sftpays') {
        return rate_data_sft($rate[$type . '_cost']);
    } else {
        return rate_datas($rate[$type . '_cost']);
    }
}

#费率档案
function rate_data($type)
{
    #先取当前通道的低价
    $where['web_authcode'] = domain_auth();
    $cid = M('domain_auth')->where($where)->getField('id');
    $map['cid'] = $cid;
    $map['alleys_type'] = $type;
    $rate = M('domain_alleys')->where($map)->getField('rate');
    return rate_datas($rate);
}

#费率档筛选
function rate_data_sft($rate)
{
    $where['rate'] = array('egt', $rate);
    $where['sft_rate'] = array('EXP','IS NOT NULL');
    $where['status'] = 1;
    $res = M('RateData')->where($where)->order('rate asc')->field('rate,name')->select();
    return $res;
}

#费率档筛选
function rate_datas($rate)
{
    $where['rate'] = array('egt', $rate);
    $where['status'] = 1;
    $res = M('RateData')->where($where)->order('rate asc')->field('rate,name')->select();
    return $res;
}

#微信Token生成
function wx_token()
{
    $str = "abcdefghgkmnopqrstuvwABCDEFGHJKLMNOPQRSTUVWXYZ" . date('YmdHis');
    $key = "";
    for ($i = 0; $i < 15; $i++) {
        $_token .= $str{mt_rand(0, 32)};    //生成php随机数
    }
    return $_token;
}

#微信KEY生成
function wx_encodingaeskey()
{
    $str = "abcdefghgkmnopqrstuvw012356789ABCDEFGHJKLMNOPQRSTUVWXYZ";
    $key = "";
    for ($i = 0; $i < 43; $i++) {
        $_key .= $str{mt_rand(0, 32)};    //生成php随机数
    }
    return $_key;

}

/**
 * 获取域名 包括协议
 * @param bool $type 完整链接
 * @return string
 */
function getDomainUrl($type=false){
	$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
	return $http_type . $_SERVER['HTTP_HOST'].($type?$_SERVER['REQUEST_URI']:'');
}

#根据商户ID取商户信息
function Get_Seller($id)
{
    $res = M('MchSeller')->where(array('id' => $id))->find();
    return $res;
}

#根据门店ID取门店名称
function getStoreName($id)
{
	$res = M('MchStore')->where(array('id' => $id))->getField('name');
	return $res;
}

#根据门店ID取门店信息
function Get_Store($id)
{
    $res = M('MchStore')->where(array('id' => $id))->find();
    return $res;
}

#根据商户获取代理信息
function Get_Agent($id)
{
    $aid = M('MchSeller')->where(array('id' => $id))->getField('agent_id');
    $agent = M('MchAgent')->where(array('id' => $aid))->find();
    return $agent;

}

#根据门店Id获取收款码ID
function GetStoreCode($id)
{
    $res = M('MchCodes')->where(array('store_id' => $id, 'domain_auth' => domain_auth()))->getField('codes');
    return $res;
}

#根据门店Id获取收款码ID 总
function GetStoreCodeNot($id)
{
    $res = M('MchCodes')->where(array('store_id' => $id))->getField('codes');
    return $res;
}

#获取收款码ID获取路径 总
function GetStoreCodePathNot($id)
{
    $res = M('MchCodes')->where(array('store_id' => $id))->getField('code_url');
    return $res;
}

#获取配置
function GetPayConfig($domain, $arr)
{
    $res = M('MchPayConfig')->where(array('domain_auth' => $domain))->getField($arr);
    return $res;
}

#获取配置
function GetPayConfigs($arr)
{
    $res = M('MchPayConfig')->where(array('domain_auth' => domain_auth()))->getField($arr);
    return $res;
}

#获取收款码ID获取路径
function GetStoreCodePath($id)
{
    $res = M('MchCodes')->where(array('store_id' => $id, 'domain_auth' => domain_auth()))->getField('code_url');
    return $res;
}

#获取收款码ID信息
function GetCodeData($code, $arr)
{
    $res = M('MchCodes')->where(array('codes' => $code, 'domain_auth' => domain_auth()))->getField($arr);
    return $res;
}

function domain_auth_code($len=10)
{
    $str = "abcdefghgkmnopqrstuvw012356789ABCDEFGHJKLMNOPQRSTUVWXYZ";
	$out_str='';
    for ($i = 0; $i < $len; $i++) {
		$out_str .= $str{mt_rand(0, 32)};    //生成php随机数
    }
    return $out_str;
}

function ChannelId()
{
    return RandStr(12, 0);
}


function ChannelKey()
{
    return RandStr(32, 1);
}

/*随机生成数字*/
function RandStr($len, $type)
{
    if ($type == 1) {
        $str = "012356789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = "";
        for ($i = 0; $i < $len; $i++) {
            $outputstr .= $str{mt_rand(0, 32)};    //生成php随机数
        }
    } else {
        $chars_array = array("0", "1", "2", "3", "5", "6", "7", "8", "9");
        $charsLen = count($chars_array) - 1;
        $outputstr = "";
        for ($i = 0; $i < $len; $i++) {
            $outputstr .= $chars_array[rand(0, $charsLen)];
        }
    }
    return $outputstr;
}

/*账户类型*/
function reload_cardstype($id)
{
    if ($id == 1) {
        $name = "企业账户";
    } else {
        $name = "个人账户";
    }
    return $name;
}

/*证件类型*/
function reload_stype($id)
{
    if ($id == 1) {
        $name = "身份证";
    } else {
        $name = "护照";
    }
    return $name;
}

/*获取分行*/
function reload_banks($id)
{
    $res = M('BanksDataNew')->where(array('banking' => $id))->find();
    $data = $res['address'];
    return $data;
}

/*获取银行行号*/
function reload_bank_number($id)
{
    $res = M('mch_bank_list')->where(array('bnkcd' => $id))->getField('number');
    return $res;
}

/*获取银行*/
function reload_bank($id)
{
    $res = M('mch_bank_list')->where(array('bnkcd' => $id))->getField('bnknm');
    return $res;
}

/*获取银行编码*/
function reload_bank_area($id)
{
    $res = M('mch_bank_list')->where(array('bnkcd' => $id))->getField('area');
    return $res;
}

/*获取省市*/
function reload_area($id)
{
    $areaName = M('mch_areas')->where(array('areaId' => $id))->getField('areaName');
    return $areaName;
}

/*获取商户名*/
function mch_name($val)
{
    $mch_name = M('mch_merchant')->where(array('merchantId' => $val))->getField('merchantName');
    return $mch_name;
}

/*获取渠道名称*/
function mcha_name($val)
{
    $mcha_api = M('mch_parent_api')->where(array('placenum' => $val))->getField('parentid');
    //根据ID取信息
    $mcha_basic = M('mch_basic')->where(array('id' => $mcha_api))->getField('parentname');
    return $mcha_basic;
}

/*保留小数点后4位*/
function spr_int($data)
{
    return substr(sprintf("%.5f", $data), 0, -1);
}

#取图片大小
function img_size($img)
{
    if (preg_match('/(http:\/\/)|(https:\/\/)/i', $img)) {
        return ceil(filesize($img) / 1000) . "k";
    } else {
        return ceil(filesize('./' . $img) / 1000) . "k";
    }
}

/*UEditor 方法
 * $id  数据name和ID
 * $content 内容
 * $style 样式
 * $load  单页面多个编辑器使用  多个ID 逗号区分
 * */
function LoadUEditor($id,$content,$style,$load=false){
    $data ='';
    $data.='<script type="text/plain" id="'.$id.'" name="'.$id.'" style="'.$style.'">'.$content.'</script>';
    if($load){ #加载配置,配置根据$load开启
        $load=explode(',',$load);
        $data.='<script type="text/javascript">';
        $data.='var upload_mod="'.MODULE_NAME.'",upload_type="UEdit",UploadServer="";';
        $data.='UeditConfig('.json_encode($load).');';
        $data.='upload_model_html();';
        $data.='</script>';

    }
    echo $data;
}


function loadImage($type,$id){
    $data ='';
    if($type=='WeiXin'){
        $data.='<script type="text/javascript">';
        $data.='var upload_mod="'.MODULE_NAME.'",upload_type="WeiXin",UploadServer="wx";';
        $data.='upload_model_html("WeiXin");';
        $data.='</script>';
    }else{
        $data.='<script type="text/javascript">';
        $data.='var upload_mod="'.MODULE_NAME.'",upload_type="Image",UploadServer="";upload_cid="'.$id.'";';
        $data.='upload_model_html();';
        $data.='</script>';
    }

    echo $data;
}


/* 上传Model结构
 * $type: Input UEdit Image
 * $id:  表单name
 * $value 默认值
 * $required 是否必填项
 * $order  只针对多个UEdit扩展  编辑器实例递增ID
 * Time: 2017年11月29日01:40:51
*/
function uploads_model($type,$id,$value,$required,$order=1){
      #默认必须导入的
      $common='';
      switch ($type){
          case 'Input':
              $html='';
              break;
          case 'UEdit':
              $html='';
              break;
          case 'Image':
              $html='';
              break;
          default:
              $html='';
              break;
      }
}


/**
 * 图片上传快捷类
 * @param $name表单名称
 * @param $value初始数据
 * @param $req是否必填
 * @param string $id 表单ID
 * @return string
 * 2019年07月03日00:34:52 更新
 */
function uploads_map($name, $value, $req='',$id='')
{
    $_req = $req == 1?"required":'';
    $data='';
	$data .='<div class="input-group mar-btm">';
    $data .= '<input placeholder="请上传..." class="form-control" type="text" id="' . ($id?$id:$name) . '" name="' . $name . '"value="' . $value . '" ' . $_req . '>';
    $data .= '<span class="input-group-btn">';
    $data .= '<button class="btn btn-default" type="button" style="border-radius: 0px !important;" onclick="upload_modal(\'' . ($id?$id:$name) . '\')"><i class="fa fa-cloud-upload"></i></button>';
    $data .= '<button class="btn btn-default upload_view" type="button" style="border-radius: 0px !important;" onclick="upload_view(\'' . ($id?$id:$name) . '\')"><i class="fa fa-image"></i></button>';
    $data .= '</span>';
	$data .= '</div>';
    return $data;
}

#框架内使用
function uploads_maps($id, $value, $pload)
{
    if ($pload == 1) {
        $_pload = "required";
    }
    $data='';
    $data .= '<input placeholder="请上传..." class="form-control" type="text" id="' . $id . '" name="' . $id . '"value="' . $value . '" ' . $_pload . '>';
    $data .= '<span class="input-group-btn">';
    $data .= '<button class="btn btn-mint" type="button" style="border-radius: 0px !important;" onclick="upload_modals(\'' . $id . '\')">上传</button>';
    $data .= '<button class="btn btn-danger upload_view" type="button" style="border-radius: 0px !important;" onclick="upload_view(\'' . $id . '\')">预览</button>';
    $data .= '</span>';
    echo $data;
}


//传递数据以易于阅读的样式格式化后输出
function p($data)
{
    // 定义样式
    $str = '<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data = $data ? 'true' : 'false';
    } elseif (is_null($data)) {
        $show_data = 'null';
    } else {
        $show_data = print_r($data, true);
    }
    $str .= $show_data;
    $str .= '</pre>';
    echo $str;
}


/**
 * 返回文件格式
 * @param  string $str 文件名
 * @return string      文件格式
 */
function file_format($str)
{
    // 取文件后缀名
    $str = strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $image = array('webp', 'jpg', 'png', 'ico', 'bmp', 'gif', 'tif', 'pcx', 'tga', 'bmp', 'pxc', 'tiff', 'jpeg', 'exif', 'fpx', 'svg', 'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'hdri');
    // 视频格式
    $video = array('mp4', 'avi', '3gp', 'rmvb', 'gif', 'wmv', 'mkv', 'mpg', 'vob', 'mov', 'flv', 'swf', 'mp3', 'ape', 'wma', 'aac', 'mmf', 'amr', 'm4a', 'm4r', 'ogg', 'wav', 'wavpack');
    // 压缩格式
    $zip = array('rar', 'zip', 'tar', 'cab', 'uue', 'jar', 'iso', 'z', '7-zip', 'ace', 'lzh', 'arj', 'gzip', 'bz2', 'tz');
    // 文档格式
    $text = array('exe', 'doc', 'ppt', 'xls', 'wps', 'txt', 'lrc', 'wfs', 'torrent', 'html', 'htm', 'java', 'js', 'css', 'less', 'php', 'pdf', 'pps', 'host', 'box', 'docx', 'word', 'perfect', 'dot', 'dsf', 'efe', 'ini', 'json', 'lnk', 'log', 'msi', 'ost', 'pcs', 'tmp', 'xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $image):
            return 'image';
            break;
        case in_array($str, $video):
            return 'video';
            break;
        case in_array($str, $zip):
            return 'zip';
            break;
        case in_array($str, $text):
            return 'text';
            break;
        default:
            return 'image';
            break;
    }
}


/**
 * 返回用户id
 * @return integer 用户id
 */
function get_uid()
{
    return $_SESSION['user']['id'];
}

/**
 * 返回iso、Android、ajax的json格式数据
 * @param  array $data 需要发送到前端的数据
 * @param  string $error_message 成功或者错误的提示语
 * @param  integer $error_code 状态码： 0：成功  1：失败
 * @return string                 json格式的数据
 */
function ajax_return($data = '', $error_message = '成功', $error_code = 1)
{
    $all_data = array(
        'error_code' => $error_code,
        'error_message' => $error_message,
    );
    if ($data !== '') {
        $all_data['data'] = $data;
        // app 禁止使用和为了统一字段做的判断
        $reserved_words = array('id', 'title', 'price', 'product_title', 'product_id', 'product_category', 'product_number');
        foreach ($reserved_words as $k => $v) {
            if (array_key_exists($v, $data)) {
                echo 'app不允许使用【' . $v . '】这个键名 —— 此提示是function.php 中的ajax_return函数返回的';
                die;
            }
        }
    }
    // 如果是ajax或者app访问；则返回json数据 pc访问直接p出来
    echo json_encode($all_data);
    exit(0);
}

/**
 * 获取完整网络连接
 * @param  string $path 文件路径
 * @return string       http连接
 */
function get_url()
{

    $url = 'http://' . I("server.HTTP_HOST") . __SELF__;
    return $url;
}


function get_url2() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * 检测是否登录
 * @return boolean 是否登录
 */
function check_login()
{
    if (!empty($_SESSION['user']['id'])) {
        return true;
    } else {
        return false;
    }
}

function system_opid()
{
    return $_SESSION['system']['id'];
}

/**
 * 总管理检测是否登录
 * @return boolean 是否登录
 */
function system_check_login()
{
    if (!empty($_SESSION['system']['id'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * 根据配置项获取对应的key和secret
 * @return array key和secret
 */
function get_rong_key_secret()
{
    // 判断是需要开发环境还是生产环境的key
    if (C('RONG_IS_DEV')) {
        $key = C('RONG_DEV_APP_KEY');
        $secret = C('RONG_DEV_APP_SECRET');
    } else {
        $key = C('RONG_PRO_APP_KEY');
        $secret = C('RONG_PRO_APP_SECRET');
    }
    $data = array(
        'key' => $key,
        'secret' => $secret
    );
    return $data;
}


/**
 * 删除指定的标签和内容
 * @param array $tags 需要删除的标签数组
 * @param string $str 数据源
 * @param string $content 是否删除标签内的内容 0保留内容 1不保留内容
 * @return string
 */
function strip_html_tags($tags, $str, $content = 0)
{
    if ($content) {
        $html = array();
        foreach ($tags as $tag) {
            $html[] = '/(<' . $tag . '.*?>[\s|\S]*?<\/' . $tag . '>)/';
        }
        $data = preg_replace($html, '', $str);
    } else {
        $html = array();
        foreach ($tags as $tag) {
            $html[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/i";
        }
        $data = preg_replace($html, '', $str);
    }
    return $data;
}

/**
 * 传递ueditor生成的内容获取其中图片的路径
 * @param  string $str 含有图片链接的字符串
 * @return array       匹配的图片数组
 */
function get_ueditor_image_path($str)
{
    $preg = '/\/Upload\/image\/u(m)?editor\/\d*\/\d*\.[jpg|jpeg|png|bmp]*/i';
    preg_match_all($preg, $str, $data);
    return current($data);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 * @return string
 */
function re_substr($str, $start = 0, $length, $suffix = true, $charset = "utf-8")
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $omit = mb_strlen($str) >= $length ? '...' : '';
    return $suffix ? $slice . $omit : $slice;
}

// 设置验证码
function show_verify($config = '')
{
    if ($config == '') {
        $config = array(
            'codeSet' => '1234567890',
            'fontSize' => 30,
            'useCurve' => false,
            'imageH' => 60,
            'imageW' => 240,
            'length' => 4,
            'fontttf' => '4.ttf',
        );
    }
    $verify = new \Think\Verify($config);
    return $verify->entry();
}

// 检测验证码
function check_verify($code)
{
    $verify = new \Think\Verify();
    return $verify->check($code);
}

/**
 * 取得根域名
 * @param type $domain 域名
 * @return string 返回根域名
 */
function get_url_to_domain($domain)
{
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
    $array_domain = explode(".", $domain);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}

/**
 * 按符号截取字符串的指定部分
 * @param string $str 需要截取的字符串
 * @param string $sign 需要截取的符号
 * @param int $number 如是正数以0为起点从左向右截  负数则从右向左截
 * @return string 返回截取的内容
 */
/*  示例
    $str='123/456/789';
    cut_str($str,'/',0);  返回 123
    cut_str($str,'/',-1);  返回 789
    cut_str($str,'/',-2);  返回 456
    具体参考 http://www.baijunyao.com/index.php/Home/Index/article/aid/18
*/
function cut_str($str, $sign, $number)
{
    $array = explode($sign, $str);
    $length = count($array);
    if ($number < 0) {
        $new_array = array_reverse($array);
        $abs_number = abs($number);
        if ($abs_number > $length) {
            return 'error';
        } else {
            return $new_array[$abs_number - 1];
        }
    } else {
        if ($number >= $length) {
            return 'error';
        } else {
            return $array[$number];
        }
    }
}

/**
 * 发送邮件
 * @param  string $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return boolean       是否成功
 */
function send_email($address, $subject, $content)
{
    $email_smtp = C('EMAIL_SMTP');
    $email_username = C('EMAIL_USERNAME');
    $email_password = C('EMAIL_PASSWORD');
    $email_from_name = C('EMAIL_FROM_NAME');
    $email_smtp_secure = C('EMAIL_SMTP_SECURE');
    $email_port = C('EMAIL_PORT');
    if (empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)) {
        return array("error" => 1, "message" => "邮箱配置不完整");
    }
    require_once './ThinkPHP/Library/Org/Nx/class.phpmailer.php';
    require_once './ThinkPHP/Library/Org/Nx/class.smtp.php';
    $phpmailer = new \Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置设置smtp_secure
    $phpmailer->SMTPSecure = $email_smtp_secure;
    // 设置port
    $phpmailer->Port = $email_port;
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet = 'UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host = $email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth = true;
    // 设置用户名
    $phpmailer->Username = $email_username;
    // 设置密码
    $phpmailer->Password = $email_password;
    // 设置邮件头的From字段。
    $phpmailer->From = $email_username;
    // 设置发件人名字
    $phpmailer->FromName = $email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if (is_array($address)) {
        foreach ($address as $addressv) {
            $phpmailer->AddAddress($addressv);
        }
    } else {
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject = $subject;
    // 设置邮件正文
    $phpmailer->Body = $content;
    // 发送邮件。
    if (!$phpmailer->Send()) {
        $phpmailererror = $phpmailer->ErrorInfo;
        return array("error" => 1, "message" => $phpmailererror);
    } else {
        return array("error" => 0);
    }
}

/**
 * 获取一定范围内的随机数字
 * 跟rand()函数的区别是 位数不足补零 例如
 * rand(1,9999)可能会得到 465
 * rand_number(1,9999)可能会得到 0465  保证是4位的
 * @param integer $min 最小值
 * @param integer $max 最大值
 * @return string
 */
function rand_number($min = 1, $max = 9999)
{
    return sprintf("%0" . strlen($max) . "d", mt_rand($min, $max));
}

/**
 * 生成一定数量的随机数，并且不重复
 * @param integer $number 数量
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @return string
 */
function build_count_rand($number, $length = 4, $mode = 1)
{
    if ($mode == 1 && $length < strlen($number)) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand = array();
    for ($i = 0; $i < $number; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $unqiue = array_unique($rand);
    if (count($unqiue) == count($rand)) {
        return $rand;
    }
    $count = count($rand) - count($unqiue);
    for ($i = 0; $i < $count * 3; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $rand = array_slice(array_unique($rand), 0, $number);
    return $rand;
}

/**
 * 生成不重复的随机数
 * @param  int $start 需要生成的数字开始范围
 * @param  int $end 结束范围
 * @param  int $length 需要生成的随机数个数
 * @return array       生成的随机数
 */
function get_rand_number($start = 1, $end = 10, $length = 4)
{
    $connt = 0;
    $temp = array();
    while ($connt < $length) {
        $temp[] = rand($start, $end);
        $data = array_unique($temp);
        $connt = count($data);
    }
    sort($data);
    return $data;
}

/**
 * 实例化page类
 * @param  integer $count 总数
 * @param  integer $limit 每页数量
 * @return subject       page类
 */
function new_page($count, $limit = 10)
{
    return new \Org\Nx\Page($count, $limit);
}

/**
 * 获取分页数据
 * @param  subject $model model对象
 * @param  array $map where条件
 * @param  string $order 排序规则
 * @param  integer $limit 每页数量
 * @return array            分页数据
 */
function get_page_data($model, $map, $order = '', $limit = 10)
{
    $count = $model
        ->where($map)
        ->count();
    $page = new_page($count, $limit);
    // 获取分页数据
    $list = $model
        ->where($map)
        ->order($order)
        ->limit($page->firstRow . ',' . $page->listRows)
        ->select();
    $data = array(
        'data' => $list,
        'page' => $page->show()
    );
    return $data;
}

/**
 * 处理post上传的文件；并返回路径
 * @param  string $path 字符串 保存文件路径示例： /Upload/image/
 * @param  string $format 文件格式限制
 * @param  string $maxSize 允许的上传文件最大值 52428800
 * @return array           返回ajax的json格式数据
 */
function post_upload($path = 'file', $format = 'empty', $maxSize = '52428800')
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '/');
    // 添加Upload根目录
    $path = strtolower(substr($path, 0, 6)) === 'upload' ? ucfirst($path) : 'Upload/' . $path;
    // 上传文件类型控制
    $ext_arr = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'pdf')
    );
    if (!empty($_FILES)) {
        // 上传文件配置
        $config = array(
            'maxSize' => $maxSize,       //   上传文件最大为50M
            'rootPath' => './',           //文件上传保存的根路径
            'savePath' => './' . $path . '/',         //文件上传的保存路径（相对于根路径）
            'saveName' => array('uniqid', ''),     //上传文件的保存规则，支持数组和字符串方式定义
            'autoSub' => true,                   //  自动使用子目录保存上传文件 默认为true
            'exts' => isset($ext_arr[$format]) ? $ext_arr[$format] : '',
        );
        // 实例化上传
        $upload = new \Think\Upload($config);
        // 调用上传方法
        $info = $upload->upload();
        $data = array();
        if (!$info) {
            // 返回错误信息
            $error = $upload->getError();
            $data['error_info'] = $error;
            return $data;
        } else {
            // 返回成功信息
            foreach ($info as $file) {
                $data['name'] = trim($file['savepath'] . $file['savename'], '.');
                return $data;
            }
        }
    }
}

/**
 * 上传文件类型控制   此方法仅限ajax上传使用
 * @param  string $path 字符串 保存文件路径示例： /Upload/image/
 * @param  string $format 文件格式限制
 * @param  integer $maxSize 允许的上传文件最大值 52428800
 * @return booler       返回ajax的json格式数据
 */
function upload($path = 'file', $format = 'empty', $maxSize = '52428800')
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '/');
    // 添加Upload根目录
    $path = strtolower(substr($path, 0, 6)) === 'upload' ? ucfirst($path) : 'Upload/' . $path;
    // 上传文件类型控制
    $ext_arr = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'pdf')
    );
    if (!empty($_FILES)) {
        // 上传文件配置
        $config = array(
            'maxSize' => $maxSize,       //   上传文件最大为50M
            'rootPath' => './',           //文件上传保存的根路径
            'savePath' => './' . $path . '/',         //文件上传的保存路径（相对于根路径）
            'saveName' => array('uniqid', ''),     //上传文件的保存规则，支持数组和字符串方式定义
            'autoSub' => true,                   //  自动使用子目录保存上传文件 默认为true
            'exts' => isset($ext_arr[$format]) ? $ext_arr[$format] : '',
        );
        // 实例化上传
        $upload = new \Think\Upload($config);
        // 调用上传方法
        $info = $upload->upload();
        $data = array();
        if (!$info) {
            // 返回错误信息
            $error = $upload->getError();
            $data['error_info'] = $error;
            echo json_encode($data);
        } else {
            // 返回成功信息
            foreach ($info as $file) {
                $data['name'] = trim($file['savepath'] . $file['savename'], '.');
                echo json_encode($data);
            }
        }
    }
}

/**
 * 使用curl获取远程数据
 * @param  string $url url连接
 * @return string      获取到的数据
 */
function curl_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
    // curl_setopt($ch,CURLOPT_HEADER,1);               //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);        //设置 referer
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);          //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}


/**
 * 将路径转换加密
 * @param  string $file_path 路径
 * @return string            转换后的路径
 */
function path_encode($file_path)
{
    return rawurlencode(base64_encode($file_path));
}

/**
 * 将路径解密
 * @param  string $file_path 加密后的字符串
 * @return string            解密后的路径
 */
function path_decode($file_path)
{
    return base64_decode(rawurldecode($file_path));
}

/**
 * 根据文件后缀的不同返回不同的结果
 * @param  string $str 需要判断的文件名或者文件的id
 * @return integer     1:图片  2：视频  3：压缩文件  4：文档  5：其他
 */
function file_category($str)
{
    // 取文件后缀名
    $str = strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $images = array('webp', 'jpg', 'png', 'ico', 'bmp', 'gif', 'tif', 'pcx', 'tga', 'bmp', 'pxc', 'tiff', 'jpeg', 'exif', 'fpx', 'svg', 'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'hdri');
    // 视频格式
    $video = array('mp4', 'avi', '3gp', 'rmvb', 'gif', 'wmv', 'mkv', 'mpg', 'vob', 'mov', 'flv', 'swf', 'mp3', 'ape', 'wma', 'aac', 'mmf', 'amr', 'm4a', 'm4r', 'ogg', 'wav', 'wavpack');
    // 压缩格式
    $zip = array('rar', 'zip', 'tar', 'cab', 'uue', 'jar', 'iso', 'z', '7-zip', 'ace', 'lzh', 'arj', 'gzip', 'bz2', 'tz');
    // 文档格式
    $document = array('exe', 'doc', 'ppt', 'xls', 'wps', 'txt', 'lrc', 'wfs', 'torrent', 'html', 'htm', 'java', 'js', 'css', 'less', 'php', 'pdf', 'pps', 'host', 'box', 'docx', 'word', 'perfect', 'dot', 'dsf', 'efe', 'ini', 'json', 'lnk', 'log', 'msi', 'ost', 'pcs', 'tmp', 'xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $images):
            return 1;
            break;
        case in_array($str, $video):
            return 2;
            break;
        case in_array($str, $zip):
            return 3;
            break;
        case in_array($str, $document):
            return 4;
            break;
        default:
            return 5;
            break;
    }
}

/**
 * 组合缩略图
 * @param  string $file_path 原图path
 * @param  integer $size 比例
 * @return string              缩略图
 */
function get_min_image_path($file_path, $width = 170, $height = 170)
{
    $min_path = str_replace('.', '_' . $width . '_' . $height . '.', trim($file_path, '.'));
    $min_path = OSS_URL . $min_path;
    return $min_path;
}

/**
 * 不区分大小写的in_array()
 * @param  string $str 检测的字符
 * @param  array $array 数组
 * @return boolear       是否in_array
 */
function in_iarray($str, $array)
{
    $str = strtolower($str);
    $array = array_map('strtolower', $array);
    if (in_array($str, $array)) {
        return true;
    }
    return false;
}

/**
 * 传入时间戳,计算距离现在的时间
 * @param  number $time 时间戳
 * @return string     返回多少以前
 */
function word_time($time)
{
    $time = (int)substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2) {
        $str = sprintf('刚刚', $int);
    } elseif ($int < 60) {
        $str = sprintf('%d秒前', $int);
    } elseif ($int < 3600) {
        $str = sprintf('%d分钟前', floor($int / 60));
    } elseif ($int < 86400) {
        $str = sprintf('%d小时前', floor($int / 3600));
    } elseif ($int < 1728000) {
        $str = sprintf('%d天前', floor($int / 86400));
    } else {
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}

/**
 * 生成缩略图
 * @param  string $image_path 原图path
 * @param  integer $width 缩略图的宽
 * @param  integer $height 缩略图的高
 * @return string             缩略图path
 */
function crop_image($image_path, $width = 170, $height = 170)
{
    $image_path = trim($image_path, '.');
    $min_path = '.' . str_replace('.', '_' . $width . '_' . $height . '.', $image_path);
    $image = new \Think\Image();
    $image->open($image_path);
    // 生成一个居中裁剪为$width*$height的缩略图并保存
    $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($min_path);
    oss_upload($min_path);
    return $min_path;
}

/**
 * 上传文件类型控制 此方法仅限ajax上传使用
 * @param  string $path 字符串 保存文件路径示例： /Upload/image/
 * @param  string $format 文件格式限制
 * @param  integer $maxSize 允许的上传文件最大值 52428800
 * @return booler   返回ajax的json格式数据
 */
function ajax_upload($path = 'file', $format = 'empty', $maxSize = '52428800')
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '/');
    // 添加Upload根目录
    $path = strtolower(substr($path, 0, 6)) === 'upload' ? ucfirst($path) : 'Upload/' . $path;
    // 上传文件类型控制
    $ext_arr = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'pdf')
    );
    if (!empty($_FILES)) {
        // 上传文件配置
        $config = array(
            'maxSize' => $maxSize,               // 上传文件最大为50M
            'rootPath' => './',                   // 文件上传保存的根路径
            'savePath' => './' . $path . '/',         // 文件上传的保存路径（相对于根路径）
            'saveName' => array('uniqid', ''),     // 上传文件的保存规则，支持数组和字符串方式定义
            'autoSub' => true,                   // 自动使用子目录保存上传文件 默认为true
            'exts' => isset($ext_arr[$format]) ? $ext_arr[$format] : '',
        );
        // p($_FILES);
        // 实例化上传
        $upload = new \Think\Upload($config);
        // 调用上传方法
        $info = $upload->upload();
        // p($info);
        $data = array();
        if (!$info) {
            // 返回错误信息
            $error = $upload->getError();
            $data['error_info'] = $error;
            echo json_encode($data);
        } else {
            // 返回成功信息
            foreach ($info as $file) {
                $data['name'] = trim($file['savepath'] . $file['savename'], '.');
                // p($data);
                echo json_encode($data);
            }
        }
    }
}

/**
 * 检测webuploader上传是否成功
 * @param  string $file_path post中的字段
 * @return boolear           是否成功
 */
function upload_success($file_path)
{
    // 为兼容传进来的有数组；先转成json
    $file_path = json_encode($file_path);
    // 如果有undefined说明上传失败
    if (strpos($file_path, 'undefined') !== false) {
        return false;
    }
    // 如果没有.符号说明上传失败
    if (strpos($file_path, '.') === false) {
        return false;
    }
    // 否则上传成功则返回true
    return true;
}


/**
 * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
 */
function emoji_encode($str)
{
    if (!is_string($str)) return $str;
    if (!$str || $str == 'undefined') return '';

    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
        return addslashes($str[0]);
    }, $text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    return json_decode($text);
}

/**
 * 检测是否是手机访问
 */
function is_mobile()
{
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    function _is_mobile($substrs, $text)
    {
        foreach ($substrs as $substr)
            if (false !== strpos($text, $substr)) {
                return true;
            }
        return false;
    }

    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

    $found_mobile = _is_mobile($mobile_os_list, $useragent_commentsblock) ||
        _is_mobile($mobile_token_list, $useragent);
    if ($found_mobile) {
        return true;
    } else {
        return false;
    }
}

/**
 * 将utf-16的emoji表情转为utf8文字形
 * @param  string $str 需要转的字符串
 * @return string      转完成后的字符串
 */
function escape_sequence_decode($str)
{
    $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';
    return preg_replace_callback($regex, function ($matches) {
        if (isset($matches[3])) {
            $cp = hexdec($matches[3]);
        } else {
            $lead = hexdec($matches[1]);
            $trail = hexdec($matches[2]);
            $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
        }

        if ($cp > 0xD7FF && 0xE000 > $cp) {
            $cp = 0xFFFD;
        }
        if ($cp < 0x80) {
            return chr($cp);
        } else if ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
        }
        $result = html_entity_decode('&#' . $cp . ';');
        return $result;
    }, $str);
}

/**
 * 获取当前访问的设备类型
 * @return integer 1：其他  2：iOS  3：Android
 */
function get_device_type()
{
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 1;
    //分别进行判断
    if (strpos($agent, 'iphone') !== false || strpos($agent, 'ipad') !== false) {
        $type = 2;
    }
    if (strpos($agent, 'android') !== false) {
        $type = 3;
    }
    return $type;
}

/**
 * 生成pdf
 * @param  string $html 需要生成的内容
 */
function pdf($html = '<h1 style="color:red">hello word</h1>')
{
    vendor('Tcpdf.tcpdf');
    $pdf = new \Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // 设置打印模式
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nicola Asuni');
    $pdf->SetTitle('TCPDF Example 001');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    // 是否显示页眉
    $pdf->setPrintHeader(false);
    // 设置页眉显示的内容
    $pdf->SetHeaderData('logo.png', 60, 'AAAA', '123', array(0, 64, 255), array(0, 64, 128));
    // 设置页眉字体
    $pdf->setHeaderFont(Array('dejavusans', '', '12'));
    // 页眉距离顶部的距离
    $pdf->SetHeaderMargin('5');
    // 是否显示页脚
    $pdf->setPrintFooter(true);
    // 设置页脚显示的内容
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    // 设置页脚的字体
    $pdf->setFooterFont(Array('dejavusans', '', '10'));
    // 设置页脚距离底部的距离
    $pdf->SetFooterMargin('10');
    // 设置默认等宽字体
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // 设置行高
    $pdf->setCellHeightRatio(1);
    // 设置左、上、右的间距
    $pdf->SetMargins('10', '10', '10');
    // 设置是否自动分页  距离底部多少距离时分页
    $pdf->SetAutoPageBreak(TRUE, '15');
    // 设置图像比例因子
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->setFontSubsetting(true);
    $pdf->AddPage();
    // 设置字体
    $pdf->SetFont('stsongstdlight', '', 14, '', true);
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_001.pdf', 'I');
}

/**
 * 生成二维码
 * @param  string $url url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url, $size = 4)
{
    Vendor('Phpqrcode.phpqrcode');
    QRcode::png($url, false, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
}

/**
 * 数组转xls格式的excel文件
 * @param  array $data 需要生成excel文件的数组
 * @param  string $filename 生成的excel文件名
 *      示例数据：
 * $data = array(
 * array(NULL, 2010, 2011, 2012),
 * array('Q1',   12,   15,   21),
 * array('Q2',   56,   73,   86),
 * array('Q3',   52,   61,   69),
 * array('Q4',   30,   32,    0),
 * );
 */
function create_xls($data, $filename = 'simple.xls')
{
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename = str_replace('.xls', '', $filename) . '.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

/**
 * 数据转csv格式的excle
 * @param  array $data 需要转的数组
 * @param  string $header 要生成的excel表头
 * @param  string $filename 生成的excel文件名
 *      示例数组：
 * $data = array(
 * '1,2,3,4,5',
 * '6,7,8,9,0',
 * '1,3,5,6,7'
 * );
 * $header='用户名,密码,头像,性别,手机号';
 */
function create_csv($data, $header = null, $filename = 'simple.csv')
{
    // 如果手动设置表头；则放在第一行
    if (!is_null($header)) {
        array_unshift($data, $header);
    }
    // 防止没有添加文件后缀
    $filename = str_replace('.csv', '', $filename) . '.csv';
    ob_clean();
    Header("Content-type:  application/octet-stream ");
    Header("Accept-Ranges:  bytes ");
    Header("Content-Disposition:  attachment;  filename=" . $filename);
    foreach ($data as $k => $v) {
        // 如果是二维数组；转成一维
        if (is_array($v)) {
            $v = implode(',', $v);
        }
        // 替换掉换行
        $v = preg_replace('/\s*/', '', $v);
        // 解决导出的数字会显示成科学计数法的问题
        $v = str_replace(',', "\t,", $v);
        // 转成gbk以兼容office乱码的问题
        echo iconv('UTF-8', 'GBK', $v) . "\t\r\n";
    }
}

/**
 * 导入excel文件
 * @param  string $file excel文件路径
 * @return array        excel文件内容数组
 */
function import_excel($file)
{
    // 判断文件是什么格式
    $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    if ($type == 'xlsx') {
        $type = 'Excel2007';
    } elseif ($type == 'xls') {
        $type = 'Excel5';
    }
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数 
    $highestRow = $sheet->getHighestRow();
    // 取得总列数      
    $highestColumn = $sheet->getHighestColumn();
    //循环读取excel文件,读取一条,插入一条
    $data = array();
    //从第一行开始读取数据
    for ($j = 1; $j <= $highestRow; $j++) {
        //从A列读取数据
        for ($k = 'A'; $k <= $highestColumn; $k++) {
            // 读取单元格
            $data[$j][] = $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        }
    }
    return $data;
}


/**
 * geetest检测验证码
 */
function geetest_chcek_verify($data)
{
    $geetest_id = C('GEETEST_ID');
    $geetest_key = C('GEETEST_KEY');
    $geetest = new \Org\Xb\Geetest($geetest_id, $geetest_key);
    $user_id = $_SESSION['geetest']['user_id'];
    if ($_SESSION['geetest']['gtserver'] == 1) {
        $result = $geetest->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $user_id);
        if ($result) {
            return true;
        } else {
            return false;
        }
    } else {
        if ($geetest->fail_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'])) {
            return true;
        } else {
            return false;
        }
    }
}




//curl post 支持http/https
function curl_calls($urls, $datas, $get = null,$json)
{
    $ch = curl_init();
    // 设置curl允许执行的最长秒数
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // 获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (1 == strpos("$".$urls, "https://"))
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    //是否get请求
    if ($get == 1) {
        curl_setopt($ch, CURLOPT_URL, $urls);
    }elseif ($get == 2){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "put");
        curl_setopt($ch, CURLOPT_URL, $urls);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    } else {
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $urls);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    }
    if($json){
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            "Accept: application/json",
        ));
    }else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type' => 'multipart/form-data',
        ));
    }
    // 执行操作
    $res = curl_exec($ch);
    if ($res == NULL) {
        $res = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
    }
    curl_close($ch);
    return $res;
}

/**
 * microsecond 微秒
 * @return float 返回时间戳的毫秒数部分
 */
function get_millisecond()
{
    list($usec, $sec) = explode(" ", microtime());
    $msec = round($usec * 1000);
    return $msec;

}


/*输出毫秒*/
function GetMillisecond()
{
    $millisecond = get_millisecond();
    $millisecond = str_pad($millisecond, 3, '0', STR_PAD_RIGHT);
    return date("YmdHis") . $millisecond;
}


/*获取客户端IP*/
function Get_Clienti_Ips($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($_SERVER['HTTP_X_REAL_IP']) {//nginx 代理模式下，获取客户端真实IP
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

function encrypt($strinfo, $desKey)
{//数据加密
    $size = mcrypt_get_block_size(MCRYPT_3DES, 'ecb');
    $strinfo = pkcs5_pad($strinfo, $size);
    $key = str_pad($desKey, 24, '0');
    $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
    $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    @mcrypt_generic_init($td, $key, $iv);
    $data = mcrypt_generic($td, $strinfo);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    //    $data = base64_encode($this->PaddingPKCS7($data));
    //$data = urlencode($data);
    $data = base64_encode($data);

    //$data = str_replace('','',$data);
    //$data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}

function pkcs5_pad($text, $blocksize)
{
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

function rand_one($data)
{
    $index = rand(0, count($data) - 1);
    return $data[$index];
}

#多渠道解决模板  用于部分页面按照渠道自己页面修改实现

function set_theme($theme = '')
{
    //判断是否存在设置的模板主题
    if (empty($theme)) {
        $theme_name = C('DEFAULT_THEME');
    } else {
        $file = MODULE_PATH . 'View/' . $theme . '/' . CONTROLLER_NAME . '/' . ACTION_NAME . '.html';
        if (file_exists($file)) { #存在这个模板文件用 定义的模板
            $theme_name = $theme;
        } else { #不存在 用默认的
            $theme_name = C('DEFAULT_THEME');
        }

    }
    //替换COMMON模块中设置的模板值
    if (C('Current_Theme')) {
        C('TMPL_PARSE_STRING', str_replace(C('Current_Theme'), $theme_name, C('TMPL_PARSE_STRING')));
    } else {
        C('TMPL_PARSE_STRING', str_replace("MODULE_NAME", MODULE_NAME, C('TMPL_PARSE_STRING')));
        C('TMPL_PARSE_STRING', str_replace("DEFAULT_THEME", $theme_name, C('TMPL_PARSE_STRING')));
    }
    C('Current_Theme', $theme_name);
    C('DEFAULT_THEME', $theme_name);


}

#品牌列表

function DomainAuthList()
{
    $res = M('domain_auth')->field('web_name,web_authcode')->where(array('status'=>1))->select();
    return $res;
}

#通道列表

function AlleysList()
{
    $res = M('MchAlleys')->field('type,name')->where(array('status'=>1))->select();
    return $res;
}

#根据授权码获取代理品牌名称
function DomainName($domain)
{
    $_domain = M('domain_auth')->where(array('web_authcode' => $domain))->getField('web_name');
    return $_domain;
}
function DomainAuthField($domain,$data)
{
    $_domain = M('domain_auth')->where(array('web_authcode' => $domain))->getField($data);
    return $_domain;
}

function DomainAuthData($data)
{
    $_domain = M('domain_auth')->where(array('web_domain' => domain_rel()))->getField($data);
    return $_domain;
}

function domain_auth()
{
	$cacheId='domain_'.md5(domain_rel());
	if(empty(S($cacheId))){
		$_domain = M('domain_auth')->where(array('web_domain' => domain_rel()))->find();
		S($cacheId,$_domain['web_authcode']);
		return $_domain['web_authcode'];
	}else{
		return S($cacheId);
	}

}

function domain_id($type)
{
    if ($type) {
        $_domain = M('domain_auth')->where(array('web_authcode' => $type))->find();
    } else {
        $_domain = M('domain_auth')->where(array('web_domain' => domain_rel()))->find();
    }
    return $_domain['id'];
}

function domain_rel()
{
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn", "cc", "xin","top","vip");
    $array_domain = explode(".", $_SERVER['HTTP_HOST']);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}


function agent_name($pid)
{
    $user_name = M('Mch_agent')->where(array('id' => $pid))->getField('user_name');
    return $user_name;
}

#阿里云服务
function ali_mns($data)
{
    if (!$data) {
        return 'Ali_MNS数据参数不能为空';
    }
    Vendor('Ali_topic');
    $accessId = C('Ali_MNS.accessId');
    $accessKey = C('Ali_MNS.accessKey');
    $endPoint = C('Ali_MNS.endPoint');
    $Top_name = C('Ali_MNS.topName');
    $instance = new \CreateTopicAndPublishMessage($accessId, $accessKey, $endPoint);
    $_data = $data;
    $data = base64_encode(json_encode($data));
    //$res=$instance->set($data,$Top_name);
    $res = object_array($instance->set($data, $Top_name));
    if ($res['succeed'] == true) {
        $arr = array(
            'messageId' => $res['messageId'],
            'type' => $_data['mc'] . '_' . $_data['ac'],
            'ctime' => time(),
            'status' => 0,
            'data' => json_encode($_data)
        );
        $msn_id = M('Alimsn')->add($arr);
        $rel = array(
            'status' => 1,
            'msn_id' => $msn_id,
            'messageId' => $res['messageId'],
            'msg' => '任务创建成功!',
        );
    } else {
        $rel = array(
            'status' => 0,
            'msg' => json_encode($res),
        );
    }
    return $rel;
}

function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}


function object_array($object)
{
    $ref = new \ReflectionClass($object);
    $props = $ref->getProperties();
    $arr = [];
    foreach ($props as $prop) {
        $prop->setAccessible(true);
        $arr[$prop->getName()] = $prop->getValue($object);
        $prop->setAccessible(false);
    }
    return $arr;
    //return $array;
}


function ALI_OSS($type = null)
{
    $_data = M('domain_auth')->where(array('web_domain' => domain_rel()))->find();
    if ($type == 'BUCKET') {
        if ($_data['oss_bucket']) {
            return $_data['oss_bucket'];
        } else {
            return C('ALI_OSS_CONFIG.oss_bucket');
        }
    } else {
        if ($_data['oss_domain']) {
            return 'http://' . $_data['oss_domain'];
        } else {
            return 'http://' . C('ALI_OSS_CONFIG.oss_domain');
        }
    }
}

function ALI_SMS()
{
    $_data = M('MchAlismsConfig')->where(array('domain_auth' => domain_auth()))->find();
    return $_data;

}

# 微信SDK 入口加载
function & load_wechat($type = '', $id)
{
    Vendor('Wechat.Loader');
    static $wechat = array();
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        // 从数据库查询配置参数
        $config = M('MchWeixin')->where(array('id' => $id, 'domain_auth' => domain_auth()))->field('token,appid,appsecret,encodingaeskey,qrc_img')->find();
        // 设置SDK的缓存路径
        $config['cachepath'] = CACHE_PATH . 'Data/';
        $wechat[$index] = \Wechat\Loader::get_instance($type, $config);
    }
    return $wechat[$index];
}

# 微信SDK 入口加载
function & DoLoad_wechat($type = '', $id, $domain)
{
    Vendor('Wechat.Loader');
    static $wechat = array();
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        // 从数据库查询配置参数
        $config = M('MchWeixin')->where(array('id' => $id))->field('token,appid,appsecret,encodingaeskey,qrc_img')->find();
        // 设置SDK的缓存路径
        $config['cachepath'] = CACHE_PATH . 'Data/';
        $wechat[$index] = \Wechat\Loader::get_instance($type, $config);
    }
    return $wechat[$index];
}


/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function Xencode($string = '', $skey = 'xunmafu_ccl')
{
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key] .= $value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function Xdecode($string = '', $skey = 'xunmafu_ccl')
{
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}


#获取商户公众号的二维码
function GetMchQrcode(){
    $id=GetWxId('m');
    $qrc_img=M('MchWeixin')->where(array('id' => $id, 'domain_auth' => domain_auth()))->getField('qrc_img');
    return $qrc_img;
}

#所用公众号
function GetWxId($type)
{
    //rwlog('GET_Data',$_GET);
    $_data = M('MchPayConfig')->where(array('domain_auth' => domain_auth()))->field('mch_wxid,pay_wxid')->find();
    if ($type == 'm') { #商户 代理注册所用
        return $_data['mch_wxid'];
    } else { #关注 支付所用
        return $_data['pay_wxid'];
    }


}


#所用公众号
function DoGetWxId($type, $domain)
{
    $_data = M('MchPayConfig')->where(array('domain_auth' => $domain))->field('mch_wxid,pay_wxid')->find();
    if ($type == 'm') { #商户 代理注册所用
        return $_data['mch_wxid'];
    } else { #关注 支付所用
        return $_data['pay_wxid'];
    }
}

#阿里云开发市场API
function ali_appcode()
{
    #测试  随机使用列表里的Code参数
    //return '6125724544064bc2ad852cc18fd905d8';
    $code = array('6125724544064bc2ad852cc18fd905d8', '1f5c61ab7a614f118ec7a9137d74730b', '6ec818bf3cd34e97986b36113776d7f8', '9a95afaf2c28439791e2b3b5b8a9bf02', '4c47dd5f98a245b0abd48063c962156a', 'bc3ef622239b4f098885cbcdb0a8bf7b');
    return rand_one($code);

}

#盛付通图片base64编码
function sft_imgToBase64($img_file)
{
    if (preg_match('/(http:\/\/)|(https:\/\/)/i', $img_file)) {
        #网络图片
        $file_content = chunk_split(base64_encode(file_get_contents(ltrim($img_file,'.')))); // base64编码
        $img_base64 = $file_content;//合成图片的base64编码
    }else {
        #本地图片
        $img_base64 = '';
        if (file_exists($img_file)) {
            $app_img_file = $img_file; // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限
            if ($fp) {
                $filesize = filesize($app_img_file);
                $content = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                $img_base64 = $file_content;//合成图片的base64编码

            }
            fclose($fp);
        }
    }

    return $img_base64; //返回图片的base64
}

#图片base64编码
function imgToBase64($img_file)
{
    if (preg_match('/(http:\/\/)|(https:\/\/)/i', $img_file)) {
        #网络图片
       $file_content = chunk_split(base64_encode(file_get_contents(ltrim($img_file,'.')))); // base64编码
       $img_base64 = $file_content;//合成图片的base64编码
    }else {
        #本地图片
        $img_base64 = '';
        if (file_exists(getcwd().ltrim($img_file,'.'))) {
            $app_img_file = getcwd().ltrim($img_file,'.'); // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限
            if ($fp) {
                $filesize = filesize($app_img_file);
                $content = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                $img_base64 = $file_content;//合成图片的base64编码

            }
            fclose($fp);
        }
    }

    return $img_base64; //返回图片的base64
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(mb_strlen($str,$charset)>$length)
    {
        if(function_exists("mb_substr")){
            if($suffix)
                return mb_substr($str, $start, $length, $charset)."...";
            else
                return mb_substr($str, $start, $length, $charset);
        }elseif(function_exists('iconv_substr')) {
            if($suffix)
                return iconv_substr($str,$start,$length,$charset)."...";
            else
                return iconv_substr($str,$start,$length,$charset);
        }
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        if($suffix) return $slice."…";
        return $slice;
    }
    else
    {
        return $str;
    }
}

#微信名称过滤特殊字符
function WxNameFilter($str)
{
    if ($str) {
        $name = $str;
        $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
        $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S', '?', $name);
        $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie", "", json_encode($name)));
        if (!$return) {
            return json_encode($return,JSON_UNESCAPED_UNICODE);
        }
    } else {
        $return = '';
    }
    return $return;
}


#根据网络图片转存本地图片路径
function ImgToLocalUrl($data){
    if (preg_match('/(http:\/\/)|(https:\/\/)/i',$data)) {
        $savePath =  './Upload/NetImageTemp/';// 设置附件上传目录
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].$savePath)||!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)){
            mkdir($_SERVER['DOCUMENT_ROOT'].$savePath,0777);
        }
        if (file_exists($savePath.md5_file($data).".jpg")) {
            $url=$savePath.md5_file($data).".jpg";
        }else {
           // $resp = _getImage($data, $savePath);
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$data);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            //curl_setopt($hander,CURLOPT_TIMEOUT,600);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            $img=curl_exec($ch);
            curl_close($ch);
            $filename=md5_file($data).".jpg";//文件名
            //文件大小
            $fp2=@fopen($savePath.$filename,'a');
            fwrite($fp2,$img);
            fclose($fp2);
            unset($img,$url);
            $url=ltrim($savePath,'.').$filename;
        }
        //$url=$resp['save_path'];
    }else {#本地
        $url=$data;
    }
    return $url;
}

function qf_get_curl_calls($curl, $data, $https = true,$config)
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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //'Content-type' => 'multipart/form-data',
        'X-QF-APPCODE:'.$config['AppCode'],
        'X-QF-SIGN:'.qf_data_sign($data,$config['SignKey']),
    ));
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_URL, $curl);
    curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
    $str = curl_exec($ch);
    curl_close($ch);
    return $str;
}


#前方好进curl
function qf_curl_calls($curl, $data, $file=null, $https = true,$config)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    if ($https) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    //$data = json_encode($data, JSON_UNESCAPED_UNICODE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type' => 'multipart/form-data',
        'X-QF-APPCODE:'.$config['Appcode'],
        'X-QF-SIGN:'.qf_data_sign($data,$config['SignKey']),
    ));
    if($file) {
        $_file=array();
        foreach ($file as $key => $val) {
            if ($val) {
                $_file[] = array('key'=>$key,'url'=>$val);
            }
        }
        foreach ($_file as $k=>$v){
            $data[$v['key']]=new CURLFile(getcwd().ltrim(ImgToLocalUrl($v['url']),'.'),'image/jpeg',$v['key']);

        }
    }
    rwlog('qf_file',$config);
    rwlog('qf_file',$data);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_URL, $curl);
    curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
    $str = curl_exec($ch);
    curl_close($ch);
    return $str;
}

#支付签名
function qf_data_sign($data,$key,$type=null){
    $signPars = "";
    ksort($data);
    foreach($data as $k => $v) {
        if("" != $v && "sign" != $k) {
            $signPars .= $k . "=" . $v . "&";
        }
    }
    $sign_data=rtrim($signPars,'&').$key;
    $sign = md5($sign_data);
    if($type){
        return $sign_data;
    }else {
        return $sign;
    }
}


/**
 * 获取所有 以 HTTP开头的header参数
 * @return array
 */
function getAllHeaders1(){
    $headers = array();
    foreach($_SERVER as $key=>$value){
        if(substr($key, 0, 5)==='HTTP_'){
            $key = substr($key, 5);
            $key = str_replace('_', ' ', $key);
            $key = str_replace(' ', '-', $key);
            $key = strtolower($key);
            $headers[$key] = $value;
        }
    }
    return $headers;
}


#U网联通道签名
function Upay_sign($data){
    $signPars = "";
    ksort($data);
    foreach($data as $k => $v) {
        if("" != $v && "signature" != $k && "payList" != $k) {
            $signPars .= $k . "=" . $v . "&";
        }
    }
    $sign_data=rtrim($signPars,'&');
    rwlog('sign_data',$sign_data);
    $priKey = '-----BEGIN RSA PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAJo7Ev+iABPAAayU
Clhgeoi7fe8mSMSnYLVRggAAfqf6AxX0mxDTMhkTZQnkq1IT0k5id8qHZf1JB6w2
/Ia1r8MvGOXFI7pSfjWFCj617LoB/3iiiXGOSY6gDj705xV1mYAwgsF/qgwQcvLE
r0JumxAfSQaHLzVF6FkDefrveDR3AgMBAAECgYAToa1baK2F1gzggL+IXE98lBEF
nePpVDzVF5jqkyWMECDQbKBIVfmsg3F6/uZnvioo3BPPbcinouIdI6qlIv9KMCVN
/blEekBNNNBezry2KRvKuYK+QFhgu3NC9V3loX9Tr7iEqSNS0dFUb+321969QyYI
D91yzgotOMLVNLr9IQJBANqoT+vGRotM8gyIKlFYnIv4JjPterYEodpKTc1xuUAT
7VPsQQJoKcJnK5DWTc8R8BmrA9jF38wvwmQn4oAX/akCQQC0kgf6KaUEPOFbfXzx
osGl15OS1mbSqimaAR8bCMFQb7+yDEVlA6Zd4GCas515X1rDKyN1l5dJU5X61LR3
eLUfAkEAoXYsHgR5zx9wlURvS0ZNwyXov+ua85GUGudHcG+Lxf9w/sm0b5yPSZh5
mShGqKOsUqfv3UCws8yIlQDGxqPwkQJBAJQ8IbJbh61f8T0zWmPf0gs8W1i7C/Q7
XyWfcBK4cEKBSqR28G0JvwToE0ZM16TxA8ie3GBHzxmSNdiRz4kYnc8CQADMjhht
v+UbbnRen2M/9kG8kRFENS2ZnicH9c8JDZlJ0P1bVN1e9OnttWIJAXOX/8zOaKsw
iTJPo7RngDC7V0M=
-----END RSA PRIVATE KEY-----'; //打开文件，将文件内容保存成字符串
    $res = openssl_pkey_get_private($priKey); //检查该私钥是否可用
    openssl_sign($sign_data, $sign, $res, OPENSSL_ALGO_MD5); //设置签名的哈希算法(Algorithm)为MD5
    openssl_free_key($res);
    $sign = base64_encode($sign);
    return $sign;
}



// 获取页面中的 form 表单中的所有 input、textarea 元素中 name、value、type 等属性值
function get_page_form_data($content)
{
    $arr_form = array();
    $form = regular_form_tags($content);
    for($i = 0;$i < count($form[0]);$i ++)
    {
        $arr_form[$i]['action'] = regular_form_action($form[1][$i]);
        $arr_form[$i]['method'] = regular_form_method($form[1][$i]);
        $input = regular_input_tags($form[2][$i]);
        for($j = 0;$j < count($input[0]);$j ++)
        {
            $arr_form[$i]['inputs'][$j]['name'] = regular_input_name($input[0][$j]);
            $arr_form[$i]['inputs'][$j]['type'] = regular_input_type($input[0][$j]);
            $arr_form[$i]['inputs'][$j]['value'] = regular_input_value($input[0][$j]);
        }
        $textarea = regular_textarea_tags($form[2][$i]);
        for($k = 0;$k < count($textarea);$k ++)
        {
            $arr_form[$i]['textarea'][$k]['name'] = regular_textarea_name($textarea[$k]);
            $arr_form[$i]['textarea'][$k]['value'] = regular_textarea_value($textarea[$k]);
        }
        $select = regular_select_tags($form[2][$i]);
        for($l = 0;$l < count($select[0]);$l ++)
        {
            $arr_form[$i]['select'][$l]['name'] = regular_select_name($select[1][$l]);
            $option = regular_option_tags($select[2][$l]);
            for($n = 0;$n < count($option[$l]);$n ++)
            {
                $arr_form[$i]['select'][$l]['option'][$n] = regular_option_value($option[$l][$n]);
            }
        }
    }
    return $arr_form;
}
// 正则匹配 form 标签
function regular_form_tags($string)
{
    $pattern = '/<form(.*?)>(.*?)<\/form>/si';
    preg_match_all($pattern,$string,$result);
    return $result;
}
// 正则匹配 form 标签的 action 属性值
function regular_form_action($string)
{
    $pattern = '/action[\s]*?=[\s]*?([\'\"])(.*?)\1/';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}
// 正则匹配 form 标签的 method 属性值
function regular_form_method($string)
{
    $pattern = '/method[\s]*?=[\s]*?([\'\"])(.*?)\1/';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}
// 正则匹配 input 标签
function regular_input_tags($string)
{
    $pattern = '/<input.*?\/?>/si';
    if(preg_match_all($pattern,$string,$result))
    {
        return $result;
    }
    return null;
}
// 正则匹配 input 标签的 name 属性值
function regular_input_name($string)
{
    $pattern = '/name[\s]*?=[\s]*?([\'\"])(.*?)\1/';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;

}
// 正则匹配 input 标签的 type 属性值
function regular_input_type($string)
{
    $pattern = '/type[\s]*?=[\s]*?([\'\"])(.*?)\1/';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}
// 正则匹配 input 标签的 value 属性值
function regular_input_value($string)
{
    $pattern = '/value[\s]*?=[\s]*?([\'\"])(.*?)\1/';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}
// 正则匹配 textarea 标签
function regular_textarea_tags($string)
{
    $pattern = '/(<textarea.*?>.*?<\/textarea[\s]*?>)/si';
    if(preg_match_all($pattern,$string,$result))
    {
        return $result[1];
    }
    return null;
}
// 正则匹配 textarea 标签的 name 属性值
function regular_textarea_name($string)
{
    $pattern = '/name[\s]*?=[\s]*?([\'\"])(.*?)\1/si';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}
// 正则匹配 textarea 标签的 name 属性值
function regular_textarea_value($string)
{
    $pattern = '/<textarea.*?>(.*?)<\/textarea>/si';
    if(preg_match($pattern,$string,$result))
    {
        return $result[1];
    }
    return null;
}
// 正则匹配 select 标签
function regular_select_tags($string)
{
    $pattern = '/<select(.*?)>(.*?)<\/select[\s]*?>/si';
    preg_match_all($pattern,$string,$result);
    return $result;
}
// 正则匹配 select 标签的 option 子标签
function regular_option_tags($string)
{
    $pattern = '/<option(.*?)>.*?<\/option[\s]*?>/si';
    preg_match_all($pattern,$string,$result);
    return $result;
}
// 正则匹配 select 标签的 name 属性值
function regular_select_name($string)
{
    $pattern = '/name[\s]*?=[\s]*?([\'\"])(.*?)\1/si';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}
// 正则匹配 select 的子标签 option 的 value 属性值
function regular_option_value($string)
{
    $pattern = '/value[\s]*?=[\s]*?([\'\"])(.*?)\1/si';
    if(preg_match($pattern,$string,$result))
    {
        return $result[2];
    }
    return null;
}

