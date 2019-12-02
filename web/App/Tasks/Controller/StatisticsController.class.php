<?php
namespace Tasks\Controller;
use Think\Controller;
#数据统计
class StatisticsController extends Controller
{

    #总后台数据统计
    public function system(){
        $db=M('MchOrders');
        $map['status']=1;
        //$map['domain_auth']=domain_auth();
        #数据汇总
        #总交易额
        $To_sum=$db->where($map)->sum('total_fee');
        #总笔数
        $To_count=$db->where($map)->count();
        #微信总额
        $ToWxSum=$db->where($map)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
        #微信总笔数
        $ToWxCount=$db->where($map)->where(array('service'=>array('like','wx_%')))->count();
        #支付宝总额
        $ToAliSum=$db->where($map)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
        #支付宝总笔数
        $ToAliCount=$db->where($map)->where(array('service'=>array('like','ali_%')))->count();
        #快捷总额
        $ToCardSum=$db->where($map)->where(array('service'=>array('like','card_%')))->sum('total_fee');
        #快捷总笔数
        $ToCardCount=$db->where($map)->where(array('service'=>array('like','card_%')))->count();
        #商户数量
        $ToMch=M('MchSeller')->where($map)->count();

        #今日汇总
        $J_STime=date("Y-m-d");
        $J_ETime=date('Y-m-d',strtotime("1 day"));
        $J_where['_string'] = "(`time_end`> '" . strtotime($J_STime) . "') AND ( `time_end` < '" . strtotime($J_ETime) . "') ";
        #统计今日总交易额
        $J_Sum=$db->where($map)->where($J_where)->sum('total_fee');
        #今日总笔数
        $J_Count=$db->where($map)->where($J_where)->count();
        #今日微信总额
        $J_WxSum=$db->where($map)->where($J_where)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
        #今日微信总笔数
        $J_WxCount=$db->where($map)->where($J_where)->where(array('service'=>array('like','wx_%')))->count();
        #今日支付宝总额
        $J_AliSum=$db->where($map)->where($J_where)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
        #今日支付宝总笔数
        $J_AliCount=$db->where($map)->where($J_where)->where(array('service'=>array('like','ali_%')))->count();
        #今日快捷总额
        $J_CardSum=$db->where($map)->where($J_where)->where(array('service'=>array('like','card_%')))->sum('total_fee');
        #今日快捷笔数
        $J_CardCount=$db->where($map)->where($J_where)->where(array('service'=>array('like','card_%')))->count();


        #昨日汇总
        $STime=date("Y-m-d",strtotime("-1 day"));
        $ETime=date('Y-m-d');
        $where['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        #统计昨日总交易额
        $GoSum=$db->where($map)->where($where)->sum('total_fee');
        #昨日总笔数
        $GoCount=$db->where($map)->where($where)->count();
        #昨日微信总额
        $GoWxSum=$db->where($map)->where($where)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
        #昨日微信总笔数
        $GoWxCount=$db->where($map)->where($where)->where(array('service'=>array('like','wx_%')))->count();
        #昨日支付宝总额
        $GoAliSum=$db->where($map)->where($where)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
        #昨日支付宝总笔数
        $GoAliCount=$db->where($map)->where($where)->where(array('service'=>array('like','ali_%')))->count();
        #昨日快捷总额
        $GoCardSum=$db->where($map)->where($where)->where(array('service'=>array('like','card_%')))->sum('total_fee');
        #昨日快捷总笔数
        $GoCardCount=$db->where($map)->where($where)->where(array('service'=>array('like','card_%')))->count();

        #活跃商户数
        $Mch=$db->where($map)->distinct(true)->getField('mid',true);


        #七天统计数据
        $date=array();
        for($i=6;$i>=0;$i--){
            $date[]=date("Y-m-d",strtotime("-$i day"));
        }
        $OutData=array();
        foreach ($date as $key=>$val) {
            $SDate=strtotime($val);
            $SDates=date('Y/m/d', $SDate);
            $EDate = date('Y/m/d', strtotime("$SDates +1 day"));
            $EDates=strtotime($EDate);
            #筛选
            $_map['_string'] = "(`time_end`> '" . $SDate. "') AND ( `time_end` < '" . $EDates. "') ";
            #总交易额
            $_Sum=$db->where($map)->where($_map)->sum('total_fee');
            #总笔数
            $_Count=$db->where($map)->where($_map)->count();
            #微信交易额
            $_WxSum=$db->where($map)->where($_map)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
            #微信笔数
            $_WxCount=$db->where($map)->where($_map)->where(array('service'=>array('like','wx_%')))->count();
            #支付宝交易额
            $_AliSum=$db->where($map)->where($_map)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
            #支付宝笔数
            $_AliCount=$db->where($map)->where($_map)->where(array('service'=>array('like','ali_%')))->count();
            #快捷交易额
            $_CardSum=$db->where($map)->where($_map)->where(array('service'=>array('like','card_%')))->sum('total_fee');
            #快捷笔数
            $_CardCount=$db->where($map)->where($_map)->where(array('service'=>array('like','card_%')))->count();
            #商户数量
            $_maps['_string'] = "(`ctime`> '" . $SDate. "') AND ( `ctime` < '" . $EDates. "') ";
            $_Mch=M('MchSeller')->where($map)->where($_maps)->count();
            $OutData[]=array(
                'sum'=>round($_Sum,2),
                'count'=>$_Count,
                'wx_sum'=>round($_WxSum,2),
                'wx_count'=>$_WxCount,
                'ali_sum'=>round($_AliSum,2),
                'ali_count'=>$_AliCount,
                'card_sum'=>round($_CardSum,2),
                'card_count'=>$_CardCount,
                'mch'=>$_Mch,
                'day'=>$SDates,
            );
        }


        $_sum=array();$_count=array();$_wxsum=array();$_wxcount=array();$_alicount=array();$_alisum=array();$_mch=array();$_day=array();$_cardsum=array();$_cardcount=array();
        foreach ($OutData as $_key=>$_val){
            $_sum[]="'".$_val['sum']."'";
            $_count[]="'".$_val['count']."'";
            $_wxsum[]="'".$_val['wx_sum']."'";
            $_wxcount[]="'".$_val['wx_count']."'";
            $_alisum[]="'".$_val['ali_sum']."'";
            $_alicount[]="'".$_val['ali_count']."'";
            $_cardsum[]="'".$_val['card_sum']."'";
            $_cardcount[]="'".$_val['card_count']."'";
            $_mch[]="'".$_val['mch']."'";
            $_day[]="'".$_val['day']."'";
        }


        // dump($OutData);

        $assign=array(
            'Go'=>array(
                'sum'=>round($GoSum,2),
                'count'=>$GoCount,
                'wxsum'=>round($GoWxSum,2),
                'wxcount'=>$GoWxCount,
                'alisum'=>round($GoAliSum,2),
                'alicount'=>$GoAliCount,
                'card_sum'=>round($GoCardSum,2),
                'card_count'=>$GoCardCount,
            ),
            'Day'=>array(
                'sum'=>round($J_Sum,2),
                'count'=>$J_Count,
                'wxsum'=>round($J_WxSum,2),
                'wxcount'=>$J_WxCount,
                'alisum'=>round($J_AliSum,2),
                'alicount'=>$J_AliCount,
                'card_sum'=>round($J_CardSum,2),
                'card_count'=>$J_CardCount,
            ),
            'To'=>array(
                'sum'=>round($To_sum,2),
                'count'=>$To_count,
                'wxsum'=>round($ToWxSum,2),
                'wxcount'=>$ToWxCount,
                'alisum'=>round($ToAliSum,2),
                'alicount'=>$ToAliCount,
                'card_sum'=>round($ToCardSum,2),
                'card_count'=>$ToCardCount,
                'mch'=>$ToMch,
            ),
            'Mch'=>array(
                'live'=>count($Mch),
                'bed'=>$ToMch-count($Mch),
            ),
            'Mon'=>array(
                'sum'=>implode(',', $_sum),
                'count'=>implode(',', $_count),
                'wxsum'=>implode(',', $_wxsum),
                'wxcount'=>implode(',', $_wxcount),
                'alisum'=>implode(',', $_alisum),
                'alicount'=>implode(',', $_alicount),
                'card_sum'=>implode(',', $_cardsum),
                'card_count'=>implode(',', $_cardcount),
                'mch'=>implode(',', $_mch),
                'day'=>implode(',', $_day)
            )
        );

        //dump($assign);
        $Static=M('DataStatistics');
        $sys_data=array(
            'type'=>'system',
            'day_data'=>json_encode($assign['Day']), //今日
            'terday_data'=>json_encode($assign['Go']), //昨日
            'count_data'=>json_encode($assign['To']),//总数据
            'mch_data'=>json_encode($assign['Mch']), //商户统计
            'week_data'=>json_encode($assign['Mon']),//近七天
            'etime'=>date('Y-m-d H:i:s'),
            'domain_auth'=>domain_auth(),
        );
		$cache_system='system_static';
		S($cache_system,$sys_data);

//        $result=$Static->where(array('type'=>'system'))->count();
//        if($result){
//            $Static->where(array('type'=>'system'))->save($sys_data);
//        }else{
//            $Static->add($sys_data);
//        }
    }



    #品牌管理端
    public function admin(){
        #先列出所有品牌
        $DomainAuth=M('Domain_auth')->where(array('status'=>1))->field('web_authcode')->select();
        #根据品牌列出代理列表
        foreach($DomainAuth as $do) {
            $db=M('MchOrders');
            $map['status']=1;
            $map['domain_auth']=$do['web_authcode'];
            #数据汇总
            #总交易额
            $To_sum=$db->where($map)->sum('total_fee');
            #总笔数
            $To_count=$db->where($map)->count();
            #微信总额
            $ToWxSum=$db->where($map)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
            #微信总笔数
            $ToWxCount=$db->where($map)->where(array('service'=>array('like','wx_%')))->count();
            #支付宝总额
            $ToAliSum=$db->where($map)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
            #支付宝总笔数
            $ToAliCount=$db->where($map)->where(array('service'=>array('like','ali_%')))->count();
            #快捷总额
            $ToCardSum=$db->where($map)->where(array('service'=>array('like','card_%')))->sum('total_fee');
            #快捷总笔数
            $ToCardCount=$db->where($map)->where(array('service'=>array('like','card_%')))->count();
            #商户数量
            $ToMch=M('MchSeller')->where($map)->count();

            #今日汇总
            $J_STime=date("Y-m-d");
            $J_ETime=date('Y-m-d',strtotime("1 day"));
            $J_where['_string'] = "(`time_end`> '" . strtotime($J_STime) . "') AND ( `time_end` < '" . strtotime($J_ETime) . "') ";
            #统计昨日总交易额
            $J_Sum=$db->where($map)->where($J_where)->sum('total_fee');
            #今日总笔数
            $J_Count=$db->where($map)->where($J_where)->count();
            #今日微信总额
            $J_WxSum=$db->where($map)->where($J_where)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
            #今日微信总笔数
            $J_WxCount=$db->where($map)->where($J_where)->where(array('service'=>array('like','wx_%')))->count();
            #今日支付宝总额
            $J_AliSum=$db->where($map)->where($J_where)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
            #今日支付宝总笔数
            $J_AliCount=$db->where($map)->where($J_where)->where(array('service'=>array('like','ali_%')))->count();
            #今日快捷总额
            $J_CardSum=$db->where($map)->where($J_where)->where(array('service'=>array('like','card_%')))->sum('total_fee');
            #今日快捷笔数
            $J_CardCount=$db->where($map)->where($J_where)->where(array('service'=>array('like','card_%')))->count();


            #昨日汇总
            $STime=date("Y-m-d",strtotime("-1 day"));
            $ETime=date('Y-m-d');
            $where['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            #统计昨日总交易额
            $GoSum=$db->where($map)->where($where)->sum('total_fee');
            #昨日总笔数
            $GoCount=$db->where($map)->where($where)->count();
            #昨日微信总额
            $GoWxSum=$db->where($map)->where($where)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
            #昨日微信总笔数
            $GoWxCount=$db->where($map)->where($where)->where(array('service'=>array('like','wx_%')))->count();
            #昨日支付宝总额
            $GoAliSum=$db->where($map)->where($where)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
            #昨日支付宝总笔数
            $GoAliCount=$db->where($map)->where($where)->where(array('service'=>array('like','ali_%')))->count();
            #昨日快捷总额
            $GoCardSum=$db->where($map)->where($where)->where(array('service'=>array('like','card_%')))->sum('total_fee');
            #昨日快捷总笔数
            $GoCardCount=$db->where($map)->where($where)->where(array('service'=>array('like','card_%')))->count();
            #活跃商户数
            $Mch=$db->where($map)->distinct(true)->getField('mid',true);


            #七天统计数据
            $date=array();
            for($i=6;$i>=0;$i--){
                $date[]=date("Y-m-d",strtotime("-$i day"));
            }
            $OutData=array();
            foreach ($date as $key=>$val) {
                $SDate=strtotime($val);
                $SDates=date('Y/m/d', $SDate);
                $EDate = date('Y/m/d', strtotime("$SDates +1 day"));
                $EDates=strtotime($EDate);
                #筛选
                $_map['_string'] = "(`time_end`> '" . $SDate. "') AND ( `time_end` < '" . $EDates. "') ";
                #总交易额
                $_Sum=$db->where($map)->where($_map)->sum('total_fee');
                #总笔数
                $_Count=$db->where($map)->where($_map)->count();
                #微信交易额
                $_WxSum=$db->where($map)->where($_map)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
                #微信笔数
                $_WxCount=$db->where($map)->where($_map)->where(array('service'=>array('like','wx_%')))->count();
                #支付宝交易额
                $_AliSum=$db->where($map)->where($_map)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
                #支付宝笔数
                $_AliCount=$db->where($map)->where($_map)->where(array('service'=>array('like','ali_%')))->count();
                #快捷交易额
                $_CardSum=$db->where($map)->where($_map)->where(array('service'=>array('like','card_%')))->sum('total_fee');
                #快捷笔数
                $_CardCount=$db->where($map)->where($_map)->where(array('service'=>array('like','card_%')))->count();
                #商户数量
                $_maps['_string'] = "(`ctime`> '" . $SDate. "') AND ( `ctime` < '" . $EDates. "') ";
                $_Mch=M('MchSeller')->where($map)->where($_maps)->count();
                $OutData[]=array(
                    'sum'=>round($_Sum,2),
                    'count'=>$_Count,
                    'wx_sum'=>round($_WxSum,2),
                    'wx_count'=>$_WxCount,
                    'ali_sum'=>round($_AliSum,2),
                    'ali_count'=>$_AliCount,
                    'card_sum'=>round($_CardSum,2),
                    'card_count'=>$_CardCount,
                    'mch'=>$_Mch,
                    'day'=>$SDates,
                );
            }


            $_sum=array();$_count=array();$_wxsum=array();$_wxcount=array();$_alicount=array();$_alisum=array();$_mch=array();$_day=array();$_cardsum=array();$_cardcount=array();
            foreach ($OutData as $_key=>$_val){
                $_sum[]="'".$_val['sum']."'";
                $_count[]="'".$_val['count']."'";
                $_wxsum[]="'".$_val['wx_sum']."'";
                $_wxcount[]="'".$_val['wx_count']."'";
                $_alisum[]="'".$_val['ali_sum']."'";
                $_alicount[]="'".$_val['ali_count']."'";
                $_cardsum[]="'".$_val['card_sum']."'";
                $_cardcount[]="'".$_val['card_count']."'";
                $_mch[]="'".$_val['mch']."'";
                $_day[]="'".$_val['day']."'";
            }


            // dump($OutData);

            $assign=array(
                'Go'=>array(
                    'sum'=>round($GoSum,2),
                    'count'=>$GoCount,
                    'wxsum'=>round($GoWxSum,2),
                    'wxcount'=>$GoWxCount,
                    'alisum'=>round($GoAliSum,2),
                    'alicount'=>$GoAliCount,
                    'card_sum'=>round($GoCardSum,2),
                    'card_count'=>$GoCardCount,
                ),
                'Day'=>array(
                    'sum'=>round($J_Sum,2),
                    'count'=>$J_Count,
                    'wxsum'=>round($J_WxSum,2),
                    'wxcount'=>$J_WxCount,
                    'alisum'=>round($J_AliSum,2),
                    'alicount'=>$J_AliCount,
                    'card_sum'=>round($J_CardSum,2),
                    'card_count'=>$J_CardCount,
                ),
                'To'=>array(
                    'sum'=>round($To_sum,2),
                    'count'=>$To_count,
                    'wxsum'=>round($ToWxSum,2),
                    'wxcount'=>$ToWxCount,
                    'alisum'=>round($ToAliSum,2),
                    'alicount'=>$ToAliCount,
                    'card_sum'=>round($ToCardSum,2),
                    'card_count'=>$ToCardCount,
                    'mch'=>$ToMch,
                ),
                'Mch'=>array(
                    'live'=>count($Mch),
                    'bed'=>$ToMch-count($Mch),
                ),
                'Mon'=>array(
                    'sum'=>implode(',', $_sum),
                    'count'=>implode(',', $_count),
                    'wxsum'=>implode(',', $_wxsum),
                    'wxcount'=>implode(',', $_wxcount),
                    'alisum'=>implode(',', $_alisum),
                    'alicount'=>implode(',', $_alicount),
                    'card_sum'=>implode(',', $_cardsum),
                    'card_count'=>implode(',', $_cardcount),
                    'mch'=>implode(',', $_mch),
                    'day'=>implode(',', $_day)
                )
            );


            $Static=M('DataStatistics');
            $sys_data=array(
                'type'=>'admin',
                'day_data'=>json_encode($assign['Day']), //今日
                'terday_data'=>json_encode($assign['Go']), //昨日
                'count_data'=>json_encode($assign['To']),//总数据
                'mch_data'=>json_encode($assign['Mch']), //商户统计
                'week_data'=>json_encode($assign['Mon']),//近七天
                'etime'=>date('Y-m-d H:i:s'),
                'domain_auth'=>$do['web_authcode']
            );
            $cache_admin='admin_'.$do['web_authcode'];
            S($cache_admin,$sys_data);
//            $result=$Static->where(array('type'=>'admin','domain_auth'=>$do['web_authcode']))->count();
//            if($result){
//                $Static->where(array('type'=>'admin','domain_auth'=>$do['web_authcode']))->save($sys_data);
//            }else{
//                $Static->add($sys_data);
//            }
            //dump($sys_data);
            //dump($assign);
        }

    }

    

}