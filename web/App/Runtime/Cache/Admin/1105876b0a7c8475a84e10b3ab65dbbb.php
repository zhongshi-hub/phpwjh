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

<div id="content-container" style="padding-top: 0px!important;padding-bottom: 0px!important;">
    <div id="page-content">

        <div class="panel">
            <form method="post" action="<?php echo U('A_rata_save');?>">
                <div class="panel-body">
                    <div class="alert alert-purple">
                        <strong>温馨提醒:</strong>费率单位按照千比计算 如3%。 输入框内无需输入%。
                    </div>
                  <?php if(empty($pid)): if(is_array($api)): $i = 0; $__LIST__ = $api;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">通道:<?php echo ($v["alleys"]); ?><span class="pull-right badge badge-mint">通道最低成本费率:<?php echo ($v["rate"]); ?>%。</span></p>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">成本费率</label>
                                <input class="form-control" type="text" name="<?php echo ($v["alleys_type"]); ?>_cost" placeholder="费率千分比  如 3.8 " value="<?php $type=$v['alleys_type'].'_cost'; echo $rate[$type]; ?>" required>
                            </div>
                        </div>
                        <input type="hidden" name="<?php echo ($v["alleys_type"]); ?>_term" value="6">
                        <!--<div class="col-sm-6">-->
                            <!--<div class="form-group">-->
                                <!--<label class="control-label">终端费率</label>-->
                                <!--<select data-placeholder="请选择..." tabindex="2"   class="form-control"  name="<?php echo ($v["alleys_type"]); ?>_term" required="required">-->
                                    <!--<?php $_result=rate_data($v['alleys_type']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vs): $mod = ($i % 2 );++$i;?>-->
                                        <!--<option value="<?php echo ($vs["rate"]); ?>" <?php $type=$v['alleys_type'].'_term'; if($rate[$type]==$vs['rate']){ echo selected; } ; ?> ><?php echo ($vs["name"]); ?></option>-->
                                    <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                <!--</select>-->
                            <!--</div>-->
                        <!--</div>-->
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>

                    <?php else: ?>

                    <?php if(is_array($api)): $i = 0; $__LIST__ = $api;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">通道:<?php echo ($v["alleys"]); ?><span class="pull-right badge badge-mint">通道最低成本费率:<?php echo rate_data_pid($pid,$v['alleys_type'],1); ?>%。</span></p>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">成本费率</label>
                                    <input class="form-control" type="text" name="<?php echo ($v["alleys_type"]); ?>_cost" placeholder="费率千分比  如 3.8 " value="<?php $type=$v['alleys_type'].'_cost'; echo $rate[$type]; ?>" required>
                                </div>
                            </div>
                            <input type="hidden" name="<?php echo ($v["alleys_type"]); ?>_term" value="6">
                            <!--<div class="col-sm-6">-->
                                <!--<div class="form-group">-->
                                    <!--<label class="control-label">终端费率</label>-->
                                    <!--<select data-placeholder="请选择..." tabindex="2"   class="form-control"  name="<?php echo ($v["alleys_type"]); ?>_term" required="required">-->
                                        <!--<?php $_result=rate_data_pid($pid,$v['alleys_type']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vs): $mod = ($i % 2 );++$i;?>-->
                                            <!--<option value="<?php echo ($vs["rate"]); ?>" <?php $type=$v['alleys_type'].'_term'; if($rate[$type]==$vs['rate']){ echo selected; } ; ?> ><?php echo ($vs["name"]); ?></option>-->
                                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                    <!--</select>-->
                                <!--</div>-->
                            <!--</div>-->
                        </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>

                </div>
                <div class="panel-footer text-justify">
                    <input type="hidden" name="aid" value="<?php echo ($_GET['id']); ?>">
                    <button type="submit" class="btn btn-danger" >提交</button>
                </div>
            </form>
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
</style>
<script type="text/javascript">

    var index = parent.layer.getFrameIndex(window.name);
    $('select').chosen();
    $(function() {
        $('.OpenUrl').click(function(){
            var frameSrc = $(this).attr("href");
            $('#IfRaMeModal').on('show.bs.modal',function() {
                $('iframe').attr("src",frameSrc);
            });
            $('#IfRaMeModal').modal({show:true});
            return false;
        });

        $("form").submit(function (e) {
             var not=$(this).attr("ajax");
             if(not != 'n'){
             e.preventDefault(); //阻止自动提交表单
             }


            <?php if(is_array($api)): $i = 0; $__LIST__ = $api;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dv): $mod = ($i % 2 );++$i;?>//多通道自动生成判断  当前通道: <?php echo ($dv["alleys"]); ?>

             var <?php echo ($dv["alleys_type"]); ?>_cost= $('[name="<?php echo ($dv["alleys_type"]); ?>_cost"]').val();
             var <?php echo ($dv["alleys_type"]); ?>_term= $('[name="<?php echo ($dv["alleys_type"]); ?>_term"]').val();
             //终端费率不能小于成本费率
             if(<?php echo ($dv["alleys_type"]); ?>_term < <?php echo ($dv["alleys_type"]); ?>_cost){
               alert('通道:<?php echo ($dv["alleys"]); ?> 的终端费率不能小于成本费率');
               return false;
             }

             //成本费率不能小于自己本身的费率
            <?php if(empty($pid)): ?>if(<?php echo ($dv["alleys_type"]); ?>_cost < <?php echo ($dv["rate"]); ?>){
                alert('通道:<?php echo ($dv["alleys"]); ?> 的成本费率不能小于最低成本费率哦  最低费率:<?php echo ($dv["rate"]); ?> 您输入的费率:'+<?php echo ($dv["alleys_type"]); ?>_cost);
                return false;
             }
            <?php else: ?>

             if(<?php echo ($dv["alleys_type"]); ?>_cost < <?php echo rate_data_pid($pid,$dv['alleys_type'],1); ?> ){
                alert('通道:<?php echo ($dv["alleys"]); ?> 的成本费率不能小于最低成本费率哦  最低费率:<?php echo rate_data_pid($pid,$dv['alleys_type'],1); ?>  您输入的费率:'+<?php echo ($dv["alleys_type"]); ?>_cost);
                return false;
             }<?php endif; endforeach; endif; else: echo "" ;endif; ?>

            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function(data){
                if(data.status == 1){
                    parent.layer.close(index);
                    parent.layer.msg(data.info, {shade: 0.3});
                }
                else{
                    parent.layer.msg(data.info, {shade: 0.3});
                }
            }, 'json');

        });
    });

</script>
</body>
</html>