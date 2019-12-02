<?php
namespace Common\Controller;
use Think\Controller;
/**
 * 会员卡独立控制器
 */
class MemberActivityController extends Controller{

	protected $db;

	public function _initialize(){
		$this->db=M('mchMemberActivity');
	}

	/**
	 * 获取筛选后的配置数据规则
	 * @param $mid
	 * @param string $store_id
	 * @param string $type
	 * @return mixed
	 */
	public function getDataArr($mid,$store_id='',$type='cz'){
		 $where=['mid'=>$mid,'type'=>$type,'status'=>1];
         $list=M('mchMemberActivity')->where($where)->find();
         if($list) {
			 $data = json_decode($list['data'], true);
			 $infoType = $list['type'] . "Info";
			 $info = $this->$infoType($list['data'], true);
			 $data['rule_desc'] = is_array($info) ? implode(';', $info) : $info;
			 //是否限制门店
			 if (!empty($store_id) && !empty($list['store_id'])) {
				 $storeArr = explode(',', $list['store_id']);
				 if (!in_array($store_id, $storeArr)) {
					 unset($data);
				 }
			 }
			 //是否在时间内 充值和消费有时间限制
			 if ($type == 'cz' || $type == 'xf') {
				 if ($data['rule_type'] == 1) {
					 $time = explode(' - ', $data['rule_time']);
					 $start = strtotime($time[0]);
					 $end = strtotime($time[1]); //当前时间
					 $now = time();
					 if ($now >= $start && $now <= $end) {
						 return $data;
					 } else {
						 unset($data);
					 }
				 }
			 }
			 return $data;
		 }else{
         	return [];
		 }

	}


	/**
	 * 活动数据
	 * @param $mid
	 * @return array
	 */
	public function getListApi($mid,$store_id,$types=false){
		$data =$this->db->where(['mid'=>$mid,'status'=>1])->select();
		if($data) {
			$list = [];
			foreach ($data as $k => $v) {
				$type = $v['type'] . "Info";
				$info = $this->$type($v['data'], true,$types);
				$list[$k] = [
					'type' => $v['type'],
					'desc' => $this->typeName($v['type']),
					'name' => $v['name'],
					'list_desc' => is_array($info) ? implode(';', $info) : $info,
				];
				if (!empty($v['store_id'])) {
					$storeArr = explode(',', $v['store_id']);
					if (!in_array($store_id, $storeArr)) {
						unset($list[$k]);
					}
				}
				$rule_data = json_decode($v['data'], true);
				//是否在时间内 充值和消费有时间限制
				if ($v['type'] == 'cz' || $v['type'] == 'xf') {
					if ($rule_data['rule_type'] == 1) {
						$time = explode(' - ', $rule_data['rule_time']);
						$start = strtotime($time[0]);
						$end = strtotime($time[1]); //当前时间
						$now = time();
						if ($now >= $start && $now <= $end) {
						} else {
							unset($list[$k]);
						}
					}
				}
			}
			return $list;
		}else{
			return [];
		}
	}


	/**
	 * 活动列表
	 * @param $data
	 * @return array
	 */
	public function getList($data){
		$map=['mid'=>$data['mid']];
		$count      = $this->db->where($map)->count();// 查询满足要求的总记录数
		$page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $page->show();// 分页显示输出
		$list = $this->db->order('status desc')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
		if($list) {
			$lists = [];
			foreach ($list as $k => $v) {
				$type = $v['type'] . "Info";
				$info = $this->$type($v['data']);
				$lists[$k] = $v;
				$lists[$k]['info'] = is_array($info) ? implode(';', $info) : $info;
				$lists[$k]['time'] = $this->timeInfo($v['data']);
				$lists[$k]['typeName'] = $this->typeName($v['type']);
				$lists[$k]['statusName'] = ($v['status'] == 1) ? '进行中' : '已结束';
				$lists[$k]['storeNum'] = $this->storeNum($v['store_id']);
			}
			$assign = [
				'list' => $lists,
				'page' => $show,
			];
			return $assign;
		}else{
			return [];
		}
	}


	/**
	 * 活动详情
	 * @param $data
	 * @return mixed
	 */
	public function getDetail($data){
		$res=$this->db->where($data)->find();
		$res['data']=$this->jsonArr($res['data']);
		$res['time']=$this->timeInfo($res['data']);
		$res['typeName']=$this->typeName($res['type']);
		$type=$res['type']."Info";
		$info=$this->$type($res['data']);
		$res['info']=is_array($info)?implode(';',$info):$info;
		$res['xfType']=$this->xfType($res['data']['fx_type']);
		$res['ruleTypeInfo']=$this->ruleTypeInfo($res['data']);
		$res['store_name']=$this->storeName($res['store_id']);
		return $res;
	}


	/**
	 * 所属门店
	 * @param $data
	 * @param bool $type true返回数组 false返回字符串
	 * @return array|string
	 */
	public function storeName($data,$type=false){
		$name=[];
		foreach (explode(',',$data) as $k=>$v){
			$name[]=M('MchStore')->where(array('id' => $v))->getField('name');
		};
		return $type?$name:implode(',',$name);
	}

	/**
	 * 关闭活动
	 * @param $data
	 * @return bool
	 */
	public function setStatus($data){
		$this->db->where(['id'=>$data['id'],'mid'=>$data['mid']])->save(['status'=>0]);
		return true;
	}

	/**
	 * 门店数量统计
	 * @param $data
	 * @return int|string
	 */
	public function storeNum($data){
		$arr=explode(',',$data);
		$num=count($arr);
		return $num?$num:'无限制';
	}

	/**
	 * 活动时间转换
	 * @param $data
	 * @return string
	 */
	public function timeInfo($data){
		$json=is_array($data)?$data:$this->jsonArr($data);
		if(!$json['rule_type']||$json['rule_type']==1) {
			$time = explode(' - ', $json['rule_time']);
			if (empty($time[0])) {
				return '无限制';
			} else {
				return date('Y-m-d', strtotime($time[0])) . '至' . date('Y-m-d', strtotime($time[1]));
			}
		}else{
			return '无限制';
		}
	}

	/**
	 * 激活送描述
	 * @param $data
	 * @return string
	 */
	public function jhInfo($data,$type=false){
		$json=is_array($data)?$data:$this->jsonArr($data);
		return $type?"激活会员送{$json['total']}":"本店激活会员卡赠送余额{$json['total']}元";
	}


	/**
	 * 时间规则
	 * @param $data
	 * @return string
	 */
	public function ruleTypeInfo($data){
		$json=is_array($data)?$data:$this->jsonArr($data);
		return ($json['rule_type']==1)?'指定时间':'不限时间';
	}

	/**
	 * 推荐送描述
	 * @param $data
	 * @return string
	 */
	public function tjInfo($data,$type=false,$types=false){
		$json=is_array($data)?$data:$this->jsonArr($data);
		$ret=$types?"推荐会员首次充值{$json['cz_total']}元,送{$json['tjr_total']}元":"推荐会员送{$json['tjr_total']}";
		return $type?$ret:"推荐一个会员且完成首笔充值(充值金额大于或等于{$json['cz_total']}元)，推荐人和被推荐人分别赠送{$json['tjr_total']}元";
	}

	/**
	 * 消费返现描述
	 * @param $data
	 * @return array
	 */
    public function xfInfo($data,$type=false){
		$json=is_array($data)?$data:$this->jsonArr($data);
		$info=[];
		if($json['fx_type']=="1"){
			foreach ($json['xf_money'] as $k=>$v){
				$info[]=$type?"消费{$v}返{$json['xf_total'][$k]}":"消费满{$v}元返{$json['xf_total'][$k]}元 ({$this->xfType($json['fx_type'])})";
			}
		}elseif ($json['fx_type']=="2"){
			$info[]=$type?"消费{$json['xf2_money']}返{$json['xf2_total']}":"消费满{$json['xf2_money']}元返{$json['xf2_total']}元 ({$this->xfType($json['fx_type'])})";
		}elseif ($json['fx_type']=="3"){
			$info[]=$type?"消费返{$json['xf3_money']}%":"返现比例{$json['xf3_money']}% ({$this->xfType($json['fx_type'])})";
		}else{
			$info[]="未知";
		}
		return $info;
	}

	/**
	 * 消费返现类型
	 * @param $data
	 * @return string
	 */
	public function xfType($data){
		switch ($data){
			case '1':
				$name='分比例返现';
				break;
			case '2':
				$name='等比例返现';
				break;
			case '3':
				$name='百分比返现';
				break;
			default:
				$name='未知';
				break;
		}
		return $name;
	}

	/**
	 * 充值送转描述
	 * @param $data
	 * @return array
	 */
	public function czInfo($data,$type=false){
		$json=is_array($data)?$data:$this->jsonArr($data);
		$info=[];
		$infos=[];
		foreach ($json['cz_money'] as $k=>$v){
			$info[]="充值满{$v}元送{$json['cz_total'][$k]}元";
			$infos[]="充{$v}返{$json['cz_total'][$k]}";
		}
		return $type?$infos:$info;
	}


	/**
	 * json转数组
	 * @param $data
	 * @return mixed
	 */
	public function jsonArr($data){
		return json_decode($data,true);
	}

	/**
	 * 类型转换
	 * @param $type
	 * @return string
	 */
	public function typeName($type){
		switch ($type){
			case 'cz':
				$name='充值送';
				break;
			case 'xf':
				$name='消费返';
				break;
			case 'jh':
				$name='激活送';
				break;
			case 'tj':
				$name='推荐送';
				break;
			default:
				$name='未知';
				break;
		}
		return $name;
	}

	/**
	 * 充值送
	 * @param $data
	 * @return array
	 */
	public function czCheck($data){
		if(empty($data['mid'])){
			return ['status'=>false,'msg'=>'获取商户信息失败,请重新登录'];
		}
		if(empty($data['name'])){
			return ['status'=>false,'msg'=>'活动名称不能为空'];
		}
		if(empty($data['cz_money'][0])||empty($data['cz_total'][0])){
			return ['status'=>false,'msg'=>'请输入正确的充值满减门栏金额'];
		}
		if($data['rule_type']==1&&empty($data['rule_time'])){
			return ['status'=>false,'msg'=>'请设置时间范围'];
		}
		if(!is_array($data['store_id'])){
			return ['status'=>false,'msg'=>'请选择活动门店'];
		}
		return $this->setActivity($data);
	}

	/**
	 * 消费返现
	 * @param $data
	 * @return array
	 */
	public function xfCheck($data){
		if(empty($data['mid'])){
			return ['status'=>false,'msg'=>'获取商户信息失败,请重新登录'];
		}
		if(empty($data['name'])){
			return ['status'=>false,'msg'=>'活动名称不能为空'];
		}
		if($data['rule_type']==1&&empty($data['rule_time'])){
			return ['status'=>false,'msg'=>'请设置时间范围'];
		}
		if(!is_array($data['store_id'])){
			return ['status'=>false,'msg'=>'请选择活动门店'];
		}
		if($data['fx_type']==1){
            if(empty($data['xf_money'][0])||empty($data['xf_total'][0])){
				return ['status'=>false,'msg'=>'请输入正确的分比例返现金额'];
			}
		}
		if($data['fx_type']==2){
			if(empty($data['xf2_money'])||empty($data['xf2_total'])){
				return ['status'=>false,'msg'=>'请输入正确的等比例返现金额'];
			}
		}
		if($data['fx_type']==3){
			if(empty($data['xf3_money'])){
				return ['status'=>false,'msg'=>'请输入正确的百分比返现'];
			}
		}
		return $this->setActivity($data);
	}


	/**
	 * 激活送
	 * @param $data
	 * @return array
	 */
	public function jhCheck($data){
		if(empty($data['mid'])){
			return ['status'=>false,'msg'=>'获取商户信息失败,请重新登录'];
		}
		if(empty($data['name'])){
			return ['status'=>false,'msg'=>'活动名称不能为空'];
		}
		if(empty($data['total'])){
			return ['status'=>false,'msg'=>'请填写激活会员卡赠送余额'];
		}
		return $this->setActivity($data);
	}


	/**
	 * 推荐送
	 * @param $data
	 * @return array
	 */
	public function tjCheck($data){
		if(empty($data['mid'])){
			return ['status'=>false,'msg'=>'获取商户信息失败,请重新登录'];
		}
		if(empty($data['name'])){
			return ['status'=>false,'msg'=>'活动名称不能为空'];
		}
		if(empty($data['cz_total'])){
			return ['status'=>false,'msg'=>'请填写奖励首单充值最低金额'];
		}
		if(empty($data['tjr_total'])){
			return ['status'=>false,'msg'=>'请填写推荐人与被推荐人奖励余额'];
		}
		return $this->setActivity($data);
	}


	/**
	 * 创建活动
	 * @param $data
	 * @return array
	 */
	public function setActivity($data){
		//是否新增过 新增过先禁用 在新增新的活动
		$this->db->where(['type'=>$data['type'],'mid'=>$data['mid']])->save(['status'=>0]);
		$json=$data;
		unset($json['mid']);
		unset($json['name']);
		unset($json['type']);
		unset($json['store_id']);
		//新增
		$arr=[
			'mid'=>$data['mid'],
			'type'=>$data['type'],
			'name'=>$data['name'],
			'create_time'=>time(),
			'status'=>1,
			'data'=>json_encode($json,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
		];
		if(!empty($data['store_id'][0])){
			$arr['store_id']=implode(',',$data['store_id']);
		}
		$res=$this->db->add($arr);
		return $res?['status'=>true,'msg'=>'活动创建成功']:['status'=>false,'msg'=>'活动创建失败'];
	}
}