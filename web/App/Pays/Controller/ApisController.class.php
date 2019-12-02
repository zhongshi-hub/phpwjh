<?php
/**
 * chencunlong@126.com
 * end 2019年04月08日12:05:02
 */
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class ApisController extends Alleys_initBaseController {


    #JS支付相关
    public function gateway(){
    	$action_type=$this->data['is_api']==true?'api':'pay';
    	//流量操作
		$is_flow=mch_is_flow($this->data['sid']);
		if($is_flow) {
			//如果启用查询余额是否可用本次交易
			$is_pay = mch_flow_is_pay($this->data['sid'], $action_type?$this->data['total']/100:$this->data['total']);
			if(false==$is_pay['status']){
				$this->error($is_pay['msg']);
			}
		}
		//系统轮询
		$status=getFlowStatus($this->data['sid']);
		if($status){ //启用轮询接口
			$config=getMchPoll($this->data['sid'],(($this->data['type']=='wx')?'wx':'ali'),$this->data['total']);
			if($config['status']==1){
				//进入轮询操作
				$alleys=$config['config']['alleys'];
				$module = A('Pays/P' . $alleys);
				$modules = method_exists($module, $action_type.'_' . $this->data['type'] . '_jsapi');
				if ($modules) {
					R('Pays/P' . $alleys . '/'.$action_type.'_' . $this->data['type'] . '_jsapi');
				} else {
					$this->error('接口异常('.strtoupper($alleys.'_'.$this->data['type'].'_JS_'.$action_type).')');
				}
			}else{
				$this->error($config['msg']);
			}
		}else {
			//根据ID获取当前所用的通道
			if ($this->data['type'] == 'wx') {
				$alleys = M('MchSeller')->where(array('id' => $this->data['sid']))->getField('wx_alleys');
			} else {
				$alleys = M('MchSeller')->where(array('id' => $this->data['sid']))->getField('ali_alleys');
			}
			if (!$alleys) {
				$this->error('商户未配置支付通道!');
			} else {
				$module = A('Pays/P' . $alleys);
				$modules = method_exists($module, $action_type.'_' . $this->data['type'] . '_jsapi');
				if ($modules) {
					R('Pays/P' . $alleys . '/'.$action_type.'_' . $this->data['type'] . '_jsapi');
				} else {
					$this->error('接口异常('.strtoupper($alleys.'_'.$this->data['type'].'_JS_'.$action_type).')');
				}
			}
		}
    }


    public function gateway_type(){
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys){
            $this->error('未开通当前通道');
        }else{
            #信息存在 查看是否进过件
            if(!$alleys['mch_id']){
                $this->error('未获取到当前商户Mch_Id');
            }
            $module = A('Pays/P' . $this->data['alleys']);
            $modules = method_exists($module,$this->data['way_type']);
            if ($modules) {
                R('Pays/P' .$this->data['alleys']. '/'.$this->data['way_type']);
            } else {
                $this->error('无此通道接口 Error: Pays/Pay_'.$this->data['alleys'].'/'.$this->data['way_type']);
            }
        }
    }
    

    #接口进件相关
    public function mch_in(){
        //根据ID和进件类型选择对应进件接口
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys){
            $this->error('此商户未配置当前通道商户信息');
        }else{
            #信息存在 查看是否进过件
            /*if($alleys['mch_id']){
                $this->error('此商户本通道已经配置商户号信息,无法进行自动进件操作,如需继续进件,请联系相关技术人员!');
            }*/
            
            $module = A('Pays/P' . $this->data['alleys']);
            $modules = method_exists($module,'mch_in');
            if ($modules) {
                R('Pays/P' .$this->data['alleys']. '/mch_in');
            } else {
                $this->error('无此通道进件接口 Error: Pays/Pay_'.$this->data['alleys'].'/mch_in');
            }
        }
    }

    #接口更新
    public function mch_updata(){
        //根据ID和进件类型选择对应进件接口
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys){
            $this->error('此商户未配置当前通道商户信息');
        }else{
            #信息存在 查看是否进过件
            if($alleys['mch_id']&&$alleys['load_status']!=3){
                $this->error('此商户本通道已经配置商户号信息,且状态非驳回状态!无法进行商户更新进件操作,如有疑问,请联系相关技术人员!');
            }

            $module = A('Pays/P' . $this->data['alleys']);
            $modules = method_exists($module,'mch_updata');
            if ($modules) {
                R('Pays/P' .$this->data['alleys']. '/mch_updata');
            } else {
                $this->error('无此通道进件接口 Error: Pays/Pay_'.$this->data['alleys'].'/mch_updata');
            }
        }
    }

    #接口信息变更
    public function mch_alter(){
        //根据ID和进件类型选择对应进件接口
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys){
            $this->error('此商户未配置当前通道商户信息');
        }else{
            #信息存在 查看是否进过件
            if(!$alleys['mch_id']){
                $this->error('未获取到当前商户Mch_Id 无法进行变更!');
            }

            $module = A('Pays/P' . $this->data['alleys']);
            $modules = method_exists($module,$this->data['alter_type']);
            if ($modules) {
                R('Pays/P' .$this->data['alleys']. '/'.$this->data['alter_type']);
            } else {
                $this->error('无此通道变更接口 Error: Pays/Pay_'.$this->data['alleys'].'/'.$this->data['alter_type']);
            }
        }
    }

}