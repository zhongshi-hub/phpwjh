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
                <h3 class="panel-title">自定义菜单</h3>

            </div>
            <link href="/Source/Css/wechat/menu.css" rel="stylesheet">
            <div class="panel-body">
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="one">
                        <div class="widget-body no-padding">
                            <div class="weixin-menu-setting clearfix">
                                <div class="mobile-menu-preview">
                                    <div class="mobile-head-title">公众号名称</div>
                                    <ul class="menu-list" id="menu-list">
                                        <li class="add-item extra" id="add-item">
                                            <a href="javascript:;" class="menu-link" title="添加菜单"><i class="weixin-icon add-gray"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="weixin-body">
                                    <div class="weixin-content" style="display:none">
                                        <div class="item-info">
                                            <form id="form-item" class="form-item" data-value="" >
                                                <div class="item-head">
                                                    <h4 id="current-item-name">添加子菜单</h4>
                                                    <div class="item-delete"><a href="javascript:;" id="item_delete">删除菜单</a></div>
                                                </div>
                                                <div style="margin-top: 20px;">
                                                    <dl>
                                                        <dt id="current-item-option"><span class="is-sub-item">子</span>菜单标题：</dt>
                                                        <dd><div class="input-box"><input id="item_title" name="item-title" type="text" value=""></div></dd>
                                                    </dl>
                                                    <dl class="is-item">
                                                        <dt id="current-item-type"><span class="is-sub-item">子</span>菜单内容：</dt>
                                                        <dd>
                                                            <input id="type1" type="radio" name="type" value="click"><label for="type1" data-editing="1"><span class="lbl_content">发送消息</span></label>
                                                            <input id="type2" type="radio" name="type" value="view" ><label for="type2"  data-editing="1"><span class="lbl_content">跳转网页</span></label>
                                                        </dd>
                                                    </dl>
                                                    <div id="menu-content" class="is-item">
                                                        <div class="viewbox is-view">
                                                            <p class="menu-content-tips">点击该<span class="is-sub-item">子</span>菜单会跳到以下链接</p>
                                                            <dl>
                                                                <dt>页面地址：</dt>
                                                                <dd><div class="input-box"><input type="text" id="url" name="url"></div>
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                        <div class="clickbox is-click" style="display: none;">
                                                            <input type="hidden" name="key" id="key" value="" />
                                                            <span class="create-click"><a href="response-select.html" id="select-resources"><i class="weixin-icon big-add-gray"></i><strong>选择现有资源</strong></a></span>
                                                            <span class="create-click"><a href="response-edit.html" id="add-resources"><i class="weixin-icon big-add-gray"></i><strong>添加新资源</strong></a></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                    <div class="no-weixin-content">
                                        点击左侧菜单进行编辑操作
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-4 text-center text-danger">
                                    <i class="fa fa-lightbulb-o"></i> <small>可直接拖动菜单排序</small>
                                </div>
                                <div class="col-xs-8 text-center"><a href="javascript:;" id="menuSyn" class="btn btn-danger">保存并发布</a></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .col-lg-2 {
        width: 20% !important;
    }
</style>
<script src="/Source/assets/libs/Sortable/Sortable.min.js"></script>
<script type="text/javascript">
    var menu = [{"name":"Xun", "sub_button":[{"name":"官网", "type":"view", "url":"http://www.baidu.com"}, {"name":"在线演示", "type":"view", "url":"http://www.baidu.com"}, {"name":"文档", "type":"view", "url":"http://www.baidu.com"}]}, {"name":"在线客服", "type":"click", "key":"58cb852984970"}, {"name":"关于我们", "type":"click", "key":"58bf944aa0777"}];
    var responselist = {"58adaf7876aab":"签到送积分", "58bf944aa0777":"关于我们", "58c7d908c4570":"自动回复1", "58cb852984970":"联系客服", "58fdfaa9e1965":"自动回复2"};
    String.prototype.subByte = function (start, bytes) {
        for (var i = start; bytes > 0; i++) {
            var code = this.charCodeAt(i);
            bytes -= code < 256 ? 1 : 2;
        }
        return this.slice(start, i + bytes)
    };
    var init_menu = function (menu) {
        var str = "";
        var items = menu;
        var type = action = "";
        for (i in items) {
            if (items[i]['sub_button'] != undefined) {
                type = action = "";
            } else {
                type = items[i]['type'];
                if (items[i]['url'] != undefined)
                    action = "url|" + items[i]['url'];
                if (items[i]['key'] != undefined)
                    action = "key|" + items[i]['key'];
            }
            str += '<li id="menu-' + i + '" class="menu-item" data-type="' + type + '" data-action="' + action + '" data-name="' + items[i]['name'] + '"> <a href="javascript:;" class="menu-link"> <i class="icon-menu-dot"></i> <i class="weixin-icon sort-gray"></i> <span class="title">' + items[i]['name'] + '</span> </a>';
            var tem = '';
            if (items[i]['sub_button'] != undefined) {
                var sub_menu = items[i]['sub_button'];
                for (j in sub_menu) {
                    type = sub_menu[j]['type'];
                    if (sub_menu[j]['url'] != undefined)
                        action = "url|" + sub_menu[j]['url'];
                    if (sub_menu[j]['key'] != undefined)
                        action = "key|" + sub_menu[j]['key'];
                    tem += '<li id="sub-menu-' + j + '" class="sub-menu-item" data-type="' + type + '" data-action="' + action + '" data-name="' + sub_menu[j]['name'] + '"> <a href="javascript:;"> <i class="weixin-icon sort-gray"></i><span class="sub-title">' + sub_menu[j]['name'] + '</span></a> </li>';
                }
            }
            str += '<div class="sub-menu-box" style="' + (i != 0 ? 'display:none;' : '') + '"> <ul class="sub-menu-list">' + tem + '<li class=" add-sub-item"><a href="javascript:;" title="添加子菜单"><span class=" "><i class="weixin-icon add-gray"></i></span></a></li> </ul> <i class="arrow arrow-out"></i> <i class="arrow arrow-in"></i></div>';
            str += '</li>';
        }
        $("#add-item").before(str);
    };
    var refresh_type = function () {
        if ($('input[name=type]:checked').val() == 'view') {
            $(".is-view").show();
            $(".is-click").hide();
        } else {
            $(".is-view").hide();
            $(".is-click").show();
        }
    };
    //初始化菜单
    init_menu(menu);
    //拖动排序
    new Sortable($("#menu-list")[0], {draggable: 'li.menu-item'});
    $(".sub-menu-list").each(function () {
        new Sortable(this, {draggable: 'li.sub-menu-item'});
    });
    //添加主菜单
    $(document).on('click', '#add-item', function () {
        var menu_item_total = $(".menu-item").size();
        if (menu_item_total < 3) {
            var item = '<li class="menu-item" data-type="click" data-action="key|" data-name="添加菜单" > <a href="javascript:;" class="menu-link"> <i class="icon-menu-dot"></i> <i class="weixin-icon sort-gray"></i> <span class="title">添加菜单</span> </a> <div class="sub-menu-box" style=""> <ul class="sub-menu-list"><li class=" add-sub-item"><a href="javascript:;" title="添加子菜单"><span class=" "><i class="weixin-icon add-gray"></i></span></a></li> </ul> <i class="arrow arrow-out"></i> <i class="arrow arrow-in"></i> </div></li>';
            var itemDom = $(item);
            itemDom.insertBefore(this);
            itemDom.trigger("click");
            $(".sub-menu-box", itemDom).show();
            new Sortable($(".sub-menu-list", itemDom)[0], {draggable: 'li.sub-menu-item'});
        }
    });
    $(document).on('change', 'input[name=type]', function () {
        refresh_type();
    });
    $(document).on('click', '#item_delete', function () {
        var current = $("#menu-list li.current");
        var prev = current.prev("li[data-type]");
        var next = current.next("li[data-type]");

        if (prev.size() == 0 && next.size() == 0 && $(".sub-menu-box", current).size() == 0) {
            last = current.closest(".menu-item");
        } else if (prev.size() > 0 || next.size() > 0) {
            last = prev.size() > 0 ? prev : next;
        } else {
            last = null;
            $(".weixin-content").hide();
            $(".no-weixin-content").show();
        }
        $("#menu-list li.current").remove();
        if (last) {
            last.trigger('click');
        } else {
            $("input[name='item-title']").val('');
        }
        updateChangeMenu();
    });

    //更新修改与变动
    var updateChangeMenu = function () {
        var title = $("input[name='item-title']").val();
        var type = $("input[name='type']:checked").val();
        var key = value = '';
        if (type == 'view') {
            key = 'url';
        } else {
            key = 'key';
        }
        value = $("input[name='" + key + "']").val();

        if (key == 'key') {
            var keytitle = typeof responselist[value] != 'undefined' ? responselist[value] : '';
            var cont = $(".is-click .create-click:first");
            $(".keytitle", cont).remove();
            cont.append('<div class="keytitle">资源名:' + keytitle + '</div>');
        }
        var currentItem = $("#menu-list li.current");
        if (currentItem.size() > 0) {
            currentItem.attr('data-type', type);
            currentItem.attr('data-action', key + "|" + value);
            if (currentItem.siblings().size() == 4) {
                $(".add-sub-item").show();
            } else if (false) {

            }
            currentItem.children("a").find("span").text(title.subByte(0, 16));
            $("input[name='item-title']").val(title);
            currentItem.attr('data-name', title);
            $('#current-item-name').text(title);
        }
        menuUpdate();
    };
    //更新菜单数据
    var menuUpdate = function () {
        $.post("data/edit.json", {menu: JSON.stringify(getMenuList())}, function (data) {
            if (data['code'] == 1) {
            } else {
                alert('Operation failed');
            }
        }, 'json');
    };

    //获取菜单数据
    var getMenuList = function () {
        var menus = new Array();
        var sub_button = new Array();
        var menu_i = 0;
        var sub_menu_i = 0;
        var item;
        $("#menu-list li").each(function (i) {
            item = $(this);
            var name = item.attr('data-name');
            var type = item.attr('data-type');
            var action = item.attr('data-action');
            if (name != null) {
                actions = action.split('|');
                if (item.hasClass('menu-item')) {
                    sub_menu_i = 0;
                    if (item.find('.sub-menu-item').size() > 0) {
                        menus[menu_i] = {"name": name, "sub_button": "sub_button"}
                    } else {
                        if (actions[0] == 'url') {
                            menus[menu_i] = {"name": name, "type": type, "url": actions[1]};
                        }else {
                            menus[menu_i] = {"name": name, "type": type, "key": actions[1]};
                        }
                    }
                    if (menu_i > 0) {
                        if (menus[menu_i - 1]['sub_button'] == "sub_button") {
                            menus[menu_i - 1]['sub_button'] = sub_button;
                        }else {
                            menus[menu_i - 1]['sub_button'];
                        }
                    }
                    sub_button = new Array();
                    menu_i++;
                } else {
                    if (actions[0] == 'url')
                        sub_button[sub_menu_i++] = {"name": name, "type": type, "url": actions[1]};
                    else
                        sub_button[sub_menu_i++] = {"name": name, "type": type, "key": actions[1]};
                }
            }
        });
        if (sub_button.length > 0) {
            var len = menus.length;
            menus[len - 1]['sub_button'] = sub_button;
        }
        return menus;
    };


    //添加子菜单
    $(document).on('click', ".add-sub-item", function () {
        var sub_menu_item_total = $(this).parent().find(".sub-menu-item").size();
        if (sub_menu_item_total < 5) {
            var item = '<li class="sub-menu-item" data-type="click" data-action="key|" data-name="添加子菜单"><a href="javascript:;"><span class=" "><i class="weixin-icon sort-gray"></i><span class="sub-title">添加子菜单</span></span></a></li>';
            var itemDom = $(item);
            //alert(sub_menu_item_total);
            itemDom.insertBefore(this);
            itemDom.trigger("click");
           // alert(sub_menu_item_total);
            if (sub_menu_item_total == 4) {
                $(this).hide();
            }

        }
        return false;
    });

    //主菜单子菜单点击事件
    $(document).on('click', ".menu-item, .sub-menu-item", function () {
        if ($(this).hasClass("sub-menu-item")) {
            $("#menu-list li").removeClass('current');
            $(".is-item").show();
            $(".is-sub-item").show();
        } else {
            $("#menu-list li").removeClass('current');
            $("#menu-list > li").not(this).find(".sub-menu-box").hide();
            $(".sub-menu-box", this).toggle();
            //如果当前还没有子菜单
            if ($(".sub-menu-item", this).size() == 0) {
                $(".is-item").show();
                $(".is-sub-item").show();
            } else {
                $(".is-item").hide();
                $(".is-sub-item").hide();
            }
        }
        $(this).addClass('current');
        var type = $(this).attr('data-type');
        var action = $(this).attr('data-action');
        var title = $(this).attr('data-name');

        actions = action.split('|');
        $("input[name=type][value='" + type + "']").prop("checked", true);
        $("input[name='item-title']").val(title);
        $('#current-item-name').text(title);
        if (actions[0] == 'url') {
            $('input[name=key]').val('');
        } else {
            $('input[name=url]').val('');
        }
        $("input[name='" + actions[0] + "']").val(actions[1]);
        if (actions[0] == 'key') {
            var keytitle = typeof responselist[actions[1]] != 'undefined' ? responselist[actions[1]] : '';
            var cont = $(".is-click .create-click:first");
            $(".keytitle", cont).remove();
            if (keytitle) {
                cont.append('<div class="keytitle">资源名:' + keytitle + '</div>');
            }
        } else {

        }

        $(".weixin-content").show();
        $(".no-weixin-content").hide();
        refresh_type();
        return false;
    });
    $("form").on('change', "input,textarea", function () {
        updateChangeMenu();
    });
    /*$(document).on('click', "#menuSyn", function () {
        $.post("data/edit.json", {}, function (ret) {
            var msg = ret.hasOwnProperty("msg") && ret.msg != "" ? ret.msg : "";
            if (ret.code == 1) {
                alert('菜单同步更新成功，生效时间看微信官网说明，或者你重新关注微信号！');
            } else {
                alert(msg ? msg : __('Operation failed'));
            }
        }, 'json');
    });
    $(document).on('click', "#select-resources", function () {
        var key = $("#key").val();
        Backend.api.open($(this).attr("href") + "?callback=refreshkey&key=" + key, __('Select'));
        return false;
    });

    $(document).on('click', "#add-resources", function () {
        Backend.api.open($(this).attr("href") + "?callback=refreshkey&key=" + key, __('Add'));
        return false;
    });*/
    window.refreshkey = function (data) {
        responselist[data.eventkey] = data.title;
        $("input[name=key]").val(data.eventkey).trigger("change");
        //layer.closeAll();
    };
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