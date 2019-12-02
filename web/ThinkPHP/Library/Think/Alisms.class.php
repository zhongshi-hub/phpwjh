<?php
namespace Think;
header("Content-Type:text/html;charset=UTF-8");

class Alisms{
    // 保存错误信息
    public function __construct($config = array()) {
        // 配置参数
        $this->accessKeyId = $config['key'];
        $this->accessKeySecret = $config['secret'];
    }
    /**
     * 短信接口
     */
    function sms_send($sms_data=array()){
        $paramArr = array(
            'app_key' => $this->accessKeyId,
            'method' => 'alibaba.aliqin.fc.sms.num.send',
            'format' => 'json',
            'v' => '2.0',
            'sign_method'=>'md5',
            'timestamp' => date('Y-m-d H:i:s'),
            'fields' => 'nick,type,user_id',
            'sms_type' => 'normal',
            'sms_free_sign_name' => $sms_data['sign'],
            'sms_param' => $sms_data['param'],
            'rec_num' => $sms_data['mobile'],
            'sms_template_code' => $sms_data['code'],
        );
        //生成签名
        $sign = $this -> createSign($paramArr, $this->accessKeySecret);
        $strParam = $this -> createStrParam($paramArr);
        $strParam .= 'sign='.$sign;
        $url = 'http://gw.api.taobao.com/router/rest?'.$strParam; //正式环境调用地址
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        if($arr['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code']==0&&$arr['alibaba_aliqin_fc_sms_num_send_response']['result']['success']==true){
            return $arr['alibaba_aliqin_fc_sms_num_send_response']['result'];
        }else{
            return $arr['error_response'];
        }
    }
    /*
     * 签名
     * */
    function createSign ($paramArr, $secret) {
        $sign = $secret;
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= $key.$val;
            }
        }
        $sign.=$secret;
        $sign = strtoupper(md5($sign));
        return $sign;
    }
    /**
     * 组装参数
     */
    function createStrParam ($paramArr) {
        $strParam = '';
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $strParam .= $key.'='.urlencode($val).'&';
            }
        }
        return $strParam;
    }
}


