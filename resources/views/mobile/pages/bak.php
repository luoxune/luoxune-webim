@extends('mobile.layouts.root')
@section('links')
@stop
@section('content')
<body>
<script>
    var app_list_url = "{{URL::asset('api/v1/list')}}";
    var upload_image_url = "{{URL::asset('api/v1/images')}}";
    var upload_file_url = "{{URL::asset('api/v1/files')}}";
    layui.use('mobile', function(){
        var mobile = layui.mobile
            ,layim = mobile.layim;
        $.ajaxSetup({
            headers: {
                'Authorization': localStorage.getItem('Authorization')
            },
        });
        $.ajax({
            url: app_list_url,
            type: 'GET',
            success: function(res){
                layim.config({
                    //里面的字段格式 同 上文的 data 中的格式
                    init:res.data
                    //上传图片接口（返回的数据格式见下文），若不开启图片上传，剔除该项即可
                    ,uploadImage: {
                        url: upload_image_url //接口地址
                        ,type: 'post' //默认post
                    }

                    //上传文件接口（返回的数据格式见下文），若不开启文件上传，剔除该项即可
                    ,uploadFile: {
                        url: upload_file_url //接口地址
                        ,type: 'post' //默认post
                    }
                    //扩展工具栏，下文会做进一步介绍（如果无需扩展，剔除该项即可）
                    ,tool: [{
                        alias: 'code' //工具别名
                        ,title: '代码' //工具名称
                        ,icon: '&#xe64e;' //工具图标，参考图标文档
                    }]

                    //扩展“更多”的自定义列表，下文会做进一步介绍（如果无需扩展，剔除该项即可）
                    ,moreList: [{
                        //可同时配置多个
                        moreList: [{
                            alias: 'find'
                            ,title: '发现'
                            ,iconUnicode: '&#xe628;' //图标字体的unicode，可不填
                            ,iconClass: '' //图标字体的class类名
                        },{
                            alias: 'share'
                            ,title: '分享'
                            ,iconUnicode: '' //图标字体的unicode，可不填
                            ,iconClass: '' //图标字体的class类名
                        }]
                    }],
                    brief:false,
                    title:'骆寻',
                    chatTitleColor:'#36373C',
                    isNewFriend:true,
                    tabIndex:0,
                    isgroup:false,
                    notice:false,
                    voice:"default.wav",
                    maxLength:3000,
                    copyright:false,
                });
            },
            error : function (msg ) {
                layer.msg(msg.responseJSON.message,{icon: 5});
            },
        })

    });
</script>
</body>
@stop
