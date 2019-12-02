<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 支付宝Isv管理控制器
 */
class IsvController extends AdminBaseController
{
	public function _initialize()
	{
		parent::_initialize();

	}



	public function info(){
		$apply=M('isvApplyin')->where(['mid'=>I('get.m'),'apply_id'=>I('get.apply_id'),'domain_auth'=>domain_auth()])->find();
		dump($apply);
		$this->display();
	}

	/**
	 * 门店申请状态查询
	 */
	public function applyState(){
		$apply=M('isvApplyin')->where(['id'=>I('post.id'),'domain_auth'=>domain_auth()])->find();
		if($apply){
			try{
				$res=R('Pays/XaliIsv/queryShopState',[$apply['apply_id'],$apply['mid']]);
			}catch (\Exception $e){
				$this->error($e->getMessage());
			}
			if(empty($res)){
				$this->error('查询失败');
			}else{
				switch ($res['status']){
					case 'SUCCESS':
						$msg=$res['sub_status']=='FINISH'?'门店审核通过':$res['status'].$res['sub_status'];
						break;
					case 'PROCESS':
						$msg='处理中';
						break;
					case 'INIT':
						$msg='初始状态';
						break;
					case 'FAIL':
						$msg='失败';
						break;
					default:
						$msg=$res['status'];
						break;
				}
				switch ($res['sub_status']){
					case 'WAIT_CERTIFY':
						$state='等待认证';
						break;
					case 'LICENSE_AUDITING':
						$state='证照审核中';
						break;
					case 'RISK_AUDITING':
						$state='风控审核中';
						break;
					case 'WAIT_SIGN':
						$state='等待签约';
						break;
					case 'FINISH':
						$state='终结';
						break;
					default:
						$state=$res['sub_status'];
						break;
				}
				$rs='<p style="font-size: 18px;text-align: center;color: #0a6aa1">'.$apply['main_shop_name'].'</p><p style="color: red;text-align: center;">申请状态:'.$msg.'('.$state.')<p>';
				if(!empty($res['result_desc'])){
					$rs.='<p>'.$res['result_desc'].'</p>';
				}
				$this->success($rs);
			}
		}else{
			$this->error('获取信息失败');
		}
		$this->error('sss');
	}


	/**
	 * 门店申请
	 */
	public function shopCreate(){
		if (IS_POST){
			$post=I('post.');
			try{
				$res=R('Pays/XaliIsv/shopCreate',[$post]);
			}catch (\Exception $e){
				$this->error($e->getMessage());
			}
			$this->success($res);
		}else {
			$get = I('get.');
			if (!isset($get['id']) || empty($get['id'])) {
				$this->error('请先选择商户');
			}
			$data = M('mchSeller')->where(['id' => $get['id'], 'domain_auth' => domain_auth()])->find();
			if (empty($data)) {
				$this->error('获取商户信息失败');
			}
			//商户是否授权
			$isToken = aliIsvToken($get['id']);
			if (empty($isToken)) {
				$this->error('当前商户未授权,请先让客户授权签约后再试!');
			}
//			//获取支付宝类目
//			try {
//				$cate = R('Pays/XaliIsv/queryShopCategory', [$get['id']]);
//			} catch (\Exception $e) {
//				$this->error($e->getMessage());
//			}

			$assign = [
				'data' => $data,
				//'cate' => $cate,
				'pro' => $this->getProCity(),
			];
			$this->assign($assign);
			$this->display();
		}
	}

	/**
	 * 获取省市区数据
	 */
	public function getProCityData(){
		if(IS_POST){
			$res=$this->getProCity(I('post.name'));
			if($res) {
				$this->success($res);
			}else{
				$this->error('获取信息失败');
			}
		}
	}

	/**
	 * 私有方法 默认显示省
	 * @param int $pid
	 * @return mixed
	 */
	protected function  getProCity($pid=1){
		#省份
		$pro = M('ccbCityData')->where(['pid'=>$pid])->distinct(true)->field('name,mid')->select();
		return $pro;
	}


	/**
	 * 口碑入网列表
	 */
	public function shopList(){
		$db = M('isvApplyin');
		if(!empty(I('post.mid'))){
			$map['mid']=I('post.mid');
		}
		if(!empty(I('post.status'))) {
			$map['status'] = I('post.status');
		}
		if(!empty(I('post.audit_status'))) {
			$map['audit_status'] = I('post.audit_status');
		}
		$map['domain_auth'] = domain_auth();
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}

	/**
	 * 更新支付宝ISV通道费率
	 */
	public function setRate(){
		if(IS_POST){
			$data=I('post.');
			//全局是否配置服务商APPID
			$isAppId=GetPayConfigs('ali_isv_appid');
			if(empty($isAppId)){$this->error('支付宝ISV服务商配置信息获取失败');}
			//代理是否配置费率
			$_where['domain_auth'] = domain_auth();
			$_where['id'] = $data['id'];
			$aid = M('mch_seller')->where($_where)->getField('agent_id');
			$_rel = M('MchAgent')->where(array('id' => $aid))->find();
			$rate = unserialize($_rel['rate']);
			#判断如果没配置代理终端费率 提示配置
			if (!$rate['Aliisv_term']) {
				$this->error('<p>所属代理姓名:  <span style="color: #2279da;font-size: 1.3em">' . $_rel['user_name'] . '</span></p><p>未配置支付宝ISV服务商通道的默认端费率请先配置代理的终端费率后再操作!<p></p>', '', 9999);
			}
			//是否通道数据里有此通道数据 用来费率计算等
			$count=M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' =>'Aliisv', 'domain_auth' => domain_auth()))->count();
			if($count<1){
				$seller = M('MchSeller')->where(array('id' => $data['id'], 'domain_auth' => domain_auth()))->find();
				unset($seller['id']);
				unset($seller['mch_wx_openid']);
				unset($seller['mch_wx_name']);
				unset($seller['mch_wx_img']);
				unset($seller['codes']);
				unset($seller['alleys']);
				unset($seller['alleys_type']);
				unset($seller['status']);
				unset($seller['mch_id']);
				unset($seller['api_rel']);
				unset($seller['rate']);
				unset($seller['load_status']);
				$allys = array(
					'mch_id'=>'0000',
					'cid' => $data['id'],
					'alleys_type' => 'Aliisv',
					'rate' => $data['rate'],
					'status'=>1,
					'load_status'=>1,
				);
				$add = array_merge($allys, $seller);
				M('MchSellerAlleys')->add($add);
				$this->success('支付宝ISV通道费率更新成功');
			}else {
				$db = M('MchSellerAlleys');
				$where = [
					'cid' => $data['id'],
					'alleys_type' => 'Aliisv',
					'domain_auth' => domain_auth()
				];
				$save=[
					'mch_id'=>'0000',
					'rate' => $data['rate'],
					'status'=>1,
					'load_status'=>1,
				];
				$res = $db->where($where)->save($save);
				if($res){
					$this->success('支付宝ISV通道费率更新成功');
				}else{
					$this->error('支付宝ISV通道费率更新失败');
				}
			}
		}
	}

	/**
	 * 更新ISV授权信息
	 */
	public function setToken(){
		if(IS_POST) {
			$db = M('isvToken');
			$data=I('post.');
			$app_id = $data['appid'];
			$map = [
				'mid' => $data['mid'],
				'domain_auth' => domain_auth(),
			];
			if ($db->where($map)->count()) {
				$save = [
					'app_auth_token' => $data['app_auth_token'],
					'user_id' => $data['user_id'],
					'appid' => $app_id,
					'time' => date('Y-m-d H:i:s'),
				];
				$res = $db->where($map)->save($save);
			} else {
				$map['time'] = date('Y-m-d H:i:s');
				$map['app_auth_token'] = $data['app_auth_token'];
				$map['user_id'] = $data['user_id'];
				$map['appid'] = $app_id;
				$res = $db->add($map);
			}
			if ($res) {
				$this->success('商户授权信息更新成功');
			} else {
				$this->error('商户授权信息更新失败');
			}
		}
	}


	/**
	 * 获取基础信息及授权信息
	 */
	public function getToken(){
		if(IS_POST){
			$data=I('post.');
			//全局是否配置服务商APPID
			$isAppId=GetPayConfigs('ali_isv_appid');
			if(empty($isAppId)){$this->error('支付宝ISV服务商配置信息获取失败');}
			//代理是否配置费率
			$_where['domain_auth'] = domain_auth();
			$_where['id'] = $data['id'];
			$aid = M('mch_seller')->where($_where)->getField('agent_id');
			$_rel = M('MchAgent')->where(array('id' => $aid))->find();
			$rate = unserialize($_rel['rate']);
			#判断如果没配置代理终端费率 提示配置
			if (!$rate['Aliisv_term']) {
				$this->error('<p>所属代理姓名:  <span style="color: #2279da;font-size: 1.3em">' . $_rel['user_name'] . '</span></p><p>未配置支付宝ISV服务商通道的默认端费率请先配置代理的终端费率后再操作!<p></p>', '', 9999);
			}
			//是否通道数据里有此通道数据 用来费率计算等
			$count=M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' =>'Aliisv', 'domain_auth' => domain_auth()))->count();
			if($count<1){
				$seller = M('MchSeller')->where(array('id' => $data['id'], 'domain_auth' => domain_auth()))->find();
				unset($seller['id']);
				unset($seller['mch_wx_openid']);
				unset($seller['mch_wx_name']);
				unset($seller['mch_wx_img']);
				unset($seller['codes']);
				unset($seller['alleys']);
				unset($seller['alleys_type']);
				unset($seller['status']);
				unset($seller['mch_id']);
				unset($seller['api_rel']);
				unset($seller['rate']);
				unset($seller['load_status']);
				$allys = array(
					'mch_id'=>'0000',
					'cid' => $data['id'],
					'alleys_type' => 'Aliisv',
					'rate' => $rate['Aliisv_term'],
					'status'=>1,
					'load_status'=>1,
				);
				$add = array_merge($allys, $seller);
				M('MchSellerAlleys')->add($add);
			}
			//获取参数
			$config=M('isvToken')->where(['mid'=>$data['id'],'appid'=>$isAppId,'domain_auth'=>domain_auth()])->find();
			unset($config['id']);
			unset($config['domain_auth']);
			$this->success($config?$config:['mid'=>$data['id'],'user_id'=>'','app_auth_token'=>'']);
		}
	}

	public function storeApi(){
		$data = I('get.');
		$map['domain_auth'] = domain_auth();
		$map['id'] = $data['id'];
		#先判断是否有通道的数据,如果没则用主商户信息
		$info = M('mchSeller')->where($map)->find();
		$assign = array(
			'data' => $info,
		);
		$this->assign($assign);
		$this->display();
	}

}