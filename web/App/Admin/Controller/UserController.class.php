<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台首页控制器
 */
class UserController extends AdminBaseController{

	/**
	 * 用户列表
	 */
	public function index(){
		$data=D('AuthGroupAccess')->getAllData();
        $assign=array(
            'data'=>$data
            );
        //dump($data);
        $this->assign($assign);
        $this->display();
	}



    /**
     * 添加管理员
     */
    public function add_user(){
        if(IS_POST){
            $data=I('post.');
            //防止重复账户 进行筛选
            $username=M('Users')->where(array('username'=>$data['username'],'domain_auth'=>domain_auth()))->count();
            if($username){
                $this->error('账户已存在!请更改后重新提交!');
            }else {
                $result = D('Users')->addData($data);
                if ($result) {
                    if (!empty($data['group_ids'])) {
                        foreach ($data['group_ids'] as $k => $v) {
                            $group = array(
                                'uid' => $result,
                                'group_id' => $v,
                                'domain_auth'=>domain_auth()
                            );
                            D('AuthGroupAccess')->addData($group);
                        }
                    }
                    // 操作成功
                    $this->success('添加成功', U('Admin/User/index'));
                } else {
                    $error_word = D('Users')->getError();
                    // 操作失败
                    $this->error($error_word);
                }
            }
        }else{
            $data=D('AuthGroup')->where(array('domain_auth'=>domain_auth()))->select();
            $assign=array(
                'data'=>$data
                );
            $this->assign($assign);
            $this->display();
        }
    }


    /**
     * 修改管理员
     */
    public function edit_user(){
        if(IS_POST){
            $data=I('post.');
            // 组合where数组条件
            $uid=$data['id'];
            $map=array(
                'id'=>$uid,
                'domain_auth'=>domain_auth()
                );

            $user=M('Users')->where($map)->find();
            $username=M('Users')->where(array('username'=>$data['username'],'domain_auth'=>domain_auth()))->count();
            if($data['username']!=$user['username']&&$username){
                $this->error('账户已存在!请更改后重新提交!');
            }else {
                // 修改权限
                D('AuthGroupAccess')->deleteData(array('uid' => $uid,'domain_auth'=>domain_auth()));
                foreach ($data['group_ids'] as $k => $v) {
                    $group = array(
                        'uid' => $uid,
                        'group_id' => $v,
                        'domain_auth'=>domain_auth()
                    );
                    D('AuthGroupAccess')->addData($group);
                }
                //$data = array_filter($data);
                // 如果修改密码则md5
                /*if (!empty($data['password'])) {
                    $data['password'] = md5($data['password']);
                }else{
                    unset($data['password']);
                }*/

                if($data['password']){
                    $data['password'] = md5($data['password']);
                }else{
                    unset($data['password']);
                }


               // dump($data);
                $result = D('Users')->editData($map, $data);
                if ($result) {
                    // 操作成功
                    $this->success('编辑成功', U('Admin/User/index', array('id' => $uid)));
                } else {
                    $error_word = D('Users')->getError();
                    if (empty($error_word)) {
                        $this->success('编辑成功', U('Admin/User/index', array('id' => $uid)));
                    } else {
                        // 操作失败
                        $this->error($error_word);
                    }

                }
            }
        }else{
            $id=I('get.id',0,'intval');
            // 获取用户数据
            $user_data=M('Users')->where(array('id'=>$id,'domain_auth'=>domain_auth()))->find();
            if(!$user_data){
                $this->error('账户信息有误!');
            }
            // 获取已加入用户组
            $group_data=M('AuthGroupAccess')
                ->where(array('uid'=>$id,'domain_auth'=>domain_auth()))
                ->getField('group_id',true);
            // 全部用户组
            $data=D('AuthGroup')->where(array('domain_auth'=>domain_auth()))->select();
            $assign=array(
                'data'=>$data,
                'user_data'=>$user_data,
                'group_data'=>$group_data
                );
            $this->assign($assign);
            $this->display();
        }
    }



    /*修改个人资料*/
    /*public function change_msg(){
        if(IS_POST){
            $data['username']  =  trim(I('post.username'));
            $data['email']  =  trim(I('post.email'));
            $data['phone']=trim(I('post.phone'));
            $map=array(
                'username'=>session('user')['username'],
                'domain_auth'=>domain_auth()
                );
            if (!empty(I('post.password'))) {
                $data['password']=md5(I('post.password'));
            }
            $result=D('Users')->where($map)->save($data);

            if($result){
                // 操作成功
                session('user',null);
                $this->success('退出成功、前往登录页面',U('Home/Index/index'));
            }else{
                $this->error("您没有做任何修改");   
            }
        }
    }*/

    /*密码修改*/
    public function editpass(){
        if(IS_POST){
            $old=  trim(I('post.oldpass'));
            $new=  trim(I('post.newpass'));
            $news=  trim(I('post.newspass'));
            //判断新密码和旧密码是否一样
            if(md5($old)==md5($new)){
                $this->error('您输入的新密码和旧密码一样哦!');
            }
            //判断新密码和确认密码是否一致
            if($new!=$news){
                $this->error('两次输入的新密码不一致!');
            }
            //判断旧密码是否一致
            $User=M('Users')->where(array('id'=>session('user')['id'],'domain_auth'=>domain_auth()))->find();
            if(md5($old)!=$User['password']){
                $this->error('旧密码不正确');
            }else{
                if (!empty(I('post.newpass'))) {
                    $data['password']=md5(I('post.newpass'));
                }
                $result=M('Users')->where(array('id'=>session('user')['id'],'domain_auth'=>domain_auth()))->save($data);
                if($result){
                    // 操作成功
                    session('user',null);
                    $this->success('密码修改成功!系统将退出需重新登录!','/Admins');
                }else{
                    $this->error("您没有做任何修改");
                }

            }


        }

    }


}
