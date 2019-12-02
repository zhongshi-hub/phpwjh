<?php
namespace Pays\Controller;
use app\behavior\ActionLog;
use Think\Controller;
/*
 * 商户扫码网关核心验证
 * Chen
 * **/

class GatewayBaseController extends Controller
{

    protected  $termional;
    protected  $post;
    protected  $api_data;

    public function _initialize()
    {
        $arr=['send_notify','mns_notify','notify_data_save'];//不受验证的方法
        if(!in_array(ACTION_NAME,$arr)&&IS_POST) {
            $this->post = json_decode(file_get_contents("php://input"), true);
            $api_data = $this->post['data'];
            $this->terminal = self::terminal_data($this->post['appid']);
            //判断是否指定商户门店信息
            $db = M('MchStore');
            if ($api_data['store_id']) {
                $res = $db->where(['id' => $api_data['store_id'], 'sid' => $this->terminal['mch_id']])->field('id,sid,name')->find();
                if ($res) {
                    $api_data['store_data'] = $res;
                } else {
                    self::json_data(206);
                }
            } else { //如果不穿门店ID 则默认选择一个
                $res = $db->where(['sid' => $this->terminal['mch_id']])->limit(1)->field('id,sid,name')->find();
                if ($res) {
                    $api_data['store_data'] = $res;
                } else {
                    self::json_data(206);
                }
            }
            //自定义单号验证
			if($api_data['oid']){
				$len=mb_strlen($api_data['oid']);
				if($len<20||$len>40){self::json_data(1008,[$len]);}
				if (!preg_match("/^[a-z0-9A-Z]*$/", $api_data['oid']))
				{
					self::json_data(1009);
				}
				//验证订单是否存在
				$oC=M('mchOrders')->where(['out_trade_no'=>$api_data['oid']])->count();
				if($oC){self::json_data(1010);}
				//验证缓存订单是否存在
				if(S($api_data['oid'])){self::json_data(1010);}
				//JS类型接口每个订单缓存有效期一天  防止一天内重复
				if(explode('_',$this->post['method'])[1]=='js'){
					S($api_data['oid'],'is oid',86400);
				}
			}
            $api_data['appid'] = $this->post['appid'];
            $this->api_data = $api_data;
            self::DataNotNuLL();
        }
    }


    #必传字段判断
    public function DataNotNuLL(){
        $native_arr=array('wx_native','ali_native');
        $micropay_arr=array('wx_micropay','ali_micropay');
        if(in_array($this->post['method'],$native_arr)){
            if(!$this->api_data['nonce_str']){self::json_data(1003);}
            if(!$this->api_data['total']){self::json_data(1001);}
        }
        if(in_array($this->post['method'],$micropay_arr)){
            if(!$this->api_data['nonce_str']){self::json_data(1003);}
            if(!$this->api_data['total']){self::json_data(1001);}
            if(!$this->api_data['auth_code']){self::json_data(1004);}
        }
        if($this->post['method']=='order_query'||$this->post['method']=='unified_refund'){
            if(!$this->api_data['out_trade_no']){self::json_data(1005);}
            if(!$this->api_data['nonce_str']){self::json_data(1003);}
            #判断订单号是否在系统存在
            $rel=M('MchOrders')->where(array('out_trade_no'=>$this->api_data['out_trade_no'],'attach'=>$this->api_data['appid']))->count();
            if(!$rel){self::json_data(8002);}
        }
        if($this->post['method']=='order_data'){
            if(!$this->api_data['status']){self::json_data(1006);}
            if(!$this->api_data['nonce_str']){self::json_data(1003);}
        }
    }


    /*json格式输出*/
    public  function json_data($code,$data=null,$msg=''){
        header('Content-type: application/json');
        if($data){
            $sign=self::sign($data,$this->terminal['appkey']);
            $rel = ['code' => $code, 'msg' => $msg?$msg:self::CodeMsg($code), 'data' => $data,'sign'=>$sign];
        }else {
            $rel = ['code' => $code, 'msg' => $msg?$msg:self::CodeMsg($code)];
        }
        #增加异步数据 用来异步通知 只增加下单成功后的数据 且接口在$arr数组中
        $arr=['wx_js'.'ali_js','wx_native','wx_micropay','ali_native','wx_micropay'];
        if(in_array($data['method'],$arr)&&$data['out_trade_no']){
            //增加异步数据
            self::notify_data_save($data['out_trade_no'],$data['method'],$this->api_data['notify_url']);
        }
        exit(json_encode($rel,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));

    }


    /*错误码及提示内容*/
    public static function CodeMsg($code){
        $arr=array(
          '100'=>'成功',
          '103'=>'data参数不能为空',
          '300'=>'签名验证失败',
          '400'=>'失败',
          '404'=>'请求不合法',
          '203'=>'Appid不能为空',
          '204'=>'Appid不合法',
          '205'=>'当前终端已关闭',
          '206'=>'门店信息不存在',
          '501'=>'服务类型不能为空',
          '502'=>'服务接口不存在',
          '601'=>'网关数据不合法，请联系技术',
          '8001'=>'下单失败',
          '8002'=>'订单不存在',
          '1001'=>'金额不能为空或不合法',
          '1003'=>'nonce_str字段不能为空或不合法',
          '1004'=>'auth_code字段用户付款码编码不能为空或不合法',
          '1005'=>'out_trade_no字段不能为空或不合法',
          '1006'=>'status字段不能为空或不合法',
          '1007'=>'结束时间不能大于开始时间',
		  '1008'=>'订单号长度不合法,长度范围20-40字节',
		  '1009'=>'订单号规则不合法,规则范围a-z0-9A-Z',
		  '1010'=>'订单号重复,请重新生成唯一订单号',
        );
        return $arr[$code];
    }


    /*签名*/
    public static function sign($data=array(),$key,$type=null){
        ksort($data);
        $tmp = '';
        foreach ($data as $k => $v ) {
            if($k == 'sign' ||$k == 'store_data'||$k == 'appid' || $v == '' || $v == null) continue;
            $tmp .= $k . '=' . $v . '&';
        }
        $tmp .= 'key=' . $key;
        if($type){ //测试使用 字符串
            return $tmp;
        }else {
            return strtoupper(md5($tmp));
        }
    }


    /*商户信息*/
    public function terminal_data($appid){
		$res=M('MchTerminal')->where(array('appid'=>$appid))->find();
		if($res){
			return  $res;
		}else{
			self::json_data(204);
		}

    }

    /*商户通道信息*/
    public function MchAlley($type){
        $api_type=substr($type , 0 , 1);
        if($api_type=='P') { //P开通为移动支付  C或其他为无卡快捷
            $db = M('MchSellerAlleys');
        }else{
            $db = M('MchSellerCardAlleys');
        }
        $res=$db->where(['alleys_type'=>ltrim($type,$api_type),'cid'=>$this->terminal['mch_id']])->field('id,cid,rate,mch_id,mch_key,mch_appid,agent_id,domain_auth,api_rel')->find();
        $res['api_rel']=unserialize($res['api_rel']);
        return $res;
    }


    #增加异步数据
    public function notify_data_save($oid,$method,$notify_url=''){
       $db=M('ApiNotify');
       #数据库是否存在此订单号 存在不在增加 不存在再增加此订单数据
       $count=$db->where(['out_trade_no'=>$oid])->count();
       if(!$count){
           //查询此订单的终端号及异步地址
           $appid=M('MchOrders')->where(['out_trade_no'=>$oid])->getField('attach');
           if($appid){
               $data=[
                 'out_trade_no'=>$oid,
                 'appid'=>$appid,
                 'add_time'=>date('YmdHis'),
                 'pay_type'=>$method,
				 'notify_url'=>$notify_url,
               ];

               $db->add($data);
           }
       }
    }


}