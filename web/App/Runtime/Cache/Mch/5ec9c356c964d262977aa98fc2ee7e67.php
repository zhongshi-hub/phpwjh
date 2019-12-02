<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html style="height: auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>密码重置</title>
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
    <style>
        .btn-primary{
            background-color:#4282e3;
        }
        .btn-primary:active{
            background-color:#5593F1;
        }
    </style>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back();" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">重置密码</span></div>
    </header>
    <section class="g-scrollview">
        <aside class="demo-tip">
            为了您的信息安全,建议设置密码为8-12位密码
        </aside>
        <form action="<?php echo U('r_pass');?>" method="post">
            <div class="m-cell">
                <div class="cell-item">
                    <div class="cell-right"><input class="cell-input" placeholder="请输入您商户绑定的手机号" autocomplete="off"
                                                   type="number" pattern="[0-9]*" name="user_tel" required></div>
                </div>
                <div class="cell-item">

                    <div class="cell-right">
                        <input type="number" pattern="[0-9]*" class="cell-input" placeholder="请输入验证码" autocomplete="off"
                               name="verify" required/>
                        <a href="javascript:;" class="btn btn-warning" id="J_GetCode">获取短信验证码</a>
                    </div>
                </div>

                <div class="cell-item">
                    <div class="cell-right"><input class="cell-input" placeholder="请输入新密码" autocomplete="off"
                                                   type="text" name="new_pass" required>
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-right"><input class="cell-input" placeholder="请再次输入新密码" autocomplete="off"
                                                   type="text" name="new_pass_rese" required>
                    </div>
                </div>
            </div>
            <div class="m-button">
                <button type="submit" class="btn-block btn-primary">提 交</button>
            </div>
        </form>
    </section>
    <section class="with-line xun_footer">
        <?php echo ($_domain['web_name']); ?>&copy;版权所有
    </section>

</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<script>
    $(function () {
        $("form").submit(function (e) {
            e.preventDefault();
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            YDUI.dialog.loading.open('信息提交中...');
            $.post(actionurl, ajax_data, function (data) {
                YDUI.dialog.loading.close();
                if (data.status == 1) {
                    YDUI.dialog.toast(data.info, 'success', 3000, function () {
                        window.location.href = data.url
                    });
                }
                else {
                    msg(data.info);
                }
            }, 'json');
        });

        var dialog = YDUI.dialog;
        var $getCode = $('#J_GetCode');
        // 定义参数
        $getCode.sendCode({
            disClass: 'btn-disabled',
            secs: 60,
            run: false,
            runStr: '{%s}秒后重新获取',
            resetStr: '重新获取验证码'
        });

        $getCode.on('click', function () {
            var $this = $(this);
            var telNo = $('[name="user_tel"]').val();
            if (telNo == "") {
                msg('请输入手机号');
                return false;
            }
            var telReg = !!telNo.match(/^1[3|4|5|7|8][0-9]{9}$/);
            if (telReg == false) {
                msg('请输入正确的手机号');
                return false;
            }

            dialog.loading.open('发送中...');
            var ajax_data = {tel: telNo};
            var actionurl = '<?php echo U("check_rest");?>';
            $.post(actionurl, ajax_data, function (data) {
                dialog.loading.close();
                if (data.status == 1) {
                    $this.sendCode('start');
                    dialog.toast('已发送', 'success', 1500);
                }
                else {
                    YDUI.dialog.alert(data.info);
                }
            }, 'json');

        });


        function msg(data) {
            YDUI.dialog.toast(data, 'none');
        }



    });
    function call_back() {
        location.href=document.referrer;
    }
</script>

</body>

</html>