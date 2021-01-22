@extends('pc.layouts.root')
@section('title', '我的资料')
@section('links')
@stop
@section('content')
<body>
<div id="mine_info">
    <div class="layui-container">
        <div class="layui-row">
    <form class="layui-form" action="" lay-filter="formDemo">
        <div class="layui-form-item">
            <label class="layui-form-label">昵称</label>
            <div class="layui-input-block">
                <input type="text" name="username" required  lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">头像</label>
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
        <div class="layui-form-item">
            <label class="layui-form-label">账号</label>
            <div class="layui-input-block">
                <input type="text" name="number" disabled required  lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-inline">
                <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
            </div>
{{--            <div class="layui-form-mid layui-word-aux">辅助文字</div>--}}
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">性别</label>
            <div class="layui-input-block">
                <input type="radio" name="sex" value="男" title="男">
                <input type="radio" name="sex" value="女" title="女" checked>
                <input type="radio" name="sex" value="保密" title="保密" checked>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">QQ</label>
            <div class="layui-input-block">
                <input type="text" name="QQ"   lay-verify="" placeholder="QQ" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">Email</label>
            <div class="layui-input-block">
                <input type="text" name="email"   lay-verify="" placeholder="Email" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">生日</label>
            <div class="layui-input-block">
                <input type="text" name="birth"   lay-verify="" placeholder="生日" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">血型</label>
            <div class="layui-input-block">
                <input type="text" name="blt"   lay-verify="" placeholder="血型" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">个性签名</label>
            <div class="layui-input-block">
                <textarea name="sign" placeholder="个性签名" class="layui-textarea"></textarea>
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
    var user_url = "{{URL::asset('api/v1/users')}}";
    var upload_image_url = "{{URL::asset('api/v1/images')}}";
    var Mine = JSON.parse(localStorage.getItem("Mine"));
    layui.use(['form','element','upload'], function(){
        var form = layui.form,
            element = layui.element
            ,upload = layui.upload;
        $.ajaxSetup({
            headers: {
                'Authorization': localStorage.getItem('Authorization')
            },
        });
        //给表单赋值
        console.log(Mine);
        form.val("formDemo", { //formTest 即 class="layui-form" 所在元素属性 lay-filter="" 对应的值
            "username": Mine.username // "name": "value"
            ,"number": Mine.number
            ,"password": "123456"
            ,"sex": Mine.sex
            ,"sign": Mine.sign
            ,"birth": Mine.birth
            ,"email": Mine.email
            ,"blt": Mine.blt
            ,"QQ": Mine.QQ
            ,"avatar":Mine.avatar
        });
        $("#avatar").attr('src',Mine.avatar);
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
                url: user_url,
                type: 'PATCH',
                data:{
                    "name": data.field.username // "name": "value"
                    ,"number": data.field.number
                    ,"password": data.field.password
                    ,"sex": data.field.sex
                    ,"sign": data.field.sign
                    ,"birth": data.field.birth
                    ,"email": data.field.email
                    ,"blt": data.field.blt
                    ,"QQ": data.field.QQ
                    ,"avatar":data.field.avatar
                },
                success: function(res){
                    localStorage.setItem("Mine", JSON.stringify(res));
                    layer.msg("更新成功", {
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
