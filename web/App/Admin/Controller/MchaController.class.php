<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 渠道管理控制器
 */
class MchaController extends AdminBaseController{

  /*渠道结算状态更新*/
  public function account_status(){
      $partner=I('post.partner');
      $daytime=I('post.datatime');
      if(!$partner||!$daytime){
          $this->error('非法操作');
      }
      //防止信息串
      $accounts=M('mch_accounts')->where(array('partner'=>$partner,'acctime'=>$daytime))->find();
      if(!$accounts){
          $this->error('当前渠道结算信息读取错误');
      }
      $save=array(
        'status'=> I('post.status'),
        'fit_fee'=> I('post.fit_fee'),
        'fit_time'=> time(),
      );
      $res=M('mch_accounts')->where(array('partner'=>$partner,'acctime'=>$daytime))->save($save);
      if($res){
          $this->success('结算状态变更成功!');
      }else{
          $this->error('结算状态变更失败');
      }
  }

  /*渠道结算更新*/
  public function updates(){
      $partner=I('get.partner');
      $daytime=I('get.datatime');
      $endtime=strtotime(date('Y-m-d',$daytime) ."+1 day");
      if(!$partner||!$daytime){
          $this->error('非法操作');
      }
      //防止信息串
      $accounts=M('mch_accounts')->where(array('partner'=>$partner,'acctime'=>$daytime))->find();
      if(!$accounts){
          $this->error('当前渠道结算信息读取错误');
      }
      //查询支付成功的订单
      $or['_string'] = "(`createtime`> '" . $daytime . "') AND ( `createtime` < '" . $endtime . "') ";
      $orders=M('mch_orders')->where(array('partner'=>$partner,'status'=>1))->where($or)->order('id Desc')->select();
      $fee=M('mch_orders')->where(array('partner'=>$partner,'status'=>1))->where($or)->order('id Desc')->sum('total_fee');
      //渠道信息
      $Api=M('mch_parent_api')->where(array('placenum'=>$partner))->find();
      if(!$Api){
          $this->error('渠道不存在!');
      }elseif(!$Api['mcha_rate']){
          $this->error('渠道结算费率不存在!');
      }else{
          //根据渠道编号统计当前渠道下所有商户的分润
          foreach ($orders as $k => $v) {
              $datas=array(
                  'term'=>$v['mch_rate'], //终端价
                  'cost'=>$accounts['rate'], //渠道价 随时不影响以前 按照结算表里成本
                  'fee'=>$v['total_fee']/100, //交易金额
              );
              $set['endtime']=date('Y-m-d H:i:s',$endtime);
              $set['daytime']=date('Y-m-d H:i:s',$daytime);
              $set['otime']=date('Y-m-d H:i:s',$v['createtime']);
              $set['fit']=accounts_rate($datas); //分润金额
              $set['mch_id']=$v['mch_id']; //商户号
              //取商户名称
              $Mch_name=M('mch_merchant')->where(array('merchantid'=>$v['mch_id']))->getField('merchantName');
              $set['mch_name']=$Mch_name; //商户号
              $set['service']=$v['service']; //服务接口
              $set['TotalFee']=$v['total_fee']/100; //交易金额
              $set['rate']=$v['mch_rate'];
              $fit[]=$set;
              $fr[]=accounts_rate($datas);
          }
          $save=array(
              'pay_count'=>count($fit),
              'total_fee'=>$fee/100,
              'profit'=>array_sum($fr),
          );
          $res=M('mch_accounts')->where(array('partner'=>$partner,'acctime'=>$daytime))->save($save);
          if($res){
              $this->success('当前渠道结算信息更新成功');
          }else{
              $this->error('当前渠道结算信息更新失败!也许此结算信息正确!不需要更新!');
          }
      }

  }

  /*渠道结算详情*/
  public function accounts_detail(){
      $partner=I('get.partner');
      $daytime=I('get.datatime');
      $endtime=strtotime(date('Y-m-d',$daytime) ."+1 day");
      if(!$partner||!$daytime){
          $this->error('非法操作');
      }
      //防止信息串
      $accounts=M('mch_accounts')->where(array('partner'=>$partner,'acctime'=>$daytime))->find();
      if(!$accounts){
          $this->error('当前渠道结算信息读取错误');
      }

      //查询支付成功的订单
      $or['_string'] = "(`createtime`> '" . $daytime . "') AND ( `createtime` < '" . $endtime . "') ";
      $orders=M('mch_orders')->where(array('partner'=>$partner,'status'=>1))->where($or)->order('id Desc')->select();
      //渠道信息
      $Api=M('mch_parent_api')->where(array('placenum'=>$partner))->find();
      if(!$Api){
          $this->error('渠道不存在!');
      }elseif(!$Api['mcha_rate']){
          $this->error('渠道结算费率不存在!');
      }else{
          //根据渠道编号统计当前渠道下所有商户的分润
          foreach ($orders as $k => $v) {
              $datas=array(
                  'term'=>$v['mch_rate'], //终端价
                  'cost'=>$accounts['rate'], //渠道价 随时不影响以前 按照结算表里成本
                  'fee'=>$v['total_fee']/100, //交易金额
              );
              $set['endtime']=date('Y-m-d H:i:s',$endtime);
              $set['daytime']=date('Y-m-d H:i:s',$daytime);
              $set['otime']=date('Y-m-d H:i:s',$v['createtime']);
              $set['fit']=accounts_rate($datas); //分润金额
              $set['mch_id']=$v['mch_id']; //商户号
              //取商户名称
              $Mch_name=M('mch_merchant')->where(array('merchantId'=>$v['mch_id']))->getField('merchantName');
              $set['mch_name']=$Mch_name; //商户号
              $set['service']=$v['service']; //服务接口
              $set['TotalFee']=$v['total_fee']/100; //交易金额
              $set['rate']=$v['mch_rate'];
              $fit[]=$set;
          }

          //分页
          $count=count($fit);
          $Page=new \Think\Page($count,10);
          $show = $Page->show();
          $list=array_slice($fit,$Page->firstRow,$Page->listRows);
          $assign=array(
              'fit'=>$list,
              'page'=>$show,
          );
          $this->assign($assign);
          $this->display();

      }

  }

  /*渠道结算*/
  public function accounts(){

      if ($_REQUEST['partner']) {
              //根据ID获取渠道号
              $partner_s = M('mch_parent_api')->where(array('parentid' => $_REQUEST['partner']))->find();
              $map['partner'] = $partner_s['placenum'];
      }
      if ($_REQUEST['status'] == 1) {//已结算
              $map['status'] = 1;
      } elseif ($_REQUEST['status'] == 2) { //全部
      } else {//未结算
              $map['status'] = 0;
      }
      if($_REQUEST['times']){
          $map['acctime'] = strtotime($_REQUEST['times']);
      }

      $Data = M('mch_accounts');
      $count      = $Data->where($map)->count();// 查询满足要求的总记录数
      $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
      $show       = $Page->show();// 分页显示输出
      $list = $Data->order('id')->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

      /*导出xls*/
      $result =M("mch_accounts")->where($map)->order("id desc")->select();

      if(!empty($_POST['export'])&&$_POST['export']=='ccl'){
          $xlsName  = "Fit_Accounts";//导出名称
          $xlsCell  = array(
              array('acctime','日结时间'),
              array('partner','所属渠道'),
              array('mch_id','渠道编号'),
              array('count','交易总笔数'),
              array('sum','交易总金额'),
              array('fit','分润金额'),
              array('status','结算状态'),
              array('fit_time','结算时间'),
          );
          $atitle="交易流水 XLS生成时间:".date('Y-m-d H:i:s');
          $wbscms=array(
              'Atitle'=>$atitle,
          );
          $status=array('1'=>'已结算','0'=>'未结算');
          foreach ($result as $k => $v){
              $xlsData[$k]['acctime']=date('Y-m-d H:i:s',$v['acctime']);
              $xlsData[$k]['partner']=mcha_name($v['partner']);
              $xlsData[$k]['mch_id']=$v['partner'];
              $xlsData[$k]['count']=$v['pay_count'];
              $xlsData[$k]['sum']=$v['total_fee'];
              $xlsData[$k]['fit']=$v['profit'];
              $xlsData[$k]['status']=$status[$v['status']];
              $xlsData[$k]['fit_time']=date('Y-m-d H:i:s',$v['fit_time']);
          }
          $this->exportExcel($xlsName,$xlsCell,$xlsData,$wbscms);
      }


      $assign=array(
          'list' => $list,
          'page'=>  $show,
          'data'=>$_REQUEST
      );
      //dump($list);
      $this->assign($assign);// 赋值分页输出
      $this->display(); // 输出模板

  }

  /*渠道接口信息更新*/
  public function parapi_updata(){
      if(IS_POST){
          $api=M('mch_parent_api');
          $data=I('post.');
          if(empty($data['id'])){
              $this->error('参数非法');
          }
          $res=$api->where(array('parentid'=>$data['id']))->count();
          if($data['type']=='status'){ //更新接口 启用或禁用
              //先判断当前接口是否存在数据库 存在更新 不存在生成
              if($res){//存在
                  $api->where(array('parentid'=>$data['id']))->save(array('status'=>$data['status'],'endtime'=>time()));
              }else{//不存在信息
                  $add=array(
                    'parentid'=>$data['id'],
                    'placenum'=>self::randparent(),
                    'placekey'=>RandStr(32,1),
                    'createtime'=>time(),
                    'opid'=>$_SESSION['user']['id'],
                    'status'=>$data['status']
                  );
                  $api->add($add);
              }
              if($api) {
                  $this->success('状态更新成功!');
              }else{
                  $this->error('状态更新失败!');
              }
          }elseif ($data['type']=='mp'){ //配置公众号配置
              if($res){
                  $save=array(
                    'mpname'=>$data['mpname'],
                    'mpappid'=>$data['mpappid'],
                    'endtime'=>time(),
                    'opid'=>$_SESSION['user']['id'],
                  );
                  $rel=$api->where(array('parentid'=>$data['id']))->save($save);
                  if($rel){
                      $this->success('公众号配置信息更新成功!');
                  }else{
                      $this->error('公众号配置信息更新失败!');
                  }

              }else{
                  $this->error('请先开启渠道状态后操作!');
              }
          }elseif ($data['type']=='code'){ //配置交易识别码
              if($res){
                  $save=array(
                      'parcode'=>$data['parcode'],
                      'parurl'=>$data['parurl'],
                      'endtime'=>time(),
                      'opid'=>$_SESSION['user']['id'],
                  );
                  $rel=$api->where(array('parentid'=>$data['id']))->save($save);
                  if($rel){
                      $this->success('交易识别码配置信息更新成功!');
                  }else{
                      $this->error('交易识别码配置信息更新失败!');
                  }
              }else{
                  $this->error('请先开启渠道状态后操作!');
              }
          }elseif ($data['type']=='rate'){ //T1结算费率
              if($res){
                  //结算费率不能小于2.8
                  if($data['mcha_rate']<2.5){
                      $this->error('渠道费率不能小于我们自己的结算费率哦!');
                  }
                  $save=array(
                      'mcha_rate'=>$data['mcha_rate'],
                      'mcha_rate_type'=>$data['mcha_rate_type'],
                      'endtime'=>time(),
                      'opid'=>$_SESSION['user']['id'],
                  );
                  $rel=$api->where(array('parentid'=>$data['id']))->save($save);
                  if($rel){
                      $this->success('结算配置信息更新成功!');
                  }else{
                      $this->error('结算配置信息更新失败!');
                  }
              }else{
                  $this->error('请先开启渠道状态后操作!');
              }
          }elseif ($data['type']=='rateT0'){ //结算费率
              if($res){
                  //结算费率不能小于2.8
                  if($data['mcha_rateT0']<2.6){
                      $this->error('渠道费率不能小于我们自己的结算费率哦!');
                  }
                  $save=array(
                      'mcha_rateT0'=>$data['mcha_rateT0'],
                      'mcha_rateT0_type'=>$data['mcha_rateT0_type'],
                      'endtime'=>time(),
                      'opid'=>$_SESSION['user']['id'],
                  );
                  $rel=$api->where(array('parentid'=>$data['id']))->save($save);
                  if($rel){
                      $this->success('结算配置信息更新成功!');
                  }else{
                      $this->error('结算配置信息更新失败!');
                  }
              }else{
                  $this->error('请先开启渠道状态后操作!');
              }
          }
      }else{
          $this->error('非法操作');
      }
  }

  /*渠道接口*/
  public function parapi(){
      //dump(RandStr(32,1));
      $data=I('get.');
      $basic=M('mch_basic')->where(array('id'=>$data['id']))->find();
      //ID不能为空
      if(empty($data['id'])){
          $this->error('非法操作');
      }
      if(!$basic){
          $this->error('渠道不存在!');
      }
      //接口信息
      $api=M('mch_parent_api')->where(array('parentid'=>$data['id']))->find();
      $assign=array(
          'basic'=>unserialize($basic['data']), //反序列化解析
          'mchid'=>$data['id'], //ID
          'api'=>$api
      );
      $this->assign($assign);
      $this->display();
  }

  /*渠道编辑*/
  public function edits(){
      if(IS_POST){
          $data=I('post.');
          /*渠道基本信息*/
          $basic=array(
              'JB_parent'=>$data['JB_parent'],
              'JB_province'=>$data['JB_province'],
              'JB_city'=>$data['JB_city'],
              'JB_area'=>$data['JB_area'],
              'JB_address'=>$data['JB_address'],
              'JB_names'=>$data['JB_names'],
              'JB_email'=>$data['JB_email'],
              'JB_phone'=>$data['JB_phone'],
              'JB_other'=>$data['JB_other']
          );

          /*渠道结算信息*/
          $balance=array(
              'JS_bank'=>$data['JS_bank'],
              'JS_cards'=>$data['JS_cards'],
              'JS_cardsname'=>$data['JS_cardsname'],
              'JS_cardstype'=>$data['JS_cardstype'],
              'JS_Province'=>$data['JS_Province'],
              'JS_CityName'=>$data['JS_CityName'],
              'JS_LBnk'=>$data['JS_LBnk'],
              'JS_type'=>$data['JS_type'],
              'JS_cid'=>$data['JS_cid'],
              'JS_phone'=>$data['JS_phone'],
              'JS_lhh'=>$data['JS_lhh']
          );
          /*渠道图片附件信息*/
          $paper=array(
              'Z_YYZZ'=>$data['Z_YYZZ'],
              'Z_JGDMZ'=>$data['Z_JGDMZ'],
              'Z_KHXUZ'=>$data['Z_KHXUZ'],
              'Z_FRSFZ'=>$data['Z_FRSFZ'],
              'Z_QT1'=>$data['Z_QT1'],
              'Z_QT2'=>$data['Z_QT2'],
          );

          //为了渠道的唯一性  渠道名称只能唯一  由于是编辑  先看看是否一样  如果一样就不判断
          $parentname=M('mch_basic')->where(array('id'=>$data['id']))->getField('parentname');
          $reload=M('mch_basic')->where(array('parentname'=>$data['JB_parent']))->count();

          if(($data['JB_parent']!=$parentname)&&$reload){
              $this->error('当前渠道名称已存在!');
          }else {
              //保存信息到数据库
              $addbasic['parentname'] = $data['JB_parent'];
              $addbasic['endtime'] = time();
              $addbasic['opid'] = $_SESSION['user']['id'];
              $addbasic['data'] = serialize($basic);
              $basicid = M('mch_basic')->where(array('id'=>$data['id']))->save($addbasic); //增加渠道信息

              $addbalance['endtime'] = time();
              $addbalance['opid'] = $_SESSION['user']['id'];
              $addbalance['data'] = serialize($balance);
              $balanceid = M('mch_balance')->where(array('parentid'=>$data['id']))->save($addbalance);

              $addpaper['endtime'] = time();
              $addpaper['opid'] = $_SESSION['user']['id'];
              $addpaper['data'] = serialize($paper);
              $paperid = M('mch_paper')->where(array('parentid'=>$data['id']))->save($addpaper);
              if ($basicid && $balanceid && $paperid) {
                  $this->success('渠道编辑成功', U('Admin/Mcha/detail',array('id'=>$data['id'])));
              } else {
                  $this->error('渠道编辑失败');
              }
          }
      }else{
          $data=I('get.');
          if(empty($data['id'])){
              $this->error('非法操作!ERROR Not Id!');
          }else{
              $basic=M('mch_basic')->where(array('id'=>$data['id']))->find();
              //根据ID获取结算信息
              $balance=M('mch_balance')->where(array('parentid'=>$data['id']))->getfield('data');
              //根据ID获取证件图片
              $paper=M('mch_paper')->where(array('parentid'=>$data['id']))->getfield('data');
              //列出所有银行
              $banks=M('mch_bank_list')->order('id asc')->select();
              //列出所有省
              $area=M('mch_areas')->where(array('type'=>2))->order('id asc')->select();
              
              //反序列化解析
              $assign=array(
                  'basic'=>unserialize($basic['data']),
                  'balance'=>unserialize($balance),
                  'paper'=>unserialize($paper),
                  'banks'=>$banks,
                  'area'=>$area,
                  'mchid'=>$data['id'],
              );
              $this->assign($assign);
              $this->display();
          }
      }


  }

  /*渠道详情*/
  public function detail(){
      $data=I('get.');
      if(empty($data['id'])){
          $this->error('非法操作!ERROR Not Id!');
      }else{
          $basic=M('mch_basic')->where(array('id'=>$data['id']))->find();
          //根据ID获取结算信息
          $balance=M('mch_balance')->where(array('parentid'=>$data['id']))->getfield('data');
          //根据ID获取证件图片
          $paper=M('mch_paper')->where(array('parentid'=>$data['id']))->getfield('data');

          //反序列化解析
          $assign=array(
             'basic'=>unserialize($basic['data']),
             'balance'=>unserialize($balance),
             'paper'=>unserialize($paper),
          );
          $this->assign($assign);
          $this->display();
      }
  }

  /*渠道列表*/
  public function lists(){
      $Data = M('mch_basic');
      $count      = $Data->where($map)->count();// 查询满足要求的总记录数
      $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
      $show       = $Page->show();// 分页显示输出
      $list = $Data->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
      //根据ID获取
      $basic=$Data->field('id,data')->order('id asc')->select();
      foreach($basic as $key=>$val){
          $datas[$val['id']]=unserialize($val['data']);
      }
      unset($basic);

      //根据ID取渠道号
      $Api=M('mch_parent_api')->field('parentid,placenum,status')->order('id asc')->select();
      foreach($Api as $key=>$val){
          $Apis[$val['parentid']]=$val['placenum'];
          $Api_status[$val['parentid']]=$val['status'];
      }
      unset($Api);

      $assign=array(
        'Api_status'=>$Api_status,
        'Apis'=>$Apis,
        'bdata'=> $datas,
        'list' => $list,
        'page'=>  $show,
      );
      $this->assign($assign);// 赋值分页输出
      $this->display(); // 输出模板

  }

  /*添加渠道*/
  public function adds(){
      if(IS_POST){
          $data=I('post.');
          /*渠道基本信息*/
          $basic=array(
              'JB_parent'=>$data['JB_parent'],
              'JB_province'=>$data['JB_province'],
              'JB_city'=>$data['JB_city'],
              'JB_area'=>$data['JB_area'],
              'JB_address'=>$data['JB_address'],
              'JB_names'=>$data['JB_names'],
              'JB_email'=>$data['JB_email'],
              'JB_phone'=>$data['JB_phone'],
              'JB_other'=>$data['JB_other']
          );

          /*渠道结算信息*/
          $balance=array(
              'JS_bank'=>$data['JS_bank'],
              'JS_cards'=>$data['JS_cards'],
              'JS_cardsname'=>$data['JS_cardsname'],
              'JS_cardstype'=>$data['JS_cardstype'],
              'JS_Province'=>$data['JS_Province'],
              'JS_CityName'=>$data['JS_CityName'],
              'JS_LBnk'=>$data['JS_LBnk'],
              'JS_type'=>$data['JS_type'],
              'JS_cid'=>$data['JS_cid'],
              'JS_phone'=>$data['JS_phone'],
              'JS_lhh'=>$data['JS_lhh']
          );
          /*渠道图片附件信息*/
          $paper=array(
              'Z_YYZZ'=>$data['Z_YYZZ'],
              'Z_JGDMZ'=>$data['Z_JGDMZ'],
              'Z_KHXUZ'=>$data['Z_KHXUZ'],
              'Z_FRSFZ'=>$data['Z_FRSFZ'],
              'Z_QT1'=>$data['Z_QT1'],
              'Z_QT2'=>$data['Z_QT2'],
          );

          //为了渠道的唯一性  渠道名称只能唯一
          $reload=M('mch_basic')->where(array('parentname'=>$data['JB_parent']))->count();
          if($reload){
              $this->error('当前渠道名称已存在!');
          }else {
              //保存信息到数据库
              $addbasic['parentname'] = $data['JB_parent'];
              $addbasic['createtime'] = time();
              $addbasic['endtime'] = time();
              $addbasic['opid'] = $_SESSION['user']['id'];
              $addbasic['data'] = serialize($basic);
              $basicid = M('mch_basic')->add($addbasic); //增加渠道信息

              $addbalance['parentid'] = $basicid;
              $addbalance['createtime'] = time();
              $addbalance['endtime'] = time();
              $addbalance['opid'] = $_SESSION['user']['id'];
              $addbalance['data'] = serialize($balance);
              $balanceid = M('mch_balance')->add($addbalance);

              $addpaper['parentid'] = $basicid;
              $addpaper['createtime'] = time();
              $addpaper['endtime'] = time();
              $addpaper['opid'] = $_SESSION['user']['id'];
              $addpaper['data'] = serialize($paper);
              $paperid = M('mch_paper')->add($addpaper);
              if ($basicid && $balanceid && $paperid) {
                  $this->success('新增渠道成功', U('Admin/Mcha/lists'));
              } else {
                  $this->error('新增渠道失败');
              }
          }
      }else{
          //列出所有银行
          $banks=M('mch_bank_list')->order('id asc')->select();
          //列出所有省
          $area=M('mch_areas')->where(array('type'=>2))->order('id asc')->select();
          $assign=array(
              'banks'=>$banks,
              'area'=>$area,
          );
          $this->assign($assign);

          $this->display();
      }
  }


    //城市信息
    public function queryBnkCity(){
        $set=$_GET['parentId'];
        $data=M('mch_areas')->where(array('type'=>3,'bms'=>$set))->select();
        $list=array(
            'list'=>$data,
        );
        echo json_encode($list);

    }

    //支行信息
    public function bnkLink(){
        $depositBnk = $_POST['depositBnk'];
        //根据ID筛选银行名称
        $Bnk= M('mch_bank_list')->where(array('bnkCd'=>$depositBnk))->getfield('bnkNm');
        $cityId = $_POST['cityId'];
        //根据ID查出市名
        $cityname=M('mch_areas')->where(array('areaId'=>$cityId))->getfield('areaName');
        //威富通提供的数据库
        $arr['address']=array(array('like',"%".$cityname."%"),array('like',"%".$Bnk."%"),'and');
        $data=M('mch_banks')->where($arr)->select();
        //dump($data);
        $list=array(
            'list'=>$data,
        );
        echo json_encode($list);
    }


    /*生成渠道号*/
    public function randparent(){
        $data=rand(1,3).RandStr(10);
        //根据生成的渠道号进行数据库筛选 避免重复
        $res=M('mch_parent_api')->where(array('placenum'=>$data))->count();
        if($res){
            self::randparent();
        }else{
            $placenum=$data;
        }
        return $placenum;
    }


}