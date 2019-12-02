<?php
namespace Common\Controller;
use Think\Controller;
/**
 * Base基类控制器
 */
class BaseController extends Controller{
    /**
     * 初始化方法
     */
    public function _initialize(){
        #全局渠道域名授权参数
        $domain=domain_rel();
        $this->_domain=M('domain_auth')->where(array('web_domain'=>$domain))->find();
        if($_SERVER['HTTP_HOST']=='192.168.1.1'){
           die('Server');
        }
        if(!$this->_domain){
            $content='服务未授权!请联系管理员!';
            print($domain);
            die($content);
        }
        if($this->_domain['status']!=1){
            $content='服务已被停止!请联系专员!';
            die($content);
        }
        #全局设置主题
        set_theme($this->_domain['theme']);
        $assign=array(
            '_domain'=>$this->_domain,
        );
        $this->assign($assign);
    }




    //默认错误提示
    public function wftmsg($status,$message){
        $array=array(
            'version'=>'2.0',
            'charset'=>'UTF-8',
            'result_code'=>$status,
            'err_msg'=>$message,
            'err_code'=>$status,
            'status'=>$status,
            'message'=>$message
        );
        self::show_xml($array);
    }

    //默认错误提示
    public function show_msg($message){
        $array=self::xmlctojson($message); //先解析为json数组
        self::show_xml($array); //最后输出
    }


    //解析XML
    public function xmlctojson($str){
        $obj = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON,true);
        return $dJSON;
    }

    //数组转XML
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";

        }
        $xml.="</xml>";
        return $xml;
    }

    //数组转XML 输出结果
    public function show_xml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        header('Content-Type:text/xml');
        echo $xml;
        exit;
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
