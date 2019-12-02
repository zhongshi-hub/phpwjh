<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
namespace Think;

class Swooke_task
{

    public function __construct()
    {

    }


    #投递异步任务
    public function On_receive($_Ser=null, $fd=null, $from_id=null, $data=null){
        rwlog('on_receive',$data);
    }

    #处理异步任务
    public function On_task($_Ser=null, $task_id=null, $from_id=null, $data=null){
        rwlog('on_task',$data);
    }

    #处理异步任务的结果
    public function On_finish($_Ser=null, $task_id=null, $data=null){
        rwlog('on_finish',$data);
    }





}
