<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_CardinitBaseController;
class CYFcardController extends Alleys_CardinitBaseController
{

    public function _initialize()
    {
        parent::_initialize();

        #网关
        //http://140.206.72.238:8080/payserv/serverInterface/Service/kjMerUploadService.do
        $this->data_api=array(
            'mch_in'=>'http://120.132.24.153:9090/payserv/serverInterface/Service/kjMerUploadService.do', #进件网关
            'pay_url'=>'http://shopping.maibei365.com/xlzf/webPay',
            'mch_sign'=>'e1a7384d81abcc6ad1d08118f0fccef0', #进件签名密钥
            'brhId'=>'0199981235',#机构号
            'pay_sign'=>'12f1f0935be5cc9f0b4899cb583866e1',#交易密钥
        );

        #取参数
        $this->mch_api=json_decode($this->Mdata['api_rel'],true);

    }


    #新版页面 支付跳转
    public function pay_submit_data(){
        $order_id=$this->orderNum;
        $data=I('post.');
        $data['pay_data']=json_decode(Xdecode($data['pay_data']),true);
        $data['mch_api']=$this->mch_api;
        #参数
        $arr=array(
            'token'=>$this->mch_api['token'],
            'mer_order_no'=>$order_id,
            'mer_amount'=>$this->data['total']*100,
            'pay_type'=>1,
            'remark'=>date('YmdHis'),
        );
        $arr['sign']=self::sign($arr,0);
        //ksort($arr);
        foreach($arr as $k => $v) { //拼接
            if("" != $v) {
                $OutData .= $k . "=" . $v . "&";
            }
        }

        #转链接
        //$url='http://140.206.72.238:18080/xlzf/webPay?'.$OutData;
        $url=$this->data_api['pay_url'].'?'.$OutData;
        if($OutData) {
            $array=array(
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>$OutData,
                'data'=>serialize($this->data),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'card_api',
                'out_trade_no'=>$order_id,
                'body'=>$this->Sdata['name'],
                'total_fee'=>$arr['mer_amount']/100, //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'type'=>'D0',
                'alleys'=>'YFcard',
                'domain_auth'=>domain_auth(),
            );
            M('mch_orders')->add($array);
            $this->success('通信成功', $url);
        }else{
            $this->error('通信失败');
        }
    }


    #支付跳转
    public function pay_sub(){
        $order_id=$this->orderNum;
        $data=I('post.');
        $data['pay_data']=json_decode(Xdecode($data['pay_data']),true);
        $data['mch_api']=$this->mch_api;
        #参数
        $arr=array(
          'token'=>$this->mch_api['token'],
          'mer_order_no'=>$order_id,
          'mer_amount'=>$data['pay_data']['total_fee']*100,
          'pay_type'=>1,
          'remark'=>date('YmdHis'),
        );
        $arr['sign']=self::sign($arr,0);
        //ksort($arr);
        foreach($arr as $k => $v) { //拼接
            if("" != $v) {
                $OutData .= $k . "=" . $v . "&";
            }
        }
        #转链接
        //$url='http://140.206.72.238:18080/xlzf/webPay?'.$OutData;
        $url=$this->data_api['pay_url'].'?'.$OutData;
        if($OutData) {
            $array=array(
                'mid'=>$data['pay_data']['aid'],
                'store_id'=>$data['pay_data']['mid'],
                'agent_id'=>GetMchAid($data['pay_data']['aid']),
                'new'=>$OutData,
                'data'=>serialize($this->data),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'card_api',
                'out_trade_no'=>$order_id,
                'body'=>$this->Sdata['name'],
                'total_fee'=>$arr['mer_amount']/100, //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'type'=>'D0',
                'alleys'=>'YFcard',
                'domain_auth'=>domain_auth(),
            );
            M('mch_orders')->add($array);
            $this->success('通信成功', $url);
        }else{
            $this->error('通信失败');
        }
    }

    #商户进件
    public function card_mch_in(){
        $alleys=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        if(!$alleys['rate']){
            $this->error('系统进行二次监测,此商户的('.$this->data['alleys'].')通道D0通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        }elseif ($alleys['rate']!=6){
            $this->error('封顶通道费率统一6%. 当前商户费率不符合规则!请修改商户费率后再进件!');
        }else {
            $data = array(
                'brhId' => $this->data_api['brhId'],
                'serialNo' => date('YmdHis') .$alleys['id'],
                'merchantName' => $alleys['mch_name'],#商户名称
                'accountName' => $alleys['mch_bank_name'],#账户名称
                'account' => $alleys['mch_bank_cid'],#银行卡号
                'idCard' => $alleys['mch_card_id'],#身份证号
                'telephone' => $alleys['mch_bank_tel'],#手机号
                'rate' => $alleys['rate'],#结算费率
                'type' => 'xlkj',#类型
                'top' => '6000',#封顶手续费 0不限制 单位分
                'isNeedD0' => 'YES',#是否开通D0
                'd0Rate' => '0.3',#代付垫资费率
                'd0FeeBi' => '0',#代付单笔手续费
                'd0MinAmount' => '1000',#代付单笔最小金额
                'doMinFee' => '30',#保底手续费
             );
            $data['sign'] = self::sign($data, 1);
            //rwlog('yf_mch_log',$data);
            $res = ccb_curl_calls($this->data_api['mch_in'], $data);
            //rwlog('yf_mch_log',$res);
            $res =json_decode($res,true);
            if ($res['resultCode'] == '0000') {
                    $save = array(
                        'mch_id' => $res['merId'],
                        'load_status' => 1,
                        'status' => 1,
                        'api_rel' => json_encode($res,JSON_UNESCAPED_UNICODE),
                    );
                    //rwlog('yf_mch_log',$save);
                    M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                    $this->success('商户进件成功!');
            } else {
                $this->error('商户进件失败! 提示:'.$res['resultCode'].'-'.$res['resultMsg']);
            }
        }
    }


    #签名
    public function sign($data,$type){
        if($type==1){#商户进件签名
            $signPars = "";
            ksort($data);
            foreach($data as $k => $v) { //拼接
                if("" != $v && "sign" != $k) {
                    $this->OutData .= $k . "=" . $v . "&";
                }
            }
            $signPars .=rtrim($this->OutData,'&').$this->data_api['mch_sign'];
            //rwlog('yf_mch_log',$signPars);
            $sign = strtoupper(md5($signPars)); //加密
        }else{#支付签名
            $signPars = "";
            ksort($data);
            foreach($data as $k => $v) { //拼接
                if("" != $v && "sign" != $k) {
                    $this->OutData .= $k . "=" . $v . "&";
                }
            }
            $signPars .=$this->OutData.'key='.$this->data_api['pay_sign'];
            $sign = strtoupper(md5($signPars)); //加密
        }
        return $sign;
    }

}