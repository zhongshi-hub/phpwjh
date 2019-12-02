(function( $ ){
    $(function() {
        $("#FileUpload").on("shown.bs.modal", function () {
            var $wrap = $('#uploader'),
                // 图片容器
                $queue = $('<ul class="filelist" ></ul>')
                    .appendTo($wrap.find('.queueList')),

                // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),

                // 文件总体选择信息。
                $info = $statusBar.find('.info'),

                // 上传按钮
                $upload = $wrap.find('.uploadBtn'),

                // 没选择文件之前的内容。
                $placeHolder = $wrap.find('.placeholder'),

                $progress = $statusBar.find('.progress').hide(),

                // 添加的文件数量
                fileCount = 0,

                // 添加的文件总大小
                fileSize = 0,

                // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,

                // 缩略图大小
                thumbnailWidth = 110 * ratio,
                thumbnailHeight = 110 * ratio,

                // 可能有pedding, ready, uploading, confirm, done.
                state = 'pedding',

                // 所有文件的进度信息，key为file id
                percentages = {},
                // 判断浏览器是否支持图片的base64
                isSupportBase64 = (function () {
                    var data = new Image();
                    var support = true;
                    data.onload = data.onerror = function () {
                        if (this.width != 1 || this.height != 1) {
                            support = false;
                        }
                    }
                    data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                    return support;
                })(),

                // 检测是否已经安装flash，检测flash的版本
                flashVersion = (function () {
                    var version;

                    try {
                        version = navigator.plugins['Shockwave Flash'];
                        version = version.description;
                    } catch (ex) {
                        try {
                            version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                                .GetVariable('$version');
                        } catch (ex2) {
                            version = '0.0';
                        }
                    }
                    version = version.match(/\d+/g);
                    return parseFloat(version[0] + '.' + version[1], 10);
                })(),

                supportTransition = (function () {
                    var s = document.createElement('p').style,
                        r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                    s = null;
                    return r;
                })(),

                // WebUploader实例
                uploader;

            if (!WebUploader.Uploader.support('flash') && WebUploader.browser.ie) {

                // flash 安装了但是版本过低。
                if (flashVersion) {
                    (function (container) {
                        window['expressinstallcallback'] = function (state) {
                            switch (state) {
                                case 'Download.Cancelled':
                                    alert('您取消了更新！')
                                    break;

                                case 'Download.Failed':
                                    alert('安装失败')
                                    break;

                                default:
                                    alert('安装已成功，请刷新！');
                                    break;
                            }
                            delete window['expressinstallcallback'];
                        };

                        var swf = './expressInstall.swf';
                        // insert flash object
                        var html = '<object type="application/' +
                            'x-shockwave-flash" data="' + swf + '" ';

                        if (WebUploader.browser.ie) {
                            html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                        }

                        html += 'width="100%" height="100%" style="outline:0">' +
                            '<param name="movie" value="' + swf + '" />' +
                            '<param name="wmode" value="transparent" />' +
                            '<param name="allowscriptaccess" value="always" />' +
                            '</object>';

                        container.html(html);

                    })($wrap);

                    // 压根就没有安转。
                } else {
                    $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
                }

                return;
            } else if (!WebUploader.Uploader.support()) {
                alert('Web Uploader 不支持您的浏览器！');
                return;
            }

            if(UploadServer == 'wx'){
                var UploadServers='/Plugs/Upload/upload_weixin';
            }else{
                var UploadServers='/Plugs/Upload/upload_local';
            }


            // 实例化
            uploader = WebUploader.create({
                pick: {
                    id: '#filePicker',
                    label: '点击选择图片'
                },
                dnd: '#dndArea',
                paste: '#uploader',
                swf: '/Source/webuploader/Uploader.swf',
                chunked: false,
                chunkSize: 512 * 1024,
                server: UploadServers,
                formData: { "Mod": upload_mod,'TypeKey':$('#XunTypeKey').val()},
                //runtimeOrder: 'flash',
                accept: {
                    title: '上传图片',
                    extensions: 'ico,jpg,jpeg,png',
                    mimeTypes: 'image/ico,image/jpg,image/jpeg,image/png'
                },
                //不启用压缩
                compress: false,
                // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
                disableGlobalDnd: true,
                fileNumLimit: 1,
                fileSizeLimit: 200 * 1024 * 1024,    // 200 M
                fileSingleSizeLimit: 100 * 1024 * 1024    // 100 M
            });
            uploader.option('compress', {
                width: 2000,
                height: 1600,
                // 图片质量，只有type为`image/jpeg`的时候才有效。
                quality: 90,
                // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                allowMagnify: false,
                // 是否允许裁剪。
                crop: false,
                // 是否保留头部meta信息。
                preserveHeaders: true,
                // 如果发现压缩后文件大小比原来还大，则使用原来图片
                // 此属性可能会影响图片自动纠正功能
                noCompressIfLarger: false,
                // 单位字节，如果图片大小小于此值，不会采用压缩。
                compressSize: 102400
            });

            // 拖拽时不接受 js, txt 文件。
            uploader.on('dndAccept', function (items) {
                var denied = false,
                    len = items.length,
                    i = 0,
                    // 修改js类型
                    unAllowed = 'text/plain;application/javascript ';

                for (; i < len; i++) {
                    // 如果在列表里面
                    if (~unAllowed.indexOf(items[i].type)) {
                        denied = true;
                        break;
                    }
                }

                return !denied;
            });

            uploader.on('dialogOpen', function () {
                console.log('here');
            });

            // uploader.on('filesQueued', function() {
            //     uploader.sort(function( a, b ) {
            //         if ( a.name < b.name )
            //           return -1;
            //         if ( a.name > b.name )
            //           return 1;
            //         return 0;
            //     });
            // });

            // 添加“添加文件”的按钮，
            uploader.addButton({
                //id: '#filePicker2',
                //label: '继续添加'
            });

            uploader.on('ready', function () {
                window.uploader = uploader;
            });

            // 当有文件添加进来时执行，负责view的创建
            function addFile(file) {
                var $li = $('<li id="' + file.id + '">' +
                        '<p class="title">' + file.name + '</p>' +
                        '<p class="imgWrap"></p>' +
                        /*'<p class="progress"><span></span></p>' +*/
                        '</li>'),

                    $btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span></div>').appendTo($li),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find('p.imgWrap'),
                    $info = $('<p class="error"></p>'),

                    showError = function (code) {
                        switch (code) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;

                            case 'interrupt':
                                text = '上传暂停';
                                break;

                            default:
                                text = '上传失败，请重试';
                                break;
                        }

                        $info.text(text).appendTo($li);
                    };

                if (file.getStatus() === 'invalid') {
                    showError(file.statusText);
                } else {
                    // @todo lazyload
                    $wrap.text('预览中');
                    uploader.makeThumb(file, function (error, src) {
                        var img;

                        if (error) {
                            $wrap.text('不能预览');
                            return;
                        }

                        if (isSupportBase64) {
                            img = $('<img src="' + src + '">');
                            $wrap.empty().append(img);
                        } else {
                            $.ajax('../../server/preview.php', {
                                method: 'POST',
                                data: src,
                                dataType: 'json'
                            }).done(function (response) {
                                if (response.result) {
                                    img = $('<img src="' + response.result + '">');
                                    $wrap.empty().append(img);
                                } else {
                                    $wrap.text("预览出错");
                                }
                            });
                        }
                    }, thumbnailWidth, thumbnailHeight);

                    percentages[file.id] = [file.size, 0];
                    file.rotation = 0;
                }

                file.on('statuschange', function (cur, prev) {
                    if (prev === 'progress') {
                        $prgress.hide().width(0);
                    } else if (prev === 'queued') {
                        $li.off('mouseenter mouseleave');
                        $btns.remove();
                    }

                    // 成功
                    if (cur === 'error' || cur === 'invalid') {
                        console.log(file.statusText);
                        showError(file.statusText);
                        percentages[file.id][1] = 1;
                    } else if (cur === 'interrupt') {
                        showError('interrupt');
                    } else if (cur === 'queued') {
                        percentages[file.id][1] = 0;
                    } else if (cur === 'progress') {
                        $info.remove();
                        $prgress.css('display', 'block');
                    } else if (cur === 'complete') {
                        $li.append('<span class="success"></span>');
                    }

                    $li.removeClass('state-' + prev).addClass('state-' + cur);
                });

                $li.on('mouseenter', function () {
                    $btns.stop().animate({height: 30});
                });

                $li.on('mouseleave', function () {
                    $btns.stop().animate({height: 0});
                });

                $btns.on('click', 'span', function () {
                    var index = $(this).index(),
                        deg;

                    switch (index) {
                        case 0:
                            uploader.removeFile(file);
                            return;

                        case 1:
                            file.rotation += 90;
                            break;

                        case 2:
                            file.rotation -= 90;
                            break;
                    }

                    if (supportTransition) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        $wrap.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
                    }


                });

                $li.appendTo($queue);
            }

            // 负责view的销毁
            function removeFile(file) {
                var $li = $('#' + file.id);

                delete percentages[file.id];
                updateTotalProgress();
                $li.off().find('.file-panel').off().end().remove();
            }

            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

                $.each(percentages, function (k, v) {
                    total += v[0];
                    loaded += v[0] * v[1];
                });

                percent = total ? loaded / total : 0;


                spans.eq(0).text(Math.round(percent * 100) + '%');
                spans.eq(1).css('width', Math.round(percent * 100) + '%');
                updateStatus();
            }

            function updateStatus() {
                var text = '', stats;

                if (state === 'ready') {
                    text = '选中' + fileCount + '张图片，共' +
                        WebUploader.formatSize(fileSize) + '。';
                } else if (state === 'confirm') {
                    stats = uploader.getStats();
                    if (stats.uploadFailNum) {
                        text = stats.uploadFailNum + '个上传失败，<a class="retry" href="#">重新上传</a>失败附件'
                    }

                } else {
                    stats = uploader.getStats();
                    text = '共' + fileCount + '张（' +
                        WebUploader.formatSize(fileSize) +
                        '）';

                    if (stats.uploadFailNum) {
                        text += '，失败' + stats.uploadFailNum + '张';
                    }
                }

                $info.html(text);
            }

            function setState(val) {
                var file, stats;

                if (val === state) {
                    return;
                }

                $upload.removeClass('state-' + state);
                $upload.addClass('state-' + val);
                state = val;

                switch (state) {
                    case 'pedding':
                        $placeHolder.removeClass('element-invisible');
                        $queue.hide();
                        //$statusBar.addClass( 'element-invisible' );
                        uploader.refresh();
                        break;

                    case 'ready':
                        $placeHolder.addClass('element-invisible');
                        $('#filePicker2').removeClass('element-invisible');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'uploading':
                        $('#filePicker2').addClass('element-invisible');
                        $progress.show();
                        $upload.text('暂停上传');
                        break;

                    case 'paused':
                        $progress.show();
                        $upload.text('继续上传');
                        break;

                    case 'confirm':
                        $progress.hide();
                        $('#filePicker2').removeClass('element-invisible');
                        $upload.text('确认使用');

                        stats = uploader.getStats();
                        if (stats.successNum && !stats.uploadFailNum) {
                            setState('finish');
                            return;
                        }
                        break;
                    case 'finish':
                        stats = uploader.getStats();
                        //alert(stats);
                        if (stats.successNum) {
                            // alert( '上传成功' );
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                        break;
                }

                updateStatus();
            }

            uploader.onUploadProgress = function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

                $percent.css('width', percentage * 100 + '%');
                percentages[file.id][1] = percentage;
                updateTotalProgress();
            };

            uploader.onFileQueued = function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    $placeHolder.addClass('element-invisible');
                    $statusBar.show();
                }

                addFile(file);
                setState('ready');
                updateTotalProgress();
            };

            uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;

                if (!fileCount) {
                    setState('pedding');
                }

                removeFile(file);
                updateTotalProgress();

            };

            uploader.on('all', function (type) {
                var stats;
                switch (type) {
                    case 'uploadFinished':
                        setState('confirm');
                        break;

                    case 'startUpload':
                        setState('uploading');
                        break;

                    case 'stopUpload':
                        setState('paused');
                        break;

                }
            });
            //上传完成结果返回
            uploader.on('uploadSuccess', function (file, res) {
                if (res.status == 0) {//错误报告
                    alert(res.msg);
                    state = 'done';
                    location.reload();
                } else {
                    if(upload_type=='Input'){
                        $('#'+res.TypeKey).val(res.FilePath);
                    }else if(upload_type=='UEdit'){
                        UE.getEditor(res.TypeKey).execCommand('insertimage', {
                            src: res.FilePath,
                            _src:res.file.save_path,
                            alt: res.file.file_name,
                            width:'350'
                        });
                    }else if(upload_type=='Image'){
                        $('#'+res.TypeKey).attr('src',res.FilePath);
                        $('#'+upload_cid).val(res.FilePath).trigger('change');
                        $('#'+res.TypeKey).show();
                    }else if(upload_type=='WeiXin'){
                        $.niftyNoty({
                            type: 'success',
                            message : '<strong>'+res.msg+'</strong>',
                            container : 'floating',
                            timer : 2500
                        });
                        setTimeout(function(){
                            window.location.reload();
                        }, 2500);
                    }else{
                        alert(res.msg);
                    }
                    $('#FileUpload').modal('hide');
                }
            });


            uploader.onError = function (code) {
                if (code=="Q_TYPE_DENIED"){
                    alert("请上传JPG、PNG、JPEG、ICO格式文件");
                }else if(code=="F_EXCEED_SIZE"){
                    alert("文件超出规定大小");
                }else if(code=='Q_EXCEED_NUM_LIMIT'){
                    alert('超出单次最大上传数量')
                }else{
                    alert('Eroor: ' + code);
                }
            };

            $upload.on('click', function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }

                if (state === 'ready') {
                    uploader.upload();
                } else if (state === 'paused') {
                    uploader.upload();
                } else if (state === 'uploading') {
                    uploader.stop();
                }
            });

            $info.on('click', '.retry', function () {
                uploader.retry();
            });

            $info.on('click', '.ignore', function () {
                alert('todo');
            });

            $upload.addClass('state-' + state);
            updateTotalProgress();
        });
    });
})( jQuery );

function edit_upload_modal(key){
    $('#XunTypeKey').val(key);
    $('#FileUpload').modal({show: true});
}


function upload_network(){
    var dataUrl=$("#dataUrl").val();
    if(dataUrl==''||dataUrl==null||dataUrl=='undefined'){
        alert('请输入网络图片地址');
    }else{
        $.post("/Plugs/Upload/upload_network",{type:'XunNetWorkDown',url:dataUrl,"Mod": upload_mod,'TypeKey':$('#XunTypeKey').val()}, function(res){
            if (res.status == 0) {//错误报告
                alert(res.msg);
            } else {
                if(upload_type=='Input'){
                    $('#'+res.TypeKey).val(res.FilePath);
                }else if(upload_type=='UEdit'){
                    UE.getEditor(res.TypeKey).execCommand('insertimage', {
                        src: res.FilePath,
                        _src:res.file.save_path,
                        alt: res.file.file_name,
                        width:'350'
                    });
                }else if(upload_type=='Image'){
                    $('#'+res.TypeKey).attr('src',res.FilePath);
                    $('#'+upload_cid).val(res.FilePath).trigger('change');
                    $('#'+res.TypeKey).show();
                }else if(upload_type=='WeiXin'){
                    window.location.reload();
                }else{
                    alert(res.msg);
                }
                $('#FileUpload').modal('hide');
            }
        }, 'json');
    }
}

function upload_model_html() {
    if(UploadServer == 'wx'){
        var strVar = "";
        strVar += "<div class=\"XunMaFu bootbox modal fade\" id=\"FileUpload\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\"\n";
        strVar += "     aria-hidden=\"true\">\n";
        strVar += "    <div class=\"modal-dialog\">\n";
        strVar += "        <div class=\"modal-content\">\n";
        strVar += "            <div class=\"modal-body\">\n";
        strVar += "                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" style=\"margin-top: -10px;\"><i\n";
        strVar += "                        class=\"pci-cross pci-circle\"><\/i><\/button>\n";
        strVar += "                <div class=\"bootbox-body\">\n";
        strVar += "                    <div class=\"tab-content\">\n";
        strVar += "                        <input type=\"hidden\" id=\"XunTypeKey\" value=\"\">\n";
        strVar += "                        <!--图片上传 Start-->\n";
        strVar += "                        <div id=\"upload_local\" class=\"tab-pane fade active in\">\n";
        strVar += "                            <div id=\"uploader\">\n";
        strVar += "                                <div class=\"queueList\">\n";
        strVar += "                                    <div id=\"dndArea\" class=\"placeholder\">\n";
        strVar += "                                        <div id=\"filePicker\"><\/div>\n";
        strVar += "                                        <p>或将照片拖到这里，单次最多可选1张<\/p>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                <\/div>\n";
        strVar += "                                <div class=\"statusBar\">\n";
        strVar += "                                    <div class=\"progress\">\n";
        strVar += "                                        <span class=\"text\">0%<\/span>\n";
        strVar += "                                        <span class=\"percentage\"><\/span>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                    <div class=\"info\"><\/div>\n";
        strVar += "                                    <div class=\"btns\">\n";
        strVar += "                                        <div id=\"filePicker2\"><\/div>\n";
        strVar += "                                        <div class=\"uploadBtn\">开始上传<\/div>\n";
        strVar += "                                        <div class=\"webuploader-pick\" data-dismiss=\"modal\">取消<\/div>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                <\/div>\n";
        strVar += "                            <\/div>\n";
        strVar += "                        <\/div>\n";
        strVar += "                    <\/div>\n";
        strVar += "                <\/div>\n";
        strVar += "            <\/div>\n";
        strVar += "        <\/div>\n";
        strVar += "    <\/div>\n";
        strVar += "<\/div>\n";
    }else {
        var strVar = "";
        strVar += "<div class=\"XunMaFu bootbox modal fade\" id=\"FileUpload\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\"\n";
        strVar += "     aria-hidden=\"true\">\n";
        strVar += "    <div class=\"modal-dialog\">\n";
        strVar += "        <div class=\"modal-content\">\n";
        strVar += "            <div class=\"modal-body\">\n";
        strVar += "                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" style=\"margin-top: -10px;\"><i\n";
        strVar += "                        class=\"pci-cross pci-circle\"><\/i><\/button>\n";
        strVar += "                <div class=\"bootbox-body\">\n";
        strVar += "                    <ul class=\"nav nav-tabs\">\n";
        strVar += "                        <li class=\"active\">\n";
        strVar += "                            <a data-toggle=\"tab\" href=\"#upload_local\" aria-expanded=\"false\">本地上传图片<\/a>\n";
        strVar += "                        <\/li>\n";
        strVar += "                        <li>\n";
        strVar += "                            <a data-toggle=\"tab\" href=\"#upload_network\" aria-expanded=\"true\">提取网络图片<\/a>\n";
        strVar += "                        <\/li>\n";
        strVar += "                    <\/ul>\n";
        strVar += "                    <div class=\"tab-content\">\n";
        strVar += "                        <input type=\"hidden\" id=\"XunTypeKey\" value=\"\">\n";
        strVar += "                        <!--图片上传 Start-->\n";
        strVar += "                        <div id=\"upload_local\" class=\"tab-pane fade active in\">\n";
        strVar += "                            <div id=\"uploader\">\n";
        strVar += "                                <div class=\"queueList\">\n";
        strVar += "                                    <div id=\"dndArea\" class=\"placeholder\">\n";
        strVar += "                                        <div id=\"filePicker\"><\/div>\n";
        strVar += "                                        <p>或将照片拖到这里，单次最多可选1张<\/p>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                <\/div>\n";
        strVar += "                                <div class=\"statusBar\">\n";
        strVar += "                                    <div class=\"progress\">\n";
        strVar += "                                        <span class=\"text\">0%<\/span>\n";
        strVar += "                                        <span class=\"percentage\"><\/span>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                    <div class=\"info\"><\/div>\n";
        strVar += "                                    <div class=\"btns\">\n";
        strVar += "                                        <div id=\"filePicker2\"><\/div>\n";
        strVar += "                                        <div class=\"uploadBtn\">开始上传<\/div>\n";
        strVar += "                                        <div class=\"webuploader-pick\" data-dismiss=\"modal\">取消<\/div>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                <\/div>\n";
        strVar += "                            <\/div>\n";
        strVar += "                        <\/div>\n";
        strVar += "                        <!--图片上传 END-->\n";
        strVar += "                        <div id=\"upload_network\" class=\"tab-pane fade\">\n";
        strVar += "                            <div id=\"uploader\">\n";
        strVar += "                                <div class=\"queueList\">\n";
        strVar += "                                    <div class=\"form-group\" style=\"border: 3px dashed #e6e6e6;color: #cccccc;font-size: 18px;min-height: 238px;position: relative;text-align: center;\">\n";
        strVar += "                                        <textarea style=\"padding: 25px;margin: 1em;resize: none;width:95%;height:222px;\"\n";
        strVar += "                                                  placeholder=\"请输入网络图片地址 如:http://www.xxx.com/xxx.jpg  (仅支持图片格式提取)\"\n";
        strVar += "                                                  class=\"form-control valid\" name=\"dataUrl\" id=\"dataUrl\"\n";
        strVar += "                                                  aria-invalid=\"false\"><\/textarea>\n";
        strVar += "                                        <p>单次提取限制一张,不支持多个提取<\/p>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                <\/div>\n";
        strVar += "                                <div class=\"statusBar\">\n";
        strVar += "                                    <div class=\"info\">输入网络图片地址(HTTP开头) 点击确认提取<\/div>\n";
        strVar += "                                    <div class=\"btns\">\n";
        strVar += "                                        <div class=\"uploadBtn\" onclick=\"upload_network();\">确认提取<\/div>\n";
        strVar += "                                        <div class=\"webuploader-pick\" data-dismiss=\"modal\">取消<\/div>\n";
        strVar += "                                    <\/div>\n";
        strVar += "                                <\/div>\n";
        strVar += "                            <\/div>\n";
        strVar += "                        <\/div>\n";
        strVar += "                    <\/div>\n";
        strVar += "                <\/div>\n";
        strVar += "            <\/div>\n";
        strVar += "        <\/div>\n";
        strVar += "    <\/div>\n";
        strVar += "<\/div>\n";
    }
    var model_html="<script type=\'text/javascript\'>$('#FileUpload').on('hidden.bs.modal'\,function () {for (var i = 0; i < uploader.getFiles().length; i++) {uploader.removeFile(uploader.getFiles()[i]);}uploader.reset();$('#dataUrl').val('');});<\/script>";
    $("body").append(strVar).append(model_html);
}

function UeditConfig(data) {
    var UEditJs='<script type="text/javascript" src="/Source/UEditor/ueditor.config.js"><\/script>\n<script type="text/javascript" src="/Source/UEditor/ueditor.all.min.js"><\/script>';
    var html=[];
    for (var k = 0, length = data.length; k < length; k++) {
        html += 'var ue_'+data[k]+' = UE.getEditor("'+data[k]+'");\n';
    }
    var _html="<script type=\'text/javascript\'>"+html+"\UE.registerUI('XunImage',function(editor, uiName\){ImgModel \=new \UE.ui.Button\({name: uiName\,title: \"插入图片\"\,cssRules: \"background-position: -380px 0\"\,onclick: function() {edit_upload_modal(editor.key);}})\;return ImgModel;},19);<\/script>";
    $('body').append(UEditJs).append(_html);
}

