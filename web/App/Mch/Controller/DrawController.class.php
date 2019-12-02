<?php
namespace Mch\Controller;

use Mch\Controller\InitBaseController;

class DrawController extends InitBaseController
{

	/**
	 * 提现门店信息
	 */
	public function index(){
		$id=session('mch.id');
        $res=R('Pays/PStarpos/getMchDraw',[$id]);
        if($res['status']==0){
        	$this->error($res['msg']);
		}
        $this->assign($res);
		$this->display();
	}

	/**
	 * 启用、禁用提现权限
	 */
	public function drawStatus(){
		if(IS_POST){
			$p=I('post.');
			if($p['type']=='open'){
				$res=R('Pays/PStarpos/openDraw',[$p['mch_id'],$p['stoe_id']]);
			}else{
				$res=R('Pays/PStarpos/closeDraw',[$p['mch_id'],$p['stoe_id']]);
			}
			if($res['status']==1){
				$this->success($res['msg']);
			}else{
				$this->error($res['msg']);
			}
		}
	}

	/**
	 * 发起提现
	 */
	public function draw(){
		if(IS_POST){
			$p=I('post.');
			$res=R('Pays/PStarpos/sendDraw',[$p['mch_id'],$p['stoe_id'],$p['total']*100,$p['fee']*100]);
			if($res['status']==1){
				$this->success($res['msg']);
			}else{
				$this->error($res['msg']);
			}
		}
	}

	/**
	 * 提现记录流水
	 */
	public function drawOrder(){
		$p=I('get.');
		if($p['mch_id']){
			$res=R('Pays/PStarpos/drawOrder',[$p['mch_id']]);
			if($res['status']==1){
				$this->assign('data',$res['res']);
				$this->display();
			}else{
				$this->error($res['msg']);
			}
		}else{
			$this->error('非法操作');
		}
	}


	public function drawSetting(){
		$db=M('mchXdlDrawSetting');
		if(IS_POST) {
			$data = I('post.');
			$where=[
			  'mid'=>session('mch.id'),
			  'mch_id'=>$data['mch_id']
			];
			$count=$db->where($where)->count();
			if($count){
				$res=$db->where($where)->save(['total'=>$data['total']]);
			}else{
				$data['domain_auth']=domain_auth();
				$data['mid']=session('mch.id');
				$res=$db->add($data);
			}
			if($res){
				$this->success('自动提现配置成功');
			}else{
				$this->error('自动提现配置失败');
			}
		}else{
			$this->error('非法操作');
		}
	}
}