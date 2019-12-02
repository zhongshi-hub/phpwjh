<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;
/**
 * 渠道商户管理控制器
 */
class OrdersController extends SystemBaseController{

    #盛付通提现记录
    public function STxLog(){
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

    #提现记录
    public function TxLog(){
        $Data = M('MchOrdersTx');
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
        $maps['status'] = 1;

        #所属品牌
        $maps['domain_auth'] = $data['domain_auth']?$data['domain_auth']:array('EXP','IS NOT NULL');

        #通道
        $maps['alleys']=$data['alleys']?$data['alleys']:array('neq','');
        //dump($maps);

        #所属代理
        $maps['agent_id']=$data['aid']?$data['aid']:array('EXP','IS NOT NULL');

        $Data = M('mch_orders');
        $count      = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->where($maps)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        #总交易额
        $To_sum=$Data->where($maps)->sum('total_fee');
        #总笔数
        $To_count=$Data->where($maps)->count();
        #微信总额
        $ToWxSum=$Data->where($maps)->where(array('service'=>array('in','wx_jsapi')))->sum('total_fee');
        #微信总笔数
        $ToWxCount=$Data->where($maps)->where(array('service'=>array('in','wx_jsapi')))->count();
        #支付宝总额
        $ToAliSum=$Data->where($maps)->where(array('service'=>array('in','ali_jsapi')))->sum('total_fee');
        #支付宝总笔数
        $ToAliCount=$Data->where($maps)->where(array('service'=>array('in','ali_jsapi')))->count();



        #导出筛选
        $result =M("mch_orders")->where($maps)->order("id desc")->select();
        #订单内的通道列表
        $alleys =M("mch_orders")->where(array('domain_auth'=>domain_auth()))->Distinct(true)->order("id desc")->getField('alleys',true);

        $assign=array(
            'data'=>$_REQUEST,
            'list' => $list,
            'page'=>  $show,
            'alleys'=>$alleys,
            'To'=>array(
                'sum'=>round($To_sum,2),
                'count'=>$To_count,
                'wxsum'=>round($ToWxSum,2),
                'wxcount'=>$ToWxCount,
                'alisum'=>round($ToAliSum,2),
                'alicount'=>$ToAliCount,
            ),
        );




        if(!empty($data['export'])&&$data['export']=='ccl'){
            $xlsName  = "Order_";//导出名称
            $xlsCell  = array(
                array('domain','所属品牌'),
                array('mid','商户名称'),
                array('store_id','门店名称'),
                array('trade_type','交易类型'),
                array('createtime','支付时间'),
                array('time_end','完成时间'),
                array('total_fee','交易金额'),
                array('out_trade_no','交易单号'),
                array('status','交易状态'),
                array('type','通道类型'),
                array('rate','终端费率'),
                array('alleys','支付通道'),
            );
            $atitle="交易流水报表生成时间:".date('Y-m-d H:i:s');
            $wbscms=array(
                'Atitle'=>$atitle,
            );
            foreach ($result as $k => $v){
                $serller=Get_Seller($v['mid']);
                $store=Get_Store($v['store_id']);
                $xlsData[$k]['domain']=DomainName($v['domain_auth']);
                $xlsData[$k]['mid']=$serller['mch_name'];
                $xlsData[$k]['store_id']=$store['name'];
                $xlsData[$k]['trade_type']=pays_type($v['service'],1);
                $xlsData[$k]['createtime']=date('Y-m-d H:i:s',$v['createtime']);
                $xlsData[$k]['time_end']=date('Y-m-d H:i:s',$v['time_end']);
                $xlsData[$k]['total_fee']=$v['total_fee'];
                $xlsData[$k]['out_trade_no']="'".$v['out_trade_no'];
                $xlsData[$k]['status']=pays_status($v['status']);
                $xlsData[$k]['type']=$v['type'];
                $xlsData[$k]['rate']=$v['mch_rate'];
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

    //订单补发通知
    public function  notices(){
        if(IS_POST) {
            $id=I('post.id');
            $data=M('mch_orders')->where(array('id'=>$id))->find();
            $res = self::curl_calls($data['notify_url'], $data['notify_data']);
            dump($res);

            /*if($res){
                $this->success('客户端正常!已成功补发订单通知!');
            }else{
                $this->error('客户端异常!补发订单通知失败!');
            }*/
        }else{
            $this->error('非法操作');
        }
    }

    //订单状态更新
    public function  updates(){

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