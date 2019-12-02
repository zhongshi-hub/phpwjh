<?php
namespace Plugs\Controller;
use Think\Controller;
/*
 * 公共上传类
 * */
class UploadController extends Controller {

    public function _initialize(){
        if(empty($_SESSION['user'])&&empty($_SESSION['system'])){
            if(I('get.Mod')) {
                $this->error('登录超时,请重新登录', U(I('get.Mod') . '/Login/index'));
            }else{
                $this->error('登录超时,请重新登录', U(I('post.Mod') . '/Login/index'));
            }
        }

    }

    #新版本微信图片上传
    public function upload_weixin(){
        $TypeKey=I('post.TypeKey');
        if($TypeKey){
            $MchWx=M('MchWeixin')->where(array('token'=>$TypeKey,'domain_auth'=>domain_auth()))->find();
            $Media = &  load_wechat('Media',$MchWx['id']);
            if(!$MchWx){
                $status=0;
                $msg="获取微信配置信息失败!";
            }
            /*文件上传安全检测*/
            if (empty($_FILES['file']['name'])) {
                $status=0;
                $msg="上传失败, 请选择要上传的文件！";
            }
            if ($_FILES['file']['error'] != 0) {
                $status=0;
                $msg="上传失败, 请重试.";
            }
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $size = intval($_FILES['file']['size']);
            $originname = $_FILES['file']['name'];
            $harmtype = array('htm','html','asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi','java');
            $max_size = 2*1024000;//系统最大允许大小
            if ($size >= $max_size) {
                $status=0;
                $msg="上传文件大小超过限制 最大2M";
            }elseif (empty($_FILES['file'])) {
                $status=0;
                $msg="没有上传内容";
            }elseif (in_array(strtolower($ext), $harmtype)) {
                $status=0;
                $msg="不允许上传此类文件";
            }elseif (!function_exists('imagecreate')){
                $status=0;
                $msg="php不支持gd库，请配置后再使用";
            }else{//上传队列开始
                //本地存储
                $return=$this->localUploads();
                if($return['error']==1){
                    $status=0;
                    $msg=$return['msg'];
                } else {
                    $data=array(
                        'media'=>'@'.getcwd().$return['msg'],
                    );
                    // 执行接口操作
                    $result = $Media->uploadForeverMedia($data,'image');
                    if($result===FALSE){
                        $status=0;
                        $msg=$Media->errMsg;
                    }else{
                        // 接口成功的处理 成功后存数据库信息
                        $arr = array(
                            'wid' => $MchWx['id'],
                            'media_id' => $result['media_id'],
                            'name' => getcwd().$return['msg'],
                            'update_time' => time(),
                            'url' => $result['url'],
                            'domain_auth' => domain_auth()
                        );
                        M('MchWeixinImage')->add($arr);
                        //删除本地此图片
                        @unlink (getcwd().$return['msg']);
                        $status=1;
                        $msg="上传成功";
                    }
                }
            }
        }else{
            $status=0;
            $msg='上传参数有误!请联系管理员!';
        }

        die(json_encode(array('status'=>$status,'msg'=>$msg,'file'=>$return['file'],'FilePath'=>$return['msg'],'WxData'=>$result,'TypeKey'=>I('post.TypeKey'))));

    }


    #新版本地上传
    public function upload_local(){
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
            $harmtype = array('htm','html','asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi','java');
            $max_size = 2*1024000;//系统最大允许大小
            if ($size >= $max_size) {
                $status=0;
                $msgs="上传文件大小超过限制 最大2M";
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
                    $url=U('Plugs/Upload/index',array('status'=>1,'url'=>base64_encode($return['msg'])));
                }
                //上传队列结束
            }
            die(json_encode(array('status'=>$status,'msg'=>$msgs,'file'=>$return['file'],'FilePath'=>$return['msg'],'TypeKey'=>I('post.TypeKey'),'url'=>$url)));
        }
    }



    #新版网络下载本地
    public function upload_network(){
        if(IS_POST){
            //网络下载
            if(isset($_POST['type'])&&$_POST['type']=='XunNetWorkDown'){
                $url = trim(htmlspecialchars_decode($_POST['url']));
                $savePath =  './Upload/attachment/'.date('Ymd').'/';// 设置附件上传目录

                if (!file_exists($_SERVER['DOCUMENT_ROOT'].$savePath)||!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)){
                    mkdir($_SERVER['DOCUMENT_ROOT'].$savePath,0777);
                }

                $resp = $this->getImage($url,$savePath);
                //保存图片到数据库
                if($resp['error']==0){
                    /*保存附件信息到系统附件统计数据库*/
                    if(domain_auth()) {
                        $_data = array(
                            'name' => $resp['file_name'],
                            'size' => $resp['size'],
                            'createtime' => date('Y-m-d H:i:s'),
                            'type' => 'Net_' . $resp['type'],
                            'path' => $msg,
                            'domain_auth' => domain_auth()
                        );
                    }else {
                        $_data = array(
                            'name' => $resp['file_name'],
                            'size' => $resp['size'],
                            'createtime' => date('Y-m-d H:i:s'),
                            'type' => 'Net_' . $resp['type'],
                            'path' => $msg,
                            'domain_auth' => domain_rel()
                        );
                    }
                    M('attachment')->add($_data);
                    $status=1;
                    $msgs='图片处理成功';
                    $url=U('Plugs/Upload/index',array('status'=>1,'url'=>base64_encode($resp['save_path'])));
                    //$this->success($resp['msg'],$url);
                }else{
                    $status=0;
                    $msgs=$resp['msg'];
                }

                die(json_encode(array('status'=>$status,'msg'=>$msgs,'file'=>$resp,'FilePath'=>$resp['save_path'],'TypeKey'=>I('post.TypeKey'),'url'=>$url)));

            }
        }

    }




    #公共上传
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
            $harmtype = array('htm','html','asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi','java');
            $max_size = C('FILE_SIZE_MAX')*1024000;//系统最大允许大小
            if ($size >= $max_size) {
                $status=0;
                $msgs="上传文件大小超过限制 最大".C('FILE_SIZE_MAX')."M";
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
                    $url=U('Plugs/Upload/index',array('status'=>1,'url'=>base64_encode($return['msg'])));
                }
                //上传队列结束
            }
            die(json_encode(array('status'=>$status,'msg'=>$msgs,'file'=>$return['file'],'FilePath'=>$return['msg'],'url'=>$url)));
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
                $savePath =  './Upload/attachment/'.date('Ymd').'/';// 设置附件上传目录

                if (!file_exists($_SERVER['DOCUMENT_ROOT'].$savePath)||!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)){
                    mkdir($_SERVER['DOCUMENT_ROOT'].$savePath,0777);
                }

                $resp = $this->getImage($url,$savePath);
                //保存图片到数据库
                if($resp['error']==0){
                    /*保存附件信息到系统附件统计数据库*/
                    if(domain_auth()) {
                        $_data = array(
                            'name' => $resp['file_name'],
                            'size' => $resp['size'],
                            'createtime' => date('Y-m-d H:i:s'),
                            'type' => 'Net_' . $resp['type'],
                            'path' => $msg,
                            'domain_auth' => domain_auth()
                        );
                    }else {
                        $_data = array(
                            'name' => $resp['file_name'],
                            'size' => $resp['size'],
                            'createtime' => date('Y-m-d H:i:s'),
                            'type' => 'Net_' . $resp['type'],
                            'path' => $msg,
                            'domain_auth' => domain_rel()
                        );
                    }
                    M('attachment')->add($_data);
                    $url=U('Plugs/Upload/index',array('status'=>1,'url'=>base64_encode($resp['save_path'])));
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
        /*if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }*/
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5,'msg'=>'目录错误'.$save_dir);
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
        $upload->savePath =  'attachment/';// 设置附件上传目录
        $upload->saveName = RandStr(20,1); //文件名称
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
            $msg= substr($upload->rootPath,1,-1).'/'.$info['file']['savepath'].$info['file']['savename'];
            /*保存附件信息到系统附件统计数据库*/
            if(C('FILE_UPLOAD_TYPE')=='Oss') {
                $set_path=ALI_OSS();
            }else{
                $set_path='';
            }
            if(domain_auth()) {
                $datas = array(
                    'name' => $info['file']['name'],
                    'size' => intval($info['file']['size']),
                    'createtime' => date('Y-m-d H:i:s'),
                    'type' => $info['file']['type'] . '-' . $info['file']['ext'],
                    'path' => $set_path . $msg,
                    'domain_auth' => domain_auth()
                );
            }else{
                $datas = array(
                    'name' => $info['file']['name'],
                    'size' => intval($info['file']['size']),
                    'createtime' => date('Y-m-d H:i:s'),
                    'type' => $info['file']['type'] . '-' . $info['file']['ext'],
                    'path' =>$set_path . $msg,
                    'domain_auth' => domain_rel()
                );
            }
            M('attachment')->add($datas);
        }
        return array('error'=>$error,'file'=>$info['file'],'msg'=>$set_path.$msg);
    }

    function localUploads(){
        $upload = new \Think\Uploads();
        $upload->maxSize  = 500*1024 ;
        $upload->allowExts  = array('jpg','gif','png','jpeg','pdf','tif','doc','docx','xls','xlsx');
        $upload->autoSub=1;
        $upload->rootPath  = './Upload/'; // 设置附件上传根目录
        $upload->savePath =  'attachment/';// 设置附件上传目录
        $upload->saveName = RandStr(20,1); //文件名称
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
            $msg= substr($upload->rootPath,1,-1).'/'.$info['file']['savepath'].$info['file']['savename'];

        }
        return array('error'=>$error,'file'=>$info['file'],'msg'=>$msg);
    }





    function alert($msg) {
        header('Content-type: text/html; charset=UTF-8');
        //$json = new Services_JSON();
        echo json_encode(array('error' => 1, 'message' => $msg));
        exit;
    }


}