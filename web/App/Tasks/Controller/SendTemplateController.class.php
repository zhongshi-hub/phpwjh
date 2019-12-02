<?php

namespace Tasks\Controller;
use Think\Controller;
/**
 * 发送模板消息
 * 郑州讯龙软件科技有限公司
 * zzxunlong@163.com
 * Class SendTemplateController
 * @package Tasks\Controller
 */
class SendTemplateController extends Controller
{

	/**
	 * 2019年08月22日23:18:12
	 * 更新创建方式 改为同步创建
	 * @param $oid
	 */
	public function sendTempMsg($oid){
		self::Send_Message($oid);
		#发送打印请求
		self::Send_print($oid);
	}

	#发送模板消息
	public function Send_Message($id){
		$map['out_trade_no'] = $id;
		$map['status'] = 1;
		$order = M('MchOrders')->where($map)->find();
		if(date('ymd',$order['createtime'])==date('ymd')) {
			if($order) {
				#更新会员卡充值的交易记录结果
				M('mchMemberOrder')->where(['out_trade_no'=>$order['out_trade_no'],'mid'=>$order['mid']])->save(['status'=>1]);
				#处理消费返记录
				memberXffOrderSet($order['out_trade_no']);
				#发送客户消息
				#判断是否是微信 只有微信发送
				if (explode('_', $order['service'])[0] == 'wx') {
					self::Send_PerMessage($order);
				}
				#发送云喇叭播报
				if (!S('sendSpeaker_' . $id)) {
					$type = explode('_', $order['service']);
					if ($type[0] == 'wx') {
						$pt = 2;
					} elseif ($type[0] == 'ali') {
						$pt = 1;
					} else {
						$pt = 0;
					}
					$res = sendSpeaker($order['store_id'], $order['total_fee'], $pt, $order['domain_auth']);
					rwlog('ylb_'.date('ymd'), [
						'time' => date('Y-m-d H:i:s'),
						'out_id' => $id,
						'res' => $res,
					]);
					S('sendSpeaker_' . $id, 'ok'); //去重 写入缓存
				}
				#发送商户消息
				self::Send_MchMessage($order);
			}else{
				rwlog('ylb_'.date('ymd'), [
					'time' => date('Y-m-d H:i:s'),
					'out_id' => $id,
					'res' => '无此订单信息',
				]);
			}
		}else{
			rwlog('ylb_'.date('ymd'), [
				'time' => date('Y-m-d H:i:s'),
				'out_id' => $id,
				'res' => '非当天订单',
			]);
		}
	}



	/**
	 * 会员卡消费
	 * @param $oid
	 */
	public function sendMemberTemp($oid){
		$cache_id='member_'.$oid;
		if(S($cache_id)!=true) {
			S($cache_id, true, 3600);
			$order=M('mchMemberOrder')->where(['out_trade_no'=>$oid])->find();
            if($order){
				$openid=memberUserData($order['user_id'],'wx_id');
				if($openid) {
					$order['openid']=$openid;
					$this->sendMemberUser($order);
					$this->sendMemberMch($order);
				}
			}
		}
	}


	/**
	 * 会员卡消费-商户收款
	 * @param $order
	 */
	public function sendMemberMch($order){
		//根据门店ID获取门店的店员通知
		$map['store_id']=$order['store_id'];
		$map['domain_auth']=$order['domain_auth'];
		$map['status']=1; //只有启用状态的
		$map['role_wx_temp']=1; //只有启用通知
		$map['wx_openid']=array('neq','');
		$openid=M('MchStoreUser')->where($map)->getField('wx_openid',true);
		foreach ($openid as $key => $val) {
			$result=self::Send_Message_Status($order,'sendMemberMch',$val);
			if($result) {
				$wid = DoGetWxId('m', $order['domain_auth']);
				$store_data = Get_Store($order['store_id']);
				$oauth = &DoLoad_wechat('Receive', $wid, $order['domain_auth']);
				$first='您好，您有一笔新的会员卡收款!';
				if($order['goods_tag']){
					$remark='备注信息:  '.$order['goods_tag'].'\n';
				}
				$ad=ad_time_id('mch_notify');
				if($ad['status']==1){
					$adType=$ad['data']['url_type'];
					$url=$ad['data']['url'];
					$remark_ad=$ad['data']['data'];
					$data=[];
					$data['touser']=$val;
					$data['template_id']=GetPayConfig($order['domain_auth'], 'mch_template_id');
					if($adType==1){ //小程序
						$data['miniprogram']=[
							'appid'=>$ad['data']['appid'],
							'pagepath'=>$ad['data']['pagepath'],
						];
					}else{
						$data['url']=$url;
					}
					$data['data']=[
						'first' => array(
							'value' => $first,
							'color' => '#000000'
						),
						'keyword1' => array(
							'value' => str_replace('-','',$order['total']) . '元',
							'color' => '#000000'
						),
						'keyword2' => array(
							'value' => $store_data['name'],
							'color' => '#000000'
						),
						'keyword3' => array(
							'value' => '会员卡',
							'color' => '#000000'
						),
						'keyword4' => array(
							'value' => $order['out_trade_no'],
							'color' => '#000000'
						),
						'keyword5' => array(
							'value' => date('Y-m-d H:i:s', $order['create_time']),
							'color' => '#000000'
						),
						'remark' => array(
							'value' => $remark_ad?$remark_ad:$remark.GetPayConfig($order['domain_auth'],'mch_remark'),
							'color' => '#ff1212'
						),
					];
				}else{
					$data = array(
						'touser' => $val, //openid
						'template_id' => GetPayConfig($order['domain_auth'],'mch_template_id'),
						'data' => array(
							'first' => array(
								'value' => $first,
								'color' => '#000000'
							),
							'keyword1' => array(
								'value' => str_replace('-','',$order['total']) . '元',
								'color' => '#000000'
							),
							'keyword2' => array(
								'value' => $store_data['name'],
								'color' => '#000000'
							),
							'keyword3' => array(
								'value' => '会员卡',
								'color' => '#000000'
							),
							'keyword4' => array(
								'value' => $order['out_trade_no'],
								'color' => '#000000'
							),
							'keyword5' => array(
								'value' => date('Y-m-d H:i:s', $order['create_time']),
								'color' => '#000000'
							),
							'remark' => array(
								'value' => $remark.GetPayConfig($order['domain_auth'],'mch_remark'),
								'color' => '#000000'
							),
						),
					);
				}
				$res = $oauth->sendTemplateMessage($data);
				$cacheId=$order['out_trade_no'].$val.'sendMemberMch';
				if ($res['errcode'] == 0) {
					S($cacheId,true,86400);
				}
			}
		}
	}

	/***
	 * 会员卡消费-会员模板消息
	 * @param $order
	 */
	public function  sendMemberUser($order){
		$result=self::Send_Message_Status($order,'sendMemberUser',$order['openid']);
		if($result){
			$wid= DoGetWxId('',$order['domain_auth']);
			$store_data=Get_Store($order['store_id']);
			$oauth = &DoLoad_wechat('Receive',$wid,$order['domain_auth']);
			$first='您好，您有一笔新的会员卡消费!';
			$ad=ad_time_id('pay_notify');
			$memberTotal=memberOrderTotal($order['user_id']);
			if($ad['status']==1){
				$adType=$ad['data']['url_type'];
				$url=$ad['data']['url'];
				$remark_ad=$ad['data']['data'];
				$data=[];
				$data['touser']=$order['openid'];
				$data['template_id']=GetPayConfig($order['domain_auth'], 'pay_template_id');
				if($adType==1){ //小程序
					$data['miniprogram']=[
						'appid'=>$ad['data']['appid'],
						'pagepath'=>$ad['data']['pagepath'],
					];
				}else{
					$data['url']=$url;
				}
				$data['data'] = [
					'first' => array(
						'value' => $first,
						'color' => '#000000'
					),
					'keyword1' => array(
						'value' => $order['out_trade_no'],
						'color' => '#000000'
					),
					'keyword2' => array(
						'value' => str_replace('-','',$order['total']) . '元',
						'color' => '#000000'
					),
					'keyword3' => array(
						'value' => $store_data['name'],
						'color' => '#000000'
					),
					'keyword4' => array(
						'value' => date('Y-m-d H:i:s', $order['create_time']),
						'color' => '#000000'
					),
					'remark' => array(
						'value' => $memberTotal?'会员卡余额:'.$memberTotal.'元\n':''.($remark_ad?$remark_ad:''),
						'color' => '#ff1212'
					),
				];
			}else {
				$data = array(
					'touser' => $order['openid'], //openid
					'template_id' => GetPayConfig($order['domain_auth'], 'pay_template_id'),
					'data' => array(
						'first' => array(
							'value' => $first,
							'color' => '#000000'
						),
						'keyword1' => array(
							'value' => $order['out_trade_no'],
							'color' => '#000000'
						),
						'keyword2' => array(
							'value' => str_replace('-','',$order['total']) . '元',
							'color' => '#000000'
						),
						'keyword3' => array(
							'value' => $store_data['name'],
							'color' => '#000000'
						),
						'keyword4' => array(
							'value' => date('Y-m-d H:i:s', $order['create_time']),
							'color' => '#000000'
						),
						'remark' => array(
							'value' => $memberTotal?'会员卡余额:'.$memberTotal.'元\n':'',
							'color' => '#ff1212'
						),
					),
				);
			}
			$res= $oauth->sendTemplateMessage($data);
			$cacheId=$order['out_trade_no'].$order['openid'].'sendMemberUser';
			if ($res['errcode'] == 0) {
				S($cacheId,true,86400);
			}
		}
	}


    #模板消息入口
    public function Message(){
        $data=M('alimsn')->where(array('messageId'=>$this->data['MessageId']))->find();
        $task_data=unserialize($data['task_data']);
        if($data['status']!=1){
            if($task_data['type']=='tx_card'){
                self::Send_TxMessage($task_data['order_id']);
            }elseif($task_data['type']=='sft_tx'){
                self::Send_SftTxMessage($task_data['order_id']);
            }else {
                self::Send_Message($task_data['order_id']);
            }
        }
        #发送打印请求
        self::Send_print($task_data['order_id']);
    }

    #发送盛付通提现模板消息
    public function Send_SftTxMessage($id){
        //rwlog('seller',$id);
        #提现只发给注册人
        $Tx=M('MchSftTx')->where(array('tx_order'=>$id,'status'=>'18'))->find();
        $seller=M('MchSeller')->where(array('id'=>$Tx['cid'],'domain_auth'=>$Tx['domain_auth']))->find();
        $order=array(
         'out_trade_no'=>$Tx['tx_order'],
        );
        //rwlog('seller',$seller);
        $result=self::Send_Message_Status($order,'Send_MchTxMessage',$seller['mch_wx_openid']);
        //rwlog('seller',$result);
        if($result) {
            $wid = DoGetWxId('m', $Tx['domain_auth']);
            $oauth = &DoLoad_wechat('Receive', $wid, $Tx['domain_auth']);
            if (GetPayConfig($Tx['domain_auth'], 'tx_first')) {
                $first = GetPayConfig($Tx['domain_auth'], 'tx_first');
            } else {
                $first = '您好，您有一笔新的提现信息!';
            }
            #获取提现银行卡信息
            $alleys=M('MchSellerAlleys')->where(array('cid'=>$Tx['cid'],'alleys_type'=>'Sftpays'))->find();
            //$tx_bank=json_decode($TxData['tx_data'],true);
            #根据银行编码 取总行名称
            $bank=M('BanksDataNew')->where(array('banking' => $alleys['mch_bank_cid']))->getField('bank');

            $data = array(
                'touser' => $seller['mch_wx_openid'], //openid
                'template_id' => GetPayConfig($Tx['domain_auth'], 'tx_template_id'),
                'data' => array(
                    'first' => array(
                        'value' => $first,
                        'color' => '#000000'
                    ),
                    'keyword1' => array(
                        'value' => $Tx['tx_order'],
                        'color' => '#000000'
                    ),
                    'keyword2' => array(
                        'value' => date('Y-m-d H:i:s'),
                        'color' => '#000000'
                    ),
                    'keyword3' => array(
                        'value' => $Tx['tx_total'].'元',
                        'color' => '#000000'
                    ),
                    'keyword4' => array(
                        'value' => $bank.'尾号('.substr($alleys['mch_bank_cid'], -4).')',
                        'color' => '#000000'
                    ),
                    'remark' => array(
                        'value' => GetPayConfig($Tx['domain_auth'], 'tx_remark'),
                        'color' => '#000000'
                    ),
                ),
            );
            //rwlog('seller',$data);
            $res = $oauth->sendTemplateMessage($data);
            if ($res['errcode'] == 0) {
                $arr = array(
                    'send_id' => $Tx['tx_order'],
                    'send_openid' => $seller['mch_wx_openid'],
                    'send_type' => 'Send_MchTxMessage',
                    'send_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                    'send_time' => date('Y-m-d H:i:s'),
                    'send_rel' => json_encode($res),
                    'status' => 1
                );
                M('SendtemplateLog')->add($arr);
            } else {
                $arr = array(
                    'send_id' => $Tx['tx_order'],
                    'send_openid' => $seller['mch_wx_openid'],
                    'send_type' => 'Send_MchTxMessage',
                    'send_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                    'send_time' => date('Y-m-d H:i:s'),
                    'send_rel' => $oauth->errMsg,
                    'status' => 0
                );
                M('SendtemplateLog')->add($arr);
            }
        }

    }



    #发送提现模板消息
    public function Send_TxMessage($id){
        $map['out_trade_no']=$id;
        $map['status']=1;
        $order=M('MchOrders')->where($map)->find();
        #发送商户消息
        self::Send_MchTxMessage($order);
    }





    #发送个人模板消息
    public function Send_PerMessage($order){
       $result=self::Send_Message_Status($order,'Send_PerMessage',null);
       if($result){
           $wid= DoGetWxId('',$order['domain_auth']);
           $store_data=Get_Store($order['store_id']);
           $oauth = &DoLoad_wechat('Receive',$wid,$order['domain_auth']);
           if(GetPayConfig($order['domain_auth'],'pay_first')){
               $first=GetPayConfig($order['domain_auth'],'pay_first');
           }else{
               $first='您好，您的微信支付已成功';
           }
           $ad=ad_time_id('pay_notify');
           if($ad['status']==1){
           	   $adType=$ad['data']['url_type'];
			   $url=$ad['data']['url'];
			   $remark_ad=$ad['data']['data'];
           	   $data=[];
           	   $data['touser']=$order['sub_openid'];
           	   $data['template_id']=GetPayConfig($order['domain_auth'], 'pay_template_id');
           	   if($adType==1){ //小程序
                   $data['miniprogram']=[
                   	 'appid'=>$ad['data']['appid'],
					 'pagepath'=>$ad['data']['pagepath'],
				   ];
			   }else{
				   $data['url']=$url;
			   }
               $data['data'] = [
				   'first' => array(
					   'value' => $first,
					   'color' => '#000000'
				   ),
				   'keyword1' => array(
					   'value' => $order['out_trade_no'],
					   'color' => '#000000'
				   ),
				   'keyword2' => array(
					   'value' => $order['total_fee'] . '元',
					   'color' => '#000000'
				   ),
				   'keyword3' => array(
					   'value' => $store_data['name'],
					   'color' => '#000000'
				   ),
				   'keyword4' => array(
					   'value' => date('Y-m-d H:i:s', $order['createtime']),
					   'color' => '#000000'
				   ),
				   'remark' => array(
					   'value' => $remark_ad?$remark_ad:GetPayConfig($order['domain_auth'], 'pay_remark'),
					   'color' => '#ff1212'
				   ),
			   ];
           }else {
               $data = array(
                   'touser' => $order['sub_openid'], //openid
                   'template_id' => GetPayConfig($order['domain_auth'], 'pay_template_id'),
                   'data' => array(
                       'first' => array(
                           'value' => $first,
                           'color' => '#000000'
                       ),
                       'keyword1' => array(
                           'value' => $order['out_trade_no'],
                           'color' => '#000000'
                       ),
                       'keyword2' => array(
                           'value' => $order['total_fee'] . '元',
                           'color' => '#000000'
                       ),
                       'keyword3' => array(
                           'value' => $store_data['name'],
                           'color' => '#000000'
                       ),
                       'keyword4' => array(
                           'value' => date('Y-m-d H:i:s', $order['createtime']),
                           'color' => '#000000'
                       ),
                       'remark' => array(
                           'value' => GetPayConfig($order['domain_auth'], 'pay_remark'),
                           'color' => '#000000'
                       ),
                   ),
               );
           }
           $res= $oauth->sendTemplateMessage($data);
		   $cacheId=$order['out_trade_no'].$order['sub_openid'].'Send_PerMessage';
		   if ($res['errcode'] == 0) {
			   S($cacheId,true,86400);
		   }
       }
    }

    #发送商户模板消息
    public function Send_MchMessage($order){
        //根据门店ID获取门店的店员通知
        $map['store_id']=$order['store_id'];
        $map['domain_auth']=$order['domain_auth'];
        $map['status']=1; //只有启用状态的
        $map['role_wx_temp']=1; //只有启用通知
        $map['wx_openid']=array('neq','');
        $openid=M('MchStoreUser')->where($map)->getField('wx_openid',true);
        foreach ($openid as $key => $val) {
            $result=self::Send_Message_Status($order,'Send_MchMessage',$val);
            if($result) {
                $wid = DoGetWxId('m', $order['domain_auth']);
                $store_data = Get_Store($order['store_id']);
                $oauth = &DoLoad_wechat('Receive', $wid, $order['domain_auth']);
                if(GetPayConfig($order['domain_auth'],'mch_first')){
                    $first=GetPayConfig($order['domain_auth'],'mch_first');
                }else{
                    $first='您好，您有一笔新的收款!';
                }
                if($order['goods_tag']){
                    $remark='备注信息:  '.$order['goods_tag'].'\n';
                }
                $ad=ad_time_id('mch_notify');
                if($ad['status']==1){
					$adType=$ad['data']['url_type'];
					$url=$ad['data']['url'];
					$remark_ad=$ad['data']['data'];
					$data=[];
					$data['touser']=$val;
					$data['template_id']=GetPayConfig($order['domain_auth'], 'mch_template_id');
					if($adType==1){ //小程序
						$data['miniprogram']=[
							'appid'=>$ad['data']['appid'],
							'pagepath'=>$ad['data']['pagepath'],
						];
					}else{
						$data['url']=$url;
					}
					$data['data']=[
						'first' => array(
							'value' => $first,
							'color' => '#000000'
						),
						'keyword1' => array(
							'value' => $order['total_fee'] . '元',
							'color' => '#000000'
						),
						'keyword2' => array(
							'value' => $store_data['name'],
							'color' => '#000000'
						),
						'keyword3' => array(
							'value' => pays_type($order['service'],1),
							'color' => '#000000'
						),
						'keyword4' => array(
							'value' => $order['out_trade_no'],
							'color' => '#000000'
						),
						'keyword5' => array(
							'value' => date('Y-m-d H:i:s', $order['createtime']),
							'color' => '#000000'
						),
						'remark' => array(
							'value' => $remark_ad?$remark_ad:$remark.GetPayConfig($order['domain_auth'],'mch_remark'),
							'color' => '#ff1212'
						),
					];
                }else{
                    $data = array(
                        'touser' => $val, //openid
                        'template_id' => GetPayConfig($order['domain_auth'],'mch_template_id'),
                        'data' => array(
                            'first' => array(
                                'value' => $first,
                                'color' => '#000000'
                            ),
                            'keyword1' => array(
                                'value' => $order['total_fee'] . '元',
                                'color' => '#000000'
                            ),
                            'keyword2' => array(
                                'value' => $store_data['name'],
                                'color' => '#000000'
                            ),
                            'keyword3' => array(
                                'value' => pays_type($order['service'],1),
                                'color' => '#000000'
                            ),
                            'keyword4' => array(
                                'value' => $order['out_trade_no'],
                                'color' => '#000000'
                            ),
                            'keyword5' => array(
                                'value' => date('Y-m-d H:i:s', $order['createtime']),
                                'color' => '#000000'
                            ),
                            'remark' => array(
                                'value' => $remark.GetPayConfig($order['domain_auth'],'mch_remark'),
                                'color' => '#000000'
                            ),
                        ),
                    );
                }
                $res = $oauth->sendTemplateMessage($data);
				$cacheId=$order['out_trade_no'].$val.'Send_MchMessage';
				if ($res['errcode'] == 0) {
					S($cacheId,true,86400);
				}
            }
        }
    }

    #发送商户提现模板消息
    public function Send_MchTxMessage($order){
            //根据门店ID获取门店的店员通知
            $map['store_id']=$order['store_id'];
            $map['domain_auth']=$order['domain_auth'];
            $map['status']=1; //只有启用状态的
            $map['role_wx_temp']=1; //只有启用通知
            $map['wx_openid']=array('neq','');
            $openid=M('MchStoreUser')->where($map)->getField('wx_openid',true);
            foreach ($openid as $key => $val) {
                $result=self::Send_Message_Status($order,'Send_MchTxMessage',$val);
                if($result) {
                    $TxData=M('MchOrdersTx')->where(array('domain_auth'=>$order['domain_auth'],'out_trade_no'=>$order['out_trade_no'],'status'=>1))->find();
                    if($TxData) {
                        $wid = DoGetWxId('m', $order['domain_auth']);
                        $oauth = &DoLoad_wechat('Receive', $wid, $order['domain_auth']);
                        if (GetPayConfig($order['domain_auth'], 'tx_first')) {
                            $first = GetPayConfig($order['domain_auth'], 'tx_first');
                        } else {
                            $first = '您好，您有一笔新的提现信息!';
                        }
                        #提现银行卡信息
                        $tx_bank=json_decode($TxData['tx_data'],true);
                        #根据银行编码 取总行名称
                        $bank=M('BanksDataNew')->where(array('banking' => $tx_bank['accBankNo']))->getField('bank');
                        $data = array(
                            'touser' => $val, //openid
                            'template_id' => GetPayConfig($order['domain_auth'], 'tx_template_id'),
                            'data' => array(
                                'first' => array(
                                    'value' => $first,
                                    'color' => '#000000'
                                ),
                                'keyword1' => array(
                                    'value' => $order['out_trade_no'],
                                    'color' => '#000000'
                                ),
                                'keyword2' => array(
                                    'value' => date('Y-m-d H:i:s'),
                                    'color' => '#000000'
                                ),
                                'keyword3' => array(
                                    'value' => $TxData['tx_total'].'元',
                                    'color' => '#000000'
                                ),
                                'keyword4' => array(
                                    'value' => $bank.'尾号('.substr($tx_bank['acctNo'], -4).')',
                                    'color' => '#000000'
                                ),
                                'remark' => array(
                                    'value' => GetPayConfig($order['domain_auth'], 'tx_remark'),
                                    'color' => '#000000'
                                ),
                            ),
                        );
                        $res = $oauth->sendTemplateMessage($data);
						$cacheId=$order['out_trade_no'].$val.'Send_MchTxMessage';
                        if ($res['errcode'] == 0) {
                        	S($cacheId,true,86400);
                        }
                    }
             }
         }
    }


    #判断消息的唯一性 禁止重复发送
    public function  Send_Message_Status($order,$type,$openid){
		if($type=='Send_MchMessage'){
			$send_openid = $openid;
		}else if($type=='Send_MchTxMessage'){
			$send_openid = $openid;
		}else {
			$send_openid = $order['sub_openid'];
		}
    	$cacheId=$order['out_trade_no'].$send_openid.$type;
    	if(S($cacheId)==true||S($cacheId)==1){
			return false;
		}else{
			return true;
		}
    }



    #打印订单
    public function Send_print($order_id){
        #根据订单号取门店打印机配置
        $where['out_trade_no']=$order_id;
        $where['status']=1;
        $Store=M('MchOrders')->where($where)->field('mid,store_id,service,transaction_id,out_trade_no,total_fee,createtime,status')->find();
        #根据信息取打印配置
        $print=M('MchStorePrint')->where(array('sid'=>$Store['mid'],'store_id'=>$Store['store_id']))->find();
        #打印机配置项
        $config=unserialize($print['data']);
        if($print['status']==1) {
            #只有打印机开启的时候打印
            Vendor('print');
            $print = new \Yprint();

            #打印模板 开始
            $print_top= $config['print_top'];//门头
            $print_sh= $config['print_mchname'];//收款商户
            $print_bottom= str_replace("||","\r\n",$config['print_footer']) ;//底部显示内容
            if($config['print_num']>1){
                $msg.="<MN>".$config['print_num']."</MN>";
                $msg.= '<center>@@2'.$print_top.'</center>\r\n';
            }else{
                $msg.= '<center>@@2'.$print_top.'</center>\r\n';
            }
            $msg.= '※※※※※※※※※※※※※※※※\r\n\r\n'; //间隔
            $msg.= '收款商户：'.$print_sh.'\r\n'; //打印内容
            $msg.= '支付方式：'.pays_type($Store['service'],1).'\r\n';
            $msg.= '支付结果：'.pays_status($Store['status'],1).'\r\n';
            $msg.= '支付时间: '.date('Y-m-d H:i:s',$Store['createtime']).'\r\n\r\n'; //打印内容
            $msg.= '-----------订单详情-------------\r\n\r\n'; //打印内容
            $msg.= '商户单号: \r\n'; //打印内容
            $msg.= ''.$Store['out_trade_no'].'\r\n'; //打印内容
            if(!empty($Store['transaction_id'])) {
                $msg .= '交易单号: \r\n'; //打印内容
                $msg .= '' . $Store['transaction_id'] . '\r\n'; //打印内容
            }
            $msg.= '支付总金额: \r\n'; //打印内容
            $msg.= '<center>@@2'.$Store['total_fee'].'元</center>\r\n'; //打印内容
            $msg.= '-----------------------------\r\n\r\n'; //打印内容
            $msg.='\r\n'.$print_bottom.'\r\n';
            #打印模板 结束


            $content = urlencode($msg);
            $apiKey = $config['print_api'];
            $msign = $config['print_key'];
            #判断是否打印过
            $map['send_id'] = $order_id;
            $map['send_type'] = 'Send_print';
            $map['status'] = 1;
            $status = M('SendtemplateLog')->where($map)->count();
            if (!$status) {
                #没有打印过 打印订单
                $res = $print->action_print($config['print_id'],$config['print_zd'], $content, $apiKey, $msign);
                $res=json_decode($res,true);
                /*rwlog('print',$Store);
                rwlog('print',$config);
                rwlog('print',$content);
                rwlog('print',$res);*/
                //根据结果处理
                if($res['state']==1){
                    $status = 1;
                }else{
                    $status = 0;
                }
                $add = array(
                    'send_id' => $order_id,
                    'send_openid'=>'print_no',
                    'send_type' => 'Send_print',
                    'send_data'=>$content,
                    'status' => $status,
                    'send_time'=>date('Y-m-d H:i:s'),
                    'send_rel'=>serialize($res),
                );
                $_map['send_id'] = $order_id;
                $_map['send_type'] = 'Send_print';
                $count=M('SendtemplateLog')->where($_map)->count();
                if($count){
                    #如果存在此消息  直接更新
                    M('SendtemplateLog')->where($_map)->save($add);
                }else{
                    #没记录 增加
                    M('SendtemplateLog')->add($add);
                }
            }
        }


    }

}