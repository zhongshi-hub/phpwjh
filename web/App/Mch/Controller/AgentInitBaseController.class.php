<?php
namespace Mch\Controller;
use Think\Controller;
class AgentInitBaseController extends Controller {

    public function _initialize(){
        $domain=domain_rel();
        $_domain=M('domain_auth')->where(array('web_domain'=>$domain))->find();
        $this->assign('_domain',$_domain);
        $Seller = M('MchSeller')->where(array('id' => $_SESSION['mch']['id'],'domain_auth' => domain_auth()))->find();
        $this->assign('_seller',$Seller);
        #获取代理电话
        $agent=M('MchAgent')->where(array('id'=>$Seller['agent_id']))->getField('user_phone');
        $this->assign('atel',$agent);

        /*$Agent_url='http://'.$_SERVER['HTTP_HOST'].'/Mch/Plugs/AgentVite/data/'.Xencode($_SESSION['ag']['id']);
        $this->agent_code='http://xunmafu.com/Plugs/Qr/code/data/'.Xencode($Agent_url);
        $this->assign('agent_code',$this->agent_code);*/
        $ag_decode=U('Mch/Plugs/AgVite',array('Code'=>Xencode(date('YmdHis')."Xun_".$_SESSION['ag']['id'])));
        $this->assign('ag_decode',$ag_decode);
        //验证用户状态
        if(empty($_SESSION['ag'])){
            $this->error('登录超时,请重新登录',U('Mch/Login/agent'));
        }
    }

    public function AgentAll(){
        $data=self::getAll($_SESSION['ag']['id'],true);
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


}