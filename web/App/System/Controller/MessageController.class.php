<?php
namespace System\Controller;

use Common\Controller\SystemBaseController;

/**
 * 公告消息管理控制器
 */
class MessageController extends SystemBaseController
{

    #消息列表
    public function lists(){
        $Data = M('SysMessage');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id desc')->where($map)->order('ctime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign=array(
            'data'=>$list,
            'page'=>$show
        );
        $this->assign($assign);
        $this->display();
    }


    #新增消息
    public function adds(){
        $db=M('SysMessage');
        if(IS_POST){
            $data=I('post.');
            $data['data']=stripslashes(htmlspecialchars_decode($_POST['data']));
            if($data['id']){
                $res=$db->where(array('id'=>$data['id']))->save($data);
            }else{
                $data['ctime']=date('Y-m-d H:i:s');
                $res=$db->add($data);
            }
            if($res){
                $this->success('消息处理成功',U('lists'));
            }else{
                $this->error('消息处理失败');
            }
        }else {
            $data=$db->where(array('id'=>I('get.id')))->find();
            if ($data){
                $title='编辑消息';
            }else{
                $title='新增消息';
            }
            $this->assign('title',$title);
            $this->assign('data',$data);
            $this->display();
        }
    }


    #信息详情
    public function detail(){
        $db=M('SysMessage');
        $data=$db->where(array('id'=>I('get.id')))->find();
        if($data){
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('未找到相关信息');
        }

    }


    #删除消息
    public function deletes(){
        $db=M('SysMessage');
        $data=$db->where(array('id'=>I('get.id')))->delete();
        if($data){
            $this->success('删除消息成功',U('lists'));
        }else{
            $this->error('删除消息失败');
        }
    }

}