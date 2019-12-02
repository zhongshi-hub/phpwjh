<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 代理
 */
class MchAgentModel extends BaseModel{

    // 自动验证
    protected $_validate=array(
        array('user_phone','','代理联系电话已经存在',0,'unique',1),
        
        array('user_name','require','代理姓名必须'), // 验证字段必填
        array('user_phone','require','代理联系方式必须'), // 验证字段必填
        array('T1_TERM','require','T1终端费率必须'), // 验证字段必填
        array('T0_TERM','require','T0终端费率必须'), // 验证字段必填
        array('T1_COST','require','T1成本费率必须'), // 验证字段必填
        array('T0_COST','require','T0成本费率必须'), // 验证字段必填
    );

    // 自动完成
    protected $_auto=array(
        array('domain_auth','domain_auth',1,'function'), // 对date字段在新增的时候写入当前时间戳
        array('ctime','time',1,'function'), // 对date字段在新增的时候写入当前时间戳
    );


    #添加代理
    public function addData($data){
        // 对data数据进行验证
        if(!$data=$this->create($data)){
            // 验证不通过返回错误
            return false;
        }else{
            // 验证通过
            $result=$this->add($data);
            return $result;
        }
    }

    /**
     * 修改代理
     */
    public function editData($map,$data){
        // 对data数据进行验证
        if(!$data=$this->create($data)){
            // 验证不通过返回错误
            return false;
        }else{
            // 验证通过
            $result=$this
                ->where(array($map))
                ->save($data);
            return $result;
        }
    }


}