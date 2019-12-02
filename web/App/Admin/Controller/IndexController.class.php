<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台首页控制器
 */
class IndexController extends AdminBaseController{




    #首页
    public function index(){
//        $Static=M('DataStatistics');
//        $result=$Static->where(array('type'=>'admin','domain_auth'=>domain_auth()))->find();
		$cache_admin='admin_'.domain_auth();
		$result=S($cache_admin);

        #公告数据
        $goods=array('',domain_auth());
        $map['status']=1;
        $map['domain_auth']=array("in",$goods);
        $Data = M('SysMessage');
        $list = $Data->order('id desc')->where($map)->order('ctime desc')->limit(10)->select();


        $assign=array(
            'Go'=>json_decode($result['terday_data'],true),
            'Day'=>json_decode($result['day_data'],true),
            'To'=>json_decode($result['count_data'],true),
            'Mch'=>json_decode($result['mch_data'],true),
            'Mon'=>json_decode($result['week_data'],true),
            'Time'=>$result['etime'],
            'data'=>$list,
        );
        $this->assign($assign);
        $this->display();

    }

	

}
