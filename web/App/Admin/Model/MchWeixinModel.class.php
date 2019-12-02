<?php
namespace Admin\Model;
use Common\Model\BaseModel;
/**
 * 代理
 */
class MchWeixinModel extends BaseModel{

    // 自动验证
    protected $_validate=array(
        array('name','require','公众号名称必须'), // 验证字段必填
        array('appid','require','公众号Appid必须'), // 验证字段必填
        array('appsecret','require','公众号AppSecret必须'), // 验证字段必填
    );

    // 自动完成
    protected $_auto=array(
        array('domain_auth','domain_auth',1,'function'), // 对date字段在新增的时候写入当前时间戳
        array('ctime','time',1,'function'), // 对date字段在新增的时候写入当前时间戳
        array('token','wx_token',1,'function'), // 对date字段在新增的时候写入当前时间戳
        array('encodingaeskey','wx_encodingaeskey',1,'function'), // 对date字段在新增的时候写入当前时间戳
    );


    #添加代理
    public function addData($data){
        // 对data数据进行验证
        if(!$data=$this->create($data)){
            // 验证不通过返回错误
            return false;
        }else{
            // 验证通过
            $result=$this->filter('strip_tags')->add($data);
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
                ->filter('strip_tags')->save($data);
            return $result;
        }
    }


}