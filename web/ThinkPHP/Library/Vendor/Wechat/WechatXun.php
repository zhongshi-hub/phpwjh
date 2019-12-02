<?php

namespace Wechat;

use Wechat\Lib\Common;
use Wechat\Lib\Tools;

/**
 * 讯码付微信私用封装
 *
 * @author XUNMaFu CCL <chencunlong@126.com>
 * @date 2017-05-22 18:50
 */
class WechatXun extends Common {

    const UPLOAD_MEDIA_URL = 'http://file.api.weixin.qq.com/cgi-bin';
    const MEDIA_GET_URL = '/media/get?';


    #JS微信图片保存本地 压缩处理
    public function getMedia($media_id){
        if (!$this->access_token && !$this->getAccessToken()) {
            return false;
        }
        $url_prefix = self::UPLOAD_MEDIA_URL;
        $url=$url_prefix.self::MEDIA_GET_URL . "access_token={$this->access_token}" . '&media_id=' . $media_id;
        $result = Tools::httpGet($url_prefix . self::MEDIA_GET_URL . "access_token={$this->access_token}" . '&media_id=' . $media_id);
        //dump($result);
        if ($result) {
            if (is_string($result)) {
                $json = json_decode($result, true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return $this->checkRetry(__FUNCTION__, func_get_args());
                }
            }

            $fileDir=$_SERVER['DOCUMENT_ROOT'].'/Upload/attachment/mch/';
            if (!file_exists($fileDir)||!is_dir($fileDir)){
                mkdir($fileDir,0777);
            }
            $rel_name='./Upload/attachment/mch/'.date('YmdHis').RandStr(18,1).'.jpg';
            $http=new \Org\Net\Http();
            $http::curlDownload($url,$rel_name);
            $image = new \Think\Image();
            $image->open($rel_name);
            $image->thumb(800, 800)->save($rel_name);
            return ltrim($rel_name,'.');
        }
        return false;
    }


    #获取access_token
    public function access_token(){
        return $this->access_token;
    }








}
