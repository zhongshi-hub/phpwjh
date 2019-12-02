<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
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

                                <div class="profile-wrap" style="text-align: center">
                                    <div class="pad-btm">
                                        <img class="img-border" src="<?php echo ($WxData["qrc_img"]); ?>" style="border-radius: 10%;width: 55px;height: 55px;">
                                    </div>
                                    <p class="mnp-name"><?php echo ($WxData["name"]); ?></p>
                                    <a href="#profile-nav" class="box-block collapsed" data-toggle="collapse" aria-expanded="false" style="text-align: left;margin-top: 20px">
                                       <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                        </span>
                                        <span class="mnp-desc">切换公众号</span>
                                    </a>
                                </div>
                                <div id="profile-nav" class="list-group bg-trans collapse" aria-expanded="false" style="height: 2px;">
                                    <?php if(is_array($WxList)): foreach($WxList as $key=>$v): ?><a href="<?php echo U('',array('token'=>$v['token']));?>" class="list-group-item">
                                        <i class="fa  fa-weixin" style="width:20px;text-align: center;font-size: 13px"></i> <?php echo ($v["name"]); ?>
                                    </a><?php endforeach; endif; ?>
                                </div>
                            </div>

                            <ul id="mainnav-menu" class="list-group">
                                <li>
                                    <a href="<?php echo U('Index/index');?>" onclick="if(confirm('确定返回主控制台吗?')==false)return false;">
                                        <i class="fa fa-mail-reply-all" style="width:20px;text-align: center;font-size: 14px"> </i>
                                        <span class="menu-title"><strong>返回主控制台</strong></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo U('WeiXin/index',array('token'=>$_GET['token']));?>">
                                        <i class="fa fa-desktop" style="width:20px;text-align: center;font-size: 14px"> </i>
                                        <span class="menu-title"><strong>微信控制台</strong></span>
                                    </a>
                                </li>
                                <!--菜单-->
                                <?php if(is_array($WxMeun)): $k = 0; $__LIST__ = $WxMeun;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if(($rule_name_s) == $vo['name']): ?>class="active-sub active"<?php endif; ?>>
                                    <a href="<?php if(empty($vo['_data'])): echo U($vo['name'],array('token'=>$_GET['token'])); else: ?>#<?php endif; ?>">
                                    <i class="fa <?php echo $vo['icon'];?>" style="width:20px;text-align: center;font-size: 13px"> </i>
                                    <span class="menu-title"><strong><?php echo $vo['title'];?></strong></span>
                                    <?php if(!empty($vo['_data'])): ?><i class="arrow"></i><?php endif; ?>
                                    </a>
                                    <?php if(!empty($vo['_data'])): ?><!--子菜单-->
                                        <ul class="collapse">
                                            <?php if(is_array($vo['_data'])): $i = 0; $__LIST__ = $vo['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li <?php if(($rule_name) == $sub['name']): ?>class="active-link"<?php endif; ?>><a href="<?php echo U($sub['name'],array('token'=>$_GET['token']));?>"><?php echo $sub['title'];?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul><?php endif; ?>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!--头部信息结束-->

<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-control">
                    <span class="text-muted">

                    </span>
                    <span class="text-muted"><small></small></span>
                </div>
                <h3 class="panel-title"><?php echo $title;?></h3>
            </div>
            <link href="/Source/Css/wechat/common.css" rel="stylesheet">
            <div class="panel-body">
                <ul class="xun-page-tab" style="margin-left: 3px;margin-right: 3px">
                    <li class="active">
                        <a href="<?php echo U('WeiXin/Material',array('token'=>$_SESSION['Admin']['WxToken']));?>">微信图文</a>
                    </li>
                    <li>
                        <a href="<?php echo U('WeiXin/MaterialImage',array('token'=>$_SESSION['Admin']['WxToken']));?>">图片</a>
                    </li>
                </ul>
                    <div id="main" ng-controller="materialDisplay" class="ng-scope">
                        <div class="material">
                            <div class="material-appmsg">
                                <div class="material-list-head clearfix">
                                    <div class="info col-sm-6 xun-padding-none">
                                        <div class="xun-form">
                                            <div class="form-controls">
                                                <form action="" method="post" class="ng-pristine ng-valid">
                                                    <div class="input-group">
                                                        <input id="" name="title" class="form-control" size="40" value="" placeholder="标题/作者/摘要" type="text">
                                                        <span class="input-group-btn">
										<button class="btn btn-default"><i class="fa fa-search"></i></button>
									</span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <a href="javascript:;"  onclick="sync('news')" class="btn btn-default">同步微信</a>
                                        <a href="javascript:;"  onclick="news_message();"  class="btn btn-primary xun-margin-left">新建图文消息</a>
                                    </div>
                                </div>

                                <!--multi-->
                                <div class="material-appmsg-list " style="margin-left: 0px;margin-right: 0px;">

                                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><div class="material-appmsg-item <?php if(($v["count"]) != "1"): ?>multi<?php endif; ?> ">
                                        <div class="appmsg-content">
                                            <div class="appmsg-info">
                                                <em class="appmsg-date" style="color: #666">更新于  <?php echo ($v["update_time"]); ?></em>
                                            </div>
                                                <?php if(($v["count"]) == "1"): if(is_array($v["item"])): foreach($v["item"] as $k=>$val): ?><div class="appmsg-item">
                                                        <h4 class="appmsg-title">
                                                            <a href="" target="_blank"><?php echo ($val["title"]); ?> </a>
                                                        </h4>
                                                        <div class="appmsg-thumb" style="background-image:url('<?php echo ($val["img"]); ?>')">
                                                        </div>
                                                        <p class="appmsg-desc" style="color: #666;text-align: left;padding-bottom: 1px;margin-bottom:1px;margin-top:10px;white-space: inherit"><?php echo ($val["digest"]); ?></p>
                                                        <a href="<?php echo ($val["url"]); ?>" target="_blank" class="cover-dark">
                                                            <div class="edit-mask-content">
                                                                微信预览
                                                            </div>
                                                            <span class="vm-box"></span>
                                                        </a>
                                                     </div><?php endforeach; endif; ?>
                                                <?php else: ?>
                                                    <?php if(is_array($v["item"])): foreach($v["item"] as $k=>$val): ?><div class=" <?php if(($k) != "0"): ?>appmsg-item has-cover<?php else: ?>cover-appmsg-item<?php endif; ?> ">
                                                        <?php if(($k) != "0"): ?><div class="appmsg-thumb" style="background-image:url('<?php echo ($val["img"]); ?>">
                                                            </div>
                                                            <h4 class="appmsg-title">
                                                                <a href="" target="_blank" style="white-space: normal"><?php echo ($val["title"]); ?></a>
                                                            </h4>
                                                            <a href="<?php echo ($val["url"]); ?>" target="_blank" class="cover-dark">
                                                                <div class="edit-mask-content">
                                                                    <p class="">
                                                                        预览文章 </p>
                                                                </div>
                                                                <span class="vm-box"></span>
                                                            </a>
                                                        <?php else: ?>
                                                            <h4 class="appmsg-title">
                                                                <a href="" target="_blank" style="white-space: inherit;margin-left: 10px;margin-right: 5px"><?php echo ($val["title"]); ?></a>
                                                            </h4>
                                                            <div class="appmsg-thumb" style="background-image:url('<?php echo ($val["img"]); ?>')">
                                                            </div>
                                                            <p class="appmsg-desc"><?php echo ($val["digest"]); ?></p>
                                                            <a href="<?php echo ($val["url"]); ?>" target="_blank" class="cover-dark">
                                                                <div class="edit-mask-content">
                                                                    微信预览
                                                                </div>
                                                                <span class="vm-box"></span>
                                                            </a><?php endif; ?>
                                                    </div><?php endforeach; endif; endif; ?>


                                        </div>
                                        <!--<div class="appmsg-opr">
                                            <ul>
                                                <li class="appmsg-opr-item">
                                                    <a href="" class="add-tooltip" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="编辑">&nbsp;<i class="fa fa-edit" style="font-size: 20px;color: #98999a;vertical-align: middle;"></i></a>
                                                </li>
                                                <li class="appmsg-opr-item">
                                                    <a class="add-tooltip" href="javascript:void(0);" ng-click="del_material('news', '7', 'wechat')" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="删除">&nbsp;<i class="fa fa-trash-o" style="font-size: 20px;color: #98999a;vertical-align: middle;"></i></a>
                                                </li>
                                            </ul>
                                        </div>-->
                                    </div><?php endforeach; endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="text-right">
                        <ul class="pagination"><?php echo ($page); ?></ul>
                    </div>

                   <link rel="stylesheet" href="/Source/loading/css/loading.css">
                   <script src="/Source/loading/js/loading.js"></script>
                   <script src="/Source/Css/wechat/jquery.wookmark.min.js"></script>
                   <script>
                        $('.material-appmsg-list .material-appmsg-item').wookmark({
                            align: 'left',
                            autoResize: true,
                            container: $('.material-appmsg-list'),
                            itemWidth: 289,
                            offset: 30
                        });
                       function sync(type) {
                           load_show('show','news','图文信息同步中。请等待。。。');
                           var ajax_data = {type:type};
                           var actionurl = '<?php echo U("GetMaterial");?>';
                           $.post(actionurl, ajax_data, function(data){
                               load_show('hide','news');
                               if(data.status == 1){
                                   $.niftyNoty({
                                       type: 'success',
                                       message : '<strong>'+data.info+'</strong>',
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
                                   if(data.url){
                                       setTimeout(function(){
                                           window.location.href=data.url
                                       }, 3000);
                                   }
                               }
                           }, 'json');
                       }

                       /*show hide*/
                       function load_show(type,name,txt) {
                           if(type=='show') {
                               $('body').loading({
                                   loadingWidth: 240,
                                   title: '',
                                   name: name,
                                   discription: txt,
                                   discColor:'#fff',
                                   direction: 'column',
                                   type: 'origin',
                                   originDivWidth: 40,
                                   originDivHeight: 40,
                                   originWidth: 6,
                                   originHeight: 6,
                                   smallLoading: false,
                                   loadingMaskBg: 'rgba(0,0,0,0.2)'
                               });
                           }else{
                              removeLoading(name);
                           }
                       }
                       
                       function news_message() {
                           $.niftyNoty({
                               type: 'danger',
                               message : '<strong>温馨提示:系统不支持新建图文<br>请在微信公众号平台(mp.weixin.qq.com)中新建,然后在此页面同步微信数据</strong>',
                               container : 'floating',
                               timer : 5000
                           });
                       }
                   </script>
                </div>
            </div>
        </div>
    </div>
</div>