<?php
namespace Pays\Controller;

use Pays\Controller\Alleys_CardinitBaseController;

class CQcardaController extends Alleys_CardinitBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->TApiUrl = "http://121.201.111.67:9999/payment-gate-web/gateway/api/backTransReq";

    }


    #新版支付接口页面 直连银行
    public function pay_submit_data()
    {
        $order_id = $this->orderNum;
        //$returnUrl="http://".$_SERVER['HTTP_HOST']."/Mch/Index/fast_data";
        $returnUrl = "http://" . $_SERVER['HTTP_HOST'] . '/CardApi/return_url?oid=' . $order_id . '&alley=Q2';
        $arr = array(
            "requestNo" => date('YmdHis') . rand('1111', '9999'),
            "version" => "V1.0",
            "productId" => "0111",
            "transId" => "01",
            "merNo" => $this->QmerNo,
            "orderDate" => date('Ymd'),
            "orderNo" => $order_id,
            //'bankCode'=>Qcard_bank_bm($this->bank_data['bank']), //银行编码
            'acctNo' => $this->bank_data['card'],//交易卡号
            "returnUrl" => $returnUrl,
            "notifyUrl" => $this->NotifyUrl . 'q_notifyUrl',
            "transAmt" => $this->data['total'] * 100,#订单金额 单位为分
            "commodityName" => $this->Sdata['name'],
        );
        $arr['signature'] = self::Qcard_sign($arr);
        $res = card_curl_post($this->ApiUrl, http_build_query($arr));
        //rwlog('res_data1',$res);
        preg_match('/formfield=(.*)&merNo/', $res, $arr);
        $formfield_data = json_decode($arr[1], true);
        parse_str($res, $res);
        unset($res['formfield']);
        unset($res['tokenType']);
        $res['formfield'] = $formfield_data;
        //$res['formfield']=json_decode(str_replace(' ','+',$arr_res['formfield'].'&tokenType='.$arr_res['tokenType']),true);
        $_data = array();
        foreach ($res['formfield'] as $key => $val) {
            $_data[] = array(
                'name' => $key,
                'val' => $val,
            );
        }
        #存储以上订单信息到数据库
        if ($res['respCode'] == '0000') {
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
                'alleys' => 'Qcarda',
                'domain_auth' => domain_auth(),
            );
            $rel = M('mch_orders')->add($array);
            if ($rel) {
                $pay_data = array(
                    'msg' => '订单创建成功',
                    'type' => 'form',
                    'action' => $res['formaction'],
                    'v_data' => $_data,
                );
                $this->success($pay_data);
            } else {
                $this->error('订单创建失败!');
            }
        } else {
            $this->error($res['respDesc']);
        }

    }


    #直连银行
    public function pay_banks()
    {
        $order_id = $this->orderNum;
        $returnUrl = "http://" . $_SERVER['HTTP_HOST'] . "/Mch/Index/fast_data";
        $arr = array(
            "requestNo" => date('YmdHis') . rand('1111', '9999'),
            "version" => "V1.0",
            "productId" => "0111",
            "transId" => "01",
            "merNo" => $this->QmerNo,
            "orderDate" => date('Ymd'),
            "orderNo" => $order_id,
            //'bankCode'=>Qcard_bank_bm($this->bank_data['bank']), //银行编码
            'acctNo' => $this->bank_data['card'],//交易卡号
            "returnUrl" => $returnUrl,
            "notifyUrl" => $this->NotifyUrl . 'q_notifyUrl',
            "transAmt" => $this->data['total'] * 100,#订单金额 单位为分
            "commodityName" => $this->Sdata['name'],
        );
        $arr['signature'] = self::Qcard_sign($arr);
        $res = card_curl_post($this->ApiUrl, http_build_query($arr));
        //rwlog('res_data1',$res);
        preg_match('/formfield=(.*)&merNo/', $res, $arr);
        $formfield_data = json_decode($arr[1], true);
        parse_str($res, $res);
        unset($res['formfield']);
        unset($res['tokenType']);
        $res['formfield'] = $formfield_data;
        //$res['formfield']=json_decode(str_replace(' ','+',$arr_res['formfield'].'&tokenType='.$arr_res['tokenType']),true);
        $_data = array();
        foreach ($res['formfield'] as $key => $val) {
            $_data[] = array(
                'name' => $key,
                'val' => $val,
            );
        }
        #存储以上订单信息到数据库
        if ($res['respCode'] == '0000') {
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
                'alleys' => 'Qcarda',
                'domain_auth' => domain_auth(),
            );
            $rel = M('mch_orders')->add($array);
            if ($rel) {
                $pay_data = array(
                    'msg' => '订单创建成功',
                    'type' => 'form',
                    'action' => $res['formaction'],
                    'v_data' => $_data,
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