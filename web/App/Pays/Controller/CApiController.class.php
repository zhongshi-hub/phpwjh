<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_CardinitBaseController;
class CApiController extends Alleys_CardinitBaseController {

    #无卡 支付相关接口
    public function gateway(){
        /*if(Qcard_JSpay_time()=='0'){
            $this->error('不在交易时间内');
        }*/
        //根据ID获取当前所用的通道
        $alleys=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['sid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys['mch_id']){
            $this->error('商户未开通此通道接口!');
        }else{
            $module = A('Pays/C' . $this->data['alleys']);
            $modules = method_exists($module,'pay_'.$this->data['type']);
            if ($modules) {
                R('Pays/C' .$this->data['alleys']. '/pay_'.$this->data['type']);
            } else {
                $this->error('通道不存在!'.$this->data['alleys'].'/pay_'.$this->data['type']);
            }
        }
    }

    #无卡 接口进件相关
    public function mch_in(){
        //根据ID和进件类型选择对应进件接口
        $alleys=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys){
            $this->error('此商户未配置当前通道商户信息');
        }else{
            #信息存在 查看是否进过件
            if($alleys['mch_id']){
                $this->error('此商户本通道已经配置商户号信息,无法进行自动进件操作,如需继续进件,请联系相关技术人员!');
            }
            $module = A('Pays/C' . $this->data['alleys']);
            $modules = method_exists($module,'card_mch_in');
            if ($modules) {
                R('Pays/C' .$this->data['alleys']. '/card_mch_in');
            } else {
                $this->error('无此通道进件接口 Error: Pays/C_'.$this->data['alleys'].'/card_mch_in');
            }
        }
    }


    #无卡变更接口
    #接口信息变更
    public function mch_alter(){
        //根据ID和进件类型选择对应进件接口
        $alleys=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys){
            $this->error('此商户未配置当前通道商户信息');
        }else{
            #信息存在 查看是否进过件
            if(!$alleys['mch_id']){
                $this->error('未获取到当前商户Mch_Id 无法进行变更!');
            }

            $module = A('Pays/C' . $this->data['alleys']);
            $modules = method_exists($module,$this->data['alter_type']);
            if ($modules) {
                R('Pays/C' .$this->data['alleys']. '/'.$this->data['alter_type']);
            } else {
                $this->error('无此通道变更接口 Error: Pays/C_'.$this->data['alleys'].'/'.$this->data['alter_type']);
            }
        }
    }




}