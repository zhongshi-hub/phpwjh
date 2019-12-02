<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 *  刷脸设备管理
 */
class FaceController extends AdminBaseController
{
	public function _initialize()
	{
		parent::_initialize();

	}

	public function deviceReward(){
        $this->display();
	}

	/**
	 *设备列表
	 */
	public function lists(){
		$map=[];
		if(IS_POST){
			$post=I('post.');
			if(!empty($post['search'])){
				$map['device_number|device_sn']=['like','%'.$post['search'].'%'];
			}
			if(!empty($post['aid'])){
				$map['aid']=$post['aid'];
			}
			if(!empty($post['mid'])){
				$map['mid']=$post['mid'];
			}
			if(!empty($post['type'])){
				$map['type']=$post['type'];
			}
		}
		$map['domain_auth'] = domain_auth();
		$Data = M('faceDevice');
		$count = $Data->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $Data->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}


	/**
	 * 设备状态
	 */
	public function deviceStatus(){
		if(IS_POST){
			$data=I('post.');
			$status=($data['status']=="true")?1:0;
			$ret=M('faceDevice')->where(['id'=>$data['id'],'domain_auth'=>domain_auth()])->save(['status'=>$status,'update_time'=>time()]);
			if($ret){
				$this->success('状态更新成功');
			}else{
				$this->error('状态更新失败');
			}
		}
	}


	/**
	 * 设备编辑
	 */
	public function edits(){
		if(IS_POST){
			$data=I('post.');
			$domain_auth=domain_auth();
			//效验设备sn号是否存在
			$db=M('faceDevice');
			$old=$db->where(['id'=>$data['id'],'domain_auth'=>$domain_auth])->find();
			if($data['device_sn']!=$old['device_sn']){
				$count=$db->where(['device_sn'=>$data['device_sn'],'domain_auth'=>$domain_auth])->count();
				if($count){
					$this->error('设备序列号已存在');
				}
			}else{
				//如果门店和商户同时存在
				if($data['mid']&&$data['store_id']) {
					$store = M('mchStore')->where(['sid' => $data['mid'], 'id' => $data['store_id']])->count();
					if ($store < 1) {
						$this->error('所属商户和所属门店不一致');
					}
				}
				//获取代理ID
				if($data['mid']||$data['store_id']){
					if($data['store_id']){
						$data['mid']=M('mchStore')->where(['id' => $data['store_id'],'domain_auth'=>$domain_auth])->getField('sid');
					}
					//根据ID获取代理ID
					$data['aid']=M('mchSeller')->where(['id'=>$data['mid'],'domain_auth'=>$domain_auth])->getField('agent_id');
				}else{
					$data['aid']='';
				}
				$data['update_time']=time();
				$data['domain_auth']=$domain_auth;
				$res=$db->where(['id'=>$data['id'],'domain_auth'=>$domain_auth])->save($data);
				if($res){
					$this->success('设备信息更新成功',U('lists'));
				}else{
					$this->error('设备信息更新失败');
				}
			}
		}else {
			$id = I('get.id');
			$db = M('faceDevice');
			$data = $db->where(['id' => $id, 'domain_auth' => domain_auth()])->find();
			if (empty($data)) {
				$this->error('非法操作');
			}
			$this->assign('data', $data);
			$this->display();
		}
	}


	/**
	 * 设备新增
	 */
	public function adds(){
		if (IS_POST){
			$data=I('post.');
			$domain_auth=domain_auth();
			//效验设备sn号是否存在
			$db=M('faceDevice');
			$count=$db->where(['device_sn'=>$data['device_sn'],'domain_auth'=>$domain_auth])->count();
			if($count){
				$this->error('设备序列号已存在');
			}else{
				//如果门店和商户同时存在
				if($data['mid']&&$data['store_id']) {
					$store = M('mchStore')->where(['sid' => $data['mid'], 'id' => $data['store_id']])->count();
					if ($store < 1) {
						$this->error('所属商户和所属门店不一致');
					}
				}
				//获取代理ID
				if($data['mid']||$data['store_id']){
					if($data['store_id']){
						$data['mid']=M('mchStore')->where(['id' => $data['store_id'],'domain_auth'=>$domain_auth])->getField('sid');
					}
					//根据ID获取代理ID
					$data['aid']=M('mchSeller')->where(['id'=>$data['mid'],'domain_auth'=>$domain_auth])->getField('agent_id');
				}
				$data['create_time']=time();
				$data['update_time']=time();
				$data['domain_auth']=$domain_auth;
				$res=$db->add($data);
				if($res){
					$this->success('设备信息添加成功',U('lists'));
				}else{
					$this->error('设备信息添加失败');
				}
			}
		}else {
			$this->display();
		}
	}
}