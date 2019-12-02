<?php
/**
 * 支付宝ISV门店进件
 */
namespace Pays\Controller;
use Think\Controller;
class XaliIsvController extends Controller
{
	protected $config;
	protected $aop;
	protected $oid;

	public function _initialize(){
		Vendor('ali_dmf.AopSdk');
		$config=M('MchPayConfig')->where(['domain_auth'=>domain_auth()])->find();
		//基础配置信息
		$this->config=[
			'pid'=>$config['ali_isv_pid'],
			'appid'=>$config['ali_isv_appid'],
			'rsaPublicKey'=>$config['ali_isv_public_key'],
			'rsaPrivateKey'=>$config['ali_isv_private_key'],
			'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Pays/XaliIsv/', //商户审核结果异步通知
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
		$this->oid=rand_out_trade_no();

	}




	/**
	 * 门店入驻
	 * @param $param
	 * @return array|string
	 */
	public function shopCreate($param){
		if($param['is_operating_online']=='T'){
			if(empty($param['online_url'])){
				E('请填写其他平台开店的店铺链接url');
			}
		}else{
			if(empty($param['licence'])){
				E('请上传营业执照');
			}
			if(empty($param['licence_code'])){
				E('请输入营业执照编号');
			}
			if(empty($param['licence_name'])){
				E('请输入营业执照名称');
			}
			if(empty($param['licence_expires'])){
				E('请输入营业执照过期时间');
			}
		}
		$arr=[
		    'store_id'=>'XA'.$param['mid'].'S'.date('s'),
			'category_id'=>$param['category_id'], //类目id，请参考商户入驻要求。
			'main_shop_name'=>$param['main_shop_name'],// 主门店名 比如：肯德基；主店名里不要包含分店名，如“万塘路店”。主店名长度不能超过20个字符。
			'branch_shop_name'=>$param['branch_shop_name'],//分店名称，比如：万塘路店，与主门店名合并在客户端显示为：肯德基(万塘路店)。
			'province_code'=>$param['province_code'],//省份编码
			'city_code'=>$param['city_code'],//城市编码
			'district_code'=>$param['district_code'],//区县编码
			'address'=>$param['address'],//门店详细地址，地址字符长度在4-50个字符，注：不含省市区。
			'longitude'=>$param['longitude'],//经度；
			'latitude'=>$param['latitude'],//纬度
			'contact_number'=>$param['contact_number'],//门店电话号码；支持座机和手机，只支持数字和+-号，在客户端对用户展现， 支持多个电话， 以英文逗号分隔。
			'notify_mobile'=>$param['notify_mobile'],//门店店长电话号码；用于接收门店状态变更通知，收款成功通知等通知消息， 不在客户端展示。
			'main_image'=>$this->imgUpload($param['main_image'],$param['mid']),// 门店首图，非常重要，推荐尺寸2000*1500。
			'audit_images'=>$this->imgUploadArr($param['audit_images'],$param['mid']),//门店审核时需要的图片；至少包含一张门头照片，两张内景照片，必须反映真实的门店情况，审核才能够通过；多个图片之间以英文逗号分隔。
			'business_time'=>$param['business_time'],//请严格按"周一-周五 09:00-20:00,周六-周日 10:00-22:00"的格式进行填写，时间段不能重复，最多支持两个时间段，24小时营业请填写"00:00-23:59"
			'isv_uid'=>$this->config['pid'],//ISV返佣id，门店创建、或者门店交易的返佣将通过此账号反给ISV，如果有口碑签订了返佣协议，则该字段作为返佣数据提取的依据。此字段必须是个合法uid，2088开头的16位支付宝会员账号，如果传入错误将无法创建门店。
			'licence'=>$this->imgUpload($param['licence'],$param['mid']),//门店营业执照图片，各行业所需的证照资质参见商户入驻要求。
			'licence_code'=>$param['licence_code'],//门店营业执照编号，只支持输入中文，英文和数字，营业执照信息与is_operating_online至少填一项。
			'licence_name'=>$param['licence_name'],//门店营业执照名称。
			'business_certificate'=>$this->imgUpload($param['business_certificate'],$param['mid']),//许可证，各行业所需的证照资质参见商户入驻要求；该字段只能上传一张许可证，一张以外的许可证、除营业执照和许可证之外其他证照请放在其他资质字段上传。
			'business_certificate_expires'=>$param['business_certificate_expires'],//许可证有效期，格式：2020-03-20或长期。严格按照格式填写。
			'auth_letter'=>$param['auth_letter'],//门店授权函,营业执照与签约账号主体不一致时需要。
			'is_operating_online'=>$param['is_operating_online'],//是否在其他平台开店，T表示有开店，F表示未开店。
			'online_url'=>$param['online_url'],//其他平台开店的店铺链接url，多个url使用英文逗号隔开,isv迁移到新接口使用此字段，与is_operating_online=T配套使用。
			'operate_notify_url'=>$this->config['notifyUrl'].'operate_notify_url',//当商户的门店审核状态发生变化时，会向该地址推送消息。
			'request_id'=>$this->requestId($param['mid'].'C'),//请求ID
			'other_authorization'=>$this->imgUploadArr($param['other_authorization'],$param['mid']),//其他资质。用于上传营业证照、许可证照外的其他资质，除已上传许可证外的其他许可证也可以在该字段上传。
			'licence_expires'=>$param['licence_expires'],//营业执照过期时间。格式：2020-10-20或长期。严格按照格式填写。
			'op_role'=>'ISV',
			'biz_version'=>'2.0'
		];
		$request = new \AlipayOfflineMarketShopCreateRequest ();
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result = $this->setAop($request, true, $param['mid']);
		rwlog('isv_applyIn',[$arr,$result]);
		if ($result['status']) {
			if ($result['data']['code'] == '10000' && $result['data']['msg'] == 'Success') {//申请提交成功
				//创建申请记录
				$this->setApplyData($param,$arr,$result['data']);
				return '进件信息提交成功,请等待审核结果';
			} else {
				E($result['data']['sub_msg'] . "({$result['data']['msg']}|{$result['data']['sub_code']})");
			}
		} else {
			E($result['msg']);
		}
		return [$arr,$result];

    }

	/**
	 * 增加/更新申请记录
	 * @param $params
	 * @param $arr
	 * @param $res
	 * @return mixed
	 */
	private  function setApplyData($params,$arr,$res){
		$db=M('isvApplyin');
		$addArr=[
			'mid'=>$params['mid'],
			'pid'=>$this->config['pid'],
			'appid'=>$this->config['appid'],
			'apply_id'=>$res['apply_id'],
			'request_id'=>$arr['request_id'],
			'main_shop_name'=>$arr['main_shop_name'],
			'store_id'=>$arr['store_id'],
			'category_id'=>$arr['category_id'],
			'audit_status'=>$res['audit_status']?$res['audit_status']:'AUDITING',
			'update_time'=>time(),
			'domain_auth'=>domain_auth(),
		];
		$where=[
			'mid'=>$params['mid'],
			'apply_id'=>$res['apply_id']
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
	 * 查询申请状态
	 * @param string $apply_id
	 * @param int $mid
	 * @return mixed
	 */
	public function queryShopState($apply_id,$mid){
		$arr=[
			'apply_ids'=>[$apply_id],
			'biz_type'=>'SHOP',
			'op_role'=>'ISV',
		];
		$request = new \AlipayOfflineMarketApplyorderBatchqueryRequest ();
		$request->setBizContent(json_encode($arr,JSON_UNESCAPED_UNICODE));
		$result = $this->setAop($request, true, $mid);
		if($result['status']){
		     if($result['data']['code']=='10000'&&$result['data']['msg']=='Success'){
		     	 $biz=$result['data']['biz_order_infos'][0];
		     	 //更新业务结果
				 $db=M('isvApplyin');
				 $where=[
				 	'apply_id'=>$biz['apply_id'],
					'request_id'=>$biz['request_id'],
				 ];
				 $db->where($where)->save([
					 'status'=>$biz['status'],
					 'audit_status'=>$biz['sub_status'],//子状态
					 'msg'=>$biz['result_desc'],
					 'shop_id'=>$biz['biz_id']?$biz['biz_id']:''
				 ]);
		     	 return $biz;
			 }else{
				 E($result['data']['sub_msg'] . "({$result['data']['msg']}|{$result['data']['sub_code']})");
			 }
		}else {
			E($result['msg']);
		}
	}

	/**
	 * 异步接收门店审核状态
	 */
    public function operate_notify_url(){
		$data=I('param.');
		rwlog('operate_notify_url',$data);
		$db=M('isvApplyin');
		$where=[
			'apply_id'=>$data['apply_id'],
			'request_id'=>$data['request_id'],
		];
		$mid=$db->where($where)->getField('mid');
		if($mid){
			 try{
			 	 $this->queryShopState($data['apply_id'],$mid);
			 }catch (\Exception $e){
				 exit($e->getMessage());
			 }
			 exit('Success');
		}else{
			exit('ERROR NO MID');
		}

	}

	/**
	 * getBusinessCode 生成业务申请编号
	 * @return mixed|null|string|string[]
	 */
	private function requestId($id='') {
		$millisecond = $this->getMillisecond();
		return 'A'.$id.mb_strtoupper(md5(uniqid($millisecond . mt_rand())));
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
	 * 多图上传
	 * @param array $file
	 * @param $mid
	 * @return string
	 */
	public function imgUploadArr(array $file,$mid){
		$arr=[];
		foreach ($file as $k=>$v){
			$arr[]=$this->imgUpload($v,$mid);
		}
		return implode(',',$arr);
	}

	/**
	 * 上传门店照片
	 * @param $file
	 * @param $mid
	 * @return array|string
	 */
	public function imgUpload($file,$mid){
		if(empty($file)){
			return '';
		}else {
			$cache_id='img_'.md5($file);
			if(empty(S($cache_id))) {
				$file = $this->filePath($file);
				$fileData = explode('.', basename($file));
				$request = new \AlipayOfflineMaterialImageUploadRequest ();
				$request->setImageType("jpg");
				$request->setImageName($fileData[0]);
				$request->setImageContent("@" . $file);
				$request->setImagePid($this->config['pid']);
				$result = $this->setAop($request, true, $mid);
				if ($result['status']) {
					if ($result['data']['code'] == '10000' && $result['data']['msg'] == 'Success') {//获取成功
						S($cache_id,$result['data']['image_id'],86400*3);//3天有效期
						return $result['data']['image_id'];
					} else {
						E($result['data']['sub_msg'] . "({$result['data']['msg']}|{$result['data']['sub_code']})");
					}
				} else {
					E($result['msg']);
				}
			}else{
				return S($cache_id);
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
	 * 门店类目配置查询
	 * @param $mid
	 * @return mixed
	 */
	public function queryShopCategory($mid){
		$cache_id=$this->config['pid'].'_cate';
		if(empty(S($cache_id))) {
			$arr = [
				'op_role' => 'ISV'
			];
			$request = new \AlipayOfflineMarketShopCategoryQueryRequest();
			$request->setBizContent(json_encode($arr, JSON_UNESCAPED_UNICODE));
			$result = $this->setAop($request, true, $mid);
			if ($result['status']) {
				if ($result['data']['code'] == '10000' && $result['data']['msg'] == 'Success') {//获取成功
					$info=$result['data']['shop_category_config_infos'];
					S($cache_id,$info,86400); //1天有效期,有效期后重新缓存
					return $info;
				} else {
					E($result['data']['sub_msg'] . "({$result['data']['msg']}|{$result['data']['sub_code']})");
				}
			} else {
				E($result['msg']);
			}
		}else{
			return S($cache_id);
		}
	}


	/**
	 * 通信
	 * @param $request
	 * @return array
	 */
	public function setAop($request,$token=false,$mid=''){
		try{
			if($token){
				$app_auth_token=aliIsvToken($mid);
				$result = $this->aop->execute($request,null,$app_auth_token);
			}else{
				$result = $this->aop->execute($request);
			}
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$result=object_to_array($result->$responseNode);
		}catch (\Exception $e) {
			$result=$e->getMessage();
			return ['status'=>0,'msg'=>$result];
		}
		return ['status'=>1,'msg'=>'接口通信正常','data'=>$result];
	}
}