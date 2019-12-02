<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;
/**
 * 通道管理管理控制器
 */
class AlleysController extends SystemBaseController{



    #通道维护状态
    public function stop_status()
    {
        $id = I('post.id');
        if (I('post.status') == 'true') {
            $data['stop_status'] = 1;
        } else {
            $data['stop_status'] = 0;
        }
        $res = M('mch_alleys')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success('通道维护状态更新成功');
        } else {
            $this->error('通道维护状态更新失败');
        }


    }


    #通道列表
    public function  lists(){
        $Data = M('mch_alleys');
        $count      = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->where($map)->order('status desc,createtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $assign=array(
            'list' => $list,
            'page'=>  $show,
            'data'=>$_REQUEST
        );
        //dump($list);
        $this->assign($assign);// 赋值分页输出
        $this->display(); // 输出模板
    }

    #添加通道
    public function adds(){
      if(IS_POST){
         $data=I('post.');
         #先判断数据库中是否存在当前标识和名称一样的数据吗
          #如果一样则提示 不一样则直接添加
          $not=M('mch_alleys')->where(array('name'=>$data['name']))->count();
          if($not){
              $this->error('当前通道名称已存在');
          }
          $type=M('mch_alleys')->where(array('type'=>$data['type']))->count();
          if($type){
              $this->error('当前通道标识已存在');
          }
         $data['createtime']=time();
         $res=M('mch_alleys')->add($data);
         if($res){
             $this->success('通道添加成功',U('lists'));
         }else{
             $this->error('通道添加失败');
         }
      }else{
          $this->display();
      }

    }

    #编辑通道
    public function edits(){
        if(IS_POST){
            $data=I('post.');
            $id=$data['id'];
            #先判断数据库中是否存在当前标识和名称一样的数据吗
            #如果一样则提示 不一样则直接添加
            $name=M('mch_alleys')->where(array('id'=>$id))->find();
            if($data['name']!=$name['name']) {
                $not = M('mch_alleys')->where(array('name' => $data['name']))->count();
                if ($not) {
                    $this->error('当前通道名称已存在');
                }
            }
            unset($data['id']);
            $res=M('mch_alleys')->where(array('id'=>$id))->save($data);
            if($res){
                $this->success('通道编辑成功',U('lists'));
            }else{
                $this->error('通道编辑失败');
            }
        }else{
            $id=I('get.id');
            $info=M('mch_alleys')->where(array('id'=>$id))->find();
            if(empty($info)){
                $this->error('无此通道配置信息');
            }
            $this->assign('info',$info);
            $this->display();
        }

    }



}