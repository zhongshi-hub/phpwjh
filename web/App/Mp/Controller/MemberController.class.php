<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * 会员类控制器
 */
class MemberController extends BaseController
{

	protected $mid;
	public function _initialize(){
		parent::_initialize();
		$this->mid=session('mp.id');
		//所有门店
		$map=[
			'sid'=>$this->mid,
			'domain_auth'=>domain_auth(),
		];
		$store = M('mchStore')->where($map)->field('id,sid,name,status')->select();
		$this->assign('store',$store);
	}

	/**
	 * 活动状态处理
	 */
	public function restStatus(){
		$data=I('post.');
		$data['mid']=session('mp.id');
		$res=R('Common/MemberActivity/setStatus',[$data]);
		if($res){
			$this->success('活动状态更新成功');
		}else{
			$this->error('活动状态更新失败');
		}
	}

	/**
	 * 活动列表
	 */
	public function lists(){
		$set=R('Common/MemberActivity/getList',[['mid'=>session('mp.id')]]);
		$this->assign($set);
		$this->display();
	}

	/**
	 * 活动详情
	 */
	public function detail(){
		$id=I('get.id');
		$set=R('Common/MemberActivity/getDetail',[['mid'=>session('mp.id'),'id'=>$id]]);
		$this->assign($set);
		//dump($set);
		$this->display();
	}


	/**
	 * 我的会员
	 */
	public function index(){
		$db=M('mchMemberUser');
		$map=[
		   'mid'=>session('mp.id')
		];
		if(IS_POST){
			$post=I('post.');
			if($post['search_data']){
				$map['name|phone|num']=['like','%'.$post['search_data'].'%'];
			}
			if($post['search_time']&&empty($post['search_data'])){
				$time=explode(' - ',$post['search_time']);
				$time[1]=$time[1].'23:59:59';
				$map['_string'] = "(`create_time`> '" . strtotime($time[0]) . "') AND ( `create_time` < '" . strtotime($time[1]) . "') ";
			}
		}
		$count      = $db->where($map)->count();// 查询满足要求的总记录数
		$page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $page->show();// 分页显示输出
		$list = $db->order('id desc')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
		//统计数据
		$st=$this->getMemberCount();
		$assign=[
			'list' => $list,
			'page' => $show,
			'st'=>$st,
		];

		$this->assign($assign);
		$this->display();
	}

	public function getMemberCount(){
		$db=M('mchMemberUser');
		$map=[
			'mid'=>session('mp.id')
		];
		//本日新增会员
		$where['create_time']=['egt',strtotime(date('Ymd'))];
		$count_day=$db->where($where)->where($map)->count();
		//会员总数
		$count=$db->where($map)->count();
		//充值会员数
		$count_pay=M('mchMemberOrder')->where($map)->where(['type'=>'cz','status'=>1])->distinct(true)->Field('user_id')->select();
		//绑定实体会员卡的会员
		$count_st=$db->where(['st_num'=>['exp','IS NOT NULL']])->where($map)->count();
		$arr=[
			'count_day'=>$count_day,
			'count'=>$count,
			'count_pay'=>count($count_pay),
			'count_st'=>$count_st
		];
		return $arr;

	}

	/**
	 * 会员营销
	 */
	public function activity(){
		$this->display();
	}

	/**
	 * 充值记录
	 */
	public function userCz(){
		$db=M('mchMemberOrder');
		$map=[
			'mid'=>session('mp.id'),
			'total_type'=>1,
		];
		if(IS_POST){
			$post=I('post.');
			if($post['data']){
				$userId=M('mchMemberUser')->where(['num|name|phone'])->getField('id',true);
				$map['user_id']=['in',$userId];
			}
			if($post['time']){
				$time=explode(' - ',$post['time']);
				$time[1]=$time[1].'23:59:59';
				$map['_string'] = "(`create_time`> '" . strtotime($time[0]) . "') AND ( `create_time` < '" . strtotime($time[1]) . "') ";
			}
			if($post['status']>0){
				$map['status']=($post['status']==1)?1:0;
			}
			if($post['type']&&$post['type']!='-1'){
				$map['type']=$post['type'];
			}
			if($post['store_id']){
				$map['store_id']=['in',$post['store_id']];
			}
		}

		$count      = $db->where($map)->count();// 查询满足要求的总记录数
		$page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $page->show();// 分页显示输出
		$list = $db->order('id desc')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
		$count=$this->getCzCount();
		$assign=[
			'list' => $list,
			'page' => $show,
			'count' =>$count
		];
		$this->assign($assign);
		$this->display();
	}

	public function getCzCount(){
		$db=M('mchMemberOrder');
		//获取充值总额
		$map=[
			'mid'=>session('mp.id'),
			'total_type'=>1,
			'status'=>1
		];
		$data=[];
		$data['sum']=$db->where($map)->sum('total');
		//线上充值
		$data['cz']=$db->where($map)->where(['type'=>'cz'])->sum('total');
		//现金充值
		$data['xj']=$db->where($map)->where(['type'=>'xj'])->sum('total');
		//赠送/返现
		$data['fx']=$db->where($map)->where(['type'=>['like','%_s']])->sum('total');
		//手续费
		$data['sxf']=$db->where($map)->sum('sxf');
		//实收
		$data['money']=$db->where($map)->sum('money');
		return $data;
	}

	/**
	 * 消费记录
	 */
	public function userXf(){
		$db=M('mchMemberOrder');
		$map=[
			'mid'=>session('mp.id'),
			'total_type'=>2,
		];
		if(IS_POST){
			$post=I('post.');
			if($post['data']){
				$userId=M('mchMemberUser')->where(['num|name|phone'])->getField('id',true);
				$map['user_id']=['in',$userId];
			}
			if($post['time']){
				$time=explode(' - ',$post['time']);
				$time[1]=$time[1].'23:59:59';
				$map['_string'] = "(`create_time`> '" . strtotime($time[0]) . "') AND ( `create_time` < '" . strtotime($time[1]) . "') ";
			}
			
			if($post['store_id']){
				$map['store_id']=['in',$post['store_id']];
			}
		}

		$count      = $db->where($map)->count();// 查询满足要求的总记录数
		$page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $page->show();// 分页显示输出
		$list = $db->order('id desc')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
		$count=$this->getXfCount();
		$assign=[
			'list' => $list,
			'page' => $show,
			'count' =>$count
		];
		$this->assign($assign);
		$this->display();
	}


	public function getXfCount(){
		$db=M('mchMemberOrder');
		//获取充值总额
		$map=[
			'mid'=>session('mp.id'),
			'total_type'=>2,
			'status'=>1
		];
		$data=[];
		$data['count']=$db->where($map)->count();
		$data['sum']=$db->where($map)->sum('total');
		$data['card']=$db->where(['mid'=>session('mp.id'),'status'=>1])->sum('total');
		$data['sum']=abs($data['sum']);
		return $data;
	}

	/**
	 * 会员模板
	 */
	public function userTemp(){
		if(IS_POST){
			$data=I('post.');
			unset($data['__TokenHash__']);
			$data['mid']=session('mp.id');
			$set=R('Common/Member/temp',[$data]);
			if(true==$set['status']){
				$this->success($set['msg']);
			}else{
				$this->error($set['msg']);
			}
		}else {
			$data=R('Common/Member/getTempConf',[session('mp.id')]);
			$this->assign('data',$data);
			$this->display();
		}
	}

	public function setPayStore(){
		$id=I('post.id');
		if(empty($id)){
			$this->error('非法操作');
		}
		$data=[
		   'mid'=>session('mp.id'),
		   'id'=>$id
		];
		$set=R('Common/Member/setPayStore',[$data]);
		if(true==$set['status']){
			$this->success($set['msg']);
		}else{
			$this->error($set['msg']);
		}
	}

	/**
	 * 会员充值配置
	 */
	public function userConf(){
		//获取配置信息
		$temp=R('Common/Member/getTempConf',[session('mp.id')]);
		if(empty($temp)){
			$this->error('请先配置会员卡',U('userTemp'));
		}
		//所有门店
		$store=M('mchStore')->where(['sid'=>session('mp.id')])->field('id,name')->select();
		$url=R('Common/Member/getPayUrl',[session('mp.id')]);
		$assign=[
		  'store'=>$store,
		  'temp'=>$temp,
		  'url'=>$url['url'],
		  'qr'=>$url['qr']
		];
		$this->assign($assign);
		$this->display();
	}

	/**
	 * 充值活动
	 */
	public function activity_cz(){
		if(IS_POST){
			$data=I('post.');
			unset($data['__TokenHash__']);
			$data['mid']=session('mp.id');
			$data['type']='cz';
			//检查
			$set=R('Common/MemberActivity/czCheck',[$data]);
			if(true==$set['status']){
				$this->success($set['msg']);
			}else{
			    $this->error($set['msg']);
			}
		}else {
			$this->display();
		}
	}

	/**
	 * 消费活动
	 */
	public function activity_xf(){
		if(IS_POST){
			$data=I('post.');
			unset($data['__TokenHash__']);
			$data['mid']=session('mp.id');
			$data['type']='xf';
			//检查
			$set=R('Common/MemberActivity/xfCheck',[$data]);
			if(true==$set['status']){
				$this->success($set['msg']);
			}else{
				$this->error($set['msg']);
			}
		}else {
			$this->display();
		}
	}

	/**
	 * 激活活动
	 */
	public function activity_jh(){
		if(IS_POST){
			$data=I('post.');
			unset($data['__TokenHash__']);
			$data['mid']=session('mp.id');
			$data['type']='jh';
			//检查
			$set=R('Common/MemberActivity/jhCheck',[$data]);
			if(true==$set['status']){
				$this->success($set['msg']);
			}else{
				$this->error($set['msg']);
			}
		}else {
			$this->display();
		}
	}

	/**
	 * 推荐活动
	 */
	public function activity_tj(){
		if(IS_POST){
			$data=I('post.');
			unset($data['__TokenHash__']);
			$data['mid']=session('mp.id');
			$data['type']='tj';
			//检查
			$set=R('Common/MemberActivity/tjCheck',[$data]);
			if(true==$set['status']){
				$this->success($set['msg']);
			}else{
				$this->error($set['msg']);
			}
		}else {
			$this->display();
		}
	}
}