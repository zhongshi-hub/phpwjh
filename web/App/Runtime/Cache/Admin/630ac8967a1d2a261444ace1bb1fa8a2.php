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
    .form-control{width: 300px !important;}
    .imgborder{width: 300px;border: solid 1px #E4E4E4;height: 220px;padding: 5px;}
    .imgborder img { width:100%; display:block;height: 100%;}
    .chosen-container{display: block}
</style>
<div id="content-container" style="padding-top: 0px!important;padding-bottom: 0px!important;">
    <div id="page-content">
        <div class="panel">
            <form method="post" action="<?php echo U('mdata_save');?>">
                <div class="panel-body">
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">(编辑)基本信息</p>
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
                                <input class="form-control" type="text"  value="<?php echo (agent_name($data["agent_id"])); ?>" readonly>
                                <input  type="hidden" name="agent_id" value="<?php echo ($data["agent_id"]); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">商户名称</label>
                                <input class="form-control" type="text"  name="mch_name" value="<?php echo ($data["mch_name"]); ?>"  >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" style="color: red">商户类型</label>
                                <select id="mch_bus_type" data-placeholder="请选择..."  tabindex="2"  class="form-control" name="mch_bus_type" required="required">
                                    <option value="快速" <?php if(($data["mch_bus_type"]) == "快速"): ?>selected<?php endif; ?> >快速</option>
                                    <option value="企业" <?php if(($data["mch_bus_type"]) == "企业"): ?>selected<?php endif; ?> >企业</option>
                                    <option value="小微" <?php if(($data["mch_bus_type"]) == "小微"): ?>selected<?php endif; ?> >小微</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">行业类别</label>
                                <select data-placeholder="请选择..." id="mch_industry" tabindex="2"    style="width: 300px;" name="mch_industry" required="required">
                                    <?php if(is_array($ind)): foreach($ind as $key=>$v): ?><option value="<?php echo ($v["pid"]); ?>"  <?php if($data["mch_industry"] == $v['pid']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属省份</label>
                                <select data-placeholder="请选择..."  tabindex="2"  id="provice"  style="width: 300px;" name="mch_provice" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["provice"]); ?>"  <?php if($data["mch_provice"] == $v['provice']): ?>selected<?php endif; ?>><?php echo ($v["provice"]); ?></option><?php endforeach; endif; ?>
                                </select>

                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属城市</label>
                                <select id="selectCity" class="form-control" data-live-search="true" data-width="100%" name="mch_citys" required="required">
                                    <option value="<?php echo ($data["mch_citys"]); ?>" selected><?php echo ($data["mch_citys"]); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">所属区县</label>
                                <select id="selectDis" class="form-control" data-live-search="true" data-width="100%" name="mch_district" required="required">
                                    <option value="<?php echo ($data["mch_district"]); ?>" selected><?php echo ($data["mch_district"]); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">地址</label>
                                <input class="form-control" type="text" name="mch_address" value="<?php echo ($data["mch_address"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">电话</label>
                                <input class="form-control" type="text" name="mch_tel" value="<?php echo ($data["mch_tel"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">负责人</label>
                                <input class="form-control" type="text" name="mch_card_name" value="<?php echo ($data["mch_card_name"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">负责人身份证</label>
                                <input class="form-control" type="text" name="mch_card_id" value="<?php echo ($data["mch_card_id"]); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">证件有效期(如企业为法人)</label>
                                <input class="form-control" type="text" name="card_time" value="<?php echo ($data["card_time"]); ?>" placeholder="格式: 年-月-日 永久:9999-12-31">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">新大陆MCC码</label>
                                <select data-placeholder="请选择..." id="pos_mcc" tabindex="2"    style="width: 300px;" name="pos_mcc">
                                    <option value="">请选择MCC</option>
                                    <?php if(is_array($mccData)): foreach($mccData as $key=>$v): ?><option value="<?php echo ($v["mcc"]); ?>"  <?php if($data["pos_mcc"] == $v['mcc']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="qy_data">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">企业名称</label>
                                <input class="form-control" type="text" name="qy_name" value="<?php echo ($data["qy_name"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">工商注册号</label>
                                <input class="form-control" type="text" name="qy_cid" value="<?php echo ($data["qy_cid"]); ?>" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">执照有效期</label>
                                <input class="form-control" type="text" name="qy_time" value="<?php echo ($data["qy_time"]); ?>" placeholder="格式: 年-月-日 永久:9999-12-31" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人姓名</label>
                                <input class="form-control" type="text" name="qy_fr_name" value="<?php echo ($data["qy_fr_name"]); ?>" >
                            </div>
                        </div>
                    </div>
                    <div class="row" id="qy_data1">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人身份证号</label>
                                <input class="form-control" type="text" name="qy_fr_cid" value="<?php echo ($data["qy_fr_cid"]); ?>" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">终端费率</label>
                                <!--<input class="form-control" type="text"  name="rate" value="<?php echo ($rate); ?>" placeholder="不能小于本身低价">-->
                                <select data-placeholder="请选择..." id="rate" tabindex="2"    style="width: 300px;" name="rate" required="required">
                                    <?php $_result=rate_data_pid($data['agent_id'],$_GET['type']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["rate"]); ?>"  <?php if($data["rate"] == $v['rate']): ?>selected<?php endif; ?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>

                            </div>
                        </div>
                    </div>

                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">结算信息</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户银行</label>
                                <select data-placeholder="请选择..." id="mch_bank_list" tabindex="2"    style="width: 300px;" name="mch_bank_list" required="required">
                                    <?php if(is_array($bank_list)): foreach($bank_list as $key=>$v): ?><option value="<?php echo ($v["bnkcd"]); ?>"  <?php if($data["mch_bank_list"] == $v['bnkcd']): ?>selected<?php endif; ?>><?php echo ($v["bnknm"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">银行卡号</label>
                                <input class="form-control" type="text" name="mch_bank_cid" value="<?php echo ($data["mch_bank_cid"]); ?>"  >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户名称</label>
                                <input class="form-control" type="text" name="mch_bank_name" value="<?php echo ($data["mch_bank_name"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">帐户类型</label>
                                <select id="mch_bank_type" data-placeholder="请选择..."  tabindex="2"  class="form-control" name="mch_bank_type" required="required">
                                    <option value="个人账户" <?php if($data["mch_bank_type"] == '个人账户'): ?>selected<?php endif; ?> >个人账户</option>
                                    <option value="企业账户" <?php if($data["mch_bank_type"] == '企业账户'): ?>selected<?php endif; ?> >企业账户</option>
                                </select>

                                <input class="form-control" id="Gr" style="display: none" type="text"  value="个人账户" disabled>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">结算类型</label>
                                <select id="mch_bank_type_s" data-placeholder="请选择..."  tabindex="2"  class="form-control" name="mch_bank_type_s" required="required">
                                    <option value="0" <?php if($data["mch_bank_type_s"] != 1): ?>selected<?php endif; ?> >法人结算</option>
                                    <option value="1" <?php if($data["mch_bank_type_s"] == 1): ?>selected<?php endif; ?> >非法人结算</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">开户省份</label>
                                <select data-placeholder="请选择..."  tabindex="2"  id="mch_bank_provice"  style="width: 300px;" name="mch_bank_provice" required="required">
                                    <?php if(is_array($pro)): foreach($pro as $key=>$v): ?><option value="<?php echo ($v["provice"]); ?>"  <?php if($data["mch_bank_provice"] == $v['provice']): ?>selected<?php endif; ?>><?php echo ($v["provice"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" id="depositCity">
                                <label class="control-label">开户城市</label>
                                <select id="selectBCity" class="form-control" data-live-search="true" data-width="100%" name="mch_bank_citys" required="required">
                                    <option value="<?php echo ($data["mch_bank_citys"]); ?>" selected><?php echo ($data["mch_bank_citys"]); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" id="depositLBnk">
                                <label class="control-label">开户支行</label>
                                <select  class="form-control" id="selectLBnk" required="required">
                                    <option value="<?php echo ($data["mch_linkbnk"]); ?>" selected="selected"><?php echo (reload_banks($data["mch_linkbnk"])); ?></option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">联行号</label>
                                <input class="form-control" id="lianhanghao" type="text" name="mch_linkbnk" value="<?php echo ($data["mch_linkbnk"]); ?>" readonly>
                            </div>
                        </div>„
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">预留手机号码</label>
                                <input class="form-control" type="text" name="mch_bank_tel" value="<?php echo ($data["mch_bank_tel"]); ?>">
                            </div>
                        </div>
                    </div>
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">证件信息</p>
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证正面</span></label>
                                <?php echo uploads_map('mch_img_z',$data['mch_img_z']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证反面</span></label>
                                <?php echo uploads_map('mch_img_p',$data['mch_img_p']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">结算银行卡</span></label>
                                <?php echo uploads_map('mch_img_bank',$data['mch_img_bank']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">手持照</span></label>
                                <?php echo uploads_map('mch_img_s',$data['mch_img_s']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">营业执照</span></label>
                                <?php echo uploads_map('mch_img_yyzz',$data['mch_img_yyzz']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">收款人授权函</span></label>
                                <?php echo uploads_map('mch_img_sqh',$data['mch_img_sqh']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证正面(授权人)</span></label>
                                <?php echo uploads_map('mch_img_auth_z',$data['mch_img_auth_z']);?>
                            </div>
                        </div>
                        <div class="col-sm-3 Yz">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证反面(授权人)</span></label>
                                <?php echo uploads_map('mch_img_auth_p',$data['mch_img_auth_p']);?>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">门头照片</span></label>
                                <?php echo uploads_map('mch_img_m1',$data['mch_img_m1']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">门店内景</span></label>
                                <?php echo uploads_map('mch_img_m2',$data['mch_img_m2']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">收银台照</span></label>
                                <?php echo uploads_map('mch_img_m3',$data['mch_img_m3']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">商户协议</span></label>
                                <?php echo uploads_map('mch_img_m4',$data['mch_img_m4']);?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">商户信息</span></label>
                                <?php echo uploads_map('mch_img_m5',$data['mch_img_m5']);?>
                            </div>
                        </div>
                        <!--<div class="col-sm-3 Sq">
                                <div class="form-group">
                                    <label class="control-label"><span style="color: red">授权函</span></label>
                                    <?php echo uploads_map('mch_img_sqh',$data['mch_img_sqh']);?>
                                </div>
                        </div>-->
                    </div>
                </div>
                <div class="panel-footer text-justify">
                    <?php if(($_GET['mch']) == "sin"): ?><a class="btn btn-warning" href="<?php echo U('mdata',array('type'=>$_GET['type'],'id'=>$_GET['id'],'mch'=>'sin'));?>">取消编辑并返回上一层</a>
                        <?php else: ?>
                        <a class="btn btn-warning" href="<?php echo U('mdata',array('type'=>$_GET['type'],'id'=>$_GET['id']));?>">取消编辑并返回上一层</a><?php endif; ?>
                    <input type="hidden" name="cid" value="<?php echo ($_GET['id']); ?>">
                    <input type="hidden" name="alleys_type" value="<?php echo ($_GET['type']); ?>">
                    <button type="submit" class="btn btn-danger" >确认更新信息并提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#pos_mcc,#mch_bus_type,#mch_industry,#provice,#mch_bank_list,#mch_bank_provice,#rate,#provice').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});

        $('#mch_bus_type').change(BusChange);
        $('#mch_bank_type').change(BTypeChange);


        $('#provice').change(provinceChange); //省份
        $('#selectCity').change(cityChange); //市级

        $('#mch_bank_provice').change(BproChange); //银行省份
        $('#selectBCity').change(BcityChange); //市级
        $('#mch_bank_list').change(bankChange);
        $('#selectLBnk').change(selectLBnkval);


        BusChange();
        BTypeChange();
    });

    function BTypeChange() {
        var type= $('#mch_bank_type option:selected').val();
        if(type=='个人账户'){
            $('.Sq').show();
        }else{
            $('.Sq').hide();
        }
    }

    function BusChange() {
        var Bus= $('#mch_bus_type option:selected').val();
        if(Bus=='快速'||Bus=='小微'){
            $('.Yz,#qy_data,#qy_data1,#yyzz,#sqh').hide();
            $('.Sq,#Gr').show();
            $('#mch_bank_type').hide();
        }else{
            $('#mch_bank_type,#qy_data,#qy_data1').show();
            $('.Yz').show();
            $('#Gr').hide();
        }
    }



    //当支行变了 联行号就变
    function selectLBnkval() {
        var lh= $('#selectLBnk option:selected').val();
        $('#lianhanghao').val(lh);
    }

    //当开户行放生改变时，带出默认的支行
    function bankChange(){
        var cityId = $('#selectBCity option:selected').val();
        if(cityId!=null && cityId!="" && typeof(cityId)!='undefined'){
            setLBnk(cityId);
        }
    }

    //
    function BproChange() {
        var provinceId = $('#mch_bank_provice option:selected').val();
        setBCity(provinceId);
    }

    //当省份改变时，带出默认城市
    function provinceChange(){
        var provinceId = $('#provice option:selected').val();
        setCity(provinceId);
    }
    //当城市改变的时候,改变区县
    function cityChange(){
        var cityId = $('#selectCity option:selected').val();
        setdistrict(cityId);
    }

    //当城市改变的时候,改变银行
    function BcityChange(){
        var cityId = $('#selectBCity option:selected').val();
        setLBnk(cityId);
    }

    //设置银行所属城市
    function setBCity(provinceId) {
        var cityStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"/Pays/Reg/A_area_city",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                //alert(data.length);
                for(var i=0; i<data.length; i++){
                    cityStr += "<option value='"+data[i]+"'>"+data[i]+"</option>";
                }
                $("#selectBCity").find("option").remove();
                $("#selectBCity").append(cityStr);
                $('#selectBCity').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectBCity").trigger("chosen:updated");
                $("#selectBCity").trigger("liszt:updated");
                var cityId = data[0];
                setLBnk(cityId);
            }
        });
    }

    //设置显示城市
    function setCity(provinceId){
        var cityStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"/Pays/Reg/A_area_city",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                //alert(data.length);
                for(var i=0; i<data.length; i++){
                    cityStr += "<option value='"+data[i]+"'>"+data[i]+"</option>";
                    //alert(cityStr);
                }
                $("#selectCity").find("option").remove();
                $("#selectCity").append(cityStr);
                $('#selectCity').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectCity").trigger("chosen:updated");
                $("#selectCity").trigger("liszt:updated");
                var cityId = data[0];
                setdistrict(cityId);
            }
        });
    }


    //设置显示区县
    function setdistrict(provinceId){
        var disStr = "<option value=''>--请选择--</option>";
        $.ajax({
            url:"/Pays/Reg/A_area_disc",
            data:'name='+provinceId,
            type:"POST",
            dataType:"json",
            async: false,
            cache: false,
            success:function(data){
                //alert(data.length);
                for(var i=0; i<data.length; i++){
                    disStr += "<option value='"+data[i]+"'>"+data[i]+"</option>";
                    //alert(cityStr);
                }
                $("#selectDis").find("option").remove();
                $("#selectDis").append(disStr);
                $('#selectDis').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                $("#selectDis").trigger("chosen:updated");
                $("#selectDis").trigger("liszt:updated");
            }
        });
    }


    //当开户市改变且开户行已选择时， 选择开户支行
    function setLBnk(cityId){
        var depositBnk = $('#mch_bank_list option:selected').val();
        var params = {depositBnk:depositBnk,cityId:cityId,'alley':'<?php echo ($_GET["type"]); ?>'};
        $('#lianhanghao').val("");
        var linkBnkStr = "<option value=''>请选择</option>";
        if(depositBnk&&cityId) {
            $.ajax({
                data: params,
                url: "/Pays/Reg/A_bnkLink",
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.list == null) {
                        linkBnkStr = "<option value=''>此区域无支行信息,请重新选择</option>";
                    } else {
                        for (var i = 0; i < data.list.length; i++) {
                            linkBnkStr += "<option value='" + data.list[i].banking + "'>" + data.list[i].address + "</option>";
                        }
                    }
                    $("#selectLBnk").find("option").remove();
                    $("#depositLBnk select").append(linkBnkStr);
                    $('#selectLBnk').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
                    $("#selectLBnk").trigger("chosen:updated");
                    $("#selectLBnk").trigger("liszt:updated");
                }
            });
        }
    }
</script>
</div>





<!-- 模态框开始 -->
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
<!-- 模态框结束 -->

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
        return true;
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