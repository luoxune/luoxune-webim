@extends('pc.layouts.root')
@section('title', '好友资料')
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
                            <input type="text" name="username" required disabled  lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input">
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
                        <label class="layui-form-label">性别</label>
                        <div class="layui-input-block">
                            <input type="radio" disabled name="sex" value="男" title="男">
                            <input type="radio" disabled name="sex" value="女" title="女" checked>
                            <input type="radio" disabled name="sex" value="保密" title="保密" checked>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">QQ</label>
                        <div class="layui-input-block">
                            <input type="text" name="QQ"   lay-verify="" disabled placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">Email</label>
                        <div class="layui-input-block">
                            <input type="text" name="email"   lay-verify="" disabled placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">Birth</label>
                        <div class="layui-input-block">
                            <input type="text" name="birth"   lay-verify="" disabled placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">blt</label>
                        <div class="layui-input-block">
                            <input type="text" name="blt"   lay-verify="" disabled placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">个人签名</label>
                        <div class="layui-input-block">
                            <textarea name="sign" placeholder="请输入内容" disabled class="layui-textarea"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </body>
    <script>
        var user_url = "{{URL::asset('api/v1/users/show')}}";
        var Mine = JSON.parse(localStorage.getItem("Mine"));
        layui.use(['form'], function(){
            var form = layui.form;
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
                url: user_url,
                type: 'POST',
                data:{
                  user_id :  param['friend']
                },
                success: function(res){
                    form.val("formDemo", { //formTest 即 class="layui-form" 所在元素属性 lay-filter="" 对应的值
                        "username": res.username // "name": "value"
                        ,"number": res.number
                        ,"sex": res.sex
                        ,"sign": res.sign
                        ,"birth": res.birth
                        ,"email": res.email
                        ,"blt": res.blt
                        ,"QQ": res.QQ
                        ,"avatar":res.avatar
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
