@extends('pc.layouts.frame')
@section('title', 'luoXun')
@section('content')
<div class="layui-container fly-marginTop">
    <div class="fly-panel fly-panel-user" pad20="">
        <div class="layui-tab layui-tab-brief" lay-filter="user">
            <ul class="layui-tab-title">
                <li class="layui-this">登入</li>
                <li><a href="{{ URL::asset("reg")}}">注册</a></li>
            </ul>
            <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form layui-form-pane">
                        <form method="post">
                            <div class="layui-form-item">
                                <input type="hidden" name="secret" value="%E9%98%B2%E6%AD%A2%E5%B9%BF%E5%91%8A%E7%8B%97446886" />
                                <label for="L_email" class="layui-form-label">手机号/账号</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_userame" name="username" required="" lay-verify="required" autocomplete="off" class="layui-input" />
                                </div>
                                <div class="layui-form-mid layui-word-aux">
                                    使用手机号或者账号中的任意一个均可（若采用手机，请确保你的帐号已绑定过该手机）
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_pass" class="layui-form-label">密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_password" name="pass" required="" lay-verify="required" autocomplete="off" class="layui-input" />
                                </div>
                            </div>
{{--                            <div class="layui-form-item">--}}
{{--                                <label for="L_vercode" class="layui-form-label">图形码</label>--}}
{{--                                <div class="layui-input-inline">--}}
{{--                                    <input type="text" id="L_imagecode" name="imagecode" required="" lay-verify="required" autocomplete="off" class="layui-input" />--}}
{{--                                </div>--}}
{{--                                <div class="layui-form-mid" style="padding: 0!important;">--}}
{{--                                    <img src="/auth/imagecode?t=1610566748978" class="fly-imagecode" />--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="layui-form-item">
                                <button class="layui-btn" type="button" id="FLY-login">立即登录</button>
                                <span style="padding-left:20px;"> <a href="http://wpa.qq.com/msgrd?v=3&uin=609961022&site=qq&menu=yes ">忘记密码？</a> </span>
                            </div>
{{--                            <div class="layui-form-item fly-form-app">--}}
{{--                                <span>或者使用社交账号登入</span>--}}
{{--                                <a href="/app/qq" onclick="layer.msg('正在通过QQ登入', {icon:16, shade: 0.1, time:0})" class="iconfont icon-qq" title="QQ登入"></a>--}}
{{--                                <a href="/app/weibo/" onclick="layer.msg('正在通过微博登入', {icon:16, shade: 0.1, time:0})" class="iconfont icon-weibo" title="微博登入"></a>--}}
{{--                            </div>--}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        var login_api_url = "{{ URL::asset('api/v1/authorizations') }}";
        var app_url = "{{ URL::asset('/') }}";
        !function(){
            var layer = layui.layer
                ,$ = layui.jquery
                ,form = layui.form;
            $("#FLY-login").click(function () {
                var formData = new FormData;
                formData.append("username",$("#L_userame").val());
                formData.append("password",$("#L_password").val());
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('cBFNRKontent')
                    },
                    url: login_api_url,
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res){
                        console.log(res);
                        var uid = res.id;
                        localStorage.setItem('Authorization', 'Bearer ' + res.access_token);
                        layer.msg("登录成功", {
                            icon: 6,//成功的表情
                            time: 1000 //1秒关闭（如果不配置，默认是3秒）
                        }, function(){
                            // location.reload();
                            window.location.href = app_url;
                        });
                        //document.getElementById("captcha").src = res.captcha_image_content;
                    },
                    error : function (msg ) {
                        layer.msg(msg.responseJSON.message,{icon: 5});
                    },
                })
            });
        }();
    </script>
@stop
