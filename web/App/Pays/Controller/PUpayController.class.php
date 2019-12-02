<?php
namespace Pays\Controller;
use Pays\Controller\Alleys_initBaseController;
#网联通道
#D1接口
class PUpayController extends Alleys_initBaseController
{

    protected  $api;
    protected  $UConfig;
    protected  $url;
    public function _initialize()
    {
       /*
        * 测试信息
       */
       /*$this->UConfig=array(
           'orgId'=>'110355',
        );
        $this->api=array(
             'upload'=>'http://youyuan.buybal.com/fileService/uploadPicture.do',
             'url'=>'http://youyuan.buybal.com/api-front/unionpay.do',
        );*/

        $this->UConfig=array(
            'orgId'=>'00000',
        );
        $this->api=array(
            'upload'=>'http://all.buybal.com/juheFileService/uploadPicture.do',
            'url'=>'http://all.buybal.com/api-front/unionpay.do',
        );
        $this->url=array(
          'notifyUrl'=>'http://www.xunmafu.com/Api/u_notify_url',
          'returnUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/Api/u_return_url',
        );

        ##不能小于1元
        if ($this->data['total']) {
          if ($this->data['total'] < 1) {
              $this->error('金额不能小于1元');
          }
        }

    }


    public function pay_wx_jsapi(){
        $order_id=$this->orderNum;
        $arr=array(
           'requestNo'=>'N'.RandStr(5).date('YmdHis').$this->Sdata['id'],#交易请求流水号
           'version'=>'V1.0',#版本号
           'funCode'=>'0004',#接口类型码 固码下单0004  其他0005
           'settleType'=>'1',#-1表示D+0，0表示T+0，N表示T+N结算
           'productId'=>'0105',#0104-微信扫码支付 0105-微信固码支付  0106-微信刷卡支付（反扫） 0109-支付宝扫码支付 0110-支付宝刷卡支付（反扫）0111-支付宝固码支付
           'merNo' =>$this->Mdata['mch_id'],#商户号
           'outOrderNo'=>$order_id,#外部订单号
           'returnUrl'=>$this->url['returnUrl'].'/order_id/'.$order_id,#
           'notifyUrl'=>$this->url['notifyUrl'],#
           'transAmt'=>strval($this->data['total'] * 100),#交易金额，单位为分
           'commodityName'=>$this->Sdata['name'],#商品名称
           'subMerName'=>$this->Sdata['name'],#商户名称
           'orgId'=>$this->UConfig['orgId'],
        );
        $arr['signature']=Upay_sign($arr);
        $_data['reqJson']=urlencode(json_encode($arr,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
        $data=array();
        foreach ($_data as $key=>$val){
            $data[]=array(
                'name'=>$key,
                'val'=>$val,
            );
        }
        $array=array(
            'mid'=>$this->data['sid'],
            'store_id'=>$this->data['id'],
            'agent_id'=>GetMchAid($this->data['sid']),
            'new' => serialize($arr),
            'data'=>serialize($this->data),
            'rel'=>json_encode($data,JSON_UNESCAPED_UNICODE),
            'createtime'=>time(),
            'mch_rate'=>$this->mch_rate,
            'mch_id'=>$this->Mdata['mch_id'],
            'service'=>'wx_jsapi',
            'out_trade_no'=>$order_id,
            'body'=>$this->Sdata['name'],
            'total_fee'=>$this->data['total'], //存数据库按照分进行统计
            'mch_create_ip'=>Get_Clienti_Ips(),
            'sub_openid'=>$this->data['openid'],
            'type'=>'T1',
            'alleys'=>'Upay',
            'domain_auth'=>domain_auth(),
            'is_raw'=>1,
        );
        $rel = M('mch_orders')->add($array);
        if($rel) {
            #存储以上订单信息到数据库
            $pay_data = array(
                'msg' => '订单创建成功',
                'type' => 'form',
                'url' => $this->api['url'],
                'data' => json_encode($data),
            );
            $this->success($pay_data);
        }else{
            $this->error('支付预下单失败!请重试!');
        }
    }

    public function pay_ali_jsapi(){
        $order_id=$this->orderNum;
        $arr=array(
            'requestNo'=>'N'.RandStr(5).date('YmdHis').$this->Sdata['id'],#交易请求流水号
            'version'=>'V1.0',#版本号
            'funCode'=>'0004',#接口类型码 固码下单0004  其他0005
            'settleType'=>'1',#-1表示D+0，0表示T+0，N表示T+N结算
            'productId'=>'0111',#0104-微信扫码支付 0105-微信固码支付  0106-微信刷卡支付（反扫） 0109-支付宝扫码支付 0110-支付宝刷卡支付（反扫）0111-支付宝固码支付
            'merNo' =>$this->Mdata['mch_id'],#商户号
            'outOrderNo'=>$order_id,#外部订单号
            'returnUrl'=>$this->url['returnUrl'].'/order_id/'.$order_id,#
            'notifyUrl'=>$this->url['notifyUrl'],#
            'transAmt'=>strval($this->data['total'] * 100),#交易金额，单位为分
            'commodityName'=>$this->Sdata['name'],#商品名称
            'subMerName'=>$this->Sdata['name'],#商户名称
            'orgId'=>$this->UConfig['orgId'],
        );
        $arr['signature']=Upay_sign($arr);
        $_data['reqJson']=urlencode(json_encode($arr,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
        $data=array();
        foreach ($_data as $key=>$val){
            $data[]=array(
                'name'=>$key,
                'val'=>$val,
            );
        }
        $array=array(
            'mid'=>$this->data['sid'],
            'store_id'=>$this->data['id'],
            'agent_id'=>GetMchAid($this->data['sid']),
            'new' => serialize($arr),
            'data'=>serialize($this->data),
            'rel'=>json_encode($data,JSON_UNESCAPED_UNICODE),
            'createtime'=>time(),
            'mch_rate'=>$this->mch_rate,
            'mch_id'=>$this->Mdata['mch_id'],
            'service'=>'ali_jsapi',
            'out_trade_no'=>$order_id,
            'body'=>$this->Sdata['name'],
            'total_fee'=>$this->data['total'], //存数据库按照分进行统计
            'mch_create_ip'=>Get_Clienti_Ips(),
            'sub_openid'=>$this->data['openid'],
            'type'=>'T1',
            'alleys'=>'Upay',
            'domain_auth'=>domain_auth(),
            'is_raw'=>1,
        );
        $rel = M('mch_orders')->add($array);
        if($rel) {
            #存储以上订单信息到数据库
            $pay_data = array(
                'msg' => '订单创建成功',
                'type' => 'form',
                'url' => $this->api['url'],
                'data' => json_encode($data),
            );
            $this->success($pay_data);
        }else{
            $this->error('支付预下单失败!请重试!');
        }

    }


    #商户状态查询
    public function mch_status(){
        $arr=array(
            'mchntId'=>$this->data['mch_id'],
            'funCode'=>'0003',
            'orgId'=>$this->UConfig['orgId'],
        );
        $arr['signature']=Upay_sign($arr);
        $data['reqJson']=urlencode(json_encode($arr));
        //rwlog('reqJson',$data);
        $res = curl_calls($this->api['url'],$data,false,false);
        $res = json_decode($res,true);
        if($res['respCode']=='200'){
            switch ($res['mchntSt']){
                case 0:
                    $info='待审核';
                    break;
                case 1:
                    $info='审核通过';
                    break;
                case 2:
                    $info='审核中';
                    break;
                case 3:
                    $info='关闭';
                    break;
                default:
                    $info='状态代码:'.$res['mchntSt'];
                    break;
            }
            $this->error('商户状态:'.$info);
        }else{
            $this->error('错误码:'.$res['respCode'].'提示:'.$res['respDesc']);
        }
    }





    public function mch_in(){
        $alleys = M('MchSellerAlleys')->where(array('cid' => $this->data['cid'], 'alleys_type' => $this->data['alleys']))->find();
        if($alleys) {
            $rate = bcdiv($alleys['rate'], '1000', 4);
            //$rate = '0.1100';
            $arr=array(
              'mchntName'=>$alleys['mch_name'],#商户名称
              'funCode'=>'0001',#接口类型
              'outMchntId'=>$alleys['id'].date('Ymd'),#外部商户号
              'shortName'=>$alleys['mch_name'],#商户简称
              'contPhone'=>$alleys['mch_tel'],#联系手机
              'mchntType'=>'1',#0:企业，1:个体户
              'businessDesc'=>'158',#经营类目
              'provCd'=>ccb_area($alleys['mch_provice']),#营业地址省代码
              'cityCd'=>ccb_area($alleys['mch_citys'],$alleys['mch_provice']),#营业地址市代码
              'countyCd'=>ccb_area($alleys['mch_district'],$alleys['mch_citys']),#营业地址区县代码
              'busiAddress'=>$alleys['mch_address'],#营业街道地址
              'cardType'=>'0',#结算卡类型，0:对私，1:对公，若商户类型为个体户，则必须是对私
              'realName'=>$alleys['mch_bank_name'],#开户名称
              'cardNo'=>$alleys['mch_bank_cid'],#开户账号
              'pmsBankNo'=>$alleys['mch_linkbnk'],#开户行行联号
              'certNo'=>$alleys['mch_card_id'],#身份证号
              'mobile'=>$alleys['mch_bank_tel'],#预留手机号
              'certMeet'=>self::ImgUpload($alleys['mch_img_s']),#手持身份证/营业执照
              'cardCorrect'=>self::ImgUpload($alleys['mch_img_bank']),#银行卡正面/开户许可证
              'certCorrect'=>self::ImgUpload($alleys['mch_img_z']),#身份证正面
              'certOpposite'=>self::ImgUpload($alleys['mch_img_p']),#身份证背面
              //'mentouFileName'=>'',#门店照片
             // 'otherFileName'=>'',#其他照片
              'payList'=>array(
                  array(
                      'payType'=>0,#0:微信；1：支付宝
                      'd0FeeRate'=>$rate,#D0费率
                      'd0PayFee'=>30, #D0代付费
                      't1FeeRate'=>$rate,#T1费率
                  ),
                  array(
                      'payType'=>1,#0:微信；1：支付宝
                      'd0FeeRate'=>$rate,#D0费率
                      'd0PayFee'=>30, #D0代付费
                      't1FeeRate'=>$rate,#T1费率
                  )
              ),
              'orgId'=>$this->UConfig['orgId'],#支行号
            );
            $arr['signature']=Upay_sign($arr);
            $data['reqJson']=urlencode(json_encode($arr));
            //rwlog('reqJson',$data);
            $res = curl_calls($this->api['url'],$data,false,false);
            $res = json_decode($res,true);
            if($res['respCode']=='200'&&$res['mchntId']){
                $save = array(
                    'mch_id' =>$res['mchntId'],
                    'load_status'=>1,
                    'status'=>1,
                    'api_rel'=>serialize($res),
                );
                M('MchSellerAlleys')->where(array('cid'=>$this->data['cid'],'alleys_type'=>$this->data['alleys']))->save($save);
                $this->success('商户进件成功!-等待通道方审核后即可使用!');
            }else{
                $this->error('进件失败!错误码:'.$res['respCode'].'提示:'.$res['respDesc']);

            }
        }
    }





    #图片上传
    public function ImgUpload($data){
        $res=curl_calls($this->api['upload'],$data);
        $res=json_decode($res,true);
        if($res['respCode']=='0000'){
            return $res['fileName'];
        }else{
            return '';
        }
    }






}