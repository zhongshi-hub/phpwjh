<?php

namespace Tasks\Controller;
use Think\Controller;

class QrCodeController extends Controller
{

   /* public function _initialize()
    {
        //parent::_initialize();
    }*/


    public function adds(){
        $data=M('alimsn')->where(array('messageId'=>$this->data['MessageId']))->find();
        if($data['status']!=1){
            $task_data=unserialize($data['task_data']);
            self::set($task_data['aid'],$task_data['count'],'P',$data['auth_code'],$this->data['MessageId']);
        }
    }


	/**
	 * 更换域名后重新生成二维码
	 * 二维码数据变 编码不变
	 */
    public function randQr(){
		$code=M('Mch_codes')->select();
		$j=0;
		foreach ($code as $k=>$v){
			$codeId=$v['codes'];
			Vendor('qrcode');
			Vendor('XunCode');
			$QRcode = new \QRcode();
			$_path = "./Upload/Code/";
			$errorCorrectionLevel = "M";//容错级别
			$matrixPointSize = "6"; //生成图片大小
			$margin = "1";  //边缘留白
			$url=C('MA_DATA_URL')."/".$codeId;
			$_qrname = $codeId.".png";
			$QRcode->png($url, $_path . $_qrname, $errorCorrectionLevel, $matrixPointSize, $margin);
			$qr=new \XunCode();
			$qr->create($url,$codeId);
			$j++;
		}
		dump("本次重新生成{$j}个");
	}


    #创建收款码
    #代理ID 数量  品牌前缀  品牌授权码  任务ID
    public function set($aid,$sum,$N,$domain_auth){
        #然后在继续生成二维码
        Vendor('qrcode');
        Vendor('XunCode');
        $QRcode = new \QRcode();
        $_path = "./Upload/Code/";
        $errorCorrectionLevel = "M";//容错级别
        $matrixPointSize = "6"; //生成图片大小
        $margin = "1";  //边缘留白
        $j=0;
        for($i=0;$i<$sum;$i++){
            //生成二维码图片
            $Qid=self::_QrRand($N);
            $url=C('MA_DATA_URL')."/".$Qid;
            $_qrname = $Qid.".png";
            $QRcode->png($url, $_path . $_qrname, $errorCorrectionLevel, $matrixPointSize, $margin);
            $qr=new \XunCode();
            $_res=$qr->create($url,$Qid);
            $res=json_decode($_res,true);
            if($res['status']==1){
                $_data['aid']=$aid;
                $_data['status']=1;
                $_data['codes'] =$Qid;
                $_data['ctime'] =time();
                $_data['code_url']=ltrim($res['url'], ".");
                $_data['domain_auth'] =$domain_auth;
				$rt=M('Mch_codes')->data($_data)->add();
				if ($rt){
					$j++;
				}
            }
        }
        return $j;
    }


    //根据代理ID随机生成一个收款码ID
    public function agentCode($aid,$domain_auth){
        #然后在继续生成二维码
        Vendor('qrcode');
        Vendor('XunCode');
        //$data=I('get.');
        $QRcode = new \QRcode();
        $_path = "./Upload/Code/";
        $errorCorrectionLevel = "M";//容错级别
        $matrixPointSize = "6"; //生成图片大小
        $margin = "1";  //边缘留白

        //生成二维码图片
        $Qid=self::_QrRand('Y');
        $url=C('MA_DATA_URL')."/".$Qid;
        $_qrname = $Qid.".png";
        $QRcode->png($url, $_path . $_qrname, $errorCorrectionLevel, $matrixPointSize, $margin);
        $qr=new \XunCode();
        $_res=$qr->create($url,$Qid);
        $res=json_decode($_res,true);
        if($res['status']==1){
            $_data['aid']=$aid;
            $_data['status']=1;
            $_data['codes'] =$Qid;
            $_data['ctime'] =time();
            $_data['code_url']=ltrim($res['url'], ".");
            $_data['domain_auth'] =$domain_auth?$domain_auth:domain_auth();
            M('Mch_codes')->data($_data)->add();
            $returnData=['status'=>1,'msg'=>'预生成收款码成功','code'=>$Qid];
        }else{
            $returnData=['status'=>0,'msg'=>'预生成收款码失败'];
        }
       return $returnData;
    }

    public function _QrRand($N){
        $Qid=$N.RandStr(8);//生成的八位数字商户号
        $set=M('Mch_codes')->where(array('codes'=>$Qid))->count();
        if($set){//如果存在 继续执行
            self::_QrRand($N);
        }else{//不存在直接输出二维码ID
            return $Qid;
        }
    }






}