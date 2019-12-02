<?php
namespace Pays\Controller;
use Pays\Controller\InitBaseController;

class QrcodeController extends InitBaseController {


    #快捷支付
    public function quick_gateway(){
        $data=I('post.');
        if($data['total']&&$data['sid']&&$data['id']) {
            if(empty($_SESSION['mch']['id'])){
                $url=U('Mch/Login/index',array('code'=>'quick','quick_data'=>Xencode(json_encode($data))));
                $this->success('未登录,登录后跳转快捷支付页面',$url);
            }else{
                $url=U('Mch/Index/quick',array('quick_data'=>Xencode(json_encode($data))));
                $this->success('已登录,跳转快捷支付页面',$url);
            }
        }else{
            $this->error('通信失败!请联系管理!');
        }
    }

	/**
	 * API接口支付同步返回参数生成
	 * 只有支付成功的才有参数
	 */
    public function  apiDataSign(){
    	if(IS_POST) {
    		$post=I('post.');
			$url=$post['call_url'];
			$res = M('MchOrders')->where(['out_trade_no' => $post['oid']])->find();
			$terminal = M('MchTerminal')->where(['appid' => $post['appid']])->field('appid,appkey')->find();
			$data = [
				'appid' => $terminal['appid'],
				'method' => $post['method'],
				'status' => 1, //订单状态 0未支付/支付中 1支付成功 2退款成功
				'out_trade_no' => $res['out_trade_no'], //订单号
				'transaction_id' => $res['transaction_id'], //官方或三方订单号
				'total_fee' => $res['total_fee'] * 100, //交易金额 转换为分单位
				'create_time' => date('Y-m-d H:i:s', $res['createtime']), //创建时间
				'nonce_str' => uniqid(), //随机字符串
			];
			$data['sign'] = ApiSign($data, $terminal['appkey']); //签名
			if (strstr($url, '?')) {
				if (substr($url, -1) != '?')
					$url = $url . '&';
			} else {
				$url = $url . '?';
			}
			$this->success($url . http_build_query($data));
		}else{
    		$this->error('非法操作');
		}
	}

    #支付页面
    public function index(){
        $Codes=Xdecode(I('get.codes'));
        if(I('get.codes')=='ApiPayssss'){ //废弃
             $cacheKey=I('get.k');
			$dataArr=S($cacheKey);
			//缓存是否存在
			if (!$dataArr){$this->error('该订单已过期,请重新发起订单来支付','',888);}
        	if($dataArr['payType']=='wx') {
				//获取openid
				$token = $this->_oauth('base');
				if (!$token['openid']) {
					$_token = $this->_oauth('base');
					if (!$_token['openid']) {
						redirect('/Pay/ApiPay?Debug=1&k=' . I('get.k'));
					} else {
						$dataArr['openid'] = $_token['openid'];
					}
				} else {
					$dataArr['openid'] = $token['openid'];
				}
			}elseif ($dataArr['payType']=='ali'){
				$app_id=GetPayConfigs('ali_appid');
				$auth_code=I('get.auth_code');
				if (!$auth_code){//获取auth_code
					$url="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=".$app_id."&scope=auth_base&redirect_uri=".urlencode(get_url());
					redirect($url);
				}else {
					Vendor('alipay_sdk.AlipayAop');
					$Ali_Aop = new \AlipayAop();
					$msg = $Ali_Aop->Alipay_oauth($auth_code,$app_id);
					$dataArr['openid']=$msg;
				}

			}else{
				$this->error('请用微信或支付宝支付!','',888);
			}
			//输出页面支付
			C('TOKEN_ON',false);

			//dump($dataArr);
			$this->assign($dataArr);

			//首次读取后即删除缓存
			S($cacheKey,null);
			$this->display('api_pay');
		}else {
			$user_agent = USER_AGENT();
			#根据Codes获取商户信息
			$map['domain_auth'] = domain_auth();
			$map['id'] = GetCodeData($Codes, 'store_id');
			$store = M('MchStore')->where($map)->find();
			if (!$store) {
				$this->error('未找到当前商户信息!如有疑问!请联系您的服务专员!', '', 888);
			}
			if ($store['status'] != 1) {
				$this->error('当前门店未开启支付功能!ERROR: Store Not Pay', '', 888);
			}
			$pay_type = unserialize($store['pay_type']);
			unset($store['pay_type']);
			unset($store['domain_auth']);
			#取当前商户的通道是否配置
			$mch = M('MchSeller')->where(array('id' => $store['sid'], 'domain_auth' => domain_auth()))->find();

			$Alley = M('MchSellerAlleys')->where(array('alleys_type' => $mch['wx_alleys'], 'cid' => $mch['id'], 'domain_auth' => domain_auth()))->find();
			//获取商户独立配置公众号ID
			$pay_where = array('alleys' => $mch['wx_alleys'], 'mch_id' => $mch['id'], 'domain_auth' => domain_auth());
			$pay_wxid = M('MchAppid')->where($pay_where)->getField('pay_wxid');
			if (!$pay_wxid || $pay_wxid == 0) {
				$wid = '';
			} else {
				$wid = $pay_wxid;
			}
			$get_openid = self::user_openid($mch['wx_alleys'], $Alley['mch_id'], $wid, $Alley['mch_key']);

			#门店自定义配置
			if ($user_agent == 'ali') {
				#判断是否开启支付宝
				if ($pay_type['data_alipay'] != 1) {
					$this->error('当前门店未开启支付宝支付功能!', '', 888);
				}
				if ($pay_type['data_aliconfig'] != 1) {
					if ($pay_type['data_aliurl']) {
						redirect($pay_type['data_aliurl']);
					} else {
						$this->error('当前门店支付宝类型非通道支付,但未配置自定义链接地址,无法完成下面的请求!', '', 888);
					}
				}
				if (!$mch['ali_alleys']) {
					$mch_alley = 0;
				} else {
					$mch_alley = 1;
				}

			}
			if ($user_agent == 'wx') {
				#判断是否开启支付宝
				if ($pay_type['data_wxpay'] != 1) {
					$this->error('当前门店未开启微信支付功能!', '', 888);
				}
				if ($pay_type['data_wxconfig'] != 1) {
					if ($pay_type['data_wxurl']) {
						redirect($pay_type['data_wxurl']);
					} else {
						$this->error('当前门店微信类型非通道支付,但未配置自定义链接地址,无法完成下面的请求!', '', 888);
					}
				}
				if (!$mch['wx_alleys']) {
					$mch_alley = 0;
				} else {
					$mch_alley = 1;
				}
			}


			$assign = array(
				'openid' => $get_openid,
				'user_agent' => $user_agent,
				'store' => $store,
				'mch_alley' => $mch_alley
			);
			//dump($assign);
			$this->assign($assign);

			$temp=memberTemp($store['sid']);
			$status=($temp&&$temp['status']==1)?1:0;
			if($status){
				$memberUser=memberUser($store['sid'],$get_openid);
				session('member.user',$memberUser);
				session('member.mid',$store['sid']);
				session('member.store_id',$store['id']);
				$this->display('new_index_member');
			}else{
				$this->display('new_index_a');
			}


//			if ($Codes != 'P77777777') {
//				$this->display('new_index_a');
//			} else {
//				$memberUser=memberUser($store['sid'],$get_openid);
//				session('member.user',$memberUser);
//				session('member.mid',$store['sid']);
//				session('member.store_id',$store['id']);
//				$this->display('new_index_member');
//			}
		}
    }


    #获取用户基本信息
    public function  user_openid($type=null,$mch_id=null,$wid=null,$key=null){
        $User_Agent=USER_AGENT();
        #目前只支持这三种
        if($User_Agent=='qq'){
            #QQ扫描
            $this->error('暂未开通QQ钱包支付,请用微信或支付宝支付!','',888);
        }elseif($User_Agent=='baidu'){
            $this->error('暂未开通百度钱包支付,请用微信或支付宝支付!','',888);
        }elseif($User_Agent=='ali'){
            #支付宝扫描
            //$app_id='2017050507132270';
            $app_id=GetPayConfigs('ali_appid');
            $auth_code=I('get.auth_code');
            if (!$auth_code){//获取auth_code
                $url="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=".$app_id."&scope=auth_base&redirect_uri=".urlencode(get_url());
                redirect($url);
            }else {
                Vendor('alipay_sdk.AlipayAop');
                $Ali_Aop = new \AlipayAop();
                $msg = $Ali_Aop->Alipay_oauth($auth_code,$app_id);
                //rwlog('testali',$msg);

                return $msg;

            }
        }else{
            #微信扫描
            if($type=='Qfpay'){
                //前方好近接口获取openid
                $_QfConfig=array(
                    'AppCode'=>'1783D1FAF82A40E889444B46EB956278',
                    'SignKey'=>'B48002D897154DF3A8E3E5ED63B46231',
                );
                if(I('get.code')){
                    $arr = array(
                        'code' => I('get.code'),
                        'mchid'=>$mch_id
                    );
                    //$arr['sign'] =self::data_sign($arr);
                    $url='https://openapi.qfpay.com/tool/v1/get_weixin_openid?'.http_build_query($arr);
                   // rwlog('qf_url',$url);
                    $res=qf_get_curl_calls($url,$arr,true,$_QfConfig);
                    $res=json_decode($res,true);
                    if($res['respcd']=='0000'&&$res['openid']){
                        return $res['openid'];
                    }else{
                      redirect('/Pay/' . I('get.codes'));
                       // dump($res);
                    }
                }else {
                    $arr = array(
                        'app_code' => $_QfConfig['AppCode'],
                        'redirect_uri' => get_url(),
                        'mchid'=>$mch_id
                    );
                    $arr['sign'] =qf_data_sign($arr,$_QfConfig['SignKey']);
                    $url='https://openapi.qfpay.com/tool/v1/get_weixin_oauth_code?'.http_build_query($arr);
                    redirect($url);
                }
            }elseif ($type=='Wspay'){
                if(!I('get.openid')) {
                    R('PWspay/getWxOpenId', [$mch_id, $key, get_url()]);
                }else{
                    $openid=I('get.openid');
                    if (!$openid) {
                        redirect('/Pay/' . I('get.codes'));
                    } else {
                        return $openid;
                    }
                }
               // dump(I('get.'));
            }else {
                $token = $this->_oauth('base',$wid);
                if (!$token['openid']) {
                    $_token = $this->_oauth('base',$wid);
                    if (!$_token['openid']) {
                        //$this->error('获取用户信息失败!请重新扫码!','',888);
                        //dump($_GET);
                        redirect('/Pay/' . I('get.codes'));
                    } else {
                        return $_token['openid'];
                    }
                } else {
                    return $token['openid'];
                }
            }

        }

    }

    #微信JSAPI
    public  function jsapi(){
        $wid= GetWxId('p');
        // 创建SDK实例
        $script = &  load_wechat('Script',$wid);
        $options = $script->getJsSign(get_url(), $timestamp, $noncestr, $appid);
        if($options===FALSE){
            // 接口失败的处理
            return $script->errMsg;
        }else{

            unset($options['jsApiList']);
            $options['jsApiList']=array('chooseWXPay');
            return $options;
        }
    }





}


