<?php
namespace Plugs\Controller;
use Think\Controller;
/*
 * 爆客服务端扩展类
 * */

class BKController extends Controller
{
    public function _initialize(){
        $this->data=I('param.');
        $this->login=self::login_data();
    }

    public function send()
    {
       #用户列表
       $data_list=self::user_list();
       #发送消息
       $url='https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxsendmsg?lang=zh_CN&pass_ticket='.$this->login['pass_ticket'];
       /*
        * {"BaseRequest":{"Uin":1717417773,"Sid":"+w5KY/g+gHV4JrvR","Skey":"@crypt_6aedc2f6_24daba52b730cc093963f00fe3878f86"
,"DeviceID":"e580993827631390"},"Msg":{"Type":1,"Content":"[咒骂]1","FromUserName":"@96353cb7d05169dfbf3b09fffbec897d1720c5a6f5245f7d1dca2990609d3eee"
,"ToUserName":"@d914c674ba792f37bfea620466fd441e","LocalID":"15047434956040810","ClientMsgId":"15047434956040810"
},"Scene":0}
        * */
       $getMillisecond=self::getMillisecond();
       /*foreach ($data_list['ToUser'] as $val) {
           $arr = array(
               'BaseRequest' => array(
                   'DeviceID' => 'e' . RandStr(15),
                   'Sid' => $this->login['wxsid'],
                   'Skey' => $this->login['skey'],
                   'Uin' => $this->login['wxuin'],
               ),
               'Msg' => array(
                   'Type' => 1,
                   'Content' => '早安!TEST!',
                   'FromUserName' => $data_list['User'],
                   'ToUserName' => $val,
                   'LocalID' => $getMillisecond,
                   'ClientMsgId' => $getMillisecond
               ),
           );
           $res=curl_calls($url,json_encode($arr,JSON_UNESCAPED_UNICODE));
           dump($res);

       }*/


       dump($data_list);
       dump($url);

    }

    #获取用户列表
    public function user_list(){
        $url='https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxgetcontact?lang=zh_CN&seq=0&skey='.$this->login['skey'];
        $res=curl_calls($url,'',1);
        //$res=json_decode($res,true);
        return $res;
    }
    #获取初始用户列表
    public function data_list(){

        $data=array(
            'BaseRequest'=>array(
                'DeviceID'=>'e'.RandStr(15),
                'Sid'=>$this->login['wxsid'],
                'Skey'=>$this->login['skey'],
                'Uin'=>$this->login['wxuin'],
            ),
        );
        $url='https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxinit?r=-1502766374&lang=zh_CN&pass_ticket='.$this->login['pass_ticket'];
        $res=curl_calls($url,json_encode($data));
        $res=json_decode($res,true);
        $ChatSet=explode(',',$res['ChatSet']);
        $out_arr=array();
        $type=$this->data['type'];
        foreach ($ChatSet as $v){
            if($v!=''&& $v !='weixin'&& $v !='filehelper'){
                #判断要发送的类型
                if($type==1){ #好友
                    if(strlen($v) < 34){
                        $out_arr[]=$v;
                    }
                }elseif($type==2){ #群
                    if(strlen($v) > 34){
                        $out_arr[]=$v;
                    }
                }else{#好友+群
                    $out_arr[]=$v;
                }
            }
        }
        $rel=array(
            'User'=>$res['User']['UserName'],
            'ToUser'=>$out_arr
        );
        return $rel;
    }

    #获取签名信息
    public function login_data(){
        $uuid = $this->data['uuid'];
        $tiket= $this->data['tiket'];
        $url='https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxnewloginpage?ticket='.$tiket.'&uuid='.$uuid.'&lang=zh_CN&scan='.time().'&fun=new&version=v2&lang=zh_CN';
        $res=curl_calls($url,'',1);
        $res=self::xmlctojson($res);
        return $res;
    }

    //解析XML
    public function xmlctojson($str){
        $obj = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON,true);
        return $dJSON;
    }

    public  function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return $t2 . ceil($t1 * 1000);
    }

}