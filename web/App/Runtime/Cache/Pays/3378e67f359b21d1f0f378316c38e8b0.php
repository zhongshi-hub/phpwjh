<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>会员卡消费</title>
    <link rel="stylesheet" type="text/css" href="/Source/hui/css/hui.css" />
    <style>
        body{background-color: #f7f7f7}
        .card_list img{width: 90%;margin: 5px;}
        .hui-media-list .hui-media-list-img {width: 15%;}
        .hui-media-content {margin-left:10px;width: 50%;padding-top: 5px}
        .hui-media-content p{margin-top: 5px}
        .card_total{width: 30%;text-align: right;font-size: 18px;padding-top: 10px;padding-right: 5px}
        .hui-media-list li{border-radius: 5px}
    </style>
</head>
<body>
<div class="hui-wrap">
    <div class="hui-media-list card_list" style="padding:10px;">
        <ul>
            <li>
                <a href="javascript:hui.toast('会员卡消费详情');">
                    <div class="hui-media-list-img"><img src="/Source/amp/member/m_xf_logo.png"/></div>
                    <div class="hui-media-content">
                        <h1>小孩子玩具店</h1>
                        <p><?php echo date('Y-m-d ');?></p>
                    </div>
                    <div class="card_total">
                        -10.55元
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:hui.toast('会员卡消费详情');">
                    <div class="hui-media-list-img"><img src="/Source/amp/member/m_xf_logo.png"/></div>
                    <div class="hui-media-content">
                        <h1>小孩子玩具店</h1>
                        <p><?php echo date('Y-m-d ');?></p>
                    </div>
                    <div class="card_total">
                        -2.15元
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:hui.toast('会员卡消费详情');">
                    <div class="hui-media-list-img"><img src="/Source/amp/member/m_xf_logo.png"/></div>
                    <div class="hui-media-content">
                        <h1>小孩子玩具店</h1>
                        <p><?php echo date('Y-m-d');?></p>
                    </div>
                    <div class="card_total">
                        -3.00元
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:hui.toast('会员卡消费详情');">
                    <div class="hui-media-list-img"><img src="/Source/amp/member/m_xf_logo.png"/></div>
                    <div class="hui-media-content">
                        <h1>小孩子玩具店</h1>
                        <p><?php echo date('Y-m-d ');?></p>
                    </div>
                    <div class="card_total">
                        -5.88元
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>
<script src="/Source/hui/js/hui.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>