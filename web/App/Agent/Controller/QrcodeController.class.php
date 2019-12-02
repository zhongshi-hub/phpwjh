<?php
namespace Agent\Controller;
use Agent\Controller\InitBaseController;
class QrcodeController extends InitBaseController
{


    #收款码代理分配
    public function allot_agent(){
        $data=I('post.');
        #代理是否存在
        $map['domain_auth']=domain_auth();
        $map['id']=array('in',self::AgentAll());
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
    public function lists(){
        $p=I('param.');
        $maps['codes']=$p['codes']?array('like','%'.$p['codes'].'%'):array('neq','');

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
        $_count=$p['page_data']?$p['page_data']:10;

        $maps['aid']=$p['aid']?$p['aid']:array('in',self::AgentAll());

        $map['id']=array('in',self::AgentAll());
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
        /*if(IS_POST) {
            $p=I('post.');
            //判断
            if(!$p['count']){$this->error('创建数量最低1个');}
            $data = array(
                'mc' => 'QrCode', #模块
                'ac' => 'adds' #方法
            );
            $res = ali_mns($data);
            if ($res['status'] == 1) {
                $arr=array(
                    'count'=>$p['count'],
                    'aid'=>$p['aid']
                );
                $_data=array(
                    'task_data'=>serialize($arr),
                    'auth_code'=>domain_auth(),
                    'rel'=>serialize($res)
                );
                $where['messageId']=$res['messageId'];
                $where['id']=$res['msn_id'];
                $rel=M('alimsn')->where($where)->save($_data);
                if($rel){
                    $this->success('云码任务创建成功,系统已在后端处理!');
                }else{
                    $this->error('云码任务创建失败!');
                }
            } else {

                $this->error('云码任务创建失败!');
            }
        }*/

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
                if (count($imgs) > 50) {
                    $this->error('单次导出数量不能大于50个');
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