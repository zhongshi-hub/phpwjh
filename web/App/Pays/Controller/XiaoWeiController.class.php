<?php
/**
 * 微信小微进件
 */
namespace Pays\Controller;
use Think\Controller;
class XiaoWeiController extends Controller
{
	protected  $config;
	// 公钥
	protected $publicKey;
	// 解密后证书地址
	protected $publicKeyAddr;

	protected  $sslCertPath;
	protected  $sslKeyPath;

	public function _initialize(){
        $config=M('MchPayConfig')->where(['domain_auth'=>domain_auth()])->find();
		$this->config=[
		     'appid'=>$config['xun_wxpay_appid'],
		     'mch_id'=>$config['xun_wxpay_mch_id'],
		     'key'=>$config['xun_wxpay_key'],
		     'apiV3'=>$config['xun_wxpay_v3key'],
		];
		$this->publicKeyAddr       = __ROOT__.'Cert/wxpay/'.$this->config['mch_id'].'_public_key.pem';
		$this->privateKeyAddr      = __ROOT__.'Cert/wxpay/'.$this->config['mch_id'].'_private_key.pem';
		$this->sslCertPath         = __ROOT__.'Cert/wxpay/'.$this->config['mch_id'].'_cert.pem';
		$this->sslKeyPath         = __ROOT__.'Cert/wxpay/'.$this->config['mch_id'].'_key.pem';
	}

	/**
	 * 重新发起提现
	 * @param $param
	 * @return mixed
	 */
	public function reAutoWithDrawByDate($param){
		$url="https://api.mch.weixin.qq.com/fund/reautowithdrawbydate";
		$arr=[
			'mch_id'=>$this->config['mch_id'],
			'sub_mch_id'=>$param['sub_mch_id'],
			'nonce_str'=>uniqid(),
			'sign_type'=>'HMAC-SHA256',
			'date'=>$param['date'],
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					E($res['err_code_des']);
				}else {
					return $res;
				}
			}
			E($res['err_code_des']);
		}
		E($res['return_msg']);

	}



	/**
	 * 查询提现状态
	 * @param $param
	 * @return mixed
	 */
	public function queryAutoWithDrawByDate($param){
		$url="https://api.mch.weixin.qq.com/fund/queryautowithdrawbydate";
		$arr=[
			'mch_id'=>$this->config['mch_id'],
			'sub_mch_id'=>$param['sub_mch_id'],
			'nonce_str'=>uniqid(),
			'sign_type'=>'HMAC-SHA256',
			'date'=>$param['date'],
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					E($res['err_code_des']);
				}else {
					return $res;
				}
			}
			E($res['err_code_des']);
		}
		E($res['return_msg']);
	}

	/**
	 * 修改联系信息
	 * @param $param
	 * @return string
	 */
	public function alterInfo($param){
		$url="https://api.mch.weixin.qq.com/applyment/micro/modifycontactinfo";
		$arr=[
			'version'=>'1.0',
			'mch_id'=>$this->config['mch_id'],
			'nonce_str'=>uniqid(),
			'sign_type'=>'HMAC-SHA256',
			'sub_mch_id'=>$param['sub_mch_id'],
			'cert_sn'=>$this->getCertficates(),
		];
		if(!empty($param['mobile_phone'])){
			$arr['mobile_phone']=$this->publicKeyEncrypt($param['mobile_phone']);
		}
		if(!empty($param['email'])){
			$arr['email']=$this->publicKeyEncrypt($param['email']);
		}
		if(!empty($param['merchant_name'])){
			$arr['merchant_name']=$param['merchant_name'];
		}
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		//rwlog('xw_alterInfoBank',[$arr,$res]);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					E($res['err_code_des']);
				}else {
					return "商户号:{$res['sub_mch_id']} 联系信息变更成功";
				}
			}
			E($res['err_code_des']);
		}
		E($res['return_msg']);
	}
	/**
	 * 修改银行卡
	 * @param $param
	 * @return string
	 */
	public function alterInfoBank($param){
		$url="https://api.mch.weixin.qq.com/applyment/micro/modifyarchives";
		$param['bank_address_code']=$param['set_bank_code']?$param['set_bank_code']:$this->getProCity($param['bank_city']);
		$arr=[
			'version'=>'1.0',
			'mch_id'=>$this->config['mch_id'],
			'nonce_str'=>uniqid(),
			'sign_type'=>'HMAC-SHA256',
			'sub_mch_id'=>$param['sub_mch_id'],
			'account_number'=>$this->publicKeyEncrypt($param['account_number']),
			'account_bank'=>$param['account_bank'],
			'bank_address_code'=>$param['bank_address_code'],
			'cert_sn'=>$this->getCertficates(),
		];
		if(!empty($param['bank_name'])){
			$arr['bank_name']=$param['bank_name'];
		}
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		//rwlog('xw_alterInfoBank',[$arr,$res]);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					E($res['err_code_des']);
				}else {
					return "商户号:{$res['sub_mch_id']} 结算信息变更成功";
				}
			}
			E($res['err_code_des']);
		}
		E($res['return_msg']);
	}

	/**
	 * 小微商户升级
	 * @param array $params
	 * @return array
	 */
	public function applyUp(array $params){
		$url="https://api.mch.weixin.qq.com/applyment/micro/submitupgrade";
		//处理参数
		$params['bank_address_code']=$params['set_bank_code']?$params['set_bank_code']:$this->getProCity($params['bank_city']);
		if($params['business_time_end']=='on'){
			$params['business_time'][1]='长期';
		}
		//身份证有效期是否正确
		if($params['business_time_end']!='on'||!$params['business_time_end']){
			if(strtotime($params['business_time'][1]) <= strtotime($params['business_time'][0])){
				E('营业期限到期日期不能小于发证日期');
			}
		}
		if(!empty($params['organization_time'][0])) {
			if ($params['organization_time_end'] == 'on') {
				$params['organization_time'][1] = '长期';
			}
			//身份证有效期是否正确
			if ($params['organization_time_end'] != 'on' || !$params['organization_time_end']) {
				if (strtotime($params['organization_time'][1]) <= strtotime($params['organization_time'][0])) {
					E('组织机构代码有效期限到期日期不能小于发证日期');
				}
			}
			$params['organization_time']=json_encode($params['organization_time'],JSON_UNESCAPED_UNICODE);
		}else{
			$params['organization_time']='';
		}
		//转换格式
		$params['business_time']=json_encode($params['business_time'],JSON_UNESCAPED_UNICODE);
		$arr = [
			'version' => '1.0',
			'mch_id' => $this->config['mch_id'],
			'nonce_str' => uniqid(),
			'sign_type' => 'HMAC-SHA256',
			'cert_sn' => $this->getCertficates(),
			'sub_mch_id' => $params['sub_mch_id'], // 小微商户号
			'organization_type' => $params['organization_type'], // 主体类型
			'business_license_copy' => $this->uploadImg($params['business_license_copy']), // 营业执照扫描件
			'business_license_number' => $params['business_license_number'], // 营业执照注册号
			'merchant_name' => $params['merchant_name'], // 商户名称
			'company_address' => $params['company_address'], // 注册地址
			'legal_person' => $this->publicKeyEncrypt($params['legal_person']), // 经营者姓名 / 法定代表人
			'business_time' => $params['business_time'], // 营业期限
			'business_licence_type' => $params['business_licence_type'], // 营业执照类型
			'organization_copy' => $this->uploadImg($params['organization_copy']), // 组织机构代码证照片
			'organization_number' => $params['organization_number'], // 组织机构代码
			'organization_time' => $params['organization_time'], // 组织机构代码有效期限
			'account_name' => $this->publicKeyEncrypt($params['account_name']), // 开户名称
			'account_bank' => $params['account_bank'], // 开户银行
			'bank_address_code' => $params['bank_address_code'], // 开户银行省市编码
			'bank_name' => $params['bank_name'], // 开户银行全称（含支行）
			'account_number' => $this->publicKeyEncrypt($params['account_number']), // 银行卡号
			'merchant_shortname' => $params['merchant_shortname'], // 商户简称
			'business' => $params['business'], // 费率结算规则 ID
			'qualifications' => $this->uploadImgArr(json_encode($params['qualifications'])), // 特殊资质
			'business_scene' => json_encode($params['business_scene']), // 经营场景
			'business_addition_desc' => $params['business_addition_desc'], // 补充说明
			'business_addition_pics' => $this->uploadImgArr(json_encode($params['business_addition_pics'])), // 补充材料
			// 以下字段在 business_scene 为线下场景，既值为 "[1721]" 时无需填写，若包含其它场景，请按以下规则填写
			'mp_appid' => $params['mp_appid'], // 公众号 APPID
			'mp_app_screen_shots' => $this->uploadImgArr(json_encode($params['mp_app_screen_shots'])), // 公众号页面截图
			'miniprogram_appid' => $params['miniprogram_appid'], // 小程序 APPID
			'miniprogram_screen_shots' => $this->uploadImgArr(json_encode($params['miniprogram_screen_shots'])), // 小程序页面截图
			'app_appid' => $params['app_appid'], // 应用 APPID
			'app_screen_shots' => $this->uploadImgArr(json_encode($params['app_screen_shots'])), // APP 截图
			'app_download_url' => $params['app_download_url'], // APP 下载链接
			'web_url' => $params['web_url'], // PC 网站域名
			'web_authoriation_letter' => $params['web_authoriation_letter'], // 网站授权函
			'web_appid' => $params['web_appid'], // PC 网站对应的公众号 APPID
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		rwlog('wx_xw_up',[$params,$arr,$res]);
		if ($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					return ['status'=>false,'msg'=>$res['err_code_des'].json_encode($res['err_param'],JSON_UNESCAPED_UNICODE)];
				}else {
					$this->setApplyUp($params['sub_mch_id'], $params);
					return ['status' => true, 'msg' => '商户升级申请信息提交成功,请5分钟后查询审核结果'];
				}
			}
			return ['status'=>false,'msg'=>$res['err_code_des']];
		}
		return ['status'=>false,'msg'=>$res['return_msg']];
	}

	/**
	 * 商户升级申请查询
	 * @param $sub_mch_id
	 * @return mixed
	 */
	public function applyUpState($sub_mch_id){
		$url="https://api.mch.weixin.qq.com/applyment/micro/getupgradestate";
		$arr=[
			'version'=>'1.0',
			'mch_id'=>$this->config['mch_id'],
			'nonce_str'=>uniqid(),
			'sign_type'=>'HMAC-SHA256',
			'sub_mch_id'=>$sub_mch_id,
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					E($res['err_code_des']);
				}else {
					//更新升级入驻状态
					$save = ['applyment_state' => $res['applyment_state']];
					M('xwApplyup')->where(['sub_mch_id' => $res['sub_mch_id']])->save($save);
					return $res;
				}
			}
			E($res['err_code_des']);
		}
		E($res['return_msg']);
	}

	/**
	 * 增加升级数据
	 * @param $applyment_id
	 * @param $params
	 * @return mixed
	 */
	private  function setApplyUp($sub_mch_id,$params){
		$db=M('xwApplyup');
		$applyArr=[
			'sub_mch_id'=>$sub_mch_id,
			'mch_name'=>$params['merchant_name'],
			'applyment_state'=>'AUDITING',//默认审核中
			'data'=>json_encode($params,JSON_UNESCAPED_UNICODE),
			'domain_auth'=>domain_auth()
		];
		$where=[
			'sub_mch_id'=>$sub_mch_id,
		];
		$count=$db->where($where)->count();
		if($count){
			$applyArr['update_time']=time();
			$res= $db->where($where)->save($applyArr);
		}else {
			$applyArr['create_time']=time();
			$applyArr['update_time']=time();
			$res = $db->add($applyArr);
		}
		return $res;
	}



	/**
	 * 小微入网进件
	 * @param array $params
	 * @return array
	 */
	public function applyIn(array $params){
		$url='https://api.mch.weixin.qq.com/applyment/micro/submit';
		//处理参数
		$params['store_address_code']=$params['set_store_code']?$params['set_store_code']:$this->getProCity($params['city']);
		$params['bank_address_code']=$params['set_bank_code']?$params['set_bank_code']:$this->getProCity($params['bank_city']);
		if($params['id_card_valid_time_end']=='on'){
			$params['id_card_valid_time'][1]='长期';
		}
		//身份证有效期是否正确
		if($params['id_card_valid_time_end']!='on'||!$params['id_card_valid_time_end']){
			if(strtotime($params['id_card_valid_time'][1]) <= strtotime($params['id_card_valid_time'][0])){
				E('身份证到期日期不能小于发证日期');
			}
		}
		//转换格式
		$params['id_card_valid_time']=json_encode($params['id_card_valid_time'],JSON_UNESCAPED_UNICODE);
		$params['business_addition_pics']=json_encode($params['business_addition_pics']);
		// 校验参数
		if (!$this->checkParams($params)) {
			E('必填参数不需为空');
		}
		// 校验银行卡号前缀是否支持
		if ($this->accountNumberIsSupport($params['account_number'])) {
			E('结算卡号类型不支持,请更换卡号');
		}
		// 此资料是否进件过 只有未进件或已被驳回才可再进件
		if ($this->applyIsExist($params['mid'],$params['id_card_number'], $params['id_card_name'])) {
			E('请勿重复提交信息，信息审核中或已完成，可以通过申请查询状态');
		}
		$arr = [
			'version' => '3.0',
			'cert_sn' => $this->getCertficates(),
			'mch_id' => $this->config['mch_id'],
			'nonce_str' => uniqid(),
			'sign_type' => 'HMAC-SHA256',
			'business_code' => $params['business_code']?$params['business_code']:$this->getBusinessCode(), // 业务申请编号
			'id_card_copy' => $this->uploadImg($params['id_card_copy']), // 身份证人像面照片  media_id
			'id_card_national' => $this->uploadImg($params['id_card_national']), // 身份证国徽面照片
			'id_card_name' => $this->publicKeyEncrypt($params['id_card_name']), //身份证姓名
			'id_card_number' => $this->publicKeyEncrypt($params['id_card_number']), //身份证号码
			'id_card_valid_time' => $params['id_card_valid_time'], // '["1970-01-01","长期"]' string(50)
			'account_name' => $this->publicKeyEncrypt($params['account_name']),
			'account_bank' => $params['account_bank'],
			'bank_address_code' => $params['bank_address_code'],
			'bank_name' => $params['bank_name'],
			'account_number' => $this->publicKeyEncrypt($params['account_number']),
			'store_name' => $params['store_name'],
			'store_address_code' => $params['store_address_code'],
			'store_street' => $params['store_street'],
			'store_entrance_pic' => $this->uploadImg($params['store_entrance_pic']),
			'store_longitude'=>$params['store_longitude'],
			'store_latitude'=>$params['store_latitude'],
			'indoor_pic' => $this->uploadImg($params['indoor_pic']),
			'address_certification' => $this->uploadImg($params['address_certification']),
			'merchant_shortname' => $params['merchant_shortname'],
			'service_phone' => $params['service_phone'],
			'product_desc' => $params['product_desc'],
			'rate' => $params['rate'] ?$params['rate']: '0.6%',
			'business_addition_desc' => $params['business_addition_desc'],
			'business_addition_pics' => $this->uploadImgArr($params['business_addition_pics']), // ["123","456"] 最多可上传5张照片，请填写已预先上传图片生成好的MediaID
			'contact' => $this->publicKeyEncrypt($params['contact']),
			'contact_phone' => $this->publicKeyEncrypt($params['contact_phone']),
			'contact_email' => isset($params['contact_email']) && !empty($params['contact_email']) ? $this->publicKeyEncrypt($params['contact_email']) : '',
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		rwlog('wx_xw_in',[$params,$arr,$res]);
		if ($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					return ['status'=>false,'msg'=>$res['err_code_des'].json_encode($res['err_param'],JSON_UNESCAPED_UNICODE)];
				}else {
					$this->setApplyData($params, $arr, $res);
					$this->setApplyLog($res['applyment_id'], $params);
					return ['status' => true, 'msg' => '进件信息提交成功,请5分钟后查询审核结果'];
				}
			}
			return ['status'=>false,'msg'=>$res['err_code_des']];
		}
		return ['status'=>false,'msg'=>$res['return_msg']];
	}


	/**
	 * 部分进件省市地址转换
	 * @param $data
	 * @return string
	 */
	private function getProCity($data){
		switch ($data){
			case '110100': //北京-北京市
				$code='110000';
				break;
			case '110228'://中国,,北京市,密云区
				$code='110118';
				break;
			case '110229'://中国,,北京市,延庆区
				$code='110119';
				break;
			case '110200'://北京-北京郊县
				$code='110000';
				break;
			case '120100'://天津市
				$code='120000';
				break;
			case '120200'://天津郊县
				$code='120000';
				break;
			case '120221'://中国,,天津市,宁河区
				$code='120117';
				break;
			case '120223'://中国,,天津市,静海区
				$code='120118';
				break;
			case '120225'://中国,,天津市,蓟州区
				$code='120119';
				break;
			case '310100'://上海
				$code='310000';
				break;
			case '310200'://上海郊县-上海
				$code='310000';
				break;
			case '310230'://上海郊县-上海
				$code='310151';
				break;
			default:
				$code=$data;
				break;

		}
		return $code;
	}

	/**
	 * 增加或更新申请单记录
	 * @param $params
	 * @param $arr
	 * @param $res
	 * @return mixed
	 */
	private  function setApplyData($params,$arr,$res){
		$db=M('xwApplyin');
		$addArr=[
			'mid'=>$params['mid'],
			'mch_id'=>$this->config['mch_id'],
			'applyment_id'=>$res['applyment_id'],
			'business_code'=>$arr['business_code'],
			'mch_name'=>$params['store_name'],
			'rate'=>$params['rate'],
			'id_card_number'=>$params['id_card_number'],
			'id_card_name'=>$params['id_card_name'],
			'applyment_state'=>$res['applyment_state']?$res['applyment_state']:'AUDITING',
			'update_time'=>time(),
			'domain_auth'=>domain_auth(),
		];
		$where=[
			'mid'=>$params['mid'],
			'applyment_id'=>$res['applyment_id']
		];
		$count=$db->where($where)->count();
		if($count){
			$ret= $db->where($where)->save($addArr);
		}else {
			$addArr['create_time']=time();
			$ret = $db->add($addArr);
		}
		return $ret;
	}

	/**
	 * 增加申请日志数据
	 * @param $applyment_id
	 * @param $params
	 * @return mixed
	 */
	private  function setApplyLog($applyment_id,$params){
		$db=M('xwApplyinLog');
		$applyArr=[
			'mid'=>$params['mid'],
			'applyment_id'=>$applyment_id,
			'data'=>json_encode($params,JSON_UNESCAPED_UNICODE),
			'create_time'=>time(),
			'domain_auth'=>domain_auth()
		];
		$where=[
			'mid'=>$params['mid'],
			'applyment_id'=>$applyment_id
		];
		$count=$db->where($where)->count();
		if($count){
			$res= $db->where($where)->save($applyArr);
		}else {
			$res = $db->add($applyArr);
		}
		return $res;
	}


	/**
	 * 查询申请状态
	 * @param $applyment_id
	 * @return mixed
	 */
	public function applyState($applyment_id){
		$url="https://api.mch.weixin.qq.com/applyment/micro/getstate";
		$arr=[
		   'version'=>'1.0',
		   'mch_id'=>$this->config['mch_id'],
		   'nonce_str'=>uniqid(),
		   'sign_type'=>'HMAC-SHA256',
		   'applyment_id'=>$applyment_id,
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				if($res['err_code']=='PARAM_ERROR'){
					E($res['err_code_des']);
				}else {
					//更新入驻状态
					$save = ['applyment_state' => $res['applyment_state']];
					if ($res['applyment_state'] == 'TO_BE_SIGNED' || $res['applyment_state'] == 'FINISH') {
						$save['sub_mch_id'] = $res['sub_mch_id'];
					}
					M('xwApplyin')->where(['applyment_id' => $applyment_id])->save($save);
					return $res;
				}
			}
			E($res['err_code_des']);
		}
		E($res['return_msg']);
	}


	/**
	 * 查询配置
	 * @param $sub_mch_id
	 * @return array
	 */
	public function querySubDevConfig($sub_mch_id){
		$url="https://api.mch.weixin.qq.com/secapi/mch/querysubdevconfig";
		$arr=[
			'appid'=>$this->config['appid'],
			'mch_id'=>$this->config['mch_id'],
			'sub_mch_id'=>$sub_mch_id,
		];
		$arr['sign']=$this->md5Sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				$ret=[];
				$ret['appid']=$this->config['appid'];
				$ret['appid_config_list']=json_decode($res['appid_config_list'],true);
				$ret['jsapi_path_list']=json_decode($res['jsapi_path_list'],true);
				return $ret;
			}else {
				E($res['err_code_des']);
			}
		}else {
			E($res['return_msg']);
		}
	}

	/**
	 * 增加关注
	 * @param $sub_mch_id
	 * @param $appid
	 * @param bool $type
	 * @return mixed
	 */
	public function addRecommendConf($sub_mch_id,$appid,$type=false){
		$url="https://api.mch.weixin.qq.com/secapi/mkt/addrecommendconf";
		$arr=[
			'sub_appid'=>$this->config['appid'],
			'mch_id'=>$this->config['mch_id'],
			'sub_mch_id'=>$sub_mch_id,
			'nonce_str'=>uniqid(),
			'sign_type'=>'HMAC-SHA256'
		];
		if($type){ //小程序
			$arr['receipt_appid']=$appid;
		}else{ //公众号
			$arr['subscribe_appid']=$appid;
		}
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				return $res;
			}else {
				E($res['err_code_msg']);
			}
		}else {
			E($res['return_msg']);
		}

	}


	/**
	 * 配置APPID
	 * @param $sub_mch_id
	 * @param $appid
	 * @return mixed
	 */
	public function addSubDevConfig($sub_mch_id,$appid){
        $url="https://api.mch.weixin.qq.com/secapi/mch/addsubdevconfig";
		$arr=[
			'appid'=>$this->config['appid'],
			'mch_id'=>$this->config['mch_id'],
			'sub_mch_id'=>$sub_mch_id,
			'sub_appid'=>$appid?$appid:$this->config['appid'],
		];
		$arr['sign']=$this->md5Sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				return $res;
			}else {
				E($res['err_code_des']);
			}
		}else {
			E($res['return_msg']);
		}
	}

	/**
	 * 支付授权目录
	 * @param $sub_mch_id
	 * @param $path
	 * @return mixed
	 */
	public function applyAddSubDevConfig($sub_mch_id,$path){
		$url="https://api.mch.weixin.qq.com/secapi/mch/addsubdevconfig";
		$arr=[
			'appid'=>$this->config['appid'],
			'mch_id'=>$this->config['mch_id'],
			'sub_mch_id'=>$sub_mch_id,
			'jsapi_path'=>$path?$path:'http://'.$_SERVER['HTTP_HOST'].'/Pay/',
		];
		$arr['sign']=$this->md5Sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url,true);
		$res=$this->FromXml($res);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				return $res;
			}else {
				E($res['err_code_des']);
			}
		}else {
			E($res['return_msg']);
		}
	}


	/**
	 * 多图上传
	 * @param $file
	 * @return string
	 */
	private  function uploadImgArr($file){
		$file=json_decode($file,true);
		$arr=[];
		foreach ($file as $k=>$v){
			if($v!=''&&!empty($v)){
				$arr[]=$this->uploadImg($v);
			}
		}
		return empty($arr)?'':json_encode($arr);
	}


	/**
	 * 上传图片获取media_id
	 * @param $file
	 * @return mixed
	 */
	protected  function uploadImg($file){
		$url='https://api.mch.weixin.qq.com/secapi/mch/uploadmedia';
		if(empty($file)){
			return '';
		}else {
			$file = $this->filePath($file);
			$fileData = explode('.', basename($file));
			if (in_array($fileData[1], ['jpeg', 'jpg', 'bmp', 'png'])) {
				if (!file_exists($file)) {
					E('图片不存在');
				}
				$arr = [
					'mch_id' => $this->config['mch_id'],
					'media_hash' => hash_file('md5', $file),
				];
				$arr['sign_type'] = 'HMAC-SHA256';
				$arr['sign'] = $this->sign($arr);
				$arr['media'] = curl_file_create(realpath($file), 'image/' . $fileData[1], basename($file));
				$header = [
					"Content-type:multipart/form-data",
				];
				$res = $this->postXmlCurl($arr, $url, true, $header);
				$res = $this->FromXml($res);
				if ($res['return_code'] == 'SUCCESS') {
					if ($res['result_code'] == 'SUCCESS') {
						return $res['media_id'];
					}
					E($res['err_code_des']);
				} else {
					E($res['return_msg']);
				}
			} else {
				E('图片格式不正确,必须为jpeg,jpg,bmp,png格式');
			}
		}
	}


	/**
	 * 附件图片路径转换
	 * @param $file
	 * @return string
	 */
	protected function filePath($file){
		if (preg_match("/^(http:\/\/|https:\/\/).*$/", $file)) {
			$savePath =  './Upload/data_tmp/'.date('Ymd').'/';// 设置附件上传目录
			if (!file_exists($_SERVER['DOCUMENT_ROOT'].$savePath)||!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$savePath,0777);
			}
			$file_data = _getImage($file,$savePath);
			if ($file_data['save_path']) {
				$file = getcwd() . $file_data['save_path'];
			}
		} else {
			$file=getcwd() . $file;
		}
		return $file;
	}




	/**
	 * 证书序列号
	 * @return false|int|string
	 */
	public function getCertficates(){
		$url="https://api.mch.weixin.qq.com/risk/getcertficates";
		$arr=[
		   'mch_id'=>$this->config['mch_id'],
		   'nonce_str'=>self::getNonceStr(),
		];
		$res=$this->sendXml($url,$arr);
		if($res['return_code']&&$res['result_code']){
			$certificates=json_decode($res['certificates'],true);
			rwlog('certificates_res',[$res,$certificates]);
			//解密证书
			$cert=$this->getNewCertificates($certificates['data']);
			return $cert;
		}else{
            E($res['result_code']!="SUCCESS"?$res['err_code_des']:$res['return_msg']);
		}
	}


	/**
	 * 数据加密
	 * @param $string
	 * @return string
	 */
	private function publicKeyEncrypt($string)
	{
		if(empty($string)){
			return '';
		}else {
			$crypted = '';
			$publicKey = $this->getPublicKey();
			if ($publicKey) {
				$publicKeyResource = openssl_get_publickey($publicKey);
				$f = openssl_public_encrypt($string, $crypted, $publicKeyResource, OPENSSL_PKCS1_PADDING);
				openssl_free_key($publicKeyResource);
				if ($f) {
					return base64_encode($crypted);
				}
			}
			E('数据加密KEY获取失败');
		}
	}





	/**
	 * getPrivateKey 超级管理员登录商户平台，在“账户中心”->“API 安全”->”API 证书（权威 CA 颁发）”中申请
	 * API 商户证书，申请过程中会获取到私钥证书文件（申请流程详见 1.1.3.3“申请 API 商户证书“），打开
	 * 文件获取私钥字符（定义变量 string sKey）
	 * @return bool|string
	 */
	protected function getPrivateKey()
	{
		if (file_exists($this->sslKeyPath)) {return file_get_contents($this->sslKeyPath);}else{
			E('API证书不存在');
		};
	}


	/**
	 * 获取公钥 用来数据加密
	 * @return bool|string
	 */
	protected function getPublicKey(){
		$key=$this->publicKey ? $this->publicKey:file_get_contents($this->publicKeyAddr);
		if(empty($key)){
			E('获取公钥失败');
		}
		return $key;
	}


	/**
	 * getNewCertificates  获取弃用日期最长证书
	 * @param array $data
	 * @return false|int|string
	 */
	protected function getNewCertificates(array $data)
	{
		$key = 0;
		if (count($data) > 1) {
			$timeArr = [];
			foreach ($data as $k => $v) {
				$timeArr[$k] = strtotime($v['expire_time']);
			}
			$key = array_search(max($timeArr), $timeArr);
		}
		$certificates=$data[$key]['encrypt_certificate'];
		rwlog('certificates',[$data,$certificates]);
		//解密证书
		$this->certDecode($certificates['ciphertext'],$certificates['nonce'],$certificates['associated_data']);
		return $data[$key]['serial_no']; //返回证书序列号
	}

	/**
	 * 解密证书
	 * 用三方接口解密 系统是5.6版本 但是解密控件要求版本必须7.x以上
	 * @param $cipherText
	 * @param $nonce
	 * @param string $associatedData
	 * @return mixed
	 */
	public function certDecode($cipherText,$nonce,$associatedData="certificate"){
		$url="https://ext.0du.cc/sodium.php";
		$arr=['cipherText'=>$cipherText,'associatedData'=>$associatedData,'key'=>$this->config['apiV3'],'nonce'=>$nonce];
		$arr['sign']=ApiSign($arr,'xunmafu');
		$res=curl_calls($url,json_encode($arr),'',true);
		$res=json_decode($res,true);
		if($res['code']==1){
			//保存证书
			$this->publicKey = $res['data'];
			file_put_contents($this->publicKeyAddr, $res['data']);
			return $res['data'];
		}
		E($res['msg']);
	}

	/**
	 * getBusinessCode 生成业务申请编号
	 * @return mixed|null|string|string[]
	 */
	private function getBusinessCode() {
		$millisecond = $this->getMillisecond();
		return mb_strtoupper(md5(uniqid($millisecond . mt_rand())));
	}




	/**
	 * checkParams 校验入驻接口必填字段信息
	 * @param array $params
	 * @return bool
	 */
	private function checkParams(array $params) {
		$data = ['id_card_copy', 'id_card_national', 'id_card_name', 'id_card_number', 'id_card_valid_time', 'account_name', 'account_bank', 'bank_address_code', 'account_number', 'store_name', 'store_address_code', 'store_street', 'store_entrance_pic', 'indoor_pic', 'merchant_shortname', 'service_phone',  'contact', 'contact_phone'];
		$result = true;
		foreach ($data as $key => $value) {
			if (!isset($params[$value]) || empty($params[$value])) {
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * accountNumberIsSupport 判断银行卡账号是否支持
	 * @param $account_number
	 * @return bool
	 */
	private function accountNumberIsSupport($account_number) {
		$account_prefix_6 = substr($account_number, 0, 6);
		$account_prefix_8 = substr($account_number, 0, 8);

		$not_support = ['623501', '621468', '620522', '625191', '622384', '623078', '940034', '622150', '622151', '622181', '622188', '955100', '621095', '620062', '621285', '621798', '621799', '621797', '622199', '621096', '62215049', '62215050', '62215051', '62218849', '62218850', '62218851', '621622', '623219', '621674', '623218', '621599', '623698', '623699', '623686', '621098', '620529', '622180', '622182', '622187', '622189', '621582', '623676', '623677', '622812', '622810', '622811', '628310', '625919', '625368', '625367', '518905', '622835', '625603', '625605', '518905'];
		if (array_search($account_prefix_6, $not_support)) {
			return true;
		}
		if (array_search($account_prefix_8, $not_support)) {
			return true;
		}
		return false;
	}

	/**
	 * applyIsExist 查询申请是否存在且申请状态是成功
	 * @param $id_card_number
	 * @param $id_card_name
	 * @return bool
	 */
	public function applyIsExist($mid,$id_card_number, $id_card_name) {
		// 查询是否存在
		$res = M('xwApplyin')->where(['id_card_number' => $id_card_number, 'id_card_name' => $id_card_name, 'mid' => $mid])->find();
		if (empty($res)) {
			return false;
		}
		// 已经提交成功后判断状态，状态不是驳回则返回true,否则返回false
		if (empty($res['sub_mch_id'])) {
			// 商户号不存在代表没有查询过或者查询过状态不为完成或待签约，重新请求查询状态接口
			$rt = $this->applyState($res['applyment_id']);
			$applyment_state = $rt['applyment_state'];
		} else {
			$applyment_state = $res['applyment_state'];
		}
		// 状态如果是被驳回则可插入新数据
		if ($applyment_state != 'REJECTED') {
			return true;
		}
		return false;
	}

	/**
	 * 发送请求
	 * @param $url
	 * @param $arr
	 * @return mixed
	 */
	public function sendXml($url,$arr){
		$arr['sign_type']='HMAC-SHA256';
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=self::postXmlCurl($xml,$url);
		$res=$this->FromXml($res);
		return $res;
	}

	/**
	 * 数据签名
	 * @param $data
	 * @param bool $type
	 * @return string
	 */
	public function sign($data,$type=false){
		ksort($data);
		$temp=$this->ToUrlParams($data);
		$temp = $temp . "&key=".$this->config['key'];
		$ret=hash_hmac("sha256",$temp,$this->config['key']);
		return $type?$temp:strtoupper($ret);
	}

	/**
	 * MD5签名
	 * @param $data
	 * @param bool $type
	 * @return string
	 */
	public function md5Sign($data,$type=false)
	{
		ksort($data);
		$buff = "";
		foreach ($data as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = $buff.'key='.$this->config['key'];
		return $type?$buff:strtoupper(md5($buff));
	}

	/**
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return
	 */
	public static function getNonceStr($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}

	/**
	 * 输出xml字符
	 * @param $data
	 * @return string
	 */
	public function ToXml($data)
	{
		if(!is_array($data) || count($data) <= 0)
		{
			E("数组数据异常！");
		}

		$xml = "<xml>";
		foreach ($data as $key=>$val)
		{
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}

	/**
	 * 将xml转为array
	 * @param $xml
	 * @return mixed
	 */
	public function FromXml($xml)
	{
		if(!$xml){
			E("xml数据异常！");
		}
		//将XML转为array
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $values;
	}

	/**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams($data)
	{
		$buff = "";
		foreach ($data as $k => $v)
		{
			if($k != "media" &&$k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}


	/**
	 * getMillisecond 获取毫秒级别的时间戳
	 * @return float
	 */
	protected function getMillisecond()
	{
		list($msec, $sec) = explode(' ', microtime());
		$msectime = (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
		return $msectime;
	}


	/**
	 * 以post方式提交xml到对应的接口url
	 * @param $xml需要post的xml数据
	 * @param $url
	 * @param bool $useCert是否需要证书，默认不需要
	 * @param bool $header 自定义http头
	 * @param int $secondurl执行超时时间，默认30s
	 * @return mixed
	 */
	private  function postXmlCurl($xml, $url, $useCert = false, $header=[],$second = 30)
	{
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if($useCert == true){
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			list($sslCertPath, $sslKeyPath) = [$this->sslCertPath, $this->sslKeyPath];
			curl_setopt($ch,CURLOPT_SSLCERT, $sslCertPath);
			curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
		}
		if(!empty($header)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			$header_size     = curl_getinfo($ch, CURLINFO_HEADER_SIZE);    // 获得响应结果里的：头大小
			$response_header = substr($data, 0, $header_size);    // 根据头大小去获取头信息内容
			curl_close($ch);
			//E("curl出错，错误码:$error");
			E("curl出错，错误码:$error.$header_size.$response_header");
		}
	}


}