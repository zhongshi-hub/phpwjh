<?php
namespace Pays\Controller;

use Pays\Controller\InitBaseController;

class MchRegController extends InitBaseController
{

	public function _initialize()
	{
		parent::_initialize();
		$aid = M('MchCodes')->where(array('codes' => $_SESSION['Reg']['codes']))->getField('aid');

		#判断系统是否开启认证
		$auth = unserialize($this->system['auth_data']);
		if ($auth['auth_status'] == 1) {
			#开启认证了
			#判断此收款码是否被认证
			$where['codes'] = $_SESSION['Reg']['codes'];
			$where['status'] = 1;
			//$where['openid']=$_SESSION['Reg']['user_info']['openid'];
			$user_auth = M('MchUserAuth')->where($where)->find();
			#获取代理是否开启认证
			$agent = M('MchAgent')->where(array('id' => $aid))->getField('auth_status');
			if ($agent == 1) {
				if ($user_auth) {
					#已经被认证占用
					if ($user_auth['openid'] != $_SESSION['Reg']['user_info']['openid']) {
						$this->error('此收款码已被其他商户认证!请更换收款码注册!', '', 888);
					}
				} else {
					if(ACTION_NAME!='WxNo') {
						#未查询到此收款码的认证信息
						redirect('/Pay/auth');
					}
				}
			}
		}



		$assign = array(
			'industry' => self::industry(),
			'per_industry' => self::industry('企业'),
			'par_industry' => self::industry('个体工商户'),
			'options' => self::jsapi(),
			'bank_list' => self::bank_list(),
		);
		$this->assign($assign);

	}

	#邀请机制 个体商户保存
	public function entensionPerSave(){
		if(extensionSetting('status')!=1){
			$this->error('系统邀请注册已关闭！无法注册！');
		}
		$data = I('post.');
		switch ($data['mch_type']){
			case 'per':
				$mch_type='企业';
				break;
			case 'par':
				$mch_type='个体户';
				break;
			default:
				$mch_type='个人';
				break;

		}
		//rwlog('per_save',$data);
		$db = M('MchSeller');
		unset($data['__TokenHash__']);
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$wx_headimgurl = $_SESSION['Reg']['user_info']['headimgurl'] ? $_SESSION['Reg']['user_info']['headimgurl'] : $data['wx_headimgurl'];
		$wx_nickname = self::wx_filter($_SESSION['Reg']['user_info']['nickname'] ? $_SESSION['Reg']['user_info']['nickname'] : $data['wx_nickname']);
		$wx_openid = $_SESSION['Reg']['user_info']['openid'] ? $_SESSION['Reg']['user_info']['openid'] : $data['wx_openid'];
		$agent_id = extensionSetting('aid');

		$check_verify = self::per_check_verify($data);
		#银行卡鉴权
		$care_data = array(
			'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
			'certNo' => str_replace(' ', '', $data['card_val']), #身份证号
			'name' => $data['bank_name'], #姓名
		);
		#判断此码身份鉴权次数 超过5次当前码就不可以使用
		$card_log = M('CardValidateLog')->where(array('mid' => $data['codes'], 'name' => $data['bank_name']))->count();
		if ($card_log > 5) {
			$this->error('由于您的信息鉴权失败超过5次,当前码ID被冻结!');
		} else {
			#鉴权$this->data['alleys']
			$res = card_validate_calls($care_data, '新户注册', $data['codes']);
			if ($res['status'] == 1) {#鉴权成功
				if ($check_verify) {
					//自动生成收款码ID
					$CodeData = R('Tasks/QrCode/agentCode', [$agent_id, domain_auth()]);
					if ($CodeData['status'] == 1) {
						$codeId = $CodeData['code'];
					} else {
						$this->error($CodeData['msg']);
					}
					$tel = $db->where(array('mch_tel' => $data['telNo'], 'domain_auth' => domain_auth()))->find();
					if ($tel) {
						$this->error('当前手机号已注册商户:' . $tel['mch_name']);
					}

					$set = array(
						'codes' => $codeId,
						'agent_id' => $agent_id,
						'mch_name' => $data['qy_name'],
						'mch_tel' => $data['telNo'],
						'mch_industry' => $data['industry'],
						'mch_provice' => $city[0],
						'mch_citys' => $city[1],
						'mch_district' => $city[2],
						'mch_address' => $data['address'],
						'mch_bus_type' => $mch_type,
						'mch_bank_cid' => str_replace(' ', '', $data['bank_cid']),
						'mch_bank_type' => '个人账户',
						'mch_bank_provice' => $bank_city[0],
						'mch_bank_citys' => $bank_city[1],
						'mch_linkbnk' => $data['linkBnk'],
						'mch_bank_list' => $data['bank_list'],
						'mch_bank_name' => $data['bank_name'],
						'mch_bank_tel' => $data['bank_tel'],
						'mch_card_name' => $data['bank_name'],
						'mch_card_id' => str_replace(' ', '', $data['card_val']),
						'mch_img_z' => $data['img-z'],
						'mch_img_p' => $data['img-p'],
						'mch_img_s' => $data['img-s'],
						'mch_img_sqh' => $data['img-sqh'],
						'mch_img_yyzz' => $data['img-yyzz'],
						'mch_img_bank' => $data['img-bank'],
						'mch_img_m1' => $data['img-m1'],
						'mch_img_m2' => $data['img-m2'],
						'mch_img_m3' => $data['img-m3'],
						'mch_img_m4' => $data['img-m4'],
						'mch_type'=>$data['mch_type'],
						'qy_fr_name'=>$data['qy_fr_name'],
						'qy_fr_cid'=>$data['qy_fr_cid'],
						'mch_img_auth_z'=>$data['img-auth-z'],
						'mch_img_auth_p'=>$data['img-auth-p'],
						'mch_wx_openid' => $wx_openid,
						'mch_wx_name' => $wx_nickname,
						'mch_wx_img' => $wx_headimgurl,
						'credit_card' => $data['credit_card'],
						'qy_name' => $data['qy_name'],
						'qy_cid' => $data['qy_cid'],
						'domain_auth' => domain_auth(),
						'ctime' => time(),
						'status' => 0,
						'type' => 'default'
					);

					#验证通过
					$seller = $db->add($set);

					#自动创建一个门店
					$store = array(
						'sid' => $seller,
						'name' => $data['qy_name'],
						'per_name' => $data['bank_name'],
						'per_phone' => $data['telNo'],
						'uptime' => time(),
						'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
						'domain_auth' => domain_auth(),
						'status' => 1,
					);
					$store_id = M('MchStore')->add($store);
					#保存收款码门店信息
					$_codes = array(
						'mch_id' => $seller,
						'store_id' => $store_id
					);
					M('MchCodes')->where(array('codes' => $codeId))->save($_codes);
					#自动创建一个店员
					$user = array(
						'sid' => $seller,
						'store_id' => $store_id,
						'username' => $data['bank_name'],
						'phone' => $data['telNo'],
						'wx_name' => $wx_nickname,
						'wx_openid' => $wx_openid,
						'wx_imgurl' => $wx_headimgurl,
						'status' => 1,
						'role_wx_temp' => 1,
						'role_order' => 1,
						'domain_auth' => domain_auth(),
						'ctime' => time()
					);
					$rel = M('MchStoreUser')->add($user);
					//创建等级数据
					$gradeArr=[
						'mid'=>$seller,
						'pid'=>$_SESSION['extensionMid'],
						'grade'=>extensionSetting('grade'),
						'create_time'=>time(),
						'update_time'=>time(),
						'domain_auth'=>domain_auth()
					];
					M('extensionMch')->add($gradeArr);

					data_log('Code' . $data['codes'], $data);

					if ($rel) {
						#发送注册成功提醒
						$RegTemplateId=GetPayConfig(domain_auth(), 'reg_template_id');
						$RegTemplateStatus=GetPayConfig(domain_auth(), 'reg_template_status');
						if($RegTemplateStatus==1&&$RegTemplateId){
							sendMchTemplateMessage($seller, 'reg');
						}
						#发送给审核员
						$ShUserTemplateId=GetPayConfig(domain_auth(), 'sh_user_template_id');
						$ShUserTemplateStatus=GetPayConfig(domain_auth(), 'sh_user_template_status');
						if($ShUserTemplateStatus==1&&$ShUserTemplateId){
							sendMchTemplateMessage($seller, 'sh_user');
						}

						$this->success('信息提交成功!', U('mch_status', array('mch_id' => $seller, 'store_id' => $store_id)));
					} else {
						$this->error('信息提交失败!');
					}

				} else {
					$this->error($check_verify);
				}
			} else {
				$this->error($res['msg']);
			}
		}

	}

	#邀请机制 个人商户保存
	public function entensionSoleSave(){
		if(extensionSetting('status')!=1){
			$this->error('系统邀请注册已关闭！无法注册！');
		}
		$data = I('post.');
		$db = M('MchSeller');
		unset($data['__TokenHash__']);
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$wx_headimgurl = $_SESSION['Reg']['user_info']['headimgurl'] ? $_SESSION['Reg']['user_info']['headimgurl'] : $data['wx_headimgurl'];
		$wx_nickname = self::wx_filter($_SESSION['Reg']['user_info']['nickname'] ? $_SESSION['Reg']['user_info']['nickname'] : $data['wx_nickname']);
		$wx_openid = $_SESSION['Reg']['user_info']['openid'] ? $_SESSION['Reg']['user_info']['openid'] : $data['wx_openid'];
		$agent_id = extensionSetting('aid');
		#判断验证码
		$check_verify = self::check_verify($data);
		#判断是否还有鉴权次数

		#银行卡鉴权
		$care_data = array(
			'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
			'certNo' => str_replace(' ', '', $data['card_val']), #身份证号
			'name' => $data['bank_name'], #姓名
		);
		#判断此码身份鉴权次数 超过5次当前码就不可以使用
		$card_log = M('CardValidateLog')->where(array('mid' => $data['codes'], 'name' => $data['bank_name']))->count();
		if ($card_log > 5) {
			$this->error('由于您的信息鉴权失败超过5次,当前码ID被冻结!');
		} else {
			#鉴权$this->data['alleys']
			$res = card_validate_calls($care_data, '新户注册', $data['codes']);
			if ($res['status'] == 1) {#鉴权成功
				if ($check_verify) {
					//自动生成收款码ID
					$CodeData = R('Tasks/QrCode/agentCode', [$agent_id, domain_auth()]);
					if ($CodeData['status'] == 1) {
						$codeId = $CodeData['code'];
					} else {
						$this->error($CodeData['msg']);
					}
					$tel = $db->where(array('mch_tel' => $data['telNo'], 'domain_auth' => domain_auth()))->find();
					if ($tel) {
						$this->error('当前手机号已注册商户:' . $tel['mch_name']);
					}

					$set = array(
						'codes' => $codeId,
						'agent_id' => $agent_id,
						'mch_name' => $data['MchName'],
						'mch_tel' => $data['telNo'],
						'mch_industry' => $data['industry'],
						'mch_provice' => $city[0],
						'mch_citys' => $city[1],
						'mch_district' => $city[2],
						'mch_address' => $data['address'],
						'mch_bus_type' => '个人',
						'mch_bank_cid' => str_replace(' ', '', $data['bank_cid']),
						'mch_bank_type' => '个人账户',
						'mch_bank_provice' => $bank_city[0],
						'mch_bank_citys' => $bank_city[1],
						'mch_linkbnk' => $data['linkBnk'],
						'mch_bank_list' => $data['bank_list'],
						'mch_bank_name' => $data['bank_name'],
						'mch_bank_tel' => $data['bank_tel'],
						'mch_card_name' => $data['bank_name'],
						'mch_card_id' => str_replace(' ', '', $data['card_val']),
						'mch_img_z' => $data['img-z'],
						'mch_img_p' => $data['img-p'],
						'mch_img_s' => $data['img-s'],
						'mch_img_sqh' => $data['img-sqh'],
						'mch_img_yyzz' => $data['img-yyzz'],
						'mch_img_bank' => $data['img-bank'],
						'mch_img_m1' => $data['img-m1'],
						'mch_img_m2' => $data['img-m2'],
						'mch_img_m3' => $data['img-m3'],
						'mch_img_m4' => $data['img-m4'],
						'mch_wx_openid' => $wx_openid,
						'mch_wx_name' => $wx_nickname,
						'mch_wx_img' => $wx_headimgurl,
						'credit_card' => $data['credit_card'],
						'domain_auth' => domain_auth(),
						'ctime' => time(),
						'status' => 0,
						'type' => 'default'
					);

					#验证通过
					$seller = $db->add($set);
					#自动创建一个门店
					$store = array(
						'sid' => $seller,
						'name' => $data['MchName'],
						'per_name' => $data['bank_name'],
						'per_phone' => $data['telNo'],
						'uptime' => time(),
						'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
						'domain_auth' => domain_auth(),
						'status' => 1,
					);
					$store_id = M('MchStore')->add($store);
					#保存收款码门店信息
					$_codes = array(
						'mch_id' => $seller,
						'store_id' => $store_id
					);
					M('MchCodes')->where(array('codes' => $codeId))->save($_codes);
					#自动创建一个店员
					$user = array(
						'sid' => $seller,
						'store_id' => $store_id,
						'username' => $data['bank_name'],
						'phone' => $data['telNo'],
						'wx_name' => $wx_nickname,
						'wx_openid' => $wx_openid,
						'wx_imgurl' => $wx_headimgurl,
						'status' => 1,
						'role_wx_temp' => 1,
						'role_order' => 1,
						'domain_auth' => domain_auth(),
						'ctime' => time()
					);
					$rel = M('MchStoreUser')->add($user);
					//创建等级数据
					$gradeArr=[
						'mid'=>$seller,
						'pid'=>$_SESSION['extensionMid'],
						'grade'=>extensionSetting('grade'),
						'create_time'=>time(),
						'update_time'=>time(),
						'domain_auth'=>domain_auth()
					];
					M('extensionMch')->add($gradeArr);
					data_log('Code' . $codeId, $data);
					if ($rel) {
						#发送注册成功提醒
						$RegTemplateId = GetPayConfig(domain_auth(), 'reg_template_id');
						$RegTemplateStatus = GetPayConfig(domain_auth(), 'reg_template_status');
						if ($RegTemplateStatus == 1 && $RegTemplateId) {
							sendMchTemplateMessage($seller, 'reg');
						}
						#发送给审核员
						$ShUserTemplateId = GetPayConfig(domain_auth(), 'sh_user_template_id');
						$ShUserTemplateStatus = GetPayConfig(domain_auth(), 'sh_user_template_status');
						if ($ShUserTemplateStatus == 1 && $ShUserTemplateId) {
							sendMchTemplateMessage($seller, 'sh_user');
						}
						$this->success('信息提交成功!', U('mch_status', array('mch_id' => $seller, 'store_id' => $store_id)));
					} else {
						$this->error('信息提交失败!');
					}
				}else{
					$this->error($check_verify);
				}
			} else {
				$this->error($res['msg']);
			}
		}
	}


	#商户资料不规范,重新修改提交
	public function EditData(){

	}


	#审核进度
	public function mch_reg_status()
	{
		$data = I('post.');
		#查找商户是否存在
		$where['domain_auth'] = domain_auth();
		$where['per_phone'] = $data['tel'];
		$res = M('MchStore')->where($where)->find();
		if ($res) {
			$url = U('mch_status', array('mch_id' => $res['sid'], 'store_id' => $res['id']));
			$this->success($url);
		} else {
			$this->error('未找到此手机号的申请记录');
		}
	}

	#企业商户申请保存
	public function per_save()
	{
		$data = I('post.');
		switch ($data['mch_type']){
			case 'per':
				$mch_type='企业';
				break;
			case 'par':
				$mch_type='小微';
				break;
			default:
				$mch_type='快速';
				break;

		}

		//rwlog('per_save',$data);
		$db = M('MchSeller');
		unset($data['__TokenHash__']);
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$wx_headimgurl = $_SESSION['Reg']['user_info']['headimgurl'] ? $_SESSION['Reg']['user_info']['headimgurl'] : $data['wx_headimgurl'];
		$wx_nickname = self::wx_filter($_SESSION['Reg']['user_info']['nickname'] ? $_SESSION['Reg']['user_info']['nickname'] : $data['wx_nickname']);
		$wx_openid = $_SESSION['Reg']['user_info']['openid'] ? $_SESSION['Reg']['user_info']['openid'] : $data['wx_openid'];
		$agent_id = M('MchCodes')->where(array('codes' => $data['codes']))->getField('aid');
		$set = array(
			'codes' => $data['codes'],
			'agent_id' => $agent_id,
			'mch_name' => $data['qy_name'],
			'mch_tel' => $data['telNo'],
			'mch_industry' => 158,
			'mch_provice' => $city[0],
			'mch_citys' => $city[1],
			'mch_district' => $city[2],
			'mch_address' => $data['address'],
			'mch_bus_type' => $mch_type,
			'mch_bank_cid' => str_replace(' ', '', $data['bank_cid']),
			'mch_bank_type' => $data['mch_bank_type']=='企业账户'?'企业账户':$data['mch_bank_type'],
			'mch_bank_type_s' => $data['mch_bank_type_s'],
			'mch_bank_provice' => $bank_city[0],
			'mch_bank_citys' => $bank_city[1],
			'mch_linkbnk' => $data['linkBnk'],
			'mch_bank_list' => $data['bank_list'],
			'mch_bank_name' => $data['mch_bank_type']=='企业账户'?$data['qy_name']:($data['mch_bank_type_s']==1?$data['bank_name']:$data['qy_fr_name']),
			'mch_bank_tel' => $data['bank_tel'],
			'mch_card_name' => $data['qy_fr_name'],
			'mch_card_id' => $data['qy_fr_cid'],
			'mch_img_z' => $data['img-z'],
			'mch_img_p' => $data['img-p'],
			'mch_img_s' => $data['img-s'],
			'mch_img_sqh' => $data['img-sqh'],
			'mch_img_yyzz' => $data['img-yyzz'],
			'mch_img_bank' => $data['img-bank'],
			'mch_img_m1' => $data['img-m1'],
			'mch_img_m2' => $data['img-m2'],
			'mch_img_m3' => $data['img-m3'],
			'mch_img_m4' => $data['img-m4'],
			'mch_img_m5' => $data['img-m5'],
			'card_time' => $data['card_time'],
			'qy_time' => $data['qy_time'],
			'mch_type'=>$data['mch_type'],
			'qy_fr_name'=>$data['qy_fr_name'],
			'qy_fr_cid'=>$data['qy_fr_cid'],
			'mch_img_auth_z'=>$data['img-auth-z'],
			'mch_img_auth_p'=>$data['img-auth-p'],
			'mch_wx_openid' => $wx_openid,
			'mch_wx_name' => $wx_nickname,
			'mch_wx_img' => $wx_headimgurl,
			'credit_card' => $data['credit_card'],
			'qy_name' => $data['qy_name'],
			'qy_cid' => $data['qy_cid'],
			'domain_auth' => domain_auth(),
			'ctime' => time(),
			'status' => 0,
			'type' => 'default'
		);
		$check_verify = self::per_check_verify($data);
		#银行卡鉴权
		if($data['mch_bank_type_s']==1){
			$care_data = array(
				'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
				'certNo' => str_replace(' ', '', $data['card_val']), #身份证号
				'name' =>$data['bank_name'], #姓名
			);
		}else{
			$care_data = array(
				'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
				'certNo' => str_replace(' ', '', $data['qy_fr_cid']), #身份证号
				'name' =>$data['qy_fr_name'], #姓名
			);
		}

		#判断此码身份鉴权次数 超过5次当前码就不可以使用
		$card_log = M('CardValidateLog')->where(array('mid' => $data['codes'], 'name' => $data['bank_name']))->count();
		if ($card_log > 5) {
			$this->error('由于您的信息鉴权失败超过5次,当前码ID被冻结!');
		} else {
			#鉴权$this->data['alleys']
			if($data['mch_bank_type']!='个人账户'){
				$res=[
					'status'=>1
				];
			}else {
				$res = card_validate_calls($care_data, '新户注册', $data['codes']);
			}
			if ($res['status'] == 1) {#鉴权成功
				if ($check_verify) {
					#判断唯一规则
					$code = M('MchCodes')->where(array('codes' => $data['codes'], 'domain_auth' => domain_auth()))->find();
					if (!empty($code['mch_id']) || !empty($code['store_id'])) {
						$this->error('此收款码已被注册!');
					}
					$tel = $db->where(array('mch_tel' => $data['telNo'], 'domain_auth' => domain_auth()))->find();
					if ($tel) {
						$this->error('当前手机号已注册商户:' . $tel['mch_name']);
					}

					rwlog('per_save',$set);

					#验证通过
					$seller = $db->add($set);
					rwlog('per_save',$seller);

					#自动创建一个门店
					$store = array(
						'sid' => $seller,
						'name' => $data['qy_name'],
						'per_name' => $data['bank_name'],
						'per_phone' => $data['telNo'],
						'uptime' => time(),
						'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
						'domain_auth' => domain_auth(),
						'status' => 1,
					);
					$store_id = M('MchStore')->add($store);
					rwlog('per_save',$store_id);
					#保存收款码门店信息
					$_codes = array(
						'mch_id' => $seller,
						'store_id' => $store_id
					);
					M('MchCodes')->where(array('codes' => $data['codes']))->save($_codes);
					#自动创建一个店员
					$user = array(
						'sid' => $seller,
						'store_id' => $store_id,
						'username' => $data['bank_name'],
						'phone' => $data['telNo'],
						'wx_name' => $wx_nickname,
						'wx_openid' => $wx_openid,
						'wx_imgurl' => $wx_headimgurl,
						'status' => 1,
						'role_wx_temp' => 1,
						'role_order' => 1,
						'domain_auth' => domain_auth(),
						'ctime' => time()
					);
					$rel = M('MchStoreUser')->add($user);
					data_log('Code' . $data['codes'], $data);

					if ($rel) {
						#发送注册成功提醒
						$RegTemplateId=GetPayConfig(domain_auth(), 'reg_template_id');
						$RegTemplateStatus=GetPayConfig(domain_auth(), 'reg_template_status');
						if($RegTemplateStatus==1&&$RegTemplateId){
							sendMchTemplateMessage($seller, 'reg');
						}
						#发送给审核员
						$ShUserTemplateId=GetPayConfig(domain_auth(), 'sh_user_template_id');
						$ShUserTemplateStatus=GetPayConfig(domain_auth(), 'sh_user_template_status');
						if($ShUserTemplateStatus==1&&$ShUserTemplateId){
							sendMchTemplateMessage($seller, 'sh_user');
						}

						$this->success('信息提交成功!', U('mch_status', array('mch_id' => $seller, 'store_id' => $store_id)));
					} else {
						$this->error('信息提交失败!');
					}

				} else {
					$this->error($check_verify);
				}
			} else {
				$this->error($res['msg']);
			}
		}

	}


	#小微商户申请保存
	public function par_save()
	{
		$data = I('post.');
		switch ($data['mch_type']){
			case 'per':
				$mch_type='企业';
				break;
			case 'par':
				$mch_type='小微';
				break;
			default:
				$mch_type='快速';
				break;
		}
		//rwlog('per_save',$data);
		$db = M('MchSeller');
		unset($data['__TokenHash__']);
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$wx_headimgurl = $_SESSION['Reg']['user_info']['headimgurl'] ? $_SESSION['Reg']['user_info']['headimgurl'] : $data['wx_headimgurl'];
		$wx_nickname = self::wx_filter($_SESSION['Reg']['user_info']['nickname'] ? $_SESSION['Reg']['user_info']['nickname'] : $data['wx_nickname']);
		$wx_openid = $_SESSION['Reg']['user_info']['openid'] ? $_SESSION['Reg']['user_info']['openid'] : $data['wx_openid'];
		$agent_id = M('MchCodes')->where(array('codes' => $data['codes']))->getField('aid');
		$set = array(
			'codes' => $data['codes'],
			'agent_id' => $agent_id,
			'mch_name' => $data['MchName'],
			'mch_tel' => $data['telNo'],
			'mch_industry' => 158,
			'mch_provice' => $city[0],
			'mch_citys' => $city[1],
			'mch_district' => $city[2],
			'mch_address' => $data['address'],
			'mch_bus_type' => $mch_type,
			'mch_bank_cid' => str_replace(' ', '', $data['bank_cid']),
			'mch_bank_type' => '个人账户',
			'mch_bank_provice' => $bank_city[0],
			'mch_bank_citys' => $bank_city[1],
			'mch_linkbnk' => $data['linkBnk'],
			'mch_bank_list' => $data['bank_list'],
			'mch_bank_name' => $data['bank_name'],
			'mch_bank_tel' => $data['bank_tel'],
			'mch_card_name' => $data['bank_name'],
			'mch_card_id' => str_replace(' ', '', $data['card_val']),
			'mch_img_z' => $data['img-z'],
			'mch_img_p' => $data['img-p'],
			'mch_img_s' => $data['img-s'],
			'mch_img_sqh' => $data['img-sqh'],
			'mch_img_yyzz' => $data['img-yyzz'],
			'mch_img_bank' => $data['img-bank'],
			'mch_img_m1' => $data['img-m1'],
			'mch_img_m2' => $data['img-m2'],
			'mch_img_m3' => $data['img-m3'],
			'mch_img_m4' => $data['img-m4'],
			'mch_img_m5' => $data['img-m5'],
			'card_time' => $data['card_time'],
			'qy_time' => $data['qy_time'],
			'mch_type'=>$data['mch_type'],
			'qy_fr_name'=>$data['qy_fr_name'],
			'qy_fr_cid'=>$data['qy_fr_cid'],
			'mch_img_auth_z'=>$data['img-auth-z'],
			'mch_img_auth_p'=>$data['img-auth-p'],
			'mch_wx_openid' => $wx_openid,
			'mch_wx_name' => $wx_nickname,
			'mch_wx_img' => $wx_headimgurl,
			'credit_card' => $data['credit_card'],
			'qy_name' => $data['qy_name'],
			'qy_cid' => $data['qy_cid'],
			'domain_auth' => domain_auth(),
			'ctime' => time(),
			'status' => 0,
			'type' => 'default'
		);
		$check_verify = self::check_verify($data);
		#银行卡鉴权
		$care_data = array(
			'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
			'certNo' => str_replace(' ', '', $data['card_val']), #身份证号
			'name' => $data['bank_name'], #姓名
		);
		#判断此码身份鉴权次数 超过5次当前码就不可以使用
		$card_log = M('CardValidateLog')->where(array('mid' => $data['codes'], 'name' => $data['bank_name']))->count();
		if ($card_log > 5) {
			$this->error('由于您的信息鉴权失败超过5次,当前码ID被冻结!');
		} else {
			#鉴权$this->data['alleys']
			$res = card_validate_calls($care_data, '新户注册', $data['codes']);
			if ($res['status'] == 1) {#鉴权成功
				if ($check_verify) {
					#判断唯一规则
					$code = M('MchCodes')->where(array('codes' => $data['codes'], 'domain_auth' => domain_auth()))->find();
					if (!empty($code['mch_id']) || !empty($code['store_id'])) {
						$this->error('此收款码已被注册!');
					}
					$tel = $db->where(array('mch_tel' => $data['telNo'], 'domain_auth' => domain_auth()))->find();
					if ($tel) {
						$this->error('当前手机号已注册商户:' . $tel['mch_name']);
					}

					rwlog('par_save',$set);

					#验证通过
					$seller = $db->add($set);
					rwlog('par_save',$seller);

					#自动创建一个门店
					$store = array(
						'sid' => $seller,
						'name' => $data['MchName'],
						'per_name' => $data['bank_name'],
						'per_phone' => $data['telNo'],
						'uptime' => time(),
						'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
						'domain_auth' => domain_auth(),
						'status' => 1,
					);
					$store_id = M('MchStore')->add($store);
					rwlog('par_save',$store_id);
					#保存收款码门店信息
					$_codes = array(
						'mch_id' => $seller,
						'store_id' => $store_id
					);
					M('MchCodes')->where(array('codes' => $data['codes']))->save($_codes);
					#自动创建一个店员
					$user = array(
						'sid' => $seller,
						'store_id' => $store_id,
						'username' => $data['bank_name'],
						'phone' => $data['telNo'],
						'wx_name' => $wx_nickname,
						'wx_openid' => $wx_openid,
						'wx_imgurl' => $wx_headimgurl,
						'status' => 1,
						'role_wx_temp' => 1,
						'role_order' => 1,
						'domain_auth' => domain_auth(),
						'ctime' => time()
					);
					$rel = M('MchStoreUser')->add($user);
					data_log('Code' . $data['codes'], $data);

					if ($rel) {
						#发送注册成功提醒
						$RegTemplateId=GetPayConfig(domain_auth(), 'reg_template_id');
						$RegTemplateStatus=GetPayConfig(domain_auth(), 'reg_template_status');
						if($RegTemplateStatus==1&&$RegTemplateId){
							sendMchTemplateMessage($seller, 'reg');
						}
						#发送给审核员
						$ShUserTemplateId=GetPayConfig(domain_auth(), 'sh_user_template_id');
						$ShUserTemplateStatus=GetPayConfig(domain_auth(), 'sh_user_template_status');
						if($ShUserTemplateStatus==1&&$ShUserTemplateId){
							sendMchTemplateMessage($seller, 'sh_user');
						}

						$this->success('信息提交成功!', U('mch_status', array('mch_id' => $seller, 'store_id' => $store_id)));
					} else {
						$this->error('信息提交失败!');
					}

				} else {
					$this->error($check_verify);
				}
			} else {
				$this->error($res['msg']);
			}
		}

	}

	#个人申请保存
	public function sole_save()
	{
		$data = I('post.');
		$db = M('MchSeller');
		unset($data['__TokenHash__']);
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$wx_headimgurl = $_SESSION['Reg']['user_info']['headimgurl'] ? $_SESSION['Reg']['user_info']['headimgurl'] : $data['wx_headimgurl'];
		$wx_nickname = self::wx_filter($_SESSION['Reg']['user_info']['nickname'] ? $_SESSION['Reg']['user_info']['nickname'] : $data['wx_nickname']);
		$wx_openid = $_SESSION['Reg']['user_info']['openid'] ? $_SESSION['Reg']['user_info']['openid'] : $data['wx_openid'];
		$agent_id = M('MchCodes')->where(array('codes' => $data['codes']))->getField('aid');
		$set = array(
			'codes' => $data['codes'],
			'agent_id' => $agent_id,
			'mch_name' => $data['MchName'],
			'mch_tel' => $data['telNo'],
			'mch_industry' => $data['industry'],
			'mch_provice' => $city[0],
			'mch_citys' => $city[1],
			'mch_district' => $city[2],
			'mch_address' => $data['address'],
			'mch_bus_type' => '快速',
			'mch_bank_cid' => str_replace(' ', '', $data['bank_cid']),
			'mch_bank_type' => '个人账户',
			'mch_bank_provice' => $bank_city[0],
			'mch_bank_citys' => $bank_city[1],
			'mch_linkbnk' => $data['linkBnk'],
			'mch_bank_list' => $data['bank_list'],
			'mch_bank_name' => $data['bank_name'],
			'mch_bank_tel' => $data['bank_tel'],
			'mch_card_name' => $data['bank_name'],
			'mch_card_id' => str_replace(' ', '', $data['card_val']),
			'mch_img_z' => $data['img-z'],
			'mch_img_p' => $data['img-p'],
			'mch_img_s' => $data['img-s'],
			'mch_img_sqh' => $data['img-sqh'],
			'mch_img_yyzz' => $data['img-yyzz'],
			'mch_img_bank' => $data['img-bank'],
			'mch_img_m1' => $data['img-m1'],
			'mch_img_m2' => $data['img-m2'],
			'mch_img_m3' => $data['img-m3'],
			'mch_img_m4' => $data['img-m4'],
			'mch_img_m5' => $data['img-m5'],
			'card_time'=>$data['card_time'],
			'qy_time'=>$data['qy_time'],
			'mch_wx_openid' => $wx_openid,
			'mch_wx_name' => $wx_nickname,
			'mch_wx_img' => $wx_headimgurl,
			'credit_card' => $data['credit_card'],
			'domain_auth' => domain_auth(),
			'ctime' => time(),
			'status' => 0,
			'type' => 'default'
		);
		//rwlog($data['codes'],$data);
		#判断验证码
		$check_verify = self::check_verify($data);
		#判断是否还有鉴权次数

		#银行卡鉴权
		$care_data = array(
			'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
			'certNo' => str_replace(' ', '', $data['card_val']), #身份证号
			'name' => $data['bank_name'], #姓名
		);
		#判断此码身份鉴权次数 超过5次当前码就不可以使用
		$card_log = M('CardValidateLog')->where(array('mid' => $data['codes'], 'name' => $data['bank_name']))->count();
		if ($card_log > 5) {
			$this->error('由于您的信息鉴权失败超过5次,当前码ID被冻结!');
		} else {
			#鉴权$this->data['alleys']
			$res = card_validate_calls($care_data, '新户注册', $data['codes']);
			if ($res['status'] == 1) {#鉴权成功
				if ($check_verify) {
					#判断唯一规则
					$code = M('MchCodes')->where(array('codes' => $data['codes'], 'domain_auth' => domain_auth()))->find();
					if (!empty($code['mch_id']) || !empty($code['store_id'])) {
						$this->error('此收款码已被注册!');
					}
					$tel = $db->where(array('mch_tel' => $data['telNo'], 'domain_auth' => domain_auth()))->find();
					if ($tel) {
						$this->error('当前手机号已注册商户:' . $tel['mch_name']);
					}

					#验证通过
					$seller = $db->add($set);

					#自动创建一个门店
					$store = array(
						'sid' => $seller,
						'name' => $data['MchName'],
						'per_name' => $data['bank_name'],
						'per_phone' => $data['telNo'],
						'uptime' => time(),
						'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
						'domain_auth' => domain_auth(),
						'status' => 1,
					);
					$store_id = M('MchStore')->add($store);
					#保存收款码门店信息
					$_codes = array(
						'mch_id' => $seller,
						'store_id' => $store_id
					);
					M('MchCodes')->where(array('codes' => $data['codes']))->save($_codes);
					#自动创建一个店员
					$user = array(
						'sid' => $seller,
						'store_id' => $store_id,
						'username' => $data['bank_name'],
						'phone' => $data['telNo'],
						'wx_name' => $wx_nickname,
						'wx_openid' => $wx_openid,
						'wx_imgurl' => $wx_headimgurl,
						'status' => 1,
						'role_wx_temp' => 1,
						'role_order' => 1,
						'domain_auth' => domain_auth(),
						'ctime' => time()
					);
					$rel = M('MchStoreUser')->add($user);
					data_log('Code' . $data['codes'], $data);
					/*rwlog($data['codes'],$data);
					rwlog($data['codes'],$set);
					rwlog($data['codes'],$store);
					rwlog($data['codes'],$user);
					rwlog($data['codes'],$_SESSION);*/
					if ($rel) {
						#发送注册成功提醒
						$RegTemplateId=GetPayConfig(domain_auth(), 'reg_template_id');
						$RegTemplateStatus=GetPayConfig(domain_auth(), 'reg_template_status');
						if($RegTemplateStatus==1&&$RegTemplateId){
							sendMchTemplateMessage($seller, 'reg');
						}
						#发送给审核员
						$ShUserTemplateId=GetPayConfig(domain_auth(), 'sh_user_template_id');
						$ShUserTemplateStatus=GetPayConfig(domain_auth(), 'sh_user_template_status');
						if($ShUserTemplateStatus==1&&$ShUserTemplateId){
							sendMchTemplateMessage($seller, 'sh_user');
						}
						$this->success('信息提交成功!', U('mch_status', array('mch_id' => $seller, 'store_id' => $store_id)));
					} else {
						$this->error('信息提交失败!');
					}
				} else {
					$this->error($check_verify);
				}

			} else {

				$this->error($res['msg']);
			}


		}

	}


	#商户状态
	public function mch_status()
	{
		$mch_id = I('get.mch_id');
		$store_id = I('get.store_id');
		if (!$mch_id || !$store_id) {
			$this->error('参数有误!', '', 888);
		}
		$map['domain_auth'] = domain_auth();
		$map['id'] = $mch_id;
		$seller = M('MchSeller')->where($map)->field('id,mch_name,status,info,auth_status')->find();
		$where['store_id'] = $store_id;
		$where['mch_id'] = $mch_id;
		$codes = M('MchCodes')->where($where)->getField('codes');

		if (!$seller) {
			$this->error('参数有误或商户不存在!如有疑问!请联系您的业务员!', '', 888);
		}
		$assign = array(
			'seller' => $seller,
			'codes' => $codes,
			'store_id' => $store_id,
		);
		$this->assign($assign);
		$this->display();
	}


	#注册 第一步
	public function index()
	{
		$this->display();
	}


	#个人
	public function MchSole()
	{
		$this->display();
	}

	#企业
	public function MchPer()
	{
		$this->display();
	}

	#个体
	public function MchPar(){
		$this->display();
	}


	#省份列表
	public function test()
	{
		$res = M('ccb_city_data')->where(array('pid' => 1))->field('name')->select();
		dump($res);
	}

	#银行列表
	public function bank_list()
	{
		$bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
		return $bank_list;
	}

	#行业类别
	public function industry($type=null)
	{
		$res = M('mch_industry')->where(array('name'=>array('like','%'.$type.'%')))->distinct(true)->field('pid,name')->order('name asc')->select();
		return $res;
	}


	#微信JSAPI
	public function jsapi()
	{
		$wid = GetWxId('m');
		// 创建SDK实例
		$script = &load_wechat('Script', $wid);
		$options = $script->getJsSign(get_url(), $timestamp, $noncestr, $appid);
		if ($options === FALSE) {
			// 接口失败的处理
			return $script->errMsg;
		} else {

			unset($options['jsApiList']);
			$options['jsApiList'] = array('chooseImage', 'uploadImage');
			return $options;
		}
	}


	#验证码
	public function per_check_verify($data)
	{
		#判断必填项
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$text_len = self::text_len($data['qy_name']);
		$store_name = M('MchStore')->where(array('name' => $data['qy_name']))->count();
		if ($store_name) {
			$this->error('您输入的企业名称已存在!请更换后重试!');
		}
		if ($text_len < 5) {
			$this->error('企业名称不能小于5个汉字');
		}
		if (!$city[0] || $city[0] == '请选择') {
			$this->error('请选择所在城市-省份');
		}
		if (!$city[1] || $city[1] == '请选择') {
			$this->error('请选择所在城市-城市');
		}
		if (!$city[2] || $city[2] == '请选择') {
			$this->error('请选择所在城市-区县');
		}
		if (!$bank_city[0] || $bank_city[0] == '请选择') {
			$this->error('请选择开户行所在城市-省份');
		}
		if (!$bank_city[1] || $bank_city[1] == '请选择') {
			$this->error('请选择开户行所在城市-城市');
		}
		if (!$data['bank_list']) {
			$this->error('请选择开户银行');
		}
		if (!$data['linkBnk']) {
			$this->error('请选择开户支行');
		}
		if (!$data['img-z']) {
			$this->error('请上传法人身份证(正面)');
		}
		if (!$data['img-p']) {
			$this->error('请上传法人身份证(反面)');
		}
		if (!$data['img-yyzz']) {
			$this->error('请上传三证合一营业执照');
		}

		if (!$data['img-m1']) {
			$this->error('请上传门头照片');
		}
		if (!$data['img-m2']) {
			$this->error('请上传内景照片');
		}
		if (!$data['img-m3']) {
			$this->error('请上传收银台照片');
		}
//		if (!$data['img-s']) {
//			$this->error('请上传手持身份证照片');
//		}
//		if($data['mch_type']=='per') {
//			if (!$data['img-auth-z']) {
//				$this->error('请上传授权收款人身份证(正面)照片');
//			}
//			if (!$data['img-auth-p']) {
//				$this->error('请上传授权收款人身份证(反面)照片');
//			}
//			if (!$data['img-sqh']) {
//				$this->error('请上传收款人授权函照片');
//			}
//		}

		if($data['mch_bank_type']=='个人账户'){
			if($data['mch_bank_type_s']==1){
				//非法人结算
				if (!$data['img-auth-z']) {
					$this->error('请上传授权收款人身份证(正面)照片');
				}
				if (!$data['img-auth-p']) {
					$this->error('请上传授权收款人身份证(反面)照片');
				}
				if (!$data['img-sqh']) {
					$this->error('请上传收款人授权函照片');
				}
				if (!$data['bank_name']){
					$this->error('请输入开户姓名');
				}
				if (!$data['card_val']){
					$this->error('请输入结算人身份证号');
				}
				if (!$data['bank_tel']){
					$this->error('请输入结算人预留手机号');
				}
			}else{
				//法人结算
				if (!$data['bank_tel']){
					$this->error('请输入结算人预留手机号');
				}
			}

			if (!$data['img-bank']) {
				$this->error('请上传银行卡正面照片');
			}
		}else{
			if (!$data['img-bank']) {
				$this->error('请上传开户许可证照片');
			}
		}
		#判断验证码
		$where['cardsn'] = $data['codes'];
		$where['tel'] = $data['telNo'];
		$where['verify'] = $data['verify'];
		$where['domain_auth'] = domain_auth();
		$_res = M('MchVerify')->where($where)->find();
		$_c = time();
		$_e = $_res['createtime'];
		$minute = floor(($_c - $_e) % 86400 / 60);
		$out_times = 10;
		//测试专用验证码
		if ($data['verify'] == '162652') {
			return true;
		} else {
			if ($_res) {
				if ($minute > $out_times) {
					$this->error('验证码已过期,请重新获取');
				} else {
					return true;
				}
			} else {
				$this->error('验证码错误');
			}
		}
	}


	#验证码
	public function check_verify($data)
	{
		#判断必填项
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$text_len = self::text_len($data['MchName']);

		$store_name = M('MchStore')->where(array('name' => $data['MchName']))->count();
		if ($store_name) {
			$this->error('您输入的商户名称已存在!请更换后重试!');
		}

		if ($text_len < 5) {
			$this->error('商户名称不能小于5个汉字');
		}

		if (!$city[0] || $city[0] == '请选择') {
			$this->error('请选择所在城市-省份');
		}
		if (!$city[1] || $city[1] == '请选择') {
			$this->error('请选择所在城市-城市');
		}
		if (!$city[2] || $city[2] == '请选择') {
			$this->error('请选择所在城市-区县');
		}

		if (!$bank_city[0] || $bank_city[0] == '请选择') {
			$this->error('请选择开户行所在城市-省份');
		}
		if (!$bank_city[1] || $bank_city[1] == '请选择') {
			$this->error('请选择开户行所在城市-城市');
		}
		if (!$data['bank_list']) {
			$this->error('请选择开户银行');
		}
		if (!$data['linkBnk']) {
			$this->error('请选择开户支行');
		}
		if (!$data['img-z']) {
			$this->error('请上传身份证(正面)');
		}
		if (!$data['img-p']) {
			$this->error('请上传身份证(反面)');
		}
//		if (!$data['img-s']) {
//			$this->error('请上传手持身份证照片');
//		}
		if (!$data['img-bank']) {
			$this->error('请上传银行卡正面照片');
		}
		if($data['mch_type']=='par') {
			if (!$data['img-m1']) {
				$this->error('请上传结算人站门头照片');
			}
			if (!$data['img-m2']) {
				$this->error('请上传门店内景照片');
			}
			if (!$data['img-m3']) {
				$this->error('请上传收银台照片');
			}
			if (!$data['img-s']) {
				$this->error('请上传手持身份证照片');
			}
		}


		#判断验证码
		$where['cardsn'] = $data['codes'];
		$where['tel'] = $data['telNo'];
		$where['verify'] = $data['verify'];
		$where['domain_auth'] = domain_auth();
		$_res = M('MchVerify')->where($where)->find();
		$_c = time();
		$_e = $_res['createtime'];
		$minute = floor(($_c - $_e) % 86400 / 60);
		$out_times = 10;
		//测试专用验证码
		if ($data['verify'] == '162652') {
			return true;
		} else {
			if ($_res) {
				if ($minute > $out_times) {
					$this->error('验证码已过期,请重新获取');
				} else {
					return true;
				}
			} else {
				$this->error('验证码错误');
			}
		}
	}

	public function wx_filter($str)
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


	public function text_len($data)
	{
		$_data = mb_strlen($data);
		return ($_data);
	}


}