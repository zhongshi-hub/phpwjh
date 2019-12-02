<?php

namespace Mch\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public function _initialize()
    {
        $domain = domain_rel();
        $_domain = M('domain_auth')->where(array('web_domain' => $domain))->find();
        $this->assign('_domain', $_domain);
        if (!$_domain) {
            $content = '服务未授权!请联系专员!';
            die($content);
        }
        if ($_domain['status'] != 1) {
            $content = '服务已被停止!请联系专员!';
            die($content);
        }
    }


    #代理登录
    public function agent()
    {
        if (IS_POST) {
            $data = I('post.');
            $map['user_phone'] = $data['user_tel'];
            $map['domain_auth'] = domain_auth();
            $res = M('MchAgent')->where($map)->find();
            if ($res) {
                if (empty($res['password'])) {
                    #未配置密码
                    if (md5($data['user_pass']) == md5('asd123456')) { #初始密码
                        $this->error('当前密码为初始密码!请重置密码后登录!', U('agent_pass'));
                    } else {
                        $this->error('手机号或密码错误!');
                    }
                } else {
                    #已配置密码
                    $where['password'] = md5($data['user_pass']);
                    $password = M('MchAgent')->where($map)->where($where)->find();
                    if ($password) {
                        $_SESSION['ag'] = array(
                            'id' => $password['id'],
                            'pid' => $password['pid'],
                            'user_name' => $password['user_name'],
                            'user_phone' => $password['user_phone'],
                        );
                        $this->success('登陆成功', U('Agent/index'));
                    } else {
                        $this->error('手机号或密码错误!');
                    }
                }
            } else {
                $this->error('账户或密码错误!');
            }
        } else {
            if (!empty($_SESSION['ag'])) {
                redirect(U('Agent/index'));
            }
            $this->display();
        }
    }

    #代理找回密码
    public function agent_pass()
    {
        if (IS_POST) {
            $data = I('post.');
            #密码判断
            $pass = md5($data['new_pass']);
            $pass_rese = md5($data['new_pass_rese']);
            if ($pass != $pass_rese) {
                $this->error('两次输入的密码不一致');
            }

            if ($pass == md5('123456')) {
                $this->error('您输入的密码和初始密码一致!请更换密码!');
            }

            if (mb_strlen($data['new_pass']) < 6) {
                $this->error('密码不能小于6位!请输入6位以上的密码!');
            }
            #验证信息
            $where['cardsn'] = 'agent_verify';
            $where['tel'] = $data['user_tel'];
            $where['verify'] = $data['verify'];
            $where['domain_auth'] = domain_auth();
            $_res = M('MchVerify')->where($where)->find();
            $_c = time();
            $_e = $_res['createtime'];
            $minute = floor(($_c - $_e) % 86400 / 60);
            $out_times = 10;
            if ($_res) {
                if ($minute > $out_times) {
                    $this->error('验证码已过期,请重新获取');
                } else {
                    #信息都正确
                    $set = array(
                        'domain_auth' => domain_auth(),
                        'user_phone' => $data['user_tel'],
                    );
                    $save = M('MchAgent')->where($set)->save(array('password' => $pass));
                    if ($save) {
                        $this->success('密码修改成功', U('agent'));
                    } else {
                        $this->error('修改失败!您输入的密码和现有的密码一致!');
                    }
                }
            } else {
                $this->error('验证码信息错误');
            }


        } else {
            $this->display();
        }
    }

    #发送验证码
    public function agent_rest()
    {
        $user_phone = I('post.tel');
        $agent = M('MchAgent')->where(array('user_phone' => $user_phone, 'domain_auth' => domain_auth()))->find();
        if ($agent) {

            //验证码  随机生成六位验证码
            $_data['verify'] = RandStr(6);
            //创建时间
            $_data['createtime'] = time();
            $_data['tel'] = $agent['user_phone'];
            $_data['cardsn'] = 'agent_verify';
            $_data['domain_auth'] = domain_auth();
            //发送验证码
            $sms = ALI_SMS();
            $sms_data = array(
                'mobile' => $_data['tel'], #接收手机号
                'code' => $sms['sms_pass'],#验证码模板ID
                'sign' => $sms['sms_sign'], #模板签名 必需审核通过
                'param' => json_encode(array(
                    'code' => $_data['verify'], #验证码
                    'product' => 'Agent',#模板变量
                )),
            );

            if (sms_api() == 1) { #用阿里云通信接口
                $re = new_ali_sms($sms_data);
                if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = 'agent_verify';
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['Code'] . ".错误消息:" . $re['Message'];
                    $this->error($info);
                }
            }else {
                $AliSms = new \Think\Alisms($sms);
                $re = $AliSms->sms_send($sms_data);
                if ($re['err_code'] == 0 && $re['success'] == true) {
                    $_where['tel'] = $_data['tel'];
                    $_where['cardsn'] = 'agent_verify';
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['code'] . ".错误消息:" . $re['sub_msg'];
                    $this->error($info);
                }
            }
        } else {
            $this->error('未获取到业务信息!发送失败!请检查手机号是否正确!');
        }

    }


    #门店登录
    public function store()
    {
        if (IS_POST) {
            $data = I('post.');
            #判断手机号商户是否存在
            $StoreUser = M('MchStoreUser')->where(array('phone' => $data['user_tel'], 'domain_auth' => domain_auth()))->find();
            //dump($StoreUser);
            if ($StoreUser) {
                #判断密码
                if (md5($data['user_pass']) == md5('123456') && empty($StoreUser['password'])) {
                    //$this->error('您使用的密码为初始密码!请重置密码后使用新密码登陆!', U('store_pass'));

                    #是否绑定多个门店
                    $count = M('MchStoreUser')->where(array('phone' => $data['user_tel'], 'domain_auth' => domain_auth()))->field('store_id')->distinct(true)->select();
                    if (count($count) == 1) {
                        #存在一个 直接登录成功
                        $_SESSION['store'] = array(
                            'sid' => $StoreUser['sid'],
                            'store_id' => $StoreUser['store_id'],
                            'user_tel' => $data['user_tel'],
                        );
                        $this->success('登陆成功!您的密码为初始密码!建议登录后修改密码!', U('Store/index'));
                    } else {
                        $_SESSION['store'] = array(
                            'user_tel' => $data['user_tel'],
                        );
                        $this->success('登陆成功!您的密码为初始密码!建议登录后修改密码!', U('Store/lists'));
                    }

                } else {
                    #判断密码是否一致
                    if (md5($data['user_pass']) == $StoreUser['password']) {
                        #是否绑定多个门店
                        $count = M('MchStoreUser')->where(array('phone' => $data['user_tel'], 'domain_auth' => domain_auth()))->count();
                        if ($count == 1) {
                            #存在一个 直接登录成功
                            $_SESSION['store'] = array(
                                'sid' => $StoreUser['sid'],
                                'store_id' => $StoreUser['store_id'],
                                'user_tel' => $data['user_tel'],
                            );
                            $this->success('登陆成功', U('Store/index'));
                        } else {
                            $_SESSION['store'] = array(
                                'user_tel' => $data['user_tel'],
                            );
                            $this->success('登陆成功', U('Store/lists'));
                        }

                    } else {
                        $this->error('您输入的密码错误!');
                    }
                }


            } else {
                $this->error('店员不存在!请输入正确的手机号!');
            }
        } else {
            #获取用户openid
            $wid = GetWxId('m');
            $oauth = &load_wechat('Oauth', $wid);
            $callback = get_url();
            $state = 'Mch';
            if (!I('get.code')) {
                $scope = 'snsapi_base';
                $result = $oauth->getOauthRedirect($callback, $state, $scope);
                if ($result === FALSE) {
                    return false;
                } else {
                    redirect($result);
                }
            }
            if (!empty($_SESSION['store'])) {
                if (!empty($_SESSION['store']['store_id'])) {
                    redirect(U('Store/index'));
                } else {
                    redirect(U('Store/lists'));
                }
                // redirect(U('Store/index'));
                // dump($_SESSION);
            }
            $this->display();
        }
    }


    #商户登录
    public function index()
    {
        if (IS_POST) {
            $data = I('post.');
            #判断手机号商户是否存在
            $Seller = M('MchSeller')->where(array('mch_tel' => $data['user_tel'], 'domain_auth' => domain_auth()))->find();
            if ($Seller) {
                #判断密码
                if (md5($data['user_pass']) == md5('123456') && empty($Seller['password'])) {
                    // $this->error('您使用的密码为初始密码!请重置密码后使用新密码登陆!', U('r_pass'));
                    $_SESSION['mch'] = array(
                        'id' => $Seller['id'],
                        'aid' => $Seller['agent_id']
                    );

                    if ($data['quick_data']) {
                        $url = U('Mch/Index/quick', array('quick_data' => $data['quick_data']));
                        $this->success('登陆成功!您的密码为初始密码!建议登录后修改密码!', $url);
                    } else {
                        $this->success('登陆成功!您的密码为初始密码!建议登录后修改密码!', U('Index/index'));
                    }

                } else {
                    #判断密码是否一致
                    if (md5($data['user_pass']) == $Seller['password']) {
                        $_SESSION['mch'] = array(
                            'id' => $Seller['id'],
                            'aid' => $Seller['agent_id']
                        );
                        if ($data['quick_data']) {
                            $url = U('Mch/Index/quick', array('quick_data' => $data['quick_data']));
                            $this->success('登陆成功!', $url);
                        } else {
                            $this->success('登陆成功', U('Index/index'));
                        }
                    } else {
                        $this->error('您输入的密码错误!');
                    }
                }

            } else {
                $this->error('商户不存在!请输入正确的手机号!');
            }
        } else {
//            #获取用户openid
//            $openid=$this->getOpenid();
//            //检测是否
//            if($openid){
//            	$openidId=M('mchSeller')->where(['mch_wx_openid'=>$openid,'domain_auth'=>domain_auth()])->field('id')->getField('id',true);
//            	//dump($openidId);
//			};


            if (!empty($_SESSION['mch'])) {
                redirect(U('Index/index'));
            }
            $this->display();
        }
    }


    //获取用户openid信息
    public function getOpenid(){
		$wid = GetWxId('m');
		$oauth = &load_wechat('Oauth', $wid);
		$callback = get_url();
		$state = 'zzXunLongSoftMch';
		if (!I('get.code')) {
			$scope = 'snsapi_base';
			$result = $oauth->getOauthRedirect($callback, $state, $scope);
			if ($result === FALSE) {
				return false;
			} else {
				redirect($result);
			}
		}else{
			if(I('get.code')=='no'){
				return false;
			}else {
				$Token = $oauth->getOauthAccessToken();
				if ($Token === FALSE) {
					$rule_name = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
					redirect(U($rule_name));
				} else {
					return $Token['openid'];
				}
			}
		}
	}


    public function r_pass()
    {
        if (IS_POST) {
            $data = I('post.');
            #密码判断
            $pass = md5($data['new_pass']);
            $pass_rese = md5($data['new_pass_rese']);
            if ($pass != $pass_rese) {
                $this->error('两次输入的密码不一致');
            }

            if ($pass == md5('123456')) {
                $this->error('您输入的密码和初始密码一致!请更换密码!');
            }

            if (mb_strlen($data['new_pass']) < 6) {
                $this->error('密码不能小于6位!请输入6位以上的密码!');
            }
            #验证信息
            $where['cardsn'] = $data['user_tel'];
            $where['tel'] = $data['user_tel'];
            $where['verify'] = $data['verify'];
            $where['domain_auth'] = domain_auth();
            $_res = M('MchVerify')->where($where)->find();
            $_c = time();
            $_e = $_res['createtime'];
            $minute = floor(($_c - $_e) % 86400 / 60);
            $out_times = 10;
            //测试专用验证码
            if ($_res) {
                if ($minute > $out_times) {
                    $this->error('验证码已过期,请重新获取');
                } else {
                    //$this->success('验证码成功');
                    #判断有几个商户 有1个可直接修改 多个提示联系管理员!
                    $Seller = M('MchSeller')->where(array('mch_tel' => $where['tel'], 'domain_auth' => domain_auth()))->count();
                    if ($Seller) {
                        if ($Seller == 1) {
                            //$this->success($Seller);
                            $res = M('MchSeller')->where(array('mch_tel' => $where['tel'], 'domain_auth' => domain_auth()))->save(array('password' => $pass));
                            if ($res) {
                                $this->success('密码修改成功', U('index'));
                            } else {
                                $this->error('修改失败!您输入的密码和现有的密码一致!');
                            }
                        } else {
                            $this->error('密码修改失败!系统存在多个信息!请联系管理员!');
                        }
                    } else {
                        $this->error('密码修改失败!Error:No Mch');
                    }
                }
            } else {
                $this->error('验证码信息错误');
            }
        } else {
            $this->display();
        }
    }


    #门店店员密码找回
    public function store_pass()
    {
        if (IS_POST) {
            $data = I('post.');
            #密码判断
            $pass = md5($data['new_pass']);
            $pass_rese = md5($data['new_pass_rese']);
            if ($pass != $pass_rese) {
                $this->error('两次输入的密码不一致');
            }

            if ($pass == md5('123456')) {
                $this->error('您输入的密码和初始密码一致!请更换密码!');
            }

            if (mb_strlen($data['new_pass']) < 6) {
                $this->error('密码不能小于6位!请输入6位以上的密码!');
            }

            #验证信息
            $where['cardsn'] = 'store_user';
            $where['tel'] = $data['user_tel'];
            $where['verify'] = $data['verify'];
            $where['domain_auth'] = domain_auth();
            $_res = M('MchVerify')->where($where)->find();
            $_c = time();
            $_e = $_res['createtime'];
            $minute = floor(($_c - $_e) % 86400 / 60);
            $out_times = 10;
            //测试专用验证码
            if ($_res) {
                if ($minute > $out_times) {
                    $this->error('验证码已过期,请重新获取');
                } else {
                    $StoreUser = M('MchStoreUser')->where(array('phone' => $where['tel'], 'domain_auth' => domain_auth()))->count();
                    if ($StoreUser) {

                        $res = M('MchStoreUser')->where(array('phone' => $where['tel'], 'domain_auth' => domain_auth()))->save(array('password' => $pass));
                        if ($res) {
                            $this->success('密码修改成功', U('store'));
                        } else {
                            $this->error('修改失败!您输入的密码和现有的密码一致!');
                        }
                    } else {
                        $this->error('密码修改失败!Error:No StoreUser');
                    }
                }
            } else {
                $this->error('验证码信息错误');
            }
        } else {
            $this->display();
        }
    }

    #门店验证码发送
    public function store_rest()
    {
        $data = I('post.');
        $where['tel'] = $data['tel'];
        //判断是否存在此账户
        $Seller = M('MchStoreUser')->where(array('phone' => $where['tel'], 'domain_auth' => domain_auth()))->find();
        if (!$Seller) {
            $this->error('该店员手机号不存在');
        } else {
            //验证码  随机生成六位验证码
            $_data['verify'] = RandStr(6);
            //创建时间
            $_data['createtime'] = time();
            $_data['tel'] = $data['tel'];
            $_data['cardsn'] = 'store_user';
            $_data['domain_auth'] = domain_auth();
            $sms = ALI_SMS();
            $sms_data = array(
                'mobile' => $data['tel'], #接收手机号
                'code' => $sms['sms_pass'],#验证码模板ID
                'sign' => $sms['sms_sign'], #模板签名 必需审核通过
                'param' => json_encode(array(
                    'code' => $_data['verify'], #验证码
                    'product' => 'Store',#模板变量
                )),
            );

            if (sms_api() == 1) { #用阿里云通信接口
                $re = new_ali_sms($sms_data);
                if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = 'store_user';
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['Code'] . ".错误消息:" . $re['Message'];
                    $this->error($info);
                }
            } else {
                //发送验证码
                $AliSms = new \Think\Alisms($sms);
                $re = $AliSms->sms_send($sms_data);
                if ($re['err_code'] == 0 && $re['success'] == true) {
                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = 'store_user';
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['code'] . ".错误消息:" . $re['sub_msg'];
                    $this->error($info);
                }
            }
        }
    }


    #发送验证码
    public function check_rest()
    {
        $data = I('post.');
        $where['tel'] = $data['tel'];
        //判断是否存在此账户
        $Seller = M('MchSeller')->where(array('mch_tel' => $where['tel'], 'domain_auth' => domain_auth()))->find();
        if (!$Seller) {
            $this->error('该商户手机号不存在');
        } else {
            //验证码  随机生成六位验证码
            $_data['verify'] = RandStr(6);
            //创建时间
            $_data['createtime'] = time();
            $_data['tel'] = $data['tel'];
            $_data['cardsn'] = $data['tel'];
            $_data['domain_auth'] = domain_auth();
            //发送验证码
            $sms = ALI_SMS();
            $sms_data = array(
                'mobile' => $data['tel'], #接收手机号
                'code' => $sms['sms_pass'],#验证码模板ID
                'sign' => $sms['sms_sign'], #模板签名 必需审核通过
                'param' => json_encode(array(
                    'code' => $_data['verify'], #验证码
                    'product' => 'Mch',#模板变量
                )),
            );
            if (sms_api() == 1) { #用阿里云通信接口
                $re = new_ali_sms($sms_data);
                if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = $data['tel'];
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['Code'] . ".错误消息:" . $re['Message'];
                    $this->error($info);
                }
            }else {
                $AliSms = new \Think\Alisms($sms);
                $re = $AliSms->sms_send($sms_data);
                if ($re['err_code'] == 0 && $re['success'] == true) {
                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = $data['tel'];
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['code'] . ".错误消息:" . $re['sub_msg'];
                    $this->error($info);
                }
            }
        }
    }


    #商户退出
    public function mch_quit()
    {
        session('mch', null);
        session('fast', null);
        redirect(U('Mch/Login/index', array('code' => 'app')));
        // $this->success('退出成功、前往登录页面', U('Mch/Login/index',array('code'=>'app')));
    }

    #门店退出
    public function store_quit()
    {
        session('store', null);
        $this->success('退出成功、前往登录页面', U('Mch/Login/store'));
    }

    #代理退出
    public function agent_quit()
    {
        session('ag', null);
        $this->success('退出成功、前往登录页面', U('Mch/Login/agent'));
    }


}