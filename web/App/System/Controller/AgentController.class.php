<?php

namespace System\Controller;
use Common\Controller\SystemBaseController;

class AgentController extends SystemBaseController
{

    #代理列表
    public function lists()
    {
        $p = I('param.');
        #详细筛选
        if ($p['search_val']) {
            $map['user_name|user_phone'] = array('like', '%' . $p['search_val'] . '%');
        }
        #省份筛选
        if ($p['sprovince']) {
            $map['province'] = $p['sprovince'];
        }
        #城市筛选
        if ($p['scity']) {
            $map['city'] = $p['scity'];
        }
        #县级筛选
        if ($p['sdistrict']) {
            $map['district'] = $p['sdistrict'];
        }
        #状态筛选
        if ($p['status']) {
            switch ($p['status']) {
                case 1:
                    $map['status'] = 1;
                    break;
                case 2:
                    $map['status'] = 0;
                    break;
                case 3:
                    break;
            }
        }

        #认证费用
        if ($p['auth_status']) {
            switch ($p['auth_status']) {
                case 1:
                    $map['auth_status'] = 1;
                    break;
                case 2:
                    $map['auth_status'] = 0;
                    break;
                case 3:
                    break;
            }
        }
        #PID
        if ($p['pid']) {
            $map['pid'] = $p['pid'];
        } else {
            $map['pid'] = 0;
        }
        if(I('get.pid')=='0'){
            $this->error('参数非法');
        }
        $map['domain_auth']=I('param.domain_auth')?I('param.domain_auth'):array('neq','');
        $Data = M('Mch_agent');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        #根据PID取上级授权品牌
        $pid=I('get.pid');
        $res=$Data->where(array('id'=>$pid))->find();
        if($pid){
            if(!$res){
                $this->error('无法获取上级代理信息!');
            }
            $DoData=$res['domain_auth'];
        }


        $assign = array(
            'dodata'=>$DoData,
            'data' => $list,
            'page' => $show
        );
        $this->assign($assign);
        $this->display();
    }


    #添加代理
    public function adds()
    {
        $data = I('post.');
        $data['ctime']=time();
        $res = M('MchAgent')->add($data);
        if ($res) {
            if ($data['pid'] != 0) {
                $this->success('代理添加成功', U('lists', array('pid' => $data['pid'])));
            } else {
                $this->success('代理添加成功', U('lists'));
            }
        } else {
            $error_data = D('MchAgent')->getError();
            $this->error($error_data);
        }

    }


    #编辑代理
    public function edits()
    {
        if (IS_POST) {
            $data = I('post.');
            $map['id'] = $data['id'];
            unset($data['id']);
            #判断是否重复
            $res = M('Mch_agent')->where($map)->find();
            if ($data['user_phone'] != $res['user_phone']) {
                $count = M('Mch_agent')->where(array('user_phone' => $data['user_phone']))->count();
                if ($count) {
                    $this->error('联系电话已存在');
                }
            }
            $res = M('Mch_agent')->where($map)->save($data);
            if ($res) {
                if ($data['pid'] != 0) {
                    $this->success('信息编辑成功', U('lists', array('pid' => $data['pid'])));
                } else {
                    $this->success('信息编辑成功', U('lists'));
                }
            } else {
                $this->error('信息编辑失败');
            }
        }
    }


    #代理信息详情
    public function detail()
    {
        $data = I('post.');
        $map['id'] = $data['id'];
        $res = M('Mch_agent')->where($map)->find();
        $domain_auth=$res['domain_auth'];
        unset($res['domain_auth']);
        if ($res) {
            $res['domain_auth']=DomainName($domain_auth);
            $this->success($res);
        } else {
            $this->error('未找到当前代理详细信息');
        }
    }


    #代理层次
    public function treeData()
    {

        $id=I('get.id');
        $map['domain_auth'] = domain_auth();
        $data =M('Mch_agent')->where($map)->field('id,pid,user_name,user_phone,status')->select();
        $_data=\Org\Nx\Data::channelLevel($data,0,'&nbsp;','id');
        $_datas[]=$_data[$id];
        self::tree_Data($_datas);
    }


    public function tree_Data($array)
    {
        if (empty($array)) {
            return;
        }
        echo '<ul>';
        foreach ($array as $key=>$v) {

            echo '<li>' . $v['user_name'];
            self::tree_Data($v['_data']);
            echo '</li>';
        }
        echo '</ul>';
    }


    #代理通道费率配置
    public function A_Rate(){
        $id=I('get.id');
        $pid=I('get.pid');
        $_where['id']=$id;
        $rel=M('MchAgent')->where($_where)->find();
        if(!$rel){
            $this->error('非法操作');
        }

        $where['cid']=domain_id($rel['domain_auth']);
        $where['status']=1;
        $api=M('DomainAlleys')->where($where)->field('alleys,alleys_type,rate')->select();

        $_agent=M('MchAgent')->field('id,rate')->order('id asc')->select();
        foreach($_agent as $key=>$val){
            $agent[$val['id']]=unserialize($val['rate']);
        }
        unset($_agent);

        $rate=$agent[$id];
        $assign=array(
            'api'=>$api,
            'rate'=>$rate,
            'pid'=>$pid,
        );

        //dump($rate['Sftpay_cost']);
        $this->assign($assign);
        $this->display();
    }


    #保存代理费率
    public  function A_rata_save(){
        $data=I('post.');
        $aid=$data['aid'];
        if(!$data['aid']){$this->error('信息非法');}
        unset($data['token']);
        unset($data['aid']);
        $save['rate']=serialize($data);
        $where['id']=$aid;
        $res=M('MchAgent')->where($where)->save($save);
        if($res){
            $this->success('费率更新成功');
        }else{
            $this->error('费率更新失败');
        }
    }



    #代理分润汇总
    public function benefit_count(){
        $this->display();
    }

    #代理扩展
    public function dialog(){
        if(IS_POST){
            $data['user_name|user_phone']=array('like','%'.I('post.Search').'%');
            $data['domain_auth']=I('post.domain_auth');
            $count=M('Mch_agent')->where($data)->count();
            $res=M('Mch_agent')->where($data)->field('id,user_name,user_phone')->select();
            foreach ($res as $key=>$val){
                $_data[]='{"id":"'.$val['id'].'","name":"'.$val['user_name'].'","phone":"'.$val['user_phone'].'"}';
            }
            $sum_data="[".implode(',', $_data).']';
            $json='{"total":'.$count.',"rows":'.$sum_data.'}';
            if($count) {
                die($json);
            }else{
                $this->error('未找到信息');
            }
        }else {
            $this->display();
        }
    }


}

?>