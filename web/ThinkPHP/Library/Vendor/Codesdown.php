<?php
/*
 *
 *
 *
 */

class Codesdown{

    public function __construct(){
        Vendor('qrcode');
    }





    //数据数组 下载名称
    public function down($image,$set){
        $zip = new ZipArchive();
        if(empty($set)){ //没有传入压缩名称 将按照时间名称输出
            $set=date('YmdHis');
        }
        $zname = $set.'.zip';
        //开始操作.zip压缩包
        if($zip->open($zname, ZipArchive::CREATE)===TRUE) {
            //向.zip压缩包里添加文件
            foreach($image as $val){
                $filename = $val.'.png';
                $files = getcwd() . '/Upload/Code/' . $filename;
                //dump($files);
                $zip->addFile($files, $filename);
            }
            //文件添加完，关闭ZipArchive的对象
            $zip->close();
            //清空（擦除）缓冲区并关闭输出缓冲
            ob_end_clean();
            //下载建好的.zip压缩包
            header("Content-Type: application/force-download");//告诉浏览器强制下载
            header("Content-Transfer-Encoding: binary");//声明一个下载的文件
            header('Content-Type: application/zip');//设置文件内容类型为zip
            header('Content-Disposition: attachment; filename=' . $zname);//声明文件名
            header('Content-Length: ' . filesize($zname));//声明文件大小
            error_reporting(0);
            //将欲下载的zip文件写入到输出缓冲
            //将缓冲区的内容立即发送到浏览器，输出
            @flush();
            @readfile($zname);
            @unlink('./'.$zname);//删除打包的临时zip文件。文件会在用户下载完成后被删除

        }
    }


    //新素材合成图下载
    //素材合成图下载
    public  function downsucai($image,$set,$type){
        //根据二维码合成图
        $_path = "./Upload/CodeSucai/";
        //路径是否存在 不存在则创建
        if (!is_dir($_path)) {
            mkdir($_path, 0777, true);
        }
        foreach($image as $val){
            if(!file_exists($_path . 'Brand_' . $val . '.png')) { //文件不存在
                $_qrurl = C('MA_DATA_URL'). "/" . $val;
                $_qrname = 'Brand_' . $val . ".png";
                $value = $_qrurl;
                $errorCorrectionLevel = "M";//容错级别
                $matrixPointSize = "12"; //生成图片大小
                $margin = "1";  //边缘留白
                QRcode::png($value, $_path . $_qrname, $errorCorrectionLevel, $matrixPointSize, $margin);
                if(!file_exists('./Source/QrBg/'.domain_auth().'.png')) {
                    $A = './Source/QrBg/default.png'; //主视图
                }else {
                    $A = './Source/QrBg/' . domain_auth() . '.png'; //主视图
                }
                $B = $_path . $_qrname;
                $x = 170;  //左
                $y = 270; //上
                $rh = 100;
                $im1 = imagecreatefromstring(file_get_contents($A));
                $im2 = imagecreatefromstring(file_get_contents($B));
                imagecopymerge($im1, $im2, $x, $y, 0, 0, imagesx($im2), imagesy($im2), $rh);
                imagepng($im1, $_path . $_qrname);
                $_im3 = imagecreatefrompng($_path . $_qrname);
                $_im4 = imagecreatetruecolor(imagesx($_im3), imagesy($_im3));
                $_bg = imagecolorallocate($_im4, 255, 255, 255);
                imagefill($_im4, 0, 0, $_bg);
                imagecopy($_im4, $_im3, 0, 0, 0, 0, imagesx($_im3), imagesy($_im3));
                $fontfile = "./Source/ttf/HYQiHei-55S.otf";
                switch (domain_auth()){
                    case 'gBBA3tgsw0': //有品
                        $str = 'No.'.$val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 0, 0,0);
                        imagettftext($_im4, 20, 0, 260, 670, $color, $fontfile, $str);
                        break;
                    case 'v8t1c5bpks': //乐享
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 255,255,255);
                        imagettftext($_im4, 20, 0, 330, 795, $color, $fontfile, $str);
                        break;
                    case 'fkBp72967d': //群强付
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 255,255,255);
                        imagettftext($_im4, 18, 0, 310, 788, $color, $fontfile, $str);
                        break;
                    case 'C86n8g38tk': //UU支付
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 255,255,255);
                        imagettftext($_im4, 18, 0, 310, 788, $color, $fontfile, $str);
                        break;
                    case 'u7hqpav879': //头条付
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 0,0,0);
                        imagettftext($_im4, 17, 0, 310, 688, $color, $fontfile, $str);
                        break;
                    case 'gtrkbaafaC': //悟空营销
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 255,255,255);
                        imagettftext($_im4, 18, 0, 310, 788, $color, $fontfile, $str);
                        break;
                    case 'mctkobtcBm': //瑞银码
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 255,255,255);
                        imagettftext($_im4, 18, 0, 310, 788, $color, $fontfile, $str);
                        break;
                    case 'Bb7cb55cq8': //用码付
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 255,255,255);
                        imagettftext($_im4, 18, 0, 310, 788, $color, $fontfile, $str);
                        break;
                    default:
                        $str = $val;
                        $str = iconv('gbk', 'utf-8', $str);
                        $color = ImageColorAllocate($_im4, 0, 171, 235);
                        imagettftext($_im4, 16, 0, 510, 970, $color, $fontfile, $str);
                        break;
                }
                //左 上
                imagepng($_im4, $_path . $_qrname);
            }
        }
        //执行完之后进行打包下载
        if($type){
            return $_path . 'Brand_' . $type . '.png';
        }else {
            $zip = new ZipArchive();
            $zname = $set . '.zip';
            //开始操作.zip压缩包
            if ($zip->open($zname, ZipArchive::CREATE) === TRUE) {
                //向.zip压缩包里添加文件
                foreach ($image as $val) {
                    $filename = 'Brand_' . $val . '.png';
                    $files = getcwd() . '/Upload/CodeSucai/' . $filename;
                    //dump($files);
                    $zip->addFile($files, $filename);
                }
                //文件添加完，关闭ZipArchive的对象
                $zip->close();
                //清空（擦除）缓冲区并关闭输出缓冲
                ob_end_clean();
                //下载建好的.zip压缩包
                header("Content-Type: application/force-download");//告诉浏览器强制下载
                header("Content-Transfer-Encoding: binary");//声明一个下载的文件
                header('Content-Type: application/zip');//设置文件内容类型为zip
                header('Content-Disposition: attachment; filename=' . $zname);//声明文件名
                header('Content-Length: ' . filesize($zname));//声明文件大小
                error_reporting(0);
                //将欲下载的zip文件写入到输出缓冲
                //将缓冲区的内容立即发送到浏览器，输出
                @flush();
                @readfile($zname);
                @unlink($zname);//删除打包的临时zip文件。文件会在用户下载完成后被删除
            }
        }
    }



    //素材合成图下载
   /* public  function old_downsucai($image,$set){
        //根据二维码合成图
        $_path = "./uploads/wbs_mcash_sucai/";
        //路径是否存在 不存在则创建
        if (!is_dir($_path)) {
            mkdir($_path, 0777, true);
        }
        foreach($image as $val){

            $A = './styles/codebg.png'; //主视图，也就是白云飘飘这张主图
            $B = './uploads/wbs_mcash/'.$val.'.png'; //复制并需旋转的小图
            //先判断文件是否存在  如果存在则不生成  如没有存在才生成素材图
            if(!file_exists($_path . 'SC_' . $val . '.png')) { //文件不存在
                $x = 180;
                $y = 335;
                $rh = 100;
                $im1 = imagecreatefromstring(file_get_contents($A));
                $im2 = imagecreatefromstring(file_get_contents($B));
                imagecopymerge($im1, $im2, $x, $y, 0, 0, imagesx($im2), imagesy($im2), $rh);
                imagepng($im1, $_path . 'SC_' . $val . '.png');
                $this->smart_resize_image($_path . 'SC_' . $val . '.png'); //进行图缩小
            }
        }
        //执行完之后进行打包下载

        $zip = new ZipArchive();
        $zname = $set.'.zip';
        //开始操作.zip压缩包
        if($zip->open($zname, ZipArchive::CREATE)===TRUE) {
            //向.zip压缩包里添加文件
            foreach($image as $val){
                $filename = 'SC_'.$val.'.png';
                $files = getcwd() . '/uploads/wbs_mcash_sucai/' . $filename;
                //dump($files);
                $zip->addFile($files, $filename);
            }
            //文件添加完，关闭ZipArchive的对象
            $zip->close();
            //清空（擦除）缓冲区并关闭输出缓冲
            ob_end_clean();
            //下载建好的.zip压缩包
            header("Content-Type: application/force-download");//告诉浏览器强制下载
            header("Content-Transfer-Encoding: binary");//声明一个下载的文件
            header('Content-Type: application/zip');//设置文件内容类型为zip
            header('Content-Disposition: attachment; filename=' . $zname);//声明文件名
            header('Content-Length: ' . filesize($zname));//声明文件大小
            error_reporting(0);
            //将欲下载的zip文件写入到输出缓冲
            //将缓冲区的内容立即发送到浏览器，输出
            @flush();
            @readfile($zname);
            @unlink($zname);//删除打包的临时zip文件。文件会在用户下载完成后被删除

        }
    }
*/
   public function smart_resize_image( $file, $width = 566, $height = 849, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false ){
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