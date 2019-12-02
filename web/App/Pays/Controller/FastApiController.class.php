<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class FastApiController extends Alleys_initBaseController {


    #JS支付相关
    public function gateway(){
        //根据ID获取当前所用的通道
        if($this->data['type']=='wx'){
            $alleys=M('MchSeller')->where(array('id'=>$this->data['sid']))->getField('wx_alleys');
        }else{
            $alleys=M('MchSeller')->where(array('id'=>$this->data['sid']))->getField('ali_alleys');
        }
        //dump($this->data);
        if(!$alleys){
            $this->error('商户未配置支付通道!');
        }else{
            $type=$this->data['pay_api']?$this->data['pay_api']:'scan';
            $module = A('Pays/P' . $alleys);
            $modules = method_exists($module,'pay_'.$this->data['type'].'_'.$type);
            if ($modules) {
                R('Pays/P' .$alleys. '/pay_'.$this->data['type'].'_'.$type);
            } else {
                $this->error('无此通道支付接口 Error: Pays/Pay_'.$alleys.'/pay_'.$this->data['type'].'_'.$type);
            }
        }
    }

    #订单查询
    public function getOrderStatus(){
        $module = A('Pays/P' . $this->data['api']);
        $modules = method_exists($module,'pay_getOrderStatus');
        if ($modules) {
            R('Pays/P' .$this->data['api']. '/pay_getOrderStatus',array('oid'=>$this->data['oid']));
        } else {
            $this->error('无此通道支付接口 Error: Pays/Pay_'.$this->data['api'].'/pay_getOrderStatus');
        }
    }

}