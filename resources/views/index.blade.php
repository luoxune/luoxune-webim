<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <title>确定客户端</title>
    <script src="{{ URL::asset('layui/layui.all.js')}}"></script>
</head>
<body>
<script>
    var mobile_app_url = "{{ URL::asset('mobile/v1/app')}}" ;
    var pc_app_url ="{{ URL::asset('pc/v1/app')}}" ;
    var device = layui.device();
    console.log(device);
    if(device.mobile){
        //询问框
        layer.confirm('检测到您目前使用的不是PC设备，是否跳转到手机客户端？', {
            btn: ['是','否'] //按钮
        }, function(){
            window.location.href = mobile_app_url;
        }, function(){
            window.location.href = pc_app_url;
        });
    }else{
        window.location.href = pc_app_url;
    }
</script>
</body>
</html>
