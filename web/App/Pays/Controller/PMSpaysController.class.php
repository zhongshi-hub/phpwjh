<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
#民生银行通道
define("JAVA_DEBUG", true); //调试设置
define("JAVA_HOSTS", "127.0.0.1:8081"); //设置javabridge监听端口
define("JAVA_LOG_LEVEL", 3); //java.log_level: 0-6
class PMSpaysController extends Alleys_initBaseController {

    public function _initialize()
    {
        #基础信息
        $this->expanderCd='0199981042';
        $this->sign_key='aeab358778b311e8acfdf0c3b670876d';
        #API网关
        $this->api_url=array(
            'mch_in'=>'https://cpos.cmbc.com.cn:18080/tbm-server/mcht/UploadsMerchant.json',
            'file_in'=>'https://cpos.cmbc.com.cn:18080/tbm-server/mcht/MerchantAttachmentUploadAction.json',
            'mch_status'=>'https://cpos.cmbc.com.cn:18080/tbm-server/mcht/QueryMerchantAndPayAction.json',
            'ali'=>'http://scp.yufu99.com/scanpay-api/api/unifiedOrder20',
            'wx'=>'http://scp.yufu99.com/scanpay-api/api/wxGZHUnifiedOrder20',
            'alter_rate'=>'https://cpos.cmbc.com.cn:18080/tbm-server/mcht/MerchantChangeAccountAndScaleAction.json'
        );
        #异步回调
        $this->ccb_notify='http://www.xunmafu.com/Api/ccb_notify_url';
        #订单号
        $this->order_id=$this->orderNum;

    }






    #微信支付
    public function pay_wx_jsapi(){
        #获取集团客户号
        $appid=ccb_appid();
        if(!$appid){
            $this->error('未配置集团Appid,获取信息失败!');
        }else {
            $data = array(
                'merNo' => $this->Mdata['mch_id'],#商户号
                'orderNo' => $this->order_id,#商户订单号
                'channelFlag' => '01',#支付通道 00微信 01支付宝 02百付包 03翼支付 04qq 05京东
                'amount' => $this->data['total'] * 100,#订单金额 单位为分
                'reqId' => $this->order_id,#请求交易的流水号
                'reqTime' => date('YmdHis'),#请求时间
                'notifyUrl' => $this->ccb_notify,#异步通知url
                'goodsName' => $this->Sdata['name'],#商品名称
                'subAppId' => $appid,
                'subOpenId' => $this->data['openid'],#支付宝uerid
            );
            $data['signIn'] = self::pay_sign($data);#签名
            //rwlog('ccb_wx',$data);
            $res = curl_calls($this->api_url['wx'], $data);
            $res=json_decode($res,true);
            if($res['result']=='0000'){
                $array=array(
                    'mid'=>$this->data['sid'],
                    'store_id'=>$this->data['id'],
                    'agent_id'=>GetMchAid($this->data['sid']),
                    'new'=>serialize($data),
                    'data'=>serialize($this->data),
                    'rel'=>serialize($res),
                    'createtime'=>time(),
                    'mch_rate'=>$this->mch_rate,
                    'mch_id'=>$this->Mdata['mch_id'],
                    'service'=>'wx_jsapi',
                    'out_trade_no'=>$data['orderNo'],
                    'body'=>$this->Sdata['name'],
                    'total_fee'=>$this->data['total'], //存数据库按照分进行统计
                    'mch_create_ip'=>Get_Clienti_Ips(),
                    'sub_openid'=>$this->data['openid'],
                    'type'=>'T0',
                    'alleys'=>'MSpays',
                    'domain_auth'=>domain_auth(),
                    'is_raw'=>1,
                );
                $rel=M('mch_orders')->add($array);
                if($rel){
                    $pay_data=array(
                        'msg'=>'订单创建成功',
                        'type'=>'js',
                        'pay_info'=>array(
                            'appId'=>$res['appId'],
                            'timeStamp'=>$res['timeStamp'],
                            'nonceStr'=>$res['nonceStr'],
                            'package'=>$res['pack'],
                            'signType'=>$res['signType'],
                            'paySign'=>$res['paySign'],
                        ),
                        'out_trade_no'=>$data['orderNo'],
                        'result'=>'/Api/ccb_result/out_trade_no/'.$data['orderNo'],
                    );
                    $this->success($pay_data);
                }else{
                    $this->error('订单创建失败!请重新支付!');
                }
            }else{
                $this->error('错误代码'.$res['result'].'提示:'.$res['desc']);
            }
        }
    }


    #支付宝支付
    public function pay_ali_jsapi(){
        $data=array(
            'merNo'=>$this->Mdata['mch_id'],#商户号
            'orderNo'=>$this->order_id,#商户订单号
            'channelFlag'=>'01',#支付通道 00微信 01支付宝 02百付包 03翼支付 04qq 05京东
            'amount'=>$this->data['total']*100,#订单金额 单位为分
            'reqId'=>$this->order_id,#请求交易的流水号
            'reqTime'=>date('YmdHis'),#请求时间
            'notifyUrl'=>$this->ccb_notify,#异步通知url
            'goodsName'=>$this->Sdata['name'],#商品名称
            'alipayUserId'=>$this->data['openid'],#支付宝uerid
        );
        $data['signIn']=self::pay_sign($data);#签名
        $res = curl_calls($this->api_url['ali'], $data);
        $res=json_decode($res,true);
        if($res['result']=='0000'){
            $array=array(
                'mid'=>$this->data['sid'],
                'store_id'=>$this->data['id'],
                'agent_id'=>GetMchAid($this->data['sid']),
                'new'=>serialize($data),
                'data'=>serialize($this->data),
                'rel'=>serialize($res),
                'createtime'=>time(),
                'mch_rate'=>$this->mch_rate,
                'mch_id'=>$this->Mdata['mch_id'],
                'service'=>'ali_jsapi',
                'out_trade_no'=>$data['orderNo'],
                'body'=>$this->Sdata['name'],
                'total_fee'=>$this->data['total'], //存数据库按照分进行统计
                'mch_create_ip'=>Get_Clienti_Ips(),
                'sub_openid'=>$this->data['openid'],
                'type'=>'T0',
                'alleys'=>'MSpays',
                'domain_auth'=>domain_auth(),
                'is_raw'=>1,
            );
            $rel=M('mch_orders')->add($array);
            if($rel){
                $pay_data=array(
                    'msg'=>'订单创建成功',
                    'type'=>'js',
                    'pay_info'=>array(
                        'tradeNO'=>$res['tradeNo']
                    ),
                    'out_trade_no'=>$data['orderNo'],
                    'result'=>'/Api/ccb_result/out_trade_no/'.$data['orderNo'],
                );
                $this->success($pay_data);
            }else{
                $this->error('订单创建失败!请重新支付!');
            }
        }else{
            $this->error('错误代码'.$res['result'].'提示:'.$res['desc']);
        }
    }


    #民生费率变更接口 T1生效
    public function alter_rate(){
        $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();

        $map=array(
            'alleys'=>$this->data['alleys'],
            'cid'=>$this->data['cid'],
            'date'=> date('Ymd')
        );
        $Log=M('AlterRateLog')->where($map)->count();
        if($Log){
            $this->error('每个商户每天只允许变更费率一次!此商户今天已变更一次!(新费率T1(00:10后)生效)请明天再变更');
        }else {
            $data = array(
                'customerId' => $alleys['mch_id'],
                'payServices' => array( #支付服务
                    array('payService' => 'WEIXIN', #支付服务  WEIXIN-微信、 QQ-手机QQ、 ZFB-支付宝、 WEIXIN-GZH-微信千6、 WEIXIN-APP-微信千1、 WEIXIN-SK-微信0费率、 JD-京东、BD-百度
                        'scale' => $this->data['rates'], #单位:千分之，例如千6费率填6 非银行卡支付服务必填 费率(‰)
                    ),
                    array('payService' => 'ZFB', #支付服务  WEIXIN-微信、 QQ-手机QQ、 ZFB-支付宝、 WEIXIN-GZH-微信千6、 WEIXIN-APP-微信千1、 WEIXIN-SK-微信0费率、 JD-京东、BD-百度
                        'scale' => $this->data['rates'], #单位:千分之，例如千6费率填6 非银行卡支付服务必填 费率(‰)
                    ),
                ),
                'thdSysNme' => 'xunkey',#第三方应用名称
                'timestamp' => ccb_timestamp(),
                'encryptedSign' => self::sign($data, true), #签名串(密文)
                'summaryPlain' => self::sign($data, false), #摘要明文
            );
            $res = ccb_curl_calls($this->api_url['alter_rate'], $data);
            $res = json_decode($res, true);
            if ($res['reply']['returnCode']['type'] == 'S' && $res['reply']['returnCode']['code'] == 'AAAAAA') {
                if ($res['reply']['customerId']) {
                    $arr = array(
                        'alleys' => $this->data['alleys'],
                        'cid' => $this->data['cid'],
                        'old_rate' => $this->data['old_rates'],
                        'new_rate' => $this->data['rates'],
                        'date' => date('Ymd'),
                        'ctime' => date('YmdHis'),
                        'status' => 0,
                        'domain_auth' => domain_auth(),
                        'rel' => json_encode($res, JSON_UNESCAPED_UNICODE)
                    );
                    M('AlterRateLog')->add($arr);
                    $res['Msg']='变更成功,新费率T1生效(第二天零点后),每天只允许变更一次!';
                    $this->alter_rate_log(1,$res);
                    $this->success('商户费率变更成功!-T1生效!');
                } else {
                    $this->alter_rate_log(0,$res);
                    $this->error('费率变更失败!消息提示:' . json_encode($res, JSON_UNESCAPED_UNICODE));
                }
            } else {
                $this->alter_rate_log(0,$res);
                $this->error('费率变更失败!消息提示:' . $res['reply']['returnCode']['message']);
            }
        }
    }

    #民生费率变更接口 T1生效
    public function alter_bank(){
        $this->error('暂不支持结算信息变更');
    }

    #商户状态查询
    public function mch_status(){
        $data = array(
            'customerId' => $this->data['mch_id'],
            'thdSysNme' => 'xunkey',#第三方应用名称
            'timestamp' => ccb_timestamp(),
            'encryptedSign' => self::sign($data, true), #签名串(密文)
            'summaryPlain' => self::sign($data, false), #摘要明文
        );
        $res = ccb_curl_calls($this->api_url['mch_status'], $data);
        $res =json_decode($res,true);
        if($res['reply']['returnCode']['type']=='S'){
            switch ($res['reply']['resultData']['mstatus']){
                case 'CREATED';
                    $info='新建-审核中';
                    break;
                case 'NORMAL';
                    $info='正常-激活成功';
                    break;
                case 'DISPOSEING';
                    $info='已提交-处理中';
                    break;
                case 'REFUSED';
                    $info='信息已拒绝';
                    break;
                case 'CANCELLED';
                    $info='已作废';
                    break;
                default:
                    $info='未知'.$res['reply']['resultData']['mstatus'];
                    break;

            }
            if($res['reply']['resultData']['mstatus']=='NORMAL'){
                #通过后更新状态
                M('MchSellerAlleys')->where(array('mch_id'=>$this->data['mch_id']))->save(array('load_status'=>1));
                $this->success($info);
            }elseif ($res['reply']['resultData']['mstatus']=='CANCELLED'){
                M('MchSellerAlleys')->where(array('mch_id'=>$this->data['mch_id'],'alleys_type'=>'MSpays'))->save(array('load_status'=>null,'mch_id'=>null,'status'=>0));
                $this->success($info);
            }else{
                $this->error($info);
            }
            //dump($info);
        }else{
            $this->error('提示:' . $res['reply']['returnCode']['message'] . '-错误码:' . $res['reply']['returnCode']['code']);
        }

    }

    #商户进件
    public function mch_in(){
        #获取集团客户号
        $parent=ccb_parent();
        if(!$parent){
          $this->error('未配置集团商户号,获取失败!');
        }else {
            $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
            #判断是否上传门店景
            if(!$alleys['mch_img_m1']){
                $this->error('商户信息缺少门头照片,请编辑上传门头照片后再进件!');
            }
            if(!$alleys['mch_img_m2']){
                $this->error('商户信息缺少门店内景照片1(内景最低2张),请编辑上传门店内景照片后再进件!');
            }
            if(!$alleys['mch_img_m3']){
                $this->error('商户信息缺少门店内景照片2(内景最低2张),请编辑上传门店内景照片后再进件!');
            }
            switch ($alleys['mch_bank_list']) {
                case 15:
                    $accountType = '1';
                    break;
                default:
                    $accountType = '3';
                    break;
            }
            switch ($alleys['mch_bus_type']) {
                case '无营业执照':
                    $wx_Type='158';
                    $openType='1';
                    $gszcName='';
                    $legalIdType='1';
                    $legalIdName=$alleys['mch_card_name'];
                    $legalIdNumber=$alleys['mch_card_id'];
                    break;
                default:
                    $wx_Type=$alleys['mch_industry'];
                    $openType='C';
                    $gszcName=$alleys['qy_name'];
                    $legalIdType='A';
                    $legalIdName='';
                    $legalIdNumber=$alleys['qy_cid'];
                    break;
            }
            #如果是有营业执照 判断
            if($alleys['mch_bus_type']=='有营业执照'){
                if(!$alleys['mch_img_yyzz']){
                    $this->error('企业类型 必须包含营业执照附件,请上传营业执照后再进件!');
                }
            }
            #只能为个人账户
            if($alleys['mch_bank_type']!='个人账户'){
                $this->error('当前通道不支持对公户结算,请更换个人(法人)账户后进件!');
            }

            if (!$alleys['rate']) {
                $this->error('系统进行二次监测,此商户的(' . $this->data['alleys'] . ')通道费率未配置,请配置后进件,如有疑问!请联系技术!');
            } else {
                //$wx_arr = array('158');
                //$wx_Type = rand_one($wx_arr);
                #支付宝经营类目ID
                $ali_arr = array('2015050700000022', '2015050700000037', '2015091000058486', '2015091000060134', '2015062600009243');
                $ali_Type = rand_one($ali_arr);
                $data = array(
                    //'customerId' => 'MS0000002156889',
                    'expanderCd' => $this->expanderCd, #拓展商号
                    'merchantName' => $alleys['mch_name'],#商户名称
                    'merchantShortName' => $alleys['mch_name'],#商户简称
                    'merchantType' => '5210', #商户类型  5210-百货零售、7272-旅游管理服务、 8650-电影放映、0710-石油开采、 1000-其他
                    'merchantLevel' => '1',#商户级别 1-分店、2-商户、3-集团商户
                    'parentCustomerId' => $parent,#上级商户名
                    'openType' => $openType,#开户类型 1-个人、C-企业
                    'gszcName' => $gszcName,#工商注册名
                    'qualificationInfos' => array( #资质信息
                        array(
                            'legalIdType' => $legalIdType,#法人证件类型
                            'legalIdName' => $legalIdName,#法人姓名
                            'legalIdNumber' => $legalIdNumber,#法人证件号码
                        )
                    ),
                    'manageOrgId' => '1600', #管理机构
                    'merchantAddr' => $alleys['mch_address'], #商户地址
                    'province' => ccb_area($alleys['mch_provice']),#省份
                    'city' => ccb_area($alleys['mch_citys'],$alleys['mch_provice']),#城市
                    'county' => ccb_area($alleys['mch_district'],$alleys['mch_citys']),#区县
                    'accountType' => $accountType,#账户类型
                    'account' => $alleys['mch_bank_cid'],#账号
                    'accountName' => $alleys['mch_bank_name'],#账户名
                    'banckCode' => reload_bank_number($alleys['mch_bank_list']), #开户行号
                    'bankName' => reload_bank($alleys['mch_bank_list']), #开户行名
                    'openBranch' => $alleys['mch_linkbnk'], #开户网点 联行号
                    'merchantConsacts' => $alleys['mch_card_name'],#联系人姓名
                    'telephone' => $alleys['mch_tel'], #联系人电话
                    'payChennel' => 'PC0000000000002',#支付通道
                    'payServices' => array( #支付服务
                        array('payService' => 'WEIXIN', #支付服务  WEIXIN-微信、 QQ-手机QQ、 ZFB-支付宝、 WEIXIN-GZH-微信千6、 WEIXIN-APP-微信千1、 WEIXIN-SK-微信0费率、 JD-京东、BD-百度
                            'isOpen' => 'Y', #Y或N，默认是Y-开通
                            'scale' => $alleys['rate'], #单位:千分之，例如千6费率填6 非银行卡支付服务必填 费率(‰)
                            'countRole' => '垫资方', #结算主体 本商户、上级商户、不参与、垫资方 D0垫资模式结算主体必须为垫资方，           微信公众号模式结算主体必须为不参与
                            'debitCardPate' => $alleys['rate'], #借记卡费率( ‰)
                            'creditCardRate' => $alleys['rate'], #贷记卡费率( ‰)
                            'tradeType' => $wx_Type, #行业类型
                        ),
                        array('payService' => 'ZFB', #支付服务  WEIXIN-微信、 QQ-手机QQ、 ZFB-支付宝、 WEIXIN-GZH-微信千6、 WEIXIN-APP-微信千1、 WEIXIN-SK-微信0费率、 JD-京东、BD-百度
                            'isOpen' => 'Y', #Y或N，默认是Y-开通
                            'scale' => $alleys['rate'], #单位:千分之，例如千6费率填6 非银行卡支付服务必填 费率(‰)
                            'countRole' => '垫资方', #结算主体 本商户、上级商户、不参与、垫资方
                            'debitCardPate' => $alleys['rate'], #借记卡费率( ‰)
                            'creditCardRate' => $alleys['rate'], #贷记卡费率( ‰)
                            'tradeType' => $ali_Type, #行业类型
                            //'supply' => sft_imgToBase64('.' . $alleys['mch_img_z']),#图片 base64
                            //'supplyname' => 'pic_' . $alleys['id'] . '.jpg',#图片名称
                        ),

                    ),

                    'businessModel' => '4', #业务模式 1-普通,2-企业内收支两线,3-微信公众号,4-D0垫资
                    //'repaidPerson' => '传化支付有限公司', #垫资方
                    'repaidRate' => '0.2', #垫资费率
                    'minAmount' => '2', #单笔最低交易金额
                    'poundage' => '0', #单笔代付手续费金
                    'minRepaidAmount' => '0.2', #保底垫付手续费
                    'thdSysNme' => 'xunkey',#第三方应用名称
                    'timestamp' => ccb_timestamp(),
                    'encryptedSign' => self::sign($data, true), #签名串(密文)
                    'summaryPlain' => self::sign($data, false), #摘要明文


                );
                //rwlog('ccb_data',$data);
                //dump($data);
                $res = ccb_curl_calls($this->api_url['mch_in'], $data);
                $res =json_decode($res,true);
                //rwlog('ccb_res',$res);
                if($res['reply']['returnCode']['type']=='S'){
                      if($res['reply']['customerId']){
                          $save = array(
                              'mch_id' =>$res['reply']['customerId'],
                              'load_status'=>0,
                              'status'=>1,
                              'api_rel'=>serialize($res),
                          );
                          M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->save($save);
                          $file_in=self::file_in($save['mch_id']);
                          if($file_in){
                              $this->success('商户进件成功!-附件同步成功!');
                          }else {
                              $this->success('商户进件成功!-附件同步失败:'.$file_in);
                          }
                      }else{
                          $this->error('进件失败!消息提示:' .json_encode($res,JSON_UNESCAPED_UNICODE));
                      }
                }else{
                    $this->error('进件失败!消息提示:' . $res['reply']['returnCode']['message'] . '-错误码:' . $res['reply']['returnCode']['code']);
                }

            }
        }
    }


    #附件上传接口
    public function file_in($mch_id){
        $data = array(
            'customerId' => $mch_id,
            'thdSysNme' => 'xunkey',#第三方应用名称
            'timestamp' => ccb_timestamp(),
            'encryptedSign' => self::sign($data, true), #签名串(密文)
            'summaryPlain' => self::sign($data, false), #摘要明文
        );
        $file=self::mch_zip();
        $res = self::calls($this->api_url['file_in'], $data,$file);
        $res =json_decode($res,true);
        if($res['reply']['returnCode']['type']=='S'){
          return true;
        }else{
          return  '附件同步失败!提示:'.$res['reply']['returnCode']['message'] . '-错误码:' . $res['reply']['returnCode']['code'];
        }
    }


    public function calls($urls,$datas,$file) {
        if($file){
            //$datas['picFile']='@'.$file; //php5.4 语法
            $datas['file']=new \CURLFile(realpath($file)); //php5.6 语法
        }
        //rwlog('datas',$datas);
        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $urls);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type'=>'multipart/form-data',
        ));
        // 执行操作
        $res = curl_exec($ch);
        if ($res == NULL) {
            $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $res;
    }


    public  function mch_zip(){
        $mch=M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        $files_to_zip=array(
            $mch['mch_img_bank'],
            $mch['mch_img_z'],
            $mch['mch_img_p'],
            $mch['mch_img_s'],
            $mch['mch_img_m1'],
            $mch['mch_img_m2'],
            $mch['mch_img_m3'],
            $mch['mch_img_m4'],
            $mch['mch_img_yyzz'],
        );
        $file=array();
        foreach ($files_to_zip as $val){
            if("" != $val) {
                if (preg_match('/(http:\/\/)/i', $val)) {
                    $file_data = self::_NetDown($val);
                    if ($file_data['save_path']) {
                        $file[] = getcwd() . $file_data['save_path'];
                    }
                } else {
                    $file[] = getcwd() . $val;
                }
            }
        }
        //dump($file);
        create_zip($file,'mch_'.$mch['id'].'.zip');
        //return getcwd().'/Upload/mch_zip/mch_'.$mch['id'].'.zip';
        return getcwd().'/Upload/mch_zip/mch_'.$mch['id'].'.zip';
    }


    public function _NetDown($url){
        $savePath =  './Upload/data_tmp/'.date('Ymd').'/';// 设置附件上传目录
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].$savePath)||!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)){
            mkdir($_SERVER['DOCUMENT_ROOT'].$savePath,0777);
        }
        $resp = _getImage($url,$savePath);
        return $resp;
    }


    #进件签名
    public function sign($data, $type = true)
    {
        require_once('/home/java/ccb_jks/Java.inc');
        $System = new \Java("com.tesla.tunguska.cuppay.util.CipherSignUtil");
        $password = "xunkey";
        $alias = "xunkey";
        $file = "/home/java/ccb_jks/xunkey.jks";
        $encryptedSign = $System->paramSignAndEncryptBase64String($data, $password, $alias, $file);
        $summaryPlain = $System->paramMap2SHA1Base64String($data);
        $encryptedSign = sprintf($encryptedSign);
        $summaryPlain = sprintf($summaryPlain);
        if ($type) {
            return $encryptedSign;
        } else {
            return $summaryPlain;
        }
    }


    #支付签名
    public function pay_sign($data){
        $signPars = "";
        ksort($data);
        foreach($data as $k => $v) {
            if("" != $v && "signIn" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $sign_data=rtrim($signPars,'&').$this->sign_key;
        $sign = strtoupper(md5($sign_data));
        return $sign;
    }



}