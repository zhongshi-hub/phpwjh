<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
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
<div id="container" >
    <div class="boxed">

<style>
    .form-control {
        width: 300px !important;
    }

    .imgborder {
        width: 300px;
        border: solid 1px #E4E4E4;
        height: 220px;
        padding: 5px;
    }

    .imgborder img {
        width: 100%;
        display: block;
        height: 100%;
    }
</style>
<div id="content-container" style="padding-top: 0px!important;padding-bottom: 0px!important;">
    <div id="page-content">
        <div class="panel">
            <div class="panel-body">
                <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">基本信息</p>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">当前通道</label>
                            <input class="form-control" type="text" value="<?php echo ($alleys); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">所属代理</label>
                            <input class="form-control" type="text" value="<?php echo (agent_name($data["agent_id"])); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">商户名称</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">商户类型</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bus_type"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">行业类别</label>
                            <input class="form-control" type="text" value="<?php echo (Industrid($data["mch_industry"])); ?>" readonly>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">省份</label>
                            <input class="form-control" type="email" value="<?php echo ($data["mch_provice"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">城市</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_citys"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">区县</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_district"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">地址</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_address"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">电话</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_tel"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">负责人</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_card_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">负责人身份证</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_card_id"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if(!empty($data["qy_name"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">企业名称</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_name"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["qy_cid"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">工商注册号</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_cid"]); ?>" readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["qy_fr_name"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人姓名</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_fr_name"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["qy_fr_cid"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人身份证号</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_fr_cid"]); ?>" readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["qy_stime"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业执照颁发时间</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_stime"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["qy_time"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">营业执照有效期时间</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_time"]); ?>" readonly>
                            </div>
                        </div><?php endif; ?>

                    <?php if(!empty($data["card_time"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">证件有效期</label>
                                <input class="form-control" type="text" value="<?php echo ($data["card_time"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">新大陆MCC码</label>
                            <input class="form-control" type="text" value="<?php echo ($data["pos_mcc"]); ?>"
                                   readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">终端费率</label>
                            <input class="form-control" type="text" value="<?php echo ($rate); ?>‰" readonly>
                        </div>
                    </div>
                </div>

                <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">结算信息</p>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">开户银行</label>
                            <input class="form-control" type="text" value="<?php echo (reload_bank($data["mch_bank_list"])); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">银行卡号</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_cid"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">开户名称</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">帐户类型</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_type"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">结算类型</label>
                            <input class="form-control" type="text" value="<?php if(($data["mch_bank_type_s"]) == "1"): ?>非法人结算<?php else: ?>法人结算<?php endif; ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">开户省份</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_provice"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group" id="depositCity">
                            <label class="control-label">开户城市</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_citys"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group" id="depositLBnk">
                            <label class="control-label">开户支行</label>
                            <input class="form-control" type="text" value="<?php echo (reload_banks($data["mch_linkbnk"])); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">联行号</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_linkbnk"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">预留手机号码</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_tel"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">证件信息</p>
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label"><span
                                    style="color: red">身份证正面(Size:<?php echo (img_size($data["mch_img_z"])); ?>)</span></label>
                            <div class="imgborder">
                                <img class="image-responsive" id="S1" src="<?php echo ($data["mch_img_z"]); ?>" onclick="view_img('S1')">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label"><span
                                    style="color: red">身份证反面(Size:<?php echo (img_size($data["mch_img_p"])); ?>)</span></label>
                            <div class="imgborder">
                                <img class="image-responsive" id="S2" src="<?php echo ($data["mch_img_p"]); ?>" onclick="view_img('S2')">
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($data["mch_img_bank"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">结算银行卡(Size:<?php echo (img_size($data["mch_img_bank"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S6" src="<?php echo ($data["mch_img_bank"]); ?>" onclick="view_img('S6')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_s"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">手持照(Size:<?php echo (img_size($data["mch_img_s"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S3" src="<?php echo ($data["mch_img_s"]); ?>" onclick="view_img('S3')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_yyzz"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">营业执照(Size:<?php echo (img_size($data["mch_img_yyzz"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S4" src="<?php echo ($data["mch_img_yyzz"]); ?>"
                                         onclick="view_img('S4')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_sqh"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">收款人授权函(Size:<?php echo (img_size($data["mch_img_sqh"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S5" src="<?php echo ($data["mch_img_sqh"]); ?>"
                                         onclick="view_img('S5')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_auth_z"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证正面(授权人)(Size:<?php echo (img_size($data["mch_img_auth_z"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="mch_img_auth_z" src="<?php echo ($data["mch_img_auth_z"]); ?>"
                                         onclick="view_img('mch_img_auth_z')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_auth_p"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证反面(授权人)(Size:<?php echo (img_size($data["mch_img_auth_p"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="mch_img_auth_p" src="<?php echo ($data["mch_img_auth_p"]); ?>"
                                         onclick="view_img('mch_img_auth_p')">
                                </div>
                            </div>
                        </div><?php endif; ?>

                    <?php if(!empty($data["mch_img_m1"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">门头照片(Size:<?php echo (img_size($data["mch_img_m1"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S7" src="<?php echo ($data["mch_img_m1"]); ?>" onclick="view_img('S7')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m2"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">门店内景(Size:<?php echo (img_size($data["mch_img_m2"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S8" src="<?php echo ($data["mch_img_m2"]); ?>" onclick="view_img('S8')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m3"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">收银台照(Size:<?php echo (img_size($data["mch_img_m3"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S9" src="<?php echo ($data["mch_img_m3"]); ?>" onclick="view_img('S9')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m4"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">商户协议(Size:<?php echo (img_size($data["mch_img_m4"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S10" src="<?php echo ($data["mch_img_m4"]); ?>" onclick="view_img('S10')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m5"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">商户信息(Size:<?php echo (img_size($data["mch_img_m5"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S11" src="<?php echo ($data["mch_img_m5"]); ?>" onclick="view_img('S11')">
                                </div>
                            </div>
                        </div><?php endif; ?>

                </div>
            </div>
            <?php if(($_GET['mch']) == "sin"): ?><div class="panel-footer text-justify">
                    <a class="btn btn-warning"
                       href="<?php echo U('mdata_edit',array('type'=>$_GET['type'],'id'=>$_GET['id'],'mch'=>'sin'));?>">编辑资料</a>
                    <a class="btn btn-mint" onclick="mch_data()">手工配置(通道参数)</a>
                    <?php if(!in_array(($_GET['type']), explode(',',"Aliisv,WxPay"))): ?><a class="btn btn-danger" onclick="mch_sin(<?php echo ($data["cid"]); ?>,'<?php echo ($data["alleys_type"]); ?>')">确认信息无误并自动进件</a><?php endif; ?>

                </div><?php endif; ?>
            <?php if(($data["load_status"]) == "3"): ?><div class="panel-footer text-justify">
                    <a class="btn btn-warning"
                       href="<?php echo U('mdata_edit',array('type'=>$_GET['type'],'id'=>$_GET['id'],'mch'=>'updata'));?>">编辑资料</a>
                    <?php if(!in_array(($_GET['type']), explode(',',"Aliisv,WxPay"))): ?><a class="btn btn-danger" onclick="mch_updata(<?php echo ($data["cid"]); ?>,'<?php echo ($data["alleys_type"]); ?>')">更新商户信息进件</a><?php endif; ?>
                </div><?php endif; ?>
        </div>
    </div>
</div>
<!-- 模态框开始 -->
<div class="modal fade" id="alleys-data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 370px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title">
                    通道参数配置
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('alley_mch_data');?>" method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">商户编号</label>
                                    <input class="form-control" type="text" name="mch_id" placeholder="商户编号"
                                           required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">商户密钥</label>
                                    <input class="form-control" type="text" name="mch_key" placeholder="如有请填写" >
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">附加参数</label>
                                    <input class="form-control" type="text" name="mch_appid" placeholder="如通道独立Appid及附加参数">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->

<script>
    function mch_data() {
        $('#alleys-data').modal('show');
    }

    //更新商户接口
    function mch_updata(id,type) {
        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
        layer.alert('此接口用于商户被拒绝驳回后进行商户信息更新重新提交银行审核,请根据驳回后的提示进行资料修改,确认修改信息后再进行资料更新提交进件记住,进件前必须确认信息真实性!真实性!真实性!重要的事情说三遍!  ( ^_^ )', {
            title:'商户更新进件提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            layer.msg('商户更新进件中...请等待结果...', {
                icon: 16,
                shade: 0.01,
                time:300000
            });
            //index_msg = layer.msg();
            var ajax_data ={'cid':id,'alleys':type};
            var actionurl ='<?php echo U("alleys_updata",array("Debug"=>"1"));?>';
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                    parent.layer.close(index);
                    parent.layer.msg(data.info, {shade: 0.3,time: 3000});
                    parent.window.location.reload();

                }
                else {
                    //layer.close(index_msg);
                    layer.msg(data.info, {time:5000,shade: 0.03});
                }
            }, 'json');

        });
    }

    function mch_sin(id,type) {
        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
        layer.alert('进件前请确认商户信息的真实性、准确性和商户费率信息,商户一旦进件成功,修改费率和信息会很麻烦哦,记住,进件前必须确认信息真实性!真实性!真实性!重要的事情说三遍!  ( ^_^ )', {
            title:'进件提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            layer.msg('进件中...请等待结果...', {
                icon: 16,
                shade: 0.01,
                time:300000
            });
            //index_msg = layer.msg();
            var ajax_data ={'cid':id,'alleys':type};
            var actionurl ='<?php echo U("alleys_in",array("Debug"=>"1"));?>';
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                    parent.layer.close(index);
                    parent.layer.msg(data.info, {shade: 0.3,time: 3000});
                    parent.window.location.reload();

                }
                else {
                    //layer.close(index_msg);
                    layer.msg(data.info, {time:5000,shade: 0.03});
                }
            }, 'json');

        });
    }
    $(function () {
        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
        $("form").submit(function (e) {
            e.preventDefault(); //阻止自动提交表单
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                    /*如果存在模态框将关闭*/
                    $('.modal').map(function () {
                        $(this).modal('hide');
                    });

                    parent.layer.close(index);
                    parent.layer.msg(data.info, {shade: 0.3});
                    parent.window.location.reload();
                }
                else {
                    $.niftyNoty({
                        type: 'danger',
                        message: '<strong>' + data.info + '</strong>',
                        container: 'floating',
                        timer: 5000
                    });
                }
            }, 'json');
        });
    });
    //img标签 图片预览
    function view_img(data) {
        $('img').fancybox({
            href: $('#'+data).attr('src')
        });
    }
</script>