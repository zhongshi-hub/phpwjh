<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Base基类控制器
 */
class OrderController extends BaseController{



	public function orderNotify(){
		if(IS_POST) {
			$oid = I('param.oid');
			if($oid){
				//异步补发
				$ret=send_notify($oid,'','',true);
				if($ret['status']==1) {
					$this->success($ret['msg'].' 下游接口返回:'.$ret['rel']);
				}else{
					$this->error($ret['msg']);
				}
			}else{
				$this->error('非法操作');
			}
		}
	}

    public function index(){
        $map=[
            'mid'=>session('mp.id'),
            'domain_auth'=>domain_auth(),
            'status'=>array('in','1,2')
        ];

        #时间筛选
        $data = I('post.');

        #订单号
        if($data['oid']){
            $map['out_trade_no|transaction_id']=array('like','%'.$data['oid'].'%');
        }else{
            $time = explode(' - ', $data['time']);
            $DTime = $time[0] ? $time[0] : date('Ymd');
            $ETime = $time[1] ? $time[1] : date('YmdHis');
            $map['_string'] = "(`time_end`> '" . strtotime($DTime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            #时间限制 最大一个月
            $timed = strtotime($ETime) - strtotime($DTime);
            $days = intval($timed / 86400);
            if ($days > 31) {
                $this->error('订单查询时间范围最多 31 天');
            }
        }
        //dump($map);



        $Data = M('mchOrders');
        $count      = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $assign=array(
            'list' => $list,
            'page'=>  $show,
        );
        //dump($show);
        $this->assign($assign);
        $this->display();
    }


    public function orderRefund(){
		$this->error('为了您的资金数据安全,请使用APP或PC客户端退款');
//		if(IS_POST){
//			$data=I('post.');
//			$res=M('mchOrders')->where(['mid'=>session('mp.id'),'out_trade_no'=>$data['oid']])->getField('alleys');
//			if($res){
//				$module = A('Pays/P' . $res);
//				$modules = method_exists($module, 'refund');
//				if($modules) {
//					$ret = R('Pays/P' . $res . '/refund', [$data['oid']]);
//					if ($ret['status']) {
//						if ($ret['res_status'] == 2) {
//							$this->success('订单退款请求成功');
//						} else {
//							switch ($ret['res_status']) {
//								case 1:
//									$msg = '订单支付成功';
//									break;
//								case 2:
//									$msg = '当前订单已退款成功';
//									break;
//								default:
//									$msg = '订单未支付或交易中';
//									break;
//							}
//							$this->error($msg);
//						}
//					} else {
//						$this->error($ret['msg']);
//					}
//				}else{
//					$this->error('当前暂无退款接口');
//				}
//			}else{
//				$this->error('订单号不存在');
//			}
//		}

	}
}