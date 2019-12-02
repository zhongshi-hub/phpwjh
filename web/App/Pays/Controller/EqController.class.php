<?php
namespace Pays\Controller;
use Pays\Controller\InitBaseController;

class EqController extends InitBaseController {




    #云码入口
    public function index(){

        $Code=I('get.codes');
        #获取主域名
        $MchCode=M('MchCodes')->where(array('codes'=>$Code))->field('status,domain_auth,codes')->find();
        if($MchCode){
            #存在收款码 获取主域名
            $MainDomain=M('DomainAuth')->where(array('web_authcode'=>$MchCode['domain_auth']))->field('main_domain,status')->find();
            //dump($MainDomain);
            if($MainDomain['status']==1){ #正常
                if($MainDomain['main_domain']){
                    $url='http://'.$MainDomain['main_domain'].'/EQM/'.$MchCode['codes'];
                    redirect($url);
                }else{
                    $this->error('Error: No Do Msg:未配置Main信息 请联系官方人员!','',888);
                }
            }else{#不正常
                $this->error('Error:Do Status Msg:渠道被禁用 请联系官方人员','',888);
            }
        }else{
            $this->error('Error:No Code Msg: 无此收款码','',888);
        }
    }


    #商户码入口
    public function codes(){
        $Code=I('get.codes');
        #首先进行码状态监测
        $map=array(
            'codes'=>$Code,
            'domain_auth'=>domain_auth()
        );
        $data=M('MchCodes')->where($map)->find();
        if(!$data){$this->error('渠道域名参数有误!未获取当前渠道域名参数!QR ERROR QRM','',888);}
        if($data['aid']==0){$this->error('Msg:此收款码未分配业务','',888);}
        if($data['status']!=1){$this->error('Msg:此收款码已被禁用','',888);}
        #判断是否被注册
        if(!$data['store_id']){
            $_data=$this->_oauth('base_xun');
            //dump($_data);
            $_SESSION['Reg']=array(
                'codes'=>$Code,
                'user_info'=>$_data
            );
            /*if($Code=='P10190881'){
                redirect(U('MchReg/index'));
            }else {
                #未注册
                redirect(U('Reg/index'));
            }*/
            redirect(U('MchReg/index'));
        }else{
            #根据ID判断商户状态
            $status=M('MchSeller')->where(array('id'=>$data['mch_id']))->getField('status');
            if($status==1) {
                redirect('/Pay/' . Xencode($data['codes']));
            }elseif ($status==3){
                $this->error('商户已被禁用!无法使用此服务!','',888);
            }else{
               // redirect(U('Reg/mch_status',array('mch_id'=>$data['mch_id'])));
                redirect(U('MchReg/mch_status',array('mch_id'=>$data['mch_id'],'store_id'=>$data['store_id'])));
            }
        }
    }
}


