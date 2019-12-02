<?php
namespace Mch\Controller;

use Mch\Controller\InitBaseController;

class ExtensionController extends InitBaseController
{


    public function _initialize()
    {

    }


    //升级等级
    public function upGrade(){
        $data=I('post.');
        $data['id']=$data['id']?$data['id']:$_SESSION['mch']['id'];
        $extensionMch=M('extensionMch');
        //获取当前商户已推广个数
        $where=[
            'pid'=>$data['id'],
            'domain_auth'=>domain_auth()
        ];
        $mchCount=$extensionMch->where($where)->count();
        //获取要升级的等级需要个数
        $grade=extensionGrade($data['grade']);
        $upGradeCount=$grade['up_number'];
        //获取中间差额
        $num=$upGradeCount-$mchCount;
        if($mchCount<=$upGradeCount){
            //增加费率变更任务
            $msnData = array(
                'mc' => 'Extension', #模块
                'ac' => 'alter_rate' #方法
            );
            $res = ali_mns($msnData);
            if ($res['status'] == 1) {
                $arr=array(
                    'mid'=>$data['id'],
                    'alleys'=>$grade['rate'],
                    'grade'=>$data['grade'],
                    'type'=>'auto',
                    'op_id'=>0
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
                    //可以升级 将等级数据插入规则数据库
                    if($extensionMch->where(['mid'=>$data['id'],'domain_auth'=>domain_auth()])->count()){
                        $res=$extensionMch->where(['mid'=>$data['id'],'domain_auth'=>domain_auth()])->save(['grade'=>$data['grade']]);
                    }else{
                        //没有商户数据进行新增
                        $arr=[
                            'mid'=>$data['id'],
                            'pid'=>0,
                            'grade'=>$data['grade'],
                            'create_time'=>time(),
                            'update_time'=>time(),
                            'domain_auth'=>domain_auth()
                        ];
                        $res=$extensionMch->add($arr);
                    }
                    if($res){
                        $this->success('升级成功!1分钟后生效!');
                    }else{
                        $this->error('升级失败');
                    }
                }else{
                    $this->error('升级任务创建失败! E2');
                }
            }else{
                $this->error('升级任务创建失败！E1');
            }

        }else{
            $this->error('不符合规则!还需要直推'.$num.'个用户才可以升级当前等级');
        }
        //$this->error('开发中');
    }

	/**
	 * 我的等级
	 */
    public function myGrade(){
		//当前用户等级
		$gradeId=extensionMch(['mid'=>$_SESSION['mch']['id']],'grade');
		//如果获取不到等级按照默认等级
		if(!$gradeId){
			$gradeId=extensionSetting('grade');
		}

		$db=M('extensionGrade');
		$map['domain_auth'] = domain_auth();
		$map['status']=1;
		$map['id']=$gradeId;
		$data=$db->where($map)->order('id desc')->select();
		//等级终端费率
		$endData=[];
		foreach ($data as $k=>$v){
			$rateData=json_decode($v['rate'],true);
			//进行等级转换
			$MidRate=[];
			foreach ($rateData as $kk=>$vv){
				$explodeRate=explode('_',$kk);
				if($explodeRate[1]=='term'){
					//标识换为通道名称
					$name=alleys_name($explodeRate[0]);
					$MidRate[$name]=$vv;
				}
			}
			$endData[$k]=[
				'id'=>$v['id'],
				'name'=>$v['name'],
				'status'=>$v['status'],
				'rate'=>$MidRate,
				'zt'=>$v['zt'],
				'jt'=>$v['jt'],
				'up_number'=>$v['up_number']
			];
		}


		$assign=[
			'data'=>$endData[0],
			'gradeId'=>$gradeId,
		];
		$this->assign($assign);
		$this->display();
	}

    //升级等级
    public function grade(){
        $db=M('extensionGrade');
        $map['domain_auth'] = domain_auth();
        $map['status']=1;
        $data=$db->where($map)->order('id desc')->select();
        //等级终端费率
        $endData=[];
        foreach ($data as $k=>$v){
            $rateData=json_decode($v['rate'],true);
            //进行等级转换
            $MidRate=[];
            foreach ($rateData as $kk=>$vv){
                $explodeRate=explode('_',$kk);
                if($explodeRate[1]=='term'){
                    //标识换为通道名称
                    $name=alleys_name($explodeRate[0]);
                    $MidRate[$name]=$vv;
                }
            }
            $endData[$k]=[
                'id'=>$v['id'],
                'name'=>$v['name'],
                'status'=>$v['status'],
                'rate'=>$MidRate,
                'zt'=>$v['zt'],
                'jt'=>$v['jt'],
                'up_number'=>$v['up_number']
            ];
        }
        //当前用户等级
        $gradeId=extensionMch(['mid'=>$_SESSION['mch']['id']],'grade');
        //如果获取不到等级按照默认等级
        if(!$gradeId){
            $gradeId=extensionSetting('grade');
        }

        $assign=[
          'data'=>$endData,
          'gradeId'=>$gradeId,
        ];
        $this->assign($assign);
        $this->display();
    }


    //分润详情
    public function benefitDataJson(){
        $db = M('extensionBenefit');
        $map['mid'] = $_SESSION['mch']['id'];
        $map['domain_auth'] = domain_auth();
        $list = $db->where($map)->order('id desc')->field('day,benefit')->select();
        $count = count($list);
        $Page = new \Think\Mpage($count, 6);
        $lists = array_slice($list, $Page->firstRow, $Page->listRows);
        $_data = array(
            'pages' => $Page->totalPages,
            'data' => $lists,
        );
        echo json_encode($_data);
        exit;
    }

    //推广分润
    public function benefit(){
        $mid=$_SESSION['mch']['id'];
        $extensionBenefit=M('extensionBenefit');
        //当前用的总分润
        $benefit=$extensionBenefit->where(['mid'=>$mid,'domain_auth'=>domain_auth()])->sum('benefit');
        $benefit=number_format($benefit,2);
        $assign=[
            'benefit'=>$benefit
        ];
        $this->assign($assign);
        $this->display();
    }


    //用户推广用户码
    public function qrCode() {
        if(IS_POST) {
            $mid = $_SESSION['mch']['id'];
            //判断是否有推广权限
            $mchAid = GetMchAid($mid);
            $extensionSettingAid = extensionSetting('aid');
            if ($mchAid != $extensionSettingAid) {
                $this->error('您无推广权限');
            }
            //推广码生成
            $data = [
                'mid' => $mid//商户ID
            ];
            $codeUrl='http://' . $_SERVER['HTTP_HOST'] . '/Plugs/Qr/code/data/';
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/Plugs/Extension/invite/data/' . Xencode(json_encode($data));
            $url=$codeUrl.Xencode($url);
            $this->success('通信成功',$url);
        }else{
            $this->error('非法操作');
        }

    }
}