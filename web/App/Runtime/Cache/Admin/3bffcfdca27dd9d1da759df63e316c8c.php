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
    .form-control{width: 300px !important;}
    .imgborder{width: 300px;border: solid 1px #E4E4E4;height: 220px;padding: 5px;}
    .imgborder img { width:100%; display:block;height: 100%;}
    .chosen-container{display: block}
</style>
<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <form method="post" action="<?php echo U('abook_save');?>">
                <div class="panel-body">
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">商户录件</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">收款码ID</label>
                                <input class="form-control" type="text" name="codes" value="<?php echo ($code["codes"]); ?>" readonly>

                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属代理</label>
                                <input class="form-control" type="text"  value="<?php echo (agent_name($code["aid"])); ?>" readonly>
                                <input  type="hidden" name="agent_id" value="<?php echo ($code["aid"]); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">商户名称</label>
                                <input class="form-control" type="text"  name="mch_name" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')" value="<?php echo ($data["mch_name"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" style="color: red">商户类型</label>
                                <select id="mch_bus_type" data-placeholder="请选择..."  tabindex="2"  class="form-control" name="mch_bus_type" required="required">
                                    <option value="个人" <?php if(($data["mch_bus_type"]) == "个人"): ?>selected<?php endif; ?> >个人</option>
                                    <option value="企业" <?php if(($data["mch_bus_type"]) == "企业"): ?>selected<?php endif; ?> >企业</option>
                                    <option value="个体户" <?php if(($data["mch_bus_type"]) == "个体户"): ?>selected<?php endif; ?> >个体户</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">行业类别</label>
                                <select data-placeholder="请选择..." id="mch_industry" tabindex="2"    style="width: 300px;" name="mch_industry" required="required">
                                    <?php if(is_array($ind)): foreach($ind as $key=>$v): ?><option value="<?php echo ($v["pid"]); ?>"  <?php if($data["mch_industry"] == $v['pid']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属省份</label>
                                <select data-placeholder="请选择..."  data-width="100%"   id="provice"  class="form-control" name="mch_provice" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["provice"]); ?>"  <?php if($data["mch_provice"] == $v['provice']): ?>selected<?php endif; ?>><?php echo ($v["provice"]); ?></option><?php endforeach; endif; ?>
                                </select>

                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属城市</label>
                                <select id="selectCity" class="form-control" data-live-search="true" data-width="100%" name="mch_citys" required="required">
                                    <option value="<?php echo ($data["mch_citys"]); ?>" selected><?php echo ($data["mch_citys"]); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属区县</label>
                                <select id="selectDis" class="form-control" data-live-search="true" data-width="100%" name="mch_district" required="required">
                                    <option value="<?php echo ($data["mch_district"]); ?>" selected><?php echo ($data["mch_district"]); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">地址</label>
                                <input class="form-control" type="text" name="mch_address" value="<?php echo ($data["mch_address"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')"  required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">电话</label>
                                <input class="form-control" type="text" name="mch_tel" value="<?php echo ($data["mch_tel"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">负责人</label>
                                <input class="form-control" type="text" name="mch_card_name" value="<?php echo ($data["mch_card_name"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')"  required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">负责人身份证</label>
                                <input class="form-control" type="text" name="mch_card_id" value="<?php echo ($data["mch_card_id"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="qy_data">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">企业名称</label>
                                <input class="form-control" type="text" name="qy_name" value="<?php echo ($data["qy_name"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">工商注册号</label>
                                <input class="form-control" type="text" name="qy_cid" value="<?php echo ($data["qy_cid"]); ?>" >
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人姓名</label>
                                <input class="form-control" type="text" name="qy_fr_name" value="<?php echo ($data["qy_fr_name"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人身份证号</label>
                                <input class="form-control" type="text" name="qy_fr_cid" value="<?php echo ($data["qy_fr_cid"]); ?>" >
                            </div>
                        </div>
                    </div>
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">结算信息</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户银行</label>
                                <select data-placeholder="请选择..." id="mch_bank_list" tabindex="2"    style="width: 300px;" name="mch_bank_list" required="required">
                                    <?php if(is_array($bank_list)): foreach($bank_list as $key=>$v): ?><option value="<?php echo ($v["bnkcd"]); ?>"  <?php if($data["mch_bank_list"] == $v['bnkcd']): ?>selected<?php endif; ?>><?php echo ($v["bnknm"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">银行卡号</label>
                                <input class="form-control" type="text" name="mch_bank_cid" value="<?php echo ($data["mch_bank_cid"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户名称</label>
                                <input class="form-control" type="text" name="mch_bank_name" value="<?php echo ($data["mch_bank_name"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">帐户类型</label>
                                <select id="mch_bank_type" data-placeholder="请选择..."  tabindex="2"  class="form-control" name="mch_bank_type" required="required">
                                    <option value="个人账户" <?php if($data["mch_bank_type"] == '个人账户'): ?>selected<?php endif; ?> >个人账户</option>
                                    <option value="企业账户" <?php if($data["mch_bank_type"] == '企业账户'): ?>selected<?php endif; ?> >企业账户</option>
                                </select>

                                <input class="form-control" id="Gr" style="display: none" type="text"  value="个人账户" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户省份</label>
                                <select data-placeholder="请选择..."  tabindex="2"  id="mch_bank_provice"  style="width: 300px;" name="mch_bank_provice" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["provice"]); ?>"  <?php if($data["mch_bank_provice"] == $v['provice']): ?>selected<?php endif; ?>><?php echo ($v["provice"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" id="depositCity">
                                <label class="control-label">开户城市</label>
                                <select id="selectBCity" class="form-control" data-live-search="true" data-width="100%" name="mch_bank_citys" required="required">
                                    <option value="<?php echo ($data["mch_bank_citys"]); ?>" selected><?php echo ($data["mch_bank_citys"]); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" id="depositLBnk">
                                <label class="control-label">开户支行</label>
                                <select  class="form-control" id="selectLBnk" required="required">
                                    <option value="<?php echo ($data["mch_linkbnk"]); ?>" selected="selected"><?php echo (reload_banks($data["mch_linkbnk"])); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">联行号</label>
                                <input class="form-control" id="lianhanghao" type="text" name="mch_linkbnk" value="<?php echo ($data["mch_linkbnk"]); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">预留手机号码</label>
                                <input class="form-control" type="text" name="mch_bank_tel" value="<?php echo ($data["mch_bank_tel"]); ?>" onkeyup="this.value=this.value.replace(/^\s+|\s+$/g,'')" required>
                            </div>
                        </div>
                    </div>
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">证件信息</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证正面</span></label>
                                <?php echo uploads_map('mch_img_z',$data['mch_img_z']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证反面</span></label>
                                <?php echo uploads_map('mch_img_p',$data['mch_img_p']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">结算银行卡</span></label>
                                <?php echo uploads_map('mch_img_bank',$data['mch_img_bank']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">手持照</span></label>
                                <?php echo uploads_map('mch_img_s',$data['mch_img_s']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">营业执照</span></label>
                                <?php echo uploads_map('mch_img_yyzz',$data['mch_img_yyzz']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">收款人授权函</span></label>
                                <?php echo uploads_map('mch_img_sqh',$data['mch_img_sqh']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证正面(授权人)</span></label>
                                <?php echo uploads_map('mch_img_auth_z',$data['mch_img_auth_z']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证反面(授权人)</span></label>
                                <?php echo uploads_map('mch_img_auth_p',$data['mch_img_auth_p']);?>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">门头照片</span></label>
                                <?php echo uploads_map('mch_img_m1',$data['mch_img_m1']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">门店内景</span></label>
                                <?php echo uploads_map('mch_img_m2',$data['mch_img_m2']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">附加照片1</span></label>
                                <?php echo uploads_map('mch_img_m3',$data['mch_img_m3']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">附加照片2</span></label>
                                <?php echo uploads_map('mch_img_m4',$data['mch_img_m4']);?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button type="submit" class="btn btn-danger" >提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#mch_bus_type,#mch_industry,#mch_bank_list,#mch_bank_provice,#rate,#provice').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
        $('#mch_bus_type').change(BusChange);
        $('#mch_bank_type').change(BTypeChange);
        $('#provice').change(provinceChange); //省份
        $('#selectCity').change(cityChange); //市级
        $('#mch_bank_provice').change(BproChange); //银行省份
        $('#selectBCity').change(BcityChange); //市级
        $('#mch_bank_list').change(bankChange);
        $('#selectLBnk').change(selectLBnkval);
        BusChange();
        BTypeChange();
    });

    function BTypeChange() {
        var type= $('#mch_bank_type option:selected').val();
        if(type=='个人账户'){
            $('.Sq').show();
        }else{
            $('.Sq').hide();
        }
    }

    function BusChange() {
        var Bus= $('#mch_bus_type option:selected').val();
        if(Bus=='个人'){
            $('.Yz,#qy_data').hide();
            $('.Sq,#Gr').show();
            $('#mch_bank_type').hide();
        }else{
            $('#mch_bank_type,#qy_data').show();
            $('.Yz').show();
            $('#Gr').hide();
        }
    }
    //当支行变了 联行号就变
    function selectLBnkval() {
        var lh= $('#selectLBnk option:selected').val();
        $('#lianhanghao').val(lh);
    }
    //当开户行放生改变时，带出默认的支行
    function bankChange(){
        var cityId = $('#selectBCity option:selected').val();
        if(cityId!=null && cityId!="" && typeof(cityId)!='undefined'){
            setLBnk(cityId);
        }
    }
    //
    function BproChange() {
        var provinceId = $('#mch_bank_provice option:selected').val();
        setBCity(provinceId);
    }

    //当省份改变时，带出默认城市
    function provinceChange(){
        var provinceId = $('#provice option:selected').val();
        setCity(provinceId);
    }
    //当城市改变的时候,改变区县
    function cityChange(){
        var cityId = $('#selectCity option:selected').val();
        setdistrict(cityId);
    }

    //当城市改变的时候,改变银行
    function BcityChange(){
        var cityId = $('#selectBCity option:selected').val();
        setLBnk(cityId);
    }

    //设置银行所属城市
    function setBCity(provinceId) {
        var cityStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"/Pays/Reg/A_area_city",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                //alert(data.length);
                for(var i=0; i<data.length; i++){
                    cityStr += "<option value='"+data[i]+"'>"+data[i]+"</option>";
                }
                $("#selectBCity").find("option").remove();
                $("#selectBCity").append(cityStr);
                $('#selectBCity').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectBCity").trigger("chosen:updated");
                $("#selectBCity").trigger("liszt:updated");
                var cityId = data[0];
                setLBnk(cityId);
            }
        });
    }

    //设置显示城市
    function setCity(provinceId){
        var cityStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"/Pays/Reg/A_area_city",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                //alert(data.length);
                for(var i=0; i<data.length; i++){
                    cityStr += "<option value='"+data[i]+"'>"+data[i]+"</option>";
                    //alert(cityStr);
                }
                $("#selectCity").find("option").remove();
                $("#selectCity").append(cityStr);
                $('#selectCity').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectCity").trigger("chosen:updated");
                $("#selectCity").trigger("liszt:updated");
                var cityId = data[0];
                setdistrict(cityId);
            }
        });
    }


    //设置显示区县
    function setdistrict(provinceId){
        var disStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"/Pays/Reg/A_area_disc",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                //alert(data.length);
                for(var i=0; i<data.length; i++){
                    disStr += "<option value='"+data[i]+"'>"+data[i]+"</option>";
                    //alert(cityStr);
                }
                $("#selectDis").find("option").remove();
                $("#selectDis").append(disStr);
                $('#selectDis').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectDis").trigger("chosen:updated");
                $("#selectDis").trigger("liszt:updated");
            }
        });
    }


    //当开户市改变且开户行已选择时， 选择开户支行
    function setLBnk(cityId){
        var depositBnk = $('#mch_bank_list option:selected').val();
        var params = {depositBnk:depositBnk,cityId:cityId};
        $('#lianhanghao').val("");
        var linkBnkStr = "<option value=''>请选择</option>";
        if(depositBnk&&cityId) {
            $.ajax({
                data: params,
                url: "/Pays/Reg/A_bnkLink",
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.list == null) {
                        linkBnkStr = "<option value=''>此区域无支行信息,请重新选择</option>";
                    } else {
                        for (var i = 0; i < data.list.length; i++) {
                            linkBnkStr += "<option value='" + data.list[i].banking + "'>" + data.list[i].address + "</option>";
                        }
                    }
                    $("#selectLBnk").find("option").remove();
                    $("#depositLBnk select").append(linkBnkStr);
                    $('#selectLBnk').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                    $("#selectLBnk").trigger("chosen:updated");
                    $("#selectLBnk").trigger("liszt:updated");
                }
            });
        }
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