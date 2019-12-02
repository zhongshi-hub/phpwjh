<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 商户流量管理控制器
 */
class FlowController extends AdminBaseController
{
	public function _initialize()
	{
		parent::_initialize();

	}


	/**
	 * 系统充值/扣费
	 */
	public function setTotal(){
		if(IS_POST){
			$data=I('post.');
			if($data['total']<0.1){
				$this->error('金额不能小于0.1元');
			}
			$arr=[
			   'mid'=>$data['mid'],
			   'type'=>$data['type']=='pay'?2:1,//类型 1充值 2消费 其它未知
			   'time'=>time(),
			   'oid'=>'SYS'.date('YmdHis').rand(10000,99999),
			   'total'=>$data['total'],
			   'status'=>1,
			   'pay_type'=>$data['type']=='pay'?'系统扣费':'系统充值',
			   'domain_auth'=>domain_auth()
			];
			if($data['type']=='pay'){
				$arr['oid_total']=0;
			}
			$res=M('mchFlowOrder')->add($arr);
			$text=$data['type']=='pay'?'扣费':'充值';
			if($res){
				$this->success('成功'.$text.$data['total'].'元');
			}else{
                $this->error($text.'失败');
			}
		}
	}


	/**
	 * 充值订单
	 */
	public function setList(){
		$data=I('get.');
		$e=M('mchSeller')->where(['id'=>$data['id'],'domain_auth'=>domain_auth()])->find();
		if(!$e){
			$this->error('ID非法操作');
		}
		$map['mid'] = $data['id'];
		$map['type'] = 1;
		$map['domain_auth'] = domain_auth();
		$Data = M('mchFlowOrder');
		$count = $Data->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $Data->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
			'arr'=>$this->getCount(1),
		);
		$this->assign($assign);
		$this->display();
	}

	/**
	 * 扣费订单
	 */
	public function payList(){
		$data=I('get.');
		$e=M('mchSeller')->where(['id'=>$data['id'],'domain_auth'=>domain_auth()])->find();
		if(!$e){
			$this->error('ID非法操作');
		}
		$map['mid'] = $data['id'];
		$map['type'] = 2;
		$map['domain_auth'] = domain_auth();
		$Data = M('mchFlowOrder');
		$count = $Data->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $Data->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
			'arr'=>$this->getCount(2),
		);
		$this->assign($assign);
		$this->display();

	}

	/**
	 * 基础配置
	 */
	public function index(){
		$db=M('mchFlowConfig');
		$map['domain_auth']=domain_auth();
		if(IS_POST) {
			$data=I('post.');
			#判断是否存在
			$count=$db->where($map)->count();
			if($count){
				$res=$db->where($map)->save($data);
			}else{
				$data['domain_auth']=domain_auth();
				$res=$db->add($data);
			}
			if($res){
				$this->success('配置信息更新成功');
			}else{
				$this->error('配置信息失败');
			}
		}else{
			if(!I('get.id')){
				$this->error('非法操作');
			}else{
				$e=M('mchSeller')->where(['id'=>I('get.id'),'domain_auth'=>domain_auth()])->find();
				if(!$e){
					$this->error('ID非法操作');
				}
			}
			$data=$db->where($map)->find();
			$assign=array(
				'name'=>$e['mch_name'],
				'data'=>$data,
			);
			$this->assign($assign);
			$this->display();
		}
	}


	/**
	 * 流量全局配置
	 */
	public function configs(){
		$db=M('flowConfig');
		$map['domain_auth']=domain_auth();
		if(IS_POST) {
			$data=I('post.');
			$data['pay_wx']=$data['wx']?1:0;
			$data['pay_ali']=$data['ali']?1:0;
			#随机选择一个门店ID
			if($data['pay_mid']){
				$rid=M('mchStore')->where(['sid'=>$data['pay_mid']])->getField('id');
				$data['pay_rid']=$rid;
			}
			#判断是否存在
			$count=$db->where($map)->count();
			if($count){
				$res=$db->where($map)->save($data);
			}else{
				$data['domain_auth']=domain_auth();
				$res=$db->add($data);
			}
			if($res){
				$this->success('配置信息更新成功');
			}else{
				$this->error('配置信息失败');
			}
		}else{
			$data=$db->where($map)->find();
			$assign=array(
				'data'=>$data,
			);
			$this->assign($assign);
			$this->display();
		}
	}


	/**
	 * 统计
	 * @param int $type
	 * @return array
	 */
	public function getCount($type=1){
		$map['mid'] = I('get.id');
		$map['status']=1;
		$where['type'] = $type?:2;
		$map['domain_auth'] = domain_auth();
		$Data = M('mchFlowOrder');
		$count = $Data->where($map)->where($where)->count();// 笔数
		$sum=$Data->where($map)->where($where)->sum('total');//总额
		//可用余额
		$types=($type==1)?2:1;
		$eSum=$Data->where($map)->where(['type'=>$types])->sum('total');
		if($type==1){
			$eTotal=round(($sum-$eSum),2);
		}else{
			$eTotal=round(($eSum-$sum),2);
		}
		$dataArr=[
		   'eTotal'=>$eTotal,
		   'count'=>$count,
		   'sum'=>$sum,
		   'type'=>$type
		];
		return $dataArr;
	}


	/**
	 * 轮询规则列表
	 */
	public function payPoll(){
		$data=I('get.');
		$e=M('mchSeller')->where(['id'=>$data['id'],'domain_auth'=>domain_auth()])->find();
		if(!$e){
			$this->error('ID非法操作');
		}
		//获取当前渠道已开通并且系统开启的通道
		$_map['is_card'] = array('neq',1);
		$_map['status'] = 1;
		$ALLEYS=M('MchAlleys')->where($_map)->getField('type',true);
		$where['cid'] = domain_id();
		$where['status'] = 1;
		$api = M('DomainAlleys')->where($where)->select();
		$_data=[];
		foreach ($api as $k => $v) {
			if(in_array($v['alleys_type'],$ALLEYS)) {
				$res['name'] = $v['alleys'];
				$res['type'] = $v['alleys_type'];
				$_data[] = $res;
			}
		}
		$map['mid'] = $data['id'];
		$map['domain_auth'] = domain_auth();
		//是否启用轮询
		$configStatus=M('flowPollConfig')->where($map)->getField('status');
		//获取规则
		$Data = M('flowPoll');
		$count = $Data->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $Data->where($map)->order('level desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign=[
			'data' => $list,
			'page' => $show,
			'mid'=>$data['id'],
			'alley'=>$_data,
			'configStatus'=>$configStatus?1:0
		];

		$this->assign($assign);
		$this->display();
	}


	/**
	 * 添加通道规则
	 */
	public function payPollSet(){
		$db=M('flowPoll');
		if(IS_POST){
			$data=I('post.');
			unset($data['__TokenHash__']);
			$data['domain_auth']=domain_auth();
			$data['times']=time();
			$res=$db->add($data);
			if($res){
				$this->success('规则添加成功');
			}else{
				$this->error('规则添加失败');
			}
		}
	}

	/**
	 * 编辑通道规则
	 */
	public function editPoll(){
		if(IS_POST){
			$db=M('flowPoll');
			$data=I('post.');
			unset($data['__TokenHash__']);
			if($data['type']=='detail'){//获取编辑信息
				$ret=$db->where(['id'=>$data['id']])->find();
				if($ret) {
					unset($ret['domain_auth']);
					$this->success($ret);
				}else{
					$this->error('编辑详细信息获取失败');
				}
			}else{
				$data['times']=time();
				$ret=$db->where(['id'=>$data['id']])->save($data);
				if($ret) {
					$this->success('规则更新成功');
				}else{
					$this->error('规则更新失败');
				}
			}

		}
	}

	/**
	 *更新规则字段
	 */
	public function pollField(){
		if(IS_POST){
			$data=I('post.');
			$where['id']=$data['id'];
			$where['domain_auth']=domain_auth();
			$save=[
				$data['name']=>($data['status']=='true')?1:0
			];
			$save['times']=time();
			$ret=M('flowPoll')->where($where)->save($save);
			if($ret){
				$this->success('规则更新成功');
			}else{
				$this->error('规则更新失败');
			}
		}
	}

	/**
	 * 删除轮询规则
	 */
	public function delPoll(){
		if(IS_POST){
			$ret=M('flowPoll')->where(['id'=>I('post.id'),'domain_auth'=>domain_auth()])->delete();
			if($ret){
				$this->success('规则删除成功');
			}else{
				$this->error('规则删除失败');
			}
		}
	}

	/**
	 *  更新轮询通道配置信息
	 */
	public function pollConfig(){
		if(IS_POST){
            $db=M('flowPollConfig');
            $data=I('post.');
            $map=[
               'mid'=>$data['mid'],
			   'domain_auth'=>domain_auth()
			];
			$count=$db->where($map)->count();
			if($count){
				$res=$db->where($map)->save($data);
			}else{
				$data['domain_auth']=domain_auth();
				$res=$db->add($data);
			}
			if($res){
				$this->success('配置信息更新成功');
			}else{
				$this->error('配置信息失败');
			}
		}
	}




}