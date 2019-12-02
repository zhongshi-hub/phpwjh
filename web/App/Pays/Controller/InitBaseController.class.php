<?php
namespace Pays\Controller;
use Think\Controller;
class InitBaseController extends Controller {

    public function _initialize(){
        $domain=domain_rel();
        $_domain=M('domain_auth')->where(array('web_domain'=>$domain))->find();
        $this->assign('_domain',$_domain);

        if(!$_domain){
            $content='服务未授权!请联系专员!';
            die($content);
        }

        if($_domain['status']!=1){
            $content='服务已被停止!请联系专员!';
            die($content);
        }


        #获取配置
        $this->system=M('SystemConfig')->where(array('domain_auth'=>domain_auth()))->find();
        $assign=array(
            'auth'=>unserialize($this->system['auth_data']),
            'sys_xy'=>unserialize($this->system['xy_data']),
            'sys'=>$this->system,
        );
        $this->assign($assign);
    }

    #OAuth 获取用户信息
    public function _oauth($type,$wxid=null){
        if($wxid){
            $wid= $wxid;
        }else{
            $wid= GetWxId('m');
        }
        $oauth = &load_wechat('Oauth',$wid);
        $User = &load_wechat('User',$wid);
        $callback = get_url();
        $state = 'Pays';
        if($type=='base'){
            if (!I('get.code')) {
                $scope = 'snsapi_base';
                $result = $oauth->getOauthRedirect($callback, $state, $scope);
                if ($result === FALSE) {
                    return false;
                } else {
                    redirect($result);
                }
            }else{
                $Token = $oauth->getOauthAccessToken();
                if ($Token === FALSE) {
                    return false;
                } else {
                    $_SESSION['openid']=$Token['openid'];
                    return $Token;
                }
            }

        }elseif ($type=="base_xun"){
            if (!I('get.code')) {
                $scope = 'snsapi_base';
                $result = $oauth->getOauthRedirect($callback, $state, $scope);
                if ($result === FALSE) {
                    return false;
                } else {
                    redirect($result);
                }
            }else{
                $Token = $oauth->getOauthAccessToken();
                if ($Token === FALSE) {
                    return false;
                } else {
                    $result = $User->getUserInfo($Token['openid']);
                    if($result['subscribe']==1){ //已关注
                        #保存商户微信信息到商户数据里
                        //$_SESSION['openid']=$Token['openid'];
                        return $result;
                    }else{//未关注
                        redirect(U('Pays/MchReg/WxNo'));
                    }
                }
            }
        }elseif ($type=="base_info"){
            if (!I('get.code')) {
                $scope = 'snsapi_base';
                $result = $oauth->getOauthRedirect($callback, $state, $scope);
                if ($result === FALSE) {
                    return false;
                } else {
                    redirect($result);
                }
            }else{
                $Token = $oauth->getOauthAccessToken();
                if ($Token === FALSE) {
                    return false;
                } else {
                    #根据获取到的信息获取用户信息
                    $User_info = $User->getUserInfo($Token['openid']);
                    if ($User_info === FALSE) {
                        return false;
                    } else {
                        return $User_info;
                    }
                }
            }


        }else{
            if (!I('get.code')) {
                // SDK实例对象
                $scope = 'snsapi_userinfo';
                $result = $oauth->getOauthRedirect($callback, $state, $scope);
                if ($result === FALSE) {
                    return false;
                } else {
                    redirect($result);
                }
            } else {
                $Token = $oauth->getOauthAccessToken();
                if ($Token === FALSE) {
                    return false;
                } else {
                    $_SESSION['openid']=$Token['openid'];
                    $User_info = $oauth->getOauthUserinfo($Token['access_token'], $Token['openid']);
                    if ($User_info === FALSE) {
                        return false;
                    } else {
                        return $User_info;
                    }
                }
            }
        }
    }


}