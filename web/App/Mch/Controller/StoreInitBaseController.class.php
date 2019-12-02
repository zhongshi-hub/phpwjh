<?php
namespace Mch\Controller;
use Think\Controller;
class StoreInitBaseController extends Controller {

    public function _initialize(){
        $domain=domain_rel();
        $_domain=M('domain_auth')->where(array('web_domain'=>$domain))->find();
        $this->assign('_domain',$_domain);
        $Seller = M('MchSeller')->where(array('id' => $_SESSION['mch']['id'],'domain_auth' => domain_auth()))->find();
        $this->assign('_seller',$Seller);
        #获取代理电话
        $agent=M('MchAgent')->where(array('id'=>$Seller['agent_id']))->getField('user_phone');
        $this->assign('atel',$agent);
        //验证用户状态
        if(empty($_SESSION['store'])){
            $this->error('登录超时,请重新登录',U('Mch/Login/store'));
        }
    }



}