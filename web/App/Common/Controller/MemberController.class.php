<?php
namespace Common\Controller;
use Think\Controller;
/**
 * 会员卡独立控制器
 */
class MemberController extends Controller
{

	protected $dbTemp;

	public function _initialize()
	{
		$this->dbTemp = M('mchMemberTemp');

	}

	/**
	 * 会员卡基础配置
	 * @param $data
	 * @return array
	 */
	public function temp($data){
		$data['name']=$data['name']?$data['name']:"高级会员卡";
		$data['xc']=$data['xc']?$data['xc']:"欢迎使用本店会员卡";
		$data['bg']=$data['bg']?$data['bg']:0;
		$data['number']=$data['number']?$data['number']:0;
		$data['help']=$data['help']?$data['help']:'欢迎使用会员卡';
		$count=$this->dbTemp->where(['mid'=>$data['mid']])->find();
		if($count){
			if(empty($count['pay_store'])){
				$data['pay_store']=$this->getStore($data['mid']);
			}
			$data['domain_auth']=domain_auth();
			$res=$this->dbTemp->where(['mid'=>$data['mid']])->save($data);
		}else{
			$data['pay_store']=$this->getStore($data['mid']);
			$data['domain_auth']=domain_auth();
			$res=$this->dbTemp->add($data);
		}
		if($res){
			return ['status'=>true,'msg'=>'参数更新成功'];
		}else{
			return ['status'=>false,'msg'=>'参数更新失败'];
		}
	}

	public function getPayUrl($mid){
		if($mid) {
			$url = getDomainUrl() . '/Pay/memberPay?mid=' . $mid;
			$qr = getDomainUrl() . '/Plugs/Qr/code/data/' . Xencode($url);
			return ['status'=>true,'msg'=>'获取成功','url' => $url, 'qr' => $qr];
		}else{
			return ['status' => false, 'msg' => '商户ID不能为空'];
		}
	}

	/**
	 * 配置充值门店
	 * @param $data
	 * @return array
	 */
	public function setPayStore($data){
		$ret=$this->dbTemp->where(['mid'=>$data['mid']])->save(['pay_store'=>$data['id']]);
		return $ret?['status'=>true,'msg'=>'配置成功']:['status'=>false,'msg'=>'配置失败'];
	}

	/**
	 * 获取会员卡基础配置信息
	 * @param $mid
	 * @return mixed
	 */
	public function getTempConf($mid){
		$data=$this->dbTemp->where(['mid'=>$mid])->find();
		return $data;
	}

	public function getStore($mid){
		$ret=M('mchStore')->where(['sid'=>$mid])->getField('id');
		return $ret;
	}
}