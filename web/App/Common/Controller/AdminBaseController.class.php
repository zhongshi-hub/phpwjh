<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * admin 基类控制器
 */
class AdminBaseController extends BaseController{ 
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
		$_auth=new \Think\Auth();
		$rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $rule_name_s=MODULE_NAME.'/'.CONTROLLER_NAME;

        //排除链接
        $notauth=array(
          'Admin/Upload/index', //上传页面
          'Admin/Upload/netdown',//网络下载图片
          'Admin/Login/index', //登录页面
          'Admin/Login/load_verify', //验证码
          'Admin/Login/out',    //退出登录
          'Admin/Mcha/queryBnkCity',//  城市列表
          'Admin/Mcha/bnkLink', //分行列表
        );

        $nauth=array(
            'Admin/Login/index', //登录页面
            'Admin/Login/load_verify', //验证码
            'Admin/Login/out'    //退出登录
        );

       //dump(__SELF__);

        //dump($_SESSION);

        if (!in_array($rule_name, $nauth)) {
            //验证用户状态
            if(empty($_SESSION['user'])){
                $this->error('登录超时,请重新登录',U('Admin/Login/index',array('callurl'=>base64_encode(__SELF__))));
            }
        }

        $is_sys=M('users')->where(array('id'=>$_SESSION['user']['id']))->getField('is_sys');


        if (!in_array($rule_name, $notauth)) {
            if($is_sys!=1){
                $result = $_auth->check($rule_name, $_SESSION['user']['id']);
                if (!$result) {
                    $this->error('您没有权限访问');
                }
            }
        }

		//头部标题 根据数据库配置的菜单名称显示
        $title=M('auth_rule')->where(array('name'=>$rule_name))->getfield('title');
        //菜单项  根据系统角色 显示对应的菜单
        $data_s=M('auth_rule')->order('orders desc')->select();
        $data=\Org\Nx\Data::channelLevel($data_s,0,'&nbsp;','id');
        $this->MenuData=$data;
        $this->MenuAuth=$_auth;
        $this->MenuIsSys=$is_sys;

        // 显示有权限的菜单
        foreach ($data as $k => $v) {
            if ($v['menu'] != 1) {
                unset($data[$k]);
            }
            if($is_sys==1){
                foreach ($v['_data'] as $m => $n) {
                    //然后菜单数据不显示的删除
                    if ($n['menu'] != 1) {
                        unset($data[$k]['_data'][$m]);
                    }
                }
            }else {
                if ($_auth->check($v['name'], $_SESSION['user']['id'])) {
                    //然后菜单数据不显示的删除
                    if ($v['menu'] != 1) {
                        unset($data[$k]);
                    }
                    foreach ($v['_data'] as $m => $n) {
                        //没有权限的删除不显示
                        if (!$_auth->check($n['name'], $_SESSION['user']['id'])) {
                            unset($data[$k]['_data'][$m]);

                        }
                        //然后菜单数据不显示的删除
                        if ($n['menu'] != 1) {
                            unset($data[$k]['_data'][$m]);
                        }
                    }
                } else {
                    // 删除无权限的菜单
                    unset($data[$k]);
                }
            }
        }


        //根据渠道号取渠道ID
        /*$parent_api=M('mch_parent_api')->field('placenum,parentid')->order('id asc')->select();
        foreach($parent_api as $key=>$val){
            $parent_apis[$val['placenum']]=$val['parentid'];
        }
        unset($parent_api);
        //根据渠道ID取渠道信息
        $basic=M('mch_basic')->field('id,parentname')->order('id asc')->select();
        foreach($basic as $key=>$val){
            $basics[$val['id']]=$val['parentname'];
        }
        unset($basic);
        //根据商户号筛选商户名称
        $merchant=M('mch_merchant')->field('merchantid,merchantname')->order('id asc')->select();
        foreach($merchant as $key=>$val){
            $merchants[$val['merchantid']]=$val['merchantname'];
        }*/
        //所有渠道
       // $b_sic=M('mch_basic')->field('id,parentname')->order('id asc')->select();

        //代理费率

        $_agent=M('MchAgent')->field('id,rate')->order('id asc')->select();
        foreach($_agent as $key=>$val){
            $agent[$val['id']]=unserialize($val['rate']);
        }
        unset($_agent);

       // dump($data);


        $assign=array(
            'rule_name_s'=>$rule_name_s,
            'rule_name'=>$rule_name,
            'menu'=>$data,
            'title'=>$title
        );

        $this->assign($assign);







	}




}

