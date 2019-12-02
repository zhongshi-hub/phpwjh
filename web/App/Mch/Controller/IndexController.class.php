<?php
namespace Mch\Controller;

use Mch\Controller\InitBaseController;

class IndexController extends InitBaseController
{
    protected $wxXwAuth;
    protected $aliAuth;
    protected $aliAuthUrl;
    protected $aliAuthImgUrl;

    public function _initialize(){
        parent::_initialize();
        $Seller = M('MchSeller')->where(array('id' => $_SESSION['mch']['id'],'domain_auth' => domain_auth()))->getField('auth_status');
        $zm_auth=DomainAuthData('zm_auth');
        if($zm_auth) {
            if ($Seller != 1 && ACTION_NAME != 'mch_auth' && ACTION_NAME != 'zm_auth' && ACTION_NAME != 'my') {
                redirect(U('mch_auth'));
            }
        }

        //是否需要微信授权
		$xwAuth=M('xwApplyin')->where(['mid'=>$_SESSION['mch']['id'],'domain_auth'=>domain_auth(),'applyment_state'=>'TO_BE_SIGNED'])->getField('applyment_id');
		$this->wxXwAuth=$xwAuth?$xwAuth:false;
		//是否需要支付宝授权
		$aliAuthData=M('isvToken')->where(['mid'=>$_SESSION['mch']['id'],'domain_auth'=>domain_auth(),'user_id'=>['exp','IS NOT NULL']])->find();
		$this->aliAuth=$aliAuthData?false:true;
		//支付宝授权链接
		$this->aliAuthUrl='http://'.$_SERVER["HTTP_HOST"].'/aliOauthUrl?id='.$_SESSION['mch']['id'];
		$this->aliAuthImgUrl="/Pays/Mch/QrData/url/".Xencode($this->aliAuthUrl);
		$this->assign([
			'wxXwAuth'=>$this->wxXwAuth,
			'aliAuth'=>$this->aliAuth,
		]);
    }


	/**
	 * 商户授权签约微信或支付宝
	 */
    public function oauthData(){
		if(I('get.type')=='wx'){
			if(false==$this->wxXwAuth){
				$this->error('当前商户状态非待签约状态');
			}
            $applyment_id=(int)$this->wxXwAuth;
			try {
				$res = R('Pays/XiaoWei/applyState', [$applyment_id]);
			}catch (\Exception $e){
				$this->error($e->getMessage());
			}
			$imgUrl=$res['sign_url'];
		}
		//获取商户注册时的姓名
		$assign=[
			'type'=>I('get.type'),
			'wxImgUrl'=>$imgUrl,
			'aliAuthImgUrl'=>$this->aliAuthImgUrl,
		];
		$this->assign($assign);
        $this->display();
	}



    public function pay_scan_data(){
        $type=I('get.type');
        if(!$type){$this->error('非法操作');}
        $Store = M('MchStore')->where(array('mid' => $_SESSION['mch']['id'], 'domain_auth' => domain_auth(), 'status' => 1))->limit(1)->getField('id');
        if ($Store) {
            $arr = array(
                'id' => $Store,
                'sid' => $_SESSION['mch']['id'],
                'type'=> $type
            );
            $url = U('pay_scan', array('mch_data' => Xencode(json_encode($arr))));
            redirect($url);
        } else {
            $this->error('未获取到商户信息');
        }
    }

    #扫码收款和定额收款 主扫被扫
    public function pay_scan(){
        $data = json_decode(Xdecode(I('get.mch_data')), true);
        if (!I('get.mch_data')) {
            $this->error('未知参数,非法操作');
        }
        #获取门店信息
        $StoreID = M('MchStore')->where(array('domain_auth' => domain_auth(), 'sid' => $_SESSION['mch']['id'], 'status' => 1))->limit(1)->getField('id');
        if ($data['id'] != $StoreID || $data['sid'] != $_SESSION['mch']['id']) {
            redirect(U('pay_scan', array('mch_data' => Xencode(json_encode(array('id' => $StoreID, 'sid' => $_SESSION['mch']['id'], 'type' => $data['type']))))));
        } else {
            $res = M('MchStore')->where(array('id' => $data['id'], 'sid' => $data['sid']))->find();
            switch ($data['type']==1){
                case 1;
                    $title='扫码收款 商家扫客户';
                    $jsapi=self::scan_jsapi();
                    break;
                default:
                    $title='定额收款 客户扫商家';
                    $jsapi='';
                    break;

            }
            $assign=array(
                'data'=>$data,
                'store'=>$res,
                'title'=>$title,
                'jsapi'=>$jsapi,
            );
            $this->assign($assign);
            $this->display();
        }
    }

    #微信JSAPI
    public  function scan_jsapi(){
        $wid= GetWxId('m');
        // 创建SDK实例
        $script = &  load_wechat('Script',$wid);
        $options = $script->getJsSign(get_url(), $timestamp, $noncestr, $appid);
        if($options===FALSE){
            // 接口失败的处理
            return $script->errMsg;
        }else{

            unset($options['jsApiList']);
            $options['jsApiList']=array('scanQRCode');
            return $options;
        }
    }


    #费率信息
    public function mch_rate_list(){
        $ALLEYS=M('MchAlleys')->where(['status'=>1])->getField('type',true);
        $api = M('DomainAlleys')->where(['cid'=>domain_id(),'status'=>1])->select();
        $_data=[];
        foreach ($api as $k => $v) {
            if(in_array($v['alleys_type'],$ALLEYS)) {
                //移动支付
                $data = M('MchSellerAlleys')->where(['mch_id'=>array('EXP','IS NOT NULL'),'domain_auth'=>domain_auth(),'cid'=>$_SESSION['mch']['id'],'alleys_type'=>$v['alleys_type']])->field('alleys_type,rate')->find();
                if($data) {
                    $res['alleys'] = alleys_name($data['alleys_type']);
                    $res['rate'] = $data['rate'];
                    $_data['pay'][] = $res;
                }
                //无卡快捷
                $card_data = M('MchSellerCardAlleys')->where(['mch_id'=>array('EXP','IS NOT NULL'),'domain_auth'=>domain_auth(),'cid'=>$_SESSION['mch']['id'],'alleys_type'=>$v['alleys_type']])->field('alleys_type,rate')->find();
                if($card_data) {
                    $card_res['alleys'] = alleys_name($card_data['alleys_type']);
                    $card_res['rate'] = $card_data['rate'];
                    $_data['card'][] = $card_res;
                }
            }
        }
        $this->assign('data',$_data);
        $this->display();
    }

    #提现记录/交易流水列表
    public function total_tx_order(){
        $this->assign('type',I('get.type'));
        $this->display();
    }

    #提现详情页
    public function total_tx_trade(){
        $map['tx_order'] = I('get.order_id');
        $map['cid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $db = M('MchSftTx')->where($map)->find();
        if (!$db) {
            $this->error('提现流水订单号不存在!');
        }
        $rel=json_decode($db['tx_rel'],true);
        $errorMsg='代码:'.$rel['errorCode'].'  提示:'.$rel['errorMsg'];
        $this->assign('msg', $errorMsg);
        $this->assign('data', $db);
        $this->display();
    }

    #V通道提现列表
    public function v_tx_order_json(){
        $db = M('MchSftTx');
        $map['alleys'] = 'Sftpays';
        $map['cid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $list = $db->where($map)->order('id desc')->field('tx_order,tx_total,status,ctime')->select();
        $visit_list = array();
        $ret = array();
        foreach ($list as $v) {
            $_Day = date('Y年m月d日', strtotime($v['ctime']));
            $visit_list[$_Day][] = $v;
        }
        foreach ($visit_list as $key => $day) {
            $_set['day'] = $key;
            $_set['count'] = count(self::v_list_data($day));
            $_set['sum'] = number_format(array_sum(self::v_total_data($day)), 2);
            $_set['data'] = self::v_list_data($day);
            $ret[] = $_set;
        }
        $count = count($ret);
        $Page = new \Think\Mpage($count, 1);
        $list = array_slice($ret, $Page->firstRow, $Page->listRows);
        $_data = array(
            'pages' => $Page->totalPages,
            'data' => $list,
        );
        echo json_encode($_data);
        exit;
        //dump($ret);
    }

    public function v_total_data($list)
    {
        $total = array();
        foreach ($list as $value) {
            $total[] = $value['tx_total'];
        }
        return $total;
    }
    public function v_list_data($list)
    {
        $res = array();
        foreach ($list as $v) {
            $img = '/Source/Image/mch/提现.png';
            $rel['status'] = Get_Sft_total_end($v['status']);
            $rel['service'] = $img;
            $rel['out_trade_no'] = $v['tx_order'];
            $rel['total_fee'] = $v['tx_total'];
            $rel['createtime'] = date('H:i:s',strtotime($v['ctime']));
            $rel['out_end'] = substr($v['tx_order'], -4);
            $res[] = $rel;
        }
        return $res;
    }

    #V通道交易流水列表
    public function v_order_data_json()
    {
        $db = M('MchOrders');
        $data = I('param.');
        $STime = $data['stime'] ? $data['stime'] : '';
        $ETime = $data['etime'] ? $data['etime'] : date('Y-m-d', strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
		$map['service'] = $data['pay_type'] ? array('like', '%' . $data['pay_type'] . '%') : array('EXP', 'IS NOT NULL');
        $map['out_trade_no'] = $data['out_trade_no'] ? array('like', '%' . $data['out_trade_no'] . '%') : array('EXP', 'IS NOT NULL');
        $map['store_id'] = $data['store_id'] ? $data['store_id'] : array('EXP', 'IS NOT NULL');
        $map['status'] = 1;
        $map['alleys'] = 'Sftpays';
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $list = $db->where($map)->order('id desc')->field('service,out_trade_no,store_id,total_fee,createtime')->select();
        #根据日期筛选
        $visit_list = array();
        $ret = array();
        foreach ($list as $v) {
            $_Day = date('Y年m月d日', $v['createtime']);
            $visit_list[$_Day][] = $v;
        }
        foreach ($visit_list as $key => $day) {
            $_set['day'] = $key;
            $_set['count'] = count(self::list_data($day));
            $_set['sum'] = number_format(array_sum(self::total_data($day)), 2);
            $_set['data'] = self::list_data($day);
            $ret[] = $_set;
        }
        #总交易额
        $sum = $db->order('id')->where($map)->sum('total_fee');
        $_count = $db->order('id')->where($map)->count();
        $count = count($ret);
        $Page = new \Think\Mpage($count, 1);
        $list = array_slice($ret, $Page->firstRow, $Page->listRows);
        $_data = array(
            'pages' => $Page->totalPages,
            'data' => $list,
            'sum' => Rand_total($sum),
            'count' => $_count,
        );
        echo json_encode($_data);
        exit;

    }

    #在线提现页面
    public function total_tx(){
        #判断是否存在V通道
        $Sft=M('MchSellerAlleys')->where(array('cid'=> $_SESSION['mch']['id'],'alleys_type'=>'Sftpays','domain_auth'=>domain_auth()))->count();
        if(!$Sft){
            $this->error('您未开通V通道,无法使用此功能');
        }else {
            $wid = GetWxId('m');
            $oauth = &load_wechat('Oauth', $wid);
            $user = &load_wechat('User', $wid);
            $callback = get_url();
            $state = 'MchOpenid';
            if (!I('get.code')) {
                $scope = 'snsapi_base';
                $result = $oauth->getOauthRedirect($callback, $state, $scope);
                if ($result === FALSE) {
                    $this->error('通信失败!请联系管理员!');
                } else {
                    redirect($result);
                }
            }else{
                $Token = $oauth->getOauthAccessToken();
                if ($Token === FALSE) {
                    redirect(U('total_tx'));
                } else {
                    $result = $user->getUserInfo($Token['openid']);
                    if($result['subscribe']==1){ //已关注
                        #保存商户微信信息到商户数据里
                        $save=array(
                          'mch_wx_name'=>WxNameFilter($result['nickname']),
                          'mch_wx_img'=>$result['headimgurl'],
                          'mch_wx_openid'=>$result['openid'],
                        );
                        M('MchSeller')->where(array('id'=>$_SESSION['mch']['id'],'domain_auth'=>domain_auth()))->save($save);
                        $assign = array(
                            'mch_id' =>$_SESSION['mch']['id']
                        );
                        $this->assign($assign);
                        $this->display();
                    }else{//未关注
                        $this->display('mch_bind_wx');
                    }
                }
            }

            /*$assign = array(
                'mch_id' =>$_SESSION['mch']['id']
            );
            $this->assign($assign);
            $this->display();*/
        }
    }

    #芝麻信用认证
    public function zm_auth()
    {
        #先查找此商户是否有认证记录
        $zm_auth = M('MchZmAuth')->where(array('mid' => $_SESSION['mch']['id'], 'domain_auth' => domain_auth()))->find();
        if ($zm_auth['biz_no']) {
            $url = U('Plugs/UserAuth/return_url', array('biz' => $zm_auth['biz_no']));
            redirect($url);
        } else {
            $mch = Xencode(json_encode(array(
                'domain_auth' => domain_auth(),
                'mch_id' => $_SESSION['mch']['id'],
            )));
            $url = U('Plugs/UserAuth/zm_auth', array('mch' => $mch));
            redirect($url);
        }
    }


    #快捷收款
    public function quick_url()
    {
        $Store = M('MchStore')->where(array('mid' => $_SESSION['mch']['id'], 'domain_auth' => domain_auth(), 'status' => 1))->limit(1)->getField('id');
        if ($Store) {
            $arr = array(
                'id' => $Store,
                'sid' => $_SESSION['mch']['id'],
            );
            $url = U('quick', array('quick_data' => Xencode(json_encode($arr))));
            redirect($url);
        } else {
            $this->error('未获取到商户信息');
        }
    }


    #新版验证码界面
    public function quick_verify()
    {
        $data = json_decode(Xdecode(I('get.quick_data')), true);
        #获取当前卡绑定的手机号
        $db = M('MchCardBank');
        #判断此卡号是否存在
        $map = array(
            'mid' => $_SESSION['mch']['id'],
            'id' => $data['bank_id'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->getField('phone');
        $data['phone'] = substr_replace($res, '****', 3, 4);
        $this->assign('data', $data);
        $this->display();
    }

    #时间鉴权
    public function alleys_pay_time()
    {
        $_time = M('MchAlleys')->where(array('type' => I('post.type')))->find();
        $pay_time = explode('-', $_time['pay_time']);
        $checkDayStr = date('Y-m-d ', time());
        $timeBegin1 = strtotime($checkDayStr . $pay_time[0] . ":00");
        $timeEnd1 = strtotime($checkDayStr . $pay_time[1] . ":00");
        $curr_time = time();
        if ($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1) {
            //$this->success('时间符合');
            #判断金额是否符合单笔金额
            $total = I('post.total');
            if ($total < $_time['min_total'] || $total > $_time['max_total']) {
                $this->error('当前通道金额限制<br>单笔' . $_time['min_total'] . '元-' . $_time['max_total'] . '元');
            } else {
                $this->success('效验通过');
            }
        } else {
            $this->error('不在交易时间内');
        }
    }

    #新版绑卡操作
    public function bind_fast_card()
    {
        $data = I('post.');
        $db = M('MchCardBank');
        #判断此卡号是否存在
        $map = array(
            'mid' => $_SESSION['mch']['id'],
            'card' => $data['card'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->count();
        if ($res) {
            $this->error('此卡号已存在<br>请在我的银行卡列表内删除后重新添加');
        } else {

            $map = array(
                'id' => $_SESSION['mch']['id'],
                'domain_auth' => domain_auth(),
            );
            $seller = M('MchSeller')->where($map)->field('mch_card_name,mch_card_id')->find();
            #银行卡鉴权
            $care_data = array(
                'cardNo' => $data['card'], #银行卡卡号
                'certNo' => $seller['mch_card_id'], #身份证号
                'name' => $seller['mch_card_name'], #姓名
            );
            #鉴权
            $res = card_validate_calls($care_data, '商户绑卡', $_SESSION['mch']['id']);
            if ($res['status'] == 1) {#鉴权成功
                $arr = array(
                    'mid' => $_SESSION['mch']['id'],
                    'name' => $seller['mch_card_name'],
                    'cert' => $seller['mch_card_id'],
                    'bank' => $data['bank'],
                    'card' => $data['card'],
                    'phone' => $data['phone'],
                    'date' => $data['date'],
                    'cvn' => $data['cvn'],
                    'time' => date('Y-m-d H:i:s'),
                    'domain_auth' => domain_auth(),
                );
                //dump($arr);
                $rel = $db->add($arr);
                if ($rel) {
                    if (I('post.quick_data')) {
                        if(I('post.quick_data')=='repay'){ //智能还款页面来源  2018年08月09日01:16:06
                            $url = U('Repay/index');
                        }else { //快捷支付来源
                            $url = U('quick', array('quick_data' => I('post.quick_data')));
                        }
                    } else {
                        $url = U('bank_card_list');
                    }
                    $this->success('新卡添加成功', $url);
                } else {
                    $this->error('添加新卡失败!');
                }
            } else {
                $this->error($res['msg']);
            }
        }
    }


    #新版添加银行卡
    public function add_bank_card()
    {
        $map = array(
            'id' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $seller = M('MchSeller')->where($map)->field('mch_card_name,mch_card_id')->find();
        #银行卡列表
        $bank_list = M('MchBankList')->where(array('status' => 1, 'qcard' => 1))->order('id asc')->field('bnkcd,bnknm')->select();
        $bank = array();
        foreach ($bank_list as $v) {
            $bank[] = array(
                'id' => $v['bnkcd'],
                'name' => $v['bnknm'],
            );
        }
        $this->assign('bank_data', json_encode($bank, JSON_UNESCAPED_UNICODE));
        $this->assign('seller', $seller);
        $this->display();
    }

    #新版解绑银行卡
    public function unbind_fast_card()
    {
        $db = M('MchCardBank');
        $map = array(
            'id' => I('post.bind_id'),
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->delete();
        if ($res) {
            $this->success('解绑成功');
        } else {
            $this->error('解绑失败');
        }
    }

    #新版快捷支付银行卡页面
    public function bank_card_list()
    {
        $this->assign('get_data', U('add_bank_card', array('quick_data' => I('get.quick_data'))));
        $this->display();
    }

    #新版银联快捷支付页面
    public function quick()
    {
        $data = json_decode(Xdecode(I('get.quick_data')), true);
        if (!I('get.quick_data')) {
            $this->error('未知参数,非法操作');
        }
        #获取门店信息
        $StoreID = M('MchStore')->where(array('domain_auth' => domain_auth(), 'sid' => $_SESSION['mch']['id'], 'status' => 1))->limit(1)->getField('id');
        if ($data['id'] != $StoreID || $data['sid'] != $_SESSION['mch']['id']) {
            redirect(U('quick', array('quick_data' => Xencode(json_encode(array('id' => $StoreID, 'sid' => $_SESSION['mch']['id'], 'total' => $data['total']))))));
        } else {
            $res = M('MchStore')->where(array('id' => $data['id'], 'sid' => $data['sid']))->find();


            $where['cid'] = domain_id();
            $where['status'] = 1;
            $api = M('DomainAlleys')->where($where)->getField('alleys_type',true);

            #获取系统已开通通道
            $_map['is_card'] = array('eq',1);
            $_map['status'] = 1;
            $_map['type']=array('in',$api);
            $ALLEYS=M('MchAlleys')->where($_map)->getField('type',true);


            #已开通的无卡快捷通道
            $map['status'] = 1;
            $map['cid'] = $_SESSION['mch']['id'];
            $map['mch_id'] = array('EXP', 'IS NOT NULL');
            $map['domain_auth'] = domain_auth();
            $map['alleys_type']=array('in',$ALLEYS);
            $alley = M('MchSellerCardAlleys')->where($map)->select();
            #取当前商户配置的通道
            $seller = M('MchSeller')->where(array('id' => $_SESSION['mch']['id'], 'domain_auth' => domain_auth()))->field('wx_alleys,ali_alleys,id')->find();
            $data['alley_data'] = $alley;
            $data['seller'] = $seller;
            $this->assign('data', $data);

            #取费率
            $rate_total = array();
            foreach ($alley as $value) {
                $is_remak = AlleysGetData($value['alleys_type'], 'is_remak');
                if ($is_remak) {
                    $rate_totals['rate'] = $is_remak;
                } else {
                    $rate_totals['rate'] = $value['rate'];
                }
                $rate_totals['type'] = $value['alleys_type'];
                $rate_totals['total'] = AlleysGetData($value['alleys_type'], 'is_total');
                $rate_total[] = $rate_totals;
            }
            $rate_total_data = json_encode($rate_total, JSON_UNESCAPED_UNICODE);
            $this->assign('rate_total_data', $rate_total_data);
            if ($res) {
                $this->assign('res', $res);
                $this->assign('total', $data['total']);
                $this->assign('get_data', U('bank_card_list', array('quick_data' => I('get.quick_data'))));
                $this->assign('data', $data);
                C('TOKEN_ON', false);
                $this->display();
            } else {
                $this->error('参数失败!');
            }
        }
    }

    #新版银联快捷支付银行卡界面
    public function bind_card_list()
    {
        #根据ID获取银行卡列表
        $db = M('MchCardBank');
        $map = array(
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->order('id desc')->select();
        if ($res) {
            $bank = array();
            foreach ($res as $v) {
                $yh = M('MchBankList')->where(array('bnkcd' => $v['bank']))->find();
                $bank[] = array(
                    'bank_name' => $yh['bnknm'] . '信用卡',
                    'bank_logo' => '/Source/Image/bank/' . $yh['bnkcd'] . '.png',
                    'card' => substr($v['card'], -4),
                    'id' => $v['id'],
                );
            }
            $this->success($bank);
        } else {
            $this->error('未添加卡');
        }
    }


    #测试页面
    public function index()
    {
        $db = M('MchOrders');
        $map['status'] = 1;
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        #统计今日总额
        $STimes = date("Y-m-d");
        $ETimes = date('Y-m-d', strtotime("1 day"));
        $wheres['_string'] = "(`time_end`> '" . strtotime($STimes) . "') AND ( `time_end` < '" . strtotime($ETimes) . "') ";
        $DSum = $db->where($map)->where($wheres)->sum('total_fee');
        //退款金额
		$re_total=$db->where($map)->where($wheres)->where(['service'=>['like','%_refund']])->sum('total_fee'); //退款总金额
        #今日笔数
        $D_count = $db->where($map)->where($wheres)->count();
        #微信金额
        $_wx = $db->where($map)->where($wheres)->where(array('service' => array('like', 'wx_%')))->sum('total_fee');
		$wx_re_total=$db->where($map)->where($wheres)->where(['service'=>['like','wx_refund']])->sum('total_fee'); //退款总金额

        $_ali = $db->where($map)->where($wheres)->where(array('service' => array('like', 'ali_%')))->sum('total_fee');

		$ali_re_total=$db->where($map)->where($wheres)->where(['service'=>['like','ali_refund']])->sum('total_fee'); //退款总金额
        $_card = $db->where($map)->where($wheres)->where(array('service' => array('like', 'card_%')))->sum('total_fee');

        #统计昨日总额
        /*$STime = date("Y-m-d", strtotime("-1 day"));
        $ETime = date('Y-m-d');
        $where['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        #统计昨日总交易额
        $GoSum = $db->where($map)->where($where)->sum('total_fee');
        #统计昨日总笔数
        $GoCount = $db->where($map)->where($where)->count();*/

        #判断是否存在V通道
        $RhCard=M('MchSellerCardAlleys')->where(array('cid'=> $_SESSION['mch']['id'],'alleys_type'=>'Rhcard','domain_auth'=>domain_auth()))->count();
        if($RhCard){
            $RhCardStatus=1;
        }else{
            $RhCardStatus=0;
        }

        //判断用户所属代理是否是邀请模式的代理
        if(GetMchAid($_SESSION['mch']['id'])==extensionSetting('aid')){
            $extensionStatus=1;
        }else{
            $extensionStatus=0;
        }
        //是否有新大陆通道
		$isStarPos=M('MchSellerAlleys')->where(array('mch_id'=>['exp','IS NOT NULL'],'cid'=> $_SESSION['mch']['id'],'alleys_type'=>'Starpos','domain_auth'=>domain_auth()))->count();
        $assign = array(
            'J_wx' => round($_wx-$wx_re_total,2),
            'J_ali' => round($_ali-$ali_re_total,2),
            'J_card' => $_card,
            'D_sum' => round($DSum-$re_total,2),
            'D_count' => $D_count,
            'RhCardStatus'=>$RhCardStatus,
            'extensionStatus'=>$extensionStatus,
			'isStarPos'=>$isStarPos
        );
        $this->assign($assign);
        $this->display('new_index');
    }

    #支付页面
    public function bank_pay()
    {
        $p = I('get.');
        if (!$p['data'] || !$p['id'] || !$p['alley']) {
            $this->error('参数有误!');
        }
        $pay_data = json_decode(Xdecode($p['data']), true);
        $pay_data['alleys'] = $p['alley'];

        #获取当前通道类型
        $alleys_bank = M('MchAlleys')->where(array('type' => $p['alley']))->getField('bank_type');
        //dump($pay_data);
        $db = M('MchCardBank');
        $map = array(
            'id' => $p['id'],
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->find();
        if (!$res) {
            $this->error('获取信息失败');
        } else {
            $assign = array(
                'data' => $res,
                'pay_data' => $pay_data,
            );
            $this->assign($assign);
            if ($alleys_bank == 1) {
                C('TOKEN_ON', false);
                $this->display('bank_pay_1');
            } else {
                $this->display();
            }
        }
    }

    #银行卡详情
    public function bank_detail()
    {
        $db = M('MchCardBank');
        #判断此卡号是否存在
        if (IS_POST) {
            if (I('post.type') == 'DelCard') {
                $_map = array(
                    'id' => I('post.id'),
                    'mid' => $_SESSION['mch']['id'],
                    'domain_auth' => domain_auth(),
                );
                $rel = $db->where($_map)->delete();
                if ($rel) {
                    $this->success('银行卡删除成功', U('my_bank'));
                } else {
                    $this->error('银行卡删除失败');
                }
            }

        } else {
            $map = array(
                'id' => I('get.id'),
                'mid' => $_SESSION['mch']['id'],
                'domain_auth' => domain_auth(),
            );
            $res = $db->where($map)->find();
            if (!$res) {
                $this->error('获取信息失败');
            } else {
                $assign = array(
                    'data' => $res
                );
                $this->assign($assign);
                $this->display();
            }
        }
    }

    #添加银行卡
    public function add_bank()
    {
        if (IS_POST) {
            $data = I('post.');
            $db = M('MchCardBank');
            #判断此卡号是否存在
            $map = array(
                'mid' => $_SESSION['mch']['id'],
                'card' => $data['card'],
                'domain_auth' => domain_auth(),
            );
            $res = $db->where($map)->count();
            if ($res) {
                $this->error('此卡号已存在!如信息有误,请在我的-我的银行卡列表内删除后重新添加');
            } else {
                $arr = array(
                    'mid' => $_SESSION['mch']['id'],
                    'name' => $data['name'],
                    'cert' => $data['cert'],
                    'bank' => $data['bank'],
                    'card' => $data['card'],
                    'phone' => $data['phone'],
                    'date' => $data['date'],
                    'cvn' => $data['cvn'],
                    'time' => date('Y-m-d H:i:s'),
                    'domain_auth' => domain_auth(),
                );
                $rel = $db->add($arr);
                if ($rel) {
                    if (I('get.data')) {
                        $url = U('my_bank', array('type' => I('get.type'), 'data' => I('get.data')));
                    } else {
                        $url = U('my_bank');
                    }
                    $this->success('新卡添加成功', $url);
                } else {
                    $this->error('添加新卡失败!');
                }
            }

        } else {
            #银行列表
            $bank_list = M('MchBankList')->where(array('status' => 1, 'qcard' => 1))->order('id asc')->field('bnkcd,bnknm')->select();
            $bank = array();
            foreach ($bank_list as $v) {
                $bank[] = array(
                    'value' => $v['bnkcd'],
                    'text' => $v['bnknm'],
                );
            }
            #获取开户姓名 身份证信息
            $seller = M('MchSeller')->where(array('id' => $_SESSION['mch']['id'], 'domain_auth' => domain_auth()))->find();
            $assign = array(
                'bank' => json_encode($bank, JSON_UNESCAPED_UNICODE),
                'seller' => $seller,
            );
            $this->assign($assign);
            $this->display();
        }
    }

    #我的银行卡列表
    public function my_bank()
    {
        $db = M('MchCardBank');
        #判断此卡号是否存在
        $map = array(
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->order('id desc')->select();
        $bank = array();
        foreach ($res as $v) {
            $yh = M('MchBankList')->where(array('bnkcd' => $v['bank']))->find();
            if (I('get.data')) {
                $bank_url = U('bank_pay', array('id' => $v['id'], 'alley' => I('get.type'), 'data' => I('get.data')));

            } else {
                $bank_url = U('bank_detail', array('id' => $v['id']));

            }
            $bank[] = array(
                'bank_name' => $yh['bnknm'],
                'bank_color' => $yh['color'],
                'bank_logo' => '/Source/Image/bank/' . $yh['bnkcd'] . '.png',
                'card' => substr($v['card'], -4),
                'id' => $v['id'],
                'bank_url' => $bank_url,
            );
        }
        if (I('get.data')) {
            $add_bank_url = U('add_bank', array('type' => I('get.type'), 'data' => I('get.data')));
            $title = '请选择支付卡';
        } else {
            $add_bank_url = U('add_bank');
            $title = '信用卡管理';
        }
        $assign = array(
            'bank' => $bank,
            'add_bank_url' => $add_bank_url,
            'title' => $title,
        );
        //dump($assign);
        $this->assign($assign);
        $this->display();
    }

    #付款结果
    public function query()
    {
        $where['out_trade_no'] = I('post.out_trade_no');
        $where['domain_auth'] = domain_auth();
        $res = M('MchOrders')->where($where)->find();
        if ($res['status'] == 1) {
            $this->success('收款成功!收款金额:' . $res['total_fee']);
        } else {
            $this->error('未支付');
        }
    }

    #收款二维码页面
    public function PayData()
    {
        $data = json_decode(Xdecode(I('get.data')), true);
        #判断此订单是否已付过款
        $where['out_trade_no'] = $data['order'];
        $where['domain_auth'] = domain_auth();
        $res = M('MchOrders')->where($where)->find();
        if ($res['status'] == 1) {
            $this->success('此订单已付款成功!', U('fast_data'));
        }

        //dump($data);
        if ($data['type'] == 'wx') {
            $info = '微信';
            $color = '#2ca43a';
            $pay_img = '/Source/qr_data/wx.png';
        } else {
            $info = '支付宝';
            $color = '#2d94df';
            $pay_img = '/Source/qr_data/ali.png';
        }
        $this->assign('pay_img', $pay_img);
        $this->assign('color', $color);
        $this->assign('info', $info);
        if ($data) {
            $this->assign($data);
            $this->display();
        } else {
            $this->error('操作非法');
        }
        //dump($data);
    }

    #快速收款
    public function alley_list()
    {

        #已开通的无卡快捷通道
        $map['status'] = 1;
        $map['cid'] = $_SESSION['mch']['id'];
        $map['mch_id'] = array('EXP', 'IS NOT NULL');
        $map['domain_auth'] = domain_auth();
        $alley = M('MchSellerCardAlleys')->where($map)->select();
        #取当前商户配置的通道
        $seller = M('MchSeller')->where(array('id' => $_SESSION['mch']['id'], 'domain_auth' => domain_auth()))->field('wx_alleys,ali_alleys,id')->find();

        //dump($alley);
        #数据
        $data = array(
            'total' => I('get.total'),
            'aid' => $_SESSION['mch']['id'],
            'total_fee' => I('get.total') / 100,
            'mid' => $_SESSION['fast']['mid'],
        );
        //dump($alley);
        $data['card_data'] = Xencode(json_encode($data));
        $data['alley_data'] = $alley;
        $data['seller'] = $seller;
        $data['alleys'] = $alleys;
        $this->assign('data', $data);
        $this->display();
    }

    #门店列表
    public function store_fast()
    {
        if (IS_POST) {
            $map['sid'] = $_SESSION['mch']['id'];
            $map['domain_auth'] = domain_auth();
            $map['id'] = I('post.sid');
            $res = M('MchStore')->where($map)->find();
            if ($res) {
                $_SESSION['fast'] = array(
                    'mid' => $res['id'],
                );
                //redirect(U('fast_data'));
                $this->success('门店切换成');
            } else {
                $this->error('切换门店失败!');
            }
        } else {
            $map['sid'] = $_SESSION['mch']['id'];
            $map['domain_auth'] = domain_auth();
            $map['status'] = 1;
            $list = M('MchStore')->where($map)->field('id,sid,name')->select();
            $lists = M('MchStore')->where($map)->field('id,sid,name,per_name,per_phone,status')->count();
            $list_store = M('MchStore')->where($map)->find();
            //dump($list_store);
            if ($lists < 1) {
                $this->error('获取门店信息失败!');
            } elseif ($lists == 1) {
                $_SESSION['fast'] = array(
                    'mid' => $list_store['id'],
                );
                redirect(U('fast_data'));
            } else {
                $this->assign('list', $list);
                $this->display();
            }
        }
    }

    #快速收款
    public function fast_data()
    {
        if ($_SESSION['fast']['mid']) {
            $maps['id'] = $_SESSION['fast']['mid'];
            $maps['sid'] = $_SESSION['mch']['id'];
            $maps['domain_auth'] = domain_auth();
            $list = M('MchStore')->where($maps)->field('id,sid,name,per_name,per_phone,status')->find();
            $this->assign('list', $list);
            $this->display();
        } else {
            redirect(U('store_fast'));
        }
        //dump($_SESSION['fast']['mid']);
    }

    #服务协议
    public function fw()
    {
        $this->display();
    }

    #保密协议
    public function bm()
    {
        $this->display();
    }

    #主页
    /*public function index()
    {
        $db = M('MchOrders');
        $map['status'] = 1;
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        #统计今日总额
        $STimes = date("Y-m-d");
        $ETimes = date('Y-m-d', strtotime("1 day"));
        $wheres['_string'] = "(`time_end`> '" . strtotime($STimes) . "') AND ( `time_end` < '" . strtotime($ETimes) . "') ";
        $DSum = $db->where($map)->where($wheres)->sum('total_fee');
        #今日笔数
        $D_count = $db->where($map)->where($wheres)->count();

        #统计昨日总额
        $STime = date("Y-m-d", strtotime("-1 day"));
        $ETime = date('Y-m-d');
        $where['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        #统计昨日总交易额
        $GoSum = $db->where($map)->where($where)->sum('total_fee');
        #统计昨日总笔数
        $GoCount = $db->where($map)->where($where)->count();

        $assign = array(
            'G_sum' => $GoSum,
            'G_count' => $GoCount,
            'D_sum' => $DSum,
            'D_count' => $D_count,
        );
        $this->assign($assign);
        $this->display();
    }*/

    #流水筛选
    public function order_data()
    {
        $db = M('MchOrders');

        $map['status'] = 1;
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $min = $db->where($map)->min('time_end');

        #门店列表
        $_map['sid'] = $_SESSION['mch']['id'];
        $_map['domain_auth'] = domain_auth();
        $store = M('MchStore')->where($_map)->field('id,name')->select();

        $assign = array(
            'min' => date('Y-m-d', $min),
            'max' => date('Y-m-d', strtotime('1 day')),
            'store' => $store,
        );


        $this->assign($assign);
        $this->display();
    }

    #流水列表
    public function order_data_json()
    {
        $db = M('MchOrders');
        $data = I('param.');
        $STime = $data['stime'] ? $data['stime'] : '';
        $ETime = $data['etime'] ? $data['etime'] : date('Y-m-d', strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        $map['service'] = $data['pay_type'] ? $data['pay_type'] . '_jsapi' : array('EXP', 'IS NOT NULL');
        $map['out_trade_no'] = $data['out_trade_no'] ? array('like', '%' . $data['out_trade_no'] . '%') : array('EXP', 'IS NOT NULL');

        $map['store_id'] = $data['store_id'] ? $data['store_id'] : array('EXP', 'IS NOT NULL');

        $map['status'] = 1;
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $list = $db->where($map)->order('id desc')->field('service,out_trade_no,store_id,total_fee,createtime')->select();
        #根据日期筛选
        $visit_list = array();
        $ret = array();
        foreach ($list as $v) {
            $_Day = date('Y年m月d日', $v['createtime']);
            $visit_list[$_Day][] = $v;
        }
        foreach ($visit_list as $key => $day) {
            $_set['day'] = $key;
            $_set['count'] = count(self::list_data($day));
            $_set['sum'] = number_format(array_sum(self::total_data($day)), 2);
            $_set['data'] = self::list_data($day);
            $ret[] = $_set;
        }
        #总交易额
        $sum = $db->order('id')->where($map)->sum('total_fee');
        #总退款金额
		$re = $db->order('id')->where($map)->where(['service'=>['like','%_refund']])->sum('total_fee');

        $_count = $db->order('id')->where($map)->count();
        $count = count($ret);
        $Page = new \Think\Mpage($count, 1);
        $list = array_slice($ret, $Page->firstRow, $Page->listRows);
        $_data = array(
            'pages' => $Page->totalPages,
            'data' => $list,
            'sum' => Rand_total($sum-$re),
            'count' => $_count,
        );
        echo json_encode($_data);
        exit;

    }

    function array_page($array, $rows)
    {
        $count = count($array);
        $Page = new \Think\Mpage($count, $rows);
        $list = array_slice($array, $Page->firstRow, $Page->listRows);
        return $list;

    }

    public function total_data($list)
    {
        $total = array();
        foreach ($list as $value) {
            $total[] = $value['total_fee'];
        }
        return $total;
    }

    public function list_data($list)
    {
        $res = array();
        foreach ($list as $v) {
            $service=explode('_',$v['service']);
            if ($service[0] == 'ali') {
                $img = '/Source/statics/ali.png';
            } elseif ($v['service'] == 'card_api') {
                $img = '/Source/statics/yl.png';
            } elseif ($v['service'] == 'repay_hk') {
                $img = '/Source/statics/repay_hk.png';
            }elseif ($v['service'] == 'repay_xf') {
                $img = '/Source/statics/repay_xf.png';
            }else {
                $img = '/Source/statics/wx.png';
            }
            $store = Get_Store($v['store_id']);
            $rel['service'] = $img;
            $rel['out_trade_no'] = $v['out_trade_no'];
            $rel['store_id'] = msubstr($store['name'], 0, 8);
            $rel['total_fee'] = $v['total_fee'];
            $rel['createtime'] = date('H:i:s', $v['createtime']);
            $rel['out_end'] = substr($v['out_trade_no'], -4);
            $res[] = $rel;
        }
        return $res;
    }


    #流水
    public function order()
    {
        /*$data=I('param.');
        $db=M('MchOrders');
        $STime=$data['stime']?$data['stime']:'';
        $ETime=$data['etime']?$data['etime']:date('Y-m-d',strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        $map['service']=$data['pay_type']?$data['pay_type'].'_jsapi':array('EXP','IS NOT NULL');
        $map['out_trade_no']=$data['out_trade_no']?array('like','%'.$data['out_trade_no'].'%'):array('EXP','IS NOT NULL');

        $map['status']=1;
        $map['mid']=$_SESSION['mch']['id'];
        $map['domain_auth']=domain_auth();
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Mpage($count, 5);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        #总交易额
        $sum = $db->order('id')->where($map)->sum('total_fee');
        $count =$db->order('id')->where($map)->count();
        $assign=array(
            'data'=>$list,
            'page'=>$show,
            'sum'=>$sum,
            'count'=>$count,
        );
        $this->assign($assign);*/

        $this->display();
    }


    #订单详细页面
    public function trade_data()
    {
        $map['out_trade_no'] = I('get.order_id');
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $db = M('MchOrders')->where($map)->find();
        if (!$db) {
            $this->error('您输入的流水订单号不存在!');
        }
        $this->assign('data', $db);
        $this->display();

    }

    #我的
    public function my()
    {
        //判断用户所属代理是否是邀请模式的代理
        if(GetMchAid($_SESSION['mch']['id'])==extensionSetting('aid')){
            $extensionStatus=1;
        }else{
            $extensionStatus=0;
        }
        $this->assign('extensionStatus',$extensionStatus);
        $this->display();
    }

    #商户信息
    public function mch_data()
    {
        $this->display();
    }


    #门店店员管理
    public function store_user()
    {
        if (I('get.store_id')) {
            $map['store_id'] = I('get.store_id');
            $map['sid'] = $_SESSION['mch']['id'];
            $map['domain_auth'] = domain_auth();
            $list = M('MchStoreUser')->where($map)->select();
            #获取门店名称
            $where['id'] = I('get.store_id');
            $where['sid'] = $_SESSION['mch']['id'];
            $where['domain_auth'] = domain_auth();
            $name = M('MchStore')->where($where)->getField('name');

            #添加店员使用
            $bind_url = 'http://' . $_SERVER['HTTP_HOST'] . U('Pays/Mch/store_user_bind', array('id' => I('get.id'), 'store_id' => I('get.store_id')));
            $QrUrl = U('Pays/Mch/QrData', array('url' => Xencode($bind_url)));

            $assign = array(
                'list' => $list,
                'QrUrl' => $QrUrl,
                'name' => $name,
            );
            $this->assign($assign);
            $this->display();
        } else {
            $this->error('门店信息获取失败!请刷新后再试!');
        }
    }

    #店员删除
    public function store_user_del()
    {
        $id = I('post.id');
        if (!$id) {
            $this->error('ID信息有误!');
        } else {
            $map['id'] = $id;
            $map['sid'] = $_SESSION['mch']['id'];
            $map['domain_auth'] = domain_auth();
            $res = M('MchStoreUser')->where($map)->delete();
            if ($res) {
                $this->success('店员信息删除成功');
            } else {
                $this->error('店员信息删除失败');
            }
        }
    }


    #门店信息
    public function store_data()
    {
        $map['sid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $list = M('MchStore')->where($map)->field('id,sid,name,per_name,per_phone,status')->select();
        $assign = array(
            'list' => $list
        );
        $this->assign($assign);
        $this->display();
    }

    #结算方式
    public function alley_data()
    {
        #获取商户已开通的通道
        $map['cid'] = $_SESSION['mch']['id'];
        $map['mch_id'] = array('EXP', 'IS NOT NULL');
        $map['domain_auth'] = domain_auth();
        $data = M('MchSellerAlleys')->where($map)->field('alleys_type,rate')->select();


        $where['cid'] = domain_id();
        $where['status'] = 1;
        $api = M('DomainAlleys')->where($where)->getField('alleys_type',true);
        //dump($api);
        $_data=array();
        foreach ($data as $k => $v) {
            if(in_array($v['alleys_type'],$api)) {
                $res['alleys_type']=$v['alleys_type'];
                $res['rate']=$v['rate'];
                $_data[] = $res;
            }
        }
        $assign = array(
            'list' => $_data
        );
        $this->assign($assign);
        $this->display();
    }

    #通道切换数据保存
    public function alley_data_save()
    {
        $data = I('post.');
        if (!$data['alley']) {
            $this->error('参数错误!请重新登录后尝试');
        }
        $map['id'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        if ($data['type'] == 'wx') {
            $save = array('wx_alleys' => $data['alley']);
        } else {
            $save = array('ali_alleys' => $data['alley']);
        }
        $res = M('MchSeller')->where($map)->save($save);
        if ($res) {
            $this->success('结算方式切换成功');
        } else {
            $this->error('结算方式切换失败!');
        }

    }

}