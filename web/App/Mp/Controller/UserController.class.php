<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Base基类控制器
 */
class UserController extends BaseController{


    /**
     * 基本资料
     */
    public function index(){
        $where['domain_auth'] = domain_auth();
        $where['id'] = session('mp.id');
        $data = M('MchSeller')->where($where)->find();
        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 修改密码
     */
    public function pass(){
        if(IS_POST){
            $data=I('post.');
            if($data['new_pass']!=$data['new_pass1']){
                $this->error('新密码和确认新密码不一致');
            }
            //先获取信息
            $where['domain_auth'] = domain_auth();
            $where['id'] = session('mp.id');
            $mch = M('MchSeller')->where($where)->find();
            //判断当前用户是否设置密码
            if($mch['password']){ //已设置密码
                if(md5($data['pass'])!=$mch['password']){
                    $this->error('旧密码不正确');
                }
            }else{//初始 未设置密码
                if(md5($data['pass'])!=md5('123456')){
                    $this->error('旧密码不正确');
                }
            }
            if(strlen($data['new_pass'])<6) {
                $this->error('新密码必须包含至少含有6个字符，请修改!');
            }
            //如果旧密码正确则重新设置为新的密码
            $res=M('MchSeller')->where($where)->save(['password'=>md5($data['new_pass'])]);
            if($res){
                session('mp',null);
                $this->success('密码修改成功');
            }else{
                $this->error('新密码与旧密码一致,修改失败!');
            }
        }else {
            $this->display();
        }
    }

    /**
     * 通道费率明细
     */
    public function rate(){
        $ALLEYS=M('MchAlleys')->where(['status'=>1])->getField('type',true);
        $api = M('DomainAlleys')->where(['cid'=>domain_id(),'status'=>1])->select();
        $_data=[];
        $class=['custom','pink','danger','info','success','purple','dark','primary'];
        foreach ($api as $k => $v) {
            if(in_array($v['alleys_type'],$ALLEYS)) {
                //移动支付
                $data = M('MchSellerAlleys')->where(['mch_id'=>array('EXP','IS NOT NULL'),'domain_auth'=>domain_auth(),'cid'=>$_SESSION['mp']['id'],'alleys_type'=>$v['alleys_type']])->field('alleys_type,rate')->find();
                if($data) {
                    $res['alleys'] = alleys_name($data['alleys_type']);
                    $res['rate'] = $data['rate'];
                    $res['class']=$class[$k]?$class[$k]:end($class);
                    $_data[] = $res;
                }
            }
        }
        $this->assign('data',$_data?$_data:false);
        $this->display();
    }

    /**
     * 流量预付费管理
     */
    public function flow(){
    	//计算余额
		$balance=flow_balance($_SESSION['mp']['id']);
		$db=M('mchFlowConfig');
		//充值金额
		$map=[
		   'mid'=>$_SESSION['mp']['id'],
		   'domain_auth'=>domain_auth()
		];
		$res=$db->where($map)->find();
		$config=M('flowConfig')->where(['domain_auth'=>domain_auth()])->find();
		$assign=[
			'config'=>$config,
			'res'=>$res,
			'balance'=>$balance
		];
		$this->assign($assign);
        $this->display();
    }


    public function setPhone(){
    	if(IS_POST){
			$sms_phone=I('post.sms_phone');
			if(!$sms_phone){
				$this->error('手机号不需为空');
			}
			$db=M('mchFlowConfig');
			//充值金额
			$map=[
				'mid'=>$_SESSION['mp']['id'],
				'domain_auth'=>domain_auth()
			];
			$res=$db->where($map)->save(['sms_phone'=>$sms_phone]);
			if($res){
				$this->success('报警手机号更新成功');
			}else{
				$this->error('报警手机号更新失败');
			}
		}
	}
}