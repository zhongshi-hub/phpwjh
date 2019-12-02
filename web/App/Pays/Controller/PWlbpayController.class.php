<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class PWlbpayController extends Alleys_initBaseController {


    #微信公众号支付
    public function pay_wx_jsapi(){
        /*if(get_curr_time_section()==0){
            //$this->error('时间:00:00-06:00 微信支付维护!请用支付宝扫码付款!');
            $this->error('时间:00:00-06:00 今晚系统维护!给您带来不便!敬请谅解!');
        }*/
        $P=$this->data;
        $total=$P['total'];
        $data=array(
            'trxType'=>'WX_SCANCODE_JSAPI',#接口类型
            'merchantNo'=>$this->merchantNo,#商户编号
            'orderNum'=>$this->orderNum,#商户订单号
            'amount'=>$total,#金额 元
            'goodsName'=>$this->Sdata['name'],#订单描述
            //'callbackUrl'=>$this->call_url,#页面回调
            'serverCallbackUrl'=>$this->notify_url,#异步回调
            'orderIp'=>Get_Clienti_Ips(),#用户IP
            'encrypt'=>'T1',#T0/T1标识，若此项为T0，对应的10,11,12,13必填
        );
        $data['sign']=$this->OrderSign($data);
        data_log('wlb_t1_wxdata',$data);
        $res=$this->curl_res($this->Api_Url['WX_Common'],$data,1);
        data_log('wlb_t1_wxres',$res);
        if($res['retCode']=='0000'){//处理成功
            $array=array(
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($data),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->merchantNo,
                'service'=>'wx_jsapi',
                'out_trade_no'=>$data['orderNum'],
                'body'=>$data['goodsName'],
                'total_fee'=>$data['amount'], //存数据库按照分进行统计
                'mch_create_ip'=>$data['orderIp'],
                'sub_openid'=>$this->data['openid'],
                'type'=>'T1',
                'alleys'=>'Wlbpay',
                'domain_auth'=>domain_auth()
            );

            $rel=M('mch_orders')->add($array);
            if($rel){
                $pay_data=array(
                    'msg'=>'订单创建成功',
                    'type'=>'hurl',
                    'localurl'=>$res['qrCode'],
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else{
            $this->error('错误代码:'.$res['retCode'].'-提示:'.$res['msg']);
        }
    }



    #支付宝H5支付
    public function pay_ali_jsapi(){
        /*if(get_curr_time_section()==0){
            //$this->error('时间:00:00-06:00 微信支付维护!请用支付宝扫码付款!');
            $this->error('时间:00:00-06:00 今晚系统维护!给您带来不便!敬请谅解!');
        }*/
        $P=$this->data;
        $total=$P['total'];
        $data=array(
            'trxType'=>'Alipay_SCANCODE_JSAPI',#接口类型
            'merchantNo'=>$this->merchantNo,#商户编号
            'orderNum'=>$this->orderNum,#商户订单号
            'amount'=>$total,#金额 元
            'goodsName'=>$this->Sdata['name'],#订单描述
            'serverCallbackUrl'=>$this->notify_url,#异步回调
            'orderIp'=>Get_Clienti_Ips(),#用户IP
            'encrypt'=>'T1',#T0/T1标识，若此项为T0，对应的10,11,12,13必填
        );
        $data['sign']=$this->OrderSign($data);
        data_log('wlb_t1_alidata',$data);
        $res=$this->curl_res($this->Api_Url['ALI_Common'],$data,1);
        data_log('wlb_t1_alires',$res);
        if($res['retCode']=='0000'){//处理成功
            $array=array(
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($data),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->merchantNo,
                'service'=>'ali_jsapi',
                'out_trade_no'=>$data['orderNum'],
                'body'=>$data['goodsName'],
                'total_fee'=>$data['amount'], //存数据库按照分进行统计
                'mch_create_ip'=>$data['orderIp'],
                'sub_openid'=>$this->data['openid'],
                'type'=>'T1',
                'alleys'=>'Wlbpay',
                'domain_auth'=>domain_auth()
            );
            $rel=M('mch_orders')->add($array);
            if($rel){
                $pay_data=array(
                    'msg'=>'订单创建成功',
                    'type'=>'hurl',
                    'localurl'=>$res['r9_qrCode'],
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else{
            $this->error('错误代码:'.$res['retCode'].'-提示:'.$res['msg']);
        }
    }


    #进件操作
    public function mch_in(){
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>'Wlbpays'))->find();
        $alley=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>'Wlbpay'))->find();
        if(!$alleys['rate']){
            $this->error('系统进行二次监测,此商户的(WLB)通道D0通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        }elseif(!$alley['rate']){
            $this->error('系统进行二次监测,此商户的(WLB)通道T1通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        }else{
            $wxType1=array('203','208','210','158','204','205');
            $wxType=rand_one($wxType1);
            #支付宝经营类目ID
            $alipayType1=array('2015050700000022','2015050700000037','2015091000058486','2015091000060134','2015062600009243');
            $alipayType= rand_one($alipayType1);

            $data=array(
                'channelName'=>$this->Cname,//渠道名称
                'channelNo'=>$this->Cno,//渠道编码
                'merchantName'=>$alleys['mch_name'],//商户名称
                'merchantBillName'=>$alleys['mch_name'],//签购单显示名称
                'installProvince'=>$alleys['mch_provice'],//安装归属省
                'installCity'=>$alleys['mch_citys'],//安装归属市
                'installCounty'=>$alleys['mch_district'],//安装归属县（区）
                'operateAddress'=>$alleys['mch_address'],//经营地址
                'merchantType'=>'PERSON',//商户类型 ENTERPRISE -企业商户 INSTITUTION -事业单位商户 INDIVIDUALBISS -个体工商户  PERSON -个人商户
                'businessLicense'=>'',//营业执照号码
                'legalPersonName'=>$alleys['mch_card_name'],//法人姓名
                'legalPersonID'=>$alleys['mch_card_id'],//法人身份证号
                'merchantPersonName'=>$alleys['mch_card_name'],  //商户联系人姓名
                'merchantPersonPhone'=>$alleys['mch_tel'],//商户联系人电话
                'wxType'=>$wxType,//微信经营类目
                'wxT1Fee'=>$alley['rate']/1000,//微信商户手T1续费
                'wxT0Fee'=>$alleys['rate']/1000,//微信商户手T0续费
                'alipayType'=>$alipayType,//支付宝经营类目
                'alipayT1Fee'=>$alley['rate']/1000,//支付宝商户手T1续费
                'alipayT0Fee'=>$alleys['rate']/1000,//支付宝商户手T0续费
                'bankType'=>'TOPRIVATE',//结算账户性质 对公-TOPUBLIC 对私-TOPRIVATE
                'accountName'=>$alleys['mch_bank_name'],//开户人姓名
                //'accountNo'=>'6227002430160277786',//开户账号
                'accountNo'=>self::encode($alleys['mch_bank_cid']),//开户账号
                'bankName'=>reload_bank($alleys['mch_bank_list']),//开户银行名（大行全称）
                'bankProv'=>$alleys['mch_bank_provice'],//开户行省
                'bankCity'=>$alleys['mch_bank_citys'],//开户行市
                'bankBranch'=>reload_banks($alleys['mch_linkbnk']),//开户银行名称（精确到支行）
                'bankCode'=>$alleys['mch_linkbnk'],//联行号
                //'creditCardNo'=>'',//信用卡认证
            );
            $data['sign']=$this->signs($data);
            $res=$this->curl_res($this->Api_Url['M_In'],$data);
            //rwlog('mch_in',$res);
            if($res['respCode']=='0000'){
                $save = array(
                    'mch_id' =>$res['merchantNo'],
                    'load_status'=>1,
                    'status'=>1,
                    'api_rel'=>serialize($res),
                );
                M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>'Wlbpays'))->save($save);
                M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>'Wlbpay'))->save($save);
                #配置默认支付通道为D0
                if($this->data['default_alleys']){
                    M('MchSeller')->where(array('id'=>$this->data['cid']))->save(array('wx_alleys'=>$this->data['default_alleys'],'ali_alleys'=>$this->data['default_alleys']));
                }
                $this->success('商户进件成功!');
            }else{
                if(empty($res['respCode'])){
                    $this->error('进件失败!详情:' . serialize($res));
                }else {
                    $this->error('进件失败!详情:' . $res['respCode'] . '-' . $res['respMsg']);
                }
            }
        }
    }


}