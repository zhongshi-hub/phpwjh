<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;
/**
 * 后台上传
 */
class UploadController extends SystemBaseController{


    /* 本地上传页面*/
    public function index(){
        if(IS_POST){
            /*文件上传安全检测*/
            if (empty($_FILES['file']['name'])) {
                $status=0;
                $msgs="上传失败, 请选择要上传的文件！";
            }
            if ($_FILES['file']['error'] != 0) {
                $status=0;
                $msgs="上传失败, 请重试.";
            }
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $size = intval($_FILES['file']['size']);
            $originname = $_FILES['file']['name'];
            $harmtype = array('htm','html','asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
            $max_size = 5000*1024;//系统最大允许大小
            if ($size >= $max_size) {
                $status=0;
                $msgs="上传文件大小超过限制";
            }elseif (empty($_FILES['file'])) {
                $status=0;
                $msgs="没有上传内容";
            }elseif (in_array(strtolower($ext), $harmtype)) {
                $status=0;
                $msgs="不允许上传此类文件";
            }elseif (!function_exists('imagecreate')){
                $status=0;
                $msgs="php不支持gd库，请配置后再使用";
            }else{//上传队列开始
                //本地存储
                    $return=$this->localUpload();
                    if($return['error']==1){
                        $status=0;
                        $msgs=$return['msg'];
                    } else {
                        $status=1;
                        $msgs="上传成功";
                        $url=U('Admin/Upload/index',array('status'=>1,'url'=>base64_encode($return['msg'])));
                    }
                //上传队列结束
            }
            die(json_encode(array('status'=>$status,'msg'=>$msgs,'url'=>$url)));
        }else{
            $this->display();
        }
    }


    /**网络下载图片**/
    public  function netdown(){
        if(IS_POST){
            //网络下载
            if(isset($_POST['type'])&&$_POST['type']=='wbs_net'){
                $url = trim(htmlspecialchars_decode($_POST['url']));
                $savePath =  './Upload/attachment/'.date('Y-m-d');// 设置附件上传目录
                $resp = $this->getImage($url,$savePath);
                    //保存图片到数据库
                    if($resp['error']==0){
                        $url=U('Admin/Upload/index',array('status'=>1,'url'=>base64_encode($resp['save_path'])));
                        $this->success($resp['msg'],$url);
                    }else{
                        $this->error($resp['msg']);
                    }
            }
        }
    }
    //远程图片本地化  //地址  保存路径  文件名  下载类型
    public function getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1,'msg'=>'图片地址不能为空');
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5,'msg'=>'目录错误');
        }
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        //curl_setopt($hander,CURLOPT_TIMEOUT,600);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $img=curl_exec($ch);
        //获取资源大小
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        //获取文件类型
        $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        //判断类型
        $ext = '';
        switch ($type){
            case 'application/x-jpg':
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/png':
                $ext = 'png';
                break;
            case 'image/gif':
                $ext = 'gif';
                break;
            default:
                return array('file_name'=>'','save_path'=>'','error'=>3,'msg'=>'提取资源失败, 资源文件类型错误.仅支持图片提取');
                break;
        }
        $max_size = 5000*1000;//系统最大允许大小
        if ($size >= $max_size) {
            return array('file_name'=>'','save_path'=>'','error'=>4,'msg'=>'上传文件大小超过限制');
            //$msgs="上传文件大小超过限制";
        }
        $filename='Net'.uniqid().".".$ext;//文件名
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>ltrim($save_dir,'.').$filename,'error'=>0,'size'=>$size,'type'=>$ext,'msg'=>'网络图片保存本地成功');
    }


    function localUpload(){
        $upload = new \Think\Upload();
        $upload->maxSize  = 500*1024 ;
        $upload->allowExts  = array('jpg','gif','png','jpeg','pdf','tif','doc','docx','xls','xlsx');
        $upload->autoSub=1;
        $upload->rootPath  = './Upload/'; // 设置附件上传根目录
        $upload->savePath =  '/attachment/';// 设置附件上传目录
        $upload->saveName = array('uniqid',''); //文件名称
        //
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/Upload')||!is_dir($_SERVER['DOCUMENT_ROOT'].'/Upload')){
            mkdir($_SERVER['DOCUMENT_ROOT'].'/Upload',0777);
        }
        $firstLetterDir=$_SERVER['DOCUMENT_ROOT'].'/Upload/attachment/';
        if (!file_exists($firstLetterDir)||!is_dir($firstLetterDir)){
            mkdir($firstLetterDir,0777);
        }
        $upload->hashLevel=4;
        $info =$upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $error=1;
            $msg=$upload->getError();
        }else{// 上传成功 获取上传文件信息
            $error=0;
            $msg= substr($upload->rootPath,1,-1).$info['file']['savepath'].$info['file']['savename'];
            /*保存附件信息到系统附件统计数据库*/
            $datas=array(
                'name'=>$info['file']['name'],
                'size'=>intval($info['file']['size']),
                'createtime'=>date('Y-m-d H:i:s'),
                'type'=>$info['file']['type'].'-'.$info['file']['ext'],
                'path'=>$msg
            );
            M('attachment')->add($datas);
        }
        return array('error'=>$error,'msg'=>$msg);
    }



    function alert($msg) {
        header('Content-type: text/html; charset=UTF-8');
        //$json = new Services_JSON();
        echo json_encode(array('error' => 1, 'message' => $msg));
        exit;
    }


}