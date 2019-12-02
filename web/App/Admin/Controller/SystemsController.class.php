<?php

namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class SystemsController extends AdminBaseController
{

	public function bank_list(){
		$Data = M('mchBankList');
		$count = $Data->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$list = $Data->order('status desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$assign = array(
			'data' => $list,
			'page' => $show,
		);
		$this->assign($assign);
		$this->display();
	}


    public function  index(){
        $db=M('SystemConfig');
        $map['domain_auth']=domain_auth();
        if(IS_POST) {
            $data=I('post.');
            if($data['auth_fee']<'0.1'){
                $this->error('认证金额不能小于0.1元');
            }
            #判断是否存在
            $count=$db->where($map)->count();
            $auth_data=array(
              'auth_status'=>$data['auth_status'],
              'auth_fee'=>$data['auth_fee'],
              'auth_mch_id'=>$data['auth_mch_id'],
              'auth_mch_key'=>$data['auth_mch_key'],
              'auth_info'=>$data['auth_info'],
            );

            $xy_data=array(
              'fw_name'=>$data['fw_name'],
              'bm_name'=>$data['bm_name'],
              'fw_status'=>$data['fw_status'],
              'bm_status'=>$data['bm_status'],
              'quick_status'=>$data['quick_status'],
              'quick_name'=>$data['quick_name'],
            );
            $ad=array(
              'ad_status'=>$data['ad_status'],
              'ad_xyk'=>$data['ad_xyk'],
              'ad_dk'=>$data['ad_dk'],
            );
            $save=array(
              'domain_auth'=>domain_auth(),
              'auth_data'=>serialize($auth_data),
              'xy_data'=>serialize($xy_data),
              'fw_info'=>stripslashes(htmlspecialchars_decode($_POST['fw_info'])),
              'bm_info'=>stripslashes(htmlspecialchars_decode($_POST['bm_info'])),
              'quick_info'=>stripslashes(htmlspecialchars_decode($_POST['quick_info'])),
              'ad_data'=>serialize($ad),
              'agent_yq'=>$data['agent_yq'],
            );
            if($count){
                $res=$db->where($map)->save($save);
            }else{
                $res=$db->add($save);
            }
            if($res){
                $this->success('配置信息更新成功');
            }else{
                $this->error('配置信息失败');
            }
        }else{
            $_data=$db->where($map)->find();
            $assign=array(
                'auth'=>unserialize($_data['auth_data']),
                'xy'=>unserialize($_data['xy_data']),
                'ad'=>unserialize($_data['ad_data']),
                'data'=>$_data
            );
            //dump($assign);
            $this->assign($assign);
            $this->display();
        }
    }


    #移动支付配置
    public function pay_config(){
        $db=M('MchPayConfig');
        $map['domain_auth']=domain_auth();
        if(IS_POST) {
            $data=I('post.');
            #判断是否存在
            $count=$db->where($map)->count();
            if($count){
                $res=$db->where($map)->save($data);
            }else{
                $data['domain_auth']=domain_auth();
                $res=$db->add($data);
            }
            if($res){
                $this->success('支付配置信息更新成功');
            }else{
                $this->error('支付配置信息失败');
            }
        }else{
            $data=$db->where($map)->find();
            #获取微信列表
            $weixin=M('MchWeixin')->where($map)->field('id,name')->select();
            $assign=array(
                'weixin'=>$weixin,
                'data'=>$data,
            );
            $this->assign($assign);
            $this->display();
        }

    }

    #短信配置
    public function sms_config(){
        $db=M('MchAlismsConfig');
        $map['domain_auth']=domain_auth();
        if(IS_POST) {
            $data=I('post.');
            #判断是否存在
            $count=$db->where($map)->count();
            if($count){
                $res=$db->where($map)->save($data);
            }else{
                $data['domain_auth']=domain_auth();
                $res=$db->add($data);
            }
            if($res){
                $this->success('短信配置信息更新成功');
            }else{
                $this->error('短信配置信息失败');
            }
        }else{
            $data=$db->where($map)->find();
            $assign=array(
                'data'=>$data,
            );
            $this->assign($assign);
            $this->display();
        }
    }


    #一键添加模板消息
    public function add_template(){
        $data=I('post.');
        if($data['template_id']){
            if($data['template_id']=='OPENTM402074550') {
                $wid = DoGetWxId('', domain_auth());
            }else{
                $wid = DoGetWxId('m', domain_auth());
            }
            $oauth = &DoLoad_wechat('Receive', $wid,domain_auth());
            $res = $oauth->addTemplateMessage($data['template_id']);
            if($res['status']==1){
                $this->success($res['id']);
            }else{
                $this->error('模板消息增加失败'.$oauth->errMsg);
            }
        }else{
            $this->error('模板ID参数失败!请联系管理员!');
        }
    }


}