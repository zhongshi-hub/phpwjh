<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>商户控制台登录 -<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_domain['web_name']); ?>商户控制台登录" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <!-- Toastr css -->
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    <!-- App css -->
    <link href="/Source/amp/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/style.css" rel="stylesheet" type="text/css" />
    <script src="/Source/amp/assets/js/modernizr.min.js"></script>
</head>
<body class="account-pages">
<!-- Begin page -->
<div class="accountbg" style="background: url('/Source/amp/assets/images/bg-1.jpg');background-size: cover;background-position: center;"></div>
<div class="wrapper-page account-page-full">
    <div class="card">
        <div class="card-block">
            <div class="account-box">
                <div class="card-box p-5">
                    <h2 class="text-uppercase text-center pb-4">
                        <a href="index.html" class="text-success">
                            <span><img src="/Source/amp/assets/images/login.png" alt="" height="26"></span>
                        </a>
                    </h2>
                    <form class="<?php echo U('index');?>" action="#">
                        <div class="form-group m-b-20 row">
                            <div class="col-12">
                                <label for="username">账户</label>
                                <input class="form-control" type="text" name="username" id="username" required="" placeholder="请输入手机号">
                            </div>
                        </div>

                        <div class="form-group row m-b-20">
                            <div class="col-12">
                                <label for="password">密码</label>
                                <input class="form-control" name="password" type="password" required="" id="password" placeholder="请输入密码">
                            </div>
                        </div>

                        <div class="form-group row m-b-20">
                            <div class="col-12">
                                <label for="verify_input">验证码</label>
                                <input class="form-control" type="text" name="verify" required="" id="verify_input" placeholder="请输入验证码">
                                <img class="pass-verifyCode" id="verify" src="<?php echo U('loadVerify');?>" style="position: inherit;cursor: pointer;border-radius: 4px;padding-right:2px;width: 30%;float:right;margin-top: -36px;height: 33px;">
                            </div>
                        </div>

                        <div class="form-group row m-b-20">
                            <div class="col-12">

                                <div class="checkbox checkbox-custom">
                                    <input id="remember" type="checkbox" checked="">
                                    <label for="remember">
                                        记住我
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div class="form-group row text-center m-t-10">
                            <div class="col-12">
                                <button class="btn btn-block btn-custom waves-effect waves-light" type="submit">登 录</button>
                            </div>
                        </div>

                    </form>

                    <div class="row m-t-50">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted">如忘记登录密码请联系平台重置密码</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <div class="m-t-40 text-center">
        <p class="account-copyright">2018 © <?php echo ($_domain['web_name']); ?>. -  <?php echo ($_domain['web_domain']); ?></p>
    </div>
</div>
<!-- jQuery  -->
<script src="/Source/amp/assets/js/jquery.min.js"></script>
<script src="/Source/amp/assets/js/bootstrap.bundle.min.js"></script>
<script src="/Source/amp/assets/js/metisMenu.min.js"></script>
<script src="/Source/amp/assets/js/waves.js"></script>
<script src="/Source/amp/assets/js/jquery.slimscroll.js"></script>
<!-- App js -->
<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>
<!-- Toastr js -->
<script src="/Source/amp/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
<script>
    $(function () {
        $("form").submit(function (e) {
            e.preventDefault();
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function (data) {
                if (data.status === 1) {
                    $.toast({
                        heading: '登录成功!',
                        text: data.info,
                        position: 'top-right',
                        loaderBg: '#5ba035',
                        icon: 'success',
                        hideAfter: 3000,
                        stack: 1
                    });
                    setTimeout(function () {
                        window.location.href = data.url
                    }, 3000);
                }
                else {
                    $.toast({
                        heading: '温馨提示',
                        text: data.info,
                        position: 'top-right',
                        loaderBg: '#bf441d',
                        icon: 'error',
                        hideAfter: 3000,
                        stack: 1
                    });
                }
            }, 'json');
        });
    });
    $('#verify').click(function () {
        var times = new Date().getTime();
        document.getElementById('verify').src = "<?php echo U('loadVerify');?>/t/" + times;
    });
</script>
</body>
</html>