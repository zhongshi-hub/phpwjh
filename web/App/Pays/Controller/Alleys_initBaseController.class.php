<?php
namespace Pays\Controller;
use Think\Controller;
class Alleys_initBaseController extends Controller
{

    public function _initialize()
    {
    	
        if(IS_POST) {
            $this->data = I('post.');
            /*商户轮询操作判断*/
			$status=getFlowStatus($this->data['sid']);
			if($status){ //启用轮询接口
				$config=getMchPoll($this->data['sid'],(($this->data['type']=='wx')?'wx':'ali'),$this->data['total']);
				if($config['status']==1){
					//进入轮询信息操作
					$SellerAlleys=[
                          'mch_name'=>$config['config']['name'],
						  'mch_id'=>$config['config']['mch_id'],
						  'mch_key'=>$config['config']['mch_key'],
						  'mch_appid'=>$config['config']['mch_k1'],
						  'mch_k2'=>$config['config']['mch_k2'],
						  'mch_k3'=>$config['config']['mch_k3'],
					];
					$this->mch_rate=$config['config']['rate'];
				}else{
					$this->error($config['msg']);
				}

			}else {
				#根据ID取配置信息
				if($this->data['type']=='wx'){
					$alleys=M('MchSeller')->where(array('id'=>$this->data['sid']))->getField('wx_alleys');
				}else{
					$alleys=M('MchSeller')->where(array('id'=>$this->data['sid']))->getField('ali_alleys');
				}
				$map['cid']=$this->data['sid'];
				$map['alleys_type']=$alleys;
				$SellerAlleys=M('MchSellerAlleys')->where($map)->find();
				if($SellerAlleys) {
					if ($SellerAlleys['load_status'] == 2) {
						$this->error('商户通道被冻结!');
					}elseif ($SellerAlleys['load_status'] != 1) {
						$this->error('通道未激活!');
					}
				}
				$this->mch_rate=$SellerAlleys['rate'];
			}
            $SellerStore=M('MchStore')->where(array('id'=>$this->data['id'],'sid'=>$this->data['sid']))->find();
            $this->Sdata=$SellerStore;
            #门店信息
            $this->Mdata=$SellerAlleys;
            #当前通道费率

            #取商户号
            $this->merchantNo=$SellerAlleys['mch_id'];
            $keys=unserialize($SellerAlleys['api_rel']);
            #当前商户密钥信息
            $this->Deskey=$keys['desKey'];//加密密钥
            $this->Signkey=$keys['signKey'];//签名密钥
            $this->QueryKey=$keys['queryKey'];//查询密钥

            //异步回调URL
            $this->NotifyUrl='http://www.xunmafu.com/Api/';
            //全局异步回调地址
            $this->notify_url='http://www.xunmafu.com/Api/wlb_notify_url';
            #回调页面
            $this->call_url='http://www.xunmafu.com/Api/wlb_call_url';

            #商户订单号
            $this->orderNum=rand_out_trade_no();

        }else{
            die('ERROR: Not Mch GateWay');
        }
    }


    /*交易签名*/
    public function OrderSign($data){
        $signPars = "";
        foreach($data as $k => $v) { //拼接
            if("" != $v && "sign" != $k) {
                $this->out_data .=  $v . "#";
            }
        }
        $signPars .='#'.stripslashes($this->out_data).$this->Signkey;
        $sign = md5($signPars); //加密
        return $sign;
    }

    /*查询签名*/
    public function OuerySign($data){
        $signPars = "";
        foreach($data as $k => $v) { //拼接
            if("" != $v && "sign" != $k) {
                $this->out_data .=  $v . "#";
            }
        }
        $signPars .='#'.$this->out_data.$this->QueryKey ;
        $sign = md5($signPars); //加密
        return $sign;
    }


    #请求封装 网关 数据  1 GET 空 POST
    public  function curl_res($url,$data,$type=null){
        ##过滤空数组
        $data=array_filter($data);
        ksort($data);
        if($type==1){ //GET形式提交
            $datas = self::datato($data);

            $set=$url.'?'.$datas;
            //rwlog('set',$set);
            $res=curl_calls($set);
        }else {//POST
            $datas = json_encode($data, JSON_UNESCAPED_UNICODE);
            //rwlog('rd_mch_in',$datas);
            $res=curl_calls($url,$datas);
        }
        $res=json_decode($res,true);
        return $res;
    }

    //数组拼接函数
    public function datato($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v) {
                $outdata .= $k . "=" . urlencode($v) . "&";
            }
        }
        $signPars .=substr($outdata,0,strlen($outdata)-1); //去除最后&
        return $signPars;
    }

    //数据加密 卡号类
    public function encode($data){
        return encrypt($data,$this->CenKey);
    }

    //数据加密 卡号类
    public function e_encode($data){
        return encrypt($data,$this->EenKey);
    }
    //数据加密 卡号类
    public function l_encode($data){
        return encrypt($data,$this->LenKey);
    }

    //数据加密 卡号类
    public function tt_encode($data){
        return encrypt($data,$this->TTenKey);
    }

    //数据加密 卡号类
    public function r_encode($data){
        return encrypt($data,$this->RenKey);
    }
    //入网签名
    public function signs($data){
        $signPars = "";
        $data=array_filter($data);
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= $this->CsignKey;
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }

    //入网签名
    public function e_sign($data){
        $signPars = "";
        $data=array_filter($data);
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= $this->EsignKey;
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }

    //入网签名
    public function l_sign($data){
        $signPars = "";
        $data=array_filter($data);
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= $this->LsignKey;
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }

    //入网签名
    public function r_sign($data){
        $signPars = "";
        $data=array_filter($data);
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= $this->RsignKey;
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }
    //入网签名
    public function tt_sign($data){
        $signPars = "";
        $data=array_filter($data);
        ksort($data);
        $datas=json_encode($data,JSON_UNESCAPED_UNICODE);
        $signPars .= stripslashes($datas);
        $signPars .= $this->TTsignKey;
        $sign = strtoupper(md5($signPars)); //加密
        return $sign;
    }

    #费率修改日志
    public function alter_rate_log($type,$rel){
       if($type==1){
           $status=1;
       }else{
           $status=0;
       }
       #取系统操作者信息
       if(MODULE_NAME=='System'){
           $op_id=$_SESSION['system']['id'];
           $system=1;
       }else{
           $op_id=$_SESSION['user']['id'];
           $system=0;
       }

       #根据CID取授权信息
       $domain_auth=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->getField('domain_auth');
       #旧数据
       $old_data=array(
            'rate'=>$this->data['old_rate'],
            'rates'=>$this->data['old_rates'],
       );
       #新数据
       $new_data=array(
            'rate'=>$this->data['rate'],
            'rates'=>$this->data['rates'],
       );
       $arr=array(
          'cid'=>$this->data['cid'],
          'alleys'=>$this->data['alleys'],
          'type'=>'rate',
          'time'=>time(),
          'status'=>$status,
          'old_data'=>serialize($old_data),
          'new_data'=>serialize($new_data),
          'domain_auth'=>$domain_auth,
          'opid'=>$op_id,
          'system'=>$system,
          'rel'=>json_encode($rel,JSON_UNESCAPED_UNICODE),
       );
       M('MchAlterLog')->add($arr);
    }

    #结算信息变更日志
    public function alter_bank_log($type,$rel){
        if($type==1){
            $status=1;
        }else{
            $status=0;
        }
        #取系统操作者信息
        if(MODULE_NAME=='System'){
            $op_id=$_SESSION['system']['id'];
            $system=1;
        }else{
            $op_id=$_SESSION['user']['id'];
            $system=0;
        }

        #根据CID取授权信息
        $domain_auth=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->getField('domain_auth');
        $alleys=M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
        #旧数据
        $old_data=array(
            'mch_bank_name'=>$alleys['mch_bank_name'],
            'mch_bank_list'=>reload_bank($alleys['mch_bank_list']),
            'mch_bank_cid'=>$alleys['mch_bank_cid'],
            'mch_bank_provice'=>$alleys['mch_bank_provice'],
            'mch_bank_citys'=>$alleys['mch_bank_citys'],
            'mch_linkbnk'=>$alleys['mch_linkbnk'],
            'mch_bank_tel'=>$alleys['mch_bank_tel'],
        );
        #新数据
        $new_data=array(
            'mch_bank_name'=>$this->data['mch_bank_name'],
            'mch_bank_list'=>reload_bank($this->data['mch_bank_list']),
            'mch_bank_cid'=>$this->data['mch_bank_cid'],
            'mch_bank_provice'=>$this->data['mch_bank_provice'],
            'mch_bank_citys'=>$this->data['mch_bank_citys'],
            'mch_linkbnk'=>$this->data['mch_linkbnk'],
            'mch_bank_tel'=>$this->data['mch_bank_tel'],
        );
        $arr=array(
            'cid'=>$this->data['cid'],
            'alleys'=>$this->data['alleys'],
            'type'=>'bank',
            'time'=>time(),
            'status'=>$status,
            'old_data'=>serialize($old_data),
            'new_data'=>serialize($new_data),
            'domain_auth'=>$domain_auth,
            'opid'=>$op_id,
            'system'=>$system,
            'rel'=>json_encode($rel,JSON_UNESCAPED_UNICODE),
        );
        M('MchAlterLog')->add($arr);

    }



}