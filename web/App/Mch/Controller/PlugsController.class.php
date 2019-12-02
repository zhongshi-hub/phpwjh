<?php
namespace Mch\Controller;
use Pays\Controller\InitBaseController;
class PlugsController extends InitBaseController {

    public function _initialize(){
        if(sys_agent_status()!=1){
            $this->error('业务调整!此功能停止!','',888);
        }
    }

    public function AgVite(){
        $data=explode('_',Xdecode(I('get.Code')));
        #获取代理姓名
        $agent=M('MchAgent')->where(array('domain_auth'=>domain_auth(),'id'=>$data[1],'status'=>1))->find();
        if($agent) {
            //dump($data);
            $Agent_url = 'http://' . $_SERVER['HTTP_HOST'] . '/Mch/Plugs/AgentVite/data/' . Xencode($data[1]);
            $agent_code = 'http://xunmafu.com/Plugs/Qr/code/data/' . Xencode($Agent_url);
            $assign=array(
              'ag_name'=>$agent['user_name'],
              'agent_code'=> $agent_code,
              'options' =>  self::jsapi(),
            );
            $this->assign($assign);
            $this->display();
        }else{
            $this->error('邀请码获取失败!','',888);
        }
    }

    #代理邀请码
    public function AgentVite(){
        $id=Xdecode(I('get.data'));
        $openid=self::user_openid();
        #获取代理信息
        $where['id']=$id;
        $where['domain_auth']=domain_auth();
        $res=M('MchAgent')->where($where)->find();
        if($res){
            #判断代理是否可用
            if($res['status']!=1){
                $this->error('代理不可用!已被冻结!','',888);
            }else{
                #根据代理ID筛选出来空码
                $map['aid']=$res['id'];
                $map['domain_auth']=domain_auth();
                $map['store_id']=array('EXP','IS NULL');
                $Code=M('MchCodes')->where($map)->getField('codes',true);
                if($Code){
                    #判断是否有认证的码
                    $UserAuth=M('MchUserAuth')->where(array('status'=>1,'openid'=>$openid))->find();
                    $data_code=in_array($UserAuth['codes'],$Code);
                    if($data_code){
                        #如果已认证 且在这个代理下 直接调到这个码注册页面
                        $url=C('MA_DATA_URL').'/'.$UserAuth['codes'];
                        redirect($url);
                    }else{
                        $code=self::user_code($Code);
                        if($code) {
                            $url = C('MA_DATA_URL') . '/' . $code;
                            redirect($url);
                        }else{
                            $this->error('收款码分配错误!请重新识别代理码!','',888);
                        }
                    }
                }else{
                    $this->error('收款码已用完!请补充码!','',888);
                }
            }
        }else{
            $this->error('代理信息获取失败','',888);
        }
    }


    #收款码ID的唯一性
    public function user_code($code){
        $rand_code=array_rand($code);
        $rand_code=$code[$rand_code];
        #判断是否已认证
        $rel=M('MchUserAuth')->where(array('status'=>1,'codes'=>$rand_code))->count();
        if($rel){
           self::user_code($code);
        }else{
           return $rand_code;
        }
    }

    #用户openid
    public function user_openid(){
        $token=$this->_oauth('base');
        if(!$token['openid']){
            $_token=$this->_oauth('base');
            if(!$_token['openid']){
                $this->error('获取用户信息失败!请重新扫码进行注册!','',888);
            }else{
                return $_token['openid'];
            }
        }else {
            return $token['openid'];
        }
    }

    #微信JSAPI
    public  function jsapi(){
        $wid= GetWxId('m');
        // 创建SDK实例
        $script = &  load_wechat('Script',$wid);
        $options = $script->getJsSign(get_url(), $timestamp, $noncestr, $appid);
        if($options===FALSE){
            // 接口失败的处理
            return $script->errMsg;
        }else{

            unset($options['jsApiList']);
            $options['jsApiList']=array('onMenuShareTimeline','onMenuShareAppMessage','hideOptionMenu','hideMenuItems','hideAllNonBaseMenuItem','menuItem:openWithSafari','menuItem:openWithQQBrowser','menuItem:readMode');
            return $options;
        }
    }

}