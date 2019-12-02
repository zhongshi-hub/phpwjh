<?php
/**
云喇叭播报接口类
@author		chencunlong@126.com
@link		http://www.xunmafu.com
 */
class ylb
{
    /**
     *请求地址
     */
    const API_URL = 'https://api.gateway.letoiot.com/';

    /**
     * 构造方法
     * ylb constructor.
     * @param string $appid
     * @param string $appkey
     * @param string $uid
     */
    public function __construct($appid = '', $appkey = '',$uid='')
    {

        $this->appid	= $appid?:'';
        $this->appkey   = $appkey?:'';
        //用户和密码可直接写在类里
        $this->apiURL = self::API_URL;
        $this->uid = $uid?:str_replace('.','',$_SERVER['SERVER_NAME']);
    }

    /**
     * 云喇叭播报
     * @param $id喇叭ID
     * @param int $vol 音量
     * @param $price金额单位分
     * @param int $pt 类型1 支付宝、2 微信支付、3 云支付、4 余额支付、5 微信储值、6 微信买单、7 银联刷卡
     * @return array
     */
    public function speaker($id,$vol=100,$price,$pt=1){
			$arr = [
				'id' => $id,
				'uid' => $this->uid,
				'vol' => $vol,//音量大小 0-100
				'price' => $price,//金额 单位分
				'pt' => $pt,//1 支付宝、2 微信支付、3 云支付、4 余额支付、5 微信储值、6 微信买单、7 银联刷卡
			];
			$url = $this->apiURL . 'speaker/add.php?' . http_build_query($arr);
			$res = $this->get_curl_calls($url);
			$res = json_decode($res, true);
			if ($res['errcode'] == 0) {
				return ['status' => 1, 'msg' => '播报信息报送成功'];
			} else {
				return ['status' => 0, 'code' => $res['errcode'], 'msg' => $res['errmsg'] . '(' . $res['errcode'] . ')'];
			}
    }


    /**
     * 设备状态查询
     * @param $id
     * @return array
     */
    public function status($id){

			$url = $this->apiURL . 'speaker/speaker/search-device-online-state';
			$arr = [
				'devNo' => $id
			];
			$res = $this->post_curl_calls($url, json_encode($arr, JSON_UNESCAPED_UNICODE), '', true);
			$res = json_decode($res, true);
			if ($res['code'] == 0) {
				return ['status' => 1, 'msg' => '通信成功', 'state' => $res['data']['state'] == 1 ? '在线' : '离线'];
			} else {
				return ['status' => 0, 'msg' => $res['message'] . '(' . $res['code'] . ')'];
			}

    }

    /**
     * 绑定/解绑设置
     * @param $id 喇叭ID
     * @param int $type 0 为解绑，1 为绑定
     * @return array
     */
    public function bind($id,$type=1){

			$arr = [
				'id' => $id,//云喇叭ID
				'm' => $type,//0 为解绑，1 为绑定
				'uid' => $this->uid,
			];
			$url = $this->apiURL . 'speaker/bind.php?' . http_build_query($arr);
			$res = $this->get_curl_calls($url);
			$res = json_decode($res, true);
			if ($res['errcode'] == 0 || $res['errcode'] == 4) {
				return ['status' => 1, 'msg' => ($type ? '绑定' : '解绑') . '成功'];
			} else {
				return ['status' => 0, 'code' => $res['errcode'], 'msg' => $res['errmsg'] . '(' . $res['errcode'] . ')'];
			}
		
    }

    /**
     * 获取签名授权
     * @return mixed|string
     */
    public function loadSign(){
        $sign_cache=S('ylb_sign');
        if($sign_cache){
            return $sign_cache;
        }else {
            $url = $this->apiURL.'gateway/api/v2/getSignature';
            $arr = [
                'app_cust_id' => $this->appid,
                'app_cust_pwd' => $this->appkey,
            ];
            $res = curl_calls($url, json_encode($arr, JSON_UNESCAPED_UNICODE), '', true);
            $res = json_decode($res, true);
            if ($res['code'] == 0) {
                $sign = $res['data']['signature'];
                $time = $res['data']['remainTime'];
                if ($time > 0) {
                    S('ylb_sign', $sign, $time * 60);
                    return $sign;
                } else {
                    return $sign;
                }
            } else {
                return $res['msg'] . '(' . $res['code'] . ')';
            }
        }
    }


    /**
     * GET Curl方法
     * @param $curl
     * @param bool $https
     * @return mixed
     */
    public function get_curl_calls($curl, $https = true)
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
        $httpHeaders = array(
            "Authorization:".$this->loadSign(),
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }

    /**
     * POST构造方法
     * @param $urls
     * @param $datas
     * @param null $get
     * @param $json
     * @return mixed|string
     */
    public function post_curl_calls($urls, $datas, $get = null,$json)
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
                "Authorization:".$this->loadSign(),
            ));
        }else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-type' => 'multipart/form-data',
                "Authorization:".$this->loadSign(),
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

}