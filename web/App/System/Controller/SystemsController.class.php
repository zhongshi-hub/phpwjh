<?php
namespace System\Controller;
use Common\Controller\SystemBaseController;
/**
 * 后台首页控制器
 */
class SystemsController extends SystemBaseController{



    
    #通道配置
    public function Api_way(){

        $this->display();
    }

    #平台分润
    public function system_count(){
        $data=I('param.');
        $map['mon']=$data['mon']?$data['mon']:array('EXP','IS NOT NULL');
        /*if(!$data['mon']) {
            $map['day'] = $data['day'] ? strtotime($data['day']) : strtotime(date('Ymd', strtotime("-1 day")));
        }*/
        $map['alleys']=$data['alleys']?$data['alleys']:array('EXP','IS NOT NULL');
        $Data = M('SystemFenrunDays');
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
            $xlsName  = "System_BeneFit_";//导出名称
            $xlsCell  = array(
                array('agent','所属通道'),
                array('mon','所属月份'),
                array('day','所属天分'),
                array('count','交易笔数'),
                array('fee','交易金额'),
                array('benefit','分润金额'),
            );
            $atitle="平台分润报表生成时间:".date('Y-m-d H:i:s');
            $wbscms=array(
                'Atitle'=>$atitle,
            );
            foreach ($result as $k => $v){
                $xlsData[$k]['agent']=alleys_name($v['alleys']);
                $xlsData[$k]['mon']=$v['mon'];
                $xlsData[$k]['day']=date('Y-m-d',$v['day']);
                $xlsData[$k]['count']=$v['count'];
                $xlsData[$k]['fee']=$v['fee'];
                $xlsData[$k]['benefit']=$v['benefit'];
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }


        $this->assign($assign);
        $this->display();
    }

    #合作伙伴分润明细
    public function system_detail(){
        $data=I('param.');
        #根据ID取出时间及代理信息筛选
        $map['id']=$data['id'];
        $agent=M('SystemFenrunDays')->where($map)->find();
        if($agent){
            #根据信息筛选
            $maps['day']=$agent['day'];
            $maps['alleys']=$agent['alleys'];
            $Data = M('SystemFenrunOrder');
            $count = $Data->where($maps)->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            $list = $Data->order('time_end desc')->where($maps)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $assign=array(
                'data'=>$list,
                'page'=>$show
            );

            /*$result=$Data->order('time_end desc')->where($maps)->select();
            if(!empty($data['export'])&&$data['export']=='ccl'){
                $xlsName  = "Partner_BeneFitDetail_";//导出名称
                $xlsCell  = array(
                    array('agent','所属品牌'),
                    array('day','所属天分'),
                    array('aid','层次代理'),
                    array('store','所属门店'),
                    array('out_trade_no','订单号'),
                    array('time_end','交易时间'),
                    array('total','交易金额'),
                    array('alleys','所属通道'),
                    array('cost','通道成本'),
                    array('term','商户终端费率'),
                    array('money','分润金额'),
                );
                $atitle="分润明细报表生成时间:".date('Y-m-d H:i:s');
                $wbscms=array(
                    'Atitle'=>$atitle,
                );
                foreach ($result as $k => $v){
                    $store=Get_Store($v['store_id']); ;
                    $xlsData[$k]['agent']=DomainName($v['domain_auth']);
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
            }*/

            $this->assign($assign);
            $this->display();
        }else{
            $this->error('未找到相关数据!');
        }
    }

    #合作伙伴 分润
    public function benefit_count(){
        $data=I('param.');
        $map['mon']=$data['mon']?$data['mon']:array('EXP','IS NOT NULL');
        if(!$data['mon']) {
            $map['day'] = $data['day'] ? strtotime($data['day']) : strtotime(date('Ymd', strtotime("-1 day")));
        }
        $map['domain_auth']=$data['domain_auth']?$data['domain_auth']:array('EXP','IS NOT NULL');

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


        $Data = M('PartnerFenrunDays');
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
            $xlsName  = "Partner_BeneFit_";//导出名称
            $xlsCell  = array(
                array('agent','所属品牌'),
                array('mon','所属月份'),
                array('day','所属天分'),
                array('count','交易笔数'),
                array('fee','交易金额'),
                array('benefit','分润金额'),
                array('status','是否清算'),
            );
            $atitle="合作伙伴分润报表生成时间:".date('Y-m-d H:i:s');
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
                $xlsData[$k]['agent']=DomainName($v['domain_auth']);
                $xlsData[$k]['mon']=$v['mon'];
                $xlsData[$k]['day']=date('Y-m-d',$v['day']);
                $xlsData[$k]['count']=$v['count'];
                $xlsData[$k]['fee']=$v['fee'];
                $xlsData[$k]['benefit']=$v['benefit'];
                $xlsData[$k]['status']=$status;
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }


        $this->assign($assign);
        $this->display();
    }

    #合作伙伴分润状态变更
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
                M('PartnerFenrunDays')->where($map)->save($save);
            }

            $this->success('您提交的信息已更新处理!');
        }else{
            $this->error('未获取到选择的数据信息!');
        }
    }

    #合作伙伴分润明细
    public function benefit_detail(){
        $data=I('param.');
        #根据ID取出时间及代理信息筛选
        $map['id']=$data['id'];
        $agent=M('PartnerFenrunDays')->where($map)->find();
        if($agent){
            #根据信息筛选
            $maps['day']=$agent['day'];
            $maps['domain_auth']=$agent['domain_auth'];
            $Data = M('PartnerFenrunOrder');
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
                $xlsName  = "Partner_BeneFitDetail_";//导出名称
                $xlsCell  = array(
                    array('agent','所属品牌'),
                    array('day','所属天分'),
                    array('aid','层次代理'),
                    array('seller','所属商户'),
                    array('store','所属门店'),
                    array('out_trade_no','订单号'),
                    array('time_end','交易时间'),
                    array('total','交易金额'),
                    array('alleys','所属通道'),
                    array('cost','通道成本'),
                    array('term','商户终端费率'),
                    array('money','分润金额'),
                );
                $atitle="分润明细报表生成时间:".date('Y-m-d H:i:s');
                $wbscms=array(
                    'Atitle'=>$atitle,
                );
                foreach ($result as $k => $v){
                    $store=Get_Store($v['store_id']);
                    $_seller=Get_Seller($v['mid']);
                    $xlsData[$k]['agent']=DomainName($v['domain_auth']);
                    $xlsData[$k]['day']=date('Y-m-d',$v['day']);
                    $xlsData[$k]['aid']=agent_name($v['aid']);
                    $xlsData[$k]['seller']=$_seller['mch_name'];
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


    #品牌分析
    public function statics(){
        $this->display();
    }


}