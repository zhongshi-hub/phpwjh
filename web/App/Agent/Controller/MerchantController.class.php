<?php
namespace Agent\Controller;
use Agent\Controller\InitBaseController;
class MerchantController extends InitBaseController {


    public function _initialize()
    {
        parent::_initialize();
        $this->db = M('MchSeller');
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
        $map['aid']=array('in',self::AgentAll());
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
                $this->error('当前商户未开通任何通道,请联系平台方开通!');
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
        $map['agent_id']=array('in',self::AgentAll());
        $map['domain_auth'] = domain_auth();
        $map['status'] = 1;
        $count = $this->db->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $this->db->order('id')->where($map)->order('loadtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        #当前品牌下的所有通道
        $where['cid'] = domain_id();
        $where['status'] = 1;
        $api = M('DomainAlleys')->where($where)->field('alleys,alleys_type')->select();

        $assign = array(
            'data' => $list,
            'page' => $show,
            'api' => $api
        );


        $this->assign($assign);

        //dump($api);

        $this->display();
    }



    #商户检索扩展
    public function mch_dialog()
    {
        if (IS_POST) {
            $data['mch_name|mch_tel|mch_card_name'] = array('like', '%' . I('post.Search') . '%');
            $data['domain_auth'] = domain_auth();
            $data['agent_id']=array('in',self::AgentAll());
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
            $data['domain_auth'] = domain_auth();
            $data['agent_id']=array('in',self::AgentAll());
            $count = M('MchSeller')->where($data)->getField('id',true);

            $data['sid']=array('in',$count);
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