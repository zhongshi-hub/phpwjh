<?php
namespace Pays\Controller;
use Pays\Controller\InitBaseController;
/*
 * 用户注册认证
 * chen
 * 2017-07-26
 * */
class AuthController extends InitBaseController {

    public function _initialize(){
        parent::_initialize();
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

        $this->auth=unserialize($this->system['auth_data']);

        #商户号 + key
        $this->MchId=$this->auth['auth_mch_id'];
        $this->MchKey=$this->auth['auth_mch_key'];
        $this->reqHandler->setGateUrl($GateUrl);
        #获取支付参数
    }

    public function code_auth(){
        $user_info=$_SESSION['Reg']['user_info'];
        $code=$_SESSION['Reg']['codes'];
        if(!$code||!$user_info){
            $this->error('获取用户信息失败!请重新扫描二维码!','',888);
        }
        $assign=array(
          'user_info'=>Xencode(serialize($user_info)),
          'user_code'=>$code,
        );
        $this->assign($assign);
        //dump($assign);
        $this->display();
    }



    #认证支付网关
    public function gateway(){
        $data=I('post.');
        //$total_fee='1';
        $total_fee=$this->auth['auth_fee']*100;
        $user_info=unserialize(Xdecode($data['user_info']));
        #根据收款码获取代理id
        $this->agent=M('MchCodes')->where(array('codes'=>$data['codes']))->getField('aid');
        $data_array=array(
            'service'=>'pay.weixin.jspay',
            'is_raw'=>1,
            'out_trade_no'=>'UA'.rand_out_trade_no(),
            'body'=>$data['codes'].'用户认证',
            'sub_openid'=>$user_info['openid'],
            'total_fee'=>$total_fee,
            'mch_create_ip'=>Get_Clienti_Ips(),
            'notify_url'=>'http://www.xunmafu.com/Api/auth_bos_notify_url',
        );
        $this->reqHandler->setReqParams($data_array,array('method'));
        $this->reqHandler->setKey($this->MchKey); //商户key
        $this->reqHandler->setParameter('mch_id',$this->MchId);//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $_data = $this->Utils->toXml($this->reqHandler->getAllParameters());
        //rwlog('test1',$_data);
        $this->pay->setReqContent($this->reqHandler->getGateURL(),$_data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                $res=$this->resHandler->getAllParameters();
                //rwlog('test1',$res);
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    $array=array(
                        'img'=>$user_info['headimgurl'],
                        'name'=>self::filter($user_info['nickname']),
                        'openid'=>$user_info['openid'],
                        'codes'=>$data['codes'],
                        'total'=>$this->auth['auth_fee'],
                        'city'=>$user_info['province'].$user_info['city'],
                        'out_trade_no'=>$data_array['out_trade_no'],
                        'createtime'=>time(),
                        'agent'=>$this->agent,
                        'agent_name'=>agent_name($this->agent),
                        'alleys'=>'Bospay',
                        'domain_auth'=>domain_auth(),
                        'status'=>0
                    );
                    //dump($array);
                    $where['codes']=$data['codes'];
                    $where['domain_auth']=domain_auth();
                    $where['openid']=$user_info['openid'];
                    #判断是否存在
                    $count=M('MchUserAuth')->where($where)->count();
                    if($count){
                        $rel = M('MchUserAuth')->where($where)->save($array);
                    }else {
                        $rel = M('MchUserAuth')->add($array);
                    }
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


        //dump($data_array);
    }


    public function filter($str) {
        if($str){
            $name = $str;
            $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
            $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S','?', $name);
            $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie","",json_encode($name)));
            if(!$return){
                return $this->jsonName($return);
            }
        }else{
            $return = '';
        }
        return $return;

    }
}