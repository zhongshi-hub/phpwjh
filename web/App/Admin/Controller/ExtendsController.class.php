<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/*
 * 扩展模块
 * */

class ExtendsController extends AdminBaseController
{


    public function wxQwAdDataSet(){
        if(IS_POST) {
            $data = I('post.');
            $db = M('faceAd');
            $s=strtotime($data['start_time']);
            $e=strtotime($data['end_time']);
            $data['s_time']=$s;
            $data['e_time']=$e;
            if($e<$s){
                $this->error('结束时间不能小于开始时间');
            }
            if ($data['id']) {
                $map = array(
                    'id' => $data['id'],
                    'domain_auth' => domain_auth(),
                );
                $res=$db->where($map)->save($data);
            } else {
                $data['domain_auth']= domain_auth();
                $res=$db->add($data);
            }
            if($res){
                $this->success('广告信息提交成功', U('wxQwAdData'));
            }else{
                $this->error('广告信息提交失败');
            }
        }else {
            if(I('get.id')){
                $data=M('faceAd')->where(['id'=>I('get.id'),'domain_auth'=>domain_auth()])->find();
                if(!$data){
                    $this->error('非法操作');
                }
                $this->assign('data',$data);
            }
            $this->display();
        }
    }

    public function wxQwAdData(){
        $map['domain_auth'] = domain_auth();
        $Data = M('faceAd');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->where($map)->order('sort desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $arr=[];
        foreach ($list as $k=>$v){
            $isTime=ad_time($v['start_time'],$v['end_time']);
            $arr[]=[
                'id'=>$v['id'],
                'name'=>$v['name'],
                'type'=>$v['type'],
                'start_time'=>$v['start_time'],
                'end_time'=>$v['end_time'],
                'status'=>$v['status'],
                'sort'=>$v['sort'],
                'img'=>$v['img'],
                'is_time'=>$isTime?1:0
            ];
        }



        $assign = array(
            'data' => $arr,
            'page' => $show,
        );
        $this->assign($assign);
        //dump($assign);
        $this->display();
    }

    /**
     * 广告位删除
     */
    public function AdDataDel(){
        $id=I('get.id');
        if($id) {
            $map = [
                'domain_auth'=>domain_auth(),
                'id'=>$id
            ];
            $res=M('systemAd')->where($map)->delete();
            if($res){
                 $this->success('广告位信息删除成功', U('AdData'));
            }else{
                $this->error('广告位信息删除失败');
            }
        }else{
            $this->error('非法操作');
        }
    }

    #广告位管理
    public function AdData(){
        $map['domain_auth'] = domain_auth();
        $Data = M('systemAd');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $arr=[];
        foreach ($list as $k=>$v){
            $isTime=ad_time($v['start_time'],$v['end_time']);
            $arr[]=[
               'id'=>$v['id'],
               'name'=>$v['name'],
               'type'=>$v['type'],
               'start_time'=>$v['start_time'],
               'end_time'=>$v['end_time'],
               'status'=>$v['status'],
               'is_time'=>$isTime?1:0
            ];
        }



        $assign = array(
            'data' => $arr,
            'page' => $show,
        );
        $this->assign($assign);
        //dump($assign);
        $this->display();
    }

    #添加广告
    public function AdDataAdd(){
        if(IS_POST) {
            $data = I('post.');
            $db = M('systemAd');
            $s=strtotime($data['start_time']);
            $e=strtotime($data['end_time']);
            $data['s_time']=$s;
            $data['e_time']=$e;
            if($e<$s){
                $this->error('结束时间不能小于开始时间');
            }
            if ($data['id']) {
                $map = array(
                    'id' => $data['id'],
                    'domain_auth' => domain_auth(),
                );
                $res=$db->where($map)->save($data);
            } else {
                $data['domain_auth']= domain_auth();
                $res=$db->add($data);
            }
            if($res){
                $this->success('广告信息提交成功', U('AdData'));
            }else{
                $this->error('广告信息提交失败');
            }
        }else {
            if(I('get.id')){
                $data=M('systemAd')->where(['id'=>I('get.id'),'domain_auth'=>domain_auth()])->find();
                if(!$data){
                    $this->error('非法操作');
                }
                $this->assign('data',$data);
            }
            $this->display();
        }
    }

    #微信公众号列表
    public function weixin_list(){
        // $map['pid']=0;
        $map['domain_auth'] = domain_auth();
        $Data = M('Mch_weixin');
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


    #新增公众号
    public function weixin_adds(){
        $data = I('post.');
        C('TOKEN_ON',false);
        if($data['id']) {
            C('TOKEN_ON',false);
            $map = array(
                'id' => $data['id'],
                'domain_auth' => domain_auth(),
            );
            unset($data['id']);
            $res = D('MchWeixin')->editData($map, $data);
        }else{
            $res = D('MchWeixin')->addData($data);
        }


        if ($res) {
                $this->success('公众号信息保存成功', U('weixin_list'));
        } else {
            $error_data = D('MchWeixin')->getError();
            $this->error($error_data);
        }

    }

    #获取微信菜单
    public function weixin_menu(){
        // 实例微信菜单接口
        //$menu = & load_wechat('menu',4);
        // 实例微信粉丝接口
        $user = & load_wechat('User',4);
        $result = $user->getUserList($openid);

        //$result = $menu->getMenu();
        if($result===FALSE){
            // 接口失败的处理
            echo $menu->errMsg;
        }else{
            dump($result);
        }
    }
    

    #收款码代理分配
    public function allot_agent(){
        $data=I('post.');
        #代理是否存在
        $map['domain_auth']=domain_auth();
        $map['user_name|id']=$data['agent'];
        $agent=M('MchAgent')->where($map)->field('id,user_name')->find();
        if($agent){
            $where['id']=$data['id'];
            $where['domain_auth']=domain_auth();
            $res=M('MchCodes')->where($where)->save(array('aid'=>$agent['id']));
            if($res){
                $this->success('代理分配成功!所分代理:'.$agent['user_name']);
            }else{
                $this->error('代理分配失败!');
            }
        }else{
            $this->error('代理信息错误!未找到当前代理信息!');
        }
    }
    #收款码列表
    public function qrcode(){
        $p=I('param.');
        $maps['codes']=$p['codes']?array('like','%'.$p['codes'].'%'):array('neq','');
        if($p['aid']) {
            $maps['aid'] = $p['aid'];
        }
        #所属商户
        if($p['mid']) {
            $maps['mch_id'] = $p['mid'];
        }
        #所属门店
        if($p['store_id']) {
            $maps['store_id'] = $p['store_id'];
        }
        #绑定状态
        switch ($p['bind']){
            case 1:
                $maps['store_id']=array('EXP','IS NOT NULL');
                break;
            case 2:
                $maps['store_id']=array('EXP','IS NULL');
                break;
        }
        #码状态
        switch ($p['status']){
            case 1:
                $maps['status']=1;
                break;
            case 2:
                $maps['status']=0;
                break;
        }

        #代理状态
        switch ($p['agent_status']){
            case 1:
                $maps['aid']=array('neq','0');
                break;
            case 2:
                $maps['aid']=0;
                break;
        }


        $_count=$p['page_data']?$p['page_data']:10;

        $map['domain_auth']=domain_auth();
        $map['status']=1;
        $agent=M('Mch_agent')->where($map)->select();
        $maps['domain_auth'] = domain_auth();
        $Data = M('Mch_codes');
        $count = $Data->where($maps)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $_count);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($maps)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();


        $assign=array(
            'list'=>$list,
            'page'=>$show,
            'agent'=>$agent
        );
        $this->assign($assign);
        $this->display();
    }


    #生成收款码
    public function adds(){
        if(IS_POST) {
            $p=I('post.');
            //判断
            if(!$p['count']){$this->error('创建数量最低1个');}
            if($p['count']>100){
                $this->error('单次创建数量最大100个');
            }
			#代理ID 数量  品牌前缀  品牌授权码  任务ID
            $res=R('Tasks/QrCode/set',[$p['aid'],$p['count'],'P',domain_auth()]);
			$this->success("处理成功,本次收款码成功生成{$res}个");
        }
    }


    #
    public function detail(){
        if(IS_POST){
            $id=I('post.id');
            $map['domain_auth']=domain_auth();
            $map['id']=$id;
            $res=M('MchWeixin')->where($map)->find();
            if($res){
                unset($res['domain_auth']);
                $this->success($res);
            }else{
                $this->error('未获取到相关数据!');
            }

        }else{
            $this->error('非法操作');
        }
    }

    #导出收款码数据
    public function codesdown(){
        Vendor('Codesdown');
        if(empty($_REQUEST['code'])){
            $this->error('请选择要导出的二维码');
        }
        if($_REQUEST['type']=='exp'){//导出二维码
            $code= new  \Codesdown();
            $imgs=explode(',',$_REQUEST['code']);
            $zipname="Code_".date('YmdHis');
            $code->down($imgs,$zipname);
        }elseif($_REQUEST['type']=='expsucai') {//导出素材二维码
            #先判断是否这种上素材背景图
            if(!file_exists('./Source/QrBg/'.domain_auth().'.png')) {
                $this->error('未配置品牌背景素材,请联系服务专员配置!');
            }else {
                $code = new  \Codesdown();
                $imgs = explode(',', $_REQUEST['code']);
                if (count($imgs) > 100) {
                    $this->error('单次导出数量不能大于100个');
                } else {
                    $zipname = "Brand_" . date('YmdHis');
                    $code->downsucai($imgs, $zipname);
                }
            }
        }elseif ($_REQUEST['type']=='kadata'){
            $imgs=explode(',',$_REQUEST['code']);
            $xlsName  = "MchQrData_";//导出名称
            $xlsCell  = array(
                array('zurl','拼合链接'),
                array('url','收款码链接'),
                array('codes','收款码ID'),
                array('times','收款码创建时间'),

            );
            $atitle="本次制作卡牌数据生成时间:".date('Y-m-d H:i:s');
            $wbscms=array(
                'Atitle'=>$atitle,
            );

            //根据码取创建时间
            $codetime=M('MchCodes')->field('codes,ctime')->select();
            foreach($codetime as $key=>$val){
                $ctime[$val['codes']]=$val['ctime'];
            }
            unset($codetime);

            foreach ($imgs as $k => $v){
                $xlsData[$k]['zurl']=C('MA_DATA_URL')."/".$v;
                $xlsData[$k]['url']=C('MA_DATA_URL')."/";
                $xlsData[$k]['codes']=$v;
                $xlsData[$k]['times']=date('Y-m-d H:i:s',$ctime[$v]);
            }
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
        }
    }

}