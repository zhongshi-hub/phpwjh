<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_CardinitBaseController;
class CXJcardController extends Alleys_CardinitBaseController
{
    protected $ApiUrl;
    protected $Api;
    protected $Url;
    public function _initialize()
    {

        $this->Api=array(
          'AppId'=>'11058970',
          'Key'=>'5e08b0b093b94cc992286b33c2b3bda2'
        );
        $this->Url='http://47.96.171.202:8010';
        $this->ApiUrl=array(
             'MchIn'=>$this->Url.'/api/v1.0/debit',#商户入网
             'Sms'=>$this->Url.'/api/v1.0/order', #下发验证码
             'Open'=>$this->Url.'/api/v1.0/open',#快捷开通
             'Submit'=>$this->Url.'/api/v1.0/order'#交易下单支付
        );



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
    }


    #提交支付
    public function pay_submit(){
        if (!$this->data['verify']) {
            $this->error('请输入验证码');
        }
        if (strlen($this->data['verify']) < 6) {
            $this->error('验证码错误!验证码为6位数字');
        }
        if (!$this->data['order_id']) {
            $this->error('未获取到订单信息,请先获取验证码!');
        }
        $oid=$this->data['order_id'];

        $arr = array(
            'orderNo' =>$oid,
            'smsCode'=>$this->data['verify'],
        );
        $res=curl_calls($this->ApiUrl['Submit'].'/'.$oid.'/sms/'.$this->data['verify'],json_encode($arr),0,true);
        $res=json_decode($res,true);
        //rwlog('XJSubmitData',$res);
        if($res['isSuccess']=='true'&&$res['data']){
            $returnUrl = "http://" . $_SERVER['HTTP_HOST'] . '/CardApi/return_url?oid=' . $res['data']['agentOrderNo'] . '&alley=XJ';
            $where = array(
                'out_trade_no' => $res['data']['agentOrderNo'],
                'domain_auth' => domain_auth(),
            );
            if($res['data']['state']==2||$res['data']['state']==3||$res['data']['state']==4){
                $status=1;
            }else{
                $status=$res['data']['state'];
            }
            $save = array(
                'transaction_id'=> $res['data']['orderNo'],
                'rel' => serialize($res),
                'total_fee' => $res['data']['totalFee'] / 100,
                'status' => $status,
                'time_end' => strtotime($res['data']['tradeTime']),
            );
            $rel = M('mch_orders')->where($where)->save($save);
            if ($rel) {
                $this->success('支付成功', $returnUrl);
            } else {
                $this->error('支付订单创建失败!');
            }
        }else{
            $this->error('支付失败!'.$res['message'].'('.$res['code'].$res['isSuccess'].')');
        }
    }

    #下发验证码
    public function pay_sms()
    {

        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['sid'], 'alleys_type' => $this->data['alleys']))->find();
        if ($alleys) {
            #开通快捷
            //$OpenCard=self::OpenCard();
            //dump($OpenCard);

            $Oid=$this->orderNum;
            $Info = array($this->bank_data['card'], $alleys['mch_bank_name'], $alleys['mch_card_id'], $this->bank_data['phone']);
            $arr = array(
                'appId' => $this->Api['AppId'],
                'nonceStr' => self::getRandCode(),
                'customerInfo' => self::DataEnCode(implode('|', $Info)),
                'totalFee'=>$this->data['total'] * 100,#订单金额 单位为分
                'agentOrderNo'=>$Oid,
                'notifyUrl'=>'http://www.xunmafu.com/CardApi/XJNotifyUrl',
                //'returnUrl'=>$returnUrl,
                'mchId'=>$alleys['mch_id'],
                'cvn2'=>$this->bank_data['cvn'],
                'expireDate'=>substr($this->bank_data['date'], -2).substr($this->bank_data['date'],0,2)
            );
            $arr['sign']=self::XJSign($arr);
            $res=curl_calls($this->ApiUrl['Sms'],json_encode($arr),0,true);
            rwlog('XJSmsData',$arr);
            rwlog('XJSmsData',$res);
            $res=json_decode($res,true);

            if($res['isSuccess']=='true'&&$res['data']){
                $array = array(
                    'mid' => $this->data['sid'],
                    'store_id' => $this->data['id'],
                    'agent_id' => GetMchAid($this->data['sid']),
                    'new' => serialize($arr),
                    'data' => serialize($this->data),
                    'rel' => serialize($res),
                    'createtime' => time(),
                    'mch_rate' => $this->mch_rate,
                    'mch_id' => $this->Mdata['mch_id'],
                    'service' => 'card_api',
                    'out_trade_no' => $res['data']['agentOrderNo'],
                    'body' => $this->Sdata['name'],
                    'total_fee' => $res['data']['totalFee'] / 100, //存数据库按照分进行统计
                    'mch_create_ip' => Get_Clienti_Ips(),
                    'type' => 'D0',
                    'alleys' => 'XJcard',
                    'domain_auth' => domain_auth(),
                );
                $rel = M('mch_orders')->add($array);
                if ($rel) {
                    $pay_data = array(
                        'msg' => '短信发送成功',
                        'type' => 'card',
                        'order_id' => $res['data']['orderNo'],
                    );
                    $this->success($pay_data);
                } else {
                    $this->error('订单创建失败!');
                }
            }else{
                $this->error('验证码下发失败!'.$res['message'].'('.$res['code'].$res['isSuccess'].')');
            }
        }else{
          $this->error('获取数据失败');
        }
    }

    #开通快捷功能
    public function OpenCard(){
        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['sid'], 'alleys_type' => $this->data['alleys']))->find();
        $arr=array(
            'appId' => $this->Api['AppId'],
            'nonceStr' => self::getRandCode(),
            'mchId'=>$alleys['mch_id'],
            'name'=>$alleys['mch_bank_name'],
            'cardNumber'=>$this->bank_data['card'],
            'tel'=>$this->bank_data['phone'],
            'cvn'=>$this->bank_data['cvn'],
            'expireDate'=>substr($this->bank_data['date'], -4),
        );
        $arr['sign']=self::XJSign($arr);
        $res=curl_calls($this->ApiUrl['Open'],json_encode($arr),0,true);
        return array('res'=>$res,'arr'=>$arr);
    }



    #进件
    public function card_mch_in(){
        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        if (!$alleys['rate']) {
            $this->error('系统进行二次监测,此商户的(' . $this->data['alleys'] . ')通道D0通道费率未配置,请配置后进件,如有疑问!请联系技术!');
        } else {
            $Info=array($alleys['mch_bank_cid'],$alleys['mch_bank_name'],$alleys['mch_card_id'],$alleys['mch_bank_tel']);
            $arr=array(
              'appId'=>$this->Api['AppId'],
              'nonceStr'=>self::getRandCode(),
              'customerInfo'=>self::DataEnCode(implode('|',$Info)),
              'provinceCode'=>self::city($alleys['mch_provice'],1),
              'cityCode'=>self::city($alleys['mch_citys'],0),
              'address'=>$alleys['mch_provice'].$alleys['mch_citys'].$alleys['mch_district'].$alleys['mch_address'],
              'fee0'=>$alleys['rate'],
              'd0fee'=>'100',
              'pointsType'=>0,
            );
            $arr['sign']=self::XJSign($arr);
            $res=curl_calls($this->ApiUrl['MchIn'],json_encode($arr),0,true);
            $res=json_decode($res,true);
            if($res['isSuccess']=='true'&&$res['data']){
                $save = array(
                    'mch_id' => $res['data'],
                    'load_status' => 1,
                    'status' => 1,
                    'api_rel' => json_encode($res,JSON_UNESCAPED_UNICODE),
                );
                M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
                $this->success('商户无卡快捷 入网成功!');
            }else{
                $this->error('入网失败!'.$res['message'].'('.$res['code'].$res['isSuccess'].')');
            }
        }

    }


    #商户资料变更接口-变更费率
    public function alter_rate()
    {
        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        $Info=array($alleys['mch_bank_cid'],$alleys['mch_bank_name'],$alleys['mch_card_id'],$alleys['mch_bank_tel']);
        $arr=array(
            'appId'=>$this->Api['AppId'],
            'nonceStr'=>self::getRandCode(),
            'mchId'=>$alleys['mch_id'],
            'customerInfo'=>self::DataEnCode(implode('|',$Info)),
            'provinceCode'=>self::city($alleys['mch_provice'],1),
            'cityCode'=>self::city($alleys['mch_citys'],0),
            'fee0'=>$this->data['rates'],
            'd0fee'=>'100'
        );
        $arr['sign']=self::XJSign($arr);
        $res=self::PutUrl($this->ApiUrl['MchIn'],$arr);
        if($res['isSuccess']==true&&$res['code']=='00'){
            $this->alter_rate_log(1, '变更成功');
            M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save(array('rate' => $this->data['rates']));
            
            $this->success('费率变更成功!新费率立即生效!', U('card_api_way', array('id' => $this->data['cid'])));
        }else{
            $this->alter_rate_log(0, '变更失败');
            $this->error('费率变更失败! '.$res['message'].'('.$res['code'].')');
        }
    }


    #变更银行卡信息
    public function alter_bank(){
        $alleys = M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        $Info=array($this->data['mch_bank_cid'],$this->data['mch_bank_name'],$alleys['mch_card_id'],$this->data['mch_bank_tel']);
        $arr=array(
            'appId'=>$this->Api['AppId'],
            'nonceStr'=>self::getRandCode(),
            'mchId'=>$alleys['mch_id'],
            'customerInfo'=>self::DataEnCode(implode('|',$Info)),
            'provinceCode'=>self::city($alleys['mch_provice'],1),
            'cityCode'=>self::city($alleys['mch_citys'],0),
            'fee0'=>$alleys['rate'],
            'd0fee'=>'100'
        );
        $arr['sign']=self::XJSign($arr);
        $res=self::PutUrl($this->ApiUrl['MchIn'],$arr);
        if($res['isSuccess']==true&&$res['code']=='00'){
            $this->alter_bank_log(1, $res);
            $save = array(
                'mch_bank_list' => $this->data['mch_bank_list'],
                'mch_bank_cid' => $this->data['mch_bank_cid'],
                'mch_bank_provice' => $this->data['mch_bank_provice'],
                'mch_bank_citys' => $this->data['mch_bank_citys'],
                'mch_linkbnk' => $this->data['mch_linkbnk'],
                'mch_bank_tel' => $this->data['mch_bank_tel'],
            );
            M('MchSellerCardAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->save($save);
            $this->success('结算信息变更成功!', U('card_api_way', array('id' => $this->data['cid'])));
        }else{
            $this->alter_bank_log(0, $res);
            $this->error('结算信息变更失败! '.$res['message'].'('.$res['code'].')');
        }
    }


    #取城市编码
    public function city($data,$is='0'){
       $res=M('XjCityData')->where(array('name'=>$data,'is'=>$is))->getField('code');
       return $res;
    }


    #签名算法
    public function XJSign($data){
        ksort($data);
        $tmp = '';
        foreach ( $data as $k => $v ) {
            // 不参与签名校验的字段
            if($k == 'sign' || $k == null) continue;
            // 拼装待签字段
            $tmp .= $k . '=' . $v . '&';
        }
        $tmp .= 'key=' . $this->Api['Key'];
        //rwlog('XJ_MchSign',$tmp);
        return md5($tmp);
    }

    #3DES数据加密
    public function DataEnCode($data){
        return encrypt($data,$this->Api['Key']);
    }

    #随机字符串
    public function getRandCode(){
        $charts = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz0123456789";
        $max = strlen($charts);
        $str = "";
        for($i = 0; $i < 16; $i++)
        {
            $str .= $charts[mt_rand(0, $max)];
        }
        return $str;
    }


    public function PutUrl($url,$data)
    {
        $data = json_encode($data);
        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); //设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }




}