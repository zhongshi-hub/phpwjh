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
            <form id="in_form" method="post" action="<?php echo U('shopCreate');?>" >
                <div class="panel-body">
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">门店创建</p>

                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">所属类目ID<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="category_id" value="" placeholder="20**************" required>
                                <!--<select class="form-control" data-placeholder="请选择..."  tabindex="2" name="category_id" required="required">-->
                                    <!--<?php if(is_array($cate)): foreach($cate as $key=>$v): ?>-->
                                        <!--<option value="<?php echo ($v["id"]); ?>"><?php echo ($v["link"]); ?></option>-->
                                    <!--<?php endforeach; endif; ?>-->
                                <!--</select>-->
                            </div>
                            <span>下载全行业类目ID: <a href="http://t.cn/Ai0umTzW" style="color: #0a6aa1">http://t.cn/Ai0umTzW</a> 按照对应类目要求填写对应资料</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">主门店名 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="main_shop_name" value="<?php echo ($data["mch_name"]); ?>" required>
                                <span>比如：肯德基；主店名里不要包含分店名，如“万塘路店”。主店名长度不能超过20个字符。</span>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">分店名称</label>
                                <input class="form-control" type="text"  name="branch_shop_name" value="" >
                                <span>比如：万塘路店，与主门店名合并在客户端显示为：肯德基(万塘路店)。</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">所属省份<span class="ccl_r">*</span></label>
                                <select data-placeholder="请选择..."  data-width="100%"   id="provice"  class="form-control" name="province_code" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["mid"]); ?>" <?php if($data["mch_provice"] == $v['name']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">所属城市<span class="ccl_r">*</span></label>
                                <select id="selectCity" class="form-control" data-live-search="true" data-width="100%" name="city_code" required="required">
                                    <option value="">请选择市</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">所属区县<span class="ccl_r">*</span></label>
                                <select id="selectDis" class="form-control" data-live-search="true" data-width="100%" name="district_code" required="required">
                                    <option value="">请选择区/县</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">门店详细地址<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="address" value="<?php echo ($data["mch_address"]); ?>" required>
                            </div>
                        </div>
                    </div>
                    <span>门店详细地址，地址字符长度在4-50个字符，注：不含省市区。门店详细地址按规范格式填写地址，以免影响门店搜索及活动报名：例1：道路+门牌号，“人民东路18号”；例2：道路+门牌号+标志性建筑+楼层，“文苑北路1552号欢乐广场1楼”。</span>
                    <hr>
                    <div class="row">
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label class="control-label">经度 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="longitude" value="" placeholder="114.266418" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">纬度 <span class="ccl_r">*</span></label>
                                <input class="form-control" type="text"  name="latitude" value="" placeholder="30.548828" required>

                            </div>
                        </div>
                    </div>
                    <span>注：高德坐标系。经纬度是门店搜索和活动推荐的重要参数，录入时请确保经纬度参数准确。高德经纬度查询：<a href="http://lbs.amap.com/console/show/picker" target="_blank">http://lbs.amap.com/console/show/picker</a></span>

                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">门店电话号码<span class="ccl_r">*</span></label>
                                <input class="form-control" type="text" name="contact_number" value="<?php echo ($data["mch_tel"]); ?>" required>
                                <span>支持座机和手机，只支持数字和+-号，在客户端对用户展现， 支持多个电话， 以英文逗号分隔。 </span>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">门店店长电话号码</label>
                                <input class="form-control" type="text" name="notify_mobile" value="" >
                                <span>用于接收门店状态变更通知，收款成功通知等通知消息， 不在客户端展示。 </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>门店首图<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('main_image','',1);?>
                                <span>门店首图，非常重要，推荐尺寸2000*1500。</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>门头照片<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('audit_images[]',$data['mch_img_m1'],1,'audit_images1');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>内景照片1<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('audit_images[]',$data['mch_img_m2'],1,'audit_images2');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>内景照片2<span class="ccl_r">*</span></span></label>
                                <?php echo uploads_map('audit_images[]','',1,'audit_images3');?>
                            </div>
                        </div>
                    </div>
                    <span>门店审核时需要的图片；至少包含一张门头照片，两张内景照片，必须反映真实的门店情况，审核才能够通过</span>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业时间</label>
                                <input class="form-control" type="text" name="business_time" value="">
                            </div>
                        </div>
                    </div>
                    <span>请严格按"周一-周五 09:00-20:00,周六-周日 10:00-22:00"的格式进行填写，时间段不能重复，最多支持两个时间段，24小时营业请填写"00:00-23:59" </span>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业执照</label>
                                <?php echo uploads_map('licence',$data['mch_img_yyzz']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业执照编号</label>
                                <input class="form-control"  type="text" name="licence_code" placeholder="营业执照编号，只支持输入中文，英文和数字">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业执照名称</label>
                                <input class="form-control"  type="text" name="licence_name" placeholder="门店营业执照名称">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业执照过期时间</label>
                                <input class="form-control"  type="text" name="licence_expires" placeholder="格式：2020-10-20或长期。严格按照格式填写。">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">许可证</label>
                                <?php echo uploads_map('business_certificate','');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">许可证有效期</label>
                                <input class="form-control"  type="text" name="business_certificate_expires" placeholder="格式：2020-03-20或长期">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">是否在其他平台开店<span class="ccl_r">*</span></label>
                                <select class="form-control" data-placeholder="请选择..."  tabindex="2" name="is_operating_online" required="required">
                                    <option value="F">未开店</option>
                                    <option value="T">有开店</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">其他平台开店的店铺链接url</label>
                                <input class="form-control"  type="text" name="online_url" placeholder="多个url使用英文逗号隔开">
                            </div>
                        </div>
                    </div>
                    <span>如选择在其它平台有开店,其他平台开店的店铺链接url必填，多个url使用英文逗号隔开。</span>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>门店授权函</span></label>
                                <?php echo uploads_map('auth_letter','');?>
                                <span>营业执照与签约账号主体不一致时需要。</span>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>其他资质1</span></label>
                                <?php echo uploads_map('other_authorization[]','','','other_authorization1');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>其他资质2</span></label>
                                <?php echo uploads_map('other_authorization[]','','','other_authorization2');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span>其他资质3</span></label>
                                <?php echo uploads_map('other_authorization[]','','','other_authorization3');?>
                            </div>
                        </div>
                    </div>
                    <span>其他资质。用于上传营业证照、许可证照外的其他资质，除已上传许可证外的其他许可证也可以在该字段上传。</span>
                </div>

                <div class="panel-footer text-justify">
                    <a class="btn btn-warning" href="<?php echo U('WxPay/xwList');?>">返回列表</a>
                    <input type="hidden" name="mid" value="<?php echo ($_GET['id']); ?>">
                    <input type="hidden" name="business_code" value="<?php echo ($_GET['business_code']); ?>">
                    <button  type="submit" class="btn btn-danger" >确认信息并进件</button>
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
    $('#in_form').submit(function (e) {
        e.preventDefault();
        var form=$(this);
        var ajax_data = form.serialize();
        var actionurl = form.attr("action");
        loading('接口同步进件中...');
        $.post(actionurl, ajax_data, function (data) {
            if (data.status) {
                layer.closeAll();
                layer.alert(data.info, {
                    skin: 'layui-layer-lan'
                    ,title: "进件结果"
                    ,offset: '100px'
                },function (e) {
                    window.location.href="<?php echo U('Isv/shopList');?>";
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


    $(function() {
        $('.OpenUrl').click(function(){
            var frameSrc = $(this).attr("href");
            $('#IfRaMeModal').on('show.bs.modal',function() {
                $('iframe').attr("src",frameSrc);
            });
            $('#IfRaMeModal').modal({show:true});
            return false;
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

</script>
</body>
</html>