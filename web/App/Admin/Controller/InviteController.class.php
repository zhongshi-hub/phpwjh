<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 推荐码
 */
class InviteController extends AdminBaseController
{
	public function _initialize()
	{
		parent::_initialize();

	}


	/**
	 * 结算记录列表
	 */
	public function inviteDrawList(){
		$db=M('inviteDrawList');
		$map['aid']=I('get.aid');
		$map['domain_auth'] = domain_auth();
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $db->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}

	/**
	 * 添加结算记录
	 */
	public function drawSet(){
		if(IS_POST) {
			$post=I('post.');
			if(empty($post['aid'])){
				$this->error('非法操作,请重新登录后再试');
			}
			if(empty($post['total'])){
				$this->error('请输入结算金额');
			}
			if($post['total']>$post['max_total']){
				$this->error('结算金额不能超出最大可结算金额');
			}
			$arr = [
				'aid' =>$post['aid'],
				'total'=>$post['total'],
				'info'=>$post['info'],
				'create_time'=>date('Y-m-d H:i:s'),
				'domain_auth'=>domain_auth()
			];
			$ret=M('inviteDrawList')->add($arr);
			if($ret){
				$this->success('结算信息增加成功');
			}else{
				$this->error('结算信息增加失败');
			}
		}

	}

	public function regDetailList(){
		$code=I('get.code');
		$db=M('mchAgent');
		$id=inviteCodeId($code);
		$map=[];
		if(IS_POST){
			$post=I('post.');
			if($post['grade']>0){
				$map['grade']=$post['grade'];
			}
			if($post['name']){
				$map['user_name']=$post['name'];
			}
			if(!empty($post['type'])){
				$searchId=$post['type']=='zt'?$id['zt']:$id['jt'];
			}else{
				$searchId=$id['merge'];
			}
		}else{
			$searchId=$id['merge'];
		}
		$map['id']=['in',$searchId];
		$map['domain_auth'] = domain_auth();
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$lists = $db->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$list=[];
		foreach ($lists as $k=>$v){
			$type=in_array($v['id'],$id['zt'])?'zt':'jt';
			$tj_grade=inviteCodeAg($v['invite_code'],'grade');
			$rate=inviteRate($type,$tj_grade,$v['grade'])['rate'];
			$reg_total=inviteSetting('reg_a'.$v['grade']);
			$list[$k]=[
			    'id'=>$v['id'],
			    'grade'=>$v['grade'],
			    'grade_name'=>gradeName($v['grade']),
			    'name'=>$v['user_name'],
				'phone'=>$v['user_phone'],
				'type'=>$type,
				'type_name'=>$type=='zt'?'直推':'间推',
				'pay_status'=>$v['pay_status'],
				'reg_total'=>$reg_total,
				'time'=>date('Y-m-d H:i:s',$v['ctime']),
				'invite_code'=>getInviteCode($v['id']),
				'tj_code'=>$v['invite_code'],
				'tj_name'=>inviteCodeAg($v['invite_code'],'user_name'),
				'tj_phone'=>inviteCodeAg($v['invite_code'],'user_phone'),
				'tj_grade'=>$tj_grade,
				'tj_grade_name'=>gradeName($tj_grade),
				'rate'=>$rate?$rate.'%':'',
				'rate_total'=>bcdiv($rate*$reg_total,100,2),
			];
		}
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
    }

	public function regList(){
		$db = M('inviteBefitCount');
		$map = [
			'domain_auth' =>domain_auth()
		];
		if(IS_POST){
			$map['name|phone']=['like','%'.I('post.code').'%'];
		}
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $db->where($map)->order('total desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}

	public function setting(){
		$db=M('inviteSetting');
		$where=['domain_auth'=>domain_auth()];
		if(IS_POST){
			$post=I('post.');
			unset($post['__TokenHash__']);
			$data=json_encode($post,JSON_UNESCAPED_UNICODE);

			$count=$db->where($where)->count();
			if($count){
				$res=$db->where($where)->save([
					'data'=>$data,
					'update_time'=>time()
				]);
			}else{
				$res=$db->add([
					'data'=>$data,
					'update_time'=>time(),
					'domain_auth'=>domain_auth()
				]);
			}
			if($res){
				$this->success('配置信息更新成功');
			}else{
				$this->error('配置信息更新失败');
			}
		}else {
			$data=$db->where($where)->getField('data');
			$data=json_decode($data,true);
			$this->assign('data',$data);
			$this->display();
		}
	}


	public function lists(){
		if(IS_POST){
			$post=I('post.');
			if($post['code']){
				$map['code']=$post['code'];
			}
			if($post['pid']){
				if($post['pid']=='admin'){
					$map['pid']=0;
				}else{
					//获取代理ID
					$id=M('mchAgent')->where(['user_phone'=>$post['pid']])->getField('id');
					$map['pid']=$id;
				}
			}
		}
		inviteInit();//初始化判断
		$map['domain_auth'] = domain_auth();
		$Data = M('inviteCode');
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

	public function inviteCodeSys(){
		if(IS_POST){
			$ret=inviteSet();
			if($ret){
				$this->success('系统推荐码创建成功');
			}else{
				$this->error('系统推荐码创建失败');
			}

		}
	}

	public function inviteCodeStatus(){
		if(IS_POST){
			$data=I('post.');
			$status=($data['status']=="true")?1:0;
			$ret=M('inviteCode')->where(['id'=>$data['id'],'domain_auth'=>domain_auth()])->save(['status'=>$status]);
			if($ret){
				$this->success('状态更新成功');
			}else{
				$this->error('状态更新失败');
			}
		}
	}
}