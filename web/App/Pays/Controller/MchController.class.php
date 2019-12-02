<?php
namespace Pays\Controller;
use Pays\Controller\InitBaseController;

class MchController extends InitBaseController {

    #
    public function index(){

    }


    #二维码
    public function QrData(){
        $url=I('get.url');
        Vendor('qrcode');
        $QrCode = new \QRcode();
        $errorCorrectionLevel =3 ;//容错级别
        $matrixPointSize = 5;//生成图片大小
        $QrCode->png(Xdecode($url), false, $errorCorrectionLevel, $matrixPointSize, 2);
        header('Content-type: image/png');
    }


    #店员绑定
    public function store_user_bind(){
        $data=I('get.');
        $mch_wxid=M('mch_pay_config')->where(array('domain_auth'=>domain_auth()))->getField('mch_wxid');
        $where['id']=$mch_wxid;
        $where['domain_auth']=domain_auth();
        $WeiXin=M('MchWeixin')->where($where)->field('name,qrc_img')->find();

        #获取门店信息
       // dump($data);
        if(!isset($data['status'])) {
            $Store=M('MchStore')->where(array('id'=>$data['store_id'],'sid'=>$data['id'],'domain_auth'=>domain_auth()))->find();
            unset($Store['domain_auth']);
            if(!$Store){
              $this->error('未找到此门店','',888);
            }
            $_data = $this->_oauth('base_info');
            if (!$_data) {
                $this->error('扫码失效,请重新扫码!', '', 888);
            }
            $User=M('MchStoreUser')->where(array('store_id'=>$data['store_id'],'sid'=>$data['id'],'wx_openid'=>$_data['openid'],'domain_auth'=>domain_auth()))->find();
            if($User){
                redirect(U('store_user_bind',array('uid'=>$User['id'],'status'=>'store_status')));
            }
        }else{
            $_User=M('MchStoreUser')->where(array('id'=>$data['uid'],'domain_auth'=>domain_auth()))->find();
            $USER=M('MchStore')->where(array('sid'=>$_User['sid'],'store_id'=>$_User['store_id'],'domain_auth'=>domain_auth()))->find();
        }
        $assign=array(
            'weixin'=>$WeiXin,
            'info'=>$_data,
            'store'=>$Store,
            'user'=>$USER,
        );



        $this->assign($assign);
        $this->display();
    }

    #店员绑定结果
    public  function  store_user_save(){
        $data=I('post.');
        if(empty($data['wx_openid'])){
            $this->error('获取微信信息失败!请重新扫码绑定!');
        }
        if(empty($data['username'])){$this->error('请输入姓名');}
        if(empty($data['phone'])){$this->error('请输入手机号码');}
        #判断是否存在此手机号的用户 取密码
        $where['phone']=$data['phone'];
        $where['domain_auth']=domain_auth();
        $pass=M('MchStoreUser')->where($where)->getField('password');


        $arr=array(
          'sid'=>$data['sid'],
          'store_id'=>$data['store_id'],
          'username'=>$data['username'],
          'phone'=>$data['phone'],
          'wx_name'=>$data['wx_name'],
          'wx_openid'=>$data['wx_openid'],
          'wx_imgurl'=>$data['wx_imgurl'],
          'status'=>1,
          'domain_auth'=>domain_auth(),
          'role_wx_temp'=>1,
          'role_order'=>1,
          'ctime'=>time(),
          'password'=>$pass,
        );
        $map['sid']=$data['sid'];
        $map['store_id']=$data['store_id'];
        $map['wx_openid']=$data['wx_openid'];
        $map['domain_auth']=domain_auth();
        $db=M('MchStoreUser');
        $res=$db->where($map)->find();
        if($res){
            $rel=$db->where($map)->save($arr);
        }else{
            $rel=$db->add($arr);
        }
        if($rel){
            $this->success('加入门店成功',U('store_user_bind',array('id'=>$data['sid'],'store_id'=>$data['store_id'],'uid'=>$rel,'status'=>'store_status')));
        }else{
            $this->error('店员加入门店失败!');
        }
    }


    #补打订单
    public function late_print(){
        $order_id=I('param.order_id');
        #根据订单号取门店打印机配置
        $where['out_trade_no']=$order_id;
        $where['status']=1;
        $Store=M('MchOrders')->where($where)->field('mid,store_id,service,transaction_id,out_trade_no,total_fee,createtime,status')->find();
        #根据信息取打印配置
        $print=M('MchStorePrint')->where(array('sid'=>$Store['mid'],'store_id'=>$Store['store_id']))->find();
        #打印机配置项
        $config=unserialize($print['data']);
        if($print['status']==1) {
            #只有打印机开启的时候打印
            Vendor('print');
            $print = new \Yprint();
            #打印模板 开始
            $print_top= $config['print_top'];//门头
            $print_sh= $config['print_mchname'];//收款商户
            $print_bottom= str_replace("||","\r\n",$config['print_footer']) ;//底部显示内容
            if($config['print_num']>1){
                $msg.="<MN>".$config['print_num']."</MN>";
                $msg.= '<center>@@2'.$print_top.'</center>\r\n';
            }else{
                $msg.= '<center>@@2'.$print_top.'</center>\r\n';
            }
            $msg.= '※※※※※※※※※※※※※※※※\r\n\r\n'; //间隔
            $msg.= '收款商户：'.$print_sh.'\r\n'; //打印内容
            $msg.= '支付方式：'.pays_type($Store['service'],1).'\r\n';
            $msg.= '支付结果：'.pays_status($Store['status'],1).'\r\n';
            $msg.= '支付时间: '.date('Y-m-d H:i:s',$Store['createtime']).'\r\n\r\n'; //打印内容
            $msg.= '-----------订单详情-------------\r\n\r\n'; //打印内容
            $msg.= '商户单号: \r\n'; //打印内容
            $msg.= ''.$Store['out_trade_no'].'\r\n'; //打印内容
            if(!empty($Store['transaction_id'])) {
                $msg .= '交易单号: \r\n'; //打印内容
                $msg .= '' . $Store['transaction_id'] . '\r\n'; //打印内容
            }
            $msg.= '支付总金额: \r\n'; //打印内容
            $msg.= '<center>@@2'.$Store['total_fee'].'元</center>\r\n'; //打印内容
            $msg.='\r\n'.$print_bottom.'\r\n';
            $msg.='\r\n@@2注:此订单为补打订单\r\n';

            #打印模板 结束
            $content = urlencode($msg);
            $apiKey = $config['print_api'];
            $msign = $config['print_key'];
            $res = $print->action_print($config['print_id'],$config['print_zd'], $content, $apiKey, $msign);
            $res=json_decode($res,true);
            switch ($res['state']){
                case 1:
                    $ret='打印指令发送成功';
                    break;
                case 2:
                    $ret='提交时间超时';
                    break;
                case 3:
                    $ret='参数有误';
                    break;
                case 4:
                    $ret='sign验证失败';
                    break;
                default:
                    $ret='未知状态!提示:'.serialize($res);
                    break;
            }
        }else{
            $ret='未开启打印功能';
        }

        $this->success($ret);
    }


}
