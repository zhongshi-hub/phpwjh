<?php
namespace Pays\Controller;
use Think\Controller;
class FaceApiController extends Controller
{
	protected  $post;
	protected  $terminal;
	protected  $api_data;
	protected  $methodType;
	public function  _initialize(){
		#基础验证 请求协议限制
		if(C('API_CONFIG.IS_HTTPS')==true){
			if(!self::isHttps())$this->json_data(400,'请使用https协议访问'); //非Https直接输出
		}
		#独立Api域名限制
		if(C('API_CONFIG.IS_API_DOMAIN')==true&&C('API_CONFIG.API_DOMAIN')){ //启用独立Api域名
			if($_SERVER['HTTP_HOST']!=C('API_CONFIG.API_DOMAIN'))$this->json_data(400,'接口域名错误,请联系相关人员!');
		}
		if(IS_POST) {
			$this->post = json_decode(file_get_contents("php://input"), true);
			$api_data = $this->post['data'];
			//rwlog("faceApi_".date('Ymd'),['time'=>date('Y-m-d H:i:s'),'data'=>$this->post]);
			$api_data['appid'] = $this->post['appid'];
			$this->terminal = self::terminal_data($this->post['appid']);
			//判断是否指定商户门店信息
			$db = M('MchStore');
			if ($api_data['store_id']) {
				$res = $db->where(['id' => $api_data['store_id'], 'sid' => $this->terminal['mch_id']])->field('id,sid,name')->find();
				$res?$api_data['store_data'] = $res:$this->json_data(206,'门店信息不存在');
			} else { //如果不穿门店ID 则默认选择一个
				$res = $db->where(['sid' => $this->terminal['mch_id']])->limit(1)->field('id,sid,name')->find();
				$res?$api_data['store_data'] = $res:$this->json_data(206,'门店信息不存在');
			}
			//validate验证
			if(!is_array($api_data)){$this->json_data(103,'data array error');}
			if(!$this->post['appid']){$this->json_data(203,'appid error');}
			if(1!=$this->terminal['status']){$this->json_data(203,'终端已关闭');}
			#判断签名
			$sign =self::sign($api_data,$this->terminal['appkey']);
			if($this->post['test']=='ccl'){$this->json_data(300,$sign);}
			if($sign!=$this->post['sign']){$this->json_data(300,'签名错误');}
			#判断接口类型
			if (!$this->post['method']){$this->json_data(103,'method error');}
			if(!$api_data['nonce_str']){$this->json_data(103,'nonce_str不可为空');}

			$postMethod=explode('_',$this->post['method']);
			if(in_array($postMethod[0],['wx','ali'])) {
				$alley = $postMethod[0] == 'wx' ? 'WxPay' : 'Aliisv';
				$api_data['alley_data'] = self::mchAlley($alley);
			}
			$this->api_data = $api_data;
			$this->methodType=[ucfirst($postMethod[0]),$postMethod[1]];
		}else{
           $this->json_data(101,'非法通信');
		}
	}
	/**
	 * 入口方法
	 */
	public function gateway(){
		$module = A("Pays/Face{$this->methodType[0]}");
		$modules = method_exists($module, $this->methodType[1]);
		if ($modules) {
			$data = R("Pays/Face{$this->methodType[0]}/" .$this->methodType[1],[$this->api_data]);
			if (is_array($data)) {
				return $this->json_data($data['code'], $data['msg'],$data['data']);
			} else {
				return $data;
			}
		} else {
			return $this->json_data(502,"刷脸接口({$this->methodType[0]}/{$this->methodType[1]})不存在");
		}
	}


	/**
	 * 签名
	 * @param array $data
	 * @param $key
	 * @param null $type
	 * @return string
	 */
	public static function sign($data=array(),$key,$type=null){
		ksort($data);
		$tmp = '';
		foreach ($data as $k => $v ) {
			if($k == 'sign' ||$k == 'order_data' ||$k == 'store_data'||$k == 'appid' || $v == '' || $v == null) continue;
			$tmp .= $k . '=' . $v . '&';
		}
		$tmp .= 'key=' . $key;
		return $type?$tmp:strtoupper(md5($tmp));
	}


	/**
	 * 商户终端信息
	 * @param $appid
	 */
	public function terminal_data($appid){
		$res=M('mchTerminal')->where(array('appid'=>$appid))->find();
		return $res?$res:$this->json_data(204,'无效appid');
	}

	/**
	 * 商户通道信息
	 * @param $type
	 * @return mixed
	 */
	public function mchAlley($type){
		$db = M('mchSellerAlleys');
		$res=$db->where(['alleys_type'=>$type,'cid'=>$this->terminal['mch_id']])->field('id,cid,rate,mch_id,mch_key,mch_appid,agent_id,domain_auth,api_rel')->find();
		$res['api_rel']=unserialize($res['api_rel']);
		return $res['mch_id']?$res:$this->json_data(204,'商户未开通本产品');
	}


	/**
	 * Api返回数据信息
	 * @param $code
	 * @param $msg
	 * @param array $data
	 */
	public function json_data($code,$msg,$data=[]){
		header('Content-type: application/json;charset=utf-8');
		header('Auth: zzxunlong chen for xunmafu.com');
		$json=['code'=>$code,'msg'=>$msg];
		if(is_array($data)&&!empty($data)){
			$data['nonce_str']='Face'.uniqid();
			$json['data']=$data;
			$json['sign']=self::sign($data,$this->terminal['appkey']);
		}
		$ret=json_encode($json,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
		rwlog("faceJsonApiLog_".date('Ymd'),['time'=>date('Y-m-d H:i:s'),'post'=>$this->post,'data'=>$ret]);
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