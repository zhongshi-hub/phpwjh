<?php
/**
 * 二维码合成 加底部商户号
 * 2017-05
 */
class XunCode
{
    public $qrstr;
    public function __construct($curpath, $savepath)
    {

    }
    //参数： 值 文件名 自定义文字  logo
    public function create($_qrurl,$_stext='',$logo='')
    {
        //include './wbsCms/Lib/ORG/phpqrcode.php';

        if(empty($_stext)){
            $_qrname = date('YmdHis').".png";
        }else{
            $_qrname = $_stext.".png";
        }
        //保存路径
        $_path = "./Upload/Code/";
        //路径是否存在 不存在则创建
        if (!is_dir($_path)) {
            mkdir($_path, 0777, true);
        }
        //进行缩放
        $this->smart_resize_image($_path . $_qrname);

        if(!empty($_stext)){
            //二次合成图片
            $A = "./Source/Image/QrDefault.png"; //默认合成背景
            $B = $_path . $_qrname;
            $x = 15;
            $y = 10;
            $rh = 100;
            $im1 = imagecreatefromstring(file_get_contents($A));
            $im2 = imagecreatefromstring(file_get_contents($B));
            imagecopymerge($im1, $im2, $x, $y, 0, 0, imagesx($im2), imagesy($im2), $rh);
            imagepng($im1, $_path . $_qrname);
            //增加商户号
            $_im1 = imagecreatefrompng($_path . $_qrname);
            //这几行必须有，否则原图的阴影层过不来
            $_im2 = imagecreatetruecolor(imagesx($_im1), imagesy($_im1));
            $_bg = imagecolorallocate($_im2, 255, 255, 255);
            imagefill($_im2, 0, 0, $_bg);
            imagecopy($_im2, $_im1, 0, 0, 0, 0, imagesx($_im1), imagesy($_im1));
            #设置水印字体颜色
            $color = imagecolorallocatealpha($im2, 0, 0, 0, 0);
            #设置字体文件路径
            $fontfile = "./Source/ttf/MsyHbd.ttf";
            #水印文字
            $str = $_stext;
            $str = iconv('gbk', 'utf-8', $str);
            #添加自定义信息
            imagettftext($_im2, 20, 0, 75, 310, $color, $fontfile, $str);
            $res = imagepng($_im2, $_path . $_qrname);
        }

        if($this->isImage($_path . $_qrname)!==false){
            $_arr=array('status'=>1,'msg'=>'success','url'=>$_path . $_qrname);
            return json_encode($_arr);
        }else{
            $_arr=array('status'=>0,'msg'=>'error');
            return json_encode($_arr);
        }


    }

    public function isImage($filename){
        $types = '.gif|.jpeg|.png|.bmp';//定义检查的图片类型
        if(file_exists($filename)){
            $info = getimagesize($filename);
            $ext = image_type_to_extension($info['2']);
            return stripos($types,$ext);
        }else{
            return false;
        }
    }

    //缩放图片大小  270*270
    public function smart_resize_image( $file, $width = 270, $height = 270, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false ){
        if ( $height <= 0 && $width <= 0 ) {
            return false;
        }
        $info = getimagesize($file);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        if ($proportional) {
            if ($width == 0) $factor = $height/$height_old;
            elseif ($height == 0) $factor = $width/$width_old;
            else $factor = min ( $width / $width_old, $height / $height_old);
            $final_width = round ($width_old * $factor);
            $final_height = round ($height_old * $factor);
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
        }
        switch ($info[2] ) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;
            default:
                return false;
        }
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $trnprt_indx = imagecolortransparent($image);
            // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
                // Get the original image's transparent color's RGB values
                $trnprt_color  = imagecolorsforindex($image, $trnprt_indx);
                // Allocate the same color in the new image resource
                $trnprt_indx  = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $trnprt_indx);
                // Set the background color for new image to transparent
                imagecolortransparent($image_resized, $trnprt_indx);
            }
            // Always make a transparent background color for PNGs that don't have one allocated already
            elseif ($info[2] == IMAGETYPE_PNG) {
                // Turn off transparency blending (temporarily)
                imagealphablending($image_resized, false);
                // Create a new transparent color for image
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $color);
                // Restore transparency blending
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
        if ( $delete_original ) {
            if ( $use_linux_commands )
                exec('rm '.$file);
            else
                @unlink($file);
        }
        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }
        switch ($info[2] ) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output);
                break;
            case IMAGETYPE_PNG:
                imagepng($image_resized, $output);
                break;
            default:
                return false;
        }
        return true;
    }




}