<?php
namespace Pays\Controller;
use Pays\Controller\GatewayBaseController;
class GatewayController extends GatewayBaseController {


    public function api(){
      #基础验证 请求协议限制
      if(C('API_CONFIG.IS_HTTPS')==true){
          if(!self::IS_HTTPS()){die('No Https Error!');} //非Https直接输出
      }
      #独立Api域名限制
      if(C('API_CONFIG.IS_API_DOMAIN')==true&&C('API_CONFIG.API_DOMAIN')){ //启用独立Api域名
          if($_SERVER['HTTP_HOST']!=C('API_CONFIG.API_DOMAIN')){die('Api Domain Error!');}
      }
      if(IS_POST){
          #提交数据不能空
          if(!is_array($this->api_data)){self::json_data(103);}
          #判断商户Appid是否合法
          if(!$this->post['appid']){self::json_data(203);}
          $terminal=self::terminal_data($this->post['appid']);
          if(!$terminal){self::json_data(204);}
          if(1!=$terminal['status']){self::json_data(205);}
          #判断签名
          $sign =self::sign($this->api_data,$terminal['appkey']);
          if($sign!=$this->post['sign']){self::json_data(300);}
          #判断接口类型
          if (!$this->post['method']){self::json_data(501);}
          #判断系统是否有此接口
          if($this->post['method_api']){
              #如果指定接口按照指定接口
              $method_api=$this->post['method_api'];
          }else{
              #如果没有指定按照商户系统配置的默认接口
              $Seller=M('MchSeller')->where(array('id'=>$terminal['mch_id']))->getField('wx_alleys');
              $method_api='P'.$Seller;
          }

		  //流量操作
		  $is_flow=mch_is_flow($this->api_data['store_data']['sid']);
		  if($is_flow) {
			  //如果启用查询余额是否可用本次交易
			  $is_pay = mch_flow_is_pay($this->api_data['store_data']['sid'], ($this->api_data['total']/100));
			  if(false==$is_pay['status']){
				  self::json_data(400,'',$is_pay['msg']);
			  }
		  }
		  if($this->post['method']=='order_data'){
              self::order_data(array_merge($this->api_data, ['alley_data' => self::MchAlley($method_api)]));
          }elseif ($this->post['method']=='wx_js'||$this->post['method']=='ali_js'||$this->post['method']=='qq_js'){
              $this->randJsPayUrl();
		  }elseif ($this->post['method']=='order_query'||$this->post['method']=='unified_refund'){
          	//统一流水查询、退款操作
			  $order=M('MchOrders');
			  $alleys=$order->where(['out_trade_no'=>$this->api_data['out_trade_no']])->getField('alleys');
			  $module = A('Pays/P' . $alleys);
			  $modules = method_exists($module, 'api_' .$this->post['method']);
			  if ($modules) {
				  $data = R('Pays/P' . $alleys . '/api_' . $this->post['method'], array('data' => $this->api_data));
				  if (is_array($data)) {
					  self::json_data($data['code'], $data['data']);
				  } else {
					  self::json_data(601); //系统函数返回不合法；
				  }
			  } else {
				  self::json_data(502);
			  }
		  }else {
			  //系统轮询
			  $status=getFlowStatus($this->api_data['store_data']['sid']);
			  if($status){ //启用轮询接口
				  $apiType=explode('_',$this->post['method']);
				  $config=getMchPoll($this->api_data['store_data']['sid'],(($apiType[0]=='wx')?'wx':'ali'),$this->api_data['total']);
				  if($config['status']==1){
					  //进入轮询操作
					  $method_api='P'.$config['config']['alleys'];
					  $alley_data=[
						  'cid'=>$config['config']['mid'],
					  	  'rate'=>$config['config']['rate'],
					  	  'alleys'=>$config['config']['alleys'],
						  'mch_name'=>$config['config']['name'],
						  'mch_id'=>$config['config']['mch_id'],
						  'mch_key'=>$config['config']['mch_key'],
						  'mch_appid'=>$config['config']['mch_k1'],
						  'mch_k2'=>$config['config']['mch_k2'],
						  'mch_k3'=>$config['config']['mch_k3'],
						  'domain_auth'=>$config['config']['domain_auth']
					  ];
					  $module = A('Pays/' . $method_api);
					  $modules = method_exists($module, 'api_' . $this->post['method']);
					  if ($modules) {
						  $data = R('Pays/' . $method_api . '/api_' . $this->post['method'], array('data' => array_merge($this->api_data, ['alley_data' => $alley_data])));
						  if (is_array($data)) {
							  self::json_data($data['code'], $data['data']);
						  } else {
							  self::json_data(601); //系统函数返回不合法；
						  }
					  } else {
						  self::json_data(502);
					  }
				  }else{
					  self::json_data(400,'',$config['msg']);
				  }
			  }else {
				  $module = A('Pays/' . $method_api);
				  $modules = method_exists($module, 'api_' . $this->post['method']);
				  if ($modules) {
					  $data = R('Pays/' . $method_api . '/api_' . $this->post['method'], array('data' => array_merge($this->api_data, ['alley_data' => self::MchAlley($method_api)])));
					  if (is_array($data)) {
						  self::json_data($data['code'], $data['data']);
					  } else {
						  self::json_data(601); //系统函数返回不合法；
					  }
				  } else {
					  self::json_data(502);
				  }
			  }
          }
      }else{
         self::json_data(404);//请求不合法
      }
    }


	/**
	 * API JS相关类型生成支付连接
	 */
    public function randJsPayUrl(){
    	//增加异步数据
		$db=M('ApiNotify');
		#数据库是否存在此订单号 存在不在增加 不存在再增加此订单数据
		$count=$db->where(['out_trade_no'=>$this->api_data['oid']])->count();
		if(!$count){
			$notify_url = M('MchTerminal')->where(['appid' =>$this->api_data['appid']])->getField('notify_url');
			$url = $this->api_data['notify_url'] ? $this->api_data['notify_url'] : $notify_url;
			$data=[
				'out_trade_no'=>$this->api_data['oid'],
				'appid'=>$this->api_data['appid'],
				'add_time'=>date('YmdHis'),
				'pay_type'=>$this->post['method'],
				'notify_url'=>$url,
			];
			$db->add($data);
		}
    	//生成JS支付URL
		$cache_id=$this->api_data['appid'].'-'.$this->post['method'].'-'.$this->api_data['oid'].'-'.date('YmdHis').rand(111111,999999);
		$cache_id=strtoupper(md5($cache_id));
		$array=[
			'body'=>$this->api_data['body']?$this->api_data['body']:'移动支付买单',
			'mch_name'=>$this->api_data['store_data']['name'],
			'mid'=>$this->api_data['store_data']['sid'],
			'sid'=>$this->api_data['store_data']['id'],
			'callUrl'=>$this->api_data['callback_url'],
			'appid'=>$this->api_data['appid'],
			'payType'=>explode('_',$this->post['method'])[0],
			'method'=>$this->post['method'],
			'oid'=>$this->api_data['oid'],
            'total'=>$this->api_data['total'],
		];
		//将数据缓存
		$key=S($cache_id,$array,1800); //有效期30分钟
		$payUrl='http://'.$_SERVER['HTTP_HOST'].'/Pay/ApiPay?k='.$cache_id;
		if(!$key){ //缓存失败将下单失败
			self::json_data(8001);
		}
		$data=['code'=>100,'data'=>['result_code'=>'0000','result_msg'=>'预下单支付URL生成成功','method'=>$this->post['method'],'out_trade_no'=>$this->api_data['oid'],'code_url'=>$payUrl,'total'=> $array['total'],'create_time'=>date('YmdHis'),'nonce_str'=>uniqid()]];
		if (is_array($data)) {
			self::json_data($data['code'], $data['data']);
		} else {
			self::json_data(601); //系统函数返回不合法；
		}
	}

    /* 流水查询
     * 为了针对多通道这里单独为总接口 不必要去一个个在单个通道里去写
     * $data 为接口data数据 包含alley_data store_data 以及Api数据data
     * 2018年04月17日15:50:34 End
     * **/
    public function order_data($data=array()){
        $StartTime=$data['stime']?strtotime($data['stime']):strtotime(date('Ymd'));
        $EndTime=$data['etime']?strtotime($data['etime']):time();
        $StoreId=$data['store_id']?$data['store_id']:array('EXP','IS NOT NULL');
        switch ($data['status']){
            case 1:
                $Status=1;
                break;
            case 2:
                $Status=2;
                break;
            case 3:
                $Status=0;
                break;
            case 4:
                $Status=array('EXP','IS NOT NULL');
                break;
            default:
                $Status=1;
                break;
        }
        $where=['store_id'=>$StoreId,'status'=>$Status,'attach'=>$this->post['appid']];
        if($StartTime&&$EndTime) {
            //判断结束时间不能大于开始时间
            if ($StartTime > $EndTime) {
                self::json_data(1007);
            } else {
                $where['_string'] = "(`createtime`> '" . $StartTime . "') AND ( `createtime` < '" . $EndTime . "') ";
            }
        }

        $order=M('MchOrders');
        $OData=$order->where($where)->field('out_trade_no,transaction_id,createtime,time_end,status,total_fee,service,store_id')->select();
        $count=$order->where($where)->count();
        $amount=$order->where($where)->sum('total_fee');
        $amount=$amount*100;
        $arr=[];
        foreach ($OData as $k=>$val){
            $arr['out_trade_no']=$val['out_trade_no'];
            $arr['transaction_id']=$val['transaction_id'];
            $arr['create_time']=date('YmdHis',$val['createtime']);
            $arr['status']=$val['status'];
            $arr['time_end']=$val['time_end']?date('YmdHis',$val['time_end']):null;
            $arr['total_fee']=$val['total_fee']*100; //转换为分单位
            $arr['store_id']=$val['store_id'];
            $arr['service']=explode('_',$val['service'])[0];
        }
        #按照接口汇总输出
        if($OData) {
            $DataArr = ['result_code' => '0000', 'result_msg' => '数据查询成功', 'method' => 'order_data', 'count' => $count ? $count : 0, 'amount' => $amount ? $amount : 0, 'order_data' => $arr ? $arr : null, 'nonce_str' => uniqid()];
        }else{
            $DataArr = ['result_code' => '1111', 'result_msg' => '无交易流水', 'method' => 'order_data','nonce_str' => uniqid()];
        }
        self::json_data(100,$DataArr);
    }

    //是否是https协议验证
    public function IS_HTTPS(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        if($http_type=='https://'){
            return true;
        }else{
            return false;
        }
    }





}