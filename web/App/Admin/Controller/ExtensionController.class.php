<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;

/**
 * 营销控制器
 */
class ExtensionController extends AdminBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->db = M('MchSeller');
    }

    //辅助工具
    public function tools(){
        if(IS_POST){
        	$data=I('post.');
        	//判断当前用户所属代理ID和系统设置的代理是否一致
			if(GetMchAid($data['mid'])!=extensionSetting('aid')){$this->error('请先将当前商户过户到关联代理名下');}
			//判断上级是否在关联代理名下
			if($data['pid']!=0) {
				if (GetMchAid($data['pid']) != extensionSetting('aid')) {
					$this->error('请先将上级商户过户到关联代理名下');
				}
			}
			//判断是否通道进件
			$alleyArr=['Rhcard','RhMin','RhMax'];
			foreach ($alleyArr as $v){
				$alleyData=M('MchSellerCardAlleys')->where(['cid'=>$data['mid'],'alleys_type'=>$v])->getField('mch_id');
				if(!$alleyData){
					$this->error('当前商户无卡快捷('.alleys_name($v).')通道未进件,请先进件');
				}
			}
			//以上信息都通过  按照等级进行费率变更操作
			$grade=extensionGrade($data['grade']);
			//增加费率变更任务
			$msnData = array(
				'mc' => 'Extension', #模块
				'ac' => 'alter_rate' #方法
			);
			$res = ali_mns($msnData);
			if ($res['status'] == 1) {
				if(MODULE_NAME=='Admin'){
					$op_id=$_SESSION['user']['id'];
				}else{
					$op_id='0';
				}
				$arr=array(
					'mid'=>$data['mid'],
					'alleys'=>$grade['rate'],
					'grade'=>$data['grade'],
					'type'=>'sys',
					'op_id'=>$op_id
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
					//可以迁移 将等级数据插入规则数据库
					$extensionMch=M('extensionMch');
					if($extensionMch->where(['mid'=>$data['mid'],'domain_auth'=>domain_auth()])->count()){
						$res=$extensionMch->where(['mid'=>$data['mid'],'domain_auth'=>domain_auth()])->save(['grade'=>$data['grade']]);
					}else{
						//没有商户数据进行新增
						$arr=[
							'mid'=>$data['mid'],
							'pid'=>$data['pid'],
							'grade'=>$data['grade'],
							'create_time'=>time(),
							'update_time'=>time(),
							'domain_auth'=>domain_auth()
						];
						$res=$extensionMch->add($arr);
					}
					if($res){
						$this->success('迁移成功!');
					}else{
						$this->error('迁移失败!已存在当前商户信息！');
					}
				}else{
					$this->error('等级任务创建失败! E2');
				}
			}else{
				$this->error('等级任务创建失败！E1');
			}
        }else {
            $map['status'] = 1;
            $map['domain_auth'] = domain_auth();
            $Data = M('extensionGrade');
            $list = $Data->where($map)->field('id,name')->select();
            $this->assign('list', $list);
            $this->display();
        }
    }

    //等级日志
    public function alterGradeLog(){
        $id=I('get.id');
        if($id){
            $map['mid'] =$id;
        }
        $map['domain_auth'] = domain_auth();
        $Data = M('extensionAlterLog');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
        );
        $this->assign($assign);
        $this->display();

    }
    //推广分润详情
    public function benefitDetail(){
        $id=I('get.id');
        if(!$id){$this->error('非法操作');}
        //获取所有分润
        $map['mid'] =$id;
        $map['domain_auth'] = domain_auth();
        $Data = M('extensionBenefit');
        $count = $Data->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
        );
        $this->assign($assign);
        $this->display();



    }
    //推广分润
    public function benefit(){
        $post=I('post.');
        //转换名称和手机号的商户为ID
        if($post['search_val']){
            //检索商户数据库
            $searchWhere=[
                'mch_name|mch_tel'=>array('like','%'.$post['search_val'].'%'),
                'domain_auth'=>domain_auth()
            ];
            $getId=M('mchSeller')->where($searchWhere)->getField('id',true);
            //根据ID 筛选推广记录
            $postWhere['pid'] = array('in',$getId);
        }

        $postWhere['domain_auth'] = domain_auth();
        //获取推广员信息
        $Data = M('extensionMch');
        $count = $Data->where($postWhere)->group('pid')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->where($postWhere)->order('id desc')->group('pid')->field('pid')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $listArr=[];
        foreach ($list as $k=>$v){
            $listArr[$k]=[
              'pid'=>$v['pid'],
              'count'=>self::benefitArr($v['pid'],'count'),  //根据pid获取推广人数
              'ztBenefit'=>number_format(self::benefitArr($v['pid'],'zt'),2),//直推总分润
              'jtBenefit'=>number_format(self::benefitArr($v['pid'],'jt'),2),//间推总分润
              'benefit'=>number_format(self::benefitArr($v['pid'],'benefit'),2),//总分润
            ];
        }
        $assign = array(
            'data' => $listArr,
            'page' => $show,
        );
        $this->assign($assign);
        $this->display();
    }

    //分润子方法
    public function benefitArr($id,$type){
        $map['domain_auth'] = domain_auth();
        if($type=='count') {
            //获取推广员信息
            $extensionMch = M('extensionMch');
            $count = $extensionMch->where($map)->where(['pid' => $id])->count();
            return $count;
        }else {
            //分润信息
            $extensionBenefit = M('extensionBenefit');
            $benefit = $extensionBenefit->where($map)->where(['mid' => $id])->sum($type);
            return $benefit;
        }
    }

    //推广列表
    public function Invite(){
        if(IS_POST){
            $post=I('post.');
            if($post['grade']) {
                $postWhere['grade'] = $post['grade'];
            }
            //转换名称和手机号的商户为ID
            if($post['search_val']){
                //检索商户数据库
                $searchWhere=[
                  'mch_name|mch_tel'=>array('like','%'.$post['search_val'].'%'),
                  'domain_auth'=>domain_auth()
                ];
                $getId=M('mchSeller')->where($searchWhere)->getField('id',true);
                //根据ID 筛选推广记录
                $postWhere['mid|pid'] = array('in',$getId);
            }
        }
        $postWhere['domain_auth'] = domain_auth();
        $Data = M('extensionMch');
        $count = $Data->where($postWhere)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = $Data->order('id')->where($postWhere)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //获取等级列表
        $map['status'] = 1;
        $Data = M('extensionGrade');
        $gradeList = $Data->where($map)->where(['domain_auth'=>domain_auth()])->field('id,name')->select();
        $assign = array(
            'data' => $list,
            'page' => $show,
            'gradeList'=>$gradeList
        );

        $this->assign($assign);
        $this->display();
    }
    //基础设置
    public function setting(){
        $map['domain_auth'] = domain_auth();
        $settingDb=M('extensionSetting');
        if(IS_POST){
            $data=I('post.');
            unset($data['__TokenHash__']);
            //判断是否存在数据
            $count=$settingDb->where($map)->count();
            if($count){
                $res=$settingDb->where($map)->save($data);
            }else{
                $data['domain_auth'] = domain_auth();
                $res=$settingDb->add($data);
            }
            if($res){
                $this->success('参数保存成功',U('setting'));
            }else{
                $this->error('参数保存失败');
            }
        }else {
            //获取等级列表
            $map['status'] = 1;
            $Data = M('extensionGrade');
            $list = $Data->where($map)->field('id,name')->select();
            $setting=$settingDb->where(['domain_auth'=>domain_auth()])->find();

            $this->assign('data', $setting);
            $this->assign('list', $list);
            $this->display();
        }
    }


    //等级列表
    public function grade(){
        $map['domain_auth'] = domain_auth();
        $Data = M('extensionGrade');
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

    //添加、编辑等级
    public function gradeSet(){
        $gradeDb=M('extensionGrade');
        if(IS_POST){
             $data=I('post.');
             //为了符合逻辑 做一个费率判断
             foreach ($data['rate'] as $k=>$v){
                 $fist=explode('_',$k);
                 $term=$fist[1];
                 if($term=='term') {
                     $cost = $data['rate'][$fist[0] . '_cost'];
                     if($v<=$cost){
                       $this->error('通道('.alleys_name($fist[0]).')终端费率不得小于等于成本费率');
                       continue;
                     }
                 }
             }
             //费率合并json格式存储
             $data['rate']=json_encode($data['rate']);
             $data['domain_auth']=domain_auth();
             unset($data['__TokenHash__']);
             //是增加还是保存
             if ($data['id']){
                 $data['update_time']=time();
                 $res=$gradeDb->where(['id'=>$data['id']])->save($data);
             }else{
                 $data['create_time']=time();
                 $data['update_time']=time();
                 $res=$gradeDb->add($data);
             }
             if($res){
                 $this->success('参数保存成功',U('grade'));
             }else{
                 $this->error('参数保存失败');
             }
        }else {
            //当前已开通所有通道
            $where['cid'] = domain_id();
            $where['status'] = 1;
            $api = M('DomainAlleys')->where($where)->field('alleys,alleys_type,rate')->select();
            $alley = [];
            foreach ($api as $k => $v) {
                //查询代还和无卡的通道 否则不输出
                $res = M('MchAlleys')->where(['type' => $v['alleys_type'], 'status' => 1, 'is_card' => ['in', '1,2']])->find();
                if ($res) {
                    $alley[] = $v;
                } else {
                    unset($api[$k]);
                }
            }

            if(I('get.id')){//编辑模式
              //取当前数据库配置的通道数据
                $res=$gradeDb->where(['id'=>I('get.id'),'domain_auth'=>domain_auth()])->find();
                if(!$res){
                    $this->error('非法操作');
                }
                $assign=[
                    'res'=>$res,
                    'rate'=>json_decode($res['rate'],true)//Json To Arr
                ];
            }

            //获取代理默认终端
            $assign['api'] = $alley;
            $this->assign($assign);
            $this->display();
        }
    }



}