<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '骆寻 · PC版')</title>
    <!-- 样式 -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css')}}">
    <!-- JS 脚本 -->
{{--    <script src="https://www.jq22.com/jquery/jquery-1.10.2.js"></script>--}}
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/2.2.4/jquery.js"></script>
    <script src="{{ URL::asset('layui/layui.js')}}"></script>
    @yield('links')
</head>
<script>
    var AccessToken = localStorage.getItem('Authorization');
    var Mine = JSON.parse(localStorage.getItem("Mine"));
    var check_token_url = "{{ URL::asset('api/v1/users')}}";
    var login_web_view_url = "{{ URL::asset('login')}}";
    layui.use(['jquery'], function () {
        var $ = layui.jquery;
        $.ajaxSetup({
            headers: {
                'Authorization': AccessToken
            },
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            complete: function complete(xhr) {
                AccessToken = xhr.getResponseHeader("Authorization");
                if (AccessToken) {
                    localStorage.setItem('Authorization', AccessToken);
                    xhr.setRequestHeader('Authorization', AccessToken);
                }
            },
            statusCode: {
                401: function _() {
                    layer.msg('身份认证信息已过期，请重新登录', {
                        icon: 0
                    }, function () {});
                    window.location.href = login_web_view_url;
                },
                504: function _() {
                    layer.msg('数据获取/输入失败，服务器没有响应。504', {
                        icon: 0
                    }, function () {});
                },
                500: function _() {
                    layer.msg('服务器有误', {
                        icon: 0
                    }, function () {});
                }
            }
        }); //check token
        $.ajax({
            url: check_token_url,
            type: 'GET',
            success: function success(e) {
                localStorage.removeItem("Mine");
                localStorage.setItem("Mine", JSON.stringify(e));
                Mine = e;
            },
            error: function error(msg) {
                layer.msg(msg.responseJSON.message, {
                    icon: 5,
                    time: 2000
                }, function () {});
            }
        });
    });
</script>
@yield('content')
