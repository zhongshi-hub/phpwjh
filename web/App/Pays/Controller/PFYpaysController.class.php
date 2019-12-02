<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
#
#富友通道

class PFYpaysController extends Alleys_initBaseController
{
    public function _initialize()
    {
       // parent::_initialize();
        $this->mch_api=json_decode($this->Mdata['api_rel'],true);
        #机构参数
        $this->inst=array(
          'no'=>'30600001',
          'key'=>'780e821e750b43b3b24a74a1c8a50abf',
        );
        #网关
        //$api_url='http://test.lcsw.cn:8045/lcsw/';
        $api_url='https://pay.lcsw.cn/lcsw/';
        $this->Api=array(
            'mch_in'=>$api_url.'/merchant/100/add',
            'mch_terminal'=>$api_url.'/terminal/100/add',
            'mch_up'=>$api_url.'/merchant/100/update',
            'wx'=>$api_url.'/pay/100/jspay',
        );

        #订单号
        $this->order_id=$this->orderNum;
        if(fy_tx_time() && $this->data['total'] >= 10) { #只有在这个时间内 09:00-22:00
            $this->tx_type='D0';
        }else{
            $this->tx_type='T1';
        }


    }


    #微信支付
    public function pay_wx_jsapi(){
         $data=array(
           'pay_ver'=>100,
           'pay_type'=>'010',
           'service_id'=>'012',
           'merchant_no'=>$this->Mdata['mch_id'],
           'terminal_id'=>$this->mch_api['terminal_id'],
           'terminal_trace'=>$this->order_id,
           'terminal_time'=>date('YmdHis'),
           'total_fee'=>$this->data['total'] * 100,#订单金额 单位为分
         );
        $data['key_sign']=self::pay_sign($data);
        $data['open_id']=$this->data['openid'];
        $data['order_body']=$this->Sdata['name'];
        $data['notify_url']=$this->NotifyUrl.'fy_notify_url';
        $res = ccb_curl_calls($this->Api['wx'], $data);
        $res=json_decode($res,true);
        //rwlog('test_fy',$res);
        if($res['return_code']=='01'&&$res['result_code']=='01'){
            $array=array(
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($data),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'wx_jsapi',
                'trade_type'=>'010',
                'out_trade_no'=>$res['terminal_trace'],
                'out_transaction_id'=>$res['out_trade_no'],
                'body'=>$this->Sdata['name'],
                'total_fee'=>$res['total_fee']/100, //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'sub_openid'=>$this->data['openid'],
                'type'=> $this->tx_type,
                'alleys'=>'FYpays',
                'domain_auth'=>domain_auth(),
                'is_raw'=>1,
            );
            $rel=M('mch_orders')->add($array);
            if($rel) {
                $pay_data=array(
                    'msg'=>'订单创建成功',
                    'type'=>'js',
                    'pay_info'=>array(
                        'appId'=>$res['appId'],
                        'timeStamp'=>$res['timeStamp'],
                        'nonceStr'=>$res['nonceStr'],
                        'package'=>$res['package_str'],
                        'signType'=>$res['signType'],
                        'paySign'=>$res['paySign'],
                    ),
                    'out_trade_no'=>$data['terminal_trace'],
                    'result'=>'/Api/fy_result/out_trade_no/'.$data['terminal_trace'],
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else {
            $this->error($res['return_msg']);
        }


    }



    #微信支付
    public function pay_ali_jsapi(){
        $data=array(
            'pay_ver'=>100,
            'pay_type'=>'020',
            'service_id'=>'012',
            'merchant_no'=>$this->Mdata['mch_id'],
            'terminal_id'=>$this->mch_api['terminal_id'],
            'terminal_trace'=>$this->order_id,
            'terminal_time'=>date('YmdHis'),
            'total_fee'=>$this->data['total'] * 100,#订单金额 单位为分
        );
        $data['key_sign']=self::pay_sign($data);
        $data['open_id']=$this->data['openid'];
        $data['order_body']=$this->Sdata['name'];
        $data['notify_url']=$this->NotifyUrl.'fy_notify_url';
        $res = ccb_curl_calls($this->Api['wx'], $data);
        $res=json_decode($res,true);
        //rwlog('test_ali_fy',$data);
        //rwlog('test_ali_res',$res);
        if($res['return_code']=='01'&&$res['result_code']=='01'){
            $array=array(
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($data),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'ali_jsapi',
                'trade_type'=>'020',
                'out_trade_no'=>$res['terminal_trace'],
                'out_transaction_id'=>$res['out_trade_no'],
                'body'=>$this->Sdata['name'],
                'total_fee'=>$res['total_fee']/100, //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'sub_openid'=>$this->data['openid'],
                'type'=> $this->tx_type,
                'alleys'=>'FYpays',
                'domain_auth'=>domain_auth(),
                'is_raw'=>1,
            );
            $rel=M('mch_orders')->add($array);
            if($rel) {
                $pay_data = array(
                    'msg' => '订单创建成功',
                    'type' => 'js',
                    'pay_info' => array(
                        'tradeNO' => $res['ali_trade_no']
                    ),
                    'out_trade_no' => $res['terminal_trace'],
                    'result' => '/Api/fy_result/out_trade_no/' . $res['terminal_trace'],
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else {
            $this->error($res['return_msg']);
        }
    }



    #进件接口
    public function mch_in(){
        $parent=fy_parent();
        if(!$parent){
            $this->error('未配置FY集团商户号,获取失败!');
        }else {
            $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
            $trace_no = create_uuid();
            #判断是否上传门店景
            /*if (!$alleys['mch_img_m1']) {
                $this->error('商户信息缺少门头照片,请编辑上传门头照片后再进件!');
            }
            if (!$alleys['mch_img_m2']) {
                $this->error('商户信息缺少门店内景照片,请编辑上传门店内景照片后再进件!');
            }*/
            if (!$alleys['mch_img_z']) {
                $this->error('商户信息缺少身份证正面照片,请编辑上传身份证正面照片后再进件!');
            }
            if (!$alleys['mch_img_p']) {
                $this->error('商户信息缺少身份证反面照片,请编辑上传身份证反面照片后再进件!');
            }
            if (!$alleys['mch_img_s']) {
                $this->error('商户信息缺少手持照片,请编辑上传手持照片后再进件!');
            }
            switch ($alleys['mch_bus_type']) {
                case '无营业执照':
                    $mch_name = $alleys['mch_name'];
                    $business_code = '158';
                    break;
                default:
                    $mch_name = $alleys['qy_name'];
                    $business_code = $alleys['mch_industry'];
                    break;
            }
            #如果是有营业执照 判断
            if ($alleys['mch_bus_type'] == '有营业执照') {
                if (!$alleys['mch_img_yyzz']) {
                    $this->error('企业类型 必须包含营业执照附件,请上传营业执照后再进件!');
                }
            }
            #只能为个人账户
            if ($alleys['mch_bank_type'] != '个人账户') {
                $this->error('当前通道不支持对公户结算,请更换个人(法人)账户后进件!');
            }
            $data = array(
                'inst_no' => $this->inst['no'], #机构编号，扫呗分配
                'trace_no' => $trace_no, #请求流水号，不带“_”的uuid
                'merchant_type' => '2', #创建商户类型，1普通商户，2二级商户
                'merchant_name' => $alleys['mch_citys'].$mch_name,    #商户名称，扫呗系统全局唯一不可重复
                'merchant_alias' => $alleys['mch_citys'].$mch_name, #商户简称
                'merchant_company' => $alleys['mch_citys'].$mch_name,    //经营实体名称，须与营业执照名称保持一致
                'merchant_province' => $alleys['mch_provice'],    //所在省
                'merchant_province_code' => fy_area(1, $alleys['mch_provice']), //省编码
                'merchant_city' => $alleys['mch_citys'],//所在市
                'merchant_city_code' => fy_area(2, $alleys['mch_citys'], $alleys['mch_provice']),//市编码
                'merchant_address' => $alleys['mch_address'],//详细地址
                'merchant_person' => $alleys['mch_card_name'], //负责人姓名
                'merchant_phone' => $alleys['mch_tel'],//负责人电话
                'merchant_email' => fy_parent('email'),//负责人邮箱
                'merchant_id_no' => $alleys['mch_card_id'],//证件号码
                'merchant_id_expire' => '29991231',//证件有效期，格式YYYYMMDD，长期填写29991231
                'business_name' => Industrid($business_code),//行业类目名称
                'business_code' => $business_code,//行业类目编码，由扫呗技术支持提供表格
                'account_type' => '2',//账户类型，1对公，2对私
                'account_name' => $alleys['mch_bank_name'],// 开户名
                'account_no' => $alleys['mch_bank_cid'], //开户号
                'bank_name' => reload_banks($alleys['mch_linkbnk']),//开户支行
                'bank_no' => $alleys['mch_linkbnk'],//开户支行联行号，由扫呗技术支持提供表格
                'settle_type' => '1',//清算类型：1自动结算；2手动结算，注：当前固定为自动结算
                'settle_amount' => '1',// 自动清算金额（单位分），清算类型为自动清算时有效，指帐户余额达到此值才清算。注：当前固定值为1分
            );
            $data['key_sign'] = self::sign($data); //签名
            $data['account_phone'] = $alleys['mch_bank_tel'];//开户绑定手机号
            $data['no_credit'] = '0';//限制信用卡使用,0不限制，1限制
            $data['merchant_id_type'] = '0';//证件类型，0身份证，1护照，2军官证，3士兵证，4回乡证，6户口本，7其他
            $data['merchant_county'] = $alleys['mch_district'];//所在区县
            $data['merchant_county_code'] = fy_area(3, $alleys['mch_district'], $alleys['mch_citys']);//所在区县编码
            $data['rate_code'] = self::mch_rate_data($alleys['rate']);//支付费率代码，默认千分之六，取值范围见下表
            $data['timely_status'] = '1'; //D0开通状态，默认0为不开通，1为开通（开通时开户绑定手机号码必填）
            $data['timely_code'] = 'T003005';  //D0费率代码，取值范围见下表
            $data['img_license'] = _img_data_url($alleys['mch_img_yyzz']);  //营业执照合
            $data['img_idcard_a'] = _img_data_url($alleys['mch_img_z']); //负责人身份证正面照片
            $data['img_idcard_b'] = _img_data_url($alleys['mch_img_p']); //负责人身份证反面照片
            $data['img_bankcard_a'] = _img_data_url($alleys['mch_img_bank']); //银行卡照片
            $data['img_logo'] = _img_data_url($alleys['mch_img_m1']); //商户门头
            $data['img_indoor'] = _img_data_url($alleys['mch_img_m2']); //商户内景
            $data['img_idcard_holding'] = _img_data_url($alleys['mch_img_s']); //手持身份证照片
            $data['notify_url'] = $this->NotifyUrl . 'fy_mch_notify'; //审核状态异步
            $data['parent_no'] = $parent; //所属大商户商户号

            //dump($data);
            $res = ccb_curl_calls($this->Api['mch_in'], $data);
            $res = json_decode($res, true);
            if ($res['return_code'] == '01') {
                if($res['result_code'] == '01') {
                    $save = array(
                        'mch_id' => $res['merchant_no'],
                        'load_status' => 0,
                        'status' => 1,
                        'codes' => serialize($res),
                        'api_rel' => self::terminal($res['merchant_no']),
                    );
                    M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                    $this->success('商户进件成功!');
                }else{
                    $this->error('商户进件失败! 提示:'.$res['return_msg']);
                }
            } else {
                $this->error(json_encode($res));
            }
        }
    }

    #商户状态查询
    public function mch_status(){
     
    }


    #商户信息更新
    public function mch_updata(){
            $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
            $trace_no = create_uuid();
            #判断是否上传门店景
            if (!$alleys['mch_img_m1']) {
                $this->error('商户信息缺少门头照片,请编辑上传门头照片后再进件!');
            }
            if (!$alleys['mch_img_m2']) {
                $this->error('商户信息缺少门店内景照片,请编辑上传门店内景照片后再进件!');
            }
            switch ($alleys['mch_bus_type']) {
                case '无营业执照':
                    $mch_name = $alleys['mch_name'];
                    $business_code = '158';
                    break;
                default:
                    $mch_name = $alleys['qy_name'];
                    $business_code = $alleys['mch_industry'];
                    break;
            }
            #如果是有营业执照 判断
            if ($alleys['mch_bus_type'] == '有营业执照') {
                if (!$alleys['mch_img_yyzz']) {
                    $this->error('企业类型 必须包含营业执照附件,请上传营业执照后再进件!');
                }
            }
            #只能为个人账户
            if ($alleys['mch_bank_type'] != '个人账户') {
                $this->error('当前通道不支持对公户结算,请更换个人(法人)账户后进件!');
            }
            $data = array(
                'inst_no' => $this->inst['no'], #机构编号，扫呗分配
                'trace_no' => $trace_no, #请求流水号，不带“_”的uuid
                'merchant_no' => $alleys['mch_id'], #创建商户类型，1普通商户，2二级商户
                'merchant_name' => $alleys['mch_citys'].$mch_name,    #商户名称，扫呗系统全局唯一不可重复
                'merchant_province' => $alleys['mch_provice'],    //所在省
                'merchant_province_code' => fy_area(1, $alleys['mch_provice']), //省编码
                'merchant_city' => $alleys['mch_citys'],//所在市
                'merchant_city_code' => fy_area(2, $alleys['mch_citys'], $alleys['mch_provice']),//市编码
                'merchant_address' => $alleys['mch_address'],//详细地址
                'merchant_person' => $alleys['mch_card_name'], //负责人姓名
                'merchant_phone' => $alleys['mch_tel'],//负责人电话
                'merchant_email' => fy_parent('email'),//负责人邮箱
                'merchant_id_no' => $alleys['mch_card_id'],//证件号码
                'merchant_id_expire' => '29991231',//证件有效期，格式YYYYMMDD，长期填写29991231
                'business_name' => Industrid($business_code),//行业类目名称
                'business_code' => $business_code,//行业类目编码，由扫呗技术支持提供表格
                'account_type' => '2',//账户类型，1对公，2对私
                'account_name' => $alleys['mch_bank_name'],// 开户名
                'account_no' => $alleys['mch_bank_cid'], //开户号
                'bank_name' => reload_banks($alleys['mch_linkbnk']),//开户支行
                'bank_no' => $alleys['mch_linkbnk'],//开户支行联行号，由扫呗技术支持提供表格
                'settle_type' => '1',//清算类型：1自动结算；2手动结算，注：当前固定为自动结算
                'settle_amount' => '1',// 自动清算金额（单位分），清算类型为自动清算时有效，指帐户余额达到此值才清算。注：当前固定值为1分
            );
            $data['key_sign'] = self::sign($data); //签名
            $data['account_phone'] = $alleys['mch_bank_tel'];//开户绑定手机号
            $data['no_credit'] = '0';//限制信用卡使用,0不限制，1限制
            $data['merchant_id_type'] = '0';//证件类型，0身份证，1护照，2军官证，3士兵证，4回乡证，6户口本，7其他
            $data['merchant_county'] = $alleys['mch_district'];//所在区县
            $data['merchant_county_code'] = fy_area(3, $alleys['mch_district'], $alleys['mch_citys']);//所在区县编码
            $data['rate_code'] = self::mch_rate_data($alleys['rate']);//支付费率代码，默认千分之六，取值范围见下表
            $data['timely_status'] = '1'; //D0开通状态，默认0为不开通，1为开通（开通时开户绑定手机号码必填）
            $data['timely_code'] = 'T003005';  //D0费率代码，取值范围见下表
            $data['img_license'] = _img_data_url($alleys['mch_img_yyzz']);  //营业执照合
            $data['img_idcard_a'] = _img_data_url($alleys['mch_img_z']); //负责人身份证正面照片
            $data['img_idcard_b'] = _img_data_url($alleys['mch_img_p']); //负责人身份证反面照片
            $data['img_bankcard_a'] = _img_data_url($alleys['mch_img_bank']); //银行卡照片
            $data['img_logo'] = _img_data_url($alleys['mch_img_m1']); //商户门头
            $data['img_indoor'] = _img_data_url($alleys['mch_img_m2']); //商户内景
            $data['img_idcard_holding'] = _img_data_url($alleys['mch_img_s']); //手持身份证照片
            $data['notify_url'] = $this->NotifyUrl . 'fy_mch_notify'; //审核状态异步

            //rwlog('up_fy',$data);
            $res = ccb_curl_calls($this->Api['mch_up'], $data);
            $res = json_decode($res, true);
            //rwlog('up_fy',$res);
            if ($res['return_code'] == '01') {
                if($res['result_code'] == '01') {
                    $save = array(
                        'mch_id' => $res['merchant_no'],
                        'load_status' => 0,
                        'status' => 1,
                        'codes' => serialize($res),
                    );
                    M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                    $this->success('商户信息更新进件成功!');
                }else{
                    $this->error('商户信息更新进件失败! 提示:'.$res['return_msg']);
                }
            } else {
                $this->error(json_encode($res));
            }
    }

    #注册终端
    public function terminal($merchant_no){
        $trace_no=create_uuid();
        $data=array(
            'inst_no'=>$this->inst['no'], #机构编号，扫呗分配
            'trace_no'=>$trace_no, #请求流水号，不带“_”的uuid
            'merchant_no'=>$merchant_no,
        );
        $data['key_sign']=self::sign($data); //签名
        $res = ccb_curl_calls($this->Api['mch_terminal'], $data);
        $res=json_decode($res,true);
        if($res['return_code']=='01'&&$res['result_code']=='01'){
            $data=array(
                'terminal_id'=>$res['terminal_id'],
                'access_token'=>$res['access_token'],
            );
        }else{
            $data=$res;
        }
        return json_encode($data);
    }



    #费率信息
    public function mch_rate_data($data){
        $res=M('RateData')->where(array('rate'=>$data))->getField('fy_rate');
        return $res;
    }
    #进件签名
    public function sign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "artif_nm" != $k && "key_sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=$signPars.'key='.$this->inst['key'];
        $sign = md5($sign_data);
        return $sign;
    }
    #支付签名
    public function pay_sign($data){
        $signPars = "";
       // ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "access_token" != $k && "key_sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=$signPars.'access_token='.$this->mch_api['access_token'];
        $sign = md5($sign_data);
        return $sign;
    }

}