<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 首页Controller
 */
class IndexController extends HomeBaseController{
    public function _initialize()
    {
        $agent_domain=M('Domain_auth')->getField('agent_domain',true);
        $result=in_array($_SERVER['HTTP_HOST'],$agent_domain);
        if($result){
            #在代理列表
            redirect('/Agent');
        }
        parent::_initialize();

        #管理域名
        $AdminDomain=in_array($_SERVER['HTTP_HOST'],C('AdminDomain'));
        if($AdminDomain){
            redirect('/Admins');
        }

    }

	/**
	 * 首页
	 */
	public function index(){
     $this->display();
	}

    

	#测试
    /*public function test(){

        $wid= GetWxId('m');
        $oauth = &load_wechat('Receive',$wid);
        $data=array(
            'touser'=>'oV37d1a79fGHxVTxN_Ulh_ThvC-c',
            'text'=>array(
                'content'=>'你好!这个是测试消息!CCL',
            ),
            'msgtype'=>'text',
        );
        $res=$oauth->previewMassMessage($data);
        $data=array(
            'touser'=>array(
                'oV37d1SSDytsYnEOzOUzhdTac7_E',
                'oV37d1a79fGHxVTxN_Ulh_ThvC-c',
            ),
            'msgtype'=>'text',
            'text'=>array(
                'content'=>'你好!这个是测试消息!Dong',
            ),
        );
        $res=$oauth->sendMassMessage($data);





        dump($res);


    }*/

    public function mchdata(){
        $gateurl = 'https://interface.swiftpass.cn/sppay-interface-war/gateway';
        $data['partner'] ='150550000261';
        $data['charset'] = 'UTF-8';
        $data['dataType'] = 'json';
        $data['serviceName'] = 'normal_mch_add';
        $data['data'] = $this->data;
        $data['dataSign'] = '555555';
        $res = curl_calls($gateurl, $data);
        dump($res);
    }


}

