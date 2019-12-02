<?php

namespace Tasks\Controller;

use Think\Controller;

set_time_limit(0);
//ignore_user_abort();
#分润日清算数据
class BenefitController extends Controller
{


	/**
	 * 推荐码注册汇总
	 */
	public function inviteCode(){
		$DomainAuth = M('domainAuth')->where(['status'=>1])->field('web_authcode,id')->select();
		#根据品牌列出代理列表
		foreach ($DomainAuth as $do) {
			$where = [
				'domain_auth' => $do['web_authcode']
			];
			$db = M('mchAgent');
			//获取推荐码信息
			$inviteCode = M('inviteCode')->where($where)->select();

			foreach ($inviteCode as $k => $v) {
				$arr = [];
				//获取当前ID的直推和间推
				$inviteId = inviteCodeId($v['code']);
				//dump($inviteId);
				//获取直推统计
				if ($inviteId['zt']) {
					$zt = $db->where($where)->where(['pay_status' => 1, 'id' => ['in', $inviteId['zt']]])->field('id,grade')->select();
					foreach ($zt as $k1 => $v1) {
						$arr['zt_' . $v1['grade']][] = $v1['id'];
					}
				}
				//获取间推统计
				if ($inviteId['jt']) {
					$jt = $db->where($where)->where(['pay_status' => 1, 'id' => ['in', $inviteId['jt']]])->field('id,grade')->select();
					foreach ($jt as $k2 => $v2) {
						$arr['jt_' . $v2['grade']][] = $v2['id'];
					}
				}
				$total = $this->inviteRateTotal($v['code']);
				if($v['pid']=='0'){
					$ag=[
						'id'=>'99999999',
						'user_name'=>'系统总管理',
						'user_phone'=>'100000000000',
						'grade'=>1
					];
				}else{
					$ag = $db->where($where)->where(['id' => $v['pid']])->find();
				}
				$data = [
					'aid' => $ag['id'],//推荐人ID
					'code' => $v['code'], //推荐人code
					'name' => $ag['user_name'], //推荐人姓名
					'phone' => $ag['user_phone'],//推荐人手机号
					'grade' => $ag['grade'], //推荐人等级
					'grade_name' => gradeName($ag['grade']),//推荐人等级名称
					'zt_1' => count($arr['zt_1']), //服务商直推统计
					'zt_2' => count($arr['zt_2']), //省代直推
					'zt_3' => count($arr['zt_3']), //市代直推
					'zt_4' => count($arr['zt_4']), //区代直推
					'jt_1' => count($arr['jt_1']), //服务商间推
					'jt_2' => count($arr['jt_2']), //省代间推
					'jt_3' => count($arr['jt_3']), //市代间推
					'jt_4' => count($arr['jt_4']), //区代间推
					'total' => $total['total'] ? $total['total'] : '0',
					'create_time' => date('Y-m-d H:i:s'),
					'domain_auth'=>$where['domain_auth']
				];
				dump($data);
				if($data['total']>0||$data['zt_1']>0||$data['zt_2']>0||$data['zt_3']>0||$data['zt_4']>0||$data['jt_1']>0||$data['jt_2']>0||$data['jt_3']>0||$data['jt_4']>0){
					$db2=M('inviteBefitCount');
					if($db2->where(['aid'=>$data['aid']])->count()){
						$db2->where(['aid'=>$data['aid']])->save($data);
					}else{
						$db2->add($data);
					}
				}

			}
		}
	}


	//获取分成总额
	public function inviteRateTotal($code){
		$db=M('mchAgent');
		$inviteId=inviteCodeId($code);
		$ag=$db->where(['pay_status'=>1,'id'=>['in',$inviteId['merge']]])->select();
		$arr=[];
		$total=[];
		foreach ($ag as $k=>$v){
			$type=in_array($v['id'],$inviteId['zt'])?'zt':'jt';
			$tj_grade=inviteCodeAg($v['invite_code'],'grade');
			$rate=inviteRate($type,$tj_grade,$v['grade'])['rate'];
			$reg_total=inviteSetting('reg_a'.$v['grade']);
			$arr[]=[
				'type'=>$type,
				'type_name'=>$type=='zt'?'直推':'间推',
				'grade'=>$v['grade'],
				'tj_grade'=>$tj_grade,
				'rate'=>$rate?$rate.'%':'',
				'rate_total'=>bcdiv($rate*$reg_total,100,2),
			];
			$total[]=bcdiv($rate*$reg_total,100,2);
		}
		return ['arr'=>$arr,'total'=>array_sum($total)];
	}

    #合作伙伴分润
    public function partner()
    {
        #时间范围
        $data = I('get.');
        if ($data['day']) {
            if (strtotime($data['day']) > time()) {
                die('您的时间还没到呢');
            } else {
                $STime = date('Ymd', strtotime($data['day'])); #开始时间
                $ETime = date('Ymd', strtotime($data['day'] . "+1 day")); #结束时间
            }
        } else {
            $STime = date('Ymd', strtotime("-1 day")); #开始时间
            $ETime = date('Ymd'); #结束时间
        }
        if ($data['auth']) {
            $do_where['web_authcode'] = $data['auth'];
        } else {
            $do_where['web_authcode'] = array('EXP', 'IS NOT NULL');
        }
        #先列出品牌
        $DomainAuth = M('Domain_auth')->where($do_where)->field('web_authcode,id')->select();
        #根据品牌列出代理列表
        foreach ($DomainAuth as $do) {
            #状态筛选
            $maps['status'] = 1;
            #所属品牌
            $maps['domain_auth'] = $do['web_authcode'];
            $maps['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            #所有流水
            $Order = M('MchOrders')->where($maps)->select();
            #总笔数
            $count = M('MchOrders')->where($maps)->count();
            #总交易额
            $fee = M('MchOrders')->where($maps)->sum('total_fee');

            #根据流水计算每一条交易的分润
            #定义数组
            $Benefit = array();
            $Money = array();
            foreach ($Order as $row) {
                $fr['day'] = strtotime($STime); //属于哪一天
                $fr['oid'] = $row['id'];
                $fr['mid'] = $row['mid'];
                $fr['store_id'] = $row['store_id'];
                $fr['aid'] = $row['agent_id'];
                $fr['out_trade_no'] = $row['out_trade_no'];
                $fr['alleys'] = $row['alleys'];
                $fr['type'] = $row['type'];
                $fr['total'] = $row['total_fee'];
                #费率相关
                $fr['cost'] = self::PartnerCost($do['id'], $row['alleys']); #所属代理的成本费率
                $fr['term'] = $row['mch_rate']; #此订单的终端费率
                #计算分润
                if(AlleyFrType($row['alleys'])==1){
                    $be = bcsub($fr['term'], $fr['cost'], 2) * 10;
                }else{
                    $be = ((bcsub($fr['term'], $fr['cost'], 2)) * $fr['total']) / 1000;
                }
                $fr['money'] = money_data($be);
                $fr['NoMoney'] = $be;
                $fr['domain_auth'] = $row['domain_auth'];
                $fr['time_end'] = $row['time_end'];
                #数据转化 详细信息存储
                if ($fr['total']) {
                    //dump($list);
                    $OrderDb = M('PartnerFenrunOrder');
                    #判断数据库是否有此记录
                    $FrMap['day'] = $fr['day'];
                    $FrMap['out_trade_no'] = $fr['out_trade_no'];
                    $FrMap['domain_auth'] = $fr['domain_auth'];
                    $FrRes = $OrderDb->where($FrMap)->count();
                    if ($FrRes) {
                        #存在信息
                        $OrderDb->where($FrMap)->save($fr);
                    } else {
                        $OrderDb->add($fr);
                    }
                }
                $Benefit[] = $fr;
                $Money[] = $fr['money'];
            }


            #计算最终结果
            $list['benefit'] = number_format(money_data(array_sum($Money)), 2); //交易总分润
            $list['count'] = $count; //交易总笔数
            $list['fee'] = number_format(money_data($fee), 2); //交易总金额
            $list['mon'] = date('Y-m', strtotime($STime)); //属于哪个月份  根据规则 提前一天算  今天前一天
            $list['day'] = strtotime($STime); //属于哪个月份  根据规则 提前一天算  今天前一天
            $list['s'] = strtotime(date('Y-m-d H:i:s', strtotime($STime))); //开始时间
            $list['e'] = strtotime(date('Y-m-d H:i:s', strtotime($ETime))); //结束时间
            $list['domain_auth'] = $do['web_authcode']; //此次更新时间
            $list['update'] = date('Y-m-d H:i:s'); //此次更新时间
            if ($list['benefit'] != '0.00' || $list['fee'] != '0.00') {
                //dump($list);
                $db = M('PartnerFenrunDays');
                #判断数据库是否有此记录
                $DayMap['day'] = $list['day'];
                $DayMap['domain_auth'] = $list['domain_auth'];
                $Days = $db->where($DayMap)->count();
                if ($Days) {
                    #存在信息 保存
                    $db->where($DayMap)->save($list);
                } else {
                    $db->add($list);
                }
                echo 'Success';
            }


            // dump($list);
        }


    }


    #获取品牌的通道成本
    public function PartnerCost($id, $alleys)
    {
        $res = M('DomainAlleys')->where(array('cid' => $id, 'alleys_type' => $alleys))->getField('rate');
        if ($res) {
            return $res;
        } else {
            return '3';
        }
    }


    #代理分润
    public function agent()
    {
        #时间范围
        $data = I('get.');
        if ($data['day']) {
            if (strtotime($data['day']) > time()) {
                die('您的时间还没到呢');
            } else {
                $STime = date('Ymd', strtotime($data['day'])); #开始时间
                $ETime = date('Ymd', strtotime($data['day'] . "+1 day")); #结束时间
            }
        } else {
            $STime = date('Ymd', strtotime("-1 day")); #开始时间
            $ETime = date('Ymd'); #结束时间
        }
        $agent = M('MchAgent')->order('id desc')->select();
        #代理的层次关系
        #分代理循环
        foreach ($agent as $ag) {
            $_SALE = self::AgentAll($ag['id']);
            #根据代理id筛选流水

            #状态筛选
            $maps['status'] = 1;
            #所属品牌
            $maps['domain_auth'] = $ag['domain_auth'];
            $maps['agent_id'] = array('in', $_SALE);
            $maps['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            #所有流水
            $Order = M('MchOrders')->where($maps)->select();
            #总笔数+
            $count = M('MchOrders')->where($maps)->count();
            #总交易额
            $fee = M('MchOrders')->where($maps)->sum('total_fee');
            #总退款笔数
			$refundCount = M('MchOrders')->where($maps)->where(['service'=>['like','%_refund']])->count();
			#总退款金额
			$refundFee = M('MchOrders')->where($maps)->where(['service'=>['like','%_refund']])->sum('total_fee');
			//dump([$refundCount,$refundFee]);
            #根据流水计算每一条交易的分润
            #定义数组
            $Benefit = array();
            $Money = array();
            foreach ($Order as $row) {
                $fr['day'] = strtotime($STime); //属于哪一天
                $fr['oid'] = $row['id'];
                $fr['mid'] = $row['mid'];
                $fr['store_id'] = $row['store_id'];
                $fr['agent'] = $ag['id'];
                $fr['aid'] = $row['agent_id'];
                $fr['out_trade_no'] = $row['out_trade_no'];
                $fr['alleys'] = $row['alleys'];
                $fr['type'] = $row['type'];
                $fr['name'] = $ag['user_name'];
                $fr['total'] = $row['total_fee'];
                #费率相关
                $fr['cost'] = self::AgentCost($ag['id'], $row['alleys']); #所属代理的成本费率
                $fr['term'] = $row['mch_rate']; #此订单的终端费率

                #计算分润
                if(AlleyFrType($row['alleys'])==1){
                    $be = bcsub($fr['term'], $fr['cost'], 2) * 10;
                }else{
                    $be = ((bcsub($fr['term'], $fr['cost'], 2)) * $fr['total']) / 1000;
                }

                //$be = ((bcsub($fr['term'], $fr['cost'], 2)) * $fr['total']) / 1000;
				//

				$fr['money'] = money_data($be);
				$service=explode('_',$row['service']);
                if($service[1]=='refund'&&$fr['money']>0){ //如果是退款订单 分润为负
					$fr['money'] = '-'.money_data($be);
				}

                $fr['NoMoney'] = $be;
                $fr['domain_auth'] = $row['domain_auth'];
                $fr['time_end'] = $row['time_end'];
                #数据转化 详细信息存储
                if ($fr['total']&&$fr['cost']&&$fr['term']) {
                    //dump($list);
                    $OrderDb = M('MchAgentFenrunOrder');
                    #判断数据库是否有此记录
                    $FrMap['agent'] = $fr['agent'];
                    $FrMap['day'] = $fr['day'];
                    $FrMap['out_trade_no'] = $fr['out_trade_no'];
                    $FrMap['domain_auth'] = $fr['domain_auth'];
                    $FrRes = $OrderDb->where($FrMap)->count();
                    if ($FrRes) {
                        #存在信息
                        $OrderDb->where($FrMap)->save($fr);
                    } else {
                        $OrderDb->add($fr);
                    }
                    $Benefit[] = $fr;
                    $Money[] = $fr['money'];
                }
            }


            #统计每个代理的用户认证费用
            $auth['agent'] = array('in', $_SALE);
            $auth['status'] = 1; //状态必须为支付成功
            $auth['total'] = array('gt', 10); //金额必须大于10  小于10为测试 不计
            $auth['_string'] = "(`time_end`> '" . strtotime($STime) . "') AND ( `time_end` < '" . strtotime($ETime) . "') ";
            //dump($auth);
            $user_auth = M('MchUserAuth')->where($auth)->count(); //统计笔数
            $auth_sum = M('MchUserAuth')->where($auth)->sum('total');//统计总金额

            if ($auth_sum) {
                $_auth_sum = round($auth_sum, 2); //成交总金额
            } else {
                $_auth_sum = 0;
            }
            #总笔数*返佣金额=最终金额
            $auth_fee = $user_auth * $ag['auth_fee'];


            #计算最终结果
            $list['agent'] = $ag['id']; //代理ID
            $list['pid'] = $ag['pid']; //是否子代理
            $list['name'] = $ag['user_name']; //代理姓名
            $list['benefit'] = number_format(round(array_sum($Money), 2), 2); //交易总分润
            $list['count'] = $count; //交易总笔数
            $list['fee'] = number_format(round($fee, 2), 2); //交易总金额
            $list['userauth'] = $refundCount; // 退款笔数
            $list['auth_fee'] = number_format($refundFee, 2);//退款总金额
            //$list['authsum'] = number_format($_auth_sum, 2); //认证总金额
            //$list['authfees'] = number_format($auth_fee, 2); //返佣总金额
            $list['sums'] = number_format(round(array_sum($Money) + $auth_fee, 2), 2); //交易分润+返佣分润;
            $list['mon'] = date('Y-m', strtotime($STime)); //属于哪个月份  根据规则 提前一天算  今天前一天
            $list['day'] = strtotime($STime); //属于哪个月份  根据规则 提前一天算  今天前一天
            $list['s'] = strtotime(date('Y-m-d H:i:s', strtotime($STime))); //开始时间
            $list['e'] = strtotime(date('Y-m-d H:i:s', strtotime($ETime))); //结束时间
            $list['domain_auth'] = $ag['domain_auth']; //此次更新时间
            $list['update'] = date('Y-m-d H:i:s'); //此次更新时间
            // rwlog('fenrun',$list);

            if ($list['benefit'] != '0.00' || $list['fee'] != '0.00' || $list['auth_fee'] != '0.00') {
                //dump($list);
                $db = M('MchAgentFenrunDays');
                #判断数据库是否有此记录
                $DayMap['agent'] = $list['agent'];
                $DayMap['day'] = $list['day'];
                $DayMap['domain_auth'] = $list['domain_auth'];
                $Days = $db->where($DayMap)->count();
                if ($Days) {
                    #存在信息
                    #保存
                    $res = $db->where($DayMap)->save($list);
                } else {
                    $res = $db->add($list);
                }
                //rwlog('fenrun',$res);
                //echo 'Success';
            }
                
        }
    }


    #根据代理ID 支付通道 获取成本费率
    public function AgentCost($id, $alleys)
    {
        $rate = M('MchAgent')->where(array('id' => $id))->getField('rate');
        $rate = unserialize($rate);
        return $rate[$alleys . '_cost'];
    }

    #代理层次
    public function AgentAll($id)
    {
        $data = self::getAll($id, true);
        return $data;
    }

    public function getAll($categoryID, $type)
    {
        //初始化ID数组
        $array[] = $categoryID;
        do {
            $ids = '';
            $where['pid'] = array('in', $categoryID);
            $cate = M('MchAgent')->where($where)->select();
            foreach ($cate as $k => $v) {
                $array[] = $v['id'];
                $ids .= ',' . $v['id'];
            }
            $ids = substr($ids, 1, strlen($ids));
            $categoryID = $ids;
        } while (!empty($cate));
        $ids = implode(',', $array);
        if ($type) {
            return $array; //返回数组
        } else {
            return $ids;    //  返回字符串
        }
    }






}