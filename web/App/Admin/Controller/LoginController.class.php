<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * admin 基类控制器
 */
class LoginController extends AdminBaseController{


    Public function index(){
        if(IS_POST){
            // 做一个简单的登录 组合where数组条件
            $data=I('post.');
            if(empty($data['username'])){$this->error('请输入用户名');}
            if(empty($data['password'])){$this->error('请输入密码');}
            if(empty($data['verify'])){$this->error('请输入验证码');}
            //先判断验证码
            if($this->check_verify($data['verify'])) {
                $map['username'] = $data['username'];
                $map['password'] = md5($data['password']);
                $map['domain_auth']=domain_auth();
                $data = M('Users')->where($map)->find();
                if (empty($data)) {
                    $this->error('账号或密码错误');
                } else {
                    $_SESSION['user'] = array(
                        'id' => $data['id'],
                        'name' => $data['name'],
                        'username' => $data['username'],
                        'avatar' => $data['avatar'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
						'is_admin' => $data['is_sys']?1:0,
                        'ip' => get_client_ip(1)
                    );
                    if(isset($_GET['callurl'])){
                        $this->success('登录成功、前往管理后台',__ROOT__.base64_decode($_GET['callurl']));
                    }else {
                        $this->success('登录成功、前往管理后台', U('Admin/Index/index'));
                    }
                }
            }else{
                $this->error('验证码错误');
            }
        }else{
            if(!empty($_SESSION['user'])){
                redirect(U('Index/index'));
            }

            $data=check_login() ? $_SESSION['user']['username'].'已登录' : '未登录';
            $assign=array(
                'data'=>$data
            );
            $this->assign($assign);
            $this->display();
        }
    }


    /*验证码验证*/
    public function check_verify($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    /*生成验证码*/
    public function load_verify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 60;
        $Verify->length   = 4;
        $Verify->useNoise = true;
        $Verify->entry();
    }

    /**
     * 退出
     */
    public function out(){
        session('user',null);
        $this->success('退出成功、前往登录页面','/Admins');
    }




}