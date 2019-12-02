<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>门店中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/Source/mui/css/mch.css"/>
    <script src="/Source/ydui/js/ydui.flexible.js"></script>
    <script src="/Source/ydui/js/ydui.js"></script>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <style>
        .demo-rollnotice {
            -moz-box-align: center;
            align-items: center;
            background-color: #fff;
            display: flex;
            padding: 0 8px;
            margin-top: 5px;
        }
        .demo-rollnotice img {
            height: 16px;
            margin-right: 4px;
            width: 43px;
        }
        .m-rollnotice {
            background-color: #fff;
            overflow: hidden;
            width: 100%;
        }
        .rollnotice-box {
            height: inherit;
        }
        .rollnotice-box.align-left {
            -moz-box-pack: start;
            justify-content: flex-start;
        }
        .rollnotice-box.align-right {
            -moz-box-pack: end;
            justify-content: flex-end;
        }
        .rollnotice-box.align-center {
            -moz-box-pack: center;
            justify-content: center;
        }
        .rollnotice-item {
            -moz-box-align: center;
            -moz-box-pack: inherit;
            align-items: center;
            display: flex;
            height: inherit;
            justify-content: inherit;
        }
    </style>
</head>
<body>
<section class="g-flexview">
    <header>
        <div class="header-wrapper">
            <div class="header-inner">
                <div class="dashboard" style="left: 67%">
                    <div class="number">
                        <span><?php echo Rand_total($D_sum); ?></span>
                    </div>
                    <div class="tip">今日交易总额(元)</div>
                </div>
                <div class="waves" style="left: 17%">
                    <div class="w1"></div>
                    <div class="w2"></div>
                </div>
                <div class="dashboard" style="left: 35%;width: 100px;height: 100px;top: 74%">
                    <div class="number">
                        <span><?php echo ($D_count); ?></span>
                    </div>
                    <div class="tip">今日笔数</div>
                </div>
            </div>
        </div>
    </header>

    <div class="g-scrollview">
        <div class="m-grids-2">
            <section class="grids-item">
                <div class="grids-icon">
                    <?php echo Rand_total($G_sum); ?>
                </div>
                <div class="grids-txt">昨日总额</div>
            </section>
            <section class="grids-item">
                <div class="grids-icon">
                    <?php echo ($G_count); ?>
                </div>
                <div class="grids-txt">昨日笔数</div>
            </section>
        </div>
        <!-- <div class="demo-rollnotice">
             <img src="/Source/statics/system-new.jpg">
             <div class="m-rollnotice" style="height: 30px;">
                 <div class="rollnotice-box align-left">
                     <div class="rollnotice-item"><nobr><span style="color: rgb(255, 0, 0);margin-left:3px;margin-right: 10px; "><i class="icon iconfont">&#xe60a;</i></span>银行系统维护 建设银行用户悉知....
                     </nobr>
                     </div>
                 </div>
             </div>
         </div>-->

        <div class="m-celltitle" style="margin-top: 15px;">门店信息</div>
        <div class="m-cell demo-small-pitch">

            <div class="cell-item">
                <div class="cell-left">门店名称</div>
                <div class="cell-right"><?php echo ($store["name"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">店员姓名</div>
                <div class="cell-right"><?php echo ($user_name); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">门店收款码</div>
                <div class="cell-right" onclick="Open_Codes('<?php echo GetStoreCodePath($store['id']);?>')" ><i class="icon iconfont" style="font-size:25px;color: #229cff">&#xe6c1;</i></div>
            </div>

        </div>


        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Store/index');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
                <span class="tabbar-txt">主页</span>
            </a>
            <a href="<?php echo U('Store/order');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-order"></i>
                    <span class="tabbar-dot"></span>
                </span>
                <span class="tabbar-txt">流水</span>
            </a>
            <a href="<?php echo U('Store/my');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-ucenter-outline"></i>
                </span>
                <span class="tabbar-txt">我的</span>
            </a>
        </footer>
</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="/Source/fancybox/jquery.fancybox.css"/>
<script type="text/javascript">
    $(document).ready(function() {
        setInterval('autoScroll(".m-rollnotice")', 2000);
    });
    function autoScroll(obj) {
        $(obj).find(".rollnotice-box:first").animate({
            marginTop: "-30px"
        }, 500, function() {
            $(this).css({marginTop:"0px"}).find("div:first").appendTo(this);
        });
    }
    function Open_Codes(url) {
        if(url) {
            $.fancybox.open(url);
        }else{
            YDUI.dialog.toast('当前门店未绑定收款码', 'none');
        }
    }
</script>
</body>
</html>