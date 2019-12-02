<?php
namespace Agent\Controller;
use Common\Controller\BaseController;
class InitBaseController extends BaseController {

    public function _initialize(){
        parent::_initialize();
        $rule_name=CONTROLLER_NAME.'/'.ACTION_NAME;
        #获取域名是否在授权内
        //$agent_domain=M('Domain_auth')->getField('agent_domain',true);
        //dump($agent_domain);
//        $result=in_array($_SERVER['HTTP_HOST'],$agent_domain);
//        if(!$result){
//            die('Error Not Auth!');
//        }
        if(empty($_SESSION['agent'])){
            $this->error('登录超时,请重新登录',U('Agent/Login/index',array('callurl'=>base64_encode(__SELF__))));
        }
        $this->is_agent=self::is_agent();
        $Agent_url='http://'.$_SERVER['HTTP_HOST'].'/Mch/Plugs/AgentVite/data/'.Xencode($_SESSION['agent']['id']);
        $this->agent_code='http://xunmafu.com/Plugs/Qr/code/data/'.Xencode($Agent_url);
        $assign=array(
            'is_agent'=>self::is_agent(),
            'rule'=>$rule_name,
            'agent_code'=>$this->agent_code,
        );

        $this->assign($assign);
    }


    public function AgentAll(){
        $data=self::getAll($_SESSION['agent']['id'],true);
        return $data;
    }

    public function getAll($categoryID,$type)
    {
        //初始化ID数组
        $array[] = $categoryID;
        do
        {
            $ids = '';
            $where['pid'] = array('in',$categoryID);
            $cate = M('MchAgent')->where($where)->select();
            foreach ($cate as $k=>$v)
            {
                $array[] = $v['id'];
                $ids .= ',' . $v['id'];
            }
            $ids = substr($ids, 1, strlen($ids));
            $categoryID = $ids;
        }
        while (!empty($cate));
        $ids = implode(',', $array);
        if($type){
            return $array; //返回数组
        }else {
            return $ids;    //  返回字符串
        }
    }

    #获取代理是否有发展下级权限
    public function is_agent(){
        $where['id']=$_SESSION['agent']['id'];
        $where['domain_auth']=domain_auth();
        $agent=M('MchAgent')->where($where)->find();
        if($agent['x_status']==1){
            return true;
        }else{
            return false;
        }
    }




}