<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;
/**
 * 后台首页控制器
 */
class UserController extends SystemBaseController{

	/**
	 * 用户列表
	 */
	public function index(){
		$data=D('SystemAuthGroupAccess')->getAllData();
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
            $username=M('System_users')->where(array('username'=>$data['username']))->count();
            if($username){
                $this->error('账户已存在!请更改后重新提交!');
            }else {
                $result = D('SystemUsers')->addData($data);
                if ($result) {
                    if (!empty($data['group_ids'])) {
                        foreach ($data['group_ids'] as $k => $v) {
                            $group = array(
                                'uid' => $result,
                                'group_id' => $v,
                            );
                            D('SystemAuthGroupAccess')->addData($group);
                        }
                    }
                    // 操作成功
                    $this->success('添加成功', U('System/User/index'));
                } else {
                    $error_word = D('SystemUsers')->getError();
                    // 操作失败
                    $this->error($error_word);
                }
            }
        }else{
            $data=D('SystemAuthGroup')->select();
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
                );

            $user=M('System_users')->where($map)->find();
            $username=M('System_users')->where(array('username'=>$data['username']))->count();
            if($data['username']!=$user['username']&&$username){
                $this->error('账户已存在!请更改后重新提交!');
            }else {
                // 修改权限
                D('SystemAuthGroupAccess')->deleteData(array('uid' => $uid));
                foreach ($data['group_ids'] as $k => $v) {
                    $group = array(
                        'uid' => $uid,
                        'group_id' => $v
                    );
                    D('SystemAuthGroupAccess')->addData($group);
                }
                $data = array_filter($data);
                // 如果修改密码则md5
                if (!empty($data['password'])) {
                    $data['password'] = md5($data['password']);
                }
                $result = D('SystemUsers')->editData($map, $data);
                if ($result) {
                    // 操作成功
                    $this->success('编辑成功', U('System/User/index', array('id' => $uid)));
                } else {
                    $error_word = D('SystemUsers')->getError();
                    if (empty($error_word)) {
                        $this->success('编辑成功', U('System/User/index', array('id' => $uid)));
                    } else {
                        // 操作失败
                        $this->error($error_word);
                    }

                }
            }
        }else{
            $id=I('get.id',0,'intval');
            // 获取用户数据
            $user_data=M('System_users')->find($id);
            // 获取已加入用户组
            $group_data=M('SystemAuthGroupAccess')
                ->where(array('uid'=>$id))
                ->getField('group_id',true);
            // 全部用户组
            $data=D('SystemAuthGroup')->select();
            $assign=array(
                'data'=>$data,
                'user_data'=>$user_data,
                'group_data'=>$group_data
                );
            $this->assign($assign);
            $this->display();
        }
    }


    /*个人中心*/    /*分开写是为了将权限更细化*/
    public function my_center(){
        $this->display();
    }

    /*修改个人资料*/
    public function change_msg(){
        if(IS_POST){
            $data['username']  =  trim(I('post.username'));
            $data['email']  =  trim(I('post.email'));
            $data['phone']=trim(I('post.phone'));
            $map=array(
                'username'=>session('system')['username'],
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
    }

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
            $User=M('System_users')->where(array('id'=>session('system')['id']))->find();
            if(md5($old)!=$User['password']){
                $this->error('旧密码不正确');
            }else{
                if (!empty(I('post.newpass'))) {
                    $data['password']=md5(I('post.newpass'));
                }
                $result=M('System_users')->where(array('id'=>session('system')['id']))->save($data);
                if($result){
                    // 操作成功
                    session('system',null);
                    $this->success('密码修改成功!系统将退出需重新登录!','/System');
                }else{
                    $this->error("您没有做任何修改");
                }

            }


        }

    }


}
