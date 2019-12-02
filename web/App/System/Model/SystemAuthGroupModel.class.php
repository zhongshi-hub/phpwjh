<?php
namespace System\Model;
use System\Model\BaseModel;
/**
 * 权限规则model
 */
class SystemAuthGroupModel extends BaseModel{

	/**
	 * 传递主键id删除数据
	 * @param  array   $map  主键id
	 * @return boolean       操作是否成功
	 */
	public function deleteData($map){
		$this->where($map)->delete();
		$group_map=array(
			'group_id'=>$map['id']
			);
		// 删除关联表中的组数据
		$result=D('SystemAuthGroupAccess')->deleteData($group_map);
		return $result;
	}



}
