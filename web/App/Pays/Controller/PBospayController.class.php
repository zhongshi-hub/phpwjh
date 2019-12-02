<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
class PBospayController extends Alleys_initBaseController {


    public function _initialize()
    {
        #威富通函数扩展
        Vendor('wft_sdk.Utils','','.class.php');
        Vendor('wft_sdk.RequestHandler','','.class.php');
        Vendor('wft_sdk.PayHttpClient','','.class.php');
        Vendor('wft_sdk.ClientResponseHandler','','.class.php');
        $this->resHandler = new \ClientResponseHandler();
        $this->reqHandler = new \RequestHandler();
        $this->pay = new \PayHttpClient();
        $this->Utils = new \Utils();

        #支付网关
        $GateUrl='https://pay.swiftpass.cn/pay/gateway';
        #商户号 + key
        $this->MchId=$this->Mdata['mch_id'];
        $this->MchKey=$this->Mdata['mch_key'];
        $this->reqHandler->setGateUrl($GateUrl);
        //$this->reqHandler->setKey($MchKey); //商户key
    }

    public function pay_wx_scan(){
        $this->error('此通道暂未开通扫码通道!');
    }
    
    public function pay_ali_scan(){
        $this->error('此通道暂未开通扫码通道!');
    }

    #微信公众号支付
    public function pay_wx_jsapi()
    {
        
        $data_array=array(
            'service'=>'pay.weixin.jspay',
            'is_raw'=>1,
            'out_trade_no'=>$this->orderNum,
            'body'=>$this->Sdata['name'],
            'sub_openid'=>$this->data['openid'],
            'total_fee'=>$this->data['total']*100,
            'mch_create_ip'=>Get_Clienti_Ips(),
            'notify_url'=>$this->NotifyUrl.'bos_notify_url',
        );
        $this->reqHandler->setReqParams($data_array,array('method'));
        $this->reqHandler->setKey($this->MchKey); //商户key
        $this->reqHandler->setParameter('mch_id',$this->MchId);//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = $this->Utils->toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                $res=$this->resHandler->getAllParameters();
                //rwlog('test1',$res);
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    $array=array(
                        'mid'=>$this->data['sid'],
                        'store_id'=>$this->data['id'],
                        'agent_id'=>GetMchAid($this->data['sid']),
                        'new'=>serialize($this->reqHandler->getAllParameters()),
                        'data'=>serialize($this->data),
                        'rel'=>serialize($res),
                        'createtime'=>time(),
                        'mch_rate'=>$this->mch_rate,
                        'mch_id'=>$this->Mdata['mch_id'],
                        'service'=>'wx_jsapi',
                        'out_trade_no'=>$data_array['out_trade_no'],
                        'body'=>$data_array['body'],
                        'total_fee'=>$data_array['total_fee']/100, //存数据库按照分进行统计
                        'mch_create_ip'=>$data_array['mch_create_ip'],
                        'sub_openid'=>$this->data['openid'],
                        'type'=>'T1',
                        'alleys'=>'Bospay',
                        'domain_auth'=>domain_auth(),
                        'is_raw'=>1,
                    );
                    $rel=M('mch_orders')->add($array);
                    if($rel){
                        $pay_data=array(
                            'msg'=>'订单创建成功',
                            'type'=>'js',
                            'pay_info'=>json_decode($res['pay_info'],true),
                            'out_trade_no'=>$data_array['out_trade_no']
                        );
                        $this->success($pay_data);
                    }else{
                        $this->error('订单创建失败!请重新支付!');
                    }
                }else{
                    $err_info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                    $this->error($err_info);
                }
            }else{
                $err_info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                $this->error($err_info);
            }
        }else{
            $err_info='错误码:'.$this->pay->getResponseCode().'错误消息:'.$this->pay->getErrInfo();
            $this->error($err_info);
        }
    }

    #支付宝H5
    public function pay_ali_jsapi(){
       // rwlog('bosdata',$this->data);
        $data_array=array(
            'service'=>'pay.alipay.jspay',
            'is_raw'=>1,
            'out_trade_no'=>$this->orderNum,
            'body'=>$this->Sdata['name'],
            'buyer_id'=>$this->data['openid'],
            'total_fee'=>$this->data['total']*100,
            'mch_create_ip'=>Get_Clienti_Ips(),
            'notify_url'=>$this->NotifyUrl.'bos_notify_url',
        );
        $this->reqHandler->setReqParams($data_array,array('method'));
        $this->reqHandler->setKey($this->MchKey); //商户key
        $this->reqHandler->setParameter('mch_id',$this->MchId);//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = $this->Utils->toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        //dump($data);

        //rwlog('test1',$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            $res=$this->resHandler->getAllParameters();
            //dump($res);

            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                //rwlog('test1',$res);

               if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                   $array=array(
                        'mid'=>$this->data['sid'],
                        'store_id'=>$this->data['id'],
                        'agent_id'=>GetMchAid($this->data['sid']),
                        'new'=>serialize($this->reqHandler->getAllParameters()),
                        'data'=>serialize($this->data),
                        'rel'=>serialize($res),
                        'createtime'=>time(),
                        'mch_rate'=>$this->mch_rate,
                        'mch_id'=>$this->Mdata['mch_id'],
                        'service'=>'ali_jsapi',
                        'out_trade_no'=>$data_array['out_trade_no'],
                        'body'=>$data_array['body'],
                        'total_fee'=>$data_array['total_fee']/100, //存数据库按照分进行统计
                        'mch_create_ip'=>$data_array['mch_create_ip'],
                        'sub_openid'=>$this->data['openid'],
                        'type'=>'T1',
                        'alleys'=>'Bospay',
                        'domain_auth'=>domain_auth(),
                        'is_raw'=>1,
                    );
                    $rel=M('mch_orders')->add($array);
                    if($rel){
                        $pay_data=array(
                            'msg'=>'订单创建成功',
                            'type'=>'js',
                            'pay_info'=>json_decode($res['pay_info'],true),
                            'out_trade_no'=>$data_array['out_trade_no']
                        );
                        $this->success($pay_data);
                    }else{
                        $this->error('订单创建失败!请重新支付!');
                    }
                }else{
                    $err_info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                    $this->error($err_info);
                }
            }else{
                $err_info='错误码:'.$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('status').'  错误消息:'.$this->resHandler->getParameter('err_msg').$this->resHandler->getParameter('message');
                $this->error($err_info);
            }
        }else{
            $err_info='错误码:'.$this->pay->getResponseCode().'错误消息:'.$this->pay->getErrInfo();
            $this->error($err_info);
        }



    }



    public function mch_in(){
        $this->error('Bos银行通道需要人工进件配置参数,无自动进件接口!');
    }


   

}