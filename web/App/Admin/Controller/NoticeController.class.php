<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 *  公告管理
 */
class NoticeController extends AdminBaseController
{
	protected  $db;
	public function _initialize()
	{
		parent::_initialize();
		$this->db=M('noticeList');
	}

	public function lists(){
		$map=[];
		if(IS_POST){
			$post=I('post.');
			if($post['search']){
				$map['title']=['like','%'.$post['search'].'%'];
			}
			if($post['type']){
				$map['type']=$post['type'];
			}
			if($post['status']){
				$map['status']=$post['status']==1?1:0;
			}
		}
		$map['domain_auth'] = domain_auth();
		$count = $this->db->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $this->db->where($map)->order('sort desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}


	/**
	 * 公告详情
	 */
	public function getNotice(){
		if(IS_POST){
			$map['domain_auth'] = domain_auth();
			$map['id']=I('post.id');
			$data=$this->db->where($map)->find();
			if($data){
				$this->success($data);
			}else{
				$this->error('获取公告信息失败');
			}
		}
	}


	/**
	 * 编辑公告
	 */
	public function edits(){
		if(IS_POST){
			$post=I('post.');
			$map['domain_auth'] = domain_auth();
			$map['id']=I('post.id');
			$data=$this->db->where($map)->save($post);
			if($data){
				$this->success('公告信息更新成功');
			}else{
				$this->error('公告信息更新失败');
			}
		}
	}

	/**
	 * 公告状态
	 */
	public function setStatus(){
		if(IS_POST){
			$data=I('post.');
			$status=($data['status']=="true")?1:0;
			$ret=$this->db->where(['id'=>$data['id'],'domain_auth'=>domain_auth()])->save(['status'=>$status]);
			if($ret){
				$this->success('状态更新成功');
			}else{
				$this->error('状态更新失败');
			}
		}
	}

	/**
	 * 增加公告
	 */
	public function sets(){
		if(IS_POST){
			$post=I('post.');
			unset($post['__TokenHash__']);
			$post['uid']=session('user.id');
			$post['status']=1;
			$post['create_time']=date('Y-m-d H:i:s');
			$post['domain_auth']=domain_auth();
			$ret=$this->db->add($post);
		    if($ret){
		    	$this->success('公告添加成功');
			}else{
		    	$this->error('公告添加失败');
			}
		}
	}
}