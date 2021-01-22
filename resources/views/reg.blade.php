@extends('pc.layouts.frame')
@section('title', 'luoXun')

@section('content')
<div class="layui-container fly-marginTop">
    <div class="fly-panel fly-panel-user" pad20="">
        <div class="layui-tab layui-tab-brief" lay-filter="user">
            <ul class="layui-tab-title">
                <li><a href="{{ URL::asset('login')}}">登入</a></li>
                <li class="layui-this">注册</li>
            </ul>
            <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form layui-form-pane">
                        <form>
                            <div class="layui-form-item">
                                <label for="L_email" class="layui-form-label">手机</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_phone" name="phone" required="" lay-verify="phone" autocomplete="off" class="layui-input" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_vercode" class="layui-form-label">图形码</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_imagecode" name="imagecode" required="" lay-verify="required" autocomplete="off" class="layui-input" />
                                </div>
                                <div class="layui-form-mid" style="padding: 0!important;">
                                    <button type="button" class="layui-btn layui-btn-normal" id="FLY-getcaptcha">获取图形码</button>
                                </div>
                                <div class="layui-form-mid" style="padding: 0!important; ">
                                    <img src="" style="display: none" id="captcha" class="fly-imagecode" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_vercode" class="layui-form-label">验证码</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_vercode" name="vercode" required="" lay-verify="required" placeholder="请输入手机短信验证码" autocomplete="off" class="layui-input" />
                                </div>
                                <div class="layui-form-mid" style="padding: 0!important;">
                                    <button type="button" class="layui-btn layui-btn-normal" id="FLY-getvercode">获取验证码</button>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_username" class="layui-form-label">昵称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="name" id="L_name"  required="" lay-verify="required" autocomplete="off" class="layui-input" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_pass" class="layui-form-label">密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_password" name="pass" required="" lay-verify="required" autocomplete="off" class="layui-input" />
                                </div>
                                <div class="layui-form-mid layui-word-aux">
                                    6到16个字符
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_repass" class="layui-form-label">确认密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_repassword" name="repass" required="" lay-verify="required" autocomplete="off" class="layui-input" />
                                </div>
                            </div>
                            <div class="layui-form-item" style="position: relative; left: -10px; height: 32px;">
                                <input type="checkbox" id="L_agreement" name="agreement" lay-skin="primary" title="" checked="" />
                                <div class="layui-unselect layui-form-checkbox layui-form-checked" lay-skin="primary">
                                    <i class="layui-icon layui-icon-ok"></i>
                                </div>
                                <a href="/instructions/terms.html" target="_blank" style="position: relative; top: 4px; left: 5px; color: #999;">同意用户服务条款</a>
                            </div>
                            <div class="layui-form-item">
                                <button type="button" class="layui-btn" id="FLY_register">立即注册</button>
                            </div>
{{--                            <div class="layui-form-item fly-form-app">--}}
{{--                                <span>或者直接使用社交账号快捷注册</span>--}}
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
    var captcha_url = "{{ URL::asset('api/v1/captchas') }}";
    var vercode_url = "{{ URL::asset('api/v1/verificationCodes') }}";
    var register_api_url = "{{ URL::asset('api/v1/users') }}";
    var login_url = "{{ URL::asset('login') }}";
    var captcha_key = null;
    var verification_key = null;
    !function(){
        var layer = layui.layer
            ,$ = layui.jquery
            ,form = layui.form;
        $("#FLY-getcaptcha").click(function () {
            var formData = new FormData;
            formData.append("phone",$("#L_phone").val());
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('cBFNRKontent')
                },
                url: captcha_url,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(res){
                    console.log(res);
                    captcha_key = res.captcha_key;
                    $("#captcha").attr("src",res.captcha_image_content);
                    $("#captcha").css("display","block");
                    //document.getElementById("captcha").src = res.captcha_image_content;

                    // if(res.code){
                    //     redirect_success(res.msg, res.url)
                    // }else{
                    //     error(res.msg);
                    // }
                },
                error : function (msg ) {
                    var json=JSON.parse(msg.responseText);
                    $.each(json.errors, function(idx, obj) {
                        layer.msg(obj[0],{icon: 5});
                        return false;
                    });
                },
            })
        });
        $("#FLY-getvercode").click(function () {
            var formData = new FormData;
            formData.append("captcha_key",captcha_key);
            formData.append("captcha_code",$("#L_imagecode").val());
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: vercode_url,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(res){
                    layer.msg("get code success", {
                        icon: 6,//成功的表情
                        time: 1000 //1秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        // location.reload();
                    });
                    console.log(res);
                    verification_key = res.key;
                    var btn = document.getElementsByTagName('button')[1];
                    btn.disabled = true;
                    var time = 60;//定义时间变量。用于倒计时用
                    var timer = null;//定义一个定时器；
                    timer = setInterval(function(){///开启定时器。函数内执行
                        btn.disabled = true;
                        btn.innerText = time+"秒后重新发送";    //点击发生后，按钮的文本内容变成之前定义好的时间值。
                        time--;//时间值自减
                        if(time === 0){     //判断,当时间值小于等于0的时候
                            btn.innerText='重新发送验证码'; //其文本内容变成……点击重新发送……
                            btn.disabled = false;
                            clearInterval(timer); //清除定时器
                        }
                    },1000)
                },
                error : function (msg ) {
                    $('#FLY-getcaptcha').trigger("click");
                    layer.msg(msg.responseJSON.message,{icon: 5});
                },
            })
        });
        $("#FLY_register").click(function () {
            if ($("#L_password").val() !== $("#L_repassword").val()){
                layer.msg("密码不匹配",{icon: 5});
                return false;
            }
            if(!$('#L_agreement').is(':checked')){
                layer.msg("请勾选同意用户服务条款");
                return false;
            }
            $.ajax({
                url: register_api_url,
                type: 'PUT',
                data: {
                    name:$("#L_name").val(),
                    verification_key:verification_key,
                    verification_code:$("#L_vercode").val(),
                    password:$("#L_password").val()
                },
                success: function(res){
                    layer.msg("注册成功", {
                        icon: 6,//成功的表情
                        time: 1000 //1秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        // location.reload();
                        window.location.href = login_url;
                    });
                },
                error : function (msg ) {
                    layer.msg(msg.responseJSON.message,{icon: 5});
                },
            })
        });
    }();
</script>
@stop
