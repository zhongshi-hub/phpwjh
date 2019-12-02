<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Base基类控制器
 */
class IndexController extends BaseController{




    public function index(){
        //今日汇总数据
        $dayData=$this->getDay();
        //今七日交易总额
        $weekSum=$this->getWeekSum();
        //今七日交易笔数
        $weekCount=$this->getWeekSum(0);
        $data=[
            'dayData'=>$dayData,
            'weekSum'=>json_encode($weekSum,JSON_UNESCAPED_SLASHES),
            'weekCount'=>json_encode($weekCount,JSON_UNESCAPED_SLASHES)
        ];
        $this->assign($data);
        //dump($data);
        $this->display();
    }


    /**
     * 今日数据汇总
     * @return array
     */
    public function getDay(){
        $db=M('MchOrders');
        $map['status']=1;
        $map['mid']=session('mp.id');
        $sTime=date("Y-m-d");
        $eTime=date('Y-m-d',strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($sTime) . "') AND ( `time_end` < '" . strtotime($eTime) . "') ";
        #统计今日总交易额
        $sum=$db->where($map)->where($map)->sum('total_fee');
        #今日总笔数
        $count=$db->where($map)->where($map)->count();
        #今日微信总额
        $wxSum=$db->where($map)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
        #今日支付宝总额
        $aliSum=$db->where($map)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
        $data=[
           'sum'=>round($sum,2),
           'count'=>$count,
           'wx'=>round($wxSum,2),
           'ali'=>round($aliSum,2)
        ];
        return $data;
    }


    /**
     * 近七日交易信息
     * @param int $type  1交易额 0交易笔数
     * @return array
     */
    public function getWeekSum($type=1){
        $db=M('MchOrders');
        $map['status']=1;
        $map['mid']=session('mp.id');
        $date=array();
        for($i=7;$i>=1;$i--){
            $date[]=date("Y-m-d",strtotime("-$i day"));
        }
        $outData=[];
        foreach ($date as $key=>$val) {
            $SDate=strtotime($val);
            $SDates=date('Y/m/d', $SDate);
            $EDate = date('Y/m/d', strtotime("$SDates +1 day"));
            $EDates=strtotime($EDate);
            #筛选
            $map['_string'] = "(`time_end`> '" . $SDate. "') AND ( `time_end` < '" . $EDates. "') ";
            if($type==1){
                #微信交易额
                $_WxSum=$db->where($map)->where(array('service'=>array('like','wx_%')))->sum('total_fee');
                #支付宝交易额
                $_AliSum=$db->where($map)->where(array('service'=>array('like','ali_%')))->sum('total_fee');
                $outData[]=array(
                    'wx'=>round($_WxSum,2),
                    'ali'=>round($_AliSum,2),
                    'day'=>date('m/d', $SDate),
                );
            }else{
                #微信笔数
                $_WxCount=$db->where($map)->where(array('service'=>array('like','wx_%')))->count();
                #支付宝笔数
                $_AliCount=$db->where($map)->where(array('service'=>array('like','ali_%')))->count();
                $outData[]=array(
                    'wx'=>$_WxCount,
                    'ali'=>$_AliCount,
                    'day'=>date('m-d', $SDate),
                );
            }

        }
        return $outData;
    }
    

}