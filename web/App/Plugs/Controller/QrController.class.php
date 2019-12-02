<?php
namespace Plugs\Controller;
use Think\Controller;
/*
 * 二维码扩展类
 * */

class QrController extends Controller
{

    public function code()
    {
        Vendor('qrcode');
        header('Content-type: image/png');
        $data = Xdecode(I('get.data'));
        $object = new \QRcode();
        $url = $data;//网址或者是文本内容
        $level = 3;
        $size = 6;
        $errorCorrectionLevel = intval($level);//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}