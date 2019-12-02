<?php
namespace System\Controller;

use Common\Controller\SystemBaseController;

class IndexController extends SystemBaseController
{
    public function index()
    {
        $Static=M('DataStatistics');
        $result=$Static->where(array('type'=>'system'))->find();
        $assign=array(
            'Go'=>json_decode($result['terday_data'],true),
            'Day'=>json_decode($result['day_data'],true),
            'To'=>json_decode($result['count_data'],true),
            'Mch'=>json_decode($result['mch_data'],true),
            'Mon'=>json_decode($result['week_data'],true),
            'Time'=>$result['etime']
        );
        $this->assign($assign);
        $this->display();
    }
}