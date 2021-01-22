@extends('pc.layouts.root')
@section('title', '查看群组资料')
@section('links')
@stop
@section('content')
<body>
    <div id="mine_info">
        <div class="layui-container">
            <div class="layui-row">
                <form class="layui-form" action="" lay-filter="formDemo">
                    <div class="layui-form-item">
                        <label class="layui-form-label">群名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" required disabled  lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">群号</label>
                        <div class="layui-input-block">
                            <input type="text" name="number" disabled  lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">创建时间</label>
                        <div class="layui-input-block">
                            <input type="text" name="created_time" disabled  lay-verify="required" placeholder="1998-10-22" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">群头像</label>
                        <div class="layui-input-block">
                            <div class="layui-upload">
                                <div class="layui-upload-list">
                                    <img src="//t.cn/RCzsdCq" id="avatar" class="layui-upload-img">
                                    <p id="demoText"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">群介绍</label>
                        <div class="layui-input-block">
                            <textarea name="sign" disabled placeholder="" class="layui-textarea"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
    var group_url = "{{URL::asset('api/v1/groups')}}";
    var Mine = JSON.parse(localStorage.getItem("Mine"));
    layui.use(['form','element','upload'], function(){
        var form = layui.form
        ,element = layui.element
            ,upload = layui.upload;
        //给表单赋值
        $.ajaxSetup({
            headers: {
                'Authorization': localStorage.getItem('Authorization')
            },
        });
        //开始请求
        var parseQueryString = function() {
            var str = window.location.search;
            var objURL = {};
            str.replace(
                new RegExp( "([^?=&]+)(=([^&]*))?", "g" ),
                function( $0, $1, $2, $3 ){
                    objURL[ $1 ] = $3;
                }
            );
            return objURL;
        };
        var param =  parseQueryString();
        $.ajax({
            url: group_url,
            type: 'POST',
            data:{
                group_id : param['group']
            },
            success: function(res){
                form.val("formDemo", { //formTest 即 class="layui-form" 所在元素属性 lay-filter="" 对应的值
                    "username": res.groupname // "name": "value"
                    ,"number": res.number
                    ,"sign": res.sign
                    ,"avatar":res.avatar
                    ,"created_time":res.created_at
                });
                $("#avatar").attr('src',res.avatar);
            },
            error : function (msg ) {
                layer.msg(msg.responseJSON.message, {
                    icon: 5,
                    time: 2000
                }, function(){
                });
            },
        });
    });
</script>
@stop
