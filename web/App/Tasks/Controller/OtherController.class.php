<?php

namespace Tasks\Controller;
use Think\Controller;
#分润日清算数据
class OtherController extends Controller
{


    public function updata_type(){
       /* $db=M('MchSeller');
        $where['mch_bus_type']='有营业执照';
        $res=$db->where($where)->save(array('mch_bus_type'=>'企业'));
        dump($res);
        dump('1');*/

    }


    /**/
   /* public function bos_alley(){
        #查询所有商户通道
        $alleys='Bospay';
        $map['wx_alleys']=array('neq',$alleys);
        $map['ali_alleys']=array('neq',$alleys);
        $map['_logic']='or';
        $res=M('MchSeller')->where($map)->select();
        $echo=array();
        foreach ($res as $v){
            $where['cid']=$v['id'];
            $where['alleys_type']=$alleys;
            $where['mch_id']=array('EXP','IS NOT NULL');
            $alley=M('MchSellerAlleys')->where($where)->find();
            if($alley){
                //$echo[]=$alley;
                $save=array(
                  'wx_alleys'=>$alleys,
                  'ali_alleys'=>$alleys,
                );
                M('MchSeller')->where(array('id'=>$v['id']))->save($save);
            }
        }
        dump(count($echo));
        dump(count($res));

    }*/


    /*
     * 查询上海银行
     * 有交易的
     * */

    public  function bos_mch_id(){
        $where=array(
          'alleys'=>'Bospay'
        );
        $res=M('MchOrders')->where($where)->Distinct(true)->getField('mch_id',true);
        $res=implode('|',$res);
        dump($res);
    }

    /*
    * DEV: 陈存龙 CCL
    * 计划清除交易订单 未支付的订单数据
    * 按照时间计算  清除今天10天之前的数据
    * 计划任务为每月执行一次  没月月初1号执行
    * 如:2017-02-01 执行  只清除 2017-01-20号之前的  以此类推
    * 最后更新日期: 2017-7-23 19:00
    */
    public function del_order(){
        $times=strtotime(date("Y-m-d",strtotime("-5 day")));
        $where['status']=0; //状态为未支付
        $where['createtime']=array('LT',$times); //时间
        $res=M('MchOrders')->where($where)->delete();
        dump($res);
        //$res=M('mcash_order')->where($where)->delete();
        //增加记录

    }

    #修改费率
    public function bos_rate(){
        /*$res=M('mch_seller_alleys')->where(array('domain_auth'=>'gBBA3tgsw0','alleys_type'=>'Bospay','rate'=>6))->save(array('rate'=>'3.8'));
        dump($res);*/
    }


    #盛付通通道-修改费率 T1生效
    public function srate(){
        $times=date("Ymd",strtotime("-1 day"));
        $map['date']=array('eq',$times);
        $map['status']=array('neq',1);
        $res_data=M('AlterRateLog')->where($map)->select();
        foreach ($res_data as $v){
            $where=array(
                'alleys_type'=>$v['alleys'],
                'cid'=>$v['cid'],
                'domain_auth'=>$v['domain_auth']
            );
            $rel=M('MchSellerAlleys')->where($where)->save(array('rate'=>$v['new_rate']));
            if($rel){
                M('AlterRateLog')->where(array('id'=>$v['id']))->save(array('e_time'=>date('Y-m-d H:i:s'),'status'=>1));
                echo 'Success';
            }else{
                echo 'ERROR';
            }
        }
    }


}