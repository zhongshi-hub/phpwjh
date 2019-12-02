<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
    <style>
        span{
            -moz-osx-font-smoothing: auto!important;
        }
    </style>
    <link href="/Ext?g=css" rel="stylesheet">
    <link  href="/Source/statics/css/themes/type-b/theme-light.min.css" rel="stylesheet">
    <script type="text/javascript" src="/Source/statics/plugins/pace/pace.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/nifty.min.js"></script>
    <script type="text/javascript" src="/Source/artDialog/jquery.artDialog.js?skin=default"></script>
    <script type="text/javascript" src="/Source/layer/layer.js"></script>
    <script type="text/javascript" src="/Source/statics/plugins/bootbox/bootbox.min.js"></script>
    <script src="/Ext?g=default_js"></script>

</head>

<!--全局 CCL-->
<body>
<div id="container" class="effect aside-float aside-bright mainnav-lg mainnav-fixed navbar-fixed">
    <!--头部开始-->
    <header id="navbar">
        <div id="navbar-container" class="boxed">
            <div class="navbar-header">
                <a href="<?php echo U('Admin/Index/index');?>" class="navbar-brand">
                    <img src="/Source/statics/img/logo.png" alt="控制台" class="brand-icon">
                    <div class="brand-title">
                        <span class="brand-text">后台管理</span>
                    </div>
                </a>
            </div>
            <div class="navbar-content clearfix">
                <ul class="nav navbar-top-links pull-left">
                    <li class="tgl-menu-btn">
                        <a class="mainnav-toggle" href="#">
                            <i class="demo-pli-view-list"></i>
                        </a>
                    </li>
                    <!--提醒事项-->
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="demo-pli-bell"></i>
                            <span class="badge badge-header badge-danger"></span>
                        </a>
                        <!--事项详情-->
                        <div class="dropdown-menu dropdown-menu-md">
                            <div class="pad-all bord-btm">
                                <p class="text-semibold text-main mar-no">You have 9 notifications.</p>
                            </div>
                            <div class="nano scrollable">
                                <div class="nano-content">
                                    <ul class="head-list">
                                        <li>
                                            <a href="#">
                                                <div class="clearfix">
                                                    <p class="pull-left">Database Repair</p>
                                                    <p class="pull-right">70%</p>
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div style="width: 70%;" class="progress-bar">
                                                        <span class="sr-only">70% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>

                                        <!-- Dropdown list-->
                                        <li>
                                            <a href="#">
                                                <div class="clearfix">
                                                    <p class="pull-left">Upgrade Progress</p>
                                                    <p class="pull-right">10%</p>
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div style="width: 10%;" class="progress-bar progress-bar-warning">
                                                        <span class="sr-only">10% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!--底部显示-->
                            <div class="pad-all bord-top">
                                <a href="#" class="btn-link text-dark box-block">
                                    <i class="fa fa-angle-right fa-lg pull-right"></i>显示所有提醒事项
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-top-links pull-right">
                    <li id="dropdown-user" class="dropdown">

                        <a href="JavaScript:password()" class="dropdown-toggle" title="修改密码">
                            <i class="fa fa-th"></i>
                            <span class="badge badge-header badge-danger"></span>
                        </a>
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
                                <span class="pull-right">
                                    <i class="demo-pli-male ic-user"></i>
                                </span>
                            <div class="username hidden-xs"><h4 class="text-main"><p class="text-pink"><?php echo ($_SESSION['user']['name']); ?></p></h4></div>
                        </a>
                        <div class="dropdown-menu  dropdown-menu-right panel-default">

                            <div class="pad-all text-right">
                                <a href="<?php echo U('Admin/Login/out');?>" class="btn btn-primary">
                                    <i class="demo-pli-unlock"></i>退出登录
                                </a>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </header>
    <!--头部结束-->

    <div class="boxed">


        <!--MAIN NAVIGATION-->
        <!--===================================================-->
        <nav id="mainnav-container">
            <div id="mainnav">

                <!--Menu-->
                <!--================================-->
                <div id="mainnav-menu-wrap">
                    <div class="nano">
                        <div class="nano-content">
                            <div id="mainnav-profile" class="mainnav-profile">

                                <div class="profile-wrap">
                                    <div class="pad-btm">
                                        <img class="img-border" src="<?php echo GetPlogo();?>" style="border-radius: 10%;width: 55px;height: 55px;">
                                    </div>
                                </div>
                            </div>

                            <ul id="mainnav-menu" class="list-group">
                                <!--菜单-->

                                <?php if(is_array($menu)): $k = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if(($rule_name_s) == $vo['name']): ?>class="active-sub active"<?php endif; ?>>
                                    
                                    <a href="<?php if(empty($vo['_data'])): echo U($vo['name']); else: ?>#<?php endif; ?>">
                                        <i class="fa <?php echo $vo['icon'];?>" style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong><?php echo $vo['title'];?></strong></span>
                                        <?php if(!empty($vo['_data'])): ?><i class="arrow"></i><?php endif; ?>
                                    </a>
                                    <?php if(!empty($vo['_data'])): ?><!--子菜单-->
                                        <ul class="collapse">
                                        <?php if(is_array($vo['_data'])): $i = 0; $__LIST__ = $vo['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li <?php if(($rule_name) == $sub['name']): ?>class="active-link"<?php endif; ?>><a href="<?php echo U($sub['name']);?>"><?php echo $sub['title'];?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul><?php endif; ?>
                                 </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!--头部信息结束-->
<style>

    .imgborder{width: 300px;border: solid 1px #E4E4E4;height: 220px;padding: 5px;}
    .imgborder img { width:100%; display:block;height: 100%;}
    .chosen-container{display: block}
    .ccl_r{color: red;font-size: 1.5em;position: absolute;margin: 0 2px;}
</style>
<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <form id="in_form" method="post" action="<?php echo U('xwIn');?>" ajax="n">
                <div class="panel-body">
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">小微入网</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">商户简称 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="merchant_shortname" value="<?php echo ($data["mch_name"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">门店名称 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="store_name" value="<?php echo ($data["mch_name"]); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">联系人姓名 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="contact" value="<?php echo ($data["mch_card_name"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">联系手机号 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="tel"  name="contact_phone" value="<?php echo ($data["mch_tel"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">联系邮箱 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="email"  name="contact_email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label class="control-label">身份证姓名 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="id_card_name" value="<?php echo ($data["mch_card_name"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">身份证号 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="id_card_number" value="<?php echo ($data["mch_card_id"]); ?>" required>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">身份证有效期 <span class="ccl_r">*</span></label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" class="form-control" id="s_t" name="id_card_valid_time[]" required>
                                    <span class="input-group-addon">至</span>
                                    <input type="text" class="form-control" id="e_t" name="id_card_valid_time[]" required>
                                    <span style="position: absolute;min-width: 100px;margin: 5px;">
                                        <input id="time_c" class="magic-checkbox" name="id_card_valid_time_end" type="checkbox">
                                        <label for="time_c">长期</label>
                                    </span>
                                </div>
                                <span>证件有效期限需与上传文件上所示期限一致</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label"><span>身份证正面<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('id_card_copy',$data['mch_img_z'],1);?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label"><span>身份证反面<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('id_card_national',$data['mch_img_p'],1);?>
                            </div>
                        </div>
                    </div>

                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">门店信息</p>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">售卖商品/提供服务描述<span class="ccl_r">*</span></label>
                                <select class="form-control" data-placeholder="请选择..."  tabindex="2" name="product_desc" required="required">
                                    <option value="餐饮">餐饮</option>
                                    <option value="线下零售">线下零售</option>
                                    <option value="居民生活服务">居民生活服务</option>
                                    <option value="休闲娱乐">休闲娱乐</option>
                                    <option value="交通出行">交通出行</option>
                                    <option value="其他">其他</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">客服电话<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="service_phone" value="<?php echo ($data["mch_tel"]); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">所属省份<span class="ccl_r">*</span></label>
                                <select data-placeholder="请选择..."  data-width="100%"   id="provice"  class="form-control" name="pro" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["mid"]); ?>" <?php if($data["mch_provice"] == $v['name']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">所属城市<span class="ccl_r">*</span></label>
                                <select id="selectCity" class="form-control" data-live-search="true" data-width="100%" name="city" required="required">
                                    <option value="">请选择市</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">所属区县<span class="ccl_r">*</span></label>
                                <select id="selectDis" class="form-control" data-live-search="true" data-width="100%" name="dist" required="required">
                                    <option value="">请选择区/县</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">门店详细地址<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="store_street" value="<?php echo ($data["mch_address"]); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>门头照片<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('store_entrance_pic',$data['mch_img_m1'],1);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>门店内景<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('indoor_pic',$data['mch_img_m2'],1);?>
                            </div>
                        </div>
                    </div>
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">结算信息</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" style="color: red">结算费率<span class="ccl_r">*</span></label>
                                <select class="form-control"  data-placeholder="请选择..." tabindex="2"  name="rate" required="required">
                                    <?php if(is_array($rate)): foreach($rate as $key=>$v): ?><option value="<?php echo ($v); ?>"><?php echo ($v); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户银行<span class="ccl_r">*</span></label>
                                <select class="form-control"  data-placeholder="请选择..." tabindex="2"  name="account_bank" required="required">
                                    <?php if(is_array($bank)): foreach($bank as $key=>$v): ?><option value="<?php echo ($v); ?>" <?php if($data["mch_bank_list"] == $v): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">银行卡号<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="account_number" value="<?php echo ($data["mch_bank_cid"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户名称<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="account_name"   value="<?php echo ($data["mch_bank_name"]); ?>" placeholder="需与身份证姓名一致" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户省份<span class="ccl_r">*</span></label>
                                <select data-placeholder="请选择..."  id="bank_pro" tabindex="2" class="form-control"  name="bank_pro" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["mid"]); ?>" <?php if($data["mch_bank_provice"] == $v['name']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" id="depositCity">
                                <label class="control-label">开户城市<span class="ccl_r">*</span></label>
                                <select id="selectBCity" class="form-control" data-live-search="true" data-width="100%" name="bank_city" required="required">
                                    <option value="">请选择市</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" id="bank_name">
                            <div class="form-group" >
                                <label class="control-label">开户支行<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="bank_name" value="<?php echo (reload_banks($data["mch_linkbnk"])); ?>" placeholder="选择其它银行后需填写银行全称精确到开户行 如郑州银行XXX路支行">
                                <span>开户银行为其他银行必填,其他17家直连银行无需填写 <a href="https://pay.weixin.qq.com/wiki/doc/api/download/wx_cityno02.zip" style="color: #17a735">银行全称对照表</a></span>
                            </div>
                        </div>
                    </div>


                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">补充资料(选填)</p>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">门店经度</label>
                                <input class="form-control"  type="text" name="store_longitude" placeholder="如:113.941355 数字或小数">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">门店纬度</label>
                                <input class="form-control"  type="text" name="store_latitude" placeholder="如:22.546245 数字或小数">
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label class="control-label">补充说明</label>
                                <input class="form-control"  type="text" name="business_addition_desc" placeholder="可填写需要额外说明的文字">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>经营场地证明</span></label>
                                <?php echo uploads_map('address_certification','');?>
                                <span>门面租赁合同扫描件或经营场地证明（需与身份证同名）</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>补充材料1</span></label>
                                <?php echo uploads_map('business_addition_pics[]','','','business_addition_pics1');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>补充材料2</span></label>
                                <?php echo uploads_map('business_addition_pics[]','','','business_addition_pics2');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>补充材料3</span></label>
                                <?php echo uploads_map('business_addition_pics[]','','','business_addition_pics3');?>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>补充材料4</span></label>
                                <?php echo uploads_map('business_addition_pics[]','','','business_addition_pics4');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>补充材料5</span></label>
                                <?php echo uploads_map('business_addition_pics[]','','','business_addition_pics5');?>
                            </div>
                        </div>
                    </div>
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">自定义配置项(当接口出现省市Code错误,需自行配置填写)</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">门店所属城市地区码</label>
                                <input class="form-control" type="text" name="set_store_code" placeholder="默认选填,当出现返回进件失败为code时填写">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户城市地区码</label>
                                <input class="form-control" type="text" name="set_bank_code"  placeholder="默认选填,当出现返回进件失败为code时填写">
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">微信官方地区码数据</label>
                                <a class="form-control" style="border: none" href="https://pay.weixin.qq.com/wiki/doc/api/download/wx_cityno.zip">https://pay.weixin.qq.com/wiki/doc/api/download/wx_cityno.zip</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-footer text-justify">
                    <a class="btn btn-warning" href="<?php echo U('WxPay/xwList');?>">返回列表</a>
                    <input type="hidden" name="mid" value="<?php echo ($_GET['id']); ?>">
                    <input type="hidden" name="business_code" value="<?php echo ($_GET['business_code']); ?>">
                    <button id="mchInEnd" type="button" class="btn btn-danger" >确认信息并进件</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var proCity=["<?php echo ($data['mch_provice']); ?>","<?php echo ($data['mch_citys']); ?>","<?php echo ($data['mch_district']); ?>"],bankProCity=["<?php echo ($data['mch_bank_provice']); ?>","<?php echo ($data['mch_bank_citys']); ?>"],bankN="<?php echo ($data['mch_bank_list']); ?>";
    $(function() {
        $('#s_t,#e_t').datepicker({
            format: "yyyy-mm-dd",
            todayBtn: "linked",
            minDate: new Date(1900, 10 - 1, 1),
            autoclose: true,
            todayHighlight: true
        });
        $('#time_c').change(function (e) {
            var checked=$(this).is(':checked');
            if(checked){$('#e_t').val('长期').attr('disabled',true);}else{
                $('#e_t').val('').attr('disabled',false);
            }
        });
        $('[name="account_bank"]').change(bankNameStatus);
        $('select').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
        $('#provice').change(provinceChange); //省份
        $('#selectCity').change(cityChange); //市级
        $('#bank_pro').change(BproChange); //银行省份
        //初始化
        bankNameStatus();
        provinceChange();
        cityChange();
        BproChange();
    });

    function bankNameStatus() {
        var bank=$('[name="account_bank"]').val();
        if(bank=='其他银行'){
            $('#bank_name').show();
        }else{
            $('#bank_name').hide();
        }
    }

    //当银行省份改变时，带出默认城市
    function BproChange() {
        var provinceId = $('#bank_pro option:selected').val();
        provinceId?setBCity(provinceId):false;
    }

    //当省份改变时，带出默认城市
    function provinceChange(){
        var provinceId = $('#provice option:selected').val();
        provinceId?setCity(provinceId):false;
    }
    //当城市改变的时候,改变区县
    function cityChange(){
        var cityId = $('#selectCity option:selected').val();
        cityId?setdistrict(cityId):false;
    }


    //设置银行所属城市
    function setBCity(provinceId) {
        var cityStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"<?php echo U('getProCityData');?>",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                for(var i=0; i<data.info.length; i++){
                    if(bankProCity[1]==data.info[i]['name']) {
                        cityStr += "<option value='" + data.info[i]['mid'] + "' selected>" + data.info[i]['name'] + "</option>";
                    }else{
                        cityStr += "<option value='" + data.info[i]['mid'] + "'>" + data.info[i]['name'] + "</option>";
                    }
                }
                $("#selectBCity").find("option").remove();
                $("#selectBCity").append(cityStr);
                $('#selectBCity').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectBCity").trigger("chosen:updated");
                $("#selectBCity").trigger("liszt:updated");
            }
        });
    }

    //设置显示城市
    function setCity(provinceId){
        var cityStr = "";
        $.ajax({
            url:"<?php echo U('getProCityData');?>",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                for(var i=0; i<data.info.length; i++){
                    if(proCity[1]==data.info[i]['name']) {
                        cityStr += "<option value='" + data.info[i]['mid'] + "' selected>" + data.info[i]['name'] + "</option>";
                    }else{
                        cityStr += "<option value='" + data.info[i]['mid'] + "'>" + data.info[i]['name'] + "</option>";
                    }

                   // cityStr += "<option value='"+data.info[i]['mid']+"'>"+data.info[i]['name']+"</option>";
                }
                $("#selectCity").find("option").remove();
                $("#selectCity").append(cityStr);
                $('#selectCity').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectCity").trigger("chosen:updated");
                $("#selectCity").trigger("liszt:updated");
                var cityId = data.info[0]['mid'];
                setdistrict(cityId);
            }
        });
    }


    //设置显示区县
    function setdistrict(provinceId){
        var disStr = "";
        $.ajax({
            url:"<?php echo U('getProCityData');?>",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                for(var i=0; i<data.info.length; i++){
                    if(proCity[2]==data.info[i]['name']) {
                        disStr += "<option value='" + data.info[i]['mid'] + "' selected>" + data.info[i]['name'] + "</option>";
                    }else{
                        disStr += "<option value='" + data.info[i]['mid'] + "'>" + data.info[i]['name'] + "</option>";
                    }

                    //disStr += "<option value='"+data.info[i]['mid']+"'>"+data.info[i]['name']+"</option>";
                }
                $("#selectDis").find("option").remove();
                $("#selectDis").append(disStr);
                $('#selectDis').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectDis").trigger("chosen:updated");
                $("#selectDis").trigger("liszt:updated");
            }
        });
    }
    $('#mchInEnd').click(function (e) {
        var form=$('#in_form');
        var ajax_data = form.serialize();
        var actionurl = form.attr("action");
        loading('接口进件中...');
        $.post(actionurl, ajax_data, function (data) {
            if (data.status) {
                layer.closeAll();
                layer.alert(data.info, {
                    skin: 'layui-layer-lan'
                    ,title: "进件结果"
                    ,offset: '100px'
                },function (e) {
                    window.location.href="<?php echo U('WxPay/xwList');?>";
                });
            }
            else {
                layer.alert(data.info,{
                    skin: 'layui-layer-lan'
                    ,title: "进件结果"
                    ,offset: '100px'
                });
            }
        }, 'json');
    });
    


    function loading(text) {
        layer.msg(text, {
            icon: 16,
            shade: 0.2,
            time:300000
        });
    }
</script>
</div>
<footer id="footer">
    <div class="hide-fixed pull-right pad-rgt">
        渠道管理后台
    </div>
    <p class="pad-lft">&#0169; <?php echo date('Y');?> </p>
</footer>
<!-- 返回顶部 -->
<button class="scroll-top btn">
    <i class="pci-chevron chevron-up"></i>
</button>
</div>

<!-- 修改密码模态框开始 -->
<div class="modal fade" id="PassWords" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    密码修改
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <form class="form-horizontal" action="<?php echo U('Admin/User/editpass');?>" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">旧密码</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="oldpass" type="text" placeholder="请输入旧密码" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >新密码</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="newpass"  type="text" placeholder="请输入新密码" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">确认新密码</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="newspass" type="text" placeholder="请再次输入新密码" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-success" type="submit">确认修改</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 修改密码模态框结束 -->


<!-- 修改交易识别码模态框开始 -->
<div class="modal fade" id="IfRaMeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    详细信息
                </h4>
            </div>
            <div class="modal-body">
                <iframe height="500px" src="" frameBorder="0" width="100%"></iframe>
            </div>
        </div>
    </div>
</div>

<style>
    .chosen-container-single .chosen-single{
        height: 35px!important;
    }
    .btn{border-radius: 5px !important;}
    select {
        border: solid 1px #e1e5ea;
        appearance:none;
        -moz-appearance:none;
        -webkit-appearance:none;
        background: url("/Source/plug/arrow.jpg") no-repeat scroll right center transparent !important;
        padding-right: 14px;
    }
    select::-ms-expand { display: none; }

    .magic-radio + label::after {
        left: 2.8px!important;
        top: 2.8px!important;
    }
</style>
<script type="text/javascript">
    var upload_mod='<?php echo MODULE_NAME;?>',upload_type='UEdit',upload_ValName='fw_info';

    $('#FileUpload').on('hidden.bs.modal', function () {
        for (var i = 0; i < uploader.getFiles().length; i++) {
            uploader.removeFile(uploader.getFiles()[i]);
        }
        uploader.reset();
        $('#dataUrl').val('');
    });
    $('#MouthTime').datepicker({
        language: "zh-CN",
        todayHighlight: true,
        format: 'yyyy-mm',
        autoclose: true,
        startView: 1,
        maxViewMode:2,
        minViewMode:1
    });
    $('#MouthDay').datepicker({
        language: "zh-CN",
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        autoclose: true,
        //startView: 2,
        maxViewMode:3
        //minViewMode:1
    });

    $('#STime').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });


    //结束时间：
    $('#ETime').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
    

    $('#s_time').datetimepicker({lang:'th'});
    $('#e_time').datetimepicker({lang:'th'});
    $('#sd_time,#ed_time').datetimepicker({
        lang:'th',
        timepicker:false,
        format:'Y-m-d',
        formatDate:'Y-m-d'
    });


    
    $(function() {
        $('.OpenUrl').click(function(){
            var frameSrc = $(this).attr("href");
            $('#IfRaMeModal').on('show.bs.modal',function() {
               $('iframe').attr("src",frameSrc);
            });
            $('#IfRaMeModal').modal({show:true});
            return false;
        });


        $('#demo-chosen-select').chosen();
        $('#demo-chosen-select1').chosen();
        $('#demo-chosen-select2').chosen();
        $('#demo-chosen-select3').chosen();
        $('#demo-chosen-select4').chosen();
        $('#demo-chosen-select5').chosen();

        $("form").submit(function (e) {
            //判断当前form是否要ajax
            var not=$(this).attr("ajax");
            if(not != 'n'){
                e.preventDefault(); //阻止自动提交表单
            }
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function(data){
                if(data.status == 1){
                    /*如果存在模态框将关闭*/
                    $('.modal').map(function() {
                        $(this).modal('hide');
                    });
                    $.niftyNoty({
                        type: 'success',
                        message : '<strong>'+data.info+'</strong> 3秒后自动跳转!',
                        container : 'floating',
                        timer : 3000
                    });
                    setTimeout(function(){
                        window.location.href=data.url
                    }, 3000);
                }
                else{
                    $.niftyNoty({
                        type: 'danger',
                        message : '<strong>'+data.info+'</strong>',
                        container : 'floating',
                        timer : 5000
                    });
                }
            }, 'json');
        });
    });

    function iFrameHeight() {
        var ifm= document.getElementById("iframepage");
        var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
        if(ifm != null && subWeb != null) {
            ifm.height = subWeb.body.scrollHeight;
            ifm.width = subWeb.body.scrollWidth;
        }
    }

    /*上传modal*/
    function upload_modal(domid){
        art.dialog.data('domid', domid);
        layer.open({
            type: 2,
            title:false,
            area: ['600px', '500px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Plugs/Upload/index/Mod/Admin');?>"
        });
    }
    //输入框图片预览
    function upload_view(data) {
        var image=$('#'+data).val();
        if(image==''){
            $.niftyNoty({
                type: 'danger',
                message : '<strong>无图片信息,无法发起预览</strong>',
                container : 'floating',
                timer : 5000
            });
        }else {
            $('.upload_view').fancybox({
                href: image
            });
        }
    }
    //img标签 图片预览
    function view_img(data) {
        $('img').fancybox({
            href: $('#'+data).attr('src')
        });
    }

    //修改密码
    function password() {
        $('#PassWords').modal({show:true});
    }


</script>
</body>
</html>