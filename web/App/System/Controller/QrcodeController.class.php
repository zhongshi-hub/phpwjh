<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;
/*
 * 云码模块
 * */

class QrcodeController extends SystemBaseController
{


    #云码列表
    public function lists(){
        $QrList=M('Domain_auth')->field('web_name,web_authcode')->where(array('status'=>1))->select();

        $Data= M('MchCodes');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign=array(
            'QrList'=>$QrList,
            'list' => $list,
            'page' => $show
        );
        $this->assign($assign);
        $this->display();

    }


    #云码生成
    public  function  adds(){
        if(IS_POST) {
            $p=I('post.');
            //判断
            if(!$p['count']){$this->error('创建数量最低1个');}
			if($p['count']>100){
				$this->error('单次创建数量最大100个');
			}
			$res=R('Tasks/QrCode/set',[$p['aid'],$p['count'],'P',$p['auth_code']]);
			$this->success("处理成功,本次收款码成功生成{$res}个");
        }
    }

    #根据品牌获取代理列表
    public function agent_data(){
        $map['domain_auth']=I('post.data');
        $data=M('MchAgent')->where($map)->field('id,user_name')->select();
        echo json_encode($data);
        exit();
    }
    
}
?>
