<?php
namespace Pays\Controller;
use Think\Controller;
class PHybController extends Controller
{


	protected $config;
	protected $oid;

	public function _initialize()
	{
		$this->config = [
			'orgNo' => '02005165',
			'key'=>'010047838b904636d93742f6af684c60c00fffc1',//支付密钥
			'notifyUrl' => 'http://' . $_SERVER['HTTP_HOST'] . '/Pays/PHyb/notifyUrl',
			//'notifyUrl' => 'http://devs.0du.cc/Pays/PHyb/notifyUrl',
			'callBackUrl'=>'http://' . $_SERVER['HTTP_HOST'] . '/Api/result/out_trade_no/',
			'apiUrl' => 'https://xpay.hybunion.cn',
			'mchApiUrl'=>'https://merch.hybunion.cn',
			'saleId'=>'',
		];
		$this->oid=rand_out_trade_no();
	}

	/**
	 * 进件
	 */
	public function mch_in(){
		$this->error('请在通道方平台开户');
//		$data=I('post.');
//		$exp=explode(',',$data['legalIdExp']);
//		if(empty($exp[0])||empty($exp[1])){
//			$this->error('法人证件有效期不能为空或格式不正确');
//		}
//		$arr=[
//			'hybPhone'=>$data['mch_tel'],//登录手机号
//			'bankAccName'=>$data['mch_bank_name'],//入账人姓名
//			'accNum'=>$data['mch_card_id'],//店主身份证号
//			'bankAccNo'=>$data['mch_bank_cid'],//结算银行卡号
//			'bankBranch'=>reload_bank($data['mch_bank_list']),//开户行
//			'bankSubbranch'=>reload_banks($data['mch_linkbnk']),//开户支行
//			'payBankId'=>$data['mch_linkbnk'],//联行号
//			'accType'=>($data['mch_bank_type']=='个人账户')?2:1,//开户类型：对公/私1 对公 2 对私
//			'saleId'=>$this->config['saleId'],//销售ID
//			'rname'=>$data['mch_name'],//商户名称
//			'areaType'=>$data['areaType'],//商户类型；4企业；5个体工商户6个人说明：accType为1时，areaType必须为4
//			'baddr'=>$data['mch_provice'].$data['mch_citys'].$data['mch_district'].$data['mch_address'],//经营地址
//			'raddr'=>$data['mch_provice'].$data['mch_citys'].$data['mch_district'].$data['mch_address'],//详细经营地址 baddr = raddr
//			'localCode'=>$data['localCode']?$data['localCode']:ccb_area($data['mch_district'],$data['mch_citys']),//门店所在地地区代码
//			'settleType'=>'D',//结算类型:T：T+1结算D：D+1结算
//			'legalPerson'=>$data['mch_card_name'],//法人姓名
//			'legalNum'=>$data['mch_card_id'],//法人身份证号
//			'remarks'=>'3',//注册来源,固定值3
//			'contactPerson'=>$data['mch_bank_name'],//联系人= bankAccName
//			'contactPhone'=>$data['mch_tel'],//联系手机号 =hybPhone
//			'businessScope'=>'全行业',//行业范围
//			'isForeign'=>'0',//是否开通储值卡（默认传0）
//			'isHighQualityMer'=>'1',//否是优质客户0是，1否
//			'scanRate'=>bcdiv($data['rate'],1000,4),//费率 优质商户固定为0.0038 非优质商户0.0025~0.006
//			'industryId'=>$data['industryId'],//所属行业（详情见所属行业描述）
//			'bno'=>$data['bno'],//营业执照号
//			'shortName'=>$data['shortName'],//营业执照注册名称
//			'licenceExp'=>$data['licenceExp'],//营业执照有效期
//			'idNumExp'=>$data['legalIdExp'],//入账人身份证有效期
//			'legalIdExp'=>$data['legalIdExp'],//法人身份证有效期
//			'businessType'=>0,//业务类型固定值0
//			'legalUploadFile'=>$this->imgToUrl($data['mch_img_p']),//法人身份证国徽
//			'bupLoadFile'=>$this->imgToUrl($data['mch_img_yyzz']),//营业执照
//			'registryUpLoadFile'=>$this->imgToUrl($data['mch_img_m1']),//门头照
//			'photoUpLoadFile'=>$this->imgToUrl($data['mch_img_m2']),//内部经营照片
//			'materialUpLoadFile'=>$this->imgToUrl($data['mch_img_z']),//法人身份证人像面
//			'materialUpLoad1File'=>$this->imgToUrl($data['mch_img_sqh']),//入账授权书
//			'materialUpLoad2File'=>$this->imgToUrl($data['mch_img_bank']),//结算银行卡正面照
//			'materialUpLoad3File'=>$this->imgToUrl($data['mch_img_p']),//店主身份证国徽面
//			'materialUpLoad4File'=>$this->imgToUrl($data['mch_img_z']),//店主身份证人像面
//			'materialUpLoad5File'=>$this->imgToUrl($data['mch_img_s']),//入账人手持身份证正面
//			'materialUpLoad7File'=>$this->imgToUrl($data['mch_img_m3']),//收银台照片
//			'rupLoadFile'=>$this->imgToUrl($data['mch_img_qy_bank']),//对公账户许可
//			'extraMaterial1'=>$this->imgToUrl($data['mch_img_m4']),//
//			'extraMaterial2'=>$this->imgToUrl($data['mch_img_m5']),//
//			'comment'=>$data['mch_remark'],//备注
//		];
//		$arr=array_filter($arr);
//		$arr['isForeign']=0;
//		$arr['businessType']=0;
//		if($arr['areaType']==4||$arr['areaType']==5){
//			if(empty($data['bno'])){
//				$this->error('营业执照号不能为空');
//			}
//			if(empty($data['shortName'])){
//				$this->error('营业执照注册名称不能为空');
//			}
//			if(empty($data['licenceExp'])){
//				$this->error('营业执照号有效期不能为空');
//			}
//			if(empty($data['legalIdExp'])){
//				$this->error('法人身份证有效期不能为空');
//			}
//			if(empty($data['legalIdExp'])){
//				$this->error('法人身份证有效期不能为空');
//			}
//			if(empty($data['mch_img_p'])){
//				$this->error('法人身份证(国徽)反面不能为空');
//			}
//		}
//		$url=$this->config['mchApiUrl'].'/JHAdminConsole/phone/phoneMicroMerchantInfo_addAggPayMerchantInfo.action';
//		$res=$this->curl_calls($url,$arr);
//		$res=json_decode($res,true);
//		if($res) {
//			if ($res['status'] == 1) {
//				$this->error($res['msg']);
//			} else {
//				//注册成功
//				$mid = $res['obj'][0]['MID'];
//				$mch_id = $mid ? $mid : $res['reportInfo']['mid'];
//				$save = ['status' => 1, 'mch_id' => $mch_id, 'rate' => $data['rate'],'mch_tel'=>$data['mch_tel']];
//				M('mchSellerAlleys')->where(array('cid' => $data['cid'], 'alleys_type' => 'Hyb'))->save($save);
//				$this->success('接口进件同步成功,审核结果请点击状态查询');
//			}
//		}else{
//			$this->error('请求超时');
//		}

	}


	/**
	 * 审核结果
	 */
	public function mch_status(){
		$data=I('post.');
		$phone=M('mchSellerAlleys')->where(array('mch_id' => $data['mch_id'], 'alleys_type' => 'Hyb'))->getField('mch_tel');
		$arr=[
			'phoneNumber'=>$phone,
			'agentId'=>$this->config['orgNo']
		];
		$url=$this->config['mchApiUrl'].'/JHAdminConsole/queryMerchantInfo.do';
		$res=curl_calls($url,json_encode($arr),'',true);
		$res=json_decode($res,true);
		if($res) {
			if ($res['status'] == 1) {
				$this->error($res['message']);
			} else {
				if($res['approveStatus']=="0") {
					M('mchSellerAlleys')->where(array('mch_id' => $data['mch_id'], 'alleys_type' => 'Hyb'))->save(['load_status' => 1]);
					$this->success('审核通过');
				}elseif ($res['approveStatus']=="1"){
					$this->error("审核被退回({$res['message']}),请登录通道方平台修改!");
				}elseif ($res['approveStatus']=="2"){
					$this->error('待审核中');
				}else{
					$this->error('状态'.$res['approveStatus']);
				}
			}
		}else{
			$this->error('请求超时');
		}
	}


	public function curl_calls($urls, $datas)
	{
		$ch = curl_init();
		// 设置curl允许执行的最长秒数
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 200); //尝试连接等待的时间，以毫秒为单位。设置为0，则无限等待。
		//curl_setopt($ch, CURLOPT_TIMEOUT, 800);    //设置cURL允许执行的最长秒数
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// 获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (1 == strpos("$".$urls, "https://"))
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		//发送一个常规的POST请求。
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $urls);
		//要传送的所有数据
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type' => 'multipart/form-data',
		));

		// 执行操作
		$res = curl_exec($ch);
		if ($res == NULL) {
			$res = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
		}
		curl_close($ch);
		return $res;
	}




	/**
	 * 附件图片远程图片转本地
	 * @param $url
	 * @return string
	 */
	public function imgToUrl($url){
		if(empty($url)){
			return '';
		}else {
			if (preg_match('/(http:\/\/)|(https:\/\/)/i', $url)) {
				$path=getcwd() . substr(ImgToLocalUrl($url),1);
			} else {
				$path=getcwd() . $url;
			}
			$info=finfo_open(FILEINFO_MIME_TYPE);// 返回 mime 类型
			$mine=finfo_file($info,$path);
			finfo_close($info);
			return curl_file_create($path,$mine,explode('.',basename($path))[0]);
			//return new \CURLFile($path,basename($path),explode('.',basename($path))[0]);
		}
	}

	/**
	 * 微信扫码
	 */
	public function pay_wx_scan(){
		$arr=[
			'mid'=>$this->Mdata['mch_id'],
			'totalFee'=>$this->data['total'],//单位元
			'outTradeNo'=>$this->oid,
			'nonceStr'=>uniqid(),
			'authCode'=>$this->data['code_data'],
			'orgNo'=>$this->config['orgNo'],
			'channel'=>'wx',//wx/ali
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=$this->postXmlCurl($xml,$this->config['apiUrl'].'/LmfPayFrontService/hyb/micropay');
		$res=$this->FromXml($res);
		if($res['resultCode']=='success'){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['channel'],
				'transaction_id'=>$res['transactionId'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'wx_scan',
				'out_trade_no'=>$arr['outTradeNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Hyb',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			if($res['payCode']=='paying'){ //等待授权
				$type='loading'; //支付中
				$msg='用户支付确认中...';
				$rel = M('mch_orders')->add($array);
			}elseif ($res['payCode']=='success'){ //交易成功
				$type='success'; //支付成功
				$array['status']=1;
				$array['time_end']=time();
				$msg='收款成功 金额:'.$this->data['total'];
				$rel = M('mch_orders')->add($array);
			}
			if ($rel) {
				if($type=='success'){
					R('Pays/Notify/sendTemplateMessage',array($arr['outTradeNo'])); //接口无异步 这里直接给商户发送收款成功模板消息
				}
				$pay_data = array(
					'msg' => $msg,
					'type' => $type,
					'api' => 'Hyb',
					'out_trade_no' => $arr['outTradeNo'],
				);
				$this->success($pay_data);
			} else {
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['errDes']);
		}
	}

	public function pay_ali_scan(){
		$arr=[
			'mid'=>$this->Mdata['mch_id'],
			'totalFee'=>$this->data['total'],//单位元
			'outTradeNo'=>$this->oid,
			'nonceStr'=>uniqid(),
			'authCode'=>$this->data['code_data'],
			'orgNo'=>$this->config['orgNo'],
			'channel'=>'ali',//wx/ali
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=$this->postXmlCurl($xml,$this->config['apiUrl'].'/LmfPayFrontService/hyb/micropay');
		$res=$this->FromXml($res);
		if($res['resultCode']=='success'){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['channel'],
				'transaction_id'=>$res['transactionId'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'ali_scan',
				'out_trade_no'=>$arr['outTradeNo'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Hyb',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			if($res['payCode']=='paying'){ //等待授权
				$type='loading'; //支付中
				$msg='用户支付确认中...';
				$rel = M('mch_orders')->add($array);
			}elseif ($res['payCode']=='success'){ //交易成功
				$type='success'; //支付成功
				$array['status']=1;
				$msg='收款成功 金额:'.$this->data['total'];
				$rel = M('mch_orders')->add($array);
			}
			if ($rel) {
				if($type=='success'){
					R('Pays/Notify/sendTemplateMessage',array($arr['outTradeNo'])); //接口无异步 这里直接给商户发送收款成功模板消息
				}
				$pay_data = array(
					'msg' => $msg,
					'type' => $type,
					'api' => 'Hyb',
					'out_trade_no' => $arr['outTradeNo'],
				);
				$this->success($pay_data);
			} else {
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['errDes']);
		}
	}

	/**
	 * 微信公众号支付
	 */
	public  function pay_wx_jsapi(){
		$arr=[
			'mid'=>$this->Mdata['mch_id'],
			'totalFee'=>$this->data['total'],//单位元
			'outTradeNo'=>$this->oid,
			'nonceStr'=>uniqid(),
			'userid'=>$this->data['openid'],
			'callBackUrl'=>$this->config['callBackUrl'].$this->oid,
			'notifyUrl'=>$this->config['notifyUrl'],
			'orgNo'=>$this->config['orgNo'],
			'payChannel'=>'wx',//wx/ali
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=$this->postXmlCurl($xml,$this->config['apiUrl'].'/LmfPayFrontService/hyb/oriJsPay');
		$res=$this->FromXml($res);
		if($res['resultCode']=='success'){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['payChannel'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'wx_jsapi',
				'out_trade_no'=>$arr['outTradeNo'],
				'transaction_id'=>$res['transactionId'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Hyb',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){

				$pay_data=array(
					'msg'=>'订单创建成功',
					'type'=>'hurl',
					'localurl'=>$res['codeUrl'],
				);
				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['errDes']);
		}

	}


	/**
	 * 支付宝服务窗支付
	 */
	public function pay_ali_jsapi(){
		$arr=[
			'mid'=>$this->Mdata['mch_id'],
			'totalFee'=>$this->data['total'],//单位元
			'outTradeNo'=>$this->oid,
			'nonceStr'=>uniqid(),
			'userid'=>$this->data['openid'],
			'callBackUrl'=>$this->config['callBackUrl'].$this->oid,
			'notifyUrl'=>$this->config['notifyUrl'],
			'orgNo'=>$this->config['orgNo'],
			'payChannel'=>'ali',//wx/ali
		];
		$arr['sign']=$this->sign($arr);
		$xml=$this->ToXml($arr);
		$res=$this->postXmlCurl($xml,$this->config['apiUrl'].'/LmfPayFrontService/hyb/oriJsPay');
		$res=$this->FromXml($res);
		if($res['resultCode']=='success'){
			$array=array(
				'goods_tag'=>$this->data['remark'],
				'trade_type'=>$arr['payChannel'],
				'mid'=>$this->data['sid'],
				'store_id'=>$this->data['id'],
				'agent_id'=>GetMchAid($this->data['sid']),
				'new'=>serialize($arr),
				'data'=>serialize($this->data),
				'rel'=>serialize($res),
				'createtime'=>time(),
				'mch_rate'=>$this->mch_rate,
				'mch_id'=>$this->Mdata['mch_id'],
				'service'=>'ali_jsapi',
				'out_trade_no'=>$arr['outTradeNo'],
				'transaction_id'=>$res['transactionId'],
				'body'=>$this->Sdata['name'],
				'total_fee'=>$this->data['total'], //存数据库按照分进行统计
				'mch_create_ip'=>Get_Clienti_Ips(),
				'sub_openid'=>$this->data['openid'],
				'type'=>'D1',
				'alleys'=>'Hyb',
				'domain_auth'=>domain_auth(),
				'is_raw'=>1,
			);
			$rel=M('mch_orders')->add($array);
			if($rel){
				$pay_data=array(
					'msg'=>'订单创建成功',
					'type'=>'js',
					'pay_info'=>array(
						'tradeNO'=>$res['package']
					),
					'out_trade_no'=>$arr['outTradeNo'],
				);
				$this->success($pay_data);
			}else{
				$this->error('订单创建失败!请重新支付!');
			}
		}else{
			$this->error($res['errDes']);
		}
	}


	/**
	 * 页面同步回调
	 */
	public function ResultData(){
		$out_trade_no=I('get.out_trade_no');
		$rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
		$store = Get_Store($rel['store_id']);
		#点击完成到支付页面
		$codes = M('MchCodes')->where(array('store_id' => $rel['store_id'], 'mch_id' => $rel['mid']))->getField('codes');
		$res = self::orderResult($out_trade_no);
		switch ($res['res_status']){
			case 1:
				$status='ok'; //结果为ok 视图结果为支付成功
				break;
			default:
				$status=$res['msg'];
				break;
		}
		$assign = array(
			'status' => $status,
			'total' => number_format($rel['total_fee'], 2),
			'mch_name' => $store['name'],
			'time' => date('Y-m-d H:i:s', $rel['createtime']),
			'order_id' => $rel['out_trade_no'],
			'url' => C('MA_DATA_URL') . '/' . $codes
		);
		$this->assign($assign);
		$this->display('Notify/new_result'); //视图页
	}

	#订单查询接口
	public function pay_getOrderStatus($oid){
		$res=self::orderResult($oid);
		if($res['status'] == true&& $res['res_status']==1){
			$pay_data=array(
				'msg'=>'收款成功',
				'type'=>'success',
			);
			$this->success($pay_data);
		}else{
			$this->error($res);
		}
	}

	/**
	 * 异步处理
	 */
	public function notifyUrl(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		rwlog('hybNotify',$xml);
		$xml=$this->FromXml($xml);
		$oid=M('MchOrders')->where(['out_trade_no'=>$xml['outTradeNo']])->getField('out_trade_no');
		if($oid) {
			$array = array(
				'notify_time' => time(),
				'notify_data' => json_encode($xml, JSON_UNESCAPED_UNICODE),
				'time_end' => strtotime($xml['tradeTime']),
			);
			$rel = M('MchOrders')->where(array('out_trade_no' => $oid))->save($array);
			//使用订单查询结果更新支付订单状态
			if ($rel) {
				$res = self::orderResult($oid);
				if ($res['status'] == true&&$res['res_status']!=0) { //订单支付成功
					die('SUCCESS');
				}
			} else {// 告诉异步结果处理失败
				die('error');
			}
		}else{
			die('error no order');
		}
	}


	/**
	 * 订单查询
	 * @param $oid
	 * @return array
	 */
	public function orderResult($oid){
		$order = M('MchOrders')->where(['out_trade_no' => $oid])->find();
		if($order) {
			$arr = [
				'mid' =>$order['mch_id'],
				'outTradeNo' => $order['out_trade_no'],
				'nonceStr' => uniqid(),
				'orgNo' => $this->config['orgNo'],
			];
			$arr['sign']=$this->sign($arr);
			$xml = $this->ToXml($arr);
			$res = $this->postXmlCurl($xml, $this->config['apiUrl'] . '/LmfPayFrontService/hyb/orderquery');
			$res = $this->FromXml($res);
			if($res['resultCode']=='success') {
				switch ($res['orderStatus']){
					case 'success':
						$status=1;
						break;
					case 'refunding':
						$status=2;
						break;
					case 'refund':
						$status=2;
						break;
					default:
						$status=0;
						break;
				}
				//更新数据库结果
				$save = [
					'status' => $status,
					'time_end' => strtotime($res['timeEnd']),
					'total' => $res['totalFee'],//分单位转换为元
					'out_transaction_id' => $res['transactionId'],
				];
				M('MchOrders')->where(['out_trade_no' =>$oid])->save($save);
				if ($status == 1) {
					//发送模板消息通知
					R('Pays/Notify/sendTemplateMessage', array($oid)); //发送收款成功模板消息
				};
				$return = ['status' => true, 'res_status' => $status, 'msg' => '查询成功'];
			}else{
				$return = ['status' => false, 'msg' => $res['errDes'] . '[' . $res['resultCode'] . ']'];
			}
		}else{
			$return = ['status' => false, 'msg' => '未知订单'];
		}
		return $return;
	}

	/**
	 * 签名
	 * @param $data
	 * @param bool $type
	 * @return string
	 */
	public function sign($data,$type=false){
		ksort($data);
		$tmp = '';
		foreach ($data as $k => $v ) {
			if($k == 'sign' || $v == '' || $v == null) continue;
			$tmp .= $k . '=' . $v . '&';
		}
		$tmp .= 'key=' . $this->config['key'];
		return $type?$tmp:strtoupper(md5($tmp));
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
			$xml.="<".$key.">".$val."</".$key.">";
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
	 * 以post方式提交xml到对应的接口url
	 * @param $xml需要post的xml数据
	 * @param $url
	 * @param int $secondurl执行超时时间，默认30s
	 * @return mixed
	 */
	private  function postXmlCurl($xml, $url,$headers='Content-type: text/xml',$second = 30)
	{
		$header[] =$headers;//定义content-type为xml
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
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
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
			return E("curl出错，错误码:$error.$header_size.$response_header");
		}
	}
}