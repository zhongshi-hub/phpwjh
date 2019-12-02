<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 测试项目
 */
class ApiTestController extends HomeBaseController
{
    protected $Extends;
    public function _initialize()
    {
        #判断TOKEN是否存在
        $Weixin=M('MchWeixin')->where(array('token' => 'LbdHkcDaCMqvfgg', 'domain_auth' => domain_auth()))->getField('id');
        if($Weixin){
            $this->Extends = &  load_wechat('Extends',$Weixin);
        }else{
            exit('Token Not Error');
        }

    }

    #创建临时参数二维码
    public function qr(){
        dump($this->Extends->getQRCode(1,0,600));
        dump($this->Extends->errMsg);
    }



}