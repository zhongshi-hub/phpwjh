<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Base基类控制器
 */
class FlowController extends BaseController{


    public function payList(){
		#时间筛选
		$data = I('param.');
		#订单号
		if($data['oid']){
			$map['oid']=array('like','%'.$data['oid'].'%');
		}else{
			$time = explode(' - ', $data['time']);
			$time[1]=$time[1].' 23:59:59';
			$map['time']  = array('between',[strtotime($time[0]),strtotime($time[1])]);
		}

		if(isset($data['status'])&&!empty($data['status'])){
			switch ($data['status']){
				case 3;
					$map['status'] = 0;
					break;
				case 'all':
					$map['status'] =array('EXP','IS NOT NULL');
					break;
				default:
					$map['status'] = $data['status'];
					break;

			}
		}else{
			$map['status']=1;
		}


		$map['mid'] = session('mp.id');
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
		);
		$this->assign($assign);
		$this->display();
    }

	/**
	 * 充值订单
	 */
	public function setList(){
		#时间筛选
		$data = I('param.');
		#订单号
		if($data['oid']){
			$map['oid']=array('like','%'.$data['oid'].'%');
		}else{
			$time = explode(' - ', $data['time']);
			$time[1]=$time[1].' 23:59:59';
			$map['time']  = array('between',[strtotime($time[0]),strtotime($time[1])]);
		}
		if(isset($data['status'])&&!empty($data['status'])){
			switch ($data['status']){
				case 3;
					$map['status'] = 0;
					break;
				case 'all':
					$map['status'] =array('EXP','IS NOT NULL');
					break;
				default:
					$map['status'] = $data['status'];
					break;

			}
		}else{
			$map['status']=1;
		}

		$map['mid'] = session('mp.id');
		$map['type'] = 1;
		$map['domain_auth'] = domain_auth();
		//dump($map);
		$Data = M('mchFlowOrder');
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
	 * 充值订单状态查询
	 */
	public function orderReload(){
		if(IS_POST){
			$data=I('post.');
			$res=M('mchOrders')->where(['out_trade_no'=>$data['oid']])->getField('alleys');
			if($res){
				$module = A('Pays/P' . $res);
				$modules = method_exists($module, 'orderResult');
				if($modules) {
					$ret = R('Pays/P' . $res . '/orderResult', [$data['oid']]);
					if ($ret['status']) {
						if ($ret['res_status'] == 1) {
							$this->success('订单支付成功');
						} else {
							switch ($ret['res_status']) {
								case 1:
									$msg = '订单支付成功';
									break;
								case 2:
									$msg = '当前订单已退款成功';
									break;
								default:
									$msg = '订单未支付或交易中';
									break;
							}
							$this->error($msg);
						}
					} else {
						$this->error($ret['msg']);
					}
				}else{
					$this->error('当前暂无查询接口');
				}
			}else{
				$this->error('订单号不存在');
			}
		}
	}
}