<?php
namespace Pays\Controller;
use Think\Controller;
class Alleys_CardinitBaseController extends Controller
{

    public function _initialize()
    {
        #测试
        /*$this->ApiUrl="http://121.201.111.67:9999/payment-gate-web/gateway/api/backTransReq";
        $this->merNo='800440054111002';*/
        #正式
        $this->ApiUrl="https://gateway.chinacardpos.com/payment-gate-web/gateway/api/backTransReq";
        $this->merNo='80041004816200001';
        $this->QmerNo='8004100481620002';



        if(IS_POST) {
            $this->data = I('post.');
            if($this->data['alleys']){
                $StopStatus=M('MchAlleys')->where(array('type'=>$this->data['alleys']))->getField('stop_status');
                if($StopStatus==1){
                    $this->error('当前通道维护,暂停交易!');
                }
            }
            #根据ID取配置信息
            //$alleys=M('MchSeller')->where(array('id'=>$this->data['sid']))->getField('alleys');
            #银行卡信息
            $this->bank_data=M('MchCardBank')->where(array('id'=>$this->data['bank_id'],'domain_auth'=>domain_auth()))->find();

            $alleys=$this->data['alleys'];
            if($this->data['cid']){
                $map['cid'] = $this->data['cid'];
            }else {
                $map['cid'] = $this->data['sid'];
            }
            $map['alleys_type']=$alleys;
            $SellerAlleys=M('MchSellerCardAlleys')->where($map)->find();
            #判断银行卡是否在支持结算的列表里
            $bank_list = M('MchBankList')->where(array('status' => 1,'qcard'=>1))->order('id asc')->getField('bnkcd',true);
            //dump($SellerAlleys);
            //rwlog('bank_data',$SellerAlleys['mch_bank_list']);
            //rwlog('bank_data',$bank_list);
            /*if($this->data['is_banks']!=1) {
                if (!in_array($SellerAlleys['mch_bank_list'], $bank_list)) {
                    $this->error('此银行结算卡不支持无卡快捷通道结算!请更换其它银行卡!');
                }
            }*/

            if(!$this->data['cid']) {
                if ($SellerAlleys) {
                    if ($SellerAlleys['load_status'] == 2) {
                        $this->error('商户通道被冻结!');
                    } elseif ($SellerAlleys['load_status'] != 1) {
                        $this->error('通道未激活!');
                    }
                }
            }
            $SellerStore=M('MchStore')->where(array('id'=>$this->data['id'],'sid'=>$this->data['sid']))->find();
            $this->Sdata=$SellerStore;
            #门店信息
            $this->Mdata=$SellerAlleys;
            #当前通道费率
            $this->mch_rate=$SellerAlleys['rate'];
            #取商户号
            $this->merchantNo=$SellerAlleys['mch_id'];
            $keys=unserialize($SellerAlleys['api_rel']);

            if(strstr($alleys,'Qcard')) {
                if ($this->data['total']) {
                    if ($this->data['total'] < 10) {
                        $this->error('金额不能小于10元');
                    }elseif ($this->data['total'] > 20000) {
                        $this->error('金额不能大于20000元');
                    }
                }
            }


            //异步回调URL
            $this->NotifyUrl='http://www.xunmafu.com/CardApi/';

            $this->orderNum=rand_out_trade_no();

            #T0不支持企业账户
            if($SellerAlleys['mch_bank_type']) {
                if ($SellerAlleys['mch_bank_type'] != '个人账户') {
                    $this->error('此商户未支持此支付类型! 编号:QY_0!');
                }
            }
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


    #银行卡鉴权
    /*public function card_validate($data){
        $host = "http://verifycard.market.alicloudapi.com/Verification4";
        $AppCode = "fa8e977370ad4177ac12188c42fe4ced";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $AppCode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $url = $host;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, self::datato($data));
        $res = curl_exec($curl);
        //rwlog('res',$res);
        return $res;
    }


    #鉴权日志
    public function card_validate_log($data,$res,$source){
        #取系统操作者信息
        if(MODULE_NAME=='System'){
            $op_id=$_SESSION['system']['id'];
            $system=1;
        }else{
            $op_id=$_SESSION['user']['id'];
            $system=0;
        }
        #根据CID取授权信息
        $domain_auth=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->getField('domain_auth');
        $arr=array(
          'alleys'=>$this->data['alleys'],
          'mid'=>$this->data['cid'],
          'card'=>$data['cardNo'],
          'cert'=>$data['certNo'],
          'name'=>$data['name'],
          'phone'=>$data['phone'],
          'op_id'=> $op_id,
          'sys'=>$system,
          'time'=>date('Y-m-d H:i:s'),
          'domain_auth'=>$domain_auth,
          'status'=>$res['status'],
          'msg'=>$res['msg'],
          'source'=>$source,
          'data'=>json_encode($data,JSON_UNESCAPED_UNICODE),
          'rel'=> json_encode($res,JSON_UNESCAPED_UNICODE),
        );
        M('CardValidateLog')->add($arr);
    }*/

    //数组拼接函数
    public function datato($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) { //拼接
            if("" != $v) {
                $outdata .= $k . "=" . $v . "&";
            }
        }
        $signPars .=substr($outdata,0,strlen($outdata)-1); //去除最后&
        return $signPars;
    }

    #生成唯一商户号
    public function rand_mch_id(){
        $FriSt = substr($this->data['alleys'], 0, 1 );
        $mch_id=$FriSt.RandStr(11);
        #判断此商户号是否存在
        $res=M('MchSellerCardAlleys')->where(array('mch_id'=>$mch_id))->count();
        if($res){
            self::rand_mch_id();
        }else{
            return $mch_id;
        }
    }

    public function array2String($arr){
        ksort($arr);
        $str = '';
        $arr_length = count($arr)-1;
        foreach( $arr as $key => $value ){
            $str.=$key.'='.$value.'&';
        }
        return urldecode(trim($str));

    }
    public function card_sign($data) {
        $data=self::datato($data);
        //rwlog('sign_data',$data);
        //$data=http_build_query($data);
        //读取私钥文件
        $priKey = file_get_contents(getcwd().'/Cert/QCARD/'.$this->merNo.'_prv.pem');
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);
        //释放资源
        openssl_free_key($res);
        return base64_encode($sign);
        //return $data;
    }

    public function Qcard_sign($data) {
        $data=self::datato($data);
        //rwlog('sign_data',$data);
        //$data=http_build_query($data);
        //读取私钥文件
        $priKey = file_get_contents(getcwd().'/Cert/QCARD/'.$this->QmerNo.'_prv.pem');
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);
        //释放资源
        openssl_free_key($res);
        return base64_encode($sign);
        //return $data;
    }

    public function test_card_sign($data) {
        $data=self::datato($data);
        //rwlog('sign_data',$data);
        //$data=http_build_query($data);
        //读取私钥文件
        $priKey = file_get_contents(getcwd().'/Cert/QCARD/800440054111002_prv.pem');
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);
        //释放资源
        openssl_free_key($res);
        return base64_encode($sign);
        //return $data;
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
        $domain_auth=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->getField('domain_auth');
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
        $domain_auth=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->getField('domain_auth');
        $alleys=M('MchSellerCardAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->find();
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