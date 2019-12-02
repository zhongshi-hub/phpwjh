<?php
namespace Agent\Controller;
use Agent\Controller\InitBaseController;
class OrdersController extends InitBaseController
{

    public function index(){
        $data=I('param.');

        $maps['agent_id']=$data['aid']?$data['aid']:array('in',self::AgentAll());

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
        $maps['status'] = 1;
        #所属品牌
        $maps['domain_auth'] = domain_auth();
        #通道
        $maps['alleys']=$data['alleys']?$data['alleys']:array('EXP','IS NOT NULL');

        $Data = M('mch_orders');
        $count      = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->where($maps)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

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

}