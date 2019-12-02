<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <?php $activity=R('Common/MemberActivity/getListApi',[$session_member['mid'],$session_member['store_id']]); ?>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>会员活动</title>
    <link rel="stylesheet" type="text/css" href="/Source/hui/css/hui.css" />
    <style>
        body{background-color: #f7f7f7}
        .hui-media-list .hui-media-list-img {width: 18%;padding: 10px;}
        .hui-media-list ul li{margin-top:20px;border-radius: 5px}
    </style>
</head>
<body>
<div class="hui-wrap">
    <div class="hui-media-list" style="padding:10px;">
        <ul>
            <?php if(is_array($activity)): foreach($activity as $key=>$v): ?><li>
                <div class="hui-media-list-img"><img src="/Source/amp/member/<?php echo ($v["type"]); ?>.png"></div>
                <div class="hui-media-content">
                    <h1><?php echo ($v["desc"]); ?></h1>
                    <p>
                        <?php switch($v["type"]): case "cz": ?>会员特权即充即送<?php break;?>
                            <?php case "xf": ?>自动返至会员卡<?php break;?>
                            <?php case "jh": ?>激活会员卡鼓励金秒到账<?php break;?>
                            <?php case "tj": ?>推荐新会员一起获奖励<?php break;?>
                            <?php default: endswitch;?>
                        </p>
                    <p><?php echo ($v["list_desc"]); ?></p>
                </div>
            </li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>
<script src="/Source/hui/js/hui.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>