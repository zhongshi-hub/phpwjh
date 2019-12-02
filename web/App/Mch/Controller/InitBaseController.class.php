<?php
namespace Mch\Controller;
use Think\Controller;
class InitBaseController extends Controller {

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
        if(empty($_SESSION['mch']['id'])){
            session('mch', null);
            //$this->error('登录超时,请重新登录',U('Mch/Login/index'));
            redirect(U('Mch/Login/index'));
        }





        if(!$_domain){
            $content='服务未授权!请联系专员!';
            die($content);
        }

        if($_domain['status']!=1){
            $content='服务已被停止!请联系专员!';
            die($content);
        }


        #获取配置
        $system=M('SystemConfig')->where(array('domain_auth'=>domain_auth()))->find();
        $assign=array(
          'sys_xy'=>unserialize($system['xy_data']),
          'sys'=>$system,
        );
        $this->assign($assign);
    }

    

}