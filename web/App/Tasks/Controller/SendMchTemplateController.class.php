<?php

namespace Tasks\Controller;

use Think\Controller;

#发送商户其他模板消息
class SendMchTemplateController extends Controller
{
    #测试
    public function test()
    {
        $ext='Sftpays';
        //认证 sendMchTemplateMessage('14', 'auth');
        //注册 sendMchTemplateMessage('14', 'reg');
        //注册发送给审核员 sendMchTemplateMessage('14', 'sh_user');
        //通道开通成功
        sendMchTemplateMessage('14', 'alleys',$ext);

    }

    #发送商户模板消息
    /*public function sendMchTemplateMessage($mch_id, $type,$ext)
    {
        $data = array(
            'mc' => 'SendMchTemplate', #模块
            'ac' => 'Message' #方法
        );
        $res = ali_mns($data);
        if ($res['status'] == 1) {
            $arr = array(
                'order_id' => $mch_id,
                'type' => $type,
                'ext' => $ext,
            );
            $_data = array(
                'task_data' => serialize($arr),
                'auth_code' => domain_auth(),
                'rel' => serialize($res)
            );
            $where['messageId'] = $res['messageId'];
            $where['id'] = $res['msn_id'];
            M('alimsn')->where($where)->save($_data);
        }
    }*/

    #模板消息入口
    public function Message()
    {
        $data = M('alimsn')->where(array('messageId' => $this->data['MessageId']))->find();
        //rwlog('ali_msn', $data);
        $task_data = unserialize($data['task_data']);
        //rwlog('ali_msn', $task_data);
        if ($data['status'] != 1) {
            $type = $task_data['type'] . 'Template';
            self::$type($this->data['MessageId'], $task_data['order_id']);
        }
    }



    #商户认证状态提醒
    public function authTemplate($msg_id, $id){
        #根据ID获取商户信息
        $seller = M('MchSeller')->where(array('id' => $id))->find();
        $result = self::Send_Message_Status($msg_id,'auth',$seller['mch_wx_openid']);
        if($result) {
            if ($seller['mch_wx_openid']) {#存在这个记录发送
                $wid = DoGetWxId('m', $seller['domain_auth']);
                $oauth = &DoLoad_wechat('Receive', $wid, $seller['domain_auth']);
                $template_id = GetPayConfig($seller['domain_auth'], 'auth_template_id');
                $url = 'http://' . DomainAuthField($seller['domain_auth'], 'main_domain') . '/Mch/Login/index';
                if ($template_id) {
                    if($seller['auth_status']==1){
                        $status='认证成功';
                        $remark='\n亲的实名认证已完成\n欢迎使用'.DomainAuthField($seller['domain_auth'], 'web_name').'，如有问题，请点击公众号底部在线客服进行咨询';
                    }else{
                        $status='还未认证';
                        $remark='\n您好，您的实名认证还未认证\n点击详情登陆完成实名认证后，即可开通支付及提现功能';
                    }
                    $data = array(
                        'touser' => $seller['mch_wx_openid'], //openid
                        'template_id' => $template_id,
                        'url' => $url,
                        'data' => array(
                            'first' => array(
                                'value' => '认证商户:  '.$seller['mch_name'],
                                'color' => '#000000'
                            ),
                            'keyword1' => array(
                                'value' => '实名认证',
                                'color' => '#000000'
                            ),
                            'keyword2' => array(
                                'value' => $status,
                                'color' => '#000000'
                            ),
                            'remark' => array(
                                'value' => $remark,
                                'color' => '#ff1212'
                            ),
                        ),
                    );
                    $res = $oauth->sendTemplateMessage($data);
                    #增加模板消息发送记录
                    self::Send_Message_add('auth', $msg_id, $id, $seller['mch_wx_openid'], $data, $res);
                }
            }
        }

    }
    #商户注册成功发送给审核员提醒
    public function sh_userTemplate($msg_id, $id){
        #根据ID获取商户信息
        $seller = M('MchSeller')->where(array('id' => $id))->find();
        #管理者
        $AdminList=explode('|',GetPayConfig($seller['domain_auth'], 'sh_user_data'));

        foreach ($AdminList as  $v){
            $result = self::Send_Message_Status($msg_id,'sh_user',$v);
            if($result&&$v){
                $wid = DoGetWxId('m', $seller['domain_auth']);
                $oauth = &DoLoad_wechat('Receive', $wid, $seller['domain_auth']);
                $template_id = GetPayConfig($seller['domain_auth'], 'sh_user_template_id');
                if ($template_id) {
                    $data = array(
                        'touser' => $v, //openid
                        'template_id' => $template_id,
                        'data' => array(
                            'first' => array(
                                'value' => '叮咚叮咚!有一个新的商户入驻!\n\n商户名称:'.$seller['mch_name'],
                                'color' => '#000000'
                            ),
                            'keyword1' => array(
                                'value' => $seller['mch_card_name'],
                                'color' => '#000000'
                            ),
                            'keyword2' => array(
                                'value' => $seller['mch_tel'],
                                'color' => '#000000'
                            ),
                            'keyword3' => array(
                                'value' => date('Y-m-d H:i:s', $seller['ctime']),
                                'color' => '#000000'
                            ),
                            'remark' => array(
                                'value' => '\n商户资料已经提交成功!请尽快完成审核!',
                                'color' => '#ff1212'
                            ),
                        ),
                    );
                    $res = $oauth->sendTemplateMessage($data);
                    #增加模板消息发送记录
                    self::Send_Message_add('sh_user', $msg_id, $id, $v, $data, $res);
                }
            }
        }
    }


    #商户通道开通提醒
    public function alleysTemplate($msg_id, $id)
    {
        #获取异步参数里的信息
        $Msn = M('alimsn')->where(array('messageId' => $this->data['MessageId']))->find();
        $task_data = unserialize($Msn['task_data']);
        #根据ID获取商户信息
        $seller = M('MchSeller')->where(array('id' => $id))->find();
        $result = self::Send_Message_Status($msg_id,'alleys',$seller['mch_wx_openid']);
        if($result) {
            if ($seller['mch_wx_openid']) {#存在这个记录发送
                #通道类型
                $is_card=AlleysGetData($task_data['ext'],'is_card');
                if($is_card==1){
                    $card_info='无卡快捷';
                    #获取通道信息
                    $alley=M('MchSellerCardAlleys')->where(array('cid'=>$seller['id'],'alleys_type'=>$task_data['ext']))->find();
                    //$rate=CardAlleysGetRate($alley['alleys_type'],$seller['id']);
                }else{
                    $card_info='扫码支付';
                    #获取通道信息
                    $alley=M('MchSellerAlleys')->where(array('cid'=>$seller['id'],'alleys_type'=>$task_data['ext']))->find();
                    //$rate=AlleysGetRate($alley['alleys_type'],$seller['id']);
                }
                $wid = DoGetWxId('m', $seller['domain_auth']);
                $oauth = &DoLoad_wechat('Receive', $wid, $seller['domain_auth']);
                $template_id = GetPayConfig($seller['domain_auth'], 'alleys_template_id');
                $url = 'http://' . DomainAuthField($seller['domain_auth'], 'main_domain') . '/Mch/Plugs/PayHelp';
                if($task_data['ext']=='Bospay'){
                    $remark = '\n尊敬的用户你好，由于政策调整，系统已自动为您切换为T1通道（隔天到账），V通道（秒到）暂停使用。开放时间另行通知！';
                }else {
                    $remark = '\n通道开通说明及常见问题操作说明请点击详情查看!如有疑问?请点击公众号底部菜单中在线客服进行咨询!';
                }
                if ($template_id) {
                    $data = array(
                        'touser' => $seller['mch_wx_openid'], //openid
                        'template_id' => $template_id,
                        'url' => $url,
                        'data' => array(
                            'first' => array(
                                'value' => '您好,已为您成功开通支付方式\n\n商户名称:  '.$seller['mch_name'],
                                'color' => '#000000'
                            ),
                            'keyword1' => array(
                                'value' => alleys_name($task_data['ext'])."({$card_info})",
                                'color' => '#000000'
                            ),
                            'keyword2' => array(
                                'value' => $alley['rate'].'‰',
                                'color' => '#000000'
                            ),
                            'keyword3' => array(
                                'value' => AlleysGetData($task_data['ext'],'total_type'),
                                'color' => '#000000'
                            ),

                            'keyword4' => array(
                                'value' => date('Y-m-d H:i:s'),
                                'color' => '#000000'
                            ),
                            'remark' => array(
                                'value' => $remark,
                                'color' => '#ff1212'
                            ),
                        ),
                    );
                    $res = $oauth->sendTemplateMessage($data);
                    #增加模板消息发送记录
                    self::Send_Message_add('alleys', $msg_id, $id, $seller['mch_wx_openid'], $data, $res);
                }
            }
        }
    }


    #商户注册入驻成功模板消息
    public function regTemplate($msg_id, $id)
    {
        #根据ID获取商户信息
        $seller = M('MchSeller')->where(array('id' => $id))->find();
        $result = self::Send_Message_Status($msg_id,'reg',$seller['mch_wx_openid']);
        if($result) {
            if ($seller['mch_wx_openid']) {#存在这个记录发送
                $wid = DoGetWxId('m', $seller['domain_auth']);
                $oauth = &DoLoad_wechat('Receive', $wid, $seller['domain_auth']);
                $template_id = GetPayConfig($seller['domain_auth'], 'reg_template_id');
                $url = 'http://' . DomainAuthField($seller['domain_auth'], 'main_domain') . '/Mch/Login/index';
                if ($template_id) {
                    $data = array(
                        'touser' => $seller['mch_wx_openid'], //openid
                        'template_id' => $template_id,
                        'url' => $url,
                        'data' => array(
                            'first' => array(
                                'value' => '您好,您已成功入驻' . DomainAuthField($seller['domain_auth'], 'web_name') . '\n',
                                'color' => '#000000'
                            ),
                            'keyword1' => array(
                                'value' => $seller['mch_name'],
                                'color' => '#000000'
                            ),
                            'keyword2' => array(
                                'value' => $seller['mch_tel'],
                                'color' => '#000000'
                            ),
                            'keyword3' => array(
                                'value' => '123456',
                                'color' => '#000000'
                            ),
                            'keyword4' => array(
                                'value' => date('Y-m-d H:i:s', $seller['ctime']),
                                'color' => '#000000'
                            ),
                            'remark' => array(
                                'value' => '\n您的申请资料已提交成功,请等待平台审核!',
                                'color' => '#ff1212'
                            ),
                        ),
                    );
                    $res = $oauth->sendTemplateMessage($data);
                    #增加模板消息发送记录
                    self::Send_Message_add('reg', $msg_id, $id, $seller['mch_wx_openid'], $data, $res);
                }
            }
        }
    }


    #此消息执行过处理
    public function AliMsnStatus($messageId)
    {
        $arr = array(
            'etime' => time(),
            'status' => 1
        );
        M('alimsn')->where(array('messageId' => $messageId))->save($arr);
    }


    #增加模板消息发送记录
    #类型  消息ID  商户ID OPENID 模板数据 返回数据
    public function Send_Message_add($type, $message_id, $mch_id, $openid, $data, $res)
    {
        if ($res['errcode'] == 0) {
            self::AliMsnStatus($message_id);
            $arr = array(
                'send_id' => $message_id,
                'send_openid' => $openid,
                'send_type' => $type,
                'send_mch_id' => $mch_id,
                'send_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'send_time' => date('Y-m-d H:i:s'),
                'send_rel' => json_encode($res),
                'status' => 1
            );
        } else {
            $arr = array(
                'send_id' => $message_id,
                'send_openid' => $openid,
                'send_type' => $type,
                'send_mch_id' => $mch_id,
                'send_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'send_time' => date('Y-m-d H:i:s'),
                'send_rel' => json_encode($res),
                'status' => 0
            );
        }
        //rwlog('tem_data',$arr);
        M('SendmchtemplateLog')->add($arr);
    }

    #判断消息的唯一性 禁止重复发送
    public function Send_Message_Status($message_id, $type, $openid)
    {
        #先判断是否发送过消息
        $map['send_openid'] = $openid;
        $map['send_id'] = $message_id;
        $map['send_type'] = $type;
        $map['status'] = 1;
        $status = M('SendmchtemplateLog')->where($map)->count();
        if ($status) {
            return false;
        } else {
            return true;
        }
    }


}