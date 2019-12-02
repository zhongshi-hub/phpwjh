<?php
/**
 * API用户统一登录接口
 * auth: chencunlong
 * email:chencunlong@126.com
 */
namespace Pays\Controller;
use Think\Controller;
class FaceUserController extends Controller
{
	protected  $post;
	protected  $key;
	public function _initialize(){
		#基础验证 请求协议限制
		if(C('API_CONFIG.IS_HTTPS')==true){
			if(!self::isHttps())$this->json_data(400,'请使用https协议通信'); //非Https直接输出
		}
		#独立Api域名限制
		if(C('API_CONFIG.IS_API_DOMAIN')==true&&C('API_CONFIG.API_DOMAIN')){ //启用独立Api域名
			if($_SERVER['HTTP_HOST']!=C('API_CONFIG.API_DOMAIN'))$this->json_data(400,'接口域名错误,请联系相关人员!');
		}
		$this->key='zzxunlong';//密码安全码  勿乱改
		$this->post = json_decode(file_get_contents("php://input"), true);
		if(!is_array($this->post)){
			$this->json_data(400,'请求数据格式不正确');
		}
	}



	/**
	 * 修改密码
	 */
	public function restPassword(){
		$post=$this->post;
		if(!array_key_exists('tel',$post)){
			$this->json_data(400,'商户手机号不可为空');
		}
		if(!array_key_exists('verify',$post)){
			$this->json_data(400,'验证码不可为空');
		}
		if(!array_key_exists('new_pass',$post)){
			$this->json_data(400,'密码不可为空');
		}
		if(!array_key_exists('end_pass',$post)){
			$this->json_data(400,'确认密码不可为空');
		}
		if($post['new_pass']!=$post['end_pass']){
			$this->json_data(400,'新密码和确认密码不一致');
		}
		if(empty($post['new_pass'])){
			$this->json_data(400,'请输入新密码');
		}
		if(strlen($post['new_pass'])<6){
			$this->json_data(400,'新密码长度为6-20之间');
		}
		$isVerify=$this->check_verify($post['tel'],$post['verify']);
		if(false==$isVerify){
			$this->json_data(400,'验证码无效或已过期');
		}else{
            //验证码有效 进行密码修改
			$pass=md5($post['new_pass']);
			$seller = M('MchSeller')->where(array('mch_tel' => $post['tel'], 'domain_auth' => domain_auth()))->find();
			if($seller){
				M('MchSeller')->where(array('mch_tel' => $post['tel'], 'domain_auth' => domain_auth()))->save(array('password' => $pass));
				return $this->json_data(100,'密码修改成功');
			}else{
			   return	$this->json_data(400,'密码修改失败');
			}
		}
	}

	/**
	 * 效验验证码
	 * @param $tel
	 * @param $verify
	 * @return bool
	 */
	public function check_verify($tel,$verify){
		$cacheId='restPass_'.domain_auth().$tel;
		if(S($cacheId)!=$verify||S($cacheId)==false){
			return false;
		}else {
			return true;
		}
	}

	/**
	 * 获取修改密码验证码
	 */
	public function passVerify(){
		$post=$this->post;
		if(!array_key_exists('tel',$post)){
			$this->json_data(400,'商户手机号不可为空');
		}
		$seller = M('MchSeller')->where(array('mch_tel' => $post['tel'], 'domain_auth' => domain_auth()))->find();
		if ($seller) {
			$cacheId='restPass_'.domain_auth().$post['tel'];
			$code= RandStr(6);
			$sms = ALI_SMS();
			if (sms_api() == 1) { #用阿里云通信接口
				$sms_data = array(
					'mobile' => $post['tel'], #接收手机号
					'code' => $sms['sms_pass'],#验证码模板ID
					'sign' => $sms['sms_sign'], #模板签名 必需审核通过
					'param' => json_encode(array(
						'code' => $code, #验证码
					)),
				);
				$re = new_ali_sms($sms_data);
				if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
					S($cacheId,$code,600);
					return $this->json_data(100, '验证码发送成功');
				} else {
					return $this->json_data(400, $re['Message'].'('.$re['Code'].')');
				}
			}else {//兼容旧阿里大于短信
				$sms_data = array(
					'mobile' => $post['tel'], #接收手机号
					'code' => $sms['sms_pass'],#验证码模板ID
					'sign' => $sms['sms_sign'], #模板签名 必需审核通过
					'param' => json_encode(array(
						'code' => $code, #验证码
						'product' => '商户',#模板变量
					)),
				);
				$AliSms = new \Think\Alisms($sms);
				$re = $AliSms->sms_send($sms_data);
				if ($re['err_code'] == 0 && $re['success'] == true) {
					S($cacheId,$code,600);
					return $this->json_data(100, '发送成功,有效期10分钟');
				} else {
//					if($re['code']==15){
//						return $this->json_data(400, '发送失败,请于一分钟后再点击发送!');
//					}else {
						return $this->json_data(400, $re['sub_msg'] . '(' . $re['code'] . ')');
					//}
				}
			}
		}else{
			return $this->json_data(400,'商户手机号无效');
		}
	}

	/**
	 * 商户登录API
	 */
	public function login(){
		$post=$this->post;
		if(!array_key_exists('type',$post)){
			$this->json_data(400,'用户类型字段不需为空');
		}
		if(!array_key_exists('tel',$post)){
			$this->json_data(400,'用户手机号字段不需为空');
		}
		if(!array_key_exists('pass',$post)){
			$this->json_data(400,'用户密码字段不需为空');
		}
		if(!array_key_exists('terminal',$post)){
			$this->json_data(400,'终端类型字段不需为空');
		}
		//判断是店员还是商户
		$userType=explode('@',$post['tel']);
		if(!empty($userType[1])){
			//店员登录
			$storeUser = M('MchStoreUser')->where(array('store_id'=>$userType[0],'phone' => $userType[1], 'domain_auth' => domain_auth()))->find();
			if($storeUser){
				//验证密码
				if (empty($storeUser['password'])) {
					$password = strtoupper(md5(md5(123456) . $this->key));
				} else {
					$password = strtoupper(md5($storeUser['password'] . $this->key));
				}
				if ($post['pass'] == $password) {
					if ($storeUser['status'] == 1) {
						$store = M('MchStore')->where(['sid' => $storeUser['sid'], 'id'=>$storeUser['store_id'], 'status' => 1])->find();
						if($store['status']==1) {
							//获取终端信息
							$terminal = M('mchTerminal');
							$count = $terminal->where(['mch_id' => $storeUser['sid'],'store_id'=>$store['id'],'user_phone'=>$userType[1]])->find();
							if (empty($count)) { //没有先新增一个
								$arr = [
									'mch_id' => $storeUser['sid'],
									'appid' => self::randApp('appid'),
									'appkey' => self::randApp(),
									'store_id'=> $store['id'],
									'user_phone'=>$userType[1],
									'status' => 1,
									'remark' => '首次自动创建',
									'create_time' => time(),
									'domain_auth' => $storeUser['domain_auth'],
								];
								$terminal->add($arr);
								$appid = $arr['appid'];
								$key = $arr['appkey'];
							} else {
								$appid = $count['appid'];
								$key = $count['appkey'];
							}

							$temp=memberTemp($storeUser['sid']);
							$member_status=($temp&&$temp['status']==1)?1:2;

							$apiArr = [
								'uid' => (int)$storeUser['sid'],
								'sid' => (int)$storeUser['store_id'],
								'mch_name' => $store['name'],
								'reg_time' => date('Y-m-d H:i:s', $store['uptime']),
								'appid' => $appid,
								'key' => $key,
								'is_member'=> $member_status,
								'store_name' => $storeUser['username'],
								'store_tel' => $storeUser['phone'],
								'api_time' => date('Y-m-d H:i:s')
							];
							$apiArr['wx_face_config']=$this->getWxFaceConfig($storeUser['sid'],$appid,$storeUser['store_id'],$store['name']);
							return $this->json_data(100, '登录成功', $apiArr);
						}else{
							return $this->json_data(400, '门店已被禁用');
						}
					}else{
						return $this->json_data(400, '账户已禁用');
					}
				}else{
					return $this->json_data(400, '账户或密码不正确');
				}

			}else{
				return $this->json_data(400, '账户或密码不正确');
			}
		}else {
			$seller = M('MchSeller')->where(array('mch_tel' => $post['tel'], 'domain_auth' => domain_auth()))->find();
			if ($seller) {
				//验证密码
				if (empty($seller['password'])) {
					$password = strtoupper(md5(md5(123456) . $this->key));
				} else {
					$password = strtoupper(md5($seller['password'] . $this->key));
				}
				if ($post['pass'] == $password) {
					if ($seller['status'] == 1) {
						//获取终端信息
						$terminal = M('mchTerminal');
						$count = $terminal->where(['mch_id' => $seller['id'],'store_id'=>['exp','IS NULL']])->find();
						if (empty($count)) { //没有先新增一个
							$arr = [
								'mch_id' => $seller['id'],
								'appid' => self::randApp('appid'),
								'appkey' => self::randApp(),
								'status' => 1,
								'remark' => '首次商户自动创建',
								'create_time' => time(),
								'domain_auth' => $seller['domain_auth'],
							];
							$terminal->add($arr);
							$appid = $arr['appid'];
							$key = $arr['appkey'];
						} else {
							$appid = $count['appid'];
							$key = $count['appkey'];
						}
						$sid = M('MchStore')->where(['sid' => $seller['id'], 'status' => 1])->order('id desc')->getField('id');
						$temp=memberTemp($seller['id']);
						$member_status=($temp&&$temp['status']==1)?1:2;

						$apiArr = [
							'uid' => (int)$seller['id'],
							'sid' => (int)$sid,
							'mch_name' => $seller['mch_name'],
							'reg_time' => date('Y-m-d H:i:s', $seller['ctime']),
							'appid' => $appid,
							'key' => $key,
							'is_member'=> $member_status,
							'store_name' => $seller['mch_card_name'],
							'store_tel' => $seller['mch_tel'],
							'api_time' => date('Y-m-d H:i:s')
						];
						$apiArr['wx_face_config']=$this->getWxFaceConfig($seller['id'],$appid,$sid,$seller['mch_name']);
						return $this->json_data(100, '登录成功', $apiArr);
					} else {
						return $this->json_data(400, '商户状态被禁用或待审核');
					}
				} else {
					return $this->json_data(400, '账户或密码不正确');
				}
			} else {
				return $this->json_data(400, '账户或密码不正确');
			}
		}
	}


	public function getWxFaceConfig($cid,$appid,$store_id,$store_name){
		//获取mch_id
		$db = M('mchSellerAlleys');
		$sub_mch_id=$db->where(['alleys_type'=>'WxPay','cid'=>$cid])->getField('mch_id');
		$arr=[
		    'appid'=>GetPayConfigs('xun_wxpay_appid'),
		    'key'=>GetPayConfigs('xun_wxpay_key'),
			'mch_id'=>GetPayConfigs('xun_wxpay_mch_id'),
			'sub_mch_id'=>$sub_mch_id,
			'store_id'=>$store_id,
			'store_name'=>$store_name,
			'device_id'=>'A'.$store_id,
		];
		return $arr;
	}

	/**
	 * 终端APPID或key生成
	 * @param null $type
	 * @return mixed
	 */
	public function randApp($type=null){
		$Data = M('MchTerminal');
		if($type=='appid'){
			$appid=RandStr(10);
			if($Data->where(array('appid'=>$appid))->count()){
				self::randApp('appid');
			}else{
				return $appid;
			}
		}else{
			$appkey=RandStr(22,1);
			if($Data->where(array('appkey'=>$appkey))->count()){
				self::randApp();
			}else{
				return $appkey;
			}
		}
	}

	/**
	 * JSON返回数据信息
	 * @param $code
	 * @param $msg
	 * @param array $data
	 */
	public function json_data($code,$msg,$data=[]){
		header('Content-type: application/json;charset=utf-8');
		header('Auth: zzxunlong chen for xunmafu.com '.date('Y-m-d H:i:s'));
		$json=['code'=>$code,'msg'=>$msg];
		if(is_array($data)&&!empty($data)){
			$json['data']=$data;
		}
		$ret=json_encode($json,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
		rwlog('userLogin',[$this->post,$ret]);
		exit($ret);
	}

	/**
	 * 是否是https协议
	 * @return bool
	 */
	public function isHttps(){
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
		return $http_type=='https://'?true:false;
	}

}