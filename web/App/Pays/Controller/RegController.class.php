<?php
namespace Pays\Controller;
use Pays\Controller\InitBaseController;

class RegController extends InitBaseController {

    #注册页面
    public function index(){
        #二次验证 防止码被篡改
        $store_id=M('MchCodes')->where(array('codes'=>$_SESSION['Reg']['codes']))->getField('store_id');
        if($store_id){
            $this->error('收款码ID已被其它商户注册,请更换收款码ID重新注册!ERROR:R1','',888);
        }
        if(!$_SESSION['Reg']['user_info']){
            $this->error('获取用户信息失败!请重新扫码','',888);
        }
        $aid=M('MchCodes')->where(array('codes'=>$_SESSION['Reg']['codes']))->getField('aid');
        #判断系统是否开启认证
        $auth=unserialize($this->system['auth_data']);
        if($auth['auth_status']==1){
            #开启认证了
            #判断此收款码是否被认证
            $where['codes']=$_SESSION['Reg']['codes'];
            $where['status']=1;
            //$where['openid']=$_SESSION['Reg']['user_info']['openid'];
            $user_auth=M('MchUserAuth')->where($where)->find();
            #获取代理是否开启认证
            $agent=M('MchAgent')->where(array('id'=>$aid))->getField('auth_status');
            if($agent==1) {
                if ($user_auth) {
                    #已经被认证占用
                    if ($user_auth['openid'] != $_SESSION['Reg']['user_info']['openid']) {
                        $this->error('此收款码已被其他商户认证!请更换收款码注册!', '', 888);
                    }
                } else {
                    #未查询到此收款码的认证信息
                    redirect('/Pay/auth');
                }
            }
        }

        $assign=array(
            'pro'=>json_encode(self::area(), JSON_UNESCAPED_UNICODE),
            'industry'=>self::industry(),
        );
        $this->assign($assign);
        //dump($auth);
        $this->display();
    }


    #注册页面第二步
    public function index2(){
        if(!$_SESSION['Reg']['user_info']){
            $this->error('获取用户信息失败!请重新扫码','',888);
        }
        $jsapi = self::jsapi();
        $assign = array(
            'options' => $jsapi,
            'bank_list'=>self::bank_list(),
            'pro'=>json_encode(self::area(), JSON_UNESCAPED_UNICODE),
        );
        $this->assign($assign);
        $this->display();


    }

    #注册页面第三步
    public function index3(){
        if(!$_SESSION['Reg']['user_info']){
            $this->error('获取用户信息失败!请重新扫码','',888);
        }
        $jsapi=self::jsapi();
        $assign=array(
            'options'=>$jsapi,
        );

        $this->assign($assign);
        $this->display();

    }

    public function test(){
        $this->display();
    }

    #用户信息提交
    public function mch_data_all(){
        $db=M('MchSeller');
        $data=I('post.');
        unset($data['token']);
        unset($data['verify']);
        $city=explode(' ',$data['citys']);
        $bank_city=explode(' ',$data['bank_city']);
        if($data['bus_type']=='有营业执照') {
            switch ($data['bank_type']) {
                case '企业账户':
                    $bank_cid = str_replace(' ', '', $data['qy_bank_cid']);
                    $bank_type='企业账户';
                    break;
                case '个人账户':
                    $bank_cid = str_replace(' ', '', $data['bank_cid']);
                    $bank_type='个人账户';
                    break;
            }
        }else{
            $bank_cid = str_replace(' ', '', $data['bank_cid']);
            $bank_type='个人账户';
        }
        #微信信息
        /*$wx_headimgurl=$data['wx_headimgurl']?$data['wx_headimgurl']:$_SESSION['Reg']['user_info']['headimgurl'];
        $wx_nickname=$data['wx_nickname']?$data['wx_nickname']:$_SESSION['Reg']['user_info']['nickname'];
        $wx_openid=$data['wx_openid']?$data['wx_openid']:$_SESSION['Reg']['user_info']['openid'];*/

        $wx_headimgurl=$_SESSION['Reg']['user_info']['headimgurl']?$_SESSION['Reg']['user_info']['headimgurl']:$data['wx_headimgurl'];
        $wx_nickname=$_SESSION['Reg']['user_info']['nickname']?$_SESSION['Reg']['user_info']['nickname']:$data['wx_nickname'];
        $wx_openid=$_SESSION['Reg']['user_info']['openid']?$_SESSION['Reg']['user_info']['openid']:$data['wx_openid'];



        $agent_id=M('MchCodes')->where(array('codes'=>$data['codes']))->getField('aid');
        $set=array(
            'codes'=>$data['codes'],
            'agent_id'=>$agent_id,
            'mch_name'=>$data['MchName'],
            'mch_tel'=>$data['telNo'],
            'mch_industry'=>$data['industry'],
            'mch_provice'=>$city[0],
            'mch_citys'=>$city[1],
            'mch_district'=>$city[2],
            'mch_address'=>$data['address'],
            'mch_bus_type'=>$data['bus_type'],
            'mch_bank_cid'=>$bank_cid,
            'mch_bank_type'=>$bank_type,
            'mch_bank_provice'=>$bank_city[0],
            'mch_bank_citys'=>$bank_city[1],
            'mch_linkbnk'=>$data['linkBnk'],
            'mch_bank_list'=>$data['bank_list'],
            'mch_bank_name'=>$data['bank_name'],
            'mch_bank_tel'=>$data['bank_tel'],
            'mch_card_name'=>$data['card_name'],
            'mch_card_id'=>str_replace(' ', '', $data['card_val']),
            'mch_img_z'=>$data['img-z'],
            'mch_img_p'=>$data['img-p'],
            'mch_img_s'=>$data['img-s'],
            'mch_img_sqh'=>$data['img-sqh'],
            'mch_img_yyzz'=>$data['img-yyzz'],
            'mch_img_bank'=>$data['img-bank'],
            'mch_wx_openid'=>$wx_openid,
            'mch_wx_name'=>$wx_nickname,
            'mch_wx_img'=>$wx_headimgurl,
            'domain_auth'=>domain_auth(),
            'ctime'=>time(),
            'status'=>0,
            'type'=>'default'
        );

        #判断唯一规则
        $code=M('MchCodes')->where(array('codes'=>$data['codes'],'domain_auth'=>domain_auth()))->find();
        if(!empty($code['mch_id'])||!empty($code['store_id'])){
            $this->error('此收款码已被注册!');
        }

        $tel=$db->where(array('mch_tel'=>$data['telNo'],'domain_auth'=>domain_auth()))->find();
        if($tel){
            $this->error('当前手机号已注册商户:'.$tel['mch_name']);
        }

        /*$tel=$db->where(array('mch_tel'=>$data['telNo'],'domain_auth'=>domain_auth()))->find();
        if($tel){
            $res=$db->where(array('mch_tel'=>$data['telNo'],'domain_auth'=>domain_auth()))->save($set);
        }else{
            $res=$db->add($set);
        }*/

        #保存信息到数据库
        $seller=$db->add($set);

        #自动创建一个门店
        $store=array(
            'sid'=>$seller,
            'name'=>$data['MchName'],
            'per_name'=>$data['card_name'],
            'per_phone'=>$data['telNo'],
            'uptime'=>time(),
            'pay_type'=>'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
            'domain_auth'=>domain_auth(),
            'status'=>1,
        );
        $store_id=M('MchStore')->add($store);

        #保存收款码门店信息
        $_codes=array(
            'mch_id'=>$seller,
            'store_id'=>$store_id
        );
        M('MchCodes')->where(array('codes'=>$data['codes']))->save($_codes);
        #自动创建一个店员
        $user=array(
            'sid'=>$seller,
            'store_id'=>$store_id,
            'username'=>$data['card_name'],
            'phone'=>$data['telNo'],
            'wx_name'=>$wx_nickname,
            'wx_openid'=>$wx_openid,
            'wx_imgurl'=>$wx_headimgurl,
            'status'=>1,
            'role_wx_temp'=>1,
            'role_order'=>1,
            'domain_auth'=>domain_auth(),
            'ctime'=>time()
        );
        $rel=M('MchStoreUser')->add($user);
        data_log('Code'.$data['codes'],$data);
        /*rwlog($data['codes'],$data);
        rwlog($data['codes'],$set);
        rwlog($data['codes'],$store);
        rwlog($data['codes'],$user);
        rwlog($data['codes'],$_SESSION);*/

        if($rel){
            $this->success('信息提交成功!',U('mch_status',array('mch_id'=>$seller)));
        }else{
            $this->error('信息提交失败!');
        }

    }




    #验证验证码是否有效
    public function verify_check(){
        $data=I('post.');
        $where['cardsn']=$data['cardSn'];
        $where['tel']=$data['tel'];
        $where['verify']=$data['verify'];
        $where['domain_auth']=domain_auth();
        $_res=M('MchVerify')->where($where)->find();
        $_c=time();
        $_e=$_res['createtime'];
        $minute=floor(($_c-$_e)%86400/60);
        $out_times=10;
        //测试专用验证码
        if($data['verify']=='162652'){
            $this->success('验证码成功!TEST');
        }else {
            if ($_res) {
                if ($minute > $out_times) {
                    $this->error('验证码已过期,请重新获取');
                } else {
                    $this->success('验证码成功');
                }
            } else {
                $this->error('验证码错误');
            }
        }
    }

    #获取验证码
    public function sms_check(){
        $data=I('post.');
        $where['tel']=$data['tel'];
        //判断是否存在此账户
        $_S=M('MchSeller')->where(array('mch_tel'=>$where['tel'],'status'=>1, 'domain_auth'=>domain_auth()))->count();
        if($_S){
            $this->error('该手机号已经注册');
        }else{
            //验证码  随机生成六位验证码
            $_data['verify']=RandStr(6);
            //创建时间
            $_data['createtime']= time();
            $_data['tel']= $data['tel'];
            $_data['cardsn'] = $data['cardSn'];
            $_data['domain_auth'] = domain_auth();
            //发送验证码
            $sms=ALI_SMS();
            $AliSms = new \Think\Alisms($sms);
            $sms_data=array(
                'mobile'=> $data['tel'], #接收手机号
                'code'  => $sms['sms_verify'],#验证码模板ID
                'sign'  => $sms['sms_sign'], #模板签名 必需审核通过
                'param' =>json_encode(array(
                    'code'=>$_data['verify'], #验证码
                   // 'product'=> '商户入驻',  #模板变量
                )),
            );

            if (sms_api() == 1) { #用阿里云通信接口
                $re = new_ali_sms($sms_data);
                if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = $data['cardSn'];
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }

                    //M('MchVerify')->add($_data);
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['Code'] . ".错误消息:" . $re['Message'];
                    $this->error($info);
                }
            }else {
                $re = $AliSms->sms_send($sms_data);
                if ($re['err_code'] == 0 && $re['success'] == true) {

                    $_where['tel'] = $data['tel'];
                    $_where['cardsn'] = $data['cardSn'];
                    $_where['domain_auth'] = domain_auth();
                    $re = M('MchVerify')->where($_where)->count();
                    #如果存在则保存
                    if ($re) {
                        M('MchVerify')->where($_where)->save($_data);
                    } else {
                        M('MchVerify')->add($_data);
                    }

                    //M('MchVerify')->add($_data);
                    $this->success('发送成功');
                } else {
                    $info = "错误代码:" . $re['code'] . ".错误消息:" . $re['sub_msg'];
                    $this->error($info);
                }
            }



        }
    }

    #联行
    public function bnkLink(){
        if (IS_POST) {
            $depositBnk = I('post.cityId');
            $city = I('post.city');
            $city =str_replace("市","",$city);
            $city =str_replace("地区","",$city);
            //根据ID筛选银行名称
            $Bnk = M('MchBankList')->where(array('bnkcd' => $depositBnk))->getfield('bnknm');
            //威富通提供的数据库
            /* $arr['address'] = array(array('like', "%" . $city . "%"), array('like', "%" . $Bnk . "%"), 'and');
             $data = M('MchBanks')->where($arr)->select();*/
            $arr=array(
                'city'=> array('like', "%" . $city . "%"),
                'bank'=> array('like', "%" . $Bnk . "%")
            );
            $data = M('BanksDataNew')->where($arr)->select();


            $list = array(
                'list' => $data,
            );
            echo json_encode($list);
        }
    }


    #A联行
    public function A_bnkLink(){
        $depositBnk = I('post.depositBnk');
        $city = I('post.cityId');
        $city =str_replace("市","",$city);
        $city =str_replace("地区","",$city);
        //根据ID筛选银行名称
        $Bnk = M('MchBankList')->where(array('bnkcd' => $depositBnk))->getfield('bnknm');
        //威富通提供的数据库
        /*$arr['address'] = array(array('like', "%" . $city . "%"), array('like', "%" . $Bnk . "%"), 'and');
        $data = M('MchBanks')->where($arr)->select();*/
        //20170612 最新联行号数据库
        $arr=array(
            'city'=> array('like', "%" . $city . "%"),
            'bank'=> array('like', "%" . $Bnk . "%")
        );
        $data = M('BanksDataNew')->where($arr)->select();
        // dump($arr);


        $list = array(
            'list' => $data,
        );
        echo json_encode($list);

    }


    #银行列表
    public function bank_list(){
        $bank_list=M('MchBankList')->where(array('status'=>1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
        return $bank_list;
    }



    #省市请求
    public function area_city(){
        $name=I('post.name');
        $data=M('CityData')->where(array('provice'=>$name))->distinct(true)->field('city,city_id')->getField('city', true);
        //$data=self::area($id);
        $_data[]='请选择';
        $data=array_merge($_data,$data);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    #区县
    public function area_disc(){
        $name=I('post.name');
        $data=M('CityData')->where(array('city'=>$name))->distinct(true)->field('district,district_id')->getField('district', true);
        //$data=self::area($id);
        $_data[]='请选择';
        $data=array_merge($_data,$data);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    #省市请求
    public function A_area_city(){
        $name=I('post.name');
        $data=M('CityData')->where(array('provice'=>$name))->distinct(true)->field('city,city_id')->getField('city', true);
        //$data=self::area($id);
        /*$_data[]='请选择';
        $data=array_merge($_data,$data);*/
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    #区县
    public function A_area_disc(){
        $name=I('post.name');
        $data=M('CityData')->where(array('city'=>$name))->distinct(true)->field('district,district_id')->getField('district', true);
        //$data=self::area($id);
        /*$_data[]='请选择';
        $data=array_merge($_data,$data);*/
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }



    #默认城市
    public function area($pid=0){
        /* if($pid){
             $map['pid']=$pid;
         }else{
             $map['pid']=0;
         }*/
        $_data[]='请选择';
        $data=M('CityData')->distinct(true)->field('provice,privice_id')->getField('provice', true);
        $data=array_merge($_data,$data);
        return $data;
    }

    #行业类别
    public function industry(){
        $res=M('mch_industry')->distinct(true)->field('pid,name')->order('name asc')->select();
        return $res;
    }


    #微信JSAPI
    public  function jsapi(){
        $wid= GetWxId('m');
        // 创建SDK实例
        $script = &  load_wechat('Script',$wid);
        $options = $script->getJsSign(get_url(), $timestamp, $noncestr, $appid);
        if($options===FALSE){
            // 接口失败的处理
            return $script->errMsg;
        }else{

            unset($options['jsApiList']);
            $options['jsApiList']=array('chooseImage','uploadImage');
            return $options;
        }
    }

    #微信图片上传示例
    public function upload_disc(){
        $wid= GetWxId('m'); //用商户微信号
        if(IS_POST){
            $p=I('post.');
            $media = &load_wechat('Xun',$wid);
            $media_id=$p['media_id'];
            $result = $media->getMedia($media_id);
            if ($result === FALSE) {
                // echo $media->errMsg;
                $this->error('图片上传失败!请重新上传! 错误消息:'.$media->errMsg);
            } else {
                #只图片上传
                if ($p['type'] == 'Images') {

                    $this->success('上传成功', $result);

                } elseif ($p['type'] == 'AliScan') { #阿里云接口识别信息
                    if($p['api']=='bank_card') {
                        $res = self::ToAli_Banks($result);
                        $res = json_decode($res, true);
                        #是否识别成功
                        if($res['status']==0){
                            #是否是借记卡 不能是信用卡
                            if($res['result']['type']!='借记卡'){
                                echo json_encode(array('status' => 0, 'info' => '结算卡不支持非借记卡的银行卡!'));
                                exit;
                            }
                            #不存在卡号
                            if(!$res['result']['number']){
                                echo json_encode(array('status' => 0, 'info' => '银行卡信息识别失败!请重新上传识别!'));
                                exit;
                            }
                            echo json_encode(array('status' => 1, 'info' => '识别成功', 'img_url' => $result, 'number' => $res['result']['number']));
                            exit;
                        }else{
                            echo json_encode(array('status' => 0, 'info' => '错误代码:'.$res['status'].' 错误消息:'.$res['msg']));
                            exit;
                        }
                    }
                    if($p['api']=='card') {
                        $res = self::ToAli_Card($result);
                        $res = json_decode($res, true);
                        $card=$res['outputs'][0]['outputValue']['dataValue'];
                        if($card){
                            $card_val=json_decode($card,true);
                            // rwlog('card',$card_val);
                            if($card_val['success']==true){
                                echo json_encode(array('status' => 1, 'info' => '识别成功', 'img_url' => $result,'name'=>$card_val['name'], 'number' => $card_val['num']));
                            }else{
                                echo json_encode(array('status' => 0, 'info' => '识别失败!请上传清晰正确的身份证正面!'));
                            }
                        }else{
                            echo json_encode(array('status' => 0, 'info' => '自动识别接口识别失败!'));
                        }

                    }
                }
            }
        }
    }



    #银行卡识别接口
    public function ToAli_Banks($file)
    {
        $host = "http://jisuyhksb.market.alicloudapi.com";
        $path = "/bankcardcognition/recognize";
        $method = "POST";
        $appcode = ali_appcode();
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $YDF_Data = 'pic=' . urlencode(imgToBase64('.'.$file));
        $url = $host . $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $YDF_Data);
        $res = curl_exec($curl);
        return $res;
    }

    /*身份证识别*/
    public function ToAli_Card($file)
    {
        $host = "https://dm-51.data.aliyun.com";
        $path = "/rest/160601/ocr/ocr_idcard.json";
        $method = "POST";
        $appcode = ali_appcode();
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");
        //$querys = "";
        $YDF_Data = "{
         \"inputs\": [
            {
            \"image\": {
                \"dataType\": 50,
                \"dataValue\": \"".imgToBase64('.'.$file)."\"
            },
            \"configure\": {
                \"dataType\": 50,
                \"dataValue\": \"{\\\"side\\\":\\\"face\\\"}\"
            }
          }
        ]
      }";
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $YDF_Data);
        $res = curl_exec($curl);
        return $res;

    }


    #商户状态
    public function mch_status(){
        $mch_id=I('get.mch_id');
        if(!$mch_id){
            $this->error('参数有误!','',888);
        }
        $map['domain_auth']=domain_auth();
        $map['id']=$mch_id;
        $seller=M('MchSeller')->where($map)->field('mch_name,status,info')->find();
        if(!$seller){
            $this->error('参数有误或商户不存在!如有疑问!请联系您的业务员!','',888);
        }
        $assign=array(
            'seller'=>$seller
        );
        $this->assign($assign);
        $this->display();
    }


}

