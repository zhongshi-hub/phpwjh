<?php
namespace Agent\Controller;
use Agent\Controller\InitBaseController;
class IndexController extends InitBaseController {



    public function index(){
       // $Static=M('AgentDataStatistics');
       // $result=$Static->where(array('type'=>'agent','domain_auth'=>domain_auth(),'agent'=>$_SESSION['agent']['id']))->find();
		$cache_agent='agent_'.domain_auth().$_SESSION['agent']['id'];
		$result=S($cache_agent);
        $assign=array(
            'Go'=>json_decode($result['terday_data'],true),
            'Day'=>json_decode($result['day_data'],true),
            'To'=>json_decode($result['count_data'],true),
            'Mch'=>json_decode($result['mch_data'],true),
            'Mon'=>json_decode($result['week_data'],true),
            'Time'=>$result['etime']
        );
        $this->assign($assign);
        $this->display();
    }


    #修改已有密码
    public function edit_pass(){
        $id=$_SESSION['agent']['id'];
        $agent=M('MchAgent')->where(array('id'=>$id,'domain_auth'=>domain_auth()))->find();
        if(IS_POST){
            #判断旧密码是否正确
            $data = I('post.');
            #密码判断
            $old_pass = md5($data['oldpass']);
            if($old_pass!=$agent['password']){
                $this->error('您输入的旧密码不正确!');
            }else{
                #旧密码正确 判断新密码两次是否一致
                $pass = md5($data['newpass']);
                $pass_rese = md5($data['newspass']);
                if ($pass != $pass_rese) {
                    $this->error('两次输入的密码不一致');
                }

                if ($pass == md5('123456')) {
                    $this->error('您输入的密码过于简单!请重新输入!');
                }

                if (mb_strlen($data['newpass']) < 6) {
                    $this->error('密码不能小于6位!请输入6位以上的密码!');
                }
                #以上信息正确
                #信息都正确
                $set=array(
                    'domain_auth'=>domain_auth(),
                    'id'=> $id,
                );
                $save=M('MchAgent')->where($set)->save(array('password'=>$pass));
                if($save){
                    session('agent', null);
                    $this->success('密码修改成功!请重新登录');
                }else{
                    $this->error('密码修改失败!您输入的密码和现有的密码一致!');
                }

            }
        }
    }

    #修改初始密码
    public function pass_data(){
        $id=$_SESSION['agent']['id'];
        $agent=M('MchAgent')->where(array('id'=>$id,'domain_auth'=>domain_auth()))->find();
        if(IS_POST){
            $data = I('post.');
            #密码判断
            $pass = md5($data['new_pass']);
            $pass_rese = md5($data['news_pass']);
            if ($pass != $pass_rese) {
                $this->error('两次输入的密码不一致');
            }

            if ($pass == md5('123456')) {
                $this->error('您输入的密码过于简单!请重新输入!');
            }

            if (mb_strlen($data['new_pass']) < 6) {
                $this->error('密码不能小于6位!请输入6位以上的密码!');
            }
            #验证信息
            $where['cardsn'] = 'agent_verify';
            $where['tel'] = $agent['user_phone'];
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
                    #信息都正确
                    $set=array(
                      'domain_auth'=>domain_auth(),
                      'id'=> $id,
                    );
                    $save=M('MchAgent')->where($set)->save(array('password'=>$pass));
                    if($save){
                        session('agent', null);
                        $this->success('密码修改成功!请重新登录');
                    }else{
                        $this->error('密码修改失败!您输入的密码和现有的密码一致!');
                    }
                }
            }else{
                $this->error('验证码信息错误');
            }
        }
    }
    #修改密码验证码
    public function sms_check(){
        $id=$_SESSION['agent']['id'];
        $agent=M('MchAgent')->where(array('id'=>$id,'domain_auth'=>domain_auth()))->find();
        if($agent){

            //验证码  随机生成六位验证码
            $_data['verify'] = RandStr(6);
            //创建时间
            $_data['createtime'] = time();
            $_data['tel'] = $agent['user_phone'];
            $_data['cardsn'] = 'agent_verify';
            $_data['domain_auth'] = domain_auth();
            //发送验证码
            $sms = ALI_SMS();
            $AliSms = new \Think\Alisms($sms);
            $sms_data = array(
                'mobile' => $_data['tel'], #接收手机号
                'code' => $sms['sms_pass'],#验证码模板ID
                'sign' => $sms['sms_sign'], #模板签名 必需审核通过
                'param' => json_encode(array(
                    'code' => $_data['verify'], #验证码
                    'product' => '业务平台',#模板变量
                )),
            );
            if (sms_api() == 1) { #用阿里云通信接口
                $re = new_ali_sms($sms_data);
                if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
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
                    $info = "错误代码:" . $re['Code'] . ".错误消息:" . $re['Message'];
                    $this->error($info);
                }
            }else {
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

        }else{
            $this->error('获取业务信息失败!');
        }

    }



    



}