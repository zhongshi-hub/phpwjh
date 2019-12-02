<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class MchApiController extends Alleys_initBaseController {


    #JS支付相关
    public function gateway(){
        //根据ID获取当前所用的通道
        $alleys=M('MchSellerAlleys')->where(array('mch_id'=>$this->data['mch_id'],'alleys_type'=>$this->data['mch_type']))->find();
        if(!$alleys){
            $this->error('未找到此商户的商户号信息!请确认是否进件!');
        }else{
            #信息存在 查看是否进过件
            $module = A('Pays/P' . $this->data['mch_type']);
            $modules = method_exists($module,'mch_status');
            if ($modules) {
                R('Pays/P' .$this->data['mch_type']. '/mch_status');
            } else {
                $this->error('无此通道进件接口 Error: Pays/Pay_'.$this->data['mch_type'].'/mch_status');
            }
        }
    }


}