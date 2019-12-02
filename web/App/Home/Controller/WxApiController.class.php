<?php
namespace Home\Controller;
use Home\Controller\WxBaseController;
/**
 * 测试项目
 */
class WxApiController extends WxBaseController
{
    protected $wechat;
    protected $openid;
    public function _initialize()
    {
        //parent::_initialize();
        //rwlog('test',$_POST);
        $this->token=I('get.token');
        #判断TOKEN是否存在
        $Weixin=M('MchWeixin')->where(array('token' => $this->token, 'domain_auth' => domain_auth()))->getField('id');
        if($Weixin){
            $this->wechat = &  load_wechat('Receive',$Weixin);
        }else{
            exit('Token Not Error');
        }

        /* 验证接口 */
        if ($this->wechat->valid() === FALSE) {
            // 接口验证错误，记录错误日志
            rwlog('WX_ERROR', "微信被动接口验证失败，{$this->wechat->errMsg}[{$this->wechat->errCode}]");
            // 退出程序
            exit($this->wechat->errMsg);
        }

        /* 获取openid */
        $this->openid = $this->wechat->getRev()->getRevFrom();
        /* 记录接口日志 */
        $this->_logs();


    }

    #入口
    public function index(){
        #分别执行对应类型的操作
        switch ($this->wechat->getRev()->getRevType()) {
            case 'text':
                $keys = $this->wechat->getRevContent();
                return self::_keys($keys);
            case 'event':
                return self::_event();
            case 'image':
                return self::_image();
            case 'location':
                return self::_location();
            default:
                return self::_default();

        }
    }


    #关键字处理
    protected function _keys($keys) {
        return $this->wechat->text($keys)->reply();
    }

    #图片事件处理
    protected function _image() {
        //return self::_keys('图片类型');
        exit('success');
    }



    #事件处理
    protected function _event() {
        $event = $this->wechat->getRevEvent();
        switch (strtolower($event['event'])) {
            case 'subscribe':/* 关注事件 */
                //$this->_sync_fans(true);
                /*if (!empty($event['key']) && stripos($event['key'], 'qrscene_') !== false) {
                    $this->_spread(preg_replace('|^.*?(\d+).*?$|', '$1', $event['key']));
                }
                return $this->_keys('wechat_keys#keys#subscribe');*/
                return self::_subscribe();

            case 'unsubscribe':/* 取消关注 */
                //$this->_sync_fans(false);
                exit('success');
            case 'click': /* 点击链接 */
                exit('success');
            case 'scancode_push':
                exit('success');
            case 'scancode_waitmsg':/* 扫码推事件 */
                exit('success');
            case 'scan':
               exit('success');
        }
    }

    #默认事件处理
    protected function _default() {
        return $this->wechat->transfer_customer_service()->reply();
        exit('success');
    }

    #关注事件信息回复
    protected function _subscribe(){
        $bead=self::DbReply('Bead','');
        if($bead){
            if($bead['type']=='text'){
                $this->wechat->text($bead['reply_text'])->reply();
            }else{
                self::_keys('回复您一张图片TEST');
            }
        }else{
            exit('success');
        }
    }

    #取回复规则数据
    protected  function DbReply($type,$keyword){
        $map=array(
            'token'=>$this->token,
            'key_type'=>$type,
            'status'=>1
        );
        if($keyword){
          $map['keyword']=array('like','%'.$keyword.'%');
        }
        $res=M('MchWeixinReply')->where($map)->find();
        if ($res){
            return $res;
        }else{
            return false;
        }
    }


    #位置类事情回复
    protected function _location() {
        $vo = $this->wechat->getRevData();
        $url = "http://apis.map.qq.com/ws/geocoder/v1/?location={$vo['Location_X']},{$vo['Location_Y']}&key=ZBHBZ-CHQ2G-RDXQF-I5TUX-SAK53-A5BZT";
        $data = json_decode(file_get_contents($url), true);
        if (!empty($data) && intval($data['status']) === 0) {
            $msg = $data['result']['formatted_addresses']['recommend'];
        } else {
            $msg = "{$vo['Location_X']},{$vo['Location_Y']}";
        }
        $this->wechat->text($msg)->reply();
    }












    #记录接口日志
    public function _logs() {
        $data = $this->wechat->getRev()->getRevData();
        if (empty($data)) {
            return;
        }
        if (isset($data['Event']) && in_array($data['Event'], array('scancode_push', 'scancode_waitmsg', 'scan'))) {
            $scanInfo = $this->wechat->getRev()->getRevScanInfo();
            $data = array_merge($data, $scanInfo);
        }
        if (isset($data['Event']) && in_array($data['Event'], array('location_select'))) {
            $locationInfo = $this->wechat->getRev()->getRevSendGeoInfo();
            $data = array_merge($data, $locationInfo);
        }
        rwlog('wechat_message', array_change_key_case($data, CASE_LOWER));
    }
}