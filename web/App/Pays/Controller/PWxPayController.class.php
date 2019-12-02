<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;

/**
 * 微信官方通道
 * Class PWxPayController
 * @package Pays\Controller
 */
class PWxPayController extends Alleys_initBaseController
{

    protected $payConfig;
    public function _initialize()
    {
        Vendor('xun_wxpay.WxPayApi');
        Vendor('xun_wxpay.JsApiPay');
        Vendor('xun_wxpay.WxPayNotify');
        Vendor("xun_wxpay.NativePay");
        $this->payConfig=[
            'subMchId'=>$this->Mdata['mch_id'],//子商户号
            'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Pays/PWxPay/notifyUrl'
        ];
    }





    public function pay_wx_scan(){
    	if(empty($this->data['total'])){
    		$this->error('请输入收款金额');
		}
		$oid = $this->orderNum;
		$input=new \WxPayMicroPay();
		$input->SetSubMch_id($this->payConfig['subMchId']);
		$input->SetBody($this->Sdata['name']."收款");
		$input->SetOut_trade_no($oid);
		$input->SetTotal_fee($this->data['total']*100);
		$input->SetAuth_code($this->data['code_data']);
		$result = \WxPayApi::micropay($input);
		if($result['return_code']=='SUCCESS'){
			if($result['result_code']=='SUCCESS'){

			}else{
				$this->error($result['err_code_des']);
			}
		}else{
			$this->error($result['return_msg']);
		}
	}


	/**
	 * 会员卡充值
	 * @param $data
	 * @return array
	 */
	public function memberPay($data){
    	//获取mch_id
		$mch_id=AlleysGetRate('WxPay',$data['mid'],'mch_id');
		$order_id = rand_out_trade_no();
		$tools = new \JsApiPay();
		$input = new \WxPayUnifiedOrder();
		$input->SetSubMch_id($mch_id);
		$input->SetBody("{$data['phone']}会员卡充值");
		$input->SetOut_trade_no($order_id);
		$input->SetTotal_fee($data['total']*100);
		$input->SetNotify_url($this->payConfig['notifyUrl']);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($data['openid']);
		$input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
		$input->SetNonce_str(uniqid());//随机字符串
		$res = \WxPayApi::unifiedOrder($input);
		if($res['return_code']=='SUCCESS'){
			if($res['result_code']=='SUCCESS'){
				$jsApiParameters = $tools->GetJsApiParameters($res);
				$array=array(
					'goods_tag'=>$data['remark']?$data['remark']:"{$data['phone']}会员充值",
					'trade_type'=>'JSAPI',
					'mid'=>$data['mid'],
					'store_id'=>$data['pay_store_id'],
					'agent_id'=>GetMchAid($data['mid']),
					'createtime'=>time(),
					'mch_rate'=>AlleysGetRate('WxPay',$data['mid'],'rate'),
					'mch_id'=>$mch_id,
					'service'=>'wx_jsapi',
					'out_trade_no'=>$order_id,
					'body'=>'会员卡充值',
					'total_fee'=>$data['total'], //存数据库按照分进行统计
					'mch_create_ip'=>Get_Clienti_Ips(),
					'sub_openid'=>$data['openid'],
					'type'=>'D1',
					'alleys'=>'WxPay',
					'domain_auth'=>domain_auth(),
					'is_raw'=>1,
				);
				$rel=M('mch_orders')->add($array);
				if($rel) {
					$pay_data = array(
						'msg' => '订单创建成功',
						'type' => 'wx',
						'pay_info' => json_decode($jsApiParameters, true),
						'out_trade_no' => $array['out_trade_no'],
						'total'=>$data['total'],
						'openid'=>$data['openid']
					);
					return ['status'=>true,'msg'=>'success','data'=>$pay_data];
				}else{
					return ['status'=>false,'msg'=>'订单创建失败'];
				}
			}else{
				return ['status'=>false,'msg'=>$res['err_code_des']];
			}
		}else{
			return ['status'=>false,'msg'=>$res['return_msg']];
		}
	}

    /**
     * 微信支付JSAPI
     */
    public function pay_wx_jsapi()
    {
        $tools = new \JsApiPay();
        $order_id = $this->orderNum;
        $input = new \WxPayUnifiedOrder();
        $input->SetSubMch_id($this->payConfig['subMchId']);
        $input->SetBody($this->Sdata['name']."买单");
        $input->SetOut_trade_no($order_id);
        $input->SetTotal_fee($this->data['total']*100);
        $input->SetNotify_url($this->payConfig['notifyUrl']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($this->data['openid']);
        $input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $input->SetNonce_str(uniqid());//随机字符串
        $res = \WxPayApi::unifiedOrder($input);
        if($res['return_code']=='SUCCESS'){
            if($res['result_code']=='SUCCESS'){
                $jsApiParameters = $tools->GetJsApiParameters($res);
                $array=array(
                    'goods_tag'=>$this->data['remark'],
                    'trade_type'=>'JSAPI',
                    'mid'=>$this->data['sid'],
                    'store_id'=>$this->data['id'],
                    'agent_id'=>GetMchAid($this->data['sid']),
                    'new'=>serialize($input),
                    'data'=>serialize($this->data),
                    'rel'=>serialize($res),
                    'createtime'=>time(),
                    'mch_rate'=>$this->mch_rate,
                    'mch_id'=>$this->Mdata['mch_id'],
                    'service'=>'wx_jsapi',
                    'out_trade_no'=>$order_id,
                    'body'=>$this->Sdata['name'],
                    'total_fee'=>$this->data['total'], //存数据库按照分进行统计
                    'mch_create_ip'=>Get_Clienti_Ips(),
                    'sub_openid'=>$this->data['openid'],
                    'type'=>'D1',
                    'alleys'=>'WxPay',
                    'domain_auth'=>domain_auth(),
                    'is_raw'=>1,
                );
                $rel=M('mch_orders')->add($array);
                if($rel) {
                    $pay_data = array(
                        'msg' => '订单创建成功',
                        'type' => 'js',
                        'pay_info' => json_decode($jsApiParameters, true),
                        'out_trade_no' => $array['out_trade_no']
                    );
                    $this->success($pay_data);
                }else{
                    $this->error('订单创建失败!请重新支付!');
                }
            }else{
                $this->error($res['err_code_des'].'['.$res['result_code'].']');
            }

        }else{
            $this->error($res['return_msg'].'['.$res['return_code'].']');
        }
    }


    /**
     * 异步回调
     */
    public function notifyUrl(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notifyData=$this->xmlToArray($xml);
        $oid=$notifyData['out_trade_no'];
        //保存异步结果
        $array = array(
            'notify_time'=>time(),
            'notify_data'=>json_encode($notifyData,JSON_UNESCAPED_UNICODE),
        );
        $rel=M('MchOrders')->where(array('out_trade_no'=>$oid))->save($array);
        //使用订单查询结果更新支付订单状态
        if($rel) {
            $res = self::orderResult($oid);
            if ($res['status'] == 1) { //订单支付成功
                die('SUCCESS');
            }
        }else{// 告诉异步结果处理失败
            die('error');
        }
    }


    //页面同步回调
    public function ResultData(){
        $out_trade_no=I('get.out_trade_no');
        $rel=M('MchOrders')->where(array('out_trade_no'=>$out_trade_no))->find();
        $store = Get_Store($rel['store_id']);
        #点击完成到支付页面
        $codes = M('MchCodes')->where(array('store_id' => $rel['store_id'], 'mch_id' => $rel['mid']))->getField('codes');
        $res = self::orderResult($out_trade_no);
        switch ($res['status']){
            case 1:
                $status='ok'; //结果为ok 视图结果为支付成功
                break;
            default:
                $status=$res['msg'];
                break;
        }
        $assign = array(
            'status' => $status,
            'total' => number_format($rel['total_fee'], 2),
            'mch_name' => $store['name'],
            'time' => date('Y-m-d H:i:s', $rel['createtime']),
            'order_id' => $rel['out_trade_no'],
            'url' => C('MA_DATA_URL') . '/' . $codes
        );
        $this->assign($assign);
        $this->display('Notify/new_result'); //视图页
    }


    /**
     * 订单查询
     * @param $oid
     * @return array
     */
    public function orderResult($oid){
        $order=M('MchOrders')->where(['out_trade_no'=>$oid])->find();
        if($order){
            $map['cid']=$order['mid'];
            $map['alleys_type']=$order['alleys'];
            $SellerAlleys=M('MchSellerAlleys')->where($map)->field('mch_id,mch_key,mch_appid')->find();
            //利用交易接口查询
            $input = new \WxPayOrderQuery();
            $input->SetSubMch_id($SellerAlleys['mch_id']);
            $input->SetOut_trade_no($order['out_trade_no']);
            $res = \WxPayApi::orderQuery($input);
            if($res['return_code']=='SUCCESS'){
                if($res['result_code']=='SUCCESS'){
                    //查询成功
                    switch ($res['trade_state']){
                        case 'SUCCESS':
                            $status=1;
                            break;
                        case 'REFUND':
                            $status=2;
                            break;
                        default:
                            $status=0;
                            break;
                    }
                    //更新数据库结果
                    $save=[
                        'status'=>$status,
                        'time_end'=>strtotime($res['time_end']),
                        'total'=>$res['total_fee']/100,//分单位转换为元
                        'transaction_id'=>$res['transaction_id'],
                    ];
                    M('MchOrders')->where(['out_trade_no'=>$res['out_trade_no']])->save($save);
                    if($status==1) {
                        R('Pays/Notify/sendTemplateMessage', array($res['out_trade_no'])); //发送收款成功模板消息
                    }
                    $return=['status'=>1,'res_status'=>$status,'msg'=>'查询成功'];
                }else{
                    $return=['status'=>0,'msg'=>$res['err_code_des'].'['.$res['result_code'].']'];
                }
            }else{
                $return=['status'=>0,'msg'=>$res['return_msg'].'['.$res['return_code'].']'];
            }
        }else{
            $return=['status'=>false,'msg'=>'订单信息获取失败'];
        }
        return $return;
    }



    /**
     * 将xml转为array
     * @param string $xml
     * return array
     */
    public function xmlToArray($xml)
    {
        if (!$xml) {
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }


}