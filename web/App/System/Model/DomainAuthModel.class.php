<?php
namespace System\Model;
use System\Model\BaseModel;
/**
 * 品牌
 */
class DomainAuthModel extends BaseModel{

    // 自动验证
    protected $_validate=array(
        array('web_name','','品牌名称已经存在',0,'unique',1),
        array('web_domain','','品牌域名已经存在',0,'unique',1),
        array('theme','','品牌模板主题已经存在',0,'unique',1),
        array('web_name','require','品牌名称必须'), // 验证字段必填
        array('web_domain','require','品牌域名必须'), // 验证字段必填
        array('theme','require','品牌模板主题必须'), // 验证字段必填
        array('web_doname','require','品牌公司必须'), // 验证字段必填
    );

    // 自动完成
    protected $_auto=array(
        array('web_authcode','domain_auth_code',1,'function') , // 对password字段在新增的时候使md5函数处理
        array('channel_id','ChannelId',1,'function'),
        array('channel_key','ChannelKey',1,'function'),
        array('web_ctime','time',1,'function'), // 对date字段在新增的时候写入当前时间戳
        array('opid','system_opid',3,'function')
    );


    #添加品牌
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

  






}
?>