<?php
namespace Pays\Controller;

use Pays\Controller\Alleys_CardinitBaseController;

class CQcardController extends Alleys_CardinitBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        //$this->error('今日通道方垫资额度已用完!请明天使用!');

    }


    #新版跳转到获取验证码界面
    public function pay_submit_data()
    {
        $quick_data = Xencode(json_encode($this->data));
        $url = U('Mch/Index/quick_verify', array('quick_data' => $quick_data));
        if ($quick_data) {
            $this->success('参数通信成功', $url);
        } else {
            $this->error('参数获取失败');
        }
        //dump();
    }

    #确认支付
    public function pay_submit()
    {
        if (!$this->data['verify']) {
            $this->error('请输入验证码');
        }
        if (strlen($this->data['verify']) < 6) {
            $this->error('验证码错误!验证码为6位数字');
        }
        if (!$this->data['order_id']) {
            $this->error('未获取到订单信息,请先获取验证码!');
        }

        $arr = array(
            "requestNo" => date('YmdHis') . rand('1111', '9999'),
            "version" => "V1.0",
            "productId" => "0103",
            "transId" => "06",
            "merNo" => $this->merNo,
            "orderDate" => date('Ymd'),
            "orderNo" => $this->data['order_id'],
            //"returnUrl"=>$this->NotifyUrl.'q_returnUrl',
            "notifyUrl" => $this->NotifyUrl . 'q_notifyUrl',
            'veriCode' => $this->data['verify'],
            "transAmt" => $this->data['total'] * 100,#订单金额 单位为分
            "commodityName" => $this->Sdata['name'],
            "phoneNo" => $this->bank_data['phone'],
            "customerName" => $this->Mdata['mch_card_name'],
            "cerdType" => "01",
            "cerdId" => $this->Mdata['mch_card_id'],
            "acctNo" => $this->bank_data['card'],
            "cvn2" => $this->bank_data['cvn'],
            "expDate" => substr($this->bank_data['date'], -4)
        );
        $arr['signature'] = self::card_sign($arr);
        //rwlog('card_submit', $arr);
        //rwlog('card_b',$arr);
        //rwlog('card_c',http_build_query($arr));
        $res = card_curl_post($this->ApiUrl, http_build_query($arr));
        parse_str($res, $res);
        $returnUrl = "http://" . $_SERVER['HTTP_HOST'] . '/CardApi/return_url?oid=' . $this->data['order_id'] . '&alley=Q1';
        if ($res['respCode'] == '0000') { #表示待支付 验证码下发成功
            $where = array(
                'out_trade_no' => $res['orderNo'],
                'domain_auth' => domain_auth(),
            );
            $save = array(
                'rel' => serialize($res),
                'total_fee' => $res['transAmt'] / 100,
                'status' => 1,
                'time_end' => time(),
            );
            $rel = M('mch_orders')->where($where)->save($save);
            if ($rel) {
                $this->success('支付成功', $returnUrl);
            } else {
                $this->error('订单创建失败!');
            }
        } else {
            $this->error($res['respDesc']);
        }
    }

    #验证码下发
    public function pay_sms()
    {

        $arr = array(
            "requestNo" => date('YmdHis') . rand('1111', '9999'),
            "version" => "V1.0",
            "productId" => "0103",
            "transId" => "15",
            "merNo" => $this->merNo,
            "orderDate" => date('Ymd'),
            "orderNo" => $this->orderNum,
            //"returnUrl"=>$this->NotifyUrl.'q_returnUrl',
            "notifyUrl" => $this->NotifyUrl . 'q_notifyUrl',
            "transAmt" => $this->data['total'] * 100,#订单金额 单位为分
            "commodityName" => $this->Sdata['name'],
            "phoneNo" => $this->bank_data['phone'],
            "customerName" => $this->Mdata['mch_card_name'],
            "cerdType" => "01",
            "cerdId" => $this->Mdata['mch_card_id'],
            "acctNo" => $this->bank_data['card'],
            "cvn2" => $this->bank_data['cvn'],
            "expDate" => substr($this->bank_data['date'], -4)
        );
        $arr['signature'] = self::card_sign($arr);
        //rwlog('card_b',$arr);
        //rwlog('card_c',http_build_query($arr));
        $res = card_curl_post($this->ApiUrl, http_build_query($arr));
        parse_str($res, $res);
        #结果
        if ($res['respCode'] == 'P000') { #表示待支付 验证码下发成功
            $array = array(
                'mid' => $this->data['sid'],
                'store_id' => $this->data['id'],
                'agent_id' => GetMchAid($this->data['sid']),
                'new' => serialize($res),
                'data' => serialize($this->data),
                'rel' => serialize($res),
                'createtime' => time(),
                'mch_rate' => $this->mch_rate,
                'mch_id' => $this->Mdata['mch_id'],
                'service' => 'card_api',
                'out_trade_no' => $res['orderNo'],
                'body' => $this->Sdata['name'],
                'total_fee' => $res['transAmt'] / 100, //存数据库按照分进行统计
                'mch_create_ip' => Get_Clienti_Ips(),
                'type' => 'D0',
                'alleys' => 'Qcard',
                'domain_auth' => domain_auth(),
            );
            $rel = M('mch_orders')->add($array);
            if ($rel) {
                $pay_data = array(
                    'msg' => '短信发送成功',
                    'type' => 'card',
                    'order_id' => $res['orderNo'],
                );
                $this->success($pay_data);
            } else {
                $this->error('订单创建失败!');
            }
        } else {
            $this->error($res['respDesc']);
        }
    }


    #商户进件
    public function card_mch_in()
    {
        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        if (!$alleys['rate']) {
            $this->error('系统进行二次监测,此商户的(' . $this->data['alleys'] . ')通道D0通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        } else {
            #鉴权银行四要素
            $care_data = array(
                'cardNo' => $alleys['mch_bank_cid'], #银行卡卡号
                'certNo' => $alleys['mch_card_id'], #身份证号
                'name' => $alleys['mch_bank_name'], #姓名
                //'phone'=>$alleys['mch_bank_tel'],#手机号
            );
            #鉴权
            $res = card_validate_calls($care_data, '进件', $this->data['cid'], $this->data['alleys']);
            if ($res['status'] == 1) {#鉴权成功
                $save = array(
                    'mch_id' => $this->rand_mch_id(),
                    'load_status' => 1,
                    'status' => 1,
                    'api_rel' => serialize($res),
                );
                M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                $this->success('商户无卡快捷进件成功!');
            } else {
                $this->error('进件鉴权失败!提示:' . $res['msg']);
            }
        }
    }


    #商户资料变更接口-变更费率
    public function alter_rate()
    {
        $res = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save(array('rate' => $this->data['rates']));
        if ($res) {
            $this->alter_rate_log(1, '变更成功');
            $this->success('费率变更成功!新费率立即生效!', U('card_api_way', array('id' => $this->data['cid'])));
        } else {
            $this->alter_rate_log(0, '变更失败');
            $this->error('费率变更失败!');
        }
    }

    #商户资料变更接口-变更结算信息
    public function alter_bank()
    {

        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        $care_data = array(
            'cardNo' => $this->data['mch_bank_cid'], #银行卡卡号
            'certNo' => $alleys['mch_card_id'], #身份证号
            'name' => $alleys['mch_bank_name'], #姓名
            //'phone'=>$alleys['mch_bank_tel'],#手机号
        );
        #鉴权
        $res = card_validate_calls($care_data, '变更', $this->data['cid'], $this->data['alleys']);
        if ($res['status'] == 1) {#鉴权成功
            $this->alter_bank_log(1, '变更成功');
            $save = array(
                'mch_bank_list' => $this->data['mch_bank_list'],
                'mch_bank_cid' => $this->data['mch_bank_cid'],
                'mch_bank_provice' => $this->data['mch_bank_provice'],
                'mch_bank_citys' => $this->data['mch_bank_citys'],
                'mch_linkbnk' => $this->data['mch_linkbnk'],
                'mch_bank_tel' => $this->data['mch_bank_tel'],
            );
            M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
            $this->success('结算信息变更成功!新结算信息立即生效!', U('card_api_way', array('id' => $this->data['cid'])));
        } else {
            $this->error('变更信息鉴权失败!提示:' . $res['msg']);
        }
    }


}