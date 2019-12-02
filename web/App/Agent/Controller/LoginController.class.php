<?php
namespace Agent\Controller;
use Common\Controller\BaseController;
class LoginController extends BaseController {

    public function _initialize()
    {
        parent::_initialize();
    }



    public function index(){
        if(IS_POST){
            $data=I('post.');
            if(empty($data['username'])){$this->error('请输入手机号');}
            if(empty($data['password'])){$this->error('请输入密码');}
            if(empty($data['verify'])){$this->error('请输入验证码');}
            //先判断验证码
            if($this->check_verify($data['verify'])) {
                #判断此代理是否配置手机号
                $map['user_phone'] = $data['username'];
                //$map['password'] = md5($data['password']);
                $map['domain_auth']=domain_auth();
                $res = M('MchAgent')->where($map)->find();
                //dump($data);
                if($res) {
                    if (empty($res['password'])) {
                        #未配置密码
                        if(md5($data['password']) == md5('asd123456')){ #初始密码
                            //$this->error('当前密码为初始密码!请重新配置密码!');
                            #这里 总核心判断
                            $_SESSION['agent'] = array(
                                'pass' => 'no',
                                'id' => $res['id'],
                                'pid' => $res['pid'],
                                'user_name' => $res['user_name'],
                                'user_phone' => $res['user_phone'],
                                'ip' => get_client_ip(1)
                            );
                            if(isset($_GET['callurl'])){
                                $this->success('登录成功、前往业务后台',__ROOT__.base64_decode($_GET['callurl']));
                            }else {
                                $this->success('登录成功、前往业务后台', U('Agent/Index/index'));
                            }

                        }else{
                            $this->error('手机号或密码错误!');
                        }
                    } else {
                        #已配置密码
                        $where['password'] = md5($data['password']);
                        $password = M('MchAgent')->where($map)->where($where)->find();
                        if ($password) {
                            #都正确
                            $_SESSION['agent'] = array(
                                'id' => $password['id'],
                                'pid' => $password['pid'],
                                'user_name' => $password['user_name'],
                                'user_phone' => $password['user_phone'],
                                'ip' => get_client_ip(1)
                            );
                            if(isset($_GET['callurl'])){
                                $this->success('登录成功、前往业务后台',__ROOT__.base64_decode($_GET['callurl']));
                            }else {
                                $this->success('登录成功、前往业务后台', U('Agent/Index/index'));
                            }


                        } else {
                            $this->error('手机号或密码错误!');
                        }
                    }
                }else{
                    $this->error('账户或密码错误!');
                }
            }else{
                $this->error('验证码错误');
            }


        }else {
            if(!empty($_SESSION['agent'])){
                redirect(U('Index/index'));
            }
            $this->display();
        }
    }



    /*验证码验证*/
    public function check_verify($code){
        $verify = new \Think\Verify();
        return $verify->check($code,'agent');
    }

    /*生成验证码*/
    public function load_verify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 35;
        $Verify->length   = 3;
        $Verify->useNoise = true;
        $Verify->entry('agent');
    }

    /**
     * 退出
     */
    public function out(){
        session('agent',null);
        $this->success('退出成功、前往登录页面',U('Agent/Login/index'));
    }

}