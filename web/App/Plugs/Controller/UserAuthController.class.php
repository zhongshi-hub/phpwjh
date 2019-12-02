<?php
namespace Plugs\Controller;
use Think\Controller;
/*
 * 芝麻信用_芝麻认证
 * Time:2017-10-25 22:00
 * */

class UserAuthController extends Controller
{

    #初始化函数
    public function _initialize(){
        $this->api=array(
            'init'=>'http://dm-102.data.aliyun.com/rest/161225/zmxy/api/zhima.customer.certification.initialize.json',
            'certify'=>'http://dm-102.data.aliyun.com/rest/161225/zmxy/api/zhima.customer.certification.certify.json',
            'query'=>'http://dm-102.data.aliyun.com/rest/161225/zmxy/api/zhima.customer.certification.query.json'
        );

        $zm_auth=DomainAuthData('zm_auth');
        if($zm_auth){
            $this->appcode=$zm_auth;
        }else{
            $this->error('当前系统未开启认证服务');
            //$this->appcode='2f9c090c8ba841208eb24642281b640e';
        }
        $this->return_url='http://' . $_SERVER['HTTP_HOST'] .'/Plugs/UserAuth/return_url';
    }



    #芝麻信用认证
    public function zm_auth(){
        $data=json_decode(Xdecode(I('get.mch')),true);
        $user_agent=USER_AGENT();
        if($user_agent=='qq'||$user_agent=='wx'){
            $this->display();
        }else{
           $map=array(
             'id'=>$data['mch_id'],
             'domain_auth'=>$data['domain_auth']
           );
           $res=M('MchSeller')->where($map)->find();
           if($res){
               $zmDb=M('MchZmAuth');
               $zm_auth=$zmDb->where(array('mid'=>$res['id']))->find();
               #判断当前商户是否认证成功过
               if($res['auth_status']==1&&$zm_auth['status']==1){
                  $this->success('此商户已认证,无需重新认证','',888);
               }else{
                   #没有认证成功。进行认证
                    #初始化 获取bizNo
                   $zm_init=self::zm_init($res);
                   if($zm_init['success']==true){
                       $bizNo=$zm_init['data']['bizNo'];
                       if($bizNo) {
                           #增加日志
                           $arr=array(
                             'mid'=>$res['id'],
                             'biz_no'=>$bizNo,
                             's_time'=>date('Y-m-d H:i:s'),
                             'domain_auth'=>$res['domain_auth']
                           );
                           #先判断当前商户是否存在认证记录 如果有 直接更新为新的
                           $zmDb=M('MchZmAuth');
                           $count=$zmDb->where(array('mid'=>$res['id']))->count();
                           if($count){
                               $zmDb->where(array('mid'=>$res['id']))->save($arr);
                           }else{
                               $zmDb->add($arr);
                           }
                           #增加完记录  进入认证
                           self::certify($bizNo);
                       }else{
                           $this->error('认证接口通信成功,错误:'.$zm_init['message'],'',888);
                       }
                   }else{
                       $this->error('认证接口初始化失败','',888);
                   }
               }
           }else{
             $this->error('未检索到此商户','',888);
           }
        }
        //dump($user_agent);
    }



    #芝麻认证初始化
    public function zm_init($mch){
        $data=array(
          'bizCode'=>'FACE',
          'identityParam'=>json_encode(array(
              'identity_type'=>'CERT_INFO',
              'cert_type'=>'IDENTITY_CARD',
              'cert_name'=>$mch['mch_card_name'],
              'cert_no'=>$mch['mch_card_id'],
          ),JSON_UNESCAPED_UNICODE)
        );
        $res=self::zm_curl($this->api['init'],$data);
        $res=json_decode($res,true);
        return $res;
        //{"success":true,"code":null,"message":null,"data":{"bizNo":"ZM201710263000000474700273283763"}}
        //dump($res);
    }


    #芝麻认证开始认证
    public function certify($bizNo){
        $data=array(
            'bizNo'=>$bizNo,
            'returnUrl'=>$this->return_url.'/biz/'.$bizNo
        );
        $res=self::zm_curl($this->api['certify'],$data);
        $res=json_decode($res,true);
        if($res['success']==true){
            if($res['data']) {
                redirect($res['data']);
            }else{
                $this->error('认证链接获取失败!提示:'.$res['message'],'',888);
            }
        }else{
            $this->error('认证接口获取失败!提示:'.$res['message'],'',888);
        }
    }


    #同步回调
    public function return_url(){
        $bizNo=I('get.biz');
        $zmDb=M('MchZmAuth');
        $res=$zmDb->where(array('biz_no'=>$bizNo))->find();
        if($bizNo){
            $rel=self::query($bizNo);
            if($rel['success']==true){
                 if($rel['data']['passed']!='false'){
                    #更新认证结果
                    $arr=array(
                      'e_time'=>date('Y-m-d H:i:s'),
                      'status'=>1,
                      'rel'=>json_encode($rel)
                    );
                    $zmDb->where(array('biz_no'=>$bizNo))->save($arr);
                    #更新商户结果
                    $save=array(
                      'auth_type'=>'zm_auth',
                      'auth_status'=>1
                    );
                    M('MchSeller')->where(array('id'=>$res['mid'],'domain_auth'=>$res['domain_auth']))->save($save);
                    $user_agent=USER_AGENT();

                    $TemplateId=GetPayConfig(domain_auth(), 'auth_template_id');
                    $TemplateStatus=GetPayConfig(domain_auth(), 'auth_template_status');
                    if($TemplateStatus==1&&$TemplateId){
                         sendMchTemplateMessage($res['mid'], 'auth');
                    }
                    if($user_agent=='wx'){
                         $this->success('认证成功,自动返回商户中心使用更多功能!',U('Mch/Index/index'),5);
                    }else{
                         $this->success('认证成功,请返回微信端商户中心使用更多功能!','',888);
                    }
                }else{
                     $mch = Xencode(json_encode(array(
                         'domain_auth' => $res['domain_auth'],
                         'mch_id' => $res['mid'],
                     )));
                     $url = U('Plugs/UserAuth/zm_auth', array('mch' => $mch));
                     $this->error('认证失败!立即进行重新认证',$url);

                    /*$url=U('Mch/Index/zm_auth');
                    redirect($url);*/
                }
            }else{
                $this->error('查询失败!提示:'.$rel['message'],'',888);
            }
        }else{
            $this->error('未获取到认证号,信息识别失败!','',888);
        }
    }


    #芝麻认证查询
    public function query($bizNo){
        $data=array(
            'bizNo'=>$bizNo,
        );
        $res=self::zm_curl($this->api['query'],$data);
        $res=json_decode($res,true);
        return $res;
    }



    #Curl类
    public function zm_curl($url,$data){
        $method = "POST";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $_data =http_build_query($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$url, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
        $res = curl_exec($curl);
        //rwlog('card_res',$res);
        //dump($_data);
        return $res;
    }





}