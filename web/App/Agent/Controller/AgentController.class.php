<?php
namespace Agent\Controller;
use Agent\Controller\InitBaseController;
class AgentController extends InitBaseController
{



    #代理分润明细
    public function benefit_detail(){
        $data=I('param.');
        #根据ID取出时间及代理信息筛选
        $map['id']=$data['id'];
        $map['domain_auth']=domain_auth();
        $agent=M('MchAgentFenrunDays')->where($map)->find();
        if($agent){
            #根据信息筛选
            //$aid=self::AgentAll($agent['agent']);
            $maps['agent']=$agent['agent'];
            //$maps['aid']=array('in',$aid);
            $maps['day']=$agent['day'];
            $maps['domain_auth']=domain_auth();
            $Data = M('MchAgentFenrunOrder');
            $count = $Data->where($maps)->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            $list = $Data->order('time_end desc')->where($maps)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $assign=array(
                'data'=>$list,
                'page'=>$show
            );

            $result=$Data->order('time_end desc')->where($maps)->select();
            if(!empty($data['export'])&&$data['export']=='ccl'){
                $xlsName  = "BeneFitDetail_";//导出名称
                $xlsCell  = array(
                    array('agent','所属代理'),
                    array('day','所属天分'),
                    array('aid','层次代理'),
                    array('store','所属门店'),
                    array('out_trade_no','订单号'),
                    array('time_end','交易时间'),
                    array('total','交易金额'),
                    array('alleys','所属通道'),
                    array('cost','所属代理成本'),
                    array('term','商户终端费率'),
                    array('money','分润金额'),
                );
                $atitle="分润明细报表生成时间:".date('Y-m-d H:i:s');
                $wbscms=array(
                    'Atitle'=>$atitle,
                );
                foreach ($result as $k => $v){
                    $store=Get_Store($v['store_id']); ;
                    $xlsData[$k]['agent']=agent_name($v['agent']);
                    $xlsData[$k]['day']=date('Y-m-d',$v['day']);
                    $xlsData[$k]['aid']=agent_name($v['aid']);
                    $xlsData[$k]['store']=$store['name'];
                    $xlsData[$k]['out_trade_no']="'".$v['out_trade_no'];
                    $xlsData[$k]['time_end']=date('Y-m-d H:i:s',$v['time_end']);
                    $xlsData[$k]['total']=$v['total'];
                    $xlsData[$k]['alleys']=alleys_name($v['alleys']);
                    $xlsData[$k]['cost']=$v['cost'].'‰';
                    $xlsData[$k]['term']=$v['term'].'‰';
                    $xlsData[$k]['money']=$v['money'];
                }
                $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
            }

            $this->assign($assign);
            $this->display();
        }else{
            $this->error('未找到相关数据!');
        }
    }

    #代理分润状态变更
    public function benefit_status(){
        $data=I('post.');
        switch ($data['status']){
            case 1:
                $save['status']=1;
                break;
            case 2:
                $save['status']=0;
                break;
            default:
                $this->error('操作参数非法');
                break;
        }
        $id=explode(',',$data['data']);
        if($id) {
            foreach ($id as $v) {
                $map['id'] = $v;
                $map['domain_auth'] = domain_auth();
                M('MchAgentFenrunDays')->where($map)->save($save);
            }

            $this->success('您提交的信息已更新处理!');
        }else{
            $this->error('未获取到选择的数据信息!');
        }
    }
    #代理分润汇总
    public function benefit_count(){
        $data=I('param.');
        $map['mon']=$data['mon']?$data['mon']:array('EXP','IS NOT NULL');
        $map['domain_auth'] = domain_auth();
        if(!$data['mon']) {
            $map['day'] = $data['day'] ? strtotime($data['day']) : strtotime(date('Ymd', strtotime("-1 day")));
        }
        $map['agent']=$data['aid']?$data['aid']:array('EXP','IS NOT NULL');
        $map['pid']=$data['pid']?$data['pid']:$_SESSION['agent']['id'];

        //dump($map);


        switch ($data['status']){
            case 1:
                $map['status']=1;
                break;
            case 2:
                $map['status']=0;
                break;
            default:
                $map['status']=array('neq',3);
                break;
        }


        $Data = M('MchAgentFenrunDays');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('day desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign=array(
            'data'=>$list,
            'page'=>$show
        );


        $result=$Data->order('id')->where($map)->select();



        if(!empty($data['export'])&&$data['export']=='ccl'){
            $xlsName  = "BeneFit_";//导出名称
            $xlsCell  = array(
                array('agent','代理姓名'),
                array('mon','所属月份'),
                array('day','所属天分'),
                array('count','交易笔数'),
                array('fee','交易金额'),
                array('userauth','认证数量'),
                array('authfees','认证返佣'),
                array('benefit','分润金额'),
                array('status','是否清算'),
            );
            $atitle="分润报表生成时间:".date('Y-m-d H:i:s');
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
                $xlsData[$k]['agent']=agent_name($v['agent']);
                $xlsData[$k]['mon']=$v['mon'];
                $xlsData[$k]['day']=date('Y-m-d',$v['day']);
                $xlsData[$k]['count']=$v['count'];
                $xlsData[$k]['fee']=$v['fee'];
                $xlsData[$k]['userauth']=$v['userauth'];
                $xlsData[$k]['authfees']=$v['authfees'];
                $xlsData[$k]['benefit']=$v['benefit'];
                $xlsData[$k]['status']=$status;
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }


        $this->assign($assign);
        $this->display();
    }

    #代理列表
    public function lists()
    {

        $p = I('param.');
        //$map['id']=array('in',self::AgentAll());
        $map['pid'] = $p['pid']?$p['pid']:$_SESSION['agent']['id'];

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
        } else {
            $map['status'] = 1;
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



        if(I('get.pid')=='0'){
            $this->error('参数非法');
        }
        // $map['pid']=0;
        $map['domain_auth'] = domain_auth();
        $Data = M('Mch_agent');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        #根据PID取上级授权品牌
        $pid=I('get.pid');

        $res=$Data->where(array('id'=>$pid,'domain_auth'=>domain_auth()))->find();
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
        if(!$this->is_agent){
            $this->error('您没有创建子代理的权限!');
        }else {
            $data = I('post.');
            if ($data['pid'] == 0) {
                $this->error('非法操作');
            }
            $res = D('MchAgent')->addData($data);
            if ($res) {
                if ($data['pid'] != $_SESSION['agent']['id']) {
                    $this->success('代理添加成功', U('lists', array('pid' => $data['pid'])));
                } else {
                    $this->success('代理添加成功', U('lists'));
                }
            } else {
                $error_data = D('MchAgent')->getError();
                $this->error($error_data);
            }
        }

    }

    #编辑代理
    public function edits()
    {
        if (IS_POST) {
            $data = I('post.');
            if($data['pid']==0){
                $this->error('非法操作');
            }
            
            $map['id'] = $data['id'];
            $map['domain_auth'] = domain_auth();
            unset($data['id']);
            #判断是否重复
            $res = M('Mch_agent')->where($map)->find();
            if ($data['user_phone'] != $res['user_phone']) {
                $count = M('Mch_agent')->where(array('domain_auth' => domain_auth(), 'user_phone' => $data['user_phone']))->count();
                if ($count) {
                    $this->error('联系电话已存在');
                }
            }

            $res = M('Mch_agent')->where($map)->save($data);
            if ($res) {
                if ($data['pid'] != $_SESSION['agent']['id']) {
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
        $map['domain_auth'] = domain_auth();
        $map['id'] = $data['id'];
        $res = M('Mch_agent')->where($map)->field('domain_auth', true)->find();
        if ($res) {
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
        $_where['domain_auth']=domain_auth();
        $rel=M('MchAgent')->where($_where)->find();
        if(!$rel){
            $this->error('非法操作');
        }



        $where['cid']=domain_id();
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
        $where['domain_auth']=domain_auth();
        $res=M('MchAgent')->where($where)->save($save);
        if($res){
            $this->success('费率更新成功');
        }else{
            $this->error('费率更新失败');
        }
    }



    #代理分润汇总
    /*public function benefit_count(){
        $this->display();
    }*/

    #代理扩展
    public function dialog(){
        if(IS_POST){
            $data['id']=array('in',self::AgentAll());
            $data['user_name|user_phone']=array('like','%'.I('post.Search').'%');
            $data['domain_auth']=domain_auth();
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