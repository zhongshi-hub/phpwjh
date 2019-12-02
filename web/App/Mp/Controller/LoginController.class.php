<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Login登录基类控制器
 */
class LoginController extends BaseController{
    

    public  function  index(){
        if(IS_POST){
            $data=I('post.');
            if(empty($data['username'])){$this->error('请输入手机号');}
            if(empty($data['password'])){$this->error('请输入密码');}
            if(empty($data['verify'])){$this->error('请输入验证码');}
            //先判断验证码
            if($this->checkVerify($data['verify'])) {
                #判断手机号商户是否存在
                $Seller = M('MchSeller')->where(array('mch_tel' => $data['username'], 'domain_auth' => domain_auth()))->find();
                if ($Seller) {
                    #判断密码
                    if (md5($data['password']) == md5('123456') && empty($Seller['password'])) {
                        // $this->error('您使用的密码为初始密码!请重置密码后使用新密码登陆!', U('r_pass'));
                        $_SESSION['mp'] = array(
                            'id' => $Seller['id'],
                            'aid' => $Seller['agent_id'],
                            'phone'=>$Seller['mch_tel'],
                            'mch_name'=>$Seller['mch_name'],
                            'mch_card_name'=>$Seller['mch_card_name']
                        );
                        $this->success('登陆成功!系统即将进入控制台!', U('mp/index/index'));
                    } else {
                        #判断密码是否一致
                        if (md5($data['password']) == $Seller['password']) {
                            $_SESSION['mp'] = array(
                                'id' => $Seller['id'],
                                'aid' => $Seller['agent_id'],
                                'phone'=>$Seller['mch_tel'],
                                'mch_name'=>$Seller['mch_name'],
                                'mch_card_name'=>$Seller['mch_card_name']
                            );
                            $this->success('登陆成功!系统即将进入控制台!', U('mp/index/index'));
                        } else {
                            $this->error('账户或密码错误!');
                        }
                    }
                } else {
                    $this->error('账户或密码错误!');
                }
            }else{
                $this->error('验证码错误');
            }
        }else {
            $this->display();
        }
    }


    /*验证码验证*/
    public function checkVerify($code){
        $verify = new \Think\Verify();
        return $verify->check($code,'mp');
    }

    /*生成验证码*/
    public function loadVerify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 35;
        $Verify->length   = 4;
        $Verify->useNoise = true;
        $Verify->entry('mp');
    }


    /**
     * 退出
     */
    public function out(){
        session('mp',null);
        $this->success('退出成功、前往登录页面','/mp');
    }
}