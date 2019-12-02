<?php
namespace Tasks\Controller;
use Tasks\Controller\TasksBaseController;
/**
 * 渠道定时计算分润
 * CCL
 * End Time: 2017-03-27 21:11
 */
class MchController extends TasksBaseController{


    //渠道分润 按天定时计算
    public function days(){
      $accounts=M('mch_accounts');
      //列出所有的渠道号
      if(isset($_GET['partner'])) {
          $partner = M('mch_parent_api')->where(array('placenum'=>$_GET['partner']))->field('placenum,mcha_rate')->select();
      }else {
          $partner = M('mch_parent_api')->field('placenum,mcha_rate')->order('id Desc')->select();
      }
      //dump($partner);
      foreach ($partner as  $v1) {
          $partner=$v1['placenum'];
          if(isset($_GET['times'])) {
              $daytime = strtotime($_GET['times']);
              $endtime = strtotime(date('Y-m-d',$daytime)."+1 day");
          }else{
              $endtime = strtotime(date('Y-m-d'));
              $daytime = strtotime(date('Y-m-d', $endtime) . "-1 day");
          }
          //查询支付成功的订单
          $or['_string'] = "(`createtime`> '" . $daytime . "') AND ( `createtime` < '" . $endtime . "') ";
          $orders=M('mch_orders')->where(array('partner'=>$partner,'status'=>1))->where($or)->order('id Desc')->select();
          $fee=M('mch_orders')->where(array('partner'=>$partner,'status'=>1))->where($or)->order('id Desc')->sum('total_fee');
          //dump($or);
          if($orders) {
            //根据渠道编号统计当前渠道下所有商户的分润
            $fit=array();
            $fr=array();
            foreach ($orders as  $v) {
                $datas = array(
                    'term' => $v['mch_rate'], //终端价
                    'cost' => $v1['mcha_rate'], //渠道价 随时不影响以前 按照结算表里成本
                    'fee' => $v['total_fee'] / 100, //交易金额
                );
                $set['placenum'] = $v1['placenum'];
                $set['daytime'] = date('Y-m-d H:i:s', $daytime);
                $set['endtime'] = date('Y-m-d H:i:s', $endtime);
                $set['otime'] = date('Y-m-d H:i:s', $v['createtime']);
                $set['fit'] = accounts_rate($datas); //分润金额
                $set['mch_id'] = $v['mch_id']; //商户号
                //取商户名称
                $Mch_name = M('mch_merchant')->where(array('merchantId' => $v['mch_id']))->getField('merchantName');
                $set['mch_name'] = $Mch_name; //商户号
                $set['service'] = $v['service']; //服务接口
                $set['TotalFee'] = $v['total_fee'] / 100; //交易金额
                $set['rate'] = $v['mch_rate'];
                $fit[] = $set;
                $fr[] = accounts_rate($datas);
            }


            //先判断此分润信息数据库是否已存在
            $res = $accounts->where(array('acctime' => $daytime, 'partner' => $v1['placenum']))->count();
            if ($res) {
                //此处存在->日志记录
                $data = array(
                    'partner' => $v1['placenum'],
                    'data' => json_encode($fit),
                    'type' => '数据库Save',
                    'log' => '当前渠道结算信息已存在.执行时间:' . date('Y-m-d H:i:s'),
                );
               self::Tasks_log($data);
            } else {
                //不存在->日志记录->数据库存储
                if(array_sum($fr)<=0){//没有分润直接变为已结算状态
                    $add = array(
                        'status'=>1,
                        'fit_time'=>time(),
                        'fit_fee'=>0,
                        'acctime' => $daytime,
                        'partner' => $v1['placenum'],
                        'rate' => $v1['mcha_rate'],
                        'pay_count' => count($fit),
                        'total_fee' => $fee / 100,
                        'profit' => array_sum($fr),
                        's_time' => date('Y-m-d H:i:s', $daytime),
                        'e_time' => date('Y-m-d H:i:s', $endtime),
                    );
                }else {
                    $add = array(
                        'acctime' => $daytime,
                        'partner' => $v1['placenum'],
                        'rate' => $v1['mcha_rate'],
                        'pay_count' => count($fit),
                        'total_fee' => $fee / 100,
                        'profit' => array_sum($fr),
                        's_time' => date('Y-m-d H:i:s', $daytime),
                        'e_time' => date('Y-m-d H:i:s', $endtime),
                    );
                }
                if ($fee > 0) {
                    M('mch_accounts')->add($add);
                    $data = array(
                        'partner' => $v1['placenum'],
                        'data' => json_encode($add),
                        'type' => '数据库Add-OK',
                        'log' => '当前渠道结算信息更新成功.执行时间:' . date('Y-m-d H:i:s'),
                    );
                } else {
                    $data = array(
                        'partner' => $v1['placenum'],
                        'data' => json_encode($add),
                        'type' => '数据库Add-Not',
                        'log' => '渠道无成交额.执行时间:' . date('Y-m-d H:i:s'),
                    );
                }
                self::Tasks_log($data);
            }
         }

      }

      echo "Success";

    }



    public function Tasks_log($data){
        $data['createtime']=date('Y-m-d H:i:s');
        M('mch_tasks_log')->add($data);
    }


    public function mondays(){
        $begin = date('Y-m',time());
        $begintime = strtotime($begin);
        $endtime = strtotime("-1 day");
        for ($start = $begintime; $start < $endtime; $start += 24 * 3600) {
            $Dates[]=date("Y-m-d", $start);
            $Times[]=$start;
        }
        return $Times;
    }




    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p><b>YDF Service</b>！</p></div>','utf-8');
    }
}