<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 动态加载js css 压缩处理
 */
class MinifyController extends Controller{

    public function index(){
        //引入压缩类库
        Vendor('Minify.index');
    }


    public function test(){
        $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
        $ret = $client->connect("127.0.0.1", 9502);
        if(empty($ret)){
            echo '任务推送失败';
        } else {
            $client->send("推送的任务内容");
        }
    }


    public function mq(){
       /* Vendor('Ten_topic');
        // 从腾讯云官网查看云api的密钥信息
        $secretId = "AKIDxjHaicWiy0sVt3A86qLbgW7TbUBeJsgG";
        $secretKey = "TiiU1JMAj39zby76qLbcPzLVR8anlZCp";
        $endPoint = "https://cmq-topic-gz.api.qcloud.com";
        $instance = new  \TopicDemo($secretId, $secretKey, $endPoint);
        $instance->set();*/


       /* Vendor('Ali_topic');
        $accessId = C('Ali_MNS.accessId');
        $accessKey = C('Ali_MNS.accessKey');
        $endPoint = C('Ali_MNS.endPoint');
        $Top_name=C('Ali_MNS.topName');
        $data='test_notifys';



        $instance = new \CreateTopicAndPublishMessage($accessId, $accessKey, $endPoint);
        $res=$instance->set($data,$Top_name);*/

        $data=array(
          'mc'=>'QrCode', #模块
          'ac'=>'adds' #方法
        );
        
        $res=ali_mns($data);
        dump($res);


    }

    public function Ali_TEST(){


        rwlog('ali_test',$_REQUEST);
        rwlog('ali_test1',$GLOBALS["HTTP_RAW_POST_DATA"]);

    }


}