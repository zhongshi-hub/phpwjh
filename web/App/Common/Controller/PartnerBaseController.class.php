<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * admin 基类控制器
 */
class PartnerBaseController extends BaseController{
    /**
     * 初始化方法
     */
    public function _initialize(){
        parent::_initialize();
        $rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $this->assign('rule_name',$rule_name);

        //验证用户状态
        if(empty($_SESSION['partner'])){
                $this->error('登录超时,请重新登录',U('Partner/Login/index',array('callurl'=>base64_encode(__SELF__))));
        }

        //根据渠道号取渠道ID
        $parent_api=M('mch_parent_api')->field('placenum,parentid')->order('id asc')->select();
        foreach($parent_api as $key=>$val){
            $parent_apis[$val['placenum']]=$val['parentid'];
        }
        unset($parent_api);
        $this->assign('parent_apis',$parent_apis);

        //根据渠道ID取渠道信息
        $basic=M('mch_basic')->field('id,parentname')->order('id asc')->select();
        foreach($basic as $key=>$val){
            $basics[$val['id']]=$val['parentname'];
        }
        unset($basic);
        $this->assign('basics',$basics);

        //根据商户号筛选商户名称
        $merchant=M('mch_merchant')->field('merchantid,merchantname')->order('id asc')->select();
        foreach($merchant as $key=>$val){
            $merchants[$val['merchantid']]=$val['merchantname'];
        }
        //unset($merchant);
        $this->assign('mname',$merchants);
        $this->assign('merchant',$merchant);

    }
    
}

