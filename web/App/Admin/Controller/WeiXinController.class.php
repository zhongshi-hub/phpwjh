<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/*
 * 微信应用扩展模块
 * */

class WeiXinController extends AdminBaseController
{
    protected $MenuData;
    protected $MenuAuth;
    //protected $data;
    protected $MenuIsSys;
    protected $MchWx;
    protected $Media;
    public function _initialize()
    {
        parent::_initialize();
        #测试期间 只允许讯码付
        if(domain_auth()!='zUG7DegfCx'){
            $this->error('功能开发中...');
        }

        $rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $rule_name_s=MODULE_NAME.'/'.CONTROLLER_NAME;

        #权限菜单
        foreach ($this->MenuData as $k => $v) {
            #消灭
            if($this->MenuData[$k]['name']!='Admin/WeiXin/index'){
                unset($this->MenuData[$k]);
            }
            foreach ($this->MenuData[$k]['_data'] as $m => $n) {
                if($this->MenuIsSys==1){
                    if ($n['menu'] != 1) {
                            unset($this->MenuData[$k]['_data'][$m]);
                    }
                    foreach ($n['_data'] as $m1 => $n1) {
                        if ($n1['menu'] != 1) {
                            unset($this->MenuData[$k]['_data'][$m]['_data'][$m1]);
                        }
                    }
                }else {
                    if ($this->MenuAuth->check($n['name'], $_SESSION['user']['id'])) {
                        //然后菜单数据不显示的删除
                        if ($n['menu'] != 1) {
                            unset($this->MenuData[$k]['_data'][$m]);
                        }
                        foreach ($n['_data'] as $m1 => $n1) {
                            //没有权限的删除不显示
                            if (!$this->MenuAuth->check($n1['name'], $_SESSION['user']['id'])) {
                                unset($this->MenuData[$k]['_data'][$m1]);
                            }
                            //然后菜单数据不显示的删除
                            if ($n1['menu'] != 1) {
                                unset($this->MenuData[$k]['_data'][$m1]);
                            }
                        }
                    } else {
                        // 删除无权限的菜单
                        unset($this->MenuData[$k]['_data']);
                    }
                }
            }
        }
       $Menu=$this->MenuData[260]['_data'];
       // $_SESSION['Admin']['WxToken']= I('get.token');
       session('Admin.WxToken',I('get.token'));
       $token=$_SESSION['Admin']['WxToken'];
       #获取微信基本信息
       $this->MchWx=M('MchWeixin')->where(array('token'=>$token,'domain_auth'=>domain_auth()))->find();
       $this->Media = &  load_wechat('Media',$this->MchWx['id']);
       if(!$this->MchWx){
           $this->error('获取微信配置信息失败!请重新点击公众号管理项进入!',U('Extends/weixin_list'));
       }
       #当前品牌下绑定的微信列表
       $WxList=M('MchWeixin')->where(array('token'=>array('neq',$token),'domain_auth'=>domain_auth()))->select();

       $assign=array(
         'rule_name_s'=>$rule_name_s,
         'rule_name'=>$rule_name,
         'WxMeun'=>$Menu,
         'WxData'=>$this->MchWx,
         'WxList'=>$WxList
       );
       $this->assign($assign);
    }


    #微信应用首页
    public function index(){
         $this->display();
    }


    #自定义菜单
    public function MenuList(){
        $this->display();
    }

    #关键词自动回复
    public function KeyReply(){
        $this->display();
    }

    #被关注自动回复
    public function BeadReply(){
        $db=M('MchWeixinReply');
        if(IS_POST){
            $data=I('post.');
            $arr=array(
                'token'=>$this->MchWx['token'],
                'wid'=>$this->MchWx['id'],
                'type'=>$data['type'],
                'domain_auth'=>domain_auth(),
                'etime'=>time(),
                'status'=>$data['status'],
                'key_type'=>'Bead',
            );
            if($data['type']=='text'){
                if(!$data['basic']){
                    $this->error('您选择的回复类型为文本!请输入回复内容');
                }
                $arr['reply_text']=I('post.basic','','htmlspecialchars_decode');
            }else{
                if(!$data['image_url']){
                    $this->error('您选择的回复类型为图片!请上传回复图片');
                }
                $arr['reply_image_url']=$data['image_url'];
            }

            #判断是否存在 存在则直接保存
            $count=$db->where(array('wid'=>$this->MchWx['id'],'key_type'=>'Bead'))->count();
            if($count){
                $res=$db->where(array('wid'=>$this->MchWx['id'],'key_type'=>'Bead'))->save($arr);
            }else{
                $res=$db->add($arr);
            }
            if($res){
                $this->success('回复规则配置更新成功');
            }else{
                $this->error('回复规则配置更新失败');
            }
        }else {
            $res=$db->where(array('wid'=>$this->MchWx['id'],'key_type'=>'Bead'))->find();
            $this->assign('data',$res);
            $this->display();
        }
    }

    #无应答自动回复
    public function AutoReply(){
        $db=M('MchWeixinReply');
        if(IS_POST){
            $data=I('post.');
            $arr=array(
                'token'=>$this->MchWx['token'],
                'wid'=>$this->MchWx['id'],
                'type'=>$data['type'],
                'domain_auth'=>domain_auth(),
                'etime'=>time(),
                'status'=>$data['status'],
                'key_type'=>'Auto',
            );
            if($data['type']=='text'){
                if(!$data['basic']){
                    $this->error('您选择的回复类型为文本!请输入回复内容');
                }
                $arr['reply_text']=I('post.basic','','htmlspecialchars_decode');
            }else{
                if(!$data['image_url']){
                    $this->error('您选择的回复类型为图片!请上传回复图片');
                }
                $arr['reply_image_url']=$data['image_url'];
            }

            #判断是否存在 存在则直接保存
            $count=$db->where(array('wid'=>$this->MchWx['id'],'key_type'=>'Auto'))->count();
            if($count){
                $res=$db->where(array('wid'=>$this->MchWx['id'],'key_type'=>'Auto'))->save($arr);
            }else{
                $res=$db->add($arr);
            }
            if($res){
                $this->success('回复规则配置更新成功');
            }else{
                $this->error('回复规则配置更新失败');
            }
        }else {
            $res=$db->where(array('wid'=>$this->MchWx['id'],'key_type'=>'Auto'))->find();
            $this->assign('data',$res);
            $this->display();
        }
    }


    #新增图文
    /*public function Material_news(){
        $this->display();
    }*/





    #素材图片列表
    public function MaterialImage(){
        $map['wid']=$this->MchWx['id'];
        $map['domain_auth']=domain_auth();
        $Data = M('MchWeixinImage');
        $_list = $Data->where($map)->order('update_time desc')->group('media_id')->select();
        $list=array();
        foreach ($_list as $k=>$v){
            $list[]=array(
                'name'=>$v['name'],
                'media_id'=>$v['media_id'],
                'update_time'=>date('Y年m月d日',$v['update_time']),
                'url'=>'/WxImg/'.Xencode($v['url']),
            );
        }

        $count = count($list);
        $Page = new \Think\Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = array_slice($list, $Page->firstRow, $Page->listRows);
        #根据media_id汇总分组
        $assign=array(
            'data'=>$list,
            'page'=>$show
        );
        $this->assign($assign);
        $this->display();
    }

    #素材列表
    public function Material(){
        if(I('post.title')){
            $map['title|author|digest']=array('like','%'.I('post.title').'%');
        }
        $map['wid']=$this->MchWx['id'];
        $map['domain_auth']=domain_auth();
        $Data = M('MchWeixinNews');
        $_list = $Data->where($map)->order('create_time desc')->group('media_id')->select();
        $list=array();
        foreach ($_list as $k=>$v){
            #按照media_id汇总
            $map['media_id']=$v['media_id'];
            $media_id=$Data->where($map)->select();
            $item=array();
            foreach ($media_id as $val){
                $item[$val['displayorder']]=array(
                  'title'=>$val['title'],
                  'digest'=>$val['digest'],
                  'url'=>$val['url'],
                  'img'=>'/WxImg/'.Xencode($val['thumb_url']),
                );
            }
            $list[]=array(
                'media_id'=>$v['media_id'],
                'item'=>$item,
                'count'=>count($item),
                'create_time'=>date('Y年m月d日',$v['create_time']),
                'update_time'=>date('Y年m月d日',$v['update_time'])
            );
        }

        $count = count($list);
        $Page = new \Think\Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = array_slice($list, $Page->firstRow, $Page->listRows);
        #根据media_id汇总分组
        $assign=array(
            'data'=>$list,
            'page'=>$show
        );
        $this->assign($assign);
        $this->display();
    }


    #获取素材列表
    public function GetMaterial(){
        $type=I('post.type');
        $newsDb=M('MchWeixinNews');
        $_count=self::GetMaterialCount();
        $count=$_count['data'][$type.'_count'];
        $num=ceil($count/20);
        if($_count['status']==1){
            if($num!=0){
                if($type=='news') {
                    for ($i = 0; $i < $num; $i++) {
                        $start = $i * 20;
                        #图文列表存储
                        $result = $this->Media->getForeverList($type,$start, 20);
                        $newsDb->where(array('wid' =>$this->MchWx['id'], 'domain_auth' => domain_auth()))->delete();
                        foreach ($result['item'] as $k=>$v) {
                            foreach ($v['content']['news_item'] as $key => $val) {
                                $arr = array(
                                    'wid' => $this->MchWx['id'],
                                    'media_id' => $v['media_id'],
                                    'title' => $val['title'],
                                    'author' => $val['author'],
                                    'digest' => $val['digest'],
                                    'content' => $val['content'],
                                    'content_source_url' => $val['content_source_url'],
                                    'thumb_media_id' => $val['thumb_media_id'],
                                    'show_cover_pic' => $val['show_cover_pic'],
                                    'url' => $val['url'],
                                    'thumb_url' => $val['thumb_url'],
                                    'need_open_comment' => $val['need_open_comment'],
                                    'only_fans_can_comment' => $val['only_fans_can_comment'],
                                    'displayorder' => $key,
                                    'create_time' => $v['content']['create_time'],
                                    'update_time' => $v['content']['update_time'],
                                    'domain_auth' => domain_auth()
                                );
                                #判断系统是否存在,如果存在则直接更新
                                $count = $newsDb->where(array('thumb_media_id' => $arr['thumb_media_id'], 'domain_auth' => $arr['domain_auth']))->count();
                                if ($count) {
                                    $newsDb->where(array('thumb_media_id' => $arr['thumb_media_id'], 'domain_auth' => $arr['domain_auth']))->save($arr);
                                } else {
                                    $newsDb->add($arr);
                                }
                            }
                        }
                    }
                    $this->success('图文信息同步完成');
                }elseif($type=='image'){
                    for ($i = 0; $i < $num; $i++) {
                        $start = $i * 20;
                        #图文列表存储
                        $result = $this->Media->getForeverList($type, $start, 20);
                        #更新数据之前先清空数据
                        M('MchWeixinImage')->where(array('wid' =>$this->MchWx['id'], 'domain_auth' => domain_auth()))->delete();
                        foreach ($result['item'] as $k=>$v) {
                            $arr = array(
                                'wid' => $this->MchWx['id'],
                                'media_id' => $v['media_id'],
                                'name' => $v['name'],
                                'update_time' => $v['update_time'],
                                'url' => $v['url'],
                                'domain_auth' => domain_auth()
                            );
                            #判断系统是否存在,如果存在则直接更新
                            $count = M('MchWeixinImage')->where(array('media_id' => $arr['media_id'], 'domain_auth' => $arr['domain_auth']))->count();
                            if ($count) {
                                M('MchWeixinImage')->where(array('media_id' => $arr['media_id'], 'domain_auth' => $arr['domain_auth']))->save($arr);
                            } else {
                                M('MchWeixinImage')->add($arr);
                            }
                        }
                        $this->success('图片信息同步完成');
                    }
                     // dump($count);
                }else{

                }

            }else{
                $this->error('未找到微信同步资源!无需更新!');
            }
        }else{
            $this->error($_count['data']);
        }
    }


    #获取素材总数
    public function GetMaterialCount(){
        // 执行接口操作
        $result = $this->Media->getForeverCount();
        if($result===FALSE){
            // 接口失败的处理
           $rel=array('status'=>0,'data'=>$this->Media->errMsg);
        }else{
            // 接口成功的处理
           $rel=array('status'=>1,'data'=>$result);
        }
        return $rel;
    }



}