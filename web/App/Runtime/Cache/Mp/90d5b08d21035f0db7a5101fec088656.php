<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>充值记录-<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?>" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    
    <link href="/Source/amp/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/plugins/sweet-alert/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="/Source/amp/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="/Source/amp/plugins/spinkit/spinkit.css" rel="stylesheet" />
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    <style>
        .zzxunlong-list{border:1px solid #e7e8eb;}
        .zzxunlong-list:after{content:"";height:0;display:block;clear:both;}
        .zzxunlong-list li{list-style-type:none;padding:0;margin:0;float:left;text-align:center;border-right:1px solid #e7e8eb;}
        .zzxunlong-list li .p1{margin-top:22px;}
        .zzxunlong-list li p.num{font-size:20px;}
        .zzxunlong-list li.noBorderRight{border-right:none;}
        .zzxunlong-list li{width:33%;}
        .p1{color:#808080;}
        .top-display{display:inline-block;width:33%;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .line-display{position:relative;}
        .line-display:after{content:'';position:absolute;right:0;top:4px;width:1px;height:34px;background:#dcdcdc;}
        .zzxunlong-line p{margin-bottom:0!important;}
        .tip-help{color: #e87a14;font-size: 18px;}
        .tip-help:hover{color: red;}
        .pagination{display:inline-block;padding-left:0;margin:0px 0;border-radius:4px;}
        .pagination>li{display:inline;}
        .pagination>li>a{position:relative;float:left;padding:6px 12px;margin-left:-1px;line-height:1.42857143;color:#337ab7;text-decoration:none;background-color:#fff;border:1px solid #ddd;}
        .pagination>li:first-child>a{margin-left:0;border-top-left-radius:4px;border-bottom-left-radius:4px;}
        .pagination>li:last-child>a{border-top-right-radius:4px;border-bottom-right-radius:4px;}
        .pagination>li>a:focus,.pagination>li>a:hover{z-index:2;color:#23527c;background-color:#eee;border-color:#ddd;}
        .pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover{z-index:3;color:#fff;cursor:default;background-color:#337ab7;border-color:#337ab7;}
        .pull-right{float:none!important;}
        .pagination>li a:active{box-shadow:inset 0 3px 1px rgba(0,0,0,0.2);}
        .pagination>li a:hover,.pagination>li a:focus{background-color:#fff;border-color:#42a5f5;color:#42a5f5;box-shadow:inset 0 0 1px #42a5f5;z-index:2;transition:border-color,0.3s;}
        .pagination>li>a{color:inherit;border-color:#dcdcdc;transition:border-color,0.3s;}
        .pagination>li:first-child>a{border-top-left-radius:0;border-bottom-left-radius:0;}
        .pagination>li:last-child>a{border-top-right-radius:0;border-bottom-right-radius:0;}
        .pagination>.active>a,.pagination>.active>a:hover,.pagination>.active>a:focus{background-color:#42a5f5;border-color:#42a5f5;}
        .pagination>li>a{background-color:transparent;color:inherit;}
        .pagination>li>a:focus{box-shadow:none;}
        .pagination>li a:hover,.pagination>li a:focus{border-color:#00bcd4;color:#00bcd4;box-shadow:inset 0 0 1px #00bcd4;}
        .pagination>.active>a,.pagination>.active>a:hover,.pagination>.active>a:focus{background-color:#00bcd4;border-color:#00bcd4;}
    </style>

    <link href="/Source/amp/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/style.css" rel="stylesheet" type="text/css" />
    
    <script src="/Source/amp/assets/js/modernizr.min.js"></script>

</head>
<body>

<div id="wrapper">

    <div class="left side-menu">
        <div class="slimscroll-menu" id="remove-scroll">
            <div class="topbar-left">
                <a href="<?php echo U('mp/index/index');?>" class="logo">
                            <span>
                                <img src="/Source/amp/assets/images/logo.png" alt="" height="22">
                            </span>
                    <i>
                        <img src="/Source/amp/assets/images/logo_sm.png" alt="" height="28">
                    </i>
                </a>
            </div>
            <div class="user-box">
                <div class="user-img">
                    <img src="<?php echo ((isset($_domain['brand_logo']) && ($_domain['brand_logo'] !== ""))?($_domain['brand_logo']):'/Source/amp/assets/images/user.png'); ?>" alt="user-img" title="<?php echo ($_domain['web_name']); ?>" class="rounded-circle img-fluid">
                </div>
                <h5><a><?php echo ($_SESSION['mp']['mch_name']); ?></a> </h5>
                <p class="text-muted"><?php echo (tel_replace($_SESSION['mp']['phone'])); ?></p>
            </div>
            <div id="sidebar-menu">

                <ul class="metismenu" id="side-menu">
                    <?php if(is_array($_menu["default"])): $k = 0; $__LIST__ = $_menu["default"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if(!empty($vo['list'])): ?><li class="active">
                                <a href="javascript: void(0);"><i class="<?php echo ((isset($vo["ico"]) && ($vo["ico"] !== ""))?($vo["ico"]):'fi-layers'); ?>"></i> <span><?php echo ($vo["name"]); ?></span> <span class="menu-arrow"></span></a>
                                <ul class="nav-second-level" aria-expanded="true">
                                    <?php if(is_array($vo['list'])): $i = 0; $__LIST__ = $vo['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a <?php if(($_rule_name) == $vo['url']): ?>class="active"<?php endif; ?> href="<?php echo U($sub['url']);?>"><?php echo ($sub["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li>
                                <a <?php if(($_rule_name) == $vo['url']): ?>class="active"<?php endif; ?>  href="<?php echo U($vo['url']);?>"><i class="<?php echo ($vo["ico"]); ?>"></i> <span> <?php echo ($vo["name"]); ?> </span></a>
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    <li class="menu-title">扩展</li>
                    <?php if(is_array($_menu["plug"])): $k = 0; $__LIST__ = $_menu["plug"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if(!empty($vo['list'])): ?><li class="active">
                                <a href="javascript: void(0);"><i class="<?php echo ((isset($vo["ico"]) && ($vo["ico"] !== ""))?($vo["ico"]):'fi-layers'); ?>"></i> <span> <?php echo ($vo["name"]); ?></span> <span class="menu-arrow"></span></a>
                                <ul class="nav-second-level" aria-expanded="true">
                                    <?php if(is_array($vo['list'])): $i = 0; $__LIST__ = $vo['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U($sub['url']);?>"><?php echo ($sub["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li>
                                <a <?php if(($_rule_name) == $vo['url']): ?>class="active"<?php endif; ?>  href="<?php echo U($vo['url']);?>"><i class="<?php echo ($vo["ico"]); ?>"></i> <span> <?php echo ($vo["name"]); ?> </span></a>
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content-page">
        <div class="topbar">
            <nav class="navbar-custom">
                <ul class="list-unstyled topbar-right-menu float-right mb-0">
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button"
                           aria-haspopup="false" aria-expanded="false">
                            <img src="/Source/amp/assets/images/user.png" alt="user" class="rounded-circle"> <span class="ml-1"><?php echo ($_SESSION['mp']['mch_name']); ?> <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown">
                            <!-- item-->
                            <div class="dropdown-item noti-title">
                                <h6 class="text-overflow m-0">欢迎使用!</h6>
                            </div>
                            <!-- item-->
                            <a href="<?php echo U('user/index');?>" class="dropdown-item notify-item">
                                <i class="fi-head"></i> <span>我的资料</span>
                            </a>
                            <!-- item-->
                            <a href="<?php echo U('user/rate');?>" class="dropdown-item notify-item">
                                <i class="fi-help"></i> <span>我的费率</span>
                            </a>
                            <!-- item-->
                            <a href="<?php echo U('user/pass');?>" class="dropdown-item notify-item">
                                <i class="fi-lock"></i> <span>修改密码</span>
                            </a>
                            <!-- item-->
                            <a href="<?php echo U('login/out');?>" class="dropdown-item notify-item">
                                <i class="fi-power"></i> <span>退出登录</span>
                            </a>

                        </div>
                    </li>

                </ul>
                <ul class="list-inline menu-left mb-0">
                    <li class="float-left">
                        <button class="button-menu-mobile open-left disable-btn">
                            <i class="dripicons-menu"></i>
                        </button>
                    </li>
                    <li>
                        <div class="page-title-box">
                            <h4 class="page-title">
    充值记录
</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">商户管理系统</li>
                            </ol>
                        </div>
                    </li>

                </ul>
            </nav>
        </div>

        <div class="content">
            <div class="container-fluid">
                
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <ul class="nav nav-tabs tabs-bordered">
                    <li class="nav-item">
                        <a href="<?php echo U('index');?>"  class="nav-link ">
                            <i class="fi-monitor mr-2"></i>所有会员
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userCz');?>"  class="nav-link active">
                            <i class="fi-head mr-2"></i>充值记录
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userXf');?>"  class="nav-link">
                            <i class="fi-mail mr-2"></i>消费记录
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userTemp');?>"  class="nav-link">
                            <i class="fi-cog mr-2"></i>模板设置
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userConf');?>"  class="nav-link">
                            <i class="fi-cog mr-2"></i>充值配置
                        </a>
                    </li>
                </ul>
                <div class="row" style="margin-top: 20px">
                    <div class="col-md-12">
                        <ul class="zzxunlong-list">
                            <li class="recharge-block" style="position: relative;">
                                <p class="p1">充值总额：<span class="j-recharge-total shiji" style="font-size: 20px;color:black;"><?php echo ($count["sum"]); ?></span>
                                    <i class="mdi mdi-help-circle-outline tip-help" data-toggle="tooltip" data-placement="right" title="" data-original-title="充值总额=线上充值+现金充值+赠送/返现"></i>
                                </p>
                                <div class="p1" style="font-size: 0;height: 50px;width: 100%">
                                    <div class="top-display line-display zzxunlong-line">
                                        <p>线上充值</p>
                                        <p style="font-size: 15px;" class="shishou"><?php echo ($count["cz"]); ?> <i class="mdi mdi-help-circle-outline tip-help" data-toggle="tooltip" data-placement="right" title="" data-original-title="通过系统收款的充值实收金额"></i></p>
                                    </div>
                                    <div class="top-display line-display zzxunlong-line">
                                        <p>现金充值</p>
                                        <p style="font-size: 15px;" class="shishou"><?php echo ($count["xj"]); ?> <i class="mdi mdi-help-circle-outline tip-help" data-toggle="tooltip" data-placement="right" title="" data-original-title="通过现金收款的充值实收金额"></i></p>
                                    </div>
                                    <div class="top-display zzxunlong-line">
                                        <p>赠送/返现</p>
                                        <p style="font-size: 15px;" class="shishou"><?php echo ($count["fx"]); ?> <i class="mdi mdi-help-circle-outline tip-help" data-toggle="tooltip" data-placement="right" title="" data-original-title="消费返的金额详情请在消费记录列表查询"></i></p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <p class="p1">手续费</p>
                                <p class="num fee" style="margin-bottom: 33px"><?php echo ($count["sxf"]); ?></p>
                            </li>
                            <li class="noBorderRight real-interest-block" style="position: relative;">
                                <p class="p1">商家净收入<i class="mdi mdi-help-circle-outline tip-help" data-toggle="tooltip" data-placement="right" title="" data-original-title="商家净收入=商家实收-手续费"></i></p>
                                <p class="num"><span class="j-real-interest netIncome"><?php echo ($count["money"]); ?></span></p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <form action="<?php echo U('userCz');?>" method="post">
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-2">
                                    <label class="col-form-label" for="inlineFormInput">详细查询</label>
                                    <input type="text" class="form-control mb-2" name="data" id="inlineFormInput" placeholder="会员手机号/姓名/卡号">
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="col-form-label" for="date">充值时间</label>
                                    <input type="text" class="form-control mb-2" name="time" id="date" placeholder="充值时间范围">
                                </div>
                                <div class="form-group col-md-1" style="margin-top: -10px;">
                                    <label class="col-form-label">支付状态</label>
                                    <select data-placeholder="请选择..."  class="form-control select2" name="status">
                                        <option value="-1" selected>请选择状态</option>
                                        <option value="1">已支付</option>
                                        <option value="2">未支付</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2" style="margin-top: -10px;">
                                    <label class="col-form-label">充值类型</label>
                                    <select data-placeholder="请选择..."  class="form-control select2" name="type">
                                            <option value="-1" selected>选择充值类型</option>
                                           <?php $memberCzOrderType=memberCzOrderType(); ?>
                                           <?php if(is_array($memberCzOrderType)): foreach($memberCzOrderType as $k=>$v): ?><option value="<?php echo ($k); ?>"><?php echo ($v); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3" style="margin-top: -10px;">
                                    <label class="col-form-label">所属门店</label>
                                    <select data-placeholder="请选择门店,支持多选..."  class="form-control select2 select2-multiple" multiple="multiple"  name="store_id[]">
                                       <?php if(is_array($store)): foreach($store as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info mb-2" style="margin-top: 15px">搜索</button>
                                <button type="button" class="btn btn-pink mb-2" style="margin-left:10px;margin-top: 15px">导出</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box" style="padding: 1px">
                            <h4 class="m-t-0 header-title">充值记录</h4>
                            <p class="text-muted font-14 m-b-20">
                                充值记录默认只显示近七天的数据,如查看更多,请使用查询功能
                            </p>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>所属门店	</th>
                                    <th>订单号</th>
                                    <th>订单金额</th>
                                    <th>操作人</th>
                                    <th>充值类型</th>
                                    <th>手续费(元)</th>
                                    <th>净收入(元)</th>
                                    <th>支付状态	</th>
                                    <th>下单时间</th>
                                    <th>会员卡号</th>
                                    <th>详情</th>
                                </tr>
                                </thead>
                                <tbody>


                                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "暂无充值记录" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; $user=memberUserData($v['user_id']); ?>
                                    <tr>
                                        <th scope="row"><?php $store=Get_Store($v['store_id']); echo $store['name']; ?></th>
                                        <td><?php echo ($v['out_trade_no']); ?></td>
                                        <td><?php echo ($v["total"]); ?></td>
                                        <td><?php echo ((isset($v['czr']) && ($v['czr'] !== ""))?($v['czr']):'---'); ?></td>
                                        <td><?php echo (memberCzOrderType($v['type'])); ?></td>
                                        <td><?php echo ((isset($v["sxf"]) && ($v["sxf"] !== ""))?($v["sxf"]):'---'); ?></td>
                                        <td><?php echo ((isset($v["money"]) && ($v["money"] !== ""))?($v["money"]):'---'); ?></td>
                                        <td>
                                            <?php if(($v["status"]) == "1"): ?><span style="background-color: #00aced;padding: 5px 10px;color: #FFF;border-radius: 5px;font-size: 12px;cursor: pointer" >成功</span>
                                                <?php else: ?>
                                                <span style="background-color: #999;padding: 5px 10px;color: #FFF;border-radius: 5px;font-size: 12px;cursor: pointer" >失败</span><?php endif; ?>
                                        </td>
                                        <td><?php echo (date('Y-m-d H:i:s',$v['create_time'])); ?></td>
                                        <td><?php echo ($user["num"]); ?></td>
                                        <td><span class="showMember" style="background-color: #00aced;padding: 5px 10px;color: #FFF;border-radius: 5px;font-size: 12px;cursor: pointer" data-num="<?php echo ($user["num"]); ?>" data-name="<?php echo ($user["name"]); ?>" data-phone="<?php echo ($user["phone"]); ?>">查看</span></td>
                                    </tr><?php endforeach; endif; else: echo "暂无充值记录" ;endif; ?>
                                </tbody>
                            </table>
                            <div  style="text-align: right">
                                <?php echo ($page); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="memberModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">会员详情</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped mb-0">
                        <tbody>
                        <tr>
                            <th scope="row">会员姓名</th>
                            <td class="name"></td>
                        </tr>
                        <tr>
                            <th scope="row">会员手机号</th>
                            <td class="phone"></td>
                        </tr>
                        <tr>
                            <th scope="row">会员卡号</th>
                            <td class="num"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


            </div> <!-- 主区域 -->
        </div>
        <footer class="footer">
            2018 ©<?php echo ($_domain['web_name']); ?>. -  <?php echo ($_domain['web_domain']); ?>
        </footer>
    </div>
</div>

<script src="/Source/amp/assets/js/jquery.min.js"></script>
<script src="/Source/amp/assets/js/bootstrap.bundle.min.js"></script>
<script src="/Source/amp/assets/js/metisMenu.min.js"></script>
<script src="/Source/amp/assets/js/waves.js"></script>
<script src="/Source/amp/assets/js/jquery.slimscroll.js"></script>
<script src="/Source/amp/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
<script>
    function toast(icon,text) {
        $.toast({
            heading: '温馨提示',
            text: text,
            position: 'top-right',
            loaderBg: icon=='error'?'#bf441d':'#5ba035',
            icon: icon?icon:'error',
            hideAfter: 3000,
            stack: 1
        });
    }
</script>

    <script src="/Source/amp/plugins/moment/moment.js"></script>
    <script src="/Source/amp/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/Source/amp/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="/Source/amp/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
    <script src="/Source/amp/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            $(".select2").select2();
            $('.showMember').on('click', function () {
                $('.name').text($(this).data('name'));
                $('.phone').text($(this).data('phone'));
                $('.num').text($(this).data('num'));
                $('#memberModal').modal('show');
            });

            $('#date').daterangepicker({
                format: 'MM/DD/YYYY',
                startDate:false,
                endDate: moment(),
                minDate: '01/01/2017',
                maxDate: '12/31/2021',
                dateLimit: {
                    days: 31
                },
                showDropdowns: true,
                showWeekNumbers: false,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    '今日': [moment(), moment()],
                    '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '近7天': [moment().subtract(6, 'days'), moment()],
                    '近30天': [moment().subtract(29, 'days'), moment()],
                    '本月': [moment().startOf('month'), moment().endOf('month')],
                    '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                opens: 'left',
                drops: 'down',
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-success',
                cancelClass: 'btn-light',
                separator: ' to ',
                locale: {
                    applyLabel: '确定',
                    cancelLabel: '取消',
                    fromLabel: '起始时间',
                    toLabel: '结束时间',
                    customRangeLabel: '自定义',
                    daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                    monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                    firstDay: 1
                }
            }, function (start, end, label) {
                //$('#date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            });
        });

        $('.order-reload').click(function () {
            var oid=$(this).data('id');
            var loading=$('#oLoading_'+oid),reload=$('#oReload_'+oid);
            reload.hide();
            loading.show();
            $.post('<?php echo U("orderRefund");?>?Debug=1', {oid:oid}, function (data) {
                reload.show();
                loading.hide();
                if (data.status === 1) {
                    $.toast({
                        heading: '温馨提示',
                        text: data.info,
                        position: 'top-right',
                        loaderBg: '#5ba035',
                        icon: 'success',
                        hideAfter: 3000,
                        stack: 1
                    });
                    setTimeout(function () {
                        window.location.href = data.url
                    }, 3000);
                }
                else {
                    $.toast({
                        heading: '温馨提示',
                        text: data.info,
                        position: 'top-right',
                        loaderBg: '#bf441d',
                        icon: 'error',
                        hideAfter: 3000,
                        stack: 1
                    });
                }
            }, 'json');
        });

        $('.order-notify').click(function () {
            var oid=$(this).data('id');
            var loading=$('#notifyLoading_'+oid),reload=$('#notify_'+oid);
            reload.hide();
            loading.show();
            $.post('<?php echo U("orderNotify");?>?Debug=1', {oid:oid}, function (data) {
                reload.show();
                loading.hide();
                if (data.status === 1) {
                    $.toast({
                        heading: '温馨提示',
                        text: data.info,
                        position: 'top-right',
                        loaderBg: '#5ba035',
                        icon: 'success',
                        hideAfter: 3000,
                        stack: 1
                    });
                    setTimeout(function () {
                        window.location.href = data.url
                    }, 5000);
                }
                else {
                    $.toast({
                        heading: '温馨提示',
                        text: data.info,
                        position: 'top-right',
                        loaderBg: '#bf441d',
                        icon: 'error',
                        hideAfter: 3000,
                        stack: 1
                    });
                }
            }, 'json');
        })
    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>