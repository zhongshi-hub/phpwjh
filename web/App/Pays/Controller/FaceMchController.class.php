<?php
namespace Pays\Controller;
use Think\Controller;
class FaceMchController extends Controller
{


	/**
	 * 青蛙系列刷脸设备广告
	 * @param $data
	 * @return array
	 */
	public function adData($data){
        $db=M('faceAd');
        $map=[
            'status'=>1,
            'domain_auth'=>domain_auth(),
            'type'=>$data['type']
        ];
        $list=$db->where($map)->order('sort desc')->select();
        $listId=[];
        foreach ($list as $k=>$v){
            $isTime=ad_time($v['start_time'],$v['end_time']);
            if($isTime){
                $listId[]=$v['img'];
            }
        }
        $img=$listId;
		$imgArr=[];
		foreach ($img as $k=>$v){
			$k+=1;
			if (!preg_match("/^(http:\/\/|https:\/\/).*$/", $v)) {
				$path='http://'.$_SERVER['HTTP_HOST'].'/'.$v;
			}else{
				$path=$v;
			}
			$imgArr["image{$k}"]=$path;
		}
		$imgArr['store_id']=$data['store_id'];
		$imgArr['type']=$data['type'];
		$imgArr['test']=$listId;
		return ['code'=>100,'msg'=>'广告资料获取成功','data'=>$imgArr];
	}


	/**
	 * 交易汇总
	 * @param $data
	 * @return array
	 */
	public function orderCount($data){
        $StartTime=$data['sdate']?strtotime($data['sdate']):strtotime(date('Ymd'));
        $EndTime=$data['edate']?strtotime($data['edate']):time();
        if(date('Ymd',strtotime($data['sdate']))==date('Ymd',strtotime($data['edate']))){
            $EndTime=strtotime($data['edate']." +24 hours");
        }
        $StoreId=$data['store_id']?$data['store_id']:array('EXP','IS NOT NULL');
		$isStore=$this->getAppidStore($data['appid']);
		if($isStore['store_id']){
			$where=['store_id'=>$StoreId];
		}else{
			$where=['mid'=>$isStore['mch_id']];
		}
        if($StartTime&&$EndTime) {
            //判断结束时间不能大于开始时间
            if ($StartTime > $EndTime) {
                return ['code'=>400,'msg'=>'结束时间不能小于开始时间'];
            } else {
                $where['_string'] = "(`createtime`> '" . $StartTime . "') AND ( `createtime` < '" . $EndTime . "') ";
            }
        }
        $order=M('mchOrders');
        $count=$order->where($where)->where(['status'=>['in','1,2']])->count();//交易总笔数
        $total=$order->where($where)->where(['status'=>['in','1,2']])->sum('total_fee'); //交易总金额
        $re_total=$order->where($where)->where(['service'=>['like','%_refund'],'status'=>1])->sum('total_fee'); //退款总金额
        $suc_total=$order->where($where)->where(['status'=>1])->sum('total_fee'); //支付成功总金额
		$suc_total=$suc_total-$re_total;
        //微信
        $wx_count=$order->where($where)->where(['service'=>['like','wx_%'],'status'=>['in','1,2']])->count();//交易总笔数
        $wx_total=$order->where($where)->where(['service'=>['like','wx_%'],'status'=>['in','1,2']])->sum('total_fee'); //交易总金额
        $wx_re_total=$order->where($where)->where(['service'=>'wx_refund','status'=>1])->sum('total_fee'); //退款总金额
        $wx_suc_total=$order->where($where)->where(['service'=>['like','wx_%'],'status'=>1])->sum('total_fee'); //支付成功总金额
		$wx_suc_total=$wx_suc_total-$wx_re_total;
        //支付宝
        $ali_count=$order->where($where)->where(['service'=>['like','ali_%'],'status'=>['in','1,2']])->count();//交易总笔数
        $ali_total=$order->where($where)->where(['service'=>['like','ali_%'],'status'=>['in','1,2']])->sum('total_fee'); //交易总金额
        $ali_re_total=$order->where($where)->where(['service'=>'ali_refund','status'=>1])->sum('total_fee'); //退款总金额
        $ali_suc_total=$order->where($where)->where(['service'=>['like','ali_%'],'status'=>1])->sum('total_fee'); //支付成功总金额
		$ali_suc_total=$ali_suc_total-$ali_re_total;
        $arr=[
          'stime'=>date('Y-m-d H:i:s',$StartTime),
          'etime'=>date('Y-m-d H:i:s',$EndTime),
          'count'=>$count?$count:0,
          'total'=>round($total-$re_total,2)*100,//交易总金额-退款金额
          're_total'=>round($re_total,2)*100,
          'suc_total'=>round($suc_total-$re_total,2)*100,
          'wx_count'=>$wx_count?$wx_count:0,
          'wx_total'=>round($wx_total-$wx_re_total,2)*100,
          'wx_re_total'=>round($wx_re_total,2)*100,
          'wx_suc_total'=>round($wx_suc_total-$wx_re_total,2)*100,
          'ali_count'=>$ali_count?$ali_count:0,
          'ali_total'=>round($ali_total-$ali_re_total,2)*100,
          'ali_re_total'=>round($ali_re_total,2)*100,
          'ali_suc_total'=>round($ali_suc_total-$ali_re_total,2)*100,
        ];
        return ['code'=>100,'msg'=>'汇总数据成功','data'=>$arr];

    }

	/**
	 * 交易信息
	 * @param $data
	 * @return array
	 */
	public function orderList($data)
	{
		$StartTime=$data['sdate']?strtotime($data['sdate']):strtotime(date('Ymd'));
		$EndTime=$data['edate']?strtotime($data['edate']):time();
		if(date('Ymd',strtotime($data['sdate']))==date('Ymd',strtotime($data['edate']))){
			$EndTime=strtotime($data['edate']." +24 hours");
		}
		$StoreId=$data['store_id']?$data['store_id']:array('EXP','IS NOT NULL');
		switch ($data['status']){
			case 1:
				$Status=1;
				break;
			case 2:
				$Status=2;
				break;
			case 3:
				$Status=0;
				break;
			case 4:
				$Status=array('EXP','IS NOT NULL');
				break;
			default:
				$Status=1;
				break;
		}

		$isStore=$this->getAppidStore($data['appid']);
		if($isStore['store_id']){
			$where=['store_id'=>$StoreId];
		}else{
			$where=['mid'=>$isStore['mch_id']];
		}
		if((int)$data['status']!=4){
			$where['status']=$Status;
		}
        if($data['service']!='all'){
            $where['service']=['like',$data['service'].'_%'];
        }

		if($StartTime&&$EndTime) {
			//判断结束时间不能大于开始时间
			if ($StartTime > $EndTime) {
				return ['code'=>400,'msg'=>'结束时间不能小于开始时间'];
			} else {
				$where['_string'] = "(`createtime`> '" . $StartTime . "') AND ( `createtime` < '" . $EndTime . "') ";
			}
		}
		$page=$data['page']?$data['page']:1;
		$limit=$data['limit']?$data['limit']:10;

        //dump($where);
		$order=M('mchOrders');
		$OData=$order->where($where)->page($page.','.$limit)->order('id desc')->field('out_trade_no,transaction_id,createtime,time_end,status,total_fee,service,store_id')->select();
		$counts=$order->where($where)->count();//交易笔数
		$refundSum=$order->where($where)->where(['service'=>['like','%_refund']])->sum('total_fee'); //退款金额
		$amountSum=$order->where($where)->sum('total_fee'); //交易总金额
		$amountSum=$amountSum-$refundSum; //实收金额
		//$amount=$amount*100;
		$arr=[];
		$oArr=[];
		foreach ($OData as $k=>$val){
		    switch ($val['status']){
                case 1:
                    $status=1;
                    $payStatus=1;
                    break;
                case 2:
                    $status=1;
                    $payStatus=2;
                    break;
                default:
                    $status=0;
                    $payStatus=0;
                    break;
            }
            if(explode('_',$val['service'])[1]=='refund'){
				$status=1;
				$payStatus=2;
			}
			$arr['out_trade_no']=$val['out_trade_no'];
			$arr['transaction_id']=$val['transaction_id'];
			$arr['create_time']=date('Y-m-d H:i:s',$val['createtime']);
			$arr['status']=$status;
			$arr['pay_status']=$payStatus;
			$arr['time_end']=$val['time_end']?date('Y-m-d H:i:s',$val['time_end']):'';
			$arr['total_fee']=$val['total_fee']*100; //转换为分单位
			$arr['store_id']=$val['store_id'];
			$arr['service']=explode('_',$val['service'])[0];
			$oArr[]=$arr;
			$total[]=$val['total_fee'];

		}
		$count=count($oArr);
		//$amount=array_sum($total)*100;
		if($OData) {
			//$DataArr = ['counts'=>(int)$counts,'amounts'=>$amountSum*100,'count' => $count?$count:0, 'amount' => $amount ? $amount : 0, 'order_data' => $oArr ? $oArr : null];
            $DataArr = ['counts'=>(int)$counts,'amount'=>$amountSum*100,'count' => $count?$count:0, 'order_data' => $oArr ? $oArr : null];
            return ['code'=>100,'msg'=>'查询交易流水成功','data'=>$DataArr];
		}else{
			return ['code'=>100,'msg'=>'无交易流水','data'=>[]];
		}
	}


	public function getAppidStore($appid){
		$res=M('mchTerminal')->where(array('appid'=>$appid))->find();
		return $res;
	}

}