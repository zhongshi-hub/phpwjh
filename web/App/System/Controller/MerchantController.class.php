<?php
namespace System\Controller;

use Common\Controller\SystemBaseController;

/**
 * 商户管理控制器
 */
class MerchantController extends SystemBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->db = M('MchSeller');

    }

    #信用认证变更
    public function auth_status_alter(){
        $data=I('post.');
        if($data['type']!=1){
            M('MchZmAuth')->where(array('mid'=>$data['id']))->delete();
        }
        $res=M('MchSeller')->where(array('id'=>$data['id']))->save(array('auth_status'=>$data['type']));
        if($res){
            $this->success('认证状态变更成功');
        }else{
            $this->error('认证状态变更失败');
        }
    }


    #进件测试
    public function mch_in()
    {
        R('Pays/Apis/mch_in');
    }


    #过户代理
    public function transfer_agent()
    {
        $data = I('post.');
        $domain_auth = M('MchSeller')->where(array('id' => $data['id']))->getField('domain_auth');
        $where['user_name|id'] = $data['agent'];
        $where['domain_auth'] = $domain_auth;
        $aid = M('MchAgent')->where($where)->getField('id');
        if (!$aid) {
            $this->error('未在当前品牌下找到相关代理信息!分析:1.代理不存在 2.代理存在不在本品牌下');
        } else {
            M('MchSeller')->where(array('id' => $data['id']))->save(array('agent_id' => $aid));
            M('MchSellerAlleys')->where(array('cid' => $data['id']))->save(array('agent_id' => $aid));
            $res = M('MchCodes')->where(array('mch_id' => $data['id']))->save(array('aid' => $aid));
            if ($res) {
                $this->success('代理过户成功!');
            } else {
                $this->error('代理过户失败!');
            }
        }
    }

    #录入商户
    public function abook_save()
    {
        if (IS_POST) {
            $db = M('MchSeller');
            $data = I('post.');
            if (!$data['domain_auth']) {
                $this->error('授权关系数据有误!');
            }
            if (!$data['codes']) {
                $this->error('收款码ID不需为空!');
            }
            if (!$data['agent_id']) {
                $this->error('请选择代理!');
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
                'domain_auth' => $data['domain_auth'],
                'ctime' => time(),
                'loadtime' => time(),
                'status' => 1,
                'type' => 'default'
            );

            #判断唯一规则
            $code = M('MchCodes')->where(array('codes' => $data['codes']))->find();
            if ($code) {
                $this->error('此收款码已存在!');
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
                'domain_auth' => $data['domain_auth'],
                'status' => 1,
            );
            $store_id = M('MchStore')->add($store);

            #保存收款码门店信息
            $_codes = array(
                'aid' => $data['agent_id'],
                'mch_id' => $seller,
                'store_id' => $store_id,
                'status' => 1,
                'codes' => $data['codes'],
                'ctime' => time(),
                'code_url' => self::QrCodeAdd($data['codes']),
                'domain_auth' => $data['domain_auth'],
            );
            $rel = M('MchCodes')->add($_codes);

            if ($rel) {
                $this->success('商户录入成功', U('index'));
            } else {
                $this->error('商户录入失败');
            }

        }
    }


    #手工创建收款码
    public function CodeCrate()
    {
        $data = I('post.');
        $data_code = explode('|', $data['codes']);
        if (!$data['agent_id']) {
            $this->error('代理ID信息不能为空');
        }
        if (!$data['domain_auth']) {
            $this->error('授权品牌ID信息不能为空');
        }
        if (!$data_code) {
            $this->error('请配置收款码ID信息 多个以"|"分割');
        } else {
            $j = 0;
            foreach ($data_code as $v) {
                #判断收款码是否存在
                $code = M('MchCodes')->where(array('codes' => $v))->find();
                if (!$code) {
                    $_codes = array(
                        'aid' => $data['agent_id'],
                        'status' => 1,
                        'codes' => $v,
                        'ctime' => time(),
                        'code_url' => self::QrCodeAdd($v),
                        'domain_auth' => $data['domain_auth'],
                    );
                    $rel = M('MchCodes')->add($_codes);
                    if ($rel) {
                        $j++;
                        rwlog('CodeCrate', $v . '创建成功');
                    }
                } else {
                    rwlog('CodeCrate', $v . '创建失败!已存在');
                }
            }
            $this->success('你此次已生成' . $j . '个收款码,具体请参考日志');
        }
        //$res=self::QrCodeAdd($data);
        //dump($res);
    }


    #创建单个收款码
    public function QrCodeAdd($codes)
    {
        Vendor('qrcode');
        Vendor('XunCode');
        $QrCode = new \QRcode();
        $_path = "./Upload/Code/";
        $errorCorrectionLevel = "M";//容错级别
        $matrixPointSize = "6"; //生成图片大小
        $margin = "1";  //边缘留白
        $url = C('MA_DATA_URL') . "/" . $codes;
        $_qrname = $codes . ".png";
        $QrCode->png($url, $_path . $_qrname, $errorCorrectionLevel, $matrixPointSize, $margin);
        $qr = new \XunCode();
        $_res = $qr->create($url, $codes);
        $res = json_decode($_res, true);
        if ($res['status'] == 1) {
            return ltrim($res['url'], ".");
        } else {
            return false;
        }
    }


    #商户录件
    public function abook()
    {
        $domain_auth = I('get.domain_auth');
        $domain = M('DomainAuth')->where(array('web_authcode' => $domain_auth))->find();
        if (!$domain) {
            $this->error('品牌信息错误!未获取到相关品牌信息!');
        }

        #代理列表
        $agent = M('MchAgent')->where(array('domain_auth' => $domain['web_authcode']))->field('id,user_name')->select();

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
            'domain' => $domain,
            'agent' => $agent
        );
        $this->assign($assign);

        //dump($agent);
        $this->display();
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


        $map['sid'] = I('get.id');
        $map['store_id'] = I('get.store_id');
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $seller = M('MchSeller')->where(array('id' => I('get.id')))->count();
        if (!$seller) {
            $this->error('获取上级商户信息失败!');
        }
        $store = M('MchStore')->where(array('id' => I('get.store_id')))->count();
        if (!$store) {
            $this->error('获取上级门店信息失败!');
        }

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
        //$map['domain_auth'] = domain_auth();
        $map['sid'] = I('get.id');
        #判断是否存在商户
        $seller = M('MchSeller')->where(array('id' => I('get.id')))->find();
        if (!$seller) {
            $this->error('获取上级商户信息失败!');
        }

        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $assign = array(
            'data' => $list,
            'page' => $show,
            'domain_auth' => $seller['domain_auth'],
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
            'domain_auth' => $data['domain_auth'],
            'pay_type' => serialize($_data),
        );

        if ($data['id']) {
            $where['id'] = $data['id'];
            $where['domain_auth'] = $data['domain_auth'];
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
        //$map['domain_auth'] = domain_auth();
        $map['cid'] = $data['cid'];
        $map['alleys_type'] = $data['type'];
        $res = M('MchSellerAlleys')->where($map)->field('mch_id,mch_key,id')->find();
        if ($res) {
            $arr = array(
                'msg' => '通道配置参数获取成功',
                'mch_id' => $res['mch_id'],
                'mch_key' => $res['mch_key'],
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
        //$map['domain_auth'] = domain_auth();
        $map['id'] = $data['id'];
        $res = M('MchSellerAlleys')->where($map)->save(array('mch_id' => $data['mch_id'], 'mch_key' => $data['mch_key'], 'status' => 1, 'load_status' => 1));
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
        //$domain_auth=M('MchSeller')->where(array('id'=>$data['cid']))->getField('domain_auth');
        if (!$data['cid']) {
            $this->error('参数有误');
        }
        if (!$data['type']) {
            $this->error('通道类型参数有误!');
        }
        if (!$data['alleys_type']) {
            $this->error('通道TYPE参数有误!');
        }
        //$map['domain_auth'] = $domain_auth;
        $map['id'] = $data['cid'];
        if ($data['alleys_type'] == 'wx') {
            $save = array('wx_alleys' => $data['type']);
        } else {
            $save = array('ali_alleys' => $data['type']);
        }
        $res = M('MchSeller')->where($map)->save($save);
        //dump($res);
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
            $domain_auth = M('MchSeller')->where(array('id' => I('post.id')))->getField('domain_auth');
            $where['domain_auth'] = $domain_auth;
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


    #商户审核通过列表
    public function index()
    {
        $data = I('param.');
        if ($data['domain_auth']) {
            $map['domain_auth'] = $data['domain_auth'];
        }
        if ($data['aid']) {
            $map['agent_id'] = $data['aid'];
        }
        if ($data['search_val']) {
            $map['mch_name|mch_tel|mch_card_name'] = array('like', '%' . $data['search_val'] . '%');
        }

        if ($data['bus_type']) {
            $map['mch_bus_type'] = $data['bus_type'];
        }

        if ($data['wx_alleys'] == 1) {
            $map['wx_alleys'] = array('EXP', 'IS NOT NULL');
        } elseif ($data['wx_alleys'] == 2) {
            $map['wx_alleys'] = array('EXP', 'IS NULL');
        }
        if ($data['ali_alleys'] == 1) {
            $map['ali_alleys'] = array('EXP', 'IS NOT NULL');
        } elseif ($data['ali_alleys'] == 2) {
            $map['ali_alleys'] = array('eq', '');
        }


        $map['status'] = 1;
        $count = $this->db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $this->db->order('id')->where($map)->order('loadtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        #当前品牌下的所有通道
        $where['status'] = 1;
        $api = M('DomainAlleys')->where($where)->field('alleys,alleys_type')->distinct(true)->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
            'api' => $api
        );

        #导出筛选
        $result = $this->db->where($map)->order("id desc")->select();

        if (!empty($data['export']) && $data['export'] == 'ccl') {
            $xlsName = "MchData_";//导出名称
            $xlsCell = array(
                array('domain_auth', '所属品牌'),
                array('mch_name', '商户名称'),
                array('mch_industry', '所属行业'),
                array('mch_provice', '所属省份'),
                array('mch_citys', '所属城市'),
                array('mch_district', '所属区/县'),
                array('mch_address', '详细地址'),
                array('mch_card_name', '负责人姓名'),
                array('mch_card_id', '负责人身份证号'),
                array('mch_tel', '负责人电话'),
                array('mch_bank_list', '开户银行'),
                array('mch_bank_name', '开户人'),
                array('mch_bank_cid', '银行卡号'),
                array('mch_linkbnk_name', '支行名称'),
                array('mch_linkbnk', '联行号'),
                array('mch_bank_provice', '开户省份'),
                array('mch_bank_citys', '开户城市'),
                array('mch_bus_type', '营业类型'),
                array('status', '商户状态'),
            );
            $atitle = "商户信息报表生成时间:" . date('Y-m-d H:i:s');
            $wbscms = array(
                'Atitle' => $atitle,
            );
            foreach ($result as $k => $v) {
                $xlsData[$k]['domain_auth'] = DomainName($v['domain_auth']);
                $xlsData[$k]['mch_name'] = $v['mch_name'];
                $xlsData[$k]['mch_industry'] = Industrid($v['mch_industry']);
                $xlsData[$k]['mch_provice'] = $v['mch_provice'];
                $xlsData[$k]['mch_citys'] = $v['mch_citys'];
                $xlsData[$k]['mch_district'] = $v['mch_district'];
                $xlsData[$k]['mch_address'] = $v['mch_address'];
                $xlsData[$k]['mch_card_name'] = $v['mch_card_name'];
                $xlsData[$k]['mch_card_id'] = "'" . $v['mch_card_id'];
                $xlsData[$k]['mch_tel'] = $v['mch_tel'];
                $xlsData[$k]['mch_bank_list'] = reload_bank($v['mch_bank_list']);
                $xlsData[$k]['mch_bank_name'] = $v['mch_bank_name'];
                $xlsData[$k]['mch_bank_cid'] = "'" . $v['mch_bank_cid'];
                $xlsData[$k]['mch_linkbnk_name'] = reload_banks($v['mch_linkbnk']);
                $xlsData[$k]['mch_linkbnk'] = "'" . $v['mch_linkbnk'];
                $xlsData[$k]['mch_bank_provice'] = $v['mch_bank_provice'];
                $xlsData[$k]['mch_bank_citys'] = $v['mch_bank_citys'];
                $xlsData[$k]['mch_bus_type'] = $v['mch_bus_type'];
                $xlsData[$k]['status'] = $v['status'];

            }
            $this->exportExcel($xlsName, $xlsCell, $xlsData, $wbscms);
        }


        $this->assign($assign);
        $this->display();
    }

    /* #商户审核列表
     public function Auditing(){
         $map['domain_auth'] = domain_auth();
         switch (I('get.status')){
             case 'ref':
                 $map['status']=2;
                 break;
             case  'all':
                 $map['status']=array('neq',1);
                 break;
             default:
                 $map['status']=0;
                 break;
         }
         if(IS_POST){
             if(I('post.search_val')) {
                 $map['codes|mch_name|mch_tel|mch_card_name'] = array('like', '%' . I('post.search_val') . '%');
             }
             if(I('post.aid')){
                 $map['agent_id'] = I('post.aid');
             }
             if(I('post.bus_type')){
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

     }*/

    #商户基本信息
    public function Auditing_detail()
    {
        //$map['domain_auth'] = domain_auth();
        $map['id'] = I('get.id');
        $data = $this->db->where($map)->find();
        if (!$data) {
            $this->error('未找到当前需要审核的商户');
        }
        /*if($data['status']==1){
            $this->error('此商户状态不需要审核');
        }*/
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
        $where['id'] = $data['id'];
        $info = M('mch_seller')->where($where)->find();
        #行业类别
        $Ind = M('MchIndustry')->order('name asc')->select();
        #省份
        $pro = M('CityData')->distinct(true)->field('provice')->select();
        #银行列表
        $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
        $assign = array(
            'data' => $info,
            'ind' => $Ind,
            'pro' => $pro,
            'bank_list' => $bank_list,
        );
        $this->assign($assign);
        $this->display();
    }

    #商户信息审核状态
    /*public function Auditing_status(){
        if(IS_POST) {
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
    }*/

    #审核-商户信息保存
    public function Auditing_save()
    {
        if (IS_POST) {
            $data = I('post.');
            #判断数据库是否有此费率
            $db = M('MchSeller');
            $rel = $db->where(array('id' => $data['id']))->save($data);
            if ($rel) {
                $this->success('商户信息更新成功', U('Auditing_detail', array('id' => $data['id'])));
            } else {
                $this->error('商户信息更新失败');
            }
        }
    }

    #鉴权日志
    public function card_validate_log(){
        $db = M('CardValidateLog');
        //$map['domain_auth'] = domain_auth();
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $db->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        #总鉴权次数
        if((DomainAuthData('auth_card')-auth_card_count(domain_auth())) <= 0){
            $this->error('鉴权次数已用完!无法进件!请联系平台充值!');
        }
        $va_1='0';
        #已用鉴权次数
        $va_2=M('CardValidateLog')->count();
        #剩余鉴权次数
        $va_3='0';
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
        //$map['domain_auth'] = domain_auth();
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
        //$map['domain_auth'] = domain_auth();
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
    public function alter()
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
            $domain_auth = M('MchSeller')->where(array('id' => $data['id']))->getField('domain_auth');
            $domain_id = M('DomainAuth')->where(array('web_authcode' => $domain_auth))->getField('id');
            $map['domain_auth'] = $domain_auth;
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
            $_m = $this->db->where($m)->getField('mch_name');
            if (!$_m) {
                $this->error('商户不存在');
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


    #商户通道列表
    public function api_way()
    {
        $cid = I('get.id');
        if (!$cid) {
            $this->error('非法操作');
        }
        $_map['is_card'] = array('neq',1);
        $_map['status'] = 1;
        $ALLEYS=M('MchAlleys')->where($_map)->getField('type',true);
        #取总的
        $domain_auth = M('MchSeller')->where(array('id' => $cid))->getField('domain_auth');
        $domain_id = M('DomainAuth')->where(array('web_authcode' => $domain_auth))->getField('id');
        $where['cid'] = $domain_id;
        $where['status'] = 1;
        $api = M('DomainAlleys')->where($where)->select();
        foreach ($api as $k => $v) {
            if(in_array($v['alleys_type'],$ALLEYS)) {
                $alter_status = M('MchAlleys')->where(array('type' => $v['alleys_type']))->getField('alter_status');
                $map['cid'] = $cid;
                $map['alleys_type'] = $v['alleys_type'];
                $map['domain_auth'] = $domain_auth;
                $data = M('MchSellerAlleys')->where($map)->find();
                $res['alleys_type'] = $v['alleys_type'];
                $res['alleys'] = $v['alleys'];
                $res['mch_id'] = $data['mch_id']; #商户号
                $res['a_type'] = $data['alleys_type'];
                $res['rate'] = $data['rate'];
                $res['status'] = $data['status'];
                $res['load_status'] = $data['load_status'];
                $res['make'] = $v['make'];
                $res['id'] = $v['id'];
                $res['cid'] = $cid;
                $res['alter_status'] = $alter_status;
                $_data[] = $res;
            }
        }


        //$m['domain_auth'] = domain_auth();
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


    #商户信息列表
    public function mdata()
    {
        $data = I('get.');

        $domain_auth = M('MchSeller')->where(array('id' => $data['id']))->getField('domain_auth');
        $domain_id = M('DomainAuth')->where(array('web_authcode' => $domain_auth))->getField('id');
        #获取代理
        $_where['domain_auth'] = $domain_auth;
        $_where['id'] = $data['id'];
        $aid = M('mch_seller')->where($_where)->getField('agent_id');
        $_rel = M('MchAgent')->where(array('id' => $aid))->find();
        $rate = unserialize($_rel['rate']);
        #判断如果没配置代理终端费率 提示配置
        if (!$rate[$data['type'] . '_term']) {
            $this->error('所属代理姓名:  <span style="color: red">' . $_rel['user_name'] . '</span>未配置当前通道的终端费率,请先配置代理的终端费率后再操作!', '', 9999);
        }

        $alleys = M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type'], 'domain_auth' => $domain_auth))->find();
        #费率
        if ($alleys['rate']) {
            $rates = $alleys['rate'];
        } else {
            #如果还未配置费率 则默认用代理配置的终端费率
            $rates = $rate[$data['type'] . '_term'];
        }

        #如果不存在先新增
        $_Not = M('MchSellerAlleys')->where(array('cid' => $data['id'], 'alleys_type' => $data['type'], 'domain_auth' => $domain_auth))->count();
        if (!$_Not) {
            $_Seller = M('MchSeller')->where(array('id' => $data['id'], 'domain_auth' => $domain_auth))->find();
            $_cid = $_Seller['id'];
            unset($_Seller['id']);
            unset($_Seller['mch_wx_openid']);
            unset($_Seller['mch_wx_name']);
            unset($_Seller['mch_wx_img']);
            unset($_Seller['codes']);
            unset($_Seller['alleys']);
            unset($_Seller['status']);
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
                    $type = M('MchSellerAlleys')->where(array('cid' => $_cid, 'alleys_type' => $v, 'domain_auth' => $domain_auth))->count();
                    if (!$type) {
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


        $map['domain_auth'] = $domain_auth;
        $map['alleys_type'] = $data['type'];
        $map['cid'] = $data['id'];
        #先判断是否有通道的数据,如果没则用主商户信息
        $_allys = M('mch_seller_alleys')->where($map)->find();

        if ($_allys) {
            $info = $_allys;
        } else {
            $where['domain_auth'] = $domain_auth;
            $where['id'] = $data['id'];
            $info = M('mch_seller')->where($where)->find();
        }

        $wheres['cid'] = $domain_id;
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

    #商户信息编辑
    public function mdata_edit()
    {
        $data = I('get.');

        $domain_auth = M('MchSeller')->where(array('id' => $data['id']))->getField('domain_auth');
        $domain_id = M('DomainAuth')->where(array('web_authcode' => $domain_auth))->getField('id');
        $map['domain_auth'] = $domain_auth;
        $map['alleys_type'] = $data['type'];
        $map['cid'] = $data['id'];
        #先判断是否有通道的数据,如果没则用主商户信息
        $allys = M('mch_seller_alleys')->where($map)->find();

        $wheres['cid'] = $domain_id;
        $wheres['status'] = 1;
        $wheres['alleys_type'] = $data['type'];
        $Aname = M('DomainAlleys')->where($wheres)->getField('alleys');
        if (!$Aname) {
            $this->error('通道错误!原因: A:未开通此通道 B:通道系统不存在 C:通道被禁言 D:通道配置有误 N:联系相关负责人');
        }


        if ($allys) {
            $info = $allys;
        } else {
            $where['domain_auth'] = $domain_auth;
            $where['id'] = $data['id'];
            $info = M('mch_seller')->where($where)->find();
        }

        $wheres['cid'] = $domain_id;
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
        $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();

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

    #商户信息保存
    public function mdata_save()
    {
        if (IS_POST) {
            $data = I('post.');
            #判断数据库是否有此费率
            $db = M('MchSellerAlleys');
            //$data['domain_auth']=domain_auth();
            $res = $db->where(array('cid' => $data['cid'], 'alleys_type' => $data['alleys_type']))->count();
            #如果存在保存 没有新增
            if ($res) {
                $rel = $db->where(array('cid' => $data['cid'], 'alleys_type' => $data['alleys_type']))->save($data);
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
            $data['domain_auth'] = I('post.domain_auth');
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
            $data['domain_auth'] = I('post.domain_auth');
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