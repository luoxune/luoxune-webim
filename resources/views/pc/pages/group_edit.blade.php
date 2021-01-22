@extends('pc.layouts.root')
@section('title', '编辑群组资料')
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
                            <input type="text" name="groupname" required  lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">群号</label>
                        <div class="layui-input-block">
                            <input type="text" name="number" disabled lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">创建时间</label>
                        <div class="layui-input-block">
                            <input type="text" name="created_at" disabled  lay-verify="required" placeholder="1998-10-22" autocomplete="off" class="layui-input">
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
                                <input type="hidden" id="avatar_input" name="avatar" value="//t.cn/RCzsdCq"   lay-verify="" placeholder="请输入昵称" autocomplete="off" class="layui-input">
                                <div class="layui-upload-list">
                                    <div class="layui-progress layui-progress-big" lay-showpercent="true" lay-filter="demo">
                                        <div class="layui-progress-bar layui-bg-green" lay-percent="0%"></div>
                                    </div>
                                </div>
                                <button type="button" class="layui-btn" id="test">
                                    <i class="layui-icon">&#xe67c;</i>上传图片
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">群介绍</label>
                        <div class="layui-input-block">
                            <textarea name="sign" placeholder="请输入内容" class="layui-textarea"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-filter="formDemo" lay-submit>更新信息</button>
                            {{--                <button class="layui-btn" layui-btn-primary onclick="parent.layer.close(layer.index);" >关闭页面</button>--}}
                            {{--                <button type="reset" class="layui-btn layui-btn-primary">重置</button>--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
    var group_url = "{{URL::asset('api/v1/groups')}}";
    var upload_image_url = "{{URL::asset('api/v1/images')}}";
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
                    "groupname": res.groupname // "name": "value"
                    ,"number": res.number
                    ,"sign": res.sign
                    ,"avatar":res.avatar
                    ,"created_at":res.created_at
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
        //执行实例
        var uploadInst = upload.render({
            elem: '#test' //绑定元素
            ,url: upload_image_url //上传接口
            ,data: {
                type:'avatar'
            }
            ,progress: function(n, elem){
                var percent = n + '%' //获取进度百分比
                element.progress('demo', percent); //可配合 layui 进度条元素使用

                //以下系 layui 2.5.6 新增
                console.log(elem); //得到当前触发的元素 DOM 对象。可通过该元素定义的属性值匹配到对应的进度条。
            }
            ,done: function(res){
                layer.msg(res.msg);
                element.progress('demo', '100%')
                $("#avatar").attr('src',res.data.src);
                $("#avatar_input").val(res.data.src);
                //上传完毕回调
            }
            ,error: function(){
                //请求异常回调
            }
        });
        //监听提交
        form.on('submit(formDemo)', function(data){
            console.log(data.field.username);
            $.ajax({
                url: group_url,
                type: 'PATCH',
                data:{
                    "group_id" : param['group'],
                    "groupname": data.field.groupname // "name": "value"
                    ,"sign": data.field.sign
                    ,"avatar": data.field.avatar
                },
                success: function(res){
                    layer.msg(res.msg, {
                        icon: 6,
                        time: 2000
                    }, function(){
                        parent.layer.close(parent.layer.index);
                    });
                },
                error : function (msg ) {
                    layer.msg(msg.responseJSON.message, {
                        icon: 5,
                        time: 2000
                    }, function(){

                    });
                },
            });
            return false;
        });
    });
</script>
@stop
