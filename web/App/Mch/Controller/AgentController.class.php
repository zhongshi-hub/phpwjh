<?php
namespace Mch\Controller;
use Mch\Controller\AgentInitBaseController;
class AgentController extends AgentInitBaseController {



	#商户状态
	public function mch_status()
	{
		$mch_id = I('get.mch_id');
		$store_id = I('get.store_id');
		if (!$mch_id || !$store_id) {
			$this->error('参数有误!', '', 888);
		}
		$map['domain_auth'] = domain_auth();
		$map['id'] = $mch_id;
		$seller = M('MchSeller')->where($map)->field('id,mch_name,status,info,auth_status')->find();
		$where['store_id'] = $store_id;
		$where['mch_id'] = $mch_id;
		$codes = M('MchCodes')->where($where)->getField('codes');

		if (!$seller) {
			$this->error('参数有误或商户不存在!如有疑问!请联系您的业务员!', '', 888);
		}
		$assign = array(
			'seller' => $seller,
			'codes' => $codes,
			'store_id' => $store_id,
		);
		$this->assign($assign);
		$this->display();
	}
	

	public function per_save(){
		$data = I('post.');
		$mch_type='企业';
		$db = M('MchSeller');
		unset($data['__TokenHash__']);
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$agent_id = M('MchCodes')->where(array('codes' => $data['codes']))->getField('aid');
		$set = array(
			'codes' => $data['codes'],
			'agent_id' => $agent_id,
			'mch_name' => $data['qy_name'],
			'mch_tel' => $data['telNo'],
			'mch_industry' => 158,
			'mch_provice' => $city[0],
			'mch_citys' => $city[1],
			'mch_district' => $city[2],
			'mch_address' => $data['address'],
			'mch_bus_type' => $mch_type,
			'mch_bank_cid' => str_replace(' ', '', $data['bank_cid']),
			'mch_bank_type' => $data['mch_bank_type']=='企业账户'?'企业账户':$data['mch_bank_type'],
			'mch_bank_type_s' => $data['mch_bank_type_s'],
			'mch_bank_provice' => $bank_city[0],
			'mch_bank_citys' => $bank_city[1],
			'mch_linkbnk' => $data['linkBnk'],
			'mch_bank_list' => $data['bank_list'],
			'mch_bank_name' => $data['mch_bank_type']=='企业账户'?$data['qy_name']:($data['mch_bank_type_s']==1?$data['bank_name']:$data['qy_fr_name']),
			'mch_bank_tel' => $data['bank_tel'],
			'mch_card_name' => $data['qy_fr_name'],
			'mch_card_id' => $data['qy_fr_cid'],
			'mch_img_z' => $data['img-z'],
			'mch_img_p' => $data['img-p'],
			'mch_img_s' => $data['img-s'],
			'mch_img_sqh' => $data['img-sqh'],
			'mch_img_yyzz' => $data['img-yyzz'],
			'mch_img_bank' => $data['img-bank'],
			'mch_img_m1' => $data['img-m1'],
			'mch_img_m2' => $data['img-m2'],
			'mch_img_m3' => $data['img-m3'],
			'mch_img_m4' => $data['img-m4'],
			'mch_img_m5' => $data['img-m5'],
			'card_time' => $data['card_time'],
			'qy_time' => $data['qy_time'],
			'mch_type'=>$data['mch_type'],
			'qy_fr_name'=>$data['qy_fr_name'],
			'qy_fr_cid'=>$data['qy_fr_cid'],
			'mch_img_auth_z'=>$data['img-auth-z'],
			'mch_img_auth_p'=>$data['img-auth-p'],
			'credit_card' => $data['credit_card'],
			'qy_name' => $data['qy_name'],
			'qy_cid' => $data['qy_cid'],
			'domain_auth' => domain_auth(),
			'ctime' => time(),
			'status' => 0,
			'type' => 'default'
		);
		rwlog('agent_test',$set);
		$check_verify = self::per_check_verify($data);
		#银行卡鉴权
		if($data['mch_bank_type_s']==1){
			$care_data = array(
				'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
				'certNo' => str_replace(' ', '', $data['card_val']), #身份证号
				'name' =>$data['bank_name'], #姓名
			);
		}else{
			$care_data = array(
				'cardNo' => str_replace(' ', '', $data['bank_cid']), #银行卡卡号
				'certNo' => str_replace(' ', '', $data['qy_fr_cid']), #身份证号
				'name' =>$data['qy_fr_name'], #姓名
			);
		}

		#判断此码身份鉴权次数 超过5次当前码就不可以使用
		$card_log = M('CardValidateLog')->where(array('mid' => $data['codes'], 'name' => $data['bank_name']))->count();
		if ($card_log > 5) {
			$this->error('由于您的信息鉴权失败超过5次,当前码ID被冻结!');
		} else {
			#鉴权$this->data['alleys']
			if($data['mch_bank_type']!='个人账户'){
				$res=[
					'status'=>1
				];
			}else {
				$res = card_validate_calls($care_data, '新户注册', $data['codes']);
			}
			if ($res['status'] == 1) {#鉴权成功
				if ($check_verify) {
					#判断唯一规则
					$code = M('MchCodes')->where(array('codes' => $data['codes'], 'domain_auth' => domain_auth()))->find();
					if (!empty($code['mch_id']) || !empty($code['store_id'])) {
						$this->error('此收款码已被注册!');
					}
					$tel = $db->where(array('mch_tel' => $data['telNo'], 'domain_auth' => domain_auth()))->find();
					if ($tel) {
						$this->error('当前手机号已注册商户:' . $tel['mch_name']);
					}
					#验证通过
					$seller = $db->add($set);
					#自动创建一个门店
					$store = array(
						'sid' => $seller,
						'name' => $data['qy_name'],
						'per_name' => $data['bank_name'],
						'per_phone' => $data['telNo'],
						'uptime' => time(),
						'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
						'domain_auth' => domain_auth(),
						'status' => 1,
					);
					$store_id = M('MchStore')->add($store);
					rwlog('per_save',$store_id);
					#保存收款码门店信息
					$_codes = array(
						'mch_id' => $seller,
						'store_id' => $store_id
					);
					M('MchCodes')->where(array('codes' => $data['codes']))->save($_codes);
					#自动创建一个店员
					$user = array(
						'sid' => $seller,
						'store_id' => $store_id,
						'username' => $data['bank_name'],
						'phone' => $data['telNo'],
						'status' => 1,
						'role_wx_temp' => 1,
						'role_order' => 1,
						'domain_auth' => domain_auth(),
						'ctime' => time()
					);
					$rel = M('MchStoreUser')->add($user);
					if ($rel) {
						#发送给审核员
						$ShUserTemplateId=GetPayConfig(domain_auth(), 'sh_user_template_id');
						$ShUserTemplateStatus=GetPayConfig(domain_auth(), 'sh_user_template_status');
						if($ShUserTemplateStatus==1&&$ShUserTemplateId){
							sendMchTemplateMessage($seller, 'sh_user');
						}
						$this->success('信息提交成功!', U('mch_status', array('mch_id' => $seller, 'store_id' => $store_id)));
					} else {
						$this->error('信息提交失败!');
					}
				} else {
					$this->error($check_verify);
				}
			} else {
				$this->error($res['msg']);
			}
		}
	}

	#验证码
	public function per_check_verify($data)
	{
		#判断必填项
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$text_len = self::text_len($data['qy_name']);
		$store_name = M('MchStore')->where(array('name' => $data['qy_name']))->count();
		if ($store_name) {
			$this->error('您输入的企业名称已存在!请更换后重试!');
		}
		if ($text_len < 5) {
			$this->error('企业名称不能小于5个汉字');
		}
		if (!$city[0] || $city[0] == '请选择') {
			$this->error('请选择所在城市-省份');
		}
		if (!$city[1] || $city[1] == '请选择') {
			$this->error('请选择所在城市-城市');
		}
		if (!$city[2] || $city[2] == '请选择') {
			$this->error('请选择所在城市-区县');
		}
		if (!$bank_city[0] || $bank_city[0] == '请选择') {
			$this->error('请选择开户行所在城市-省份');
		}
		if (!$bank_city[1] || $bank_city[1] == '请选择') {
			$this->error('请选择开户行所在城市-城市');
		}
		if (!$data['bank_list']) {
			$this->error('请选择开户银行');
		}
		if (!$data['linkBnk']) {
			$this->error('请选择开户支行');
		}
		if (!$data['img-z']) {
			$this->error('请上传法人身份证(正面)');
		}
		if (!$data['img-p']) {
			$this->error('请上传法人身份证(反面)');
		}
		if (!$data['img-yyzz']) {
			$this->error('请上传三证合一营业执照');
		}

		if (!$data['img-m1']) {
			$this->error('请上传门头照片');
		}
		if (!$data['img-m2']) {
			$this->error('请上传内景照片');
		}
		if (!$data['img-m3']) {
			$this->error('请上传收银台照片');
		}
//		if (!$data['img-s']) {
//			$this->error('请上传手持身份证照片');
//		}
//		if($data['mch_type']=='per') {
//			if (!$data['img-auth-z']) {
//				$this->error('请上传授权收款人身份证(正面)照片');
//			}
//			if (!$data['img-auth-p']) {
//				$this->error('请上传授权收款人身份证(反面)照片');
//			}
//			if (!$data['img-sqh']) {
//				$this->error('请上传收款人授权函照片');
//			}
//		}

		if($data['mch_bank_type']=='个人账户'){
			if($data['mch_bank_type_s']==1){
				//非法人结算
				if (!$data['img-auth-z']) {
					$this->error('请上传授权收款人身份证(正面)照片');
				}
				if (!$data['img-auth-p']) {
					$this->error('请上传授权收款人身份证(反面)照片');
				}
				if (!$data['img-sqh']) {
					$this->error('请上传收款人授权函照片');
				}
				if (!$data['bank_name']){
					$this->error('请输入开户姓名');
				}
				if (!$data['card_val']){
					$this->error('请输入结算人身份证号');
				}
				if (!$data['bank_tel']){
					$this->error('请输入结算人预留手机号');
				}
			}else{
				//法人结算
				if (!$data['bank_tel']){
					$this->error('请输入结算人预留手机号');
				}
			}

			if (!$data['img-bank']) {
				$this->error('请上传银行卡正面照片');
			}
		}else{
			if (!$data['img-bank']) {
				$this->error('请上传开户许可证照片');
			}
		}
		#判断验证码
		$where['cardsn'] = $data['codes'];
		$where['tel'] = $data['telNo'];
		$where['verify'] = $data['verify'];
		$where['domain_auth'] = domain_auth();
		$_res = M('MchVerify')->where($where)->find();
		$_c = time();
		$_e = $_res['createtime'];
		$minute = floor(($_c - $_e) % 86400 / 60);
		$out_times = 10;
		//测试专用验证码
		if ($data['verify'] == '162652') {
			return true;
		} else {
			if ($_res) {
				if ($minute > $out_times) {
					$this->error('验证码已过期,请重新获取');
				} else {
					return true;
				}
			} else {
				$this->error('验证码错误');
			}
		}
	}


	#验证码
	public function check_verify($data)
	{
		#判断必填项
		$city = explode(' ', $data['citys']);
		$bank_city = explode(' ', $data['bank_city']);
		$text_len = self::text_len($data['MchName']);

		$store_name = M('MchStore')->where(array('name' => $data['MchName']))->count();
		if ($store_name) {
			$this->error('您输入的商户名称已存在!请更换后重试!');
		}

		if ($text_len < 5) {
			$this->error('商户名称不能小于5个汉字');
		}

		if (!$city[0] || $city[0] == '请选择') {
			$this->error('请选择所在城市-省份');
		}
		if (!$city[1] || $city[1] == '请选择') {
			$this->error('请选择所在城市-城市');
		}
		if (!$city[2] || $city[2] == '请选择') {
			$this->error('请选择所在城市-区县');
		}

		if (!$bank_city[0] || $bank_city[0] == '请选择') {
			$this->error('请选择开户行所在城市-省份');
		}
		if (!$bank_city[1] || $bank_city[1] == '请选择') {
			$this->error('请选择开户行所在城市-城市');
		}
		if (!$data['bank_list']) {
			$this->error('请选择开户银行');
		}
		if (!$data['linkBnk']) {
			$this->error('请选择开户支行');
		}
		if (!$data['img-z']) {
			$this->error('请上传身份证(正面)');
		}
		if (!$data['img-p']) {
			$this->error('请上传身份证(反面)');
		}
//		if (!$data['img-s']) {
//			$this->error('请上传手持身份证照片');
//		}
		if (!$data['img-bank']) {
			$this->error('请上传银行卡正面照片');
		}
		if($data['mch_type']=='par') {
			if (!$data['img-m1']) {
				$this->error('请上传结算人站门头照片');
			}
			if (!$data['img-m2']) {
				$this->error('请上传门店内景照片');
			}
			if (!$data['img-m3']) {
				$this->error('请上传收银台照片');
			}
			if (!$data['img-s']) {
				$this->error('请上传手持身份证照片');
			}
		}


		#判断验证码
		$where['cardsn'] = $data['codes'];
		$where['tel'] = $data['telNo'];
		$where['verify'] = $data['verify'];
		$where['domain_auth'] = domain_auth();
		$_res = M('MchVerify')->where($where)->find();
		$_c = time();
		$_e = $_res['createtime'];
		$minute = floor(($_c - $_e) % 86400 / 60);
		$out_times = 10;
		//测试专用验证码
		if ($data['verify'] == '162652') {
			return true;
		} else {
			if ($_res) {
				if ($minute > $out_times) {
					$this->error('验证码已过期,请重新获取');
				} else {
					return true;
				}
			} else {
				$this->error('验证码错误');
			}
		}
	}



	public function mch_code_in(){
		$assign = array(
			'industry' => self::industry(),
			'per_industry' => self::industry('企业'),
			'par_industry' => self::industry('个体工商户'),
			'options' => self::jsapi(),
			'bank_list' => self::bank_list(),
		);
		$this->assign($assign);
		$this->display();
	}

	#微信JSAPI
	public function jsapi()
	{
		$wid = GetWxId('m');
		// 创建SDK实例
		$script = &load_wechat('Script', $wid);
		$options = $script->getJsSign(get_url(), $timestamp, $noncestr, $appid);
		if ($options === FALSE) {
			// 接口失败的处理
			return $script->errMsg;
		} else {

			unset($options['jsApiList']);
			$options['jsApiList'] = array('chooseImage', 'uploadImage');
			return $options;
		}
	}
	public function text_len($data)
	{
		$_data = mb_strlen($data);
		return ($_data);
	}

	#银行列表
	public function bank_list()
	{
		$bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
		return $bank_list;
	}

	#行业类别
	public function industry($type=null)
	{
		$res = M('mch_industry')->where(array('name'=>array('like','%'.$type.'%')))->distinct(true)->field('pid,name')->order('name asc')->select();
		return $res;
	}

	public function mch_in(){
		$this->display();
	}

    #商户列表
    public function mch_data(){
        $this->display();
    }

    public function AgCode(){
        $this->display();
    }

    #代理首页
    public function index(){
       // dump($this->agent_code);
        $db=M('MchOrders');
        $map['status']=1;
        $map['agent_id']=array('in',self::AgentAll());
        $map['domain_auth']=domain_auth();
        #数据汇总
        #总交易额
        $To_sum=$db->where($map)->sum('total_fee');
        #总笔数
        $To_count=$db->where($map)->count();
        #商户数量
        $ToMch=M('MchSeller')->where($map)->count();

        #今日汇总
        $J_STime=date("Y-m-d");
        $J_ETime=date('Y-m-d',strtotime("1 day"));
        $J_where['_string'] = "(`time_end`> '" . strtotime($J_STime) . "') AND ( `time_end` < '" . strtotime($J_ETime) . "') ";
        #统计昨日总交易额
        $J_Sum=$db->where($map)->where($J_where)->sum('total_fee');
        #昨日总笔数
        $J_Count=$db->where($map)->where($J_where)->count();
        #今日总商户
        $j_maps['_string'] = "(`ctime`> '" . $J_STime. "') AND ( `ctime` < '" . $J_ETime. "') ";
        $J_Mch=M('MchSeller')->where($map)->where($j_maps)->count();

        #昨日汇总
        $STime=date("Y-m-d",strtotime("-1 day"));
        $ETime=date('Y-m-d');
        $where['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        #统计昨日总交易额
        $GoSum=$db->where($map)->where($where)->sum('total_fee');
        #昨日总笔数
        $GoCount=$db->where($map)->where($where)->count();
        #昨日商户数
        $_maps['_string'] = "(`ctime`> '" . $STime. "') AND ( `ctime` < '" . $ETime. "') ";
        $GoMch=M('MchSeller')->where($map)->where($_maps)->count();

        $assign=array(
            'Go'=>array(
                'sum'=>round($GoSum,2),
                'count'=>$GoCount,
                'mch'=>$GoMch,
            ),
            'Day'=>array(
                'sum'=>round($J_Sum,2),
                'count'=>$J_Count,
                'mch'=>$J_Mch,
            ),
            'To'=>array(
                'sum'=>round($To_sum,2),
                'count'=>$To_count,
                'mch'=>$ToMch,
            ),
        );
        $this->assign($assign);
        $this->display();
    }

    #代理个人资料
    public function person(){
        $where['id'] = $_SESSION['ag']['id'];
        $where['domain_auth']=domain_auth();
        $data=M('MchAgent')->where($where)->find();
        $rate=unserialize($data['rate']);
        unset($rate['__TokenHash__']);

        $map['cid']=domain_id();
        $map['status']=1;
        $api=M('DomainAlleys')->where($map)->field('alleys,alleys_type')->select();
        $assign=array(
            'data'=>$data,
            'rate'=>$rate,
            'api'=>$api
        );
        $this->assign($assign);
        $this->display();
    }


    #代理商户门店列表
    public function store_list(){
        $this->display();
    }


    #我的页面
    public function my(){
        $this->display();
    }

    #流水页面
    /*public function order(){
        $this->display();
    }*/

    #商户列表检索
    public function mch_data_seach(){
        $assign=array(
          'aid'=>self::AgentAll(),
        );
        $this->assign($assign);
        $this->display();
    }

    #商户列表
    public function mch_data_json(){
        $p=I('param.');
        $_count=$p['page_data']?$p['page_data']:5;
        if($p['search_val']) {
            //$maps['mch_tel'] = array('like', '%'.$p['name'].'%');
            $_maps['mch_name|mch_tel|mch_card_name']=array('like','%'.$p['search_val'].'%');
        }
        $maps['agent_id']=$p['agent_id']?$p['agent_id']:array('in',self::AgentAll());

        $STime=$p['stime']?$p['stime']:'';
        $ETime=$p['etime']?$p['etime']:date('Y-m-d',strtotime("1 day"));
        $_maps['_string'] = "(`ctime`> '" . strtotime($STime) . "') AND ( `ctime` < '" . strtotime($ETime) . "') ";

        $maps['domain_auth'] = domain_auth();
        $Data = M('MchSeller');
        $count = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page = new \Think\Mpage($count, $_count);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $Data->order('id')->where($maps)->where($_maps)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->field('auth_status,id,agent_id,mch_name,mch_tel,mch_card_name,status,ctime')->select();
        $rel=array();
        foreach ($list as $v) {
            switch ($v['status']){
                case 1:
                    $status='正常';
                    break;
                case 0:
                    $status="审核中";
                    break;
                case 2:
                    $status='拒绝';
                    break;
                case 3:
                    $status='冻结';
                    break;
                default:
                    $status='未知';
                    break;
            }
            switch ($v['auth_status']){
                case 1:
                    $auth_status='已实名认证';
                    $auth_color='#4cd964';
                    break;
                default:
                    $auth_status='未实名认证';
                    $auth_color='#9c9c9c';
                    break;
            }
            $res['ctime']= date('Y-m-d H:i:s', $v['ctime']);
            $res['aid']=agent_name($v['agent_id']);
            $res['mch_name']=$v['mch_name'];
            $res['mch_tel']=$v['mch_tel'];
            $res['card_name']=$v['mch_card_name'];
            $res['status']=$status;
            $res['auth_status']=$auth_status;
            $res['auth_color']=$auth_color;
            $res['id']=$v['id'];
            $rel[]=$res;
        }

        #总商户数
        $sum=$Data->order('id')->where($maps)->where($_maps)->count();

        #活跃商户数
        $Mch=M('MchOrders')->where($maps)->where(array('status'=>1))->distinct(true)->getField('mid',true);

        $_data=array(
            'pages'=>$Page->totalPages,
            'data'=>$rel,
            'sum'=>$sum,
            'count'=>count($Mch)
        );
        echo json_encode($_data);
        exit;
    }


    #收款码列表
    public function qrcode_data_json(){
        $p=I('param.');
        $_count=$p['page_data']?$p['page_data']:10;
        $maps['store_id']=array('EXP','IS NULL');
        $maps['aid']=$_SESSION['ag']['id'];
        $maps['domain_auth'] = domain_auth();
        $Data = M('Mch_codes');
        $count = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page = new \Think\Mpage($count, $_count);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($maps)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->field('aid,code_url,ctime,codes')->select();
        $rel=array();
        foreach ($list as $v) {
            $res['ctime']= date('Y-m-d H:i:s', $v['ctime']);
            $res['aid']=agent_name($v['aid']);
            $res['code_url']=$v['code_url'];
            $res['code_surl']=sc_codes($v['codes']);
            $res['codes']=$v['codes'];
            $res['auth']=code_auth($v['codes']);
            $rel[]=$res;
        }

        $_data=array(
            'pages'=>$Page->totalPages,
            'data'=>$rel,
        );
        echo json_encode($_data);
        exit;


    }


    #流水数据
    /*public function order_data_json(){
        $db=M('MchOrders');
        $map['agent_id']=array('in',self::AgentAll());

        $data=I('param.');

        $STime=$data['stime']?$data['stime']:'';
        $ETime=$data['etime']?$data['etime']:date('Y-m-d',strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        $map['service']=$data['pay_type']?$data['pay_type'].'_jsapi':array('EXP','IS NOT NULL');
        $map['out_trade_no']=$data['out_trade_no']?array('like','%'.$data['out_trade_no'].'%'):array('EXP','IS NOT NULL');
        $map['status']=1;
        $map['domain_auth']=domain_auth();
        $list = $db->where($map)->order('id desc')->field('service,out_trade_no,store_id,total_fee,createtime')->select();
        #根据日期筛选
        $visit_list =array();
        $ret=array();
        foreach ($list as $v) {
            $_Day= date('Y年m月d日', $v['createtime']);
            $visit_list[$_Day][] =$v ;
        }
        foreach ($visit_list as $key=>$day) {
            $_set['day']=$key;
            $_set['count']=count(self::list_data($day));
            $_set['sum']=number_format(array_sum(self::total_data($day)),2);
            $_set['data']=self::list_data($day);
            $ret[]=$_set;
        }
        #总交易额
        $sum = $db->order('id')->where($map)->sum('total_fee');
        $_count =$db->order('id')->where($map)->count();
        $count=count($ret);
        $Page=new \Think\Mpage($count,5);
        $list=array_slice($ret,$Page->firstRow,$Page->listRows);
        $_data=array(
            'pages'=>$Page->totalPages,
            'data'=>$list,
            'sum'=>Rand_total($sum),
            'count'=>$_count,
        );
        echo json_encode($_data);
        exit;

    }*/

    function array_page($array,$rows){
        $count=count($array);
        $Page=new \Think\Mpage($count,$rows);
        $list=array_slice($array,$Page->firstRow,$Page->listRows);
        return $list;

    }

    public function total_data($list){
        $total=array();
        foreach($list as $value){
            $total[]=$value['total_fee'];
        }
        return $total;
    }

    public function list_data($list){
        $res=array();
        foreach ($list as $v){
            if($v['service']=='ali_jsapi'){
                $img='/Source/statics/ali.png';
            }else{
                $img='/Source/statics/wx.png';
            }
            $store=Get_Store($v['store_id']);
            $rel['service']=$img;
            $rel['out_trade_no']=$v['out_trade_no'];
            $rel['store_id']=msubstr($store['name'],0,8);
            $rel['total_fee']=$v['total_fee'];
            $rel['createtime']=date('H:i:s',$v['createtime']);
            $rel['out_end']=substr($v['out_trade_no'],-4);
            $res[]=$rel;
        }
        return $res;
    }


    #测试 归类
    public function groupVisit($visit)
    {
        $visit_list = [];
        foreach ($visit as $v) {
            $date = date('Y年m月d日', $v['createtime']);
            $visit_list[$date][] = $v;
        }
        return $visit_list;
    }


    #订单详细页面
   /* public function trade_data(){
        $map['out_trade_no']=I('get.order_id');
        $map['agent_id']=array('in',self::AgentAll());
        $map['domain_auth']=domain_auth();
        $db=M('MchOrders')->where($map)->find();
        if(!$db){
            $this->error('您输入的流水订单号不存在!');
        }
        $this->assign('data',$db);
        $this->display();
    }*/

    #收款码列表
    public function qrcode(){
        $this->display();
    }

}