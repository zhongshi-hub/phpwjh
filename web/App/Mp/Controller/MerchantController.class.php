<?php
namespace Mp\Controller;
use Mp\Controller\BaseController;
/**
 * Base基类控制器
 */
class MerchantController extends BaseController{



    public function speakerSave()
    {
        $data = I('post.');
        $db=M('StoreSpeaker');
        unset($data['__TokenHash__']);
        if (!$data['sid']) {
            $this->error('操作非法!');
        }
        $data['mid']=session('mp.id');
        $data['uptime']=time();
        $data['domain_auth']=domain_auth();
        #判断信息是否有
        $where['mid'] = session('mp.id');
        $where['sid'] = $data['sid'];
        $where['domain_auth'] = domain_auth();
        $rel =$db->where($where)->count();
        //云喇叭接入
        if($data['appid']){
             if(!$data['appkey']){
                 $this->error('请配置appkey');
             }
            $ylb = $this->ylb($data['vid'], false,1,$data['appid'],$data['appkey']);
        }else {
            $ylb = $this->ylb($data['vid'], false);
        }
        if($ylb['status']==1) {
            if ($rel) {
                $res = $db->where($where)->save($data);
            } else {
                $res = $db->add($data);
            }
            if ($res) {
                $this->success('配置信息更新成功');
            } else {
                $this->error('配置信息更新失败');
            }
        }else{
            $this->error($ylb['msg']);
        }

    }

    public function speaker(){
        $data = I('get.');
        #防止跨渠道信息
        $map['id'] = $data['id'];
        $map['sid'] = session('mp.id');
        $map['domain_auth'] = domain_auth();
        $Store = M('MchStore')->where($map)->count();
        if (!$Store) {
            $this->error('非法操作!未找到对应门店信息!');
        }

        $where['mid'] = session('mp.id');
        $where['sid'] = $data['id'];
        $where['domain_auth'] = domain_auth();
        $rel = M('StoreSpeaker')->where($where)->find();
        unset($rel['domain_auth']);
        //获取云喇叭状态
        $ylbState='';
        if($rel['vid']){
            if($rel['appid']&&$rel['appkey']){
                $ylb = $this->ylb($data['vid'], true,1,$rel['appid'],$rel['appkey']);
            }else {
                $ylb=$this->ylb($rel['vid']);
            }
            $ylbState=$ylb['status']==1?$ylb['state']:$ylb['msg'];
        }
        $assign = array(
            'ylbState'=>$ylbState,
            'data' => $rel
        );
        $this->assign($assign);
        $this->display();
    }

    public function store(){
        $map=[
            'sid'=>session('mp.id'),
            'domain_auth'=>domain_auth(),
        ];
        $Data = M('mchStore');
        $count      = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $assign=array(
            'list' => $list,
            'page'=>  $show,
        );
        $this->assign($assign);
        $this->display();
    }

    public function printConfig(){
        $data = I('get.');
        #防止跨渠道信息
        $map['id'] = $data['id'];
        $map['sid'] = session('mp.id');
        $map['domain_auth'] = domain_auth();
        $Store = M('MchStore')->where($map)->count();
        if (!$Store) {
            $this->error('非法操作!未找到对应门店信息!');
        }
        $where['sid'] = session('mp.id');
        $where['store_id'] = $data['id'];
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
        $this->display();
    }


    #门店打印机保存
    public function printSave()
    {
        $data = I('post.');
        if (!$data['store_id']) {
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
            'sid' => session('mp.id'),
            'store_id' => $data['store_id'],
            'data' => serialize($data_arr),
            'status' => $data['print_status'],
            'domain_auth' => domain_auth(),
        );
        #判断信息是否有
        $where['sid'] = session('mp.id');
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


    /**
     * 测试播报
     */
    public function testSpeaker(){
        if(IS_POST){
            $data=I('post.');
            $where['mid'] = session('mp.id');
            $where['sid'] = $data['id'];
            $where['domain_auth'] = domain_auth();
            $rel = M('StoreSpeaker')->where($where)->find();
            if($rel){
                $res=sendSpeaker($data['id'],$data['total'],2);
                if($res['status']==1){
                    $this->success('测试信息发送成功');
                }else{
                    $this->error($res['msg']);
                }
            }else{
               $this->error('请先保存配置信息再发起测试');
            };
        }
    }
    
    /**
     * 云喇叭解绑/状态
     * @param $id喇叭ID
     * @param bool $type状态/绑定
     * @param int $bind
     * @param string $appid
     * @param string $appkey
     * @return array
     */
    public function ylb($id,$type=true,$bind=1,$appid='',$appkey=''){
        Vendor('ylb');
        if($appid){
            if(!$appkey){
                return ['status'=>0,'msg'=>'请填写appkey'];
            }
            $ylb=new \ylb($appid,$appkey,C('YLB.uid'));
        }else{
            $ylb=new \ylb(C('YLB.appid'),C('YLB.appkey'),C('YLB.uid'));
        }
        if($type){
            return $ylb->status($id);//状态
        }else{
            return $ylb->bind($id,$bind);//绑定/解绑
        }
    }
}