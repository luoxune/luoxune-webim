<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LuoXun') - 即时通讯 </title>
    <!-- 样式 -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css')}}">
</head>
<script src="{{ URL::asset('layui/layui.all.js')}}"></script>
<body class="layui-layout-body">
<div id="app" class="layui-layout">
    @include('pc.layouts._header')
    @yield('content')
    @include('pc.layouts._footer')
</div>
<script src="{{ URL::asset('layui/layui.all.js')}}"></script>
</body>
</html>
