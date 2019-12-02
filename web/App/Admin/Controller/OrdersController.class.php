<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 渠道商户管理控制器
 */
class OrdersController extends AdminBaseController{



	public function statistics(){
		$data=I('param.');
		#商户筛选
		if($data['mid']){
			$maps['mid']=$data['mid'];
		}
		#门店筛选
		if($data['store_id']){
			$maps['store_id']=$data['store_id'];
		}

		#支付类型
		if($data['type']){
			$maps['service']=['like',$data['type'].'_%'];
		}

		#时间筛选
		$DTime=$data['s_time']?$data['s_time']:date('Ymd');
		$ETime=$data['e_time']?$data['e_time']:date('YmdHis');
		$maps['_string'] = "(`time_end`> '" . strtotime($DTime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
		#时间限制 最大一个月
		$timed = strtotime($ETime)-strtotime($DTime);
		$days = intval($timed/86400);
		if($days > 31){
			$this->error('订单查询时间范围最多 31 天');
		}


		#状态筛选
		$maps['status'] = array('in','1,2');

		#所属品牌
		$maps['domain_auth'] = domain_auth();

		#所属代理
		$maps['agent_id']=$data['aid']?$data['aid']:array('EXP','IS NOT NULL');

		$db = M('mch_orders');
		$assign=[];
		//交易总笔数
		$assign['count']= $db->where($maps)->count();
		//交易总金额
		$assign['sum']= $db->where($maps)->sum('total_fee');
		//退款总笔数
		$assign['refund_count']= $db->where($maps)->where(['service'=>['like','%_refund']])->count();
		//退款总金额
		$assign['refund_sum']= $db->where($maps)->where(['service'=>['like','%_refund']])->sum('total_fee');
		//微信总笔数
		$assign['wx_count']= $db->where($maps)->where(['service'=>['like','wx_%']])->count();
		//微信总金额
		$assign['wx_sum']= $db->where($maps)->where(['service'=>['like','wx_%']])->sum('total_fee');
		//支付宝总笔数
		$assign['ali_count']= $db->where($maps)->where(['service'=>['like','ali_%']])->count();
		//支付宝总金额
		$assign['ali_sum']= $db->where($maps)->where(['service'=>['like','ali_%']])->sum('total_fee');
		$this->assign($assign);
		$this->display();
	}

	public function orderRefund(){
		if(IS_POST){
			$data=I('post.');
			$res=M('mchOrders')->where(['out_trade_no'=>$data['id']])->getField('alleys');
			if($res){
				$module = A('Pays/P' . $res);
				$modules = method_exists($module, 'refund');
				if($modules) {
					$ret = R('Pays/P' . $res . '/refund', [$data['id']]);
					if ($ret['status']) {
						if ($ret['res_status'] == 2) {
							$this->success('订单退款请求成功');
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
					$this->error('当前暂无退款接口');
				}
			}else{
				$this->error('订单号不存在');
			}
		}

	}


	/**
	 * 补发异步通知
	 */
	public function apiReloadNotify(){
		if(IS_POST) {
			$oid = I('param.id');
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

    #V通道提现记录
    public function TxLog(){
        $Data = M('MchSftTx');
        $count      = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->where($maps)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $assign=array(
            'data'=>$_REQUEST,
            'list' => $list,
            'page'=>  $show,
        );
        $this->assign($assign);
        $this->display();
    }

    public function index(){
        $data=I('param.');
        #订单号筛选
        if($data['trade_no']){
            $maps['out_trade_no']=array('like','%'.$data['trade_no'].'%');
        }
        #商户筛选
        if($data['mid']){
            $maps['mid']=$data['mid'];
        }
        #门店筛选
        if($data['store_id']){
            $maps['store_id']=$data['store_id'];
        }
        #通道类型
        if($data['type']){
            $maps['type']=$data['type'];
        }

        #时间筛选
        $DTime=$data['s_time']?$data['s_time']:date('Ymd');
        $ETime=$data['e_time']?$data['e_time']:date('YmdHis');
        $maps['_string'] = "(`time_end`> '" . strtotime($DTime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        #时间限制 最大一个月
        $timed = strtotime($ETime)-strtotime($DTime);
        $days = intval($timed/86400);
        if($days > 31){
            $this->error('订单查询时间范围最多 31 天');
        }


        #状态筛选
        $maps['status'] = array('in','1,2');

        #所属品牌
        $maps['domain_auth'] = domain_auth();

        #通道
        $maps['alleys']=$data['alleys']?$data['alleys']:array('EXP','IS NOT NULL');
        //dump($maps);
        #所属代理
        $maps['agent_id']=$data['aid']?$data['aid']:array('EXP','IS NOT NULL');

        $Data = M('mch_orders');
        $count      = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->where($maps)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        //dump($maps);


        #导出筛选
        $result =M("mch_orders")->where($maps)->order("id desc")->select();
        #订单内的通道列表
        $alleys =M("mch_orders")->where(array('domain_auth'=>domain_auth()))->Distinct(true)->order("id desc")->getField('alleys',true);

        $assign=array(
            'data'=>$_REQUEST,
            'list' => $list,
            'page'=>  $show,
            'alleys'=>$alleys,
        );




        if(!empty($data['export'])&&$data['export']=='ccl'){
            $xlsName  = "Order_";//导出名称
            $xlsCell  = array(
                array('mid','商户名称'),
                array('store_id','门店名称'),
                array('trade_type','交易类型'),
                array('createtime','支付时间'),
                array('time_end','完成时间'),
                array('total_fee','交易金额'),
                array('out_trade_no','交易单号'),
                array('status','交易状态'),
                array('type','通道类型'),
                array('alleys','支付通道'),
            );
            $atitle="交易流水报表生成时间:".date('Y-m-d H:i:s');
            $wbscms=array(
                'Atitle'=>$atitle,
            );
            foreach ($result as $k => $v){
                $serller=Get_Seller($v['mid']);
                $store=Get_Store($v['store_id']);
                $xlsData[$k]['mid']=$serller['mch_name'];
                $xlsData[$k]['store_id']=$store['name'];
                $xlsData[$k]['trade_type']=pays_type($v['service'],1);
                $xlsData[$k]['createtime']=date('Y-m-d H:i:s',$v['createtime']);
                $xlsData[$k]['time_end']=date('Y-m-d H:i:s',$v['time_end']);
                $xlsData[$k]['total_fee']=$v['total_fee'];
                $xlsData[$k]['out_trade_no']="'".$v['out_trade_no'];
                $xlsData[$k]['status']=pays_status($v['status']);
                $xlsData[$k]['type']=$v['type'];
                $xlsData[$k]['alleys']=alleys_name($v['alleys']);
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }


        $this->assign($assign);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //订单详情
    public function detail(){
        $id=I('get.id');
        if(empty($id)){
            $this->error('非法操作!');
        }
        $res=M('mch_orders')->where(array('id'=>$id))->find();
        if($res){
            $this->assign('data',$res);
            $this->display();
        }else{
            $this->error('订单不存在!');
        }
    }




    //执行http调用
    public function curl_calls($callurl,$calldata) {
        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        // curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $callurl);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $calldata);
        // 执行操作
        $res = curl_exec($ch);
        //$this->callresponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $res;
    }





}