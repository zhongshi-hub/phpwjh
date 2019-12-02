<!DOCTYPE html>
<html lang="zh-cn"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>跳转提示..</title>
<link href="__PUBLIC__/Jump/bootstrap.css" rel="stylesheet">
<link href="__PUBLIC__/Jump/awesome/css/font-awesome.css" rel="stylesheet">
<link href="__PUBLIC__/Jump/common.css" rel="stylesheet">
<script type="text/javascript" src="__PUBLIC__/Jump/jquery-1.js"></script>
<script type="text/javascript" src="__PUBLIC__/Jump/util.js"></script>
<script type="text/javascript" src="__PUBLIC__/Jump/require.js"></script>
<script type="text/javascript" src="__PUBLIC__/Jump/jquery-1_002.js"></script>
<script type="text/javascript" src="__PUBLIC__/Jump/bootstrap.js"></script>
</head>
<body>
 <div class="container-fluid">
		<div class="panel panel-default" style="left: 50%;margin: -200px 0 0 -250px;position: absolute;top: 50%;width:500px;">
			 <div class="panel-heading">
				温馨提示 <span style="float: right;margin-right: 10px;">等待时间： <b id="wait"><?php echo($waitSecond); ?></b></span>
			 </div>
			<div class="panel-body">
				<div class="form-group">
					<div class="col-xs-12 ">
					  <div class="col-xs-12 col-sm-3 col-lg-2">
					    <present name="message">
						   <i style="color:#3c763d;" class="fa fa-5x fa-check-circle"></i>
					    <else/>
						  <i style="color:rgb(161, 35, 35);" class="fa fa-5x fa-times-circle"></i>
					    </present>
			          </div>
					  <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10" style="margin-top:10px;">
					    <p><present name="message"> <?php echo($message); ?> <else/> <?php echo($error); ?></present></p>
					    <p><a id="href" href="<?php echo($jumpUrl); ?>">如果你的浏览器没有自动跳转，请点击此链接</a></p>
					  </div>
					</div>
				</div>
		    </div>
		</div>
 </div>
<script type="text/javascript">
	if(navigator.appName == 'Microsoft Internet Explorer'){
		if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
			alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
		}
	}
	(function(){
			 var wait = document.getElementById('wait'),href = document.getElementById('href').href;
			 var interval = setInterval(function(){
				 var time = --wait.innerHTML;
				 if(time <= 0) {
					 location.href = href;
					 clearInterval(interval);
				 }
			 }, 1000);
	})();
</script>
</body>
</html>