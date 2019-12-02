<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="/Source/webuploader/webuploader.css" />
    <link rel="stylesheet" type="text/css" href="/Source/webuploader/style.css?t=<?php echo time();?>" />
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/Source/webuploader/jquery.js"></script>
    <script type="text/javascript" src="/Source/webuploader/webuploader.js?t=<?php echo time();?>"></script>
    <script type="text/javascript" src="/Source/webuploader/upload.js?t=<?php echo time();?>"></script>
    <script src="/Source/artDialog/jquery.artDialog.js?skin=simple"></script>
    <script src="/Source/artDialog/plugins/iframeTools.js"></script>
    <script type="text/javascript" src="/Source/fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <style>
        a:focus {outline: none;}
        .nav > li > a{
            padding: 5px 10px!important;
        }
        .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus{background-color: #07aeae!important;}
    </style>
</head>
<body>
<?php if(!isset($_GET['status'])){ ?>
<ul id="myTab" class="nav nav-pills" style="margin: 1em;width: auto;">
    <li role="presentation" class="active"><a href="#wrapper" data-toggle="tab" >上传图片</a></li>
    <li role="presentation"><a href="#inter" data-toggle="tab">提取网络图片</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <!--图片上传 Start-->
    <div id="wrapper" class="tab-pane fade in active" >
        <div id="container">
            <!--头部，相册选择和格式选择-->
            <div id="uploader">
                <div class="queueList">
                    <div id="dndArea" class="placeholder">
                        <div id="filePicker"></div>
                        <p>或将照片/附件拖到这里，单次最多可选1张</p>
                        <input type="hidden" id="wbs_width" value="<?php echo $_GET['width']; ?>"/>
                        <input type="hidden" id="wbs_height" value="<?php echo $_GET['height']; ?>"/>
                    </div>
                </div>
                <div class="statusBar" style="position:fixed;bottom:0;width:95%">
                    <div class="progress">
                        <span class="text">0%</span>
                        <span class="percentage"></span>
                    </div><div class="info"></div>
                    <div class="btns">
                        <div id="filePicker2"></div><div class="uploadBtn">确认使用</div>
                        <div class="webuploader-pick" onclick="colseData()">取消</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--图片上传 END-->

    <!--提取网络图片 Start-->
    <div class="tab-pane fade" id="inter" style="margin: 1em;width: auto;">
        <div  class="tab-pane fade in active" >
            <div id="container">
                <div style="border: 3px dashed #e6e6e6;margin: 20px;">
                    <!--头部，相册选择和格式选择-->
                    <form action="" method="post" class="form-horizontal" onSubmit="return false;" role="form">
                        <div id="uploader">
                            <div class="queueList">

                                <div class="form-group" style="margin:10% 0px;">
                                    <textarea style="padding: 20px;margin: 1em;resize: none;width:95%;height:150px;" placeholder="请输入网络图片地址 如:http://www.xxx.com/xxx.jpg  (仅支持图片格式提取)" class="form-control valid" name="wbs_dataurl" id="wbs_dataurl" aria-invalid="false"></textarea>
                                </div>
                            </div>
                            <div class="statusBar" style="position:fixed;bottom:0;width:95%;margin-left: -20px;">
                                <div class="info">输入网络图片地址,点击确认提取</div>
                                <div class="btns">
                                    <div id="filePicker2"></div><div class="uploadBtn"  onclick="wbs_netsub();">确认提取</div>
                                    <div class="webuploader-pick" onclick="colseData()">取消</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--提取网络图片 END-->
</div>
<?php }else{ ?>
<script>
    var domid=art.dialog.data('domid');
    var index = parent.layer.getFrameIndex(window.name);
    // 返回数据到主页面
    function returnData(url){
        parent.$('#'+domid).val(url);
        parent.layer.close(index);
    }
    <?php if($_GET['status']==1){ ?>
    returnData('<?php echo base64_decode($_GET['url']); ?>');
    <?php } ?>
</script>
<?php } ?>
<!--下载网络图片 AJAX-->
<script type="text/javascript">
    function wbs_netsub(){
        var type='wbs_net';
        var dataurl=$("#wbs_dataurl").val();
        if(dataurl==''||dataurl==null||dataurl=='undefined'){
            alert('请输入网络图片地址');
        }else{
            $.post("<?php echo U('Plugs/Upload/netdown');?>",{type:type,url:dataurl}, function(data){
                if(data.status == 1){
                    alert(data.info);
                    location.href=data.url;
                }
                else{
                    alert(data.info);
                }
            }, 'json');
        }
    }

    function colseData() {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }
</script>
<script type="text/javascript">
    if (self == top) {location.href="../";}
</script>

</body>
</html>