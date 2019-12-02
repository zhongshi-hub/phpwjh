<?php
namespace Mch\Controller;
use Mch\Controller\InitBaseController;
/*
 * 信用卡代还
 * */
class RepayController extends InitBaseController
{
    protected  $Repay;
    public function _initialize()
    {
        parent::_initialize();
        Vendor('XunTotal/Repay');
        $this->Repay=new \Repay();
    }


    //余额提现
    public function tx(){
        $this->display();
    }

    //根据计划ID获取详情链接
    public function pastUrl(){
        $repayDb=M('MchRepay');
        $map=[
            'mid'=>$_SESSION['mch']['id'],
            'id'=>I('post.Id'),
            'domain_auth' => domain_auth(),
            'type'=>'planData',
        ];
        $repayData=$repayDb->where($map)->find();
        $dataJson=json_decode($repayData['data_json'],true);
        if($repayData){
            $arr=[
                'planId' => $repayData['plan_id'],
                'count'=>$dataJson['repayCount']+$dataJson['payCount'],
                'cardId'=>$repayData['card_id']
            ];
            $url=U('repayDetail',['data'=>Xencode(json_encode($arr))]);
            $this->success('获取详情成功',$url);
        }else{
            $this->error('获取详情失败');
        }
    }
    //往期计划
    public function pastData(){
        $repayDb=M('MchRepay');
        $map=[
          'mid'=>$_SESSION['mch']['id'],
          'card_id'=>I('get.bindId'),
          'domain_auth' => domain_auth(),
          'type'=>'planData',
        ];
        $repayData=$repayDb->where($map)->field('id,mid,card_id,plan_id,total,day,time')->select();
        if(!$repayData){
            $this->error('当前卡还没有往期计划哦');
        }else{
            $this->assign('repayData',$repayData);
            $this->display();
        }

    }

    //代还信用卡列表
    public function index(){
        C('TOKEN_ON', false);
        $this->display();
    }

    //制定计划页面
    public function settingRepay(){
        //如果有计划直接跳转到详细计划页面
        $repayDb=M('MchRepay');
        $map=[
            'mid'=>$_SESSION['mch']['id'],
            'card_id'=>I('get.bindId'),
            'domain_auth' => domain_auth(),
        ];
        //是否有计划
        $IsPlanData=$repayDb->where($map)->where(['data_type'=>0,'status'=>0])->find();
        if($IsPlanData){
            $count=$repayDb->where($map)->where(['plan_id'=>$IsPlanData['plan_id']])->count();
            $arr=[
                'planId' => $IsPlanData['plan_id'],
                'count'=>$count,
                'cardId'=>I('get.bindId')
            ];
            $url=U('repayDetail',['data'=>Xencode(json_encode($arr))]);
            redirect($url);
        }

        //获取银行卡信息
        $map = array(
            'id'=>I('get.bindId'),
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res=M('MchCardBank')->where($map)->find();
        if(!$res){
            $this->error('银行卡信息获取失败！');
        }else {
            //获取银行名称及图标
            $yh = M('MchBankList')->where(array('bnkcd' => $res['bank']))->field('bnknm,bnkcd')->find();
            //视图信息
            $assign= array(
                'cardName'=>$res['name'],
                'bank_name' => $yh['bnknm'] . '信用卡',
                'bank_logo' => '/Source/Image/bank/' . $yh['bnkcd'] . '.png',
                'card' => substr($res['card'], -4),
                'id' => $res['id'],
            );
            $this->assign($assign);
            $this->display();
        }
    }

    //是否银联绑定
    public function cardBindRepay(){
        //通道形式  此处只做扩展
        $res=R('Pays/RepayApi/gateWay',[['api'=>'card_bind','data'=>'ds','card_id'=>I('post.bind_id')]]);
        if($res['status']==1){ //需要开通
            $arr=['code'=>3,'msg'=>'立即去绑定','data'=>$res];
        }elseif ($res['status']==2){
            //是否有计划
            $arr=['code'=>1,'msg'=>'已开通绑定','url'=>U('settingRepay',['bindId'=>I('post.bind_id')])];
        }else{
            $arr=['code'=>0,'msg'=>$res['msg']];
        }
        die(json_encode($arr,JSON_UNESCAPED_UNICODE));
    }

    //计划终止
    public function PlanEnd(){
        $data=json_decode(Xdecode(I('post.data')),true);
        $where=[
            'mid'=>$_SESSION['mch']['id'],
            'card_id'=>$data['cardId'],
            'plan_id'=>$data['planId'],
            'domain_auth'=>domain_auth()
        ];
        $MchRepay=M('MchRepay');
        $day=$MchRepay->where($where)->save(['status'=>1]);
        if($day){
            $this->success('计划终止成功');
        }else{
            $this->error('计划终止失败');
        }
    }
    //计划详情
    public function repayDetail(){
        $data=json_decode(Xdecode(I('get.data')),true);
        $where=[
            'mid'=>$_SESSION['mch']['id'],
            'card_id'=>$data['cardId'],
            'plan_id'=>$data['planId'],
            'domain_auth'=>domain_auth(),
            'data_type'=>0,
        ];
        $MchRepay=M('MchRepay');
        $day=$MchRepay->group('day')->where($where)->getField('day',true);

        $repayMoney=$MchRepay->where($where)->where(['type'=>'repay'])->sum('total');//还款总金额
        $avall=$MchRepay->where($where)->where(['type'=>'repay'])->count();//还款期数
        $payCount=$MchRepay->where($where)->where(['type'=>'pay'])->count();//消费期数
        $plan=[];
        foreach ($day as $k=>$v){
            //还款总金额
            $repay=$MchRepay->where($where)->where(['type'=>'repay','day'=>$v])->sum('total');
            //消费总金额
            $pay=$MchRepay->where($where)->where(['type'=>'pay','day'=>$v])->sum('total');
            //当日消费期数
            $payCounts=$MchRepay->where($where)->where(['type'=>'pay','day'=>$v])->count();
            //当日还款期数
            $repayCount=$MchRepay->where($where)->where(['type'=>'repay','day'=>$v])->count();
            //详细数据
            $ResData=$MchRepay->where($where)->where(['day'=>$v])->select();
            $plan[$k] = [
                'day' => $v,//天数
                'repay' => round($repay,2),//还款总金额
                'pay' => round($pay,2),//消费总金额
                'payCount'=>$payCounts,//当日消费期数
                'repayCount'=>$repayCount,//当日还款期数
                'data' => $ResData //计划数据
            ];
        }

        $map = array(
            'id'=>$data['cardId'],
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res=M('MchCardBank')->where($map)->find();
        $status=$MchRepay->distinct(true)->where($where)->getField('status',true);
        if(!in_array(2,$status)&&count($status)>1){ //数据架构状态 只允许一个   要么全是1(成功) 要么全是0(正常)
            $this->error('数据出错！请联系管理员！');
        }
        //获取失败原因
        $errMsg=$MchRepay->where($where)->where(['api_status'=>2])->order('id desc')->getField('api_msg');
        //获取是否有失败的记录
        $repayError=self::repayError($data);
        //判断是否有记录
        $IsRepayError=$MchRepay->where($where)->where(['status'=>2,'total'=>$repayError['total']])->count();
        //dump($repayError);

        $EndRepay=[
            'total'=>$repayMoney,//还款总金额
            'repayCount'=>$avall,//还款期数
            'payCount'=>$payCount,//消费期数
            'data'=>$plan,
            'card'=>substr($res['card'], -4),
            'planData'=>I('get.data'),//还款详情
            'status'=>$status[0],//计划状态 1终止 0正常
            'errorMsg'=>$errMsg,//终止原因 取最后失败的接口返回消息
            'reloadUrl'=>U('settingRepay',['bindId'=>$data['cardId']]),
            'repayError'=>$repayError,
            'IsRepayError'=>$IsRepayError
        ];
        //dump($repayError);
        $this->assign('planList',$EndRepay);
        $this->display();
    }

    //手动还款操作
    public function handRepay(){
        $data = json_decode(Xdecode(I('post.data')), true);
        $total=self::repayError($data);
        if($total['status']==1&&$total['total']>100){
            //创建一个还款记录
            $arr=[
                'mid' => $_SESSION['mch']['id'],
                'plan_id' => $data['planId'],
                'card_id' => $data['cardId'],
                'total' => $total['total'],
                'type' => 'repay',
                'day' => date('Y-m-d'),
                'time' => date('H:i',time()+60),
                'times'=>date('Y-m-d H:i'),
                'domain_auth' => domain_auth(),
                'status'=>2
            ];
            //提交支付请求
            $MchRepay=M('MchRepay')->add($arr);
            if($MchRepay){
                $this->success('手动还款任务已提交');
            }
        }else{
            $this->error('无法提交手动还款操作 （'.$total['msg'].$total['total'].')');
        }
    }
    //计划终止且消费了 金额汇总
    public function repayError($data=[])
    {
        //获取代还的费率
        $mchRate=M('mch_seller_card_alleys')->where(['cid'=>$_SESSION['mch']['id'],'domain_auth'=>domain_auth(),'alleys_type'=>'Rhcard'])->getField('rate');
        //$data = json_decode(Xdecode(I('get.data')), true);
        $where=[
            'mid'=>$_SESSION['mch']['id'],
            'card_id'=>$data['cardId'],
            'plan_id'=>$data['planId'],
            'domain_auth'=>domain_auth(),
            'data_type'=>0,
            'status'=>1, //终止状态
        ];
        $MchRepay=M('MchRepay');
        //获取到所有成功及失败的数据
        $SuccessErrData=$MchRepay->where($where)->where(['api_status'=>['in','1,2']])->field('total,type,day,time,api_status')->select();
        if($SuccessErrData){
            if(count($SuccessErrData)<=1){
               $returnData=['status'=>0,'msg'=>'没有成功了记录'];
            }else{//超过了
                //是否有过还款成功的
                $repaySuccess=$MchRepay->where($where)->where(['type'=>'repay','api_status'=>1])->field('times')->select();
                if($repaySuccess) {//组合一下时间
                    $dayTime = [];
                    foreach ($repaySuccess as $v) {
                        $dayTime[] = strtotime($v['times']);
                    }
                    //取最大(最近)的还款日期
                    $maxDayTime = max($dayTime);
                }else{
                    //如果没有还款成功的 说明起始都是消费
                    $min=$MchRepay->where($where)->where(['type'=>'pay','api_status'=>1])->field('times')->select();
                    $dayTime = [];
                    foreach ($min as $v) {
                        $dayTime[] = strtotime($v['times']);
                    }
                    $maxDayTime = strtotime(date('YmdHis',min($dayTime)))-60;
                }
                //获取失败的数据 只判断一条  只要有一条失败 计划就终止了
                $repayEroor=$MchRepay->where($where)->where(['api_status'=>2])->field('times')->find();
                //获取区间的记录
                $map['_string'] = "(unix_timestamp(times) > ".$maxDayTime.") AND (unix_timestamp(times) < ".strtotime($repayEroor['times']).")";
                $res=$MchRepay->where($where)->where($map)->field('total')->select();
                $res = array_reduce($res, function ($result, $value) {
                    return array_merge($result, array_values($value));
                }, array());
                if(is_null($res)){
                    $returnData=['status'=>0,'msg'=>'没有消费成功切还款失败了记录'];
                }else{
                    $total=round(array_sum($res),2);
                    //费率转换
                    $total=round($total-($total*($mchRate/1000)),2);
                    if($total>100) {
                        $returnData = ['status' => 1, 'msg' => '有还款失败且消费成功的记录', 'total' => $total,'rate'=>$mchRate];
                    }else{
                        $returnData = ['status' => 0, 'msg' => '有还款失败且消费成功的记录,但金额必须大于100元才可', 'total' => $total];
                    }
                }
            }
        }else{
            $returnData=['status'=>0,'msg'=>'还在运行中或手动终止'];
        }

        return $returnData;
    }




    //计划结果
    public function planStatus(){
        //根据信息查询结果
        $data=json_decode(Xdecode(I('get.data')),true);
        //查询状态
        $where=[
            'mid'=>$_SESSION['mch']['id'],
            'card_id'=>$data['cardId'],
            'plan_id'=>$data['planId'],
            'domain_auth'=>domain_auth(),
            'data_type'=>0
        ];
        $res=M('MchRepay')->where($where)->count();
        if($res!=$data['count']){
            $this->error('获取计划结果失败');
        }
        $map = array(
            'id'=>$data['cardId'],
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res=M('MchCardBank')->where($map)->find();
        $assign=[
          'card'=>substr($res['card'], -4),
          'repayDetail'=>U('repayDetail',['data'=>I('get.data')]),//还款详情
        ];
        $this->assign($assign);
        $this->display();

    }


    //计划确认
    public function PlanSave(){
        $planId=Xdecode(I('post.planId'));
        $cardId=I('post.cardId');
        $PlanList=json_decode(S($planId),true);
        $saveData=[];
        $plan_id='D'.date('YmdHis'); //期数ID
        foreach ($PlanList['data'] as $k=>$v){
            $_saveData=[];
            foreach ($v['data'] as $kk=>$vv) {
                $_saveData[$kk] = [
                    'mid' => $_SESSION['mch']['id'],
                    'plan_id' => $plan_id,
                    'card_id' => $cardId,
                    'total' => $vv['total'],
                    'type' => $vv['type'],
                    'day' => $v['day'],
                    'time' => $vv['time'],
                    'times'=>$v['day'].' '.$vv['time'],
                    'domain_auth' => domain_auth(),
                ];
            }
            $saveData[]=$_saveData;
        }
        $result = array_reduce($saveData, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());
        //防止重复计划 先查询期数
        $repayDb=M('MchRepay');
        $where=[
          'mid'=>$_SESSION['mch']['id'],
          'card_id'=>$cardId,
          'status'=>0,
          'data_type'=>0
        ];
        $rel=$repayDb->where($where)->count();
        if($rel){//如果有说明还有计划在执行
            $returnMsg=['code'=>0,'msg'=>'已有计划在进行中'];
        }else {
            //增加汇总数据
            $addData=[
                'mid' => $_SESSION['mch']['id'],
                'plan_id' => $plan_id,
                'card_id' => $cardId,
                'total' => $PlanList['total'],
                'type' =>'planData',
                'day' => date('Ymd'),
                'time' => date('H:i:s'),
                'data_type'=>1,
                'domain_auth' => domain_auth(),
                'data_json'=>json_encode($PlanList),
            ];
            $repayDb->add($addData);
            //增加详细数据
            $res = $repayDb->addall($result);
            if($res) {
                //计划创建成功后 清空预览计划
                S($planId,null);
                //返回结果
                $returnMsg = ['code' => 1, 'msg' => '计划创建成功', 'data' => $res, 'url' => U('planStatus', ['data'=>Xencode(json_encode(['planId' => $plan_id,'count'=>count($result),'cardId'=>$cardId]))])];
            }else{
                $returnMsg=['code'=>0,'msg'=>'计划创建失败'];
            }
        }
        die(json_encode($returnMsg));
    }
    //预览还款计划
    public function previewPlan(){
        $id=Xdecode(I('get.dataId'));
        $cardId=I('get.cardId');
        if(empty($cardId)){$this->error('参数出错，请重新生成计划');}
        if(empty($id)){$this->error('参数出错，请重新生成计划');}
        $PlanList=json_decode(S($id,'',array('type'=>'file')),true);
        if(!$PlanList){$this->error('获取计划失败 请重新提交生成还款计划');}
        $assign=[
            'planId'=>I('get.dataId'),
            'cardId'=>$cardId,
            'planList'=>$PlanList,
            'callBackUrl'=>U('settingRepay',['bindId'=>$cardId])
        ];
        $this->assign($assign);
        //dump($id);
        //dump(json_decode(S($id,'',array('type'=>'file')),true));
        $this->display();
    }

    //生成还款计划前判断
    public function previewPlanStart(){
        if(IS_POST) {
            //基本数据
            $repayMaxDay = 10; //还款最大天数
            $repayMaxFee = '50000';//还款额最大50000
            //判断数据不能为空
            $post = I('post.');
            $repayMoney = $post['repayMoney']; //还款金额
            $avalibleBalance = $post['avalibleBalance'];//本金
            if($avalibleBalance>$repayMoney){
                $returnData = ['code' => 0, 'msg' => '本金不得大于还款金额'];
                die(json_encode($returnData));
            }
            if (!$repayMoney) {
                $returnData = ['code' => 0, 'msg' => '还款金额不能为空'];
                die(json_encode($returnData));
            }
            if (!$post['cardId']) {
                $returnData = ['code' => 0, 'msg' => '获取还款卡ID失败'];
                die(json_encode($returnData));
            }
            if (!$post['repayDay']) {
                $returnData = ['code' => 0, 'msg' => '请选择还款日'];
                die(json_encode($returnData));
            }
            if (empty($post['repayPeriodDays'])) {
                $returnData = ['code' => 0, 'msg' => '请选择代还日期'];
                die(json_encode($returnData));
            }
            //系统最大金额
            if (floor($post['repayMoney'])> $repayMaxFee) {
                $returnData = ['code' => 0, 'msg' => '还款金额最大5万,请减少还款额'];
                die(json_encode($returnData));
            }
            //还款本金不能低于500
            if($avalibleBalance<500){
                $returnData = ['code' => 0, 'msg' => '还款本金不得低于500元'];
                die(json_encode($returnData));
            }
            //本金不得低于还款额的10%
            if($avalibleBalance<floor($repayMoney/10)){
                $returnData = ['code' => 0, 'msg' => '还款本金不得低于还款额的10%'];
                die(json_encode($returnData));
            }
            //计算还款天数
            $_repayPeriodDays = explode(',', $post['repayPeriodDays']);
            //统计已选天数
            $repayPeriodDays = count($_repayPeriodDays);
            //计算当前金额最小还款天数
            $repayMinDay = $post['repayMoney'] <= 5000 ? 2 : ceil($post['repayMoney'] / 5000);
            //选择的天数是否是在最小到最大天数内
            if ($repayPeriodDays < $repayMinDay || $repayPeriodDays > $repayMaxDay) {
                $returnData = ['code' => 0, 'msg' => '代还日期需为' . $repayMinDay . '-' . $repayMaxDay . '天内'];
                die(json_encode($returnData));
            }

            //循环重置已选天数为年月日
            $repayPeriodDaysYmd = [];
            foreach ($_repayPeriodDays as $k => $v) {
                if ($v < date('d')) {
                    $repayPeriodDaysYmd[$k] = date('Y-m-d', strtotime(date('Y/m/', strtotime('+1 month')) . $v));
                } else {
                    $repayPeriodDaysYmd[$k] = date('Y-m-d', strtotime(date('Y/m/') . $v));
                }
            }

            //时间转换为时间戳
            $repayPeriodDaysTime=[];
            foreach ($repayPeriodDaysYmd as $k=>$v){
                $repayPeriodDaysTime[$k]=strtotime($v);
            }
            //如果计划设置时间为当天17点后 则不能选择当天为还款期
            if(time()>strtotime(date('Ymd'.'17:00'))&&date('Ymd',min($repayPeriodDaysTime))==date('Ymd',time())){
                $returnData = ['code' => 0, 'msg' => '当天超过17点则不算还款日期，请重新选择还款日期'];
                die(json_encode($returnData));
            }


            //根据金额生成还款详细
            $maxFee = floor(floor($repayMoney / $repayPeriodDays) * 0.07) + floor($repayMoney / $repayPeriodDays);//最大金额
            $minFee = floor($maxFee - ($maxFee * 0.2));;//最小金额
            $RepayArr = $this->Repay->create($repayMoney / 100, $minFee / 100, $maxFee / 100, $repayPeriodDays);
            rwlog('test',$RepayArr);
            if ($RepayArr['status'] != 1) {
                $returnData = ['code' => 0, 'msg' => $RepayArr['msg']];
                die(json_encode($returnData));
            }
            rsort($RepayArr['data']);
            //获取商户费率
            $map = [
                'cid' => $_SESSION['mch']['id'],
                'domain_auth' => domain_auth(),
                'alleys_type' => 'Rhcard',//所属通道
            ];
            $rate = M('MchSellerCardAlleys')->where($map)->getField('rate');
            //生成还款计划
            $oddFee = '2';//单笔代付费
            $plan = [];
            foreach ($repayPeriodDaysYmd as $k => $v) {
                $RepayNum = ceil($RepayArr['data'][$k] / $avalibleBalance);
                if($RepayNum<2){
                    $RepayNum=2;
                }
                $RepayMaxFee = floor(floor($RepayArr['data'][$k] / $RepayNum) * 0.07) + floor($RepayArr['data'][$k] / $RepayNum);
//            $payFee_max=$RepayMaxFee<1000?$RepayMaxFee:999;
                $RepayMinFee = floor($RepayMaxFee - ($RepayMaxFee * 0.2));
                $planData = self::RepayCount(['total' => $RepayArr['data'][$k], 'min' => $RepayMinFee, 'max' => $RepayMaxFee, 'num' => $RepayNum, 'rate' => $rate / 1000, 'oddFee' => $oddFee]);
                $result = array_reduce($planData['resData'], function ($result, $value) {
                    return array_merge($result, array_values($value));
                }, array());
                //计算总笔数
                $Num=count($result);
                $TimeArr=$this->Repay->create_date_array($Num);
                $ResData=[];
                foreach ($result as $k2=>$v2){
                    $ResData[]=[
                        'type'=>$v2['type'],
                        'total'=>$v2['total'],
                        'time'=>$TimeArr[$k2]
                    ];
                }
                //计算出消费总额
                $xfTotal = [];
                foreach ($result as $_k => $_v) {
                    if ($_v['type'] == 'pay') {
                        $xfTotal[] = $_v['total'];
                    }
                }
                //还款期数汇总
                $avall[]=$planData['repayCount'];
                //消费期数
                $payCount[]=count($xfTotal);
                //生成计划
                $plan[$k] = [
                    'day' => $v,//天数
                    'repay' => $RepayArr['data'][$k],//还款总金额
                    'pay' => array_sum($xfTotal),//消费总金额
                    'payCount'=>count($xfTotal),//当日消费期数
                    'repayCount'=>$planData['repayCount'],//当日还款期数
                    'data' => $ResData //计划数据
                ];
            }

            $EndRepay=[
              'total'=>$repayMoney,//还款总金额
              'avall'=>$avalibleBalance,//还款本金
              'repayCount'=>array_sum($avall),//还款期数
              'payCount'=>array_sum($payCount),//消费期数
              'data'=>$plan,
            ];
//            dump($EndRepay);
            $cacheId='RepayId-'.$post['cardId'];
            S($cacheId,NULL);
            $res=S($cacheId,json_encode($EndRepay),60*60*3); //预览计划存缓存 有效期3小时
            if($res){
                $returnData = ['code' => 1, 'msg'=>'计划生成成功','dataId' => $res,'url'=>U('previewPlan',['cardId'=>$post['cardId'],'dataId'=>Xencode($cacheId)])];
                die(json_encode($returnData));
            }
        }else{
            $this->error('非法操作');
        }
    }


    //独立计算消费还款明细方法
    public function RepayCount($data=[]){
        $RepayArr = $this->Repay->create($data['total']/100, $data['min']/100, $data['max']/100, $data['num']);
        if($RepayArr['status']==1) {
            rsort($RepayArr['data']); //还款金额
            //根据还款金额生成消费笔数
            $resMaxFee='950';//设置金额最大基数
            $res_data=[];
            $RepayArrCount=count($RepayArr['data']);
            foreach ($RepayArr['data'] as $k=>$v){
                //根据当前金额生成笔数 最小 最大金额
                $_count=ceil($v/$resMaxFee);
                $_max=floor(floor($v/$_count)*0.07)+floor($v/$_count);
                $_min=floor($_max-($_max*0.2));
                //计算还款金额
                if($v<$resMaxFee){ //还款额小于设置的最大基数的话 不再进行拆分
                    $payData=self::payRateFee([$v],$data); //计算返回的代付笔数明细
                }else{
                    //进行金额拆分
                    $RepayData=$this->Repay->create($v/100, $_min/100, $_max/100,$_count);
                    $payData=self::payRateFee($RepayData['data'],$data); //计算返回的代付笔数明细
                }
                $payEnd=[];
                foreach ($payData as $k1=>$v1){
                    $payEnd[]=[
                        'type'=>'pay',
                        'total'=>round($v1,2),
                    ];
                }
                $res_data[$k]=array_merge($payEnd,[['type'=>'repay','total'=>$v]]);
            }
            return ['repayCount'=>count($RepayArr['data']),'resData'=>$res_data];
        }
        else{
            return $RepayArr;
        }
     }

    //计算代扣金额  费率后
    public function payRateFee($pay,$data){
        $payFee=[];
        foreach ($pay as $k=>$v){
            //代付费计算   金额+(金额*费率)+代付费
            $end_xf_data=round(($v+($v*$data['rate'])+$data['oddFee']),1);
            //防止金额溢出 反汇算
            $payFee[]=round((($v-($end_xf_data-(($end_xf_data*$data['rate'])+$data['oddFee'])))+$end_xf_data)+0.3,1);

        }
        return $payFee;
    }


    //获取银行卡信息
    public function cardList(){
        #根据ID获取银行卡列表
        $db = M('MchCardBank');
        $map = array(
            'mid' => $_SESSION['mch']['id'],
            'domain_auth' => domain_auth(),
        );
        $res = $db->where($map)->order('id desc')->select();
        if ($res) {
            $repayDb=M('MchRepay');
            $bank = array();
            foreach ($res as $v) {
                $yh = M('MchBankList')->where(array('bnkcd' => $v['bank']))->find();
                //当前卡还款状态
                $map=[
                  'mid'=>$_SESSION['mch']['id'],
                  'card_id'=>$v['id'],
                  'domain_auth' => domain_auth(),
                ];

                $IsPlanData=$repayDb->where($map)->count();
                //是否有计划
                if($IsPlanData){
                    $Data=$repayDb->where($map)->where(['data_type'=>1])->getField('plan_id',true);
                    //dump($Data);
                    $DataTime=[];
                    foreach (array_unique($Data) as $k1=>$v1){
                        $DataTime[$k1]=strtotime(substr($v1,1));
                    }
                    $planId='D'.date('YmdHis',max($DataTime));
                    //dump(date('YmdHis',max($DataTime)));
                    //取出当前最大的日期为最近计划 然后取计划ID
                    //$planId=$repayDb->where($map)->where(['data_type'=>0,'day'=>date('Y-m-d',max($DataTime))])->getField('plan_id');
                    //dump($planId);
                    //根据计划ID 统计当前计划的笔数数据
                    $day=$repayDb->where($map)->where(['data_type'=>0,'plan_id'=>$planId])->getField('day',true);
                    $day_time=[];
                    foreach (array_unique($day) as $k1=>$v1){
                        $day_time[$k1]=strtotime($v1);
                    }
                    $_text='共'.count($day).'期   '.date('m/d',min($day_time)).'至'.date('m/d',max($day_time)).'还清';
                    //判断状态
                    $DayStatus=$repayDb->where($map)->where(['data_type'=>0,'plan_id'=>$planId,'status'=>0])->count();
                    //成功状态
                    $DaySuccess=$repayDb->where($map)->where(['data_type'=>0,'plan_id'=>$planId,'status'=>2])->count();
                    if($DayStatus){
                        $status=1; //已设置计划
                        $text='已设置 '.$_text;
                    }elseif ($DaySuccess){
                        $status=3; //已设置计划
                        $text='本期还款完成 '.$_text;
                    }else{
                        $status=2; //已设置计划
                        $text='计划已终止 '.$_text;
                    }
                }else{
                    $status=0; //未设置计划
                    $text='请及时设置本月代还款 点击定制计划';
                }
                //计划终止后 数据
                $count=$repayDb->where($map)->where(['plan_id'=>$planId])->count();
                $arr=[
                    'planId' => $planId,
                    'count'=>$count,
                    'cardId'=>$v['id']
                ];
                $url=U('repayDetail',['data'=>Xencode(json_encode($arr))]);



                $bank[] = array(
                    'bank_name' => $yh['bnknm'] . '信用卡',
                    'bank_logo' => '/Source/Image/bank/' . $yh['bnkcd'] . '.png',
                    'card' => substr($v['card'], -4),
                    'id' => $v['id'],
                    'status'=>$status,
                    'text'=>$text,
                    'zzUrl'=>$url,
                );
            }
            $this->success($bank);
        } else {
            $this->error('未添加卡');
        }
    }



}