<?php

namespace Tasks\Controller;
use Think\Controller;
#分润日清算数据
class SysBenefitController extends Controller
{




    #合作伙伴分润
    public function frdata(){
        #时间范围
        $data=I('get.');
        if($data['day']) {
            if(strtotime($data['day'])>time()){
                die('您的时间还没到呢');
            }else {
                $STime=date('Ymd',strtotime($data['day'])); #开始时间
                $ETime=date('Ymd',strtotime($data['day']."+1 day")); #结束时间
            }
        }else{
            $STime=date('Ymd',strtotime("-1 day")); #开始时间
            $ETime=date('Ymd'); #结束时间
        }

        #列出通道
        $SysAlleys=M('MchAlleys')->field('type,rate')->select();
        #根据品牌列出代理列表
        foreach($SysAlleys as $do) {
            #状态筛选
            $maps['status'] = 1;
            #所属品牌
            $maps['alleys'] = $do['type'];
            $maps['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            #所有流水
            $Order=M('MchOrders')->where($maps)->select();
            #总笔数
            $count= M('MchOrders')->where($maps)->count();
            #总交易额
            $fee = M('MchOrders')->where($maps)->sum('total_fee');

            #根据流水计算每一条交易的分润
            #定义数组
            $Benefit=array();
            $Money=array();
            foreach($Order as $row) {
                $fr['day']=strtotime($STime); //属于哪一天
                $fr['oid']=$row['id'];
                $fr['mid']=$row['mid'];
                $fr['store_id']=$row['store_id'];
                $fr['aid']=$row['agent_id'];
                $fr['out_trade_no']=$row['out_trade_no'];
                $fr['alleys']=$row['alleys'];
                $fr['type']=$row['type'];
                $fr['total']=$row['total_fee'];
                #费率相关
                $fr['cost']=$do['rate']; #所属通道成本费率
                $fr['term']=$row['mch_rate']; #此订单的终端费率
                #计算分润
                $be=((bcsub($fr['term'],$fr['cost'],2))*$fr['total'])/1000;
                $fr['money']=round($be,2);
                $fr['NoMoney']=$be;
                $fr['domain_auth']=$row['domain_auth'];
                $fr['time_end']=$row['time_end'];
                #数据转化 详细信息存储
                if($fr['total']){
                    //dump($list);
                    $OrderDb=M('SystemFenrunOrder');
                    #判断数据库是否有此记录
                    $FrMap['day']=$fr['day'];
                    $FrMap['out_trade_no']=$fr['out_trade_no'];
                    $FrRes=$OrderDb->where($FrMap)->count();
                    if($FrRes){
                        #存在信息
                        $OrderDb->where($FrMap)->save($fr);
                    }else{
                        $OrderDb->add($fr);
                    }
                }
                $Benefit[]=$fr;
                $Money[]=$fr['money'];
            }

            #计算最终结果
            $list['benefit'] = number_format(round(array_sum($Money),2),2); //交易总分润
            $list['count'] = $count; //交易总笔数
            $list['fee'] = number_format($fee,2); //交易总金额
            $list['mon']=date('Y-m',strtotime($STime)); //属于哪个月份  根据规则 提前一天算  今天前一天
            $list['day']=strtotime($STime); //属于哪个月份  根据规则 提前一天算  今天前一天
            $list['s'] =strtotime(date('Y-m-d H:i:s',strtotime($STime))); //开始时间
            $list['e'] =strtotime(date('Y-m-d H:i:s',strtotime($ETime))); //结束时间
            $list['update']=date('Y-m-d H:i:s'); //此次更新时间
            $list['alleys']=$do['type'];
            if($list['benefit']!='0.00'||$list['fee']!='0.00'){
                //dump($list);
                $db=M('SystemFenrunDays');
                #判断数据库是否有此记录
                $DayMap['day']=$list['day'];
                $DayMap['alleys']=$list['alleys'];
                $Days=$db->where($DayMap)->count();
                if($Days){
                    #存在信息 保存
                    $db->where($DayMap)->save($list);
                }else{
                    $db->add($list);
                }
                echo 'Success';
            }
        }
    }






}