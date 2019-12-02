<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 微信服务商扩展管理控制器
 */
class WxPayController extends AdminBaseController
{



	/**
	 * 查询提现状态
	 * @param $data
	 */
	public function drawStatus(){
		$data=I('get.');
		$arr=[
			'sub_mch_id'=>$data['sub_mch_id'],
			'date'=>$data['date']
		];
		dump($arr);
		$res = R('Pays/XiaoWei/queryAutoWithDrawByDate', [$arr]);
		dump($res);
	}


	/**
	 * 变更
	 */
	public function alterInfo(){
		if(IS_POST){
			$post=I('post.');
			if($post['type']=='bank'){ //变更银行卡
				try {
					$res = R('Pays/XiaoWei/alterInfoBank', [$post]);
				}catch (\Exception $e){
					$this->error($e->getMessage());
				}
			}else{
				if(empty($post['merchant_name'])&&empty($post['mobile_phone'])&&empty($post['email'])){
					$this->error('请填写要变更的信息,三项不可全为空');
				}
				try {
					$res = R('Pays/XiaoWei/alterInfo', [$post]);
				}catch (\Exception $e){
					$this->error($e->getMessage());
				}
			}
			$this->success($res,U('xwList'));
		}else {
			$db = M('xwApplyin');
			$data = $db->where(['id' => I('get.id'), 'domain_auth' => domain_auth()])->find();
			if (empty($data['sub_mch_id'])) {
				$this->error('获取小微商户号失败!请确保当前商户已小微入网成功');
			}
			$assign = [
				'data' => $data,
				'bank' => $this->getBank(),
				'pro' => $this->getProCity(),
			];
			$this->assign($assign);
			$this->display();
		}
	}

	/**
	 * 创建升级申请
	 */
	public function upInfoCreate(){
		$db = M('xwApplyin');
		if(IS_POST){
			$post=I('post.');
			try {
				$res = R('Pays/XiaoWei/applyUp', [$post]);
			}catch (\Exception $e){
				$this->error($e->getMessage());
			}
			if($res['status']){
				$this->success($res['msg']);
			}else{
				$this->error($res['msg']);
			}
		}else {
			$data = $db->where(['id' => I('get.id'), 'business_code' => I('get.business_code'), 'domain_auth' => domain_auth()])->find();
			if (empty($data['sub_mch_id'])) {
				$this->error('获取小微商户号失败!请确保当前商户已小微入网成功');
			}
			$assign = [
				'sub_mch_id' => $data['sub_mch_id'],
				'bank' => $this->getBank(),
				'pro' => $this->getProCity(),
			];
			$this->assign($assign);
			$this->display();
		}
	}

	/**
	 * 商户升级
	 */
	public function upInfo(){
		$db = M('xwApplyin');
		$data = $db->where(['id' => I('get.id'), 'business_code' => I('get.business_code'), 'domain_auth' => domain_auth()])->find();
		if (empty($data['sub_mch_id'])) {
			$this->error('获取小微商户号失败!请确保当前商户已小微入网成功');
		}
		//获取是否有升级记录
		$up=M('xwApplyup')->where(['sub_mch_id'=>$data['sub_mch_id']])->find();
		if($up) {
			unset($up['data']);
			$this->assign($up);
			$this->display();
		}else{
			$url=U('upInfoCreate',['id'=>I('get.id'),'business_code'=>I('get.business_code')]);
			redirect($url);
		}
	}

	/**
	 * 申请状态查询
	 */
	public function applyUpState(){
		if(IS_POST){
			$apply=M('xwApplyup')->where(['id'=>I('post.id'),'domain_auth'=>domain_auth()])->find();
			$sub_mch_id=$apply['sub_mch_id'];
			if($sub_mch_id){
				try {
					$res = R('Pays/XiaoWei/applyUpState', [$sub_mch_id]);
				}catch (\Exception $e){
					$this->error($e->getMessage());
				}
				//dump($res);
				switch ($res['applyment_state']){
					case 'REJECTED'://驳回状态
						$txt='';
						foreach (json_decode($res['audit_detail'],true)['audit_detail'] as $k=>$v){
							$k+=1;
							$txt.='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:已驳回</p><p>'.$k.'、'.$v['reject_reason'].'('.$v['param_name'].')</p>';
						}
						$msg=$txt;
						break;
					case 'TO_BE_SIGNED':
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:待签约</p><p style="text-align: center"><span style="color: red;font-size: 16px">'.$apply["id_card_name"].'</span>签约二维码:<br><image src="'.$res['sign_url'].'"></image></p><p style="text-align: center">请将二维码出示给商户扫码签约,微信官方签约说明:<a href="https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_4" target="_blank">点击我打开说明</a></p>';
						break;
					case 'AUDITING':
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:审核中('.$res['applyment_state'].')<p>';
						break;
					case 'ACCOUNT_NEED_VERIFY': //待账户验证
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:待账户验证 ('.$res['applyment_state'].')<p><p>付款户名:'.$res['account_name'].'</p><p>汇款金额:'.($res['pay_amount']*100).'元</p><p>收款卡号:'.$res['destination_account_number'].'</p><p>收款户名:'.$res['destination_account_name'].'</p><p>开户银行:'.$res['destination_account_bank'].'</p><p>省市信息:'.$res['city'].'</p><p>备注信息:'.$res['remark'].'</p><p>汇款截止时间:'.$res['deadline_time'].'</p><p style="color: red">请商户按照以下信息进行汇款，以完成账户验证,验证结束后，汇款金额将全额退还至汇款账户</p>';
						break;
					default:
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:'.$res['applyment_state_desc'].'('.$res['applyment_state'].')<p>';
						break;
				}
				//dump($msg);
				$this->success($msg);
			}else{
				$this->error('获取子商户号失败');
			}
		}
	}



	/**
	 * 配置
	 */
	public function mchConfig(){
		$db = M('xwApplyin');
		if(IS_POST){
			$post=I('post.');
			$apply= $db->where(['id' => $post['id'], 'domain_auth' => domain_auth()])->find();
			if (empty($apply['sub_mch_id'])) {
				$this->error('获取信息失败,请确保商户状态为已完成或待签约');
			}
			if($post['api_type']=='appid'){//关注配置
				try {
					$appid=$post['sub_type']==1?$post['subscribe_appid']:$post['receipt_appid'];
					R('Pays/XiaoWei/addRecommendConf', [$apply['sub_mch_id'],$appid,$post['sub_type']==1?false:true]);
				} catch (\Exception $e) {
					$this->error($e->getMessage());
				}
				$this->success('关注信息配置成功');
			}elseif ($post['api_type']=='sub'){
				try {
					R('Pays/XiaoWei/addSubDevConfig', [$apply['sub_mch_id'],$post['sub_appid']]);
				} catch (\Exception $e) {
					$this->error($e->getMessage());
				}
				$this->success('关联APPID信息配置成功');
			}else{//支付授权目录
				try {
					R('Pays/XiaoWei/applyAddSubDevConfig', [$apply['sub_mch_id'],$post['jsapi_path']]);
				} catch (\Exception $e) {
					$this->error($e->getMessage());
				}
				$this->success('支付授权目录配置成功');
			}

		}else {
			$data = $db->where(['id' => I('get.id'), 'domain_auth' => domain_auth()])->find();
			if (empty($data['sub_mch_id'])) {
				$this->error('获取信息失败,请确保商户状态为已完成或待签约');
			}
			//获取配置信息
			try {
				$api = R('Pays/XiaoWei/querySubDevConfig', [$data['sub_mch_id']]);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
			$data['appid_config_list'] = $api['appid_config_list']['appid_config_list'];
			$data['jsapi_path_list'] = $api['jsapi_path_list']['jsapi_path_list'];
			$data['appid'] =$api['appid'];
			//dump($data);
			$this->assign($data);
			$this->display();
		}
	}

	/**
	 * 资料
	 */
	public function info(){
		$data=I('get.');
		$apply=M('xwApplyinLog')->where(['mid'=>$data['m'],'applyment_id'=>$data['applyment_id'],'domain_auth'=>domain_auth()])->getField('data');
		if(!$apply){
			$this->error('获取资料信息失败');
		}
		$apply=json_decode($apply,true);
		$apply['id_card_valid_time']=json_decode($apply['id_card_valid_time'],true);
		$apply['business_addition_pics']=json_decode($apply['business_addition_pics'],true);
		$this->assign('data',$apply);
		$this->display();
	}

	/**
	 * 申请列表
	 */
	public function xwList(){
		$db = M('xwApplyin');
		if(!empty(I('post.mid'))){
			$map['mid']=I('post.mid');
		}
		if(!empty(I('post.applyment_state'))) {
			$map['applyment_state'] = I('post.applyment_state');
		}
		$map['domain_auth'] = domain_auth();
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}

	public function xwIn(){
		if(IS_POST){
			$post=I('post.');
			try {
				$res = R('Pays/XiaoWei/applyIn', [$post]);
			}catch (\Exception $e){
				$this->error($e->getMessage());
			}
			if($res['status']){
				$this->success($res['msg']);
			}else{
				$this->error($res['msg']);
			}
		}else {
			$get=I('get.');
			if($get['business_code']&&!empty($get['business_code'])){
				$applyment_state=M('xwApplyin')->where(['business_code'=>$get['business_code']])->getField('applyment_state');
				if($applyment_state!='REJECTED'){
					$this->error('当前商户状态非驳回状态,无法重新入件');
				}
			}
			if(!isset($get['id'])||empty($get['id'])){
				$this->error('请先选择商户');
			}
			$data=M('mchSeller')->where(['id'=>$get['id'],'domain_auth'=>domain_auth()])->find();
			if(empty($data)){
				$this->error('获取商户信息失败');
			}
			$data['mch_bank_list']=$this->bankNameStr($data['mch_bank_list']);
			//dump($data);
			$assign = [
				'data'=>$data,
				'bank' => $this->getBank(),
				'rate' => $this->getRate(),
				'pro' => $this->getProCity(),
			];
			$this->assign($assign);
			$this->display();
		}
	}


	/**
	 * 申请状态查询
	 */
	public function applyState(){
		if(IS_POST){
			$apply=M('xwApplyin')->where(['id'=>I('post.id'),'domain_auth'=>domain_auth()])->find();
			$applyment_id=$apply['applyment_id'];
			if($applyment_id){
				try {
					$res = R('Pays/XiaoWei/applyState', [$applyment_id]);
				}catch (\Exception $e){
					$this->error($e->getMessage());
				}
				//dump($res);
				switch ($res['applyment_state']){
					case 'REJECTED'://驳回状态
						$txt='';
						foreach (json_decode($res['audit_detail'],true)['audit_detail'] as $k=>$v){
							$k+=1;
							$txt.='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:已驳回</p><p>'.$k.'、'.$v['reject_reason'].'('.$v['param_name'].')</p>';
						}
						$msg=$txt;
						break;
					case 'TO_BE_SIGNED':
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:待签约</p><p style="text-align: center"><span style="color: red;font-size: 16px">'.$apply["id_card_name"].'</span>签约二维码:<br><image src="'.$res['sign_url'].'"></image></p><p style="text-align: center">请将二维码出示给商户扫码签约,微信官方签约说明:<a href="https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_4" target="_blank">点击我打开说明</a></p>';
						break;
					case 'AUDITING':
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:审核中('.$res['applyment_state'].')<p>';
						break;
					default:
						$msg='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['mch_name'].'</p><p style="color: red;text-align: center;">申请状态:'.$res['applyment_state_desc'].'('.$res['applyment_state'].')<p>';
						break;
				}
				//dump($msg);
				$this->success($msg);
			}else{
				$this->error('获取申请单号失败');
			}
		}
	}

	/**
	 * 转换银行格式为微信要求银行格式
	 * @param $data
	 * @return mixed|string
	 */
	protected function bankNameStr($data){
		$bank=reload_bank($data);
		switch ($bank){
			case '广东发展银行':
				$banks='广发银行';
				break;
			case '上海浦东发展银行':
				$banks='浦发银行';
				break;
			default:
				$banks=$bank;
				break;
		}
		if(strlen($banks)>12){
			$banks=str_replace('中国','',$bank);
		}
		if(!in_array($banks,$this->getBank())){$banks='其他银行';}
		return $banks;
	}

	/**
	 * 获取省市区数据
	 * 同微信
	 */
	public function getProCityData(){
		if(IS_POST){
			$res=$this->getProCity(I('post.name'));
			if($res) {
				$this->success($res);
			}else{
				$this->error('获取信息失败');
			}
		}
	}

	/**
	 * 私有方法 默认显示省
	 * @param int $pid
	 * @return mixed
	 */
	protected function  getProCity($pid=1){
		#省份
		$pro = M('ccbCityData')->where(['pid'=>$pid])->distinct(true)->field('name,mid')->select();
		return $pro;
	}

	/**
	 * 微信小微入网开户银行对照表
	 * @return array
	 */
	protected  function  getBank(){
		$arr=[
			'工商银行',
			'交通银行',
			'招商银行',
			'民生银行',
			'中信银行',
			'浦发银行',
			'兴业银行',
			'光大银行',
			'广发银行',
			'平安银行',
			'北京银行',
			'华夏银行',
			'农业银行',
			'建设银行',
			'邮政储蓄银行',
			'中国银行',
			'宁波银行',
			'其他银行',
		];
		return $arr;
	}

	/**
	 * 微信小微入网费率对照表
	 * @return array
	 */
	protected function getRate(){
		$arr=[
			'0.38%',
			'0.39%',
			'0.4%',
			'0.45%',
			'0.48%',
			'0.49%',
			'0.5%',
			'0.55%',
			'0.58%',
			'0.59%',
			'0.6%',
		];
		return $arr;
	}
}