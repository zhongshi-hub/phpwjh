<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>录入商户</title>
    <!-- Path to Framework7 Library CSS-->
    <link rel="stylesheet" href="/Source/Framework7/css/framework7.ios.min.css">
    <link rel="stylesheet" href="/Source/Framework7/css/framework7.ios.colors.min.css">
    <link rel="stylesheet" href="/Source/Framework7/css/framework7-icons.css">
    <!-- Path to your custom app styles-->
    <link rel="stylesheet" href="/Source/Framework7/css/my-app.css">
    <style>.upload_border{color: #fff!important;background-color: rgb(169, 169, 169)!important;}</style>
</head>
<body>
<div class="views">
    <div class="view view-main">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="left"></div>
                <div class="center sliding">录入商户</div>
                <div class="right">
                    <a href="#" class="link icon-only xun-menu "> <i class="icon icon-bars"></i></a>
                </div>
            </div>
        </div>
        <div class="pages navbar-through toolbar-through">
            <!-- Page, data-page contains page name-->
            <div data-page="form" class="page">
                <!-- Scrollable page content-->
                <div class="page-content">
                    <form id="form-<?php echo $_GET['code'];?>-per" class="store-data ajax-submit" method="post" action="<?php echo U('per_save');?>">
                        <input type="hidden" name="mch_type"  value="per"/>
                        <input type="hidden" name="codes"  value="<?php echo $_GET['code'];?>" />
                        <div class="content-block-title">商家验证</div>
                        <div class="list-block">
                            <ul>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">收款码ID</div>
                                            <div class="item-input">
                                                <input type="tel"   value="<?php echo $_GET['code'];?>" disabled/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">联系电话</div>
                                            <div class="item-input">
                                                <input type="tel" name="telNo" value="" placeholder="请输入手机号" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">验证码</div>
                                            <div class="item-input">
                                                <input type="tel" name="verify" placeholder="请输入验证码"
                                                       style="width: 50%;float:left;" required/>
                                                <button type="button" id="validBtn" class="button"
                                                        style="width: 45%;float:right;margin-top: 7px;"
                                                        onclick="toGetValiNum();">获取验证码
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="content-block-title">企业信息</div>
                        <div class="list-block">
                            <ul>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">企业名称</div>
                                            <div class="item-input">
                                                <input type="text" name="qy_name" value="" placeholder="企业全称 与营业执照上一致" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">工商注册号</div>
                                            <div class="item-input">
                                                <input type="text" name="qy_cid" value="" placeholder="营业执照号(三证合一信用代码)" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">执照有效期</div>
                                            <div class="item-input">
                                                <input type="text" name="qy_time"  style="width: 70%;float:left;" id="qy_time" value="" placeholder="请输入营业执照有效期" required/>
                                                <button type="button" onclick="$('#qy_time').val('9999-12-31');" class="button" style="width: 28%;float:right;margin-top: 7px;">永久
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">法人姓名</div>
                                            <div class="item-input">
                                                <input type="text" name="qy_fr_name" value="" placeholder="营业执照法人" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">法人身份证号</div>
                                            <div class="item-input">
                                                <input type="text" name="qy_fr_cid" value="" placeholder="营业执照法人身份证号" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">法人证件有效期</div>
                                            <div class="item-input">
                                                <input type="text" name="card_time"  style="width: 70%;float:left;" id="card_time" value="" placeholder="请输入身份证有效期" required/>
                                                <button type="button" onclick="$('#card_time').val('9999-12-31');" class="button" style="width: 28%;float:right;margin-top: 7px;">永久
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <!--</div>-->
                            <!--<div class="content-block-title">商户信息</div>-->
                            <!--<div class="list-block ">-->
                            <ul>
                                <!--<li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">商户名称</div>
                                            <div class="item-input">
                                                <input type="text" name="MchName" placeholder="请输入商户名称" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>-->
                                <!--<li>-->
                                <!--<a href="#" data-searchbar="true" data-back-text="返回" data-searchbar-cancel="取消"-->
                                <!--data-searchbar-placeholder="请输入关键字" data-back-on-select="true"-->
                                <!--data-open-in="popup"-->
                                <!--data-virtual-list="true"-->
                                <!--class="item-link smart-select">-->
                                <!--<select name="industry">-->
                                <!--<option value="">请选择行业</option>-->
                                <!--<?php if(is_array($per_industry)): $i = 0; $__LIST__ = $per_industry;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                                <!--<option value="<?php echo ($vo['pid']); ?>"><?php echo ($vo['name']); ?></option>-->
                                <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                <!--</select>-->
                                <!--<div class="item-content">-->
                                <!--<div class="item-inner">-->
                                <!--<div class="item-title">所属行业</div>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</a>-->
                                <!--</li>-->

                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">所属城市</div>
                                            <div class="item-input">
                                                <input type="text" name="citys" placeholder="请选择所属城市" readonly style="color: #007aff" id="picker-city"/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">详细地址</div>
                                            <div class="item-input">
                                                <input type="text" name="address" placeholder="请输入详细地址" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>



                        <div class="content-block-title">结算信息(收款人)</div>
                        <div class="list-block ">
                            <ul>
                                <li>
                                    <a href="#" data-back-text="返回"  data-back-on-select="true"
                                       data-open-in="popup"
                                       class="item-link smart-select">
                                        <select name="mch_bank_type" id="bank_type">
                                            <option value="企业账户" selected>企业账户</option>
                                            <option value="个人账户">个人账户</option>
                                        </select>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title">账户类型</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li id="bank_type_s" style="display: none">
                                    <a href="#" data-back-text="返回"  data-back-on-select="true"
                                       data-open-in="popup"
                                       class="item-link smart-select">
                                        <select name="mch_bank_type_s" id="bank_type_ren">
                                            <option value="0" selected>法人账户</option>
                                            <!--<option value="1">非法人账户</option>-->
                                        </select>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title">结算类型</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" data-searchbar="true" data-back-text="返回" data-searchbar-cancel="取消"
                                       data-searchbar-placeholder="请输入关键字" data-back-on-select="true"
                                       data-open-in="popup"
                                       class="item-link smart-select">
                                        <select name="bank_list" id="selectBnk">
                                            <option value="">请选择开户银行</option>
                                            <?php if(is_array($bank_list)): $i = 0; $__LIST__ = $bank_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["bnkcd"]); ?>"><?php echo ($v["bnknm"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title">开户银行</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">银行卡号</div>
                                            <div class="item-input">
                                                <input type="number" name="bank_cid" placeholder="请输入结算卡号(收款人)" required/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li id="s_bank_name">
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label" id="per_bank_name">开户姓名</div>
                                            <div class="item-input">
                                                <input type="text" name="bank_name" value="" placeholder="请输入开户姓名(收款人)"/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li id="s_card_val">
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">身份证号</div>
                                            <div class="item-input">
                                                <input type="text" name="card_val" value="" placeholder="请输入身份证号(收款人)"/>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li id="per_tel">
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">手机号码</div>
                                            <div class="item-input">
                                                <input type="tel" name="bank_tel" value="" placeholder="请输入银行卡预留手机号"/>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">开户地区</div>
                                            <div class="item-input">
                                                <input type="text" name="bank_city" placeholder="请选择开户城市"
                                                       readonly="readonly"
                                                       style="color: #007aff" id="picker-bank-city"/>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="#" id="depositLBnk" data-back-on-select="true" data-back-text="返回"
                                       data-open-in="popup" data-navbar-theme="red" class="item-link smart-select"
                                       data-searchbar-placeholder="搜索"
                                       data-searchbar-cancel="取消" data-searchbar="true">
                                        <select name="linkBnk" id="selectLBnk">
                                        </select>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title" id="lBnk">开户支行</div>
                                            </div>
                                        </div>
                                    </a>

                                </li>
                            </ul>
                        </div>

                        <!--<div class="content-block-title" style="margin-bottom: 0px;margin-top: 0px;">注:企业对私须填写授权书 <span style="color: #044aff" data-popover=".popover-xun-sqh" class="open-popover" > (授权书使用说明及下载)</span></div>-->
                        <div class="content-block-title">身份证照(法人)</div>
                        <div class="content-block">
                            <div class="row">
                                <div class="col-50 upload_border" id="img-z" onclick="IMG_UPLOAD('z')">
                                    <input type="hidden" name="img-z" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p>法人身份证(正面)</p>
                                </div>
                                <div class="col-50 upload_border" id="img-p" onclick="IMG_UPLOAD('p')">
                                    <input type="hidden" name="img-p" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p>法人身份证(反面)</p>
                                </div>
                            </div>
                        </div>

                        <!--<div class="content-block" id="auth_img">-->
                            <!--<div class="row">-->
                                <!--<div class="col-50 upload_border" id="img-auth-z" onclick="IMG_UPLOAD('auth-z')">-->
                                    <!--<input type="hidden" name="img-auth-z" value="">-->
                                    <!--<i class="f7-icons" >camera</i>-->
                                    <!--<p>收款人身份证(正面)</p>-->
                                <!--</div>-->
                                <!--<div class="col-50 upload_border" id="img-auth-p" onclick="IMG_UPLOAD('auth-p')">-->
                                    <!--<input type="hidden" name="img-auth-p" value="">-->
                                    <!--<i class="f7-icons" >camera</i>-->
                                    <!--<p>收款人身份证(反面)</p>-->
                                <!--</div>-->
                            <!--</div>-->
                        <!--</div>-->

                        <div class="content-block-title">基本验证照片</div>
                        <div class="content-block">
                            <div class="row">
                                <div class="col-50 upload_border" id="img-yyzz" onclick="IMG_UPLOAD('yyzz')">
                                    <input type="hidden" name="img-yyzz" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p >三证合一营业执照</p>
                                </div>
                                <div class="col-50 upload_border" id="img-bank" onclick="IMG_UPLOAD('bank')">
                                    <input type="hidden" name="img-bank" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p id="bank_text">收款人银行卡(正面)</p>
                                </div>

                            </div>
                        </div>
                        <!--<div class="content-block" id="sqh">-->
                            <!--<div class="row">-->
                                <!--<div class="col-50 upload_border" id="img-sqh" onclick="IMG_UPLOAD('sqh')">-->
                                    <!--<input type="hidden" name="img-sqh" value="">-->
                                    <!--<i class="f7-icons" >camera</i>-->
                                    <!--<p>结算授权函照</p>-->
                                <!--</div>-->
                            <!--</div>-->
                        <!--</div>-->

                        <div class="content-block-title">门店(公司)验证照片</div>
                        <div class="content-block">
                            <div class="row">
                                <div class="col-50 upload_border" id="img-m1" onclick="IMG_UPLOAD('m1')">
                                    <input type="hidden" name="img-m1" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p>门头照片</p>
                                </div>
                                <div class="col-50 upload_border" id="img-m2" onclick="IMG_UPLOAD('m2')">
                                    <input type="hidden" name="img-m2" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p>门店内景</p>
                                </div>
                                <div class="col-50 upload_border" id="img-m3" onclick="IMG_UPLOAD('m3')">
                                    <input type="hidden" name="img-m3" value="">
                                    <i class="f7-icons" >camera</i>
                                    <p>收银台照</p>
                                </div>
                            </div>
                        </div>
                        <div class="content-block-title" style="margin-top: 10px;display: none">商户协议照</div>
                        <div class="content-block" style="display: none">
                            <div class="row">
                                <div class="col-50 upload_border" id="img-m5" onclick="IMG_UPLOAD('m5')">
                                    <input type="hidden" name="img-m5" value="">
                                    <i class="f7-icons">camera</i>
                                    <p>商户信息表</p>
                                </div>
                                <div class="col-50 upload_border" id="img-m4" onclick="IMG_UPLOAD('m4')">
                                    <input type="hidden" name="img-m4" value="">
                                    <i class="f7-icons">camera</i>
                                    <p>商户协议照</p>
                                </div>
                            </div>
                        </div>

                        <?php if(($sys_xy["fw_status"]) == "1"): ?><div class="content-block-title" style="margin-top: -20px;">
                                <label class="label-checkbox item-content" style="float: left;padding-left:0px;width: 25px;margin-top: 8px" id="xy_box">
                                    <input name="xy-box" id="box" value="true" type="checkbox">
                                    <div class="item-media" style="width: 25px;"><i class="icon icon-form-checkbox"></i></div>
                                </label>
                                <div class="item-title" style="margin-left: 35px;line-height: 40px">我已阅读并同意<a data-popup=".xy-popup" class="open-popup">《<?php echo ($sys_xy["fw_name"]); ?>》</a></div>
                            </div>
                            <div class="content-block">
                                <button id="xun_sub_button" type="submit" class="button button-big button-fill color-green"  disabled style="width: 100%">提交信息
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="content-block">
                                <button  type="submit" class="button button-big button-fill color-green"  style="width: 100%">提交信息
                                </button>
                            </div><?php endif; ?>
                        <div class="content-block xun_footer">
                            Copyright ©  2015-<?php echo date('Y');?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--协议信息-->
        <div class="popup xy-popup">
            <div class="view navbar-fixed">
                <div class="pages">
                    <div class="page">
                        <div class="navbar">
                            <div class="navbar-inner">
                                <div class="center"><?php echo ($sys_xy["fw_name"]); ?></div>
                                <div class="right"><a href="#" class="link close-popup">关闭</a></div>
                            </div>
                        </div>
                        <div class="page-content" style="padding-left: 10px;padding-right: 10px">
                            <?php echo ($sys["fw_info"]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 授权函说明下载 popover -->
        <div class="popover popover-xun-sqh">
            <div class="popover-angle"></div>
            <div class="popover-inner">
                <div class="content-block">
                    <p>请使用PC电脑打开下面地址下载! 授权书下载后请使用A4纸打印后填写并签字盖章。填写完毕后使用当前手机在当前页面授权书位置拍照上传! 如不清楚如何填写,请联系平台或业务员协助填写!</p>
                    <p>授权书下载地址:  http://t.cn/RQhWxQt</p>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="/Source/Framework7/js/framework7.min.js"></script>
        <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
        <script>
            wx.config(<?php echo (json_encode($options)); ?>);
            var code_id= "<?php echo $_GET['code'];?>";
            $(function () {
                UpImage_Val();
                bankChange();
                bank_type();
                bank_type_ren();
            <?php if(($sys_xy["fw_status"]) == "1"): ?>xy_status();
                $('[name="xy-box"]').change(xy_status);<?php endif; ?>
            });

            //Xun Chen new_mch_reg
            var myApp = new Framework7(
                {
                    modalTitle: '提示',
                    modalButtonOk: '确定',
                    modalButtonCancel: '取消',
                    modalPreloaderTitle: '加载中...请稍等...',
                    animateNavBackIcon: true,
                    materialRipple: true
                }
            );

            var $$ = Dom7;

            $$(document).on('ajaxStart', function (e) {
                myApp.showIndicator();
            });
            $$(document).on('ajaxComplete', function (e) {
                myApp.hideIndicator();
            });

            $$('form.ajax-submit').on('submitted', function (e) {
                var data = JSON.parse(e.detail.data);
                if (data.status == 1) { //成功
                    myApp.alert(data.info, function () {
                        location.href = data.url;
                    });
                } else { //失败
                    myApp.alert(data.info);
                }
            });

            $$('#selectBnk').change(bankChange); //监听银行信息变动
            $$('#picker-bank-city').change(bankChange); //监听省份信息变动
            /**
             * 2019年04月18日23:08:16  新增
             *
             **/
            $$('#bank_type').change(bank_type);
            $$('#bank_type_ren').change(bank_type_ren);
            function bank_type() {
                var type = $('#bank_type option:selected').val();
                if(type=='企业账户'){
                    $('#bank_type_s,#s_bank_name,#s_card_val,#per_tel').hide();
                    $('#bank_text').text('开户许可证');
                }else{
                    $('#bank_type_s,#per_tel').show();
                    bank_type_ren();
                    $('#bank_text').text('收款人银行卡');
                }
            }
            function bank_type_ren() {
                var bank_type = $('#bank_type option:selected').val();
                var type = $('#bank_type_ren option:selected').val();
                console.log(type);
                if(bank_type!='企业账户') {
                    //非法人结算
                    if (type == 1) {
                        $('#auth_img,#sqh,#s_bank_name,#s_card_val').show();
                    } else {
                        //法人结算
                        $('#auth_img,#sqh,#s_bank_name,#s_card_val').hide();
                    }
                }else{
                    $('#auth_img,#sqh').hide();
                }
            }





            myApp.calendar({
                input: '#qy_time',
                monthNames:['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月' , '九月' , '十月', '十一月', '十二月'],
                dayNamesShort:['日','一','二','三','四','五','六'],
                closeOnSelect:true
            });
            myApp.calendar({
                input: '#card_time',
                monthNames:['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月' , '九月' , '十月', '十一月', '十二月'],
                dayNamesShort:['日','一','二','三','四','五','六'],
                closeOnSelect:true
            });

            function ToDates() {
                $$('[name="card_etime"]').val('2999-12-31');
            }
            /*初始化已上传图片*/
            function UpImage_Val() {
                var array = ['z', 'p', 's', 'm1', 'm2','m3','m4','m5','bank','yyzz','auth-z','auth-p','sqh'];
                var storedData = myApp.formGetData("form-"+code_id+"-img-per");
                var url='http://file.xunmafu.com/Upload/attachment/2017-11-23/';
                if(storedData) {
                    for (var k = 0, length = array.length; k < length; k++) {
                        src = storedData['img-' + array[k]];
                        var src_demo = ['WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','A33ENAI6K0936LOHRHMH.jpg','mentou.jpg','neijing.jpg','shouyintai.jpg','sh_xy.jpg','sh_xx.jpg','F889PLCX93NGO5FAJEMM.jpg','geti.jpg','WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','sh_sqs.jpg'];
                        if (src != null && src != "" && typeof(src) != 'undefined') {
                            $("[name='img-" + array[k] + "']").val(src);
                            $("#img-" + array[k]).css("background-image", "url(" + src + ")");
                        }else{
                            if (src_demo[k] != null && src_demo[k] != "" && typeof(src_demo[k]) != 'undefined') {
                                $("#img-" + array[k]).css("background-image", "url(" +url+ src_demo[k] + ")");
                            }
                        }
                    }
                }else{
                    var src =  ['WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','A33ENAI6K0936LOHRHMH.jpg','mentou.jpg','neijing.jpg','shouyintai.jpg','sh_xy.jpg','sh_xx.jpg','F889PLCX93NGO5FAJEMM.jpg','geti.jpg','WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','sh_sqs.jpg'];
                    for (var k = 0, length = array.length; k < length; k++) {
                        if (src[k] != null && src[k] != "" && typeof(src[k]) != 'undefined') {
                            $("#img-" + array[k]).css("background-image", "url(" +url+ src[k] + ")");
                        }
                    }
                }
            }


            function xy_status() {
                var xy=document.getElementById('box');
                if(xy.checked === true){
                    $('#xun_sub_button').attr('disabled',false);
                }else{
                    $('#xun_sub_button').attr('disabled',true);
                }
            }

            // Add view
            var mainView = myApp.addView('.view-main', {
                dynamicNavbar: true,
                domCache:false
            });




            var  pro=["请选择","北京","天津","上海","河北省","河南省","山西省","辽宁省","吉林省","黑龙江省","江苏省","浙江省","安徽省","福建省","江西省","山东省","湖北省","湖南省","广东省","海南省","重庆","四川省","贵州省","云南省","西藏自治区","陕西省","甘肃省","青海省","广西壮族自治区","内蒙古自治区","宁夏回族自治区","新疆维吾尔自治区"];
            myApp.picker({
                input: '#picker-city',
                toolbarCloseText: '关闭',
                rotateEffect: false,
                momentumRatio:2,
                cols: [
                    {
                        textAlign: 'left',
                        values: pro,
                        width: 160,
                        onChange: function (picker, country) {

                            if (picker.cols[1].replaceValues) {
                                $.ajax({
                                    url: "/Pays/Reg/area_city",
                                    type: "POST",
                                    dataType: "json",
                                    data: 'name=' + country,
                                    success: function (data) {
                                        picker.cols[1].replaceValues(data);
                                        picker.cols[2].replaceValues(["请选择"]);
                                    }
                                });

                            }
                        }
                    },
                    {
                        textAlign: 'center',
                        values: ["请选择"], //默认
                        width: 160,
                        onChange: function (picker, country) {
                            if (picker.cols[2].replaceValues) {
                                $.ajax({
                                    url: "/Pays/Reg/area_disc",
                                    type: "POST",
                                    dataType: "json",
                                    data: 'name=' + country,
                                    success: function (data) {
                                        picker.cols[2].replaceValues(data);
                                    }
                                });

                            }
                        }
                    },
                    {
                        textAlign: 'right',
                        values: ["请选择"], //默认
                        width: 160
                    }

                ]
            });

            function bankChange() {
                var cityId = $('#selectBnk option:selected').val();
                var Bank_city = $('#picker-bank-city').val();
                var _city = Bank_city.split(' ');
                var city = _city[1];
                if (city != null && city != "" && typeof(city) != 'undefined' && city != '请选择' && cityId != null && cityId != "" && typeof(cityId) != 'undefined') {
                    setLBnk(cityId, city);
                }
            }

            function setLBnk(cityId, city) {
                var params = {city: city, cityId: cityId};
                var linkBnkStr = "<option value=''>请选择支行</option>";
                $.ajax({
                    data: params,
                    url: "/Pays/Reg/bnkLink",
                    type: "POST",
                    dataType: "json",
                    success: function (data) {
                        if (data.list != '' && data.list != null) {
                            for (var i = 0; i < data.list.length; i++) {
                                linkBnkStr += "<option value='" + data.list[i].banking + "'>" + data.list[i].address + "</option>";
                            }
                        } else {

                            linkBnkStr = "<option value=''>未找到分行信息</option>";
                        }
                        $("#selectLBnk").find("option").remove();
                        myApp.smartSelectAddOption('#depositLBnk select', linkBnkStr);
                    }
                });
            }

            myApp.picker({
                input: '#picker-bank-city',
                toolbarCloseText: '关闭',
                rotateEffect: false,
                momentumRatio:2,
                cols: [
                    {
                        textAlign: 'left',
                        values: pro,
                        width: 160,
                        onChange: function (picker, country) {

                            if (picker.cols[1].replaceValues) {
                                $.ajax({
                                    url: "/Pays/Reg/area_city",
                                    type: "POST",
                                    dataType: "json",
                                    data: 'name=' + country,
                                    success: function (data) {
                                        picker.cols[1].replaceValues(data);
                                    }
                                });

                            }
                        }
                    },
                    {
                        textAlign: 'right',
                        values: ["请选择"],
                        width: 160
                    }

                ]
            });


            var countdown = 60;
            function toGetValiNum() {
                $("#validBtn").attr("disabled", "disabled");
                var telNo = $('[name="telNo"]').val();
                var Codes = code_id;
                if (telNo == "") {
                    myApp.alert('请输入手机号');
                    $("#validBtn").removeAttr("disabled");
                    return;
                }
                if (Codes == "") {
                    myApp.alert('请输入或扫描收款码ID');
                    $("#validBtn").removeAttr("disabled");
                    return;
                }
                var telReg = !!telNo.match(/^1[3|4|5|6|7|8|9][0-9]{9}$/);

                if (telReg == false) {
                    myApp.alert('请输入正确的手机号');
                    $("#validBtn").removeAttr("disabled");
                    return;
                }
                $.ajax({
                    data: {tel: telNo, cardSn: Codes},
                    url: "/Pays/Reg/sms_check",
                    type: "POST",
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 1) {
                            settime();
                        } else {
                            myApp.alert(data.info);
                            $("#validBtn").removeAttr("disabled");
                            return;
                        }
                    },
                    error: function (data) {
                        myApp.alert('获取验证码失败!');
                        $("#validBtn").removeAttr("disabled");
                    }
                });

            }

            function settime() {
                if (countdown == 0) {
                    $("#validBtn").removeAttr("disabled");
                    $("#validBtn").text("获取验证码");
                    countdown = 60;
                    return;
                } else {
                    $("#validBtn").attr("disabled", "disabled");
                    $("#validBtn").text(countdown + "s");
                    countdown--;
                }
                setTimeout(function () {
                    settime()
                }, 1000)
            }


            function IMG_UPLOAD(ids, mod) {
                wx.chooseImage({
                    count: 1,
                    sizeType: ['compressed'],
                    sourceType: ['album', 'camera'],
                    success: function (res) {
                        if (res.localIds.length == 0) {
                            myApp.alert('请先使用 chooseImage 接口选择图片');
                            return;
                        }
                        wx.uploadImage({
                            localId: '' + res.localIds,
                            isShowProgressTips: 1,
                            success: function (res) {
                                myApp.showIndicator();
                                $.ajax({
                                    url: '/Pays/Reg/upload_disc',
                                    data: {
                                        media_id: res.serverId,
                                        type: 'Images'
                                    },
                                    type: "POST",
                                    dataType: "json",
                                    success: function (res) {
                                        myApp.hideIndicator();
                                        if (res.status == 1) {
                                            $("#img-" + ids).css("background-image","url("+res.url+")");
                                            $("[name='img-" + ids + "']").val(res.url);
                                            myApp.alert('上传成功', '提示', function () {
                                                setForm();
                                            });
                                        } else {
                                            myApp.alert(res.info);
                                        }
                                    },
                                    beforeSend: function (XMLHttpRequest) {
                                        myApp.showIndicator();
                                    },
                                    error: function (res) {
                                        myApp.alert(res.info);
                                    }
                                })
                            },
                            beforeSend: function (XMLHttpRequest) {
                                myApp.showIndicator();
                            },
                            fail: function (res) {
                                myApp.alert(JSON.stringify(res));
                            }
                        })

                    }
                });
            }


            $$('.xun-menu').on('click', function () {
                var xun_my = [
                    {
                        text: '清空缓存',
                        onClick: function () {
                            myApp.confirm('缓存清空后需要重新填写!您确定要清空本页缓存数据吗?', function () {
                                myApp.formDeleteData('form-'+code_id+'-per');
                                myApp.formDeleteData('form-'+code_id+'-img-per');
                                window.location.reload();
                            });
                        }
                    }

                ];
                var xun_colse = [
                    {
                        text: '关闭',
                        color: 'red'
                    }
                ];

                var groups = [xun_my, xun_colse];
                myApp.actions(groups);
            });

            function setForm() {
                var yyzz = $("[name='img-yyzz']").val();
                var z = $("[name='img-z']").val();
                var p = $("[name='img-p']").val();
                var s = $("[name='img-s']").val();
                var bank = $("[name='img-bank']").val();
                var m1 = $("[name='img-m1']").val();
                var m2 = $("[name='img-m2']").val();

                var authz = $("[name='img-auth-z']").val();
                var authp = $("[name='img-auth-p']").val();
                var sqh = $("[name='img-sqh']").val();



                var formData = {
                    'img-yyzz': yyzz,
                    'img-z': z,
                    'img-p': p,
                    'img-s': s,
                    'img-bank': bank,
                    'img-m1': m1,
                    'img-m2': m2,
                    'img-auth-z':authz,
                    'img-auth-p':authp,
                    'img-sqh':sqh
                };
                myApp.formStoreData("form-"+code_id+"-img-per", formData);
            }




        </script>
</body>
</html>