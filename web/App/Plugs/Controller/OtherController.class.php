<?php
namespace Plugs\Controller;
use Think\Controller;
/*
 * Sms扩展类
 * */

class OtherController extends Controller
{


    public function _initialize()
    {
        Vendor('SmsApi');
    }

    public function SendSms(){
        $mch_id=I('get.mid');
        //根据ID获取
        $Seller=M('MchSellerAlleys')->where(array('cid'=>$mch_id,'alleys_type'=>'Sftpays','domain_auth'=>domain_auth()))->find();
        if(!$Seller){
            echo '未获取到商户';
        }else {
            $uid = 'ducc';
            $pwd = 'dong123456';
            $api = new \SmsApi($uid, $pwd);
            //发送的手机 多个号码用,英文逗号隔开
            $mobile = $Seller['mch_tel'];
            //echo $mobile;
            //短信内容
            $content = '您好，您的商户' . $Seller['mch_name'] . '已经成功开通V通道(终端费率' . $Seller['rate'] . '‰)秒到，请登录讯码付公众号使用！如有疑问请联系公众号在线客服';
            //echo $content;
            //发送全文模板短信
            $result = $api->sendAll($mobile,$content);

            if($result['stat']=='100')
            {
                echo '发送商户ID:'.$mch_id.'<br>';
                echo '发送手机号:'.$mobile.'<br>';
                echo '发送内容:'.$content.'<br>';
                echo '发送结果: 发送成功';
            }
            else
            {
                echo '发送商户ID:'.$mch_id.'<br>';
                echo '发送手机号:'.$mobile.'<br>';
                echo '发送内容:'.$content.'<br>';
                echo '发送结果:发送失败:'.$result['stat'].'('.$result['message'].')';
            }
        }
    }



    #微信图片防盗链
    public function WxImg(){
        $data=I('get.');
        $url = Xdecode($data['attach']);
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_REFERER, 'http://www.qq.com');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $tmpInfo = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        header('Content-type: image/jpg');
        echo $tmpInfo;
        exit();
    }






}