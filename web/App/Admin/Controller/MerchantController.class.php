<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;

/**
 * 商户管理控制器
 */
class MerchantController extends AdminBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->db = M('MchSeller');
    }


	/**
	 * 更新通道费率配置信息
	 */
	public function setAlleyRate(){
		if(IS_POST){
			$post=I('post.');
			$db = M('MchSellerAlleys');
			$where = [
				'cid' => $post['id'],
				'alleys_type' => $post['type'],
				'domain_auth' => domain_auth()
			];
			$res=$db->where($where)->save(['rate' => $post['rate']]);
			if($res) {
				$this->success('费率配置信息更新成功');
			}else{
				$this->error('费率配置信息更新失败');
			}
		}
	}

	/**
	 * 重置商户密码
	 */
    public function restMchPassword(){
    	if(IS_POST){
    		$post=I('post.');
    		$len=strlen($post['pass']);
    		if($len<6){
    			$this->error('密码不能小于6位');
			}else {
				$pass = md5($post['pass']);
				$ret = M('mchSeller')->where(['id' => $post['id'], 'domain_auth' => domain_auth()])->save(['password' => $pass]);
				if ($ret) {
					$this->success('登录密码重置成功');
				} else {
					$this->error('登录密码重置失败');
				}
			}
		}
	}

	/**
	 * 登入商户端
	 */
    public function mp_login(){
		$Seller = M('MchSeller')->where(array('id' =>I('get.id'), 'domain_auth' => domain_auth()))->find();
		if($Seller) {
			$_SESSION['mp'] = array(
				'id' => $Seller['id'],
				'aid' => $Seller['agent_id'],
				'phone' => $Seller['mch_tel'],
				'mch_name' => $Seller['mch_name'],
				'mch_card_name' => $Seller['mch_card_name']
			);
			$this->success('登如商户端系统成功!系统即将进入控制台!', U('mp/index/index'));
		}else{
			$this->error('商户ID非法');
		}
	}

    /**
     * 流量管理 以后备用
     */
    public function flow(){
       $this->display();
    }

    /**
     * 终端状态变更
     */
    public function terminal_status(){
        $id = I('post.id');
        if (I('post.status') == 'true') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = M('MchTerminal')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }
    #终端列表
    public function terminal(){
        $map['mch_id'] = I('get.id');
        $map['domain_auth'] = domain_auth();
        $Data = M('MchTerminal');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign = array(
            'data' => $list,
            'page' => $show
        );
        $this->assign($assign);
        $this->display();
    }

    #终端编辑/新增
    public function terminal_add(){
        $Data = M('MchTerminal');
        if(IS_POST){
            $post=I('post.');
            $data['notify_url']=$post['notify_url'];
            $data['remark']=$post['remark'];
            if($post['type']=='edit'){ //编辑
                $res=$Data->where(array('id'=>$post['id']))->save($data);
                if($res){
                    $this->success('终端信息编辑成功');
                }else{
                    $this->error('终端信息编辑失败');
                }
            }else{ //新增
              $data['appid']=self::randApp('appid');
              $data['appkey']=self::randApp();
              $data['mch_id']=$post['id'];
              $data['create_time']=time();
              $data['domain_auth']=domain_auth();
              $res=$Data->add($data);
              if($res){
                  $this->success('终端信息新增成功');
              }else{
                  $this->error('终端信息新增失败');
              }
            }
        }else{
          $this->error('非法操作');
        }
    }

    #终端APPID或key生成
    public function randApp($type=null){
        $Data = M('MchTerminal');
        if($type=='appid'){
            $appid=RandStr(10);
            if($Data->where(array('appid'=>$appid))->count()){
                self::randApp('appid');
            }else{
                return $appid;
            }
        }else{
            $appkey=RandStr(22,1);
            if($Data->where(array('appkey'=>$appkey))->count()){
                self::randApp();
            }else{
                return $appkey;
            }
        }
    }


    #商户通道开通发送模板消息提醒
    public function SendAlleysTemplate(){
        $data=I('post.');
        $seller=$this->db->where(array('id'=>$data['id'],'domain_auth'=>domain_auth()))->getField('mch_wx_openid');
        if($seller){#存在OPENID查询通道是否发送过,防止多次发送
            if($data['is_type']=='card'){
                $alley = M('MchSellerCardAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type']))->getField('send_success');
            }else {
                $alley = M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type']))->getField('send_success');
            }
            if($alley!=1){
                #没有发送过消息
                $TemplateId=GetPayConfig(domain_auth(), 'alleys_template_id');
                $TemplateStatus=GetPayConfig(domain_auth(), 'alleys_template_status');
                if($TemplateStatus==1&&$TemplateId){
                    if($data['is_type']=='card'){
                        M('MchSellerCardAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type']))->save(array('send_success'=>1));
                    }else {
                        M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type']))->save(array('send_success'=>1));
                    }
                    sendMchTemplateMessage($data['id'], 'alleys',$data['type']);
                    $this->success('已成功给商户发送通道开通提醒消息!');
                }else{
                    $this->error('系统未开启通道开通提醒模板消息或未配置通道开通提醒模板ID,请在系统配置-支付配置中查看是否配置!');
                }
            }else{
                $this->error('当前商户已发送过当前通道提醒!您无法再次发送!');
            }

        }else{
            $this->error('未找到商户的微信OPENID信息,此次模板消息发送失败!');
        }

    }

    #给未认证的商户发送认证提醒消息
    public function SendAuthTemplate(){
        $id=I('post.id');
        $seller=$this->db->where(array('id'=>$id,'domain_auth'=>domain_auth()))->find();
        if($seller['auth_status']==1){
            $this->error('此商户已认证成功,无需发送模板消息提醒');
        }elseif (!$seller['mch_wx_openid']){
            $this->error('未找到商户('.$seller["mch_name"].')的微信OPENID信息,请人工联系客户认证!此次模板消息发送失败!');
        }else{
            $TemplateId=GetPayConfig(domain_auth(), 'auth_template_id');
            $TemplateStatus=GetPayConfig(domain_auth(), 'auth_template_status');
            if($TemplateStatus==1&&$TemplateId){
                sendMchTemplateMessage($seller['id'], 'auth');
                $this->success('已成功给商户('.$seller["mch_name"].')发送未认证提醒消息!');
            }else{
                $this->error('系统未开启认证模板消息提醒或未配置认证模板ID,请在系统配置-支付配置中查看是否配置!');
            }
        }
    }


    #商户认证列表
    public function Auth_Fee(){
        $data=I('param.');
        if($data['codes']){
            $map['codes']=array('like','%'.$data['codes'].'%');
        }
        if($data['out_trade_no']){
            $map['out_trade_no|out_transaction_id']=array('like','%'.$data['out_trade_no'].'%');
        }
        if($data['aid']){
            $map['agent']=array('like','%'.$data['aid'].'%');
        }
        $db = M('MchUserAuth');
        $map['domain_auth'] = domain_auth();
        $map['status'] = 1;
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();


        $result=$db->order('id desc')->where($map)->select();
        if(!empty($data['export'])&&$data['export']=='ccl'){
            $xlsName  = "UserAuth_";//导出名称
            $xlsCell  = array(
                array('codes','收款码ID'),
                array('agent_name','所属代理'),
                array('total','支付金额'),
                array('time_end','支付完成时间'),
                array('out_trade_no','平台订单号'),
                array('out_transaction_id','官方订单号'),
                array('name','用户名称'),
                array('status','支付结果'),
            );
            $atitle="商户认证生成时间:".date('Y-m-d H:i:s');
            $wbscms=array(
                'Atitle'=>$atitle,
            );
            foreach ($result as $k => $v){
                switch ($v['status']){
                    case 1:
                        $status='已清算';
                        break;
                    default:
                        $status='未清算';
                        break;
                }
                $xlsData[$k]['codes']=$v['codes'];
                $xlsData[$k]['agent_name']=$v['agent_name'];
                $xlsData[$k]['total']=$v['total'];
                $xlsData[$k]['time_end']=date('Y-m-d H:i:s',$v['time_end']);
                $xlsData[$k]['out_trade_no']=$v['out_trade_no'];
                $xlsData[$k]['out_transaction_id']=$v['out_transaction_id'];
                $xlsData[$k]['name']=$v['name'];
                $xlsData[$k]['status']=pays_status($v['status']);
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }


        $assign = array(
            'data' => $list,
            'page' => $show
        );
        $this->assign($assign);
        $this->display();
    }

    #收款码回收
    public function codes_reload(){
        if(IS_POST) {
            $data = I('post.');
            $db = M('MchSeller');
            #先判断此商户的状态 是不是启用
            $map['domain_auth'] = domain_auth();
            $map['id'] = $data['id'];
            $map['codes'] = $data['codes'];
            $res = $db->where($map)->find();
            if ($res) {
                if ($res['status'] == 1) {
                    $this->error('此商户已审核通过!无法回收信息!');
                } else {
                    #不是使用状态 进行删除操作
                    $db->where($map)->delete();
                    M('MchStore')->where(array('sid' => $res['id'], 'domain_auth' => domain_auth()))->delete();
                    #收款码回收
                    $save = array(
                        'mch_id' => Null,
                        'store_id' => Null,
                    );
                    $codes = M('MchCodes')->where(array('codes' => $data['codes'], 'domain_auth' => domain_auth()))->save($save);
                    if ($codes) {
                        $this->success('此收款码已回收成功!可再次进行注册使用!');
                    } else {
                        $this->error('收款码回收失败!');
                    }
                }
            } else {
                $this->error('获取商户信息失败!此次操作无效!');
            }
        }

    }


    #录入商户
    public function abook_save()
    {
        if (IS_POST) {
            $db = M('MchSeller');
            $data = I('post.');
            if (!$data['codes']) {
                $this->error('收款码ID不能为空!');
            }
            if (!$data['agent_id']) {
                $this->error('代理信息不能为空!');
            }

            #判断手机号唯一性
            $we['domain_auth']=domain_auth();
            $we['mch_tel']=$data['mch_tel'];
            $tel=$db->where($we)->count();
            if($tel){
                $this->error('此商户手机号已存在!');
            }

            $set = array(
                'codes' => $data['codes'],
                'agent_id' => $data['agent_id'],
                'mch_name' => $data['mch_name'],
                'mch_tel' => $data['mch_tel'],
                'mch_industry' => $data['mch_industry'],
                'mch_provice' => $data['mch_provice'],
                'mch_citys' => $data['mch_citys'],
                'mch_district' => $data['mch_district'],
                'mch_address' => $data['mch_address'],
                'mch_bus_type' => $data['mch_bus_type'],
                'mch_bank_cid' => $data['mch_bank_cid'],
                'mch_bank_type' => $data['mch_bank_type'],
                'mch_bank_provice' => $data['mch_bank_provice'],
                'mch_bank_citys' => $data['mch_bank_citys'],
                'mch_linkbnk' => $data['mch_linkbnk'],
                'mch_bank_list' => $data['mch_bank_list'],
                'mch_bank_name' => $data['mch_bank_name'],
                'mch_bank_tel' => $data['mch_bank_tel'],
                'mch_card_name' => $data['mch_card_name'],
                'mch_card_id' => $data['mch_card_id'],
                'mch_img_z' => $data['mch_img_z'],
                'mch_img_p' => $data['mch_img_p'],
                'mch_img_s' => $data['mch_img_s'],
                'mch_img_sqh' => $data['mch_img_sqh'],
                'mch_img_yyzz' => $data['mch_img_yyzz'],
                'mch_img_bank' => $data['mch_img_bank'],
                'mch_img_m1' => $data['img-m1'],
                'mch_img_m2' => $data['img-m2'],
                'mch_img_m3' => $data['img-m3'],
                'mch_img_m4' => $data['img-m4'],
                'mch_type'=>$data['mch_type'],
                'qy_fr_name'=>$data['qy_fr_name'],
                'qy_fr_cid'=>$data['qy_fr_cid'],
                'mch_img_auth_z'=>$data['img-auth-z'],
                'mch_img_auth_p'=>$data['img-auth-p'],
                'qy_name' => $data['qy_name'],
                'qy_cid' => $data['qy_cid'],
                'domain_auth' => domain_auth(),
                'ctime' => time(),
                'loadtime' => time(),
                'status' => 0,
                'type' => 'default'
            );

            #判断唯一规则
            $code = M('MchCodes')->where(array('codes' => $data['codes']))->find();
            if ($code['store_id']) {
                $this->error('此收款码已经绑定门店信息!');
            }

            #保存信息到数据库
            $seller = $db->add($set);
            #自动创建一个门店
            $store = array(
                'sid' => $seller,
                'name' => $data['mch_name'],
                'per_name' => $data['mch_card_name'],
                'per_phone' => $data['mch_tel'],
                'uptime' => time(),
                'pay_type' => 'a:6:{s:10:"data_wxpay";s:1:"1";s:11:"data_alipay";s:1:"1";s:14:"data_aliconfig";s:1:"1";s:11:"data_aliurl";s:0:"";s:13:"data_wxconfig";s:1:"1";s:10:"data_wxurl";s:0:"";}',
                'domain_auth' => domain_auth(),
                'status' => 1,
            );
            $store_id = M('MchStore')->add($store);

            #保存收款码门店信息
            $_codes = array(
                'mch_id' => $seller,
                'store_id' => $store_id,
            );
            $map['domain_auth']= domain_auth();
            $map['codes']= $data['codes'];
            $rel = M('MchCodes')->where($map)->save($_codes);

            if ($rel) {
                $this->success('商户录入成功,请在审核界面审核', U('Auditing'));
            } else {
                $this->error('商户录入失败');
            }

        }
    }


    #商户录件
    public function abook()
    {
        $Codes = I('get.tid');
        $map['codes']=$Codes;
        $map['domain_auth']=domain_auth();
        $_code = M('MchCodes')->where($map)->find();
        if (!$_code) {
            $this->error('获取收款码信息失败!');
        }

        if($_code['store_id']){
            $this->error('此收款码已关联门店信息!无法进行录入!');
        }

        if(!$_code['aid']){
            $this->error('此收款码未配置代理,获取代理信息失败!');
        }

        #行业类别
        $Ind = M('MchIndustry')->order('name asc')->select();
        #省份
        $pro = M('CityData')->distinct(true)->field('provice')->select();
        #银行列表
        $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();

        $assign = array(
            'ind' => $Ind,
            'pro' => $pro,
            'bank_list' => $bank_list,
            'code' => $_code
        );
        $this->assign($assign);
        $this->display();
    }

    #无卡商户进行
    public function card_alleys_in(){
        R('Pays/CApi/mch_in');
    }
    #商户进件
    public function alleys_in(){
        $seller=M('MchSeller')->where(array('id'=>I('post.cid'),'domain_auth'=>domain_auth()))->find();
        if($seller['ctime']>1510761600){ #大于20171116000000 这个时间的判断认证
            $zm_auth=DomainAuthData('zm_auth');
            if($zm_auth) {
                if($seller['auth_status']!=1){
                    $this->error('该商户未完成实名认证，无法进件本通道!请通知商户实名认证后进件，或为商户配置银联快捷通道');
                }else{
                    R('Pays/Apis/mch_in');
                }
            }else{
                R('Pays/Apis/mch_in');
            }
        }else{
            R('Pays/Apis/mch_in');
        }
    }

    #商户更新
    public function alleys_updata(){
        R('Pays/Apis/mch_updata');
    }

    #门店店员删除
    public function store_user_del()
    {
        $data = I('post.');
        $map['id'] = $data['id'];
        $map['sid'] = $data['sid'];
        $map['domain_auth'] = domain_auth();
        $res = M('MchStoreUser')->where($map)->delete();
        if ($res) {
            $this->success('店员信息删除成功');
        } else {
            $this->error('店员信息删除失败');
        }
    }

    #门店店员编辑数据保存
    public function store_user_save()
    {
        $data = I('post.');
        if (isset($data['role_wx_temp'])) {
            $role_wx_temp = 1;
        } else {
            $role_wx_temp = 0;
        }
        if (isset($data['role_order'])) {
            $role_order = 1;
        } else {
            $role_order = 0;
        }
        $arr = array(
            'username' => $data['username'],
            'phone' => $data['phone'],
            'status' => $data['status'],
            'role_wx_temp' => $role_wx_temp,
            'role_order' => $role_order,
        );
        if(!empty($data['password'])){
        	$arr['password']=md5($data['password']);
		}
        $map['id'] = $data['id'];
        $map['sid'] = $data['sid'];
        $map['store_id'] = $data['store_id'];
        $map['domain_auth'] = domain_auth();
        $res = M('MchStoreUser')->where($map)->save($arr);
        if ($res) {
            $this->success('店员信息更新成功');
        } else {
            $this->error('店员信息更新失败');
        }
    }

    #门店店员数据
    public function store_user_data()
    {
        $data = I('post.');
        $map['domain_auth'] = domain_auth();
        $map['sid'] = $data['sid'];
        $map['id'] = $data['id'];
        $res = M('MchStoreUser')->where($map)->find();
        unset($res['domain_auth']);
        if ($res) {
            $this->success($res);
        } else {
            $this->error('参数错误');
        }

    }

    #门店店员管理
    public function store_user()
    {
        $data = I('get.');
        $db = M('MchStoreUser');

        $map['domain_auth'] = domain_auth();
        $map['sid'] = I('get.id');
        $map['store_id'] = I('get.store_id');
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        #添加店员使用
        $bind_url = 'http://' . $_SERVER['HTTP_HOST'] . U('Pays/Mch/store_user_bind', array('id' => $data['id'], 'store_id' => $data['store_id']));
        $QrUrl = U('Pays/Mch/QrData', array('url' => Xencode($bind_url)));
        $assign = array(
            'QrUrl' => $QrUrl,
            'data' => $list,
            'page' => $show
        );
        $this->assign($assign);
        //dump($QrUrl);
        $this->display();
    }

    #门店打印机配置
    public function store_config()
    {
        $data = I('get.');
        #防止跨渠道信息
        $map['id'] = $data['store_id'];
        $map['sid'] = $data['id'];
        $map['domain_auth'] = domain_auth();
        $Store = M('MchStore')->where($map)->count();
        if (!$Store) {
            $this->error('非法操作!未找到对应门店信息!');
        }
        $where['sid'] = $data['id'];
        $where['store_id'] = $data['store_id'];
        $where['domain_auth'] = domain_auth();
        $rel = M('MchStorePrint')->where($where)->find();
        $print = unserialize($rel['data']);
        unset($rel['data']);
        unset($rel['domain_auth']);
        $config = array_merge($print, $rel);

        $assign = array(
            'data' => $config
        );
        $this->assign($assign);
        //dump($config);
        $this->display();
    }

    #门店打印机保存
    public function print_save()
    {
        $data = I('post.');
        if (!$data['sid'] || !$data['store_id']) {
            $this->error('操作非法!');
        }
        $data_arr = array(
            'print_id' => $data['print_id'],
            'print_api' => $data['print_api'],
            'print_key' => $data['print_key'],
            'print_zd' => $data['print_zd'],
            'print_top' => $data['print_top'],
            'print_mchname' => $data['print_mchname'],
            'print_footer' => $data['print_footer'],
            'print_num' => $data['print_num'],
        );

        $arr = array(
            'sid' => $data['sid'],
            'store_id' => $data['store_id'],
            'data' => serialize($data_arr),
            'status' => $data['print_status'],
            'domain_auth' => domain_auth(),
        );
        #判断信息是否有
        $where['sid'] = $data['sid'];
        $where['store_id'] = $data['store_id'];
        $where['domain_auth'] = domain_auth();
        $rel = M('MchStorePrint')->where($where)->count();
        if ($rel) {
            $res = M('MchStorePrint')->where($where)->save($arr);
        } else {
            $res = M('MchStorePrint')->add($arr);
        }
        if ($res) {
            $this->success('配置信息更新成功');
        } else {
            $this->error('配置信息更新失败');
        }

    }

    #鉴权日志
    public function card_validate_log(){
        $db = M('CardValidateLog');
        $map['domain_auth'] = domain_auth();
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        #总鉴权次数
        if((DomainAuthData('auth_card')-auth_card_count(domain_auth())) <= 0){
            $this->error('鉴权次数已用完!无法进件!请联系平台充值!');
        }
        $va_1=DomainAuthData('auth_card');
        #已用鉴权次数
        $va_2=auth_card_count(domain_auth());
        #剩余鉴权次数
        $va_3=(DomainAuthData('auth_card')-auth_card_count(domain_auth()));
        $assign = array(
            'data' => $list,
            'page' => $show,
            'va1'=>$va_1,
            'va2'=>$va_2,
            'va3'=>$va_3
        );
        $this->assign($assign);
        $this->display();
    }


    #变更日志
    public function alter_log(){
        $db = M('MchAlterLog');
        $map['cid']=I('post.mid')?I('post.mid'):array('EXP','IS NOT NULL');
        $map['domain_auth'] = domain_auth();
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
        );
        $this->assign($assign);
        $this->display();
    }

    #变更信息
    public function alter_log_data(){
        $data=I('get.');
        $db = M('MchAlterLog');
        $map['domain_auth'] = domain_auth();
        $map['id']=$data['id'];
        $map['type']=$data['type'];
        $res = $db->where($map)->find();
        $old_data=unserialize($res['old_data']);
        $new_data=unserialize($res['new_data']);
        if(!$res){
            $this->error('无此变更记录!');
        }
        $assign=array(
            'old_data'=>$old_data,
            'new_data'=>$new_data,
            'res'=>$res,
        );
        $this->assign($assign);
        $this->display();
    }

    #商户信息变更
    public function card_alter()
    {
        if(IS_POST){
            $data=I('post.');
            #判断变更类型
            if($data['alter_type']=='alter_rate'){
                #变更费率 不限制时间
                if(!$data['rates']){
                    $this->error('为获取到要变更的费率');
                }
                #费率接口 如果两种原费率和新费率都一样 直接返回提示
                if($data['old_rates']==$data['rates']){
                    $this->error('原费率与新费率一致!无法完成变更!');
                }
                R('Pays/CApi/mch_alter');
            }else{
                #变更信息
                if(!$data['mch_bank_list']){$this->error('请选择开户行');}
                if(!$data['mch_bank_cid']){$this->error('请输入结算卡号');}
                if(!$data['mch_bank_name']){$this->error('获取账户姓名失败!联系技术处理!');}
                if(!$data['mch_bank_provice']){$this->error('请输入开户省份');}
                if(!$data['mch_bank_citys']){$this->error('请输入开户城市');}
                if(!$data['mch_linkbnk']){$this->error('请选择开户行');}
                if(!$data['mch_bank_tel']){$this->error('请输入预留手机号');}
                R('Pays/CApi/mch_alter');
            }
        }else {
            #根据当前通道 调用信息
            $data = I('get.');
            $map['domain_auth'] = domain_auth();
            $map['cid'] = $data['id'];
            $alleys = M('MchSellerCardAlleys')->where($map)->where(array('alleys_type' => $data['type']))->find();
            #省份
            $pro = M('CityData')->distinct(true)->field('provice')->select();
            #银行列表
            $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
            $m['id'] = $data['id'];
            $m['domain_auth'] = domain_auth();
            $_m = $this->db->where($m)->getField('mch_name');
            if (!$_m) {
                $this->error('商户信息不存在');
            }
            $assign = array(
                'data' => $alleys,
                'pro' => $pro,
                'bank_list' => $bank_list,
                'mch_name' => $_m,
            );
            $this->assign($assign);

            $this->display();
        }
    }


    #商户信息变更
    public function alter()
    {
        if(IS_POST){
            $data=I('post.');
            #判断变更类型
            if($data['alter_type']=='alter_rate'){
                #变更费率 不限制时间
                if(!$data['rates']){
                    $this->error('为获取到要变更的费率');
                }
                #费率接口 如果两种原费率和新费率都一样 直接返回提示
                if($data['old_rates']==$data['rates']){
                    $this->error('原费率与新费率一致!无法完成变更!');
                }
                R('Pays/Apis/mch_alter');
            }else{
                #变更结算信息  周一至周五 14:00-23:59
                /*$check_time=Check_time_w();
                if($check_time){*/
                #变更信息
                if(!$data['mch_bank_list']){$this->error('请选择开户行');}
                if(!$data['mch_bank_cid']){$this->error('请输入结算卡号');}
                if(!$data['mch_bank_name']){$this->error('获取账户姓名失败!联系技术处理!');}
                if(!$data['mch_bank_provice']){$this->error('请输入开户省份');}
                if(!$data['mch_bank_citys']){$this->error('请输入开户城市');}
                if(!$data['mch_linkbnk']){$this->error('请选择开户行');}
                if(!$data['mch_bank_tel']){$this->error('请输入预留手机号');}
                R('Pays/Apis/mch_alter');
                /*}else{
                    $this->error('变更时间周一至周五 14:00-23:59 非时间内无法变更!');
                }*/
            }
        }else {
            #根据当前通道 调用信息
            $data = I('get.');
            $map['domain_auth'] = domain_auth();
            $map['cid'] = $data['id'];
            $alleys = M('MchSellerAlleys')->where($map)->where(array('alleys_type' => $data['type']))->find();
            #微通道 CT1费率
            //$wlb_rate = M('MchSellerAlleys')->where($map)->where(array('alleys_type' => 'Wlbpay'))->getField('rate');
            /*if (strstr($data['type'], 'Wlbpay')) {
                $all = 'Wlbpays';
            } else {
                $all = $data['type'];
            }*/
            #省份
            $pro = M('CityData')->distinct(true)->field('provice')->select();
            #银行列表
            $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();

            $m['id'] = $data['id'];
            $m['domain_auth'] = domain_auth();
            $_m = $this->db->where($m)->getField('mch_name');
            if (!$_m) {
                $this->error('商户信息不存在');
            }
            $assign = array(
                //'all' => $all,
                /*'rate' => array(
                    'wlb' => $wlb_rate,
                ),*/
                'data' => $alleys,
                'pro' => $pro,
                'bank_list' => $bank_list,
                'mch_name' => $_m,
            );
            $this->assign($assign);

            $this->display();
        }
    }




    public function alter_bak()
    {
        if(IS_POST){
            $data=I('post.');
            #判断变更类型
            if($data['alter_type']=='alter_rate'){
                #变更费率 不限制时间
                if(!$data['rate']){
                    $this->error('为获取到要变更的CT1费率');
                }
                if(!$data['rates']){
                    $this->error('为获取到要变更的CD0费率');
                }
                #费率接口 如果两种原费率和新费率都一样 直接返回提示
                if($data['old_rate']==$data['rate']&&$data['old_rates']==$data['rates']){
                    $this->error('原费率与新费率一致!无法完成变更!');
                }
                R('Pays/Apis/mch_alter');
            }else{
                #变更结算信息  周一至周五 14:00-23:59
                $check_time=Check_time_w();
                if($check_time){
                    #变更信息
                    if(!$data['mch_bank_list']){$this->error('请选择开户行');}
                    if(!$data['mch_bank_cid']){$this->error('请输入结算卡号');}
                    if(!$data['mch_bank_name']){$this->error('获取账户姓名失败!联系技术处理!');}
                    if(!$data['mch_bank_provice']){$this->error('请输入开户省份');}
                    if(!$data['mch_bank_citys']){$this->error('请输入开户城市');}
                    if(!$data['mch_linkbnk']){$this->error('请选择开户行');}
                    if(!$data['mch_bank_tel']){$this->error('请输入预留手机号');}
                    R('Pays/Apis/mch_alter');
                }else{
                    $this->error('变更时间周一至周五 14:00-23:59 非时间内无法变更!');
                }
            }
        }else {
            #根据当前通道 调用信息
            $data = I('get.');
            $map['domain_auth'] = domain_auth();
            $map['cid'] = $data['id'];
            $alleys = M('MchSellerAlleys')->where($map)->where(array('alleys_type' => $data['type']))->find();
            #微通道 CT1费率
            $wlb_rate = M('MchSellerAlleys')->where($map)->where(array('alleys_type' => 'Wlbpay'))->getField('rate');
            if (strstr($data['type'], 'Wlbpay')) {
                $all = 'Wlbpays';
            } else {
                $all = $data['type'];
            }
            #省份
            $pro = M('CityData')->distinct(true)->field('provice')->select();
            #银行列表
            $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();

            $m['id'] = $data['id'];
            $m['domain_auth'] = domain_auth();
            $_m = $this->db->where($m)->getField('mch_name');
            if (!$_m) {
                $this->error('商户信息不存在');
            }
            $assign = array(
                'all' => $all,
                'rate' => array(
                    'wlb' => $wlb_rate,
                ),
                'data' => $alleys,
                'pro' => $pro,
                'bank_list' => $bank_list,
                'mch_name' => $_m,
            );
            $this->assign($assign);
            $this->display();
        }
    }




    #获取门店信息 用户编辑
    public function store_data()
    {
        $data = I('post.');
        $map['domain_auth'] = domain_auth();
        $map['sid'] = $data['sid'];
        $map['id'] = $data['id'];
        $res = M('MchStore')->where($map)->find();
        if ($res) {
            $pay_type = unserialize($res['pay_type']);
            unset($res['pay_type']);
            $set = array_merge($pay_type, $res);
            $this->success($set);
        } else {
            $this->error('参数错误');
        }
    }

    #门店绑定收款码ID
    public function bind_codes()
    {
        $data = I('post.');
        $db = M('MchCodes');
        #判断收款码ID是否使用
        $map['domain_auth'] = domain_auth();
        $map['codes'] = $data['code'];
        $res = $db->where($map)->find();
        if (!$res) {
            $this->error('收款码ID错误!找不到此收款码ID的信息');
        } else {
            #判断此收款码是否绑定门店 是否使用
            if ($res['store_id']) {
                $this->error('此收款码已被其他门店使用!请更换收款码ID');
            }
            #只能绑定当前代理下的收款码ID
            $aid = M('MchSeller')->where(array('id' => $data['sid']))->getField('agent_id');
            if ($aid != $res['aid']) {
                $this->error('此收款码ID不属于当前商户代理的收款码!请绑定当前商户所属代理下的收款码ID');
            }

            #信息验证正常
            $arr = array(
                'mch_id' => $data['sid'],
                'store_id' => $data['id'],
            );

            $where['domain_auth'] = domain_auth();
            $where['codes'] = $data['code'];
            $rel = $db->where($where)->save($arr);
            if ($rel) {
                $this->success('绑定收款码ID成功');
            } else {
                $this->error('绑定收款码ID失败');
            }
        }
    }


    #门店列表
    public function store()
    {
        $db = M('MchStore');
        $map['domain_auth'] = domain_auth();
        $map['sid'] = I('get.id');

        $seller = M('MchSeller')->where(array('id' => I('get.id'), 'domain_auth' => domain_auth()))->count();
        if (!$seller) {
            $this->error('获取上级商户信息失败!');
        }

        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $assign = array(
            'data' => $list,
            'page' => $show
        );
        $this->assign($assign);

        $this->display();
    }

    #门店信息保存
    public function store_set()
    {
        $data = I('post.');
        $db = M('MchStore');
        #判断门店名称是否重复
        $SName = $db->where(array('name' => $data['name'], array('status' => 1)))->count();
        #取当前ID的名称
        $DName = $db->where(array('id' => $data['id']))->getField('name');
        if ($SName && $DName != $data['name']) {
            $this->error('门店名称已存在!请更换门店名称');
        }
        #数据信息
        $_data = array(
            'data_wxpay' => $data['data_wxpay'],
            'data_alipay' => $data['data_alipay'],
            'data_aliconfig' => $data['data_aliconfig'],
            'data_aliurl' => $data['data_aliurl'],
            'data_wxconfig' => $data['data_wxconfig'],
            'data_wxurl' => $data['data_wxurl'],
        );

        $arr = array(
            'name' => $data['name'],
            'per_name' => $data['per_name'],
            'per_phone' => $data['per_phone'],
            'uptime' => time(),
            'status' => $data['status'],
            'sid' => $data['sid'],
            'domain_auth' => domain_auth(),
            'pay_type' => serialize($_data),
        );

        if ($data['id']) {
            $where['id'] = $data['id'];
            $where['domain_auth'] = domain_auth();
            $res = $db->where($where)->save($arr);
        } else {
            $res = $db->add($arr);
        }

        if ($res) {
            $this->success('门店信息配置成功');
        } else {
            $this->error('门店信息配置失败');
        }


    }

    #获取API数据
    public function mch_alleys_getapi()
    {
        $data = I('post.');
        if (!$data['cid']) {
            $this->error('参数有误');
        }
        $map['domain_auth'] = domain_auth();
        $map['cid'] = $data['cid'];
        $map['alleys_type'] = $data['type'];
        $res = M('MchSellerAlleys')->where($map)->field('mch_appid,mch_id,mch_key,id')->find();
        if ($res) {
            $arr = array(
                'msg' => '通道配置参数获取成功',
                'mch_id' => $res['mch_id'],
                'mch_key' => $res['mch_key'],
                'mch_appid' => $res['mch_appid'],
                'id' => $res['id']
            );
            $this->success($arr);
        } else {
            $this->error('未获取到配置信息');
        }


    }

    #手工配置商户通道参数
    public function alley_mch_data()
    {
        $data = I('post.');
        if (!$data['id']) {
            $this->error('参数有误');
        }
        $map['domain_auth'] = domain_auth();
        $map['id'] = $data['id'];
        $res = M('MchSellerAlleys')->where($map)->save(array('mch_appid' => TriMall($data['mch_appid']),'mch_id' => TriMall($data['mch_id']), 'mch_key' => TriMall($data['mch_key']), 'status' => 1, 'load_status' => 1));
        if ($res) {
            $this->success('通道参数配置成功');
        } else {
            $this->error('通道参数配置失败');
        }
    }

    #无卡 手工配置商户通道参数
    public function card_alley_mch_data()
    {
        $data = I('post.');
        if (!$data['id']) {
            $this->error('参数有误');
        }
        $map['domain_auth'] = domain_auth();
        $map['id'] = $data['id'];
        $res = M('MchSellerCardAlleys')->where($map)->save(array('mch_id' => TriMall($data['mch_id']), 'mch_key' => TriMall($data['mch_key']), 'status' => 1, 'load_status' => 1));
        if ($res) {
            $this->success('通道参数配置成功');
        } else {
            $this->error('通道参数配置失败');
        }
    }


    #保存当前通道(切换)
    public function mch_alleys_saves()
    {
        $data = I('post.');
        if (!$data['cid']) {
            $this->error('参数有误');
        }
        if (!$data['type']) {
            $this->error('通道类型参数有误!');
        }
        if(!$data['alleys_type']){$this->error('通道TYPE参数有误!');}

        $map['domain_auth'] = domain_auth();
        $map['id'] = $data['cid'];

        if($data['alleys_type']=='wx'){
            $save=array('wx_alleys'=>$data['type']);
        }else{
            $save=array('ali_alleys'=>$data['type']);
        }

        $res = M('MchSeller')->where($map)->save($save);
        if ($res) {
            $this->success('通道切换成功');
        } else {
            $this->error('通道切换失败');
        }
    }


    #获取商户已开通的通道列表
    public function mch_alleys_data()
    {
        if (IS_POST) {
            $where['domain_auth'] = domain_auth();
            $where['cid'] = I('post.id');
            $where['mch_id'] = array('neq', '');
            $res = M('MchSellerAlleys')->where($where)->field('alleys_type')->getField('alleys_type', true);
            if ($res) {
                $arr = array(
                    'cid' => I('post.id'),
                    'type' => $res,
                );
                $this->success($arr);
            } else {
                $this->error('当前商户未开通任何通道,请进入通道配置里开通通道!');
            }
        }
    }

    #商户审核成功短信发送
    public function mch_sms(){
        #根据ID筛选 商户名称和联系手机号
        $map['id']=I('post.id');
        $map['domain_auth'] = domain_auth();
        $seller=M('MchSeller')->where($map)->field('mch_name,mch_tel')->find();
        if($seller) {
            $sms = ALI_SMS();
            $AliSms = new \Think\Alisms($sms);
            $sms_data = array(
                'mobile' => $seller['mch_tel'], #接收手机号
                'code' => $sms['sms_audit'],#验证码模板ID
                'sign' => $sms['sms_sign'], #模板签名 必需审核通过
                'param' => json_encode(array(
                    'mch_name' => $seller['mch_name'], #商户名称
                )),
            );
            $re = $AliSms->sms_send($sms_data);

            if (sms_api() == 1) { #用阿里云通信接口
                $re = new_ali_sms($sms_data);
                if ($re['Code'] == 'OK' && $re['Message'] == 'OK') {
                    //更新发送结果
                    M('MchSeller')->where($map)->save(array('sms_status' => 1));
                    $this->success('审核成功短信发送成功');
                } else {
                    $info = "错误代码:" . $re['Code'] . ".错误消息:" . $re['Message'];
                    $this->error($info);
                }
            }else {
                if ($re['err_code'] == 0 && $re['success'] == true) {
                    //更新发送结果
                    M('MchSeller')->where($map)->save(array('sms_status' => 1));
                    $this->success('审核成功短信发送成功');
                } else {
                    $info = "错误代码:" . $re['code'] . ".错误消息:" . $re['msg'] . $re['sub_msg'];
                    $this->error($info);
                }
            }
        }else{
            $this->error('获取商户信息失败!');
        }
    }


    #商户驳回反馈列表
    public function mch_loading(){
        $data=I('post.');
        $where['mch_id']=$data['mch_id'];
        $where['alleys_type']=$data['mch_type'];
        $res=M('MchSellerAlleys')->where($where)->getField('loading');
        if($res){
            $this->error('驳回提示:'.$res.',请点击商户信息更新信息后提交');
        }else{
            $this->error('未找到当前驳回结果');
        }

    }

    #商户状态转审核
    public function mch_status(){
        $map['id']=I('post.id');
        $map['domain_auth'] = domain_auth();
        $seller=M('MchSeller')->where($map)->find();
        if($seller) {
            $res=M('MchSeller')->where($map)->save(array('status'=>0));
            if($res){
                $this->success('商户状态变更成功!');
            } else{
                $this->error('商户状态变更失败!');
            }
        }else{
            $this->error('获取商户信息失败!');
        }
    }

    #过户代理
    public function transfer_agent(){
        $data=I('post.');
        //dump($data);
        $where['user_name']=$data['agent'];
        $where['domain_auth']=domain_auth();
        $aid=M('MchAgent')->where($where)->getField('id');
        if(!$aid){
            $this->error('您输入的信息有误!未在相关代理信息!');
        }else{
            M('MchSeller')->where(array('id'=>$data['id'],'domain_auth'=>domain_auth()))->save(array('agent_id'=>$aid));
            M('MchSellerAlleys')->where(array('cid'=>$data['id'],'domain_auth'=>domain_auth()))->save(array('agent_id'=>$aid));
            $res=M('MchCodes')->where(array('mch_id'=>$data['id'],'domain_auth'=>domain_auth()))->save(array('aid'=>$aid));
            if($res){
                $this->success('当前商户的代理信息过户成功!');
            }else{
                $this->error('代理过户失败!');
            }
        }
    }


    #商户审核通过列表
    public function index()
    {
        $data=I('param.');

        if($data['aid']){
            $map['agent_id']=$data['aid'];
        }
        if($data['search_val']){
            $map['mch_name|mch_tel|mch_card_name']=array('like','%'.$data['search_val'].'%');
        }

        if($data['bus_type']){
            $map['mch_bus_type']=$data['bus_type'];
        }
        if($data['auth_status']){
            if($data['auth_status']==1) {
                $map['auth_status'] = 1;
            }else{
                $map['auth_status'] = array('EXP','IS NULL');
            }
        }
        if($data['wx_alleys']==1){
            $map['wx_alleys']=array('EXP','IS NOT NULL');
        }elseif($data['wx_alleys']==2){
            $map['wx_alleys']=array('EXP','IS NULL');
        }
        if($data['ali_alleys']==1){
            $map['ali_alleys']=array('EXP','IS NOT NULL');
        }elseif($data['ali_alleys']==2){
            $map['ali_alleys']=array('eq','');
        }

        $map['domain_auth'] = domain_auth();
        $map['status'] = 1;
        $count = $this->db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $this->db->order('id')->where($map)->order('loadtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        #当前品牌下的所有通道
        $where['cid'] = domain_id();
        $where['status'] = 1;
        //$where['is_card'] = array('neq',1);
        $api = M('DomainAlleys')->where($where)->field('alleys,alleys_type')->select();
        $alley=M('MchAlleys')->where(array('is_card'=>array('neq',1)))->getField('type',true);
        $api_data=array();
        foreach ($api as $value){
            if(in_array($value['alleys_type'],$alley)){
                $api_data[]=$value;
            }
        }



        $_result=$this->db->order('id desc')->where($map)->select();
        if(!empty($data['export'])&&$data['export']=='ccl'){
            $xlsName  = "Mch_";//导出名称
            $xlsCell  = array(
                array('agent_id','所属代理'),
                array('mch_name','商户名称'),
                array('mch_tel','联系电话'),
                array('mch_provice','所属省份'),
                array('mch_citys','所属市级'),
                array('mch_district','所属区县'),
                array('mch_address','详细地址'),
                array('mch_industry','行业类别'),
                array('mch_card_name','申请人姓名'),
                array('mch_card_id','申请人身份证号'),
                array('mch_bus_type','商户类型'),
                array('ctime','加入时间'),
                array('mch_bank_list','结算银行'),
                array('mch_linkbnk','结算支行'),
                array('mch_linkbnks','联行号'),
                array('mch_bank_cid','结算卡号'),
                array('mch_bank_name','结算开户人'),
                array('mch_bank_provice','开户省份'),
                array('mch_bank_citys','开户市级'),
            );
            $atitle="商户信息生成时间:".date('Y-m-d H:i:s')." 说明:此商户信息为首次审核成功后最终商户信息,如商户某个通道进件时修改资料,请进入平台查看!此项导出不支持导出某个通道资料!";
            $wbscms=array(
                'Atitle'=>$atitle,
            );
            foreach ($_result as $k => $v){
                switch ($v['status']){
                    case 1:
                        $status='已清算';
                        break;
                    default:
                        $status='未清算';
                        break;
                }
                $xlsData[$k]['agent_id']=agent_name($v['agent_id']);
                $xlsData[$k]['mch_name']=$v['mch_name'];
                $xlsData[$k]['mch_tel']=$v['mch_tel'];
                $xlsData[$k]['mch_provice']=$v['mch_provice'];
                $xlsData[$k]['mch_citys']=$v['mch_citys'];
                $xlsData[$k]['mch_district']=$v['mch_district'];
                $xlsData[$k]['mch_address']=$v['mch_address'];
                $xlsData[$k]['mch_industry']=Industrid($v['mch_industry']);
                $xlsData[$k]['mch_card_name']=$v['mch_card_name'];
                $xlsData[$k]['mch_card_id']="'".$v['mch_card_id'];
                $xlsData[$k]['mch_bus_type']=$v['mch_bus_type'];
                $xlsData[$k]['ctime']=date('Y-m-d H:i:s',$v['ctime']);
                $xlsData[$k]['mch_bank_list']=reload_bank($v['mch_bank_list']);
                $xlsData[$k]['mch_linkbnk']=reload_banks($v['mch_linkbnk']);
                $xlsData[$k]['mch_linkbnks']=$v['mch_linkbnk'];
                $xlsData[$k]['mch_bank_cid']="'".$v['mch_bank_cid'];
                $xlsData[$k]['mch_bank_name']=$v['mch_bank_name'];
                $xlsData[$k]['mch_bank_provice']=$v['mch_bank_provice'];
                $xlsData[$k]['mch_bank_citys']=$v['mch_bank_citys'];
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }


        $assign = array(
            'data' => $list,
            'page' => $show,
            'api' => $api_data
        );


        $this->assign($assign);

        //dump($api);

        $this->display();
    }

    #商户审核列表
    public function Auditing()
    {
        $map['domain_auth'] = domain_auth();
        switch (I('get.status')) {
            case 'ref':
                $map['status'] = 2;
                break;
            case  'all':
                $map['status'] = array('neq', 1);
                break;
            default:
                $map['status'] = 0;
                break;
        }
        if (IS_POST) {
            if (I('post.search_val')) {
                $map['codes|mch_name|mch_tel|mch_card_name'] = array('like', '%' . I('post.search_val') . '%');
            }
            if (I('post.aid')) {
                $map['agent_id'] = I('post.aid');
            }
            if (I('post.bus_type')) {
                $map['mch_bus_type'] = I('post.bus_type');
            }

        }
        $count = $this->db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $this->db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $assign = array(
            'data' => $list,
            'page' => $show
        );
        $this->assign($assign);

        $this->display();

    }


    #商户基础信息
    public function mch_detail(){
        $map['domain_auth'] = domain_auth();
        $map['id'] = I('get.id');
        $data = $this->db->where($map)->find();
        if (!$data) {
            $this->error('未找到当前商户信息');
        }
        $assign = array(
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }

    #商户审核信息
    public function Auditing_detail()
    {
        $map['domain_auth'] = domain_auth();
        $map['id'] = I('get.id');
        $data = $this->db->where($map)->find();
        if (!$data) {
            $this->error('未找到当前需要审核的商户');
        }
        if ($data['status'] == 1) {
            $this->error('此商户状态不需要审核');
        }
        $assign = array(
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }

    #商户信息编辑
    public function Auditing_edits()
    {
        $data = I('get.');
        $where['domain_auth'] = domain_auth();
        $where['id'] = $data['id'];
        $info = M('mch_seller')->where($where)->find();
        #行业类别
        $Ind = M('MchIndustry')->order('name asc')->select();
        #省份
        $pro = M('CityData')->distinct(true)->field('provice')->select();
        #银行列表
        $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
		$mcc=R('Pays/PStarpos/get_mcc');
		if($mcc['status']==1){
			$mccData=$mcc['data'];
		}else{
			$mccData=0;
		}
        $assign = array(
            'data' => $info,
            'ind' => $Ind,
            'pro' => $pro,
            'mccData'=>$mccData,
            'bank_list' => $bank_list,
        );
        $this->assign($assign);
        $this->display();
    }

    #商户信息审核状态
    public function Auditing_status()
    {
        if (IS_POST) {
            $data = I('post.');
            $where['domain_auth'] = domain_auth();
            $where['id'] = $data['id'];
            $res = M('MchSeller')->where($where)->save(array('status' => $data['status'], 'info' => $data['info'], 'loadtime' => time()));
            if ($res) {
                $this->success('商户审核状态更新成功', U('Merchant/index'));
            } else {
                $this->error('商户审核信息更新失败');
            }
        }
    }

    #审核-商户信息保存
    public function Auditing_save()
    {
        if (IS_POST) {
            $data = I('post.');
            #判断数据库是否有此费率
            $db = M('MchSeller');
            $data['domain_auth'] = domain_auth();
            $rel = $db->where(array('id' => $data['id'], 'domain_auth' => domain_auth()))->save($data);
            if ($rel) {
                $this->success('商户信息更新成功', U('Auditing_detail', array('id' => $data['id'])));
            } else {
                $this->error('商户信息更新失败');
            }
        }
    }

    #商户通道状态操作
    public function card_api_way_status(){
        $id = I('post.id');
        if (I('post.status') == 'true') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = M('MchSellerCardAlleys')->where(array('id' => $id,'domain_auth'=>domain_auth()))->save($data);
        if ($res) {
            $this->success('通道状态更新成功');
        } else {
            $this->error('通道状态更新失败');
        }
    }

    #商户无卡通道列表
    public function card_api_way(){
        $cid = I('get.id');
        if (!$cid) {
            $this->error('非法操作');
        }
        #总列表
        $_map['is_card'] = array('neq',0);
        $_map['status'] = 1;
        $ALLEYS=M('MchAlleys')->where($_map)->getField('type',true);
        $where['cid'] = domain_id();
        $where['status'] = 1;
        $api = M('DomainAlleys')->where($where)->select();
        foreach ($api as $k => $v) {
            if(in_array($v['alleys_type'],$ALLEYS)) {
                $alter_status = M('MchAlleys')->where(array('type' => $v['alleys_type']))->find();
                $map['cid'] = $cid;
                $map['alleys_type'] = $v['alleys_type'];
                $map['domain_auth'] = domain_auth();
                $data = M('MchSellerCardAlleys')->where($map)->find();
                $res['alleys_type'] = $v['alleys_type'];
                $res['alleys'] = $v['alleys'];
                $res['mch_id'] = $data['mch_id']; #商户号
                $res['send_success'] = $data['send_success']; #商户号
                $res['mch_key'] = $data['mch_key']; #商户号
                $res['a_type'] = $data['alleys_type'];
                $res['rate'] = $data['rate'];
                $res['status'] = $data['status'];
                $res['card_alley_id'] = $data['id'];
                $res['load_status'] = $data['load_status'];
                $res['make'] = $v['make'];
                $res['id'] = $v['id'];
                $res['cid'] = $cid;
                $res['alter_status'] = $alter_status['alter_status'];
                $res['rest_in'] = $alter_status['rest_in'];
                $_data[] = $res;
            }
        }


        $m['domain_auth'] = domain_auth();
        $m['id'] = $cid;
        $_m = $this->db->where($m)->getField('mch_name');
        if (!$_m) {
            $this->error('商户不存在');
        }
        $assign = array(
            'data' => $_data,
            'mch_name' => $_m,
        );
        $this->assign($assign);

        $this->display();
    }

    #商户公众号配置
    public function mch_appid_data(){
        if(IS_POST){
            $data=I('post.');
            $db=M('MchAppid');
            $map['domain_auth']=domain_auth();
            $map['mch_id']=$data['appid_mch_id'];
            $map['alleys']=$data['appid_alleys'];
            if($data['type']=='getData'){//获取当前配置
                $id=$db->where($map)->getField('pay_wxid');
                if($id) {
                    $this->success($id);
                }else{
                    $this->error('商户未配置独立');
                }

            }else{ //保存配置
                if($db->where($map)->count()) {
                    $res = $db->where($map)->save(['pay_wxid' => $data['pay_wxid']]);
                }else{
                    $map['pay_wxid']=$data['pay_wxid'];
                    $res = $db->add($map);
                }
                if($res) {
                    $this->success('配置成功');
                }else{
                    $this->error('配置失败');
                }
            }
        }
    }

    #商户通道列表
    public function api_way()
    {
        $cid = I('get.id');
        if (!$cid) {
            $this->error('非法操作');
        }
        #总列表
        $_map['is_card'] = array('neq',1);
        $_map['status'] = 1;
        $ALLEYS=M('MchAlleys')->where($_map)->getField('type',true);
        $where['cid'] = domain_id();
        $where['status'] = 1;
        $api = M('DomainAlleys')->where($where)->select();
        foreach ($api as $k => $v) {
            if(in_array($v['alleys_type'],$ALLEYS)) {
                $alter_status = M('MchAlleys')->where(array('type' => $v['alleys_type']))->find();
                $map['cid'] = $cid;
                $map['alleys_type'] = $v['alleys_type'];
                $map['domain_auth'] = domain_auth();
                $data = M('MchSellerAlleys')->where($map)->find();
                $res['alleys_type'] = $v['alleys_type'];
                $res['alleys'] = $v['alleys'];
                $res['mch_id'] = $data['mch_id']; #商户号
                $res['send_success'] = $data['send_success']; #商户号
                $res['mch_key'] = $data['mch_key']; #商户号
                $res['a_type'] = $data['alleys_type'];
                $res['rate'] = $data['rate'];
                $res['status'] = $data['status'];
                $res['load_status'] = $data['load_status'];
                $res['make'] = $v['make'];
                $res['id'] = $v['id'];
                $res['cid'] = $cid;
                $res['alter_status'] = $alter_status['alter_status'];
                $res['rest_in'] = $alter_status['rest_in'];
                $res['appid_status'] = $alter_status['appid_status'];
                $_data[] = $res;
            }
        }


        $m['domain_auth'] = domain_auth();
        $m['id'] = $cid;
        $_m = $this->db->where($m)->getField('mch_name');
        if (!$_m) {
            $this->error('商户不存在');
        }
        //微信公众号列表
        $weixin=M('MchWeixin')->where(array('domain_auth'=>domain_auth()))->field('id,name')->select();
        $assign = array(
            'data' => $_data,
            'weixin'=>$weixin,
            'mch_name' => $_m,
        );
        $this->assign($assign);

        $this->display();
    }


    #无卡商户信息列表
    public function card_mdata(){
        $data = I('get.');
        #获取代理
        $_where['domain_auth'] = domain_auth();
        $_where['id'] = $data['id'];
        $aid = M('MchSeller')->where($_where)->getField('agent_id');
        $_rel = M('MchAgent')->where(array('id' => $aid))->find();
        $rate = unserialize($_rel['rate']);
        #判断如果没配置代理终端费率 提示配置
        if (!$rate[$data['type'] . '_term']) {
            $this->error('所属代理姓名:  <span style="color: red">' . $_rel['user_name'] . '</span>未配置当前通道的终端费率,请先配置代理的终端费率后再操作!', '', 9999);
        }

        $alleys=M('MchSellerCardAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type'], 'domain_auth' => domain_auth()))->find();

        #费率
        if ($alleys['rate']) {
            $rates = $alleys['rate'];
        } else {
            //如果是系统配置的邀请代理ID 取用户等级费率
            if (extensionSetting('aid')==$aid){
                #取配置等级费率
                $gradeMch=extensionMch(['mid'=>$data['id']],'grade');
                //获取等级详细数据
                $grade=extensionGrade($gradeMch);
                $rateData=json_decode($grade['rate'],true);
                $rates = $rateData[$data['type'] . '_term']?$rateData[$data['type'] . '_term']:$rate[$data['type'] . '_term'];
            }else {
                #如果还未配置费率 则默认用代理配置的终端费率
                $rates = $rate[$data['type'] . '_term'];
            }
        }


        #如果不存在先新增
        $_Not = M('MchSellerCardAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type'], 'domain_auth' => domain_auth()))->count();
        if (!$_Not) {
            #先判断民生通道是否存在 如果存在 民生通道信息为主
            $MsAlleys= M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type'=>'MSpays','domain_auth' => domain_auth()))->find();
            if($MsAlleys){
                $_Seller=$MsAlleys;
            }else {
                $_Seller = M('MchSeller')->where(array('id' => $data['id'], 'domain_auth' => domain_auth()))->find();
            }
            $_cid = $_Seller['id'];
            unset($_Seller['id']);
            unset($_Seller['mch_wx_openid']);
            unset($_Seller['mch_wx_name']);
            unset($_Seller['mch_wx_img']);
            unset($_Seller['codes']);
            unset($_Seller['alleys']);
            unset($_Seller['alleys_type']);
            unset($_Seller['status']);
            unset($_Seller['mch_id']);
            unset($_Seller['api_rel']);
            unset($_Seller['rate']);
            unset($_Seller['load_status']);
            $allys = array(
                'cid' => $_cid,
                'alleys_type' => $data['type'],
                'rate' => $rates,
            );
            $add = array_merge($allys, $_Seller);
           // dump($add);
            M('MchSellerCardAlleys')->add($add);
        }
        $map['domain_auth'] = domain_auth();
        $map['alleys_type'] = $data['type'];
        $map['cid'] = $data['id'];
        #先判断是否有通道的数据,如果没则用主商户信息
        $_alleys = M('MchSellerCardAlleys')->where($map)->find();

        if ($_alleys) {
            $info = $_alleys;
        } else {
            $where['domain_auth'] = domain_auth();
            $where['id'] = $data['id'];
            $info = M('mch_seller')->where($where)->find();
        }

        $wheres['cid'] = domain_id();
        $wheres['status'] = 1;
        $wheres['alleys_type'] = $data['type'];
        $Aname = M('DomainAlleys')->where($wheres)->getField('alleys');
        if (!$Aname) {
            $this->error('通道错误!原因: A:未开通此通道 B:通道系统不存在 C:通道被禁言 D:通道配置有误 N:联系相关负责人');
        }
        $assign = array(
            'data' => $info,
            'alleys' => $Aname,
            'rate' => $rates,
        );
        $this->assign($assign);
        $this->display();
    }

    #商户信息列表
    public function mdata()
    {
        $data = I('get.');
        #获取代理
        $_where['domain_auth'] = domain_auth();
        $_where['id'] = $data['id'];
        $aid = M('mch_seller')->where($_where)->getField('agent_id');
        $_rel = M('MchAgent')->where(array('id' => $aid))->find();
        $rate = unserialize($_rel['rate']);
        #判断如果没配置代理终端费率 提示配置
        if (!$rate[$data['type'] . '_term']) {
            $this->error('所属代理姓名:  <span style="color: red">' . $_rel['user_name'] . '</span>未配置当前通道的终端费率,请先配置代理的终端费率后再操作!', '', 9999);
        }

        $alleys=M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type'], 'domain_auth' => domain_auth()))->find();

        #费率
        if ($alleys['rate']) {
            $rates = $alleys['rate'];
        } else {
            #如果还未配置费率 则默认用代理配置的终端费率
            $rates = $rate[$data['type'] . '_term'];
        }

        #如果不存在先新增
        $_Not = M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type'], 'domain_auth' => domain_auth()))->count();
        if (!$_Not) {
            #先判断民生通道是否存在 如果存在 民生通道信息为主
            $MsAlleys= M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type'=>'MSpays','domain_auth' => domain_auth()))->find();
            if($MsAlleys){
                $_Seller=$MsAlleys;
            }else {
                $_Seller = M('MchSeller')->where(array('id' => $data['id'], 'domain_auth' => domain_auth()))->find();
            }
            $_cid = $_Seller['id'];
            unset($_Seller['id']);
            unset($_Seller['mch_wx_openid']);
            unset($_Seller['mch_wx_name']);
            unset($_Seller['mch_wx_img']);
            unset($_Seller['codes']);
            unset($_Seller['alleys']);
            unset($_Seller['alleys_type']);
            unset($_Seller['status']);
            unset($_Seller['mch_id']);
            unset($_Seller['api_rel']);
            unset($_Seller['rate']);
            unset($_Seller['load_status']);
            #WLB二次话 因为是一次进件
            if (strstr($data['type'], 'Wlbpay')) {
                $wlb = array('Wlbpay', 'Wlbpays');
                foreach ($wlb as $v) {
                    if ($v == 'Wlbpay') {
                        $wlb_rate = $rate['Wlbpay_term'];
                    } else {
                        $wlb_rate = $rate['Wlbpays_term'];
                    }
                    $allys = array(
                        'cid' => $_cid,
                        'alleys_type' => $v,
                        'rate' => $wlb_rate,
                    );
                    $add = array_merge($allys, $_Seller);
                    $type=M('MchSellerAlleys')->where(array('cid'=>$_cid,'alleys_type'=> $v,'domain_auth'=>domain_auth()))->count();
                    if(!$type) {
                        M('MchSellerAlleys')->add($add);
                    }
                }

            } else {
                $allys = array(
                    'cid' => $_cid,
                    'alleys_type' => $data['type'],
                    'rate' => $rates,
                );
                $add = array_merge($allys, $_Seller);
                M('MchSellerAlleys')->add($add);
            }
        }


        $map['domain_auth'] = domain_auth();
        $map['alleys_type'] = $data['type'];
        $map['cid'] = $data['id'];
        #先判断是否有通道的数据,如果没则用主商户信息
        $_alleys = M('mch_seller_alleys')->where($map)->find();

        if ($_alleys) {
            $info = $_alleys;
        } else {
            $where['domain_auth'] = domain_auth();
            $where['id'] = $data['id'];
            $info = M('mch_seller')->where($where)->find();
        }

        $wheres['cid'] = domain_id();
        $wheres['status'] = 1;
        $wheres['alleys_type'] = $data['type'];
        $Aname = M('DomainAlleys')->where($wheres)->getField('alleys');
        if (!$Aname) {
            $this->error('通道错误!原因: A:未开通此通道 B:通道系统不存在 C:通道被禁言 D:通道配置有误 N:联系相关负责人');
        }


        $assign = array(
            'data' => $info,
            'alleys' => $Aname,
            'rate' => $rates,
        );

		if($data['type']=='Hyb'){
			#省份
			$pro = M('CityData')->distinct(true)->field('provice')->select();
			#银行列表
			$bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
			$assign['pro']=$pro;
			$assign['bank_list']=$bank_list;
			$this->assign($assign);
			$this->display('mdata_hyb');
		}else {
			$this->assign($assign);
			$this->display();
		}
    }

    #商户信息编辑
    public function mdata_edit()
    {
        $data = I('get.');
        $map['domain_auth'] = domain_auth();
        $map['alleys_type'] = $data['type'];
        $map['cid'] = $data['id'];
        #先判断是否有通道的数据,如果没则用主商户信息
        $allys = M('mch_seller_alleys')->where($map)->find();
        if ($allys) {
            $info = $allys;
        } else {
            $where['domain_auth'] = domain_auth();
            $where['id'] = $data['id'];
            $info = M('mch_seller')->where($where)->find();
        }

        $wheres['cid'] = domain_id();
        $wheres['status'] = 1;
        $wheres['alleys_type'] = $data['type'];
        $Aname = M('DomainAlleys')->where($wheres)->getField('alleys');
        if (!$Aname) {
            $this->error('通道错误!原因: A:未开通此通道 B:通道系统不存在 C:通道被禁言 D:通道配置有误 N:联系相关负责人');
        }

        #行业类别
        $isTable = M()->query('SHOW TABLES LIKE "'.C('DB_PREFIX').'mch_industry_'.strtolower(I('get.type')).'"');
        if( $isTable ){
            $IndDb= M('MchIndustry'.I('get.type'));
        }else{
            $IndDb=M('MchIndustry');
        }
        $Ind = $IndDb->order('name asc')->select();
        #省份
        $pro = M('CityData')->distinct(true)->field('provice')->select();
        #银行列表
        $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();

		$mcc=R('Pays/PStarpos/get_mcc');
		if($mcc['status']==1){
			$mccData=$mcc['data'];
		}else{
			$mccData=0;
		}

        $assign = array(
            'data' => $info,
            'alleys' => $Aname,
            'ind' => $Ind,
            'pro' => $pro,
            'mccData'=>$mccData,
            'bank_list' => $bank_list,
        );
        $this->assign($assign);
        $this->display();
    }


    #无卡 商户信息编辑
    public function card_mdata_edit()
    {
        $data = I('get.');
        $map['domain_auth'] = domain_auth();
        $map['alleys_type'] = $data['type'];
        $map['cid'] = $data['id'];
        #先判断是否有通道的数据,如果没则用主商户信息
        $allys = M('MchSellerCardAlleys')->where($map)->find();

        if ($allys) {
            $info = $allys;
        } else {
            $where['domain_auth'] = domain_auth();
            $where['id'] = $data['id'];
            $info = M('mch_seller')->where($where)->find();
        }

        $wheres['cid'] = domain_id();
        $wheres['status'] = 1;
        $wheres['alleys_type'] = $data['type'];
        $Aname = M('DomainAlleys')->where($wheres)->getField('alleys');
        if (!$Aname) {
            $this->error('通道错误!原因: A:未开通此通道 B:通道系统不存在 C:通道被禁言 D:通道配置有误 N:联系相关负责人');
        }


        #行业类别
        $Ind = M('MchIndustry')->order('name asc')->select();

        #省份
        $pro = M('CityData')->distinct(true)->field('provice')->select();

        #银行列表
        $bank_list = M('MchBankList')->where(array('status' => 1,'qcard'=>1))->order('id asc')->field('bnkcd,bnknm,ico')->select();

        $assign = array(
            'data' => $info,
            'alleys' => $Aname,
            'ind' => $Ind,
            'pro' => $pro,
            'bank_list' => $bank_list,
        );
        $this->assign($assign);
        $this->display();
    }


    #无卡 商户信息保存
    public function card_mdata_save(){
        if (IS_POST) {
            $data = I('post.');
            #判断数据库是否有此费率
            $db = M('MchSellerCardAlleys');
            $data['domain_auth'] = domain_auth();
            $res = $db->where(array('cid' => $data['cid'], 'alleys_type' => $data['alleys_type'], 'domain_auth' => domain_auth()))->count();
            #如果存在保存 没有新增
            if ($res) {
                $rel = $db->where(array('cid' => $data['cid'], 'alleys_type' => $data['alleys_type'], 'domain_auth' => domain_auth()))->save($data);
            } else {
                $rel = $db->add($data);
            }
            if ($rel) {
                $this->success('商户信息更新成功', U('card_mdata', array('type' => $data['alleys_type'], 'id' => $data['cid'])));
            } else {
                $this->error('商户信息更新失败');
            }
        }
    }

    #商户信息保存
    public function mdata_save()
    {
        if (IS_POST) {
            $data = I('post.');
            #判断数据库是否有此费率
            $db = M('MchSellerAlleys');
            $data['domain_auth'] = domain_auth();
            $res = $db->where(array('cid' => $data['cid'], 'alleys_type' => $data['alleys_type'], 'domain_auth' => domain_auth()))->count();
            #如果存在保存 没有新增
            if ($res) {
                $rel = $db->where(array('cid' => $data['cid'], 'alleys_type' => $data['alleys_type'], 'domain_auth' => domain_auth()))->save($data);
            } else {
                $rel = $db->add($data);
            }
            if ($rel) {
                $this->success('商户信息更新成功', U('mdata', array('type' => $data['alleys_type'], 'id' => $data['cid'])));
            } else {
                $this->error('商户信息更新失败');
            }
        }
    }

    #商户进件
    public function mch_sin()
    {
        $this->display();
    }


    #商户检索扩展
    public function mch_dialog()
    {
        if (IS_POST) {
            $data['mch_name|mch_tel|mch_card_name'] = array('like', '%' . I('post.Search') . '%');
            $data['domain_auth'] = domain_auth();
            $count = M('MchSeller')->where($data)->count();
            $res = M('MchSeller')->where($data)->field('id,mch_name,mch_tel,mch_card_name')->select();
            foreach ($res as $key => $val) {
                $_data[] = '{"id":"' . $val['id'] . '","name":"' . $val['mch_name'] . '","phone":"' . $val['mch_tel'] . '","card_name":"' . $val['mch_card_name'] . '"}';
            }
            $sum_data = "[" . implode(',', $_data) . ']';
            $json = '{"total":' . $count . ',"rows":' . $sum_data . '}';
            if ($count) {
                die($json);
            } else {
                $this->error('未找到商户信息');
            }
        } else {
            $this->display();
        }
    }

    #门店检索扩展
    public function store_dialog()
    {
        if (IS_POST) {
            $data['name|per_name|per_phone'] = array('like', '%' . I('post.Search') . '%');
            $data['domain_auth'] = domain_auth();
            $count = M('MchStore')->where($data)->count();
            $res = M('MchStore')->where($data)->field('id,name,per_name,per_phone')->select();
            foreach ($res as $key => $val) {
                $_data[] = '{"id":"' . $val['id'] . '","name":"' . $val['name'] . '","per_phone":"' . $val['per_phone'] . '","per_name":"' . $val['per_name'] . '"}';
            }
            $sum_data = "[" . implode(',', $_data) . ']';
            $json = '{"total":' . $count . ',"rows":' . $sum_data . '}';
            if ($count) {
                die($json);
            } else {
                $this->error('未找到门店信息');
            }
        } else {
            $this->display();
        }
    }


}