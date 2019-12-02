<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>iFrame Xun</title>
    <link rel="stylesheet" type="text/css" href="/Source/easyui/themes/insdep/easyui.css">
    <link rel="stylesheet" type="text/css" href="/Source/easyui/themes/insdep/insdep_tables.css">
    <link rel="stylesheet" type="text/css" href="/Source/easyui/bootstrap.min.css">
    <script type="text/javascript" src="/Source/layer/layer.js"></script>
    <script type="text/javascript" src="/Source/easyui/jquery.min.js"></script>
    <script type="text/javascript" src="/Source/easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/Source/easyui/themes/insdep/jquery.insdep-extend.min.js"></script>
<body>

<div class="container">
    <div class="fixed-table-toolbar" style="margin-top: 20px;">
        <form action="" method="post">
            <div class="pull-left" style="width: 90%;"><input class="form-control" placeholder="用户名、联系电话" type="text" name="Search" required></div>
            <div class="bs-bars pull-right">
                <div id="toolbar">
                    <button id="remove" class="btn btn-danger" type="submit">
                        搜索
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div style="margin-top: 70px;">
        <table id="dg" class="easyui-datagrid" style="height:350px">
            <thead>
            <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th data-options="field:'id'">ID</th>
                <th data-options="field:'name',width:'46%'">用户姓名</th>
                <th data-options="field:'phone',width:'45.8%'">联系电话</th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="fixed-table-toolbar" style="margin-top: 10px;">
        <div class="bs-bars pull-right">
            <div>
                <button  class="btn btn-success calls">
                    确定
                </button>
            </div>
        </div>
    </div>

</div>
<script>
    if(top==self){
    top.window.location.href   = '<?php echo U("Admin/Index/index");?>';
    }
    //初始化
    $(function(){
        $('#dg').datagrid({
            singleSelect:true,
            cache:false,
            pagination:true,
            pageSize:10,
            collapsible:true
        });
    });
    $('.calls').click(function () {
        var row = $('#dg').datagrid('getSelected');
        if (row){
            parent.$('#dialog_name').val(row.name);
            parent.$('#dialog_aid').val(row.id);
            parent.layer.closeAll();
        }
    });
    $("form").submit(function (e) {
        e.preventDefault();
        var ajax_data = $(this).serialize();
        var actionurl = $(this).attr("action");
        $.post(actionurl, ajax_data, function(data){
            if(data.status==0){
                $.messager.alert('提示',data.info,'error');
            }else {
                $('#dg').datagrid('loadData', data);
            }
        }, 'json');
    });
</script>