<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商户中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/Source/mui/css/mch.css"/>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back();" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">提现</span></div>
    </header>
    <div class="g-scrollview" style="margin-bottom: 50px;">
        <?php if(($status) == "0"): ?><div class="mui-card">
                <div class="mui-card-content">
                    <div class="mui-card-content-inner">
                        <?php echo ($msg); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php if(is_array($data)): foreach($data as $key=>$v): ?><div class="mui-table-view" style="margin-top: 10px">
                    <div class="mui-card-header">
                        <?php if(!empty($v["merc_nm"])): echo ($v["merc_nm"]); ?>
                        <?php else: ?>
                            <span style="font-size: 13px">门店ID:<?php echo ($v["stoe_id"]); ?></span><?php endif; ?>
                        <a class="mui-card-link">
                            <?php if($v["flag"] == 1): ?><span data-status="open" data-mch="<?php echo ($v["merc_id"]); ?>" data-stoe="<?php echo ($v["stoe_id"]); ?>" class="drawStatus mui-badge mui-badge-success" style="border-radius: 5px;">启用提现权限</span>
                                <?php else: ?>
                                <span data-status="close" data-mch="<?php echo ($v["merc_id"]); ?>" data-stoe="<?php echo ($v["stoe_id"]); ?>"  class="drawStatus mui-badge mui-badge-danger" style="border-radius: 5px;">关闭提现权限</span><?php endif; ?>
                        </a>
                    </div>
                    <div class="mui-card-content">
                        <div class="mui-card-content-inner" style="padding-top: 5px;padding-bottom: 5px">
                            <?php if($v["flag"] == 0): ?><div class="mui-row" style="margin: 5px 0px">
                                <div class="mui-col-sm-9" style="padding: 5px">
                                        <p style="font-size: 0.5rem;font-weight: bold;color: #0587bd"><?php $b=$v['balance']/100; echo number_format($b,2); ?></p>
                                        <p style="font-size: 12px;padding-top: 5px">可提现金额(元)</p>
                                </div>
                                <div class="mui-col-sm-3" style="float: right;padding: 10px">
                                    <?php $rate=$v['service_fee']; $total=round((($v['balance']/100)*$rate)/100,2); if($total<0.5){ $fee='0.5' ; }else{ $fee=$total;} ?>
                                    <button type="button" data-bank="<?php echo ($v["opn_bnk_desc"]); ?>(<?php echo (card_replace($v["stl_oac"])); ?>)" data-mch="<?php echo ($v["merc_id"]); ?>" data-stoe="<?php echo ($v["stoe_id"]); ?>" data-total="<?php echo ($v['balance']/100); ?>" data-fee="<?php echo ($fee); ?>" class="draw mui-btn mui-btn-primary mui-btn-outlined" <?php if($v["balance"] < 1000): ?>disabled<?php endif; ?>>提现</button>
                                </div>
                            </div>
                                <div class="mui-row" style="padding-top: 5px;border-top: 1px #e0e0dd solid;">
                                    <div class="mui-col-sm-4">
                                        <a data-fee="<?php echo ($v["service_fee"]); ?>" class="help mui-btn mui-btn-outlined" href="javascript:void(0)"><i class="mui-icon mui-icon-help"></i> 提现说明</a>
                                    </div>
                                    <div class="mui-col-sm-3" style="margin-left: 10px">
                                        <a data-mch="<?php echo ($v["merc_id"]); ?>" data-stoe="<?php echo ($v["stoe_id"]); ?>" data-total="<?php $t=getXdlDrawSetting($_SESSION['mch']['id'],$v['merc_id']); echo $t; ?>" class="setting mui-btn mui-btn-outlined" href="javascript:void(0)"><i class="mui-icon mui-icon-gear"></i>提现设置</a>
                                    </div>
                                    <div class="mui-col-sm-4" style="float: right;text-align: right;">
                                        <a  data-mch="<?php echo ($v["merc_id"]); ?>" data-stoe="<?php echo ($v["stoe_id"]); ?>" class="drawOrder mui-btn mui-btn-outlined mui-btn-sm" href="<?php echo U('drawOrder',['mch_id'=>$v['merc_id'],'stoe_id'=>$v['stoe_id']]);?>"><i class="mui-icon mui-icon-list"></i> 提现记录</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center;padding: 15px">
                                    <h4>未开启提现功能</h4>
                                </div><?php endif; ?>
                        </div>
                    </div>
                </div><?php endforeach; endif; endif; ?>
    </div>
    <footer class="m-tabbar tabbar-fixed">
        <a href="<?php echo U('Index/index');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
            <span class="tabbar-txt">主页</span>
        </a>
        <a href="<?php echo U('Index/order');?>" class="tabbar-item">
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
<script src="/Source/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="/Source/fancybox/jquery.fancybox.css"/>
<script src="/Source/mui/js/mui.min.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>

<style>
    .toast-content{color: #fff!important;}
</style>
<script>
    FastClick.attach(document.body);
    var dialog = YDUI.dialog;
    $('.drawStatus').click(function () {
       var type=$(this).data('status'),stoe_id=$(this).data('stoe'),mch_id=$(this).data('mch');
       var status=(type=="open")?"启用":"关闭";
        dialog.confirm('提示', '您确定要'+status+'提现权限吗?', function () {
            YDUI.dialog.loading.open('数据处理中...');
            $.ajax({
                url: '<?php echo U("drawStatus");?>',
                dataType: 'json',
                data: {type: type,stoe_id:stoe_id,mch_id:mch_id},
                type: 'post',
                success: function (res) {
                    YDUI.dialog.loading.close();
                    if (res.status == 1) {
                        show(res.info);
                        setTimeout(function () {
                             window.location.reload(true);
                        },2100);
                    } else {
                        show(res.info);
                    }
                },
                error: function (res) {
                    YDUI.dialog.loading.close();
                    show('通信失败');
                }
            });
        });
    });

    $('.draw').click(function () {
        var d=$(this);
        dialog.confirm('请确认提现信息','<p>手续费:'+d.data("fee")+'元</p><p>提现金额:'+d.data("total")+'元</p><p>到账卡:'+d.data("bank")+'</p>', function () {
            YDUI.dialog.loading.open('数据处理中...');
            $.ajax({
                url: '<?php echo U("draw");?>',
                dataType: 'json',
                data: {total:d.data('total'),fee:d.data('fee'),stoe_id:d.data('stoe'),mch_id:d.data('mch')},
                type: 'post',
                success: function (res) {
                    YDUI.dialog.loading.close();
                    if (res.status == 1) {
                        show(res.info);
                        setTimeout(function () {
                            window.location.reload(true);
                        },2100);
                    } else {
                        show(res.info);
                    }
                },
                error: function (res) {
                    YDUI.dialog.loading.close();
                    show('通信失败');
                }
            });
        });
    });

    $('.help').click(function (e) {
        var fee=$(this).data('fee');
        var html='<div style="text-align: center;font-size: 16px;font-weight: bold">提现说明</div>' +
            '<p style="text-align: left">提现受理时间: 5:00-22:50</p>' +
            '<p style="text-align: left">提现金额: 单日10W,单笔5W,单笔最低10元</p>' +
            '<p style="text-align: left">提现手续费: 提现金额的'+fee+'%,(单笔手续费不足0.5元,按照0.5元结算),手续费将从余额提现后自动扣除</p>' +
            '<p style="text-align: left">到账时间: 您申请的提现预计在2小时内到账,具体到账时间以银行通知为准!</p>';
        dialog.alert(html);
    });

    $('.setting').click(function () {
        var d=$(this);
        mui.prompt('输入的金额大于10则自动开启单笔自动提现功能,小于10为关闭,需手动提现','请输入金额','提现配置',['取消','确认'],function (e) {
            if (e.index == 1) {
                if(/^[0-9]+$/.test(e.value)) {
                    YDUI.dialog.loading.open('数据处理中...');
                    $.ajax({
                        url: '<?php echo U("drawSetting");?>',
                        dataType: 'json',
                        data: {total: e.value, stoe_id: d.data('stoe'), mch_id: d.data('mch')},
                        type: 'post',
                        success: function (res) {
                            YDUI.dialog.loading.close();
                            if (res.status == 1) {
                                show(res.info);
                                setTimeout(function () {
                                    window.location.reload(true);
                                }, 2100);
                            } else {
                                show(res.info);
                            }
                        },
                        error: function (res) {
                            YDUI.dialog.loading.close();
                            show('通信失败');
                        }
                    });
                }else{
                    mui.alert('请输入正确的金额,格式需为纯数字');
                }
            }
            return true;
        },'div');
        document.querySelector('.mui-popup-input input').value=d.data('total');
    });

    function call_back() {
        location.href=document.referrer;
    }
    function show(data) {
        YDUI.dialog.toast(data, 'none', 2000);
    }
</script>
</body>
</html>