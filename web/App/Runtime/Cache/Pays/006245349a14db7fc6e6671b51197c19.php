<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <?php $memberTotal=memberOrderTotal($session_member['user']['id']);$temp=memberTemp($session_member['mid']);$activity=R('Common/MemberActivity/getListApi',[$session_member['mid'],$session_member['store_id']]); ?>
    <!--Time:2019年08月27日20:56:40  DevAuth:郑州讯龙软件科技有限公司 会员卡项目-->
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>我的会员卡</title>
    <link type="text/css" rel="stylesheet" href="/Source/hui/css/hui.css" />
    <style>
        body{background-color: #f7f7f7}
        .card_bg{height:200px;margin: 40px 30px;background: rgba(0, 0, 0, 0) url('/Source/amp/member/card_bg/<?php echo ((isset($temp['bg']) && ($temp['bg'] !== ""))?($temp['bg']):'0'); ?>.png') no-repeat scroll 0% 0% / 100% 100%;position: relative;}
        .card_body{padding:20px 23px;}
        .card_name{font-size: 20px;font-weight: 600;}
        .card_slogan{margin: 6px 0 0;font-size: 15px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}
        .card_ul{margin-top: 14px;font-size: 12px;line-height: 1.2;    width: 50%;}
        .card_ul .item{margin-bottom: 5px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;font-size: 13px}
        .card_info{position: absolute;right: 10px;bottom: 10px;}
        .card_total{margin: 0 0 4px;padding-right: 12px;text-align: right;font-size: 28px;}
        .card_no{margin: 0 5px;font-size: 12px;}
        .card_user_name{font-size: 14px;text-align: right;padding-right: 12px;}
        .card_ul .item:before {display: inline-block;content: '';width: 10px;height: 10px;margin-right: 5px;-webkit-border-radius: 40%;-moz-border-radius: 40%;border-radius: 40%;}
        .card_0 .card_ul .item:before {background-color: #96d6ff;}
        .card_1 .card_ul .item:before {background-color: #f8ffff;}
        .card_2 .card_ul .item:before {background-color: #cfd0c2;}
        .card_3 .card_ul .item:before {background-color: #f9bdb4;}
        .card_4 .card_ul .item:before {background-color: #aaa;}
        .card_5 .card_ul .item:before {background-color: #eafdc0;}
        .card_0 *{color: #96d6ff;}
        .card_1 *{color: #f8ffff}
        .card_2 *{color: #cfd0c2}
        .card_3 *{color: #f9bdb4}
        .card_4 *{color: #aaa}
        .card_5 *{color: #eafdc0}
        .help_title{height: 48px;line-height: 48px;font-size: 15px;}
        .help_title img{width: 20px;font-size: 25px;line-height: 20px;flex-shrink: 0;margin: -4px 0;padding-right: 10px;margin-left: 5px;}
        .help_info{padding: 5px 30px 10px 30px;white-space:pre-line}
    </style>
</head>
<body>
<div class="hui-wrap">
    <div class="card_bg card_<?php echo ((isset($temp['bg']) && ($temp['bg'] !== ""))?($temp['bg']):'0'); ?>">
        <div class="card_body">
            <div class="card_name"><?php echo ((isset($temp['name']) && ($temp['name'] !== ""))?($temp['name']):'高级会员卡'); ?></div>
            <div class="card_slogan"><?php echo ((isset($temp['xc']) && ($temp['xc'] !== ""))?($temp['xc']):'欢迎使用本店会员卡'); ?></div>
            <ul class="card_ul card_<?php echo ((isset($temp['bg']) && ($temp['bg'] !== ""))?($temp['bg']):'0'); ?>">
                <?php if(is_array($activity)): foreach($activity as $key=>$v): ?><li class="item"><?php echo ($v["list_desc"]); ?></li><?php endforeach; endif; ?>
            </ul>
            <div class="card_info">
                <p class="card_user_name"><?php echo ($session_member['user']['name']); ?></p>
                <p class="card_total">￥<?php echo ((isset($memberTotal) && ($memberTotal !== ""))?($memberTotal):'0.00'); ?></p>
                <p class="card_no">卡号：<?php echo ($session_member['user']['num']); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="hui-list" style="margin-top:22px;">
    <a href="tel:<?php echo ($temp['phone']); ?>">
        <div class="hui-list-icons">
            <img src="/Source/amp/member/m_mch.png" />
        </div>
        <div class="hui-list-text">
            联系商家
            <div class="hui-list-info">
                <span class="hui-icons hui-icons-right"></span>
            </div>
        </div>
    </a>
    <div class="hui-accordion">
        <div class="hui-accordion-title help_title">
            <img src="/Source/amp/member/m_sm.png"/>
            使用说明
        </div>
        <div class="hui-accordion-content hui-list help_info">
            <?php echo ($temp['help']); ?>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Source/hui/js/hui.js"></script>
<script type="text/javascript" src="/Source/hui/js/hui-accordion.js"></script>
<script>
    hui.accordion(true, true);
</script>
</body>
</html>