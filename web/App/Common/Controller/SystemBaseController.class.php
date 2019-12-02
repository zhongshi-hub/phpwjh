<?php
namespace Common\Controller;
use Think\Controller;
/**
 * admin 基类控制器
 */
class SystemBaseController extends Controller{
    /**
     * 初始化方法
     */
    public function _initialize(){
        //授权域名 非授权域名禁止访问
        $domain_data=domain_rel();
        /*if($domain_data!='xunmafu.com'){
            redirect('/');
        }*/
        
        $_auth=new \Think\Auth();
        $rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        //排除链接
        $notauth=array(
            'System/Upload/index', //上传页面
            'System/Upload/netdown',//网络下载图片
            'System/Login/index', //登录页面
            'System/Login/load_verify', //验证码
            'System/Login/out',    //退出登录
            'System/Mcha/queryBnkCity',//  城市列表
            'System/Mcha/bnkLink', //分行列表
        );
        $nauth=array(
            'System/Login/index', //登录页面
            'System/Login/load_verify', //验证码
            'System/Login/out'    //退出登录
        );
        //dump(__SELF__);

        if (!in_array($rule_name, $nauth)) {
            //验证用户状态
            if(empty($_SESSION['system'])){
                $this->error('登录超时,请重新登录',U('System/Login/index',array('callurl'=>base64_encode(__SELF__))));
            }
        }


        if (!in_array($rule_name, $notauth)) {
            $result = $_auth->check($rule_name, $_SESSION['system']['id']);
            if (!$result) {
                $this->error('您没有权限访问');
            }
        }

        //头部标题 根据数据库配置的菜单名称显示
        $title=M('system_auth_rule')->where(array('name'=>$rule_name))->getfield('title');
        //菜单项  根据系统角色 显示对应的菜单
        $datas=M('system_auth_rule')->order('orders desc')->select();
        $data=\Org\Nx\Data::channelLevel($datas,0,'&nbsp;','id');


        // 显示有权限的菜单
        foreach ($data as $k => $v) {
            if ($_auth->check($v['name'],$_SESSION['system']['id'])) {
                //然后菜单数据不显示的删除
                if($v['menu']!=1){
                    unset($data[$k]);
                }
                foreach ($v['_data'] as $m => $n) {
                    //没有权限的删除不显示
                    if(!$_auth->check($n['name'],$_SESSION['system']['id'])){
                        unset($data[$k]['_data'][$m]);

                    }
                    //然后菜单数据不显示的删除
                    if($n['menu']!=1){
                        unset($data[$k]['_data'][$m]);
                    }
                }
            }else{
                // 删除无权限的菜单
                unset($data[$k]);
            }
        }

        $assign=array(
            'rule_name'=>$rule_name,
            'menu'=>$data,
            'title'=>$title
        );

        $this->assign($assign);


    }

    //excel 导出
    public function exportExcel($expTitle,$expCellName,$expTableData,$wbscms=array()){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName =  $xlsTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        import("Org.Util.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $wbscms['Atitle']);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }





}