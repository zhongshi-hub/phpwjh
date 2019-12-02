<?php
namespace System\Controller;

use Common\Controller\SystemBaseController;

class LoginController extends SystemBaseController
{


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
                $data = M('System_users')->where($map)->find();
                if (empty($data)) {
                    $this->error('账号或密码错误');
                }elseif($data['status']!=1){
                    $this->error('账号已停用');
                } else {
                    $_SESSION['system'] = array(
                        'id' => $data['id'],
                        'name' => $data['name'],
                        'username' => $data['username'],
                        'avatar' => $data['avatar'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'ip' => get_client_ip(1)
                    );
                    if(isset($_GET['callurl'])){
                        $this->success('登录成功、前往总管理后台',__ROOT__.base64_decode($_GET['callurl']));
                    }else {
                        $this->success('登录成功、前往总管理后台', U('System/Index/index'));
                    }
                }
            }else{
                $this->error('验证码错误');
            }
        }else{

            if(!empty($_SESSION['system'])){
                redirect(U('Index/index'));
            }
            if(I('get.t')!='chinaPay'){
				redirect('/Admins');
			}
            $data=system_check_login() ? $_SESSION['system']['username'].'已登录' : '未登录';
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
        return $verify->check($code,3);
    }

    /*生成验证码*/
    public function load_verify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 60;
        $Verify->length   = 4;
        $Verify->useNoise = FA;
        $Verify->entry(3);
    }

    /**
     * 退出
     */
    public function out(){
        session('system',null);
        $this->success('退出成功、前往登录页面',U('Home/Index/index'));
    }


}