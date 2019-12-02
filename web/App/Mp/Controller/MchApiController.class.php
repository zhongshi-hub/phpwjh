<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Base基类控制器
 */
class MchApiController extends BaseController{




    public function index(){
        $db=M('MchTerminal');
        $where=[
           'mch_id'=>session('mp.id'),
           'domain_auth'=>domain_auth()
        ];
        $res=$db->where($where)->count();
        //如果没有中断则新创建
        if($res<1){
            $data['appid']=self::randApp('appid');
            $data['appkey']=self::randApp();
            $data['mch_id']=session('mp.id');
            $data['create_time']=time();
            $data['status']=1;
            $data['domain_auth']=domain_auth();
            $data['remark']='商户PC端自动生成';
            $db->add($data);
        }
        //查询终端信息 如多个只显示一个
        $tem=$db->where($where)->find();
        $this->assign('api',$tem);
        $this->display();
    }


    /**
     * 异步接口设置
     */
    public function apiNotifySetting(){
        $data=I('post.');
        $db=M('MchTerminal');
        $where=[
            'mch_id'=>session('mp.id'),
            'domain_auth'=>domain_auth()
        ];
        if($data['name']=='notify_url'){
            $res=$db->where($where)->save(['notify_url'=>$data['value']]);
            if($res){
                $this->success('异步通知地址更新成功');
            }else{
                $this->error('异步通知地址更新失败');
            }
        }else{
            $this->error('非法操作');
        }

    }


    #终端APPID或key生成
    public function randApp($type=null){
        $Data = M('MchTerminal');
        if($type=='appid'){
            $appid=RandStr(10);
            if($Data->where(array('appid'=>$appid))->count()){
                self::randApp('appid');
            }else{
                return $appid;
            }
        }else{
            $appkey=RandStr(22,1);
            if($Data->where(array('appkey'=>$appkey))->count()){
                self::randApp();
            }else{
                return $appkey;
            }
        }
    }
}