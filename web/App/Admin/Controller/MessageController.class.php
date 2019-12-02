<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 站内消息管理控制器
 */
class MessageController extends AdminBaseController{

    #消息列表
    public function index(){
        $goods=array('',domain_auth());
        $map['status']=1;
        $map['domain_auth']=array("in",$goods);
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


    #信息详情
    public function detail(){
        $goods=array('',domain_auth());
        $db=M('SysMessage');
        $map['status']=1;
        $map['domain_auth']=array("in",$goods);
        $map['id']=I('get.id');
        $data=$db->where($map)->find();
        if($data){
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('未找到相关信息');
        }

    }

}