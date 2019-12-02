<?php
namespace Tasks\Controller;

use Think\Controller;

/**
 * admin 基类控制器
 */
class AliBaseController extends Controller
{
    public function _initialize()
    {
        // 引入ThinkPHP入口文件
        $this->data = json_decode($GLOBALS["HTTP_RAW_POST_DATA"], true);
        $this->Message = json_decode(base64_decode($this->data['Message']), true);
        $this->MessageId=$this->data['MessageId'];
    }


}