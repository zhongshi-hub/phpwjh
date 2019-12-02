<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 测试项目
 */
class MchAliController extends HomeBaseController
{
    public function _initialize()
    {
        parent::_initialize();


        //require('./alipay_sdk/AlipayAop.class.php');

        Vendor('alipay_sdk.AlipayAop');

    }

    public function index(){
        echo '111';
    }


    public function ali_user(){

        $oid=date('YmdHis').rand('000000','999999');
        $Ali_Data=array(
            'osn'=>$oid, #订单号
            'payee_account'=>'18637162652', #收款账户
            'payee_real_name'=>'陈存龙', #收款人姓名
            'payer_show_name'=>'2月分润',#付款显示名
            'amount'=>'0.1', #金额
            'remark'=>'编号:123',#备注
            'app_id'=>'2017050507132270' #申请开发中APPID
        );
        $Ali_Aop= new \AlipayAop();
        $msg = $Ali_Aop->Alipay_Funds($Ali_Data);

        dump($msg);

    }

    
}