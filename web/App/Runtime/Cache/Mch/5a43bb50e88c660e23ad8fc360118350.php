<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html style="height: auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>流水筛选</title>
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back();" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">流水筛选</span></div>
    </header>
    <section class="g-scrollview">
        <aside class="demo-tip">
            根据需求筛选,订单号支持泛查询!默认后七位即可搜索!
        </aside>
        <form action="<?php echo U('order');?>" method="get" style="margin-bottom: 50px">
            <div class="m-celltitle">基本筛选</div>
            <div class="m-cell">
                <div class="cell-item">
                    <div class="cell-left">订单号码：</div>
                    <div class="cell-right">
                        <input class="cell-input" placeholder="请输入订单号" autocomplete="off" type="number" pattern="[0-9]*" name="out_trade_no">
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-left">门店筛选：</div>
                    <label class="cell-right cell-arrow">
                        <select class="cell-select" name="store_id">
                            <option value="">所有门店</option>
                            <?php if(is_array($store)): foreach($store as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </label>
                </div>
                <div class="cell-item">
                    <div class="cell-left">支付类型：</div>
                    <label class="cell-right cell-arrow">
                        <select class="cell-select" name="pay_type">
                            <option value="">所有类型</option>
                            <option value="wx">微信支付</option>
                            <option value="ali">支付宝支付</option>

                        </select>
                    </label>
                </div>
            </div>

            <div class="m-celltitle">日期范围筛选</div>
            <div class="m-cell">
                <div class="cell-item">
                    <div class="cell-left">开始日期：</div>
                    <div class="cell-right">
                        <input class="cell-input" value="<?php echo ($min); ?>" placeholder="" type="date" name="stime">
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-left">结束日期：</div>
                    <div class="cell-right">
                        <input class="cell-input" value="<?php echo ($max); ?>" placeholder="" type="date" name="etime">
                    </div>
                </div>
            </div>

            <div class="m-button">
                <button type="submit" class="btn-block btn-primary">提 交</button>
            </div>
        </form>
    </section>
    <footer class="m-tabbar tabbar-fixed">
        <a href="<?php echo U('Index/index');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
            <span class="tabbar-txt">主页</span>
        </a>
        <a href="<?php echo U('Index/order');?>" class="tabbar-item  tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-order"></i>
                    <span class="tabbar-dot"></span>
                </span>
            <span class="tabbar-txt">流水</span>
        </a>
        <a href="<?php echo U('Index/my');?>" class="tabbar-item">
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