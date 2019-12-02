<?php
namespace Mch\Controller;
use Mch\Controller\StoreInitBaseController;
class StoreController extends StoreInitBaseController {

    #门店列表
    public function lists(){
        if(IS_POST){
            $data=I('post.');
            #session结果
            //dump($_SESSION);
            $_SESSION['store'] = array(
                'sid' => $data['sid'],
                'store_id' => $data['store_id'],
                'user_tel'=>$_SESSION['store']['user_tel'],
            );
            $this->success('门店切换成功', U('Store/index'));
        }else {
            #根据session的手机号取列表
            $map['phone'] = $_SESSION['store']['user_tel'];
            $map['domain_auth'] = domain_auth();
            $list = M('MchStoreUser')->where($map)->field('store_id,sid')->distinct(true)->select();
            $assign = array(
                'list' => $list
            );

            //dump($list);
            $this->assign($assign);
            $this->display();
        }
    }


    #门店首页
    public function index(){
        //dump($_SESSION);
        $session=$_SESSION['store'];
        #根据门店信息判断
        $_map['id'] = $session['sid'];
        $_map['domain_auth'] = domain_auth();
        $seller=M('MchSeller')->where($_map)->find();
        if(!$seller){
            $this->error('获取主商户信息失败!',U('Login/store_quit'));
        }
        $maps['id'] = $session['store_id'];
        $maps['domain_auth'] = domain_auth();
        $store=M('MchStore')->where($maps)->find();
        if(!$store){
            $this->error('获取门店信息失败',U('Login/store_quit'));
        }
        #店员信息
        $w['store_id'] = $session['store_id'];
        $w['domain_auth'] = domain_auth();
        $w['phone']=$session['user_tel'];
        $user_name=M('MchStoreUser')->where($w)->getField('username');

        $db=M('MchOrders');
        $map['status']=1;
        $map['mid']=$session['sid'];
        $map['store_id']=$session['store_id'];
        $map['domain_auth']=domain_auth();
        #统计今日总额
        $STimes=date("Y-m-d");
        $ETimes=date('Y-m-d',strtotime("1 day"));
        $wheres['_string'] = "(`time_end`> '" . strtotime($STimes) . "') AND ( `time_end` < '" . strtotime($ETimes) . "') ";
        $DSum=$db->where($map)->where($wheres)->sum('total_fee');
        #今日笔数
        $D_count=$db->where($map)->where($wheres)->count();

        #统计昨日总额
        $STime=date("Y-m-d",strtotime("-1 day"));
        $ETime=date('Y-m-d');
        $where['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        #统计昨日总交易额
        $GoSum=$db->where($map)->where($where)->sum('total_fee');
        #统计昨日总笔数
        $GoCount=$db->where($map)->where($where)->count();

        $assign=array(
            'G_sum'=>$GoSum,
            'G_count'=>$GoCount,
            'D_sum'=>$DSum,
            'D_count'=>$D_count,
            'user_name'=>$user_name,
            'seller'=>$seller,
            'store'=>$store
        );
        $this->assign($assign);
        $this->display();
    }


    #流水列表
    public function order(){
        /*$data=I('param.');
        $session=$_SESSION['store'];
        $db=M('MchOrders');
        $STime=$data['stime']?$data['stime']:'';
        $ETime=$data['etime']?$data['etime']:date('Y-m-d',strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        $map['service']=$data['pay_type']?$data['pay_type'].'_jsapi':array('EXP','IS NOT NULL');
        $map['out_trade_no']=$data['out_trade_no']?array('like','%'.$data['out_trade_no'].'%'):array('EXP','IS NOT NULL');

        $map['status']=1;
        $map['mid']=$session['sid'];
        $map['store_id']=$session['store_id'];
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
        /*if(IS_POST){
            $data=I('param.');
            $session=$_SESSION['store'];
            $db=M('MchOrders');
            $STime=$data['stime']?$data['stime']:'';
            $ETime=$data['etime']?$data['etime']:date('Y-m-d',strtotime("1 day"));
            $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            $map['service']=$data['pay_type']?$data['pay_type'].'_jsapi':array('EXP','IS NOT NULL');
            $map['out_trade_no']=$data['out_trade_no']?array('like','%'.$data['out_trade_no'].'%'):array('EXP','IS NOT NULL');

            $map['status']=1;
            $map['mid']=$session['sid'];
            $map['store_id']=$session['store_id'];
            $map['domain_auth']=domain_auth();
            $count = $db->where($map)->count();// 查询满足要求的总记录数
            $Page = new \Think\Mpage($count, 5);// 实例化分页类 传入总记录数和每页显示的记录数
            $list = $db->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->field('service,out_trade_no,store_id,total_fee,createtime')->select();
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
                $rel['createtime']=date('Y/m/d H:i:s',$v['createtime']);
                $rel['out_end']=substr($v['out_trade_no'],-4);
                $res[]=$rel;
            }

            $_data=array(
                'pages'=>$Page->totalPages,
                'data'=>$res,
            );
            echo json_encode($_data);
            exit;
        }else {
            $this->display();
        }*/
        $this->display();
    }


    #流水列表
    public function order_data_json(){
        $db=M('MchOrders');
        $session=$_SESSION['store'];

        $data=I('param.');

        $STime=$data['stime']?$data['stime']:'';
        $ETime=$data['etime']?$data['etime']:date('Y-m-d',strtotime("1 day"));
        $map['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
        $map['service']=$data['pay_type']?$data['pay_type'].'_jsapi':array('EXP','IS NOT NULL');
        $map['out_trade_no']=$data['out_trade_no']?array('like','%'.$data['out_trade_no'].'%'):array('EXP','IS NOT NULL');

        $map['status']=1;
        $map['mid']=$session['sid'];
        $map['store_id']=$session['store_id'];
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

    }

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
            }elseif ($v['service'] == 'card_api'){
                $img = '/Source/statics/yl.png';
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


    #我的
    public function my(){
        $session=$_SESSION['store'];
        $map['sid']=$session['sid'];
        $map['phone']=$session['user_tel'];
        $map['store_id']=$session['store_id'];
        $map['domain_auth']=domain_auth();
        $User=M('MchStoreUser')->where($map)->find();
        #取此手机号吗名下有多少个店
        $maps['phone']=$session['user_tel'];
        $maps['domain_auth']=domain_auth();
        $count=M('MchStoreUser')->where($maps)->field('store_id')->distinct(true)->select();

        $assign=array(
            'user'=>$User,
            'count'=>count($count),
        );
        $this->assign($assign);
        $this->display();
    }


    #订单详细页面
    public function trade_data(){
        $session=$_SESSION['store'];
        $map['out_trade_no']=I('get.order_id');
        $map['mid']=$session['sid'];
        $map['store_id']=$session['store_id'];
        $map['domain_auth']=domain_auth();
        $db=M('MchOrders')->where($map)->find();
        #打印机状态
        $print=M('MchStorePrint')->where(array('sid'=>$db['mid'],'store_id'=>$db['store_id'],'domain_auth'=>domain_auth()))->getField('status');


        if(!$db){
            $this->error('您输入的流水订单号不存在!');
        }
        $this->assign('data',$db);
        $this->assign('print',$print);
        $this->display();

    }

    #流水筛选
    public function order_data(){
        $db=M('MchOrders');
        $session=$_SESSION['store'];
        $map['status']=1;
        $map['mid']=$session['sid'];
        $map['store_id']=$session['store_id'];
        $map['domain_auth']=domain_auth();
        $min=$db->where($map)->min('time_end');
        $assign=array(
            'min'=>date('Y-m-d',$min),
            'max'=>date('Y-m-d',strtotime('1 day')),
        );
        $this->assign($assign);
        $this->display();
    }


    #订单补打中
    public function lasts_print(){
        $order_id=I('post.order_id');
        $session=$_SESSION['store'];
        #防止补打传单,先判断
        $map['out_trade_no']=$order_id;
        $map['domain_auth']=domain_auth();
        $map['mid']=$session['sid'];
        $map['store_id']=$session['store_id'];
        $res=M('MchOrders')->where($map)->count();
        if($res){
            R('Pays/Mch/late_print',array('order_id'=>$order_id));
        }else{
            $this->error('未获取到订单信息,无法处理补打请求!');
        }
    }



}