<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>骆寻 · 移动版</title>
    <meta content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta name="apple-mobile-web-app-capable" content="no" />
    <meta name="format-detection" content="telephone=no,email=no,adress=no"/>
    <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.mobile.css')}}" media="all">
    <script src="https://www.jq22.com/jquery/jquery-1.10.2.js"></script>
    <script src="{{ URL::asset('layui/layui.js')}}"></script>
    @yield('links')
</head>
<body>

@yield('content')
</body>
</html>
