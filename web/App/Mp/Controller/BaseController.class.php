<?php
namespace Mp\Controller;
use Think\Controller;
/**
 * Base基类控制器
 */
class BaseController extends Controller{

    public function _initialize(){
        $rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $nauth=array(
            'Mp/Login/index', //登录页面
            'Mp/Login/loadVerify', //验证码
            'Mp/Login/out'    //退出登录
        );
        if (!in_array($rule_name, $nauth)) {
            //验证用户状态
            if(empty($_SESSION['mp'])){
                $this->redirect('/mp/login');
                //$this->error('登录超时,请重新登录',U('Login/index',array('callUrl'=>base64_encode(__SELF__))));
            }
        }

        #全局渠道域名授权参数
        $domain=domain_rel();
        //$this->_domain=M('domain_auth')->where(array('web_domain'=>$domain))->find();
        if(!$this->_domain){
            $content='服务未授权!请联系管理员!';
            die($content);
        }
        if($this->_domain['status']!=1){
            $content='服务已被停止!请联系专员!';
            die($content);
        }
        #全局设置主题
        set_theme($this->_domain['theme']);
        $assign=array(
            '_domain'=>$this->_domain,
            '_menu'=>$this->menu(),
            '_rule_name'=>strtolower($rule_name),
            '_menu_name'=>$this->_menu_data(strtolower($rule_name))
        );
        $this->assign($assign);
    }



    public function menu(){
        return [
            'default'=>[
                [
                    'name'=>'控制台',
                    'url'=>'mp/index/index',
                    'ico'=>'fi-air-play'
                ],
                [
                    'name'=>'门店管理',
                    'url'=>'mp/merchant/store',
                    'ico'=>'fi-layout',
                    'list'=>[

                    ]
                ],
				[
					'name'=>'客户管理',
					'url'=>'mp/member',
					'ico'=>'fi-heart',
					'list'=>[
						[
							'name'=>'我的会员',
							'url'=>'mp/member/index'
						],
						[
							'name'=>'会员营销',
							'url'=>'mp/member/activity',
						]
					]
				],
				[
					'name'=>'交易管理',
					'url'=>'mp/order/index',
					'ico'=>'fi-bar-graph-2',
				],
                [
                    'name'=>'基本信息',
                    'url'=>'mp/user/index',
                    'ico'=>'fi-head',
                    'list'=>[
                        [
                            'name'=>'基本资料',
                            'url'=>'mp/user/index'
                        ],
                        [
                            'name'=>'修改密码',
                            'url'=>'mp/user/pass',
                        ],
//						[
//							'name'=>'流量管理',
//							'url'=>'mp/user/flow',
//						],
                        [
                            'name'=>'商户费率',
                            'url'=>'mp/user/rate',
                        ]
                    ]
                ]
            ],
//            'plug'=>[
//                [
//                    'name'=>'API信息',
//                    'url'=>'mp/mch_api/index',
//                    'ico'=>'fi-globe'
//                ]
//            ]
        ];
    }


    /**
     * 检索菜单名称
     * @param $key
     * @return mixed
     */
    public function _menu_data($key){
        $result = [];
        array_map(function ($value) use (&$result) {
            $result = array_merge($result, array_values($value));
        }, $this->menu());
        $res=[];
        foreach ($result as $k=>$v){
            if($v['list']){
                foreach ($v['list'] as $a=>$b){
                    $res[] = [
                        'url' =>$b['url'],
                        'name'=>$b['name']
                    ];
                }
            }else {
                $res[] = [
                    'url' =>$v['url'],
                    'name'=>$v['name']
                ];
            };
        }
        $_so = $key;
        $found_key = array_filter($res, function($t) use ($_so) { return $t['url'] == $_so; });
        return array_values($found_key)[0]['name'];
    }


    public function getIpName(){

    }

}