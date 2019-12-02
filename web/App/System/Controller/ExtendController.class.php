<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;

/*
 * 扩展模块
 * */

class ExtendController extends SystemBaseController
{

    #鉴权次数增加
    public function auth_card_adds(){
        $data=I('post.');
        if(!$data['auth_card_count']){
            $this->error('请输入新增的次数');
        }
        if(!$data['auth_card_id']){
            $this->error('未获取到相关品牌信息');
        }
        #添加次数
        $res=M('DomainAuth')->where(array('id'=>$data['auth_card_id']))->setInc('auth_card',$data['auth_card_count']);
        $web_name=M('DomainAuth')->where(array('id'=>$data['auth_card_id']))->getField('web_name');
        if($res){
            $data=$web_name.'新增鉴权次数'.$data['auth_card_count'].' 状态:成功 ';
        }else{
            $data=$web_name.'新增鉴权次数'.$data['auth_card_count'].' 状态:失败 ';
        }
        #保存日志
        $arr=array(
            'opid'=>$_SESSION['system']['id'],
            'data'=>$data,
            'time'=>date('Y-m-d H:i:s'),
        );
        M('AuthCardLog')->add($arr);
        $this->success('系统已处理您的请求');
    }
    #联行号管理
    public function Lbank()
    {
        $p=I('param.');
        $p_data=I('param.');
        if(($p['bank_list']&&$p['bank_city'])||($p['bank_list']&&$p['bank_provice'])){
            $p=str_replace("省","",$p);
            $p=str_replace("市","",$p);
            $map['pro']=array('like','%'.$p['bank_provice'].'%');
            if($p['bank_city']) {
            $map['city']=array('like','%'.$p['bank_city'].'%');
            }
            $map['bank'] = array('like', '%' . $p['bank_list'] . '%');
            $Data = M('BanksDataNew');
            $count = $Data->where($map)->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }
        #省份
        $pro = M('CityData')->distinct(true)->field('provice')->select();
        #银行列表
        $bank_list = M('MchBankList')->where(array('status' => 1))->order('id asc')->field('bnkcd,bnknm,ico')->select();
        $assign = array(
            'data'=>$list,
            'page'=>$show,
            'pro' => $pro,
            'bank_list' => $bank_list,
            'p'=>$p_data
        );
        $this->assign($assign);
        $this->display();
    }


    #联行号录入
    public function lbank_save(){
        $data=I('post.');
        if(!$data['address']){
            $this->error('请输入分行全称');
        }
        if(!$data['banking']){
            $this->error('请输入分行联行号');
        }
        if(!$data['bank']){
            $this->error('请选择银行');
        }
        if(!$data['pro']){
            $this->error('请选择省份');
        }
        if(!$data['city']){
            $this->error('请选择市级');
        }
        #判断是否存在联行号
        $db=M('BanksDataNew');
        $res=$db->where(array('banking'=>$data['banking']))->count();
        if($res){
            $rel=$db->where(array('banking'=>$data['banking']))->save($data);
        }else{
            $rel=$db->add($data);
        }
        if($rel){
            $this->success('联行号信息更新成功!');
        }else{
            $this->error('联行号信息更新失败');
        }

    }

    #品牌列表
    public function brand()
    {
        $Data = M('Domain_auth');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        #通道 只显示已开通的
        $api = M('MchAlleys')->where(array('status' => 1))->field('type,name')->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
            'api' => $api,
        );
        $this->assign($assign);
        $this->display();
    }


    #品牌新增
    public function add_brand()
    {
        $data = I('post.');
        $res = D('DomainAuth')->addData($data);
        if ($res) {
            $this->success('添加成功', U('brand'));
        } else {
            $error_data = D('DomainAuth')->getError();
            $this->error($error_data);
        }

    }


    #品牌编辑
    public function edit_brand()
    {
        if (IS_POST) {
            $data = I('post.');
            $map['id'] = $data['id'];
            unset($data['id']);
            $data['brand_ico'] = $data['ebrand_ico'];
            $data['brand_logo'] = $data['ebrand_logo'];
            //$data['channel_id']=ChannelId();
            //$data['channel_key']= ChannelKey();
            $res = M('Domain_auth')->where($map)->save($data);
            if ($res) {
                $this->success('编辑成功', U('brand'));
            } else {
                $this->error('编辑失败');
            }
        }
    }

    #品牌状态
    public function brand_status()
    {
        $id = I('post.id');
        if (I('post.status') == 'true') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = M('Domain_auth')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }


    }


    #品牌接口
    public function api()
    {
        $Data = M('DomainAlleys');
        $map['cid'] = I('get.id');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        #通道 只显示已开通的
        $api = M('MchAlleys')->where(array('status' => 1))->field('id,type,name')->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
            'api' => $api,
        );
        $this->assign($assign);
        $this->display();
    }

    #品牌通道添加
    public function add_alleys()
    {
        $data = I('post.', 'st');
        $rate = $data['rate'];
        if ($rate < 2 || $rate > 6) {
            $this->error('费率必须在2-6之间');
        }
        if (!$data['cid']) {
            $this->error('Cid ERROR');
        }
        if (!$data['rate']) {
            $this->error('Rate ERROR');
        }
        if (!$data['api']) {
            $this->error('Api ERROR');
        }
        $api = M('MchAlleys')->where(array('id' => $data['api']))->find();
        if ($data['make']) {
            $make = $data['make'];
        } else {
            $make = $api['data'];
        }
        $array = array(
            'cid' => $data['cid'],
            'alleys_id' => $api['id'],
            'rate' => $data['rate'],
            'alleys' => $api['name'],
            'alleys_type' => $api['type'],
            'time' => time(),
            'status' => 1,
            'make' => $make
        );

        $rel = M('DomainAlleys')->where(array('cid' => $data['cid'], 'alleys_type' => $api['type']))->find();
        if ($rel) {
            $res = M('DomainAlleys')->where(array('cid' => $data['cid'], 'alleys_type' => $api['type']))->save($array);
        } else {
            $res = M('DomainAlleys')->add($array);
        }
        if ($res) {
            $this->success('通道信息增加成功');
        } else {
            $this->error('通道信息增加失败');
        }
    }

    #品牌通道状态更新
    public function api_alleys_status()
    {
        $id = I('post.id');
        if (I('post.status') == 'true') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = M('DomainAlleys')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }


    }


    #品牌默认管理员
    public function brand_users()
    {
        $_P = I('post.');
        $codes = I('post.codes');
        $_auth = M('domain_auth')->where(array('web_authcode' => $codes))->count();
        if (I('post.type') == 'status') { //状态
            if ($_auth) { #有此品牌
                $res = M('users')->where(array('domain_auth' => $codes, 'is_sys' => 1))->find();
                if ($res) { #存在
                    $info = array(
                        'is_data' => 1,
                        'username' => $res['username'],
                        'email' => $res['email'],
                        'name' => $res['name'],
                        'phone' => $res['phone'],
                        'status' => $res['status'],
                        'codes' => $codes,
                    );
                } else {#不存在
                    $info = array(
                        'is_data' => 2,
                        'codes' => $codes,
                    );
                }
                $this->success($info);
            } else {
                $this->error('无此品牌信息!');
            }
        } else { //新增

            if ($_auth) { #有此品牌
                $res = M('users')->where(array('domain_auth' => $codes, 'is_sys' => 1))->find();
                if ($res) { #存在保存
                    $set = array(
                        'name' => $_P['name'],
                        'username' => $_P['username'],
                        'phone' => $_P['phone'],
                        'email' => $_P['email'],
                        'status' => $_P['status']
                    );
                    if (isset($_P['password'])) {
                        $set['password'] = md5($_P['password']);
                    }
                    $rel = M('users')->where(array('domain_auth' => $codes, 'is_sys' => 1))->save($set);
                    if ($rel) {
                        $this->success('管理信息保存成功');
                    } else {
                        $this->success('无操作!管理员信息保存失败!');
                    }

                } else {#不存在 可新增
                    $set = array(
                        'name' => $_P['name'],
                        'username' => $_P['username'],
                        'phone' => $_P['phone'],
                        'email' => $_P['email'],
                        'status' => $_P['status'],
                        'password' => md5($_P['password']),
                        'is_sys' => 1,
                        'domain_auth' => $codes,
                        'register_time' => time()
                    );

                    $rel = M('users')->add($set);
                    if ($rel) {
                        $this->success('管理信息增加成功');
                    } else {
                        $this->success('无操作!管理员信息增加失败!');
                    }
                }
            } else {
                $this->error('无此品牌信息!');
            }


        }
    }

}

?>
