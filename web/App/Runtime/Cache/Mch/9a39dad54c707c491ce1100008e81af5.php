<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html style="height: auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>代理管理中心</title>
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
    <style>.tabbar-item.tabbar-active{color: #108ee9}</style>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back();" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">商户筛选</span></div>
    </header>
    <section class="g-scrollview">
        <aside class="demo-tip">
            根据需求选择性筛选
        </aside>
        <form action="<?php echo U('mch_data');?>" method="get" style="margin-bottom: 50px">
            <div class="m-celltitle">基本筛选</div>
            <div class="m-cell">
                <div class="cell-item">
                    <div class="cell-left">名称查找：</div>
                    <div class="cell-right">
                        <input class="cell-input" placeholder="商户名称、负责人姓名、电话支持泛查询" autocomplete="off" type="text"  name="search_val">
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-left">代理筛选：</div>
                    <label class="cell-right cell-arrow">
                        <select class="cell-select" name="agent_id">
                            <option value="">所有</option>
                            <?php if(is_array($aid)): foreach($aid as $key=>$v): ?><option value="<?php echo ($v); ?>"><?php echo (agent_name($v)); ?></option><?php endforeach; endif; ?>
                        </select>
                    </label>
                </div>
            </div>

            <div class="m-celltitle">日期范围筛选</div>
            <div class="m-cell">
                <div class="cell-item">
                    <div class="cell-left">开始日期：</div>
                    <div class="cell-right">
                        <input class="cell-input" value="" placeholder="" type="date" name="stime">
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-left">结束日期：</div>
                    <div class="cell-right">
                        <input class="cell-input" value="" placeholder="" type="date" name="etime">
                    </div>
                </div>
            </div>

            <div class="m-button">
                <button type="submit" class="btn-block btn-primary">提 交</button>
            </div>
        </form>
    </section>
    <footer class="m-tabbar tabbar-fixed">
        <a href="<?php echo U('Agent/index');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
            <span class="tabbar-txt">主页</span>
        </a>
        <a href="<?php echo U('Agent/mch_data');?>" class="tabbar-item  tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-discover"></i>
                </span>
            <span class="tabbar-txt">商户</span>
        </a>
        <a href="<?php echo U('Agent/qrcode');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-qrscan"></i>
                    <span class="tabbar-dot"></span>
                </span>
            <span class="tabbar-txt">收款码</span>
        </a>
        <a href="<?php echo U('Agent/my');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-ucenter-outline"></i>
                </span>
            <span class="tabbar-txt">我的</span>
        </a>
    </footer>

</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<script>
    function call_back() {
        location.href=document.referrer;
    }
</script>
</body>

</html>