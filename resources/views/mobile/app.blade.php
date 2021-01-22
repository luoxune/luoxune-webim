@extends('mobile.layouts.root')
@section('content')
<script>
    var domain = 'im.luoxune.com/wss';
    var mine_info_view_url = "{{URL::asset('pc/v1/users')}}";
    var friend_info_view_url =  "{{URL::asset('pc/v1/friends')}}";
    var group_info_view_url = "{{URL::asset('pc/v1/groups')}}";
    var group_create_view_url = "{{URL::asset('pc/v1/groups/create')}}";
    var group_edit_view_url = "{{URL::asset('pc/v1/groups/edit')}}";
    var msgbox_view_url = "{{URL::asset('pc/v1/msgbox')}}";

    var chatlogs_view_url = "{{URL::asset('pc/v1/chatlogs')}}";

    var app_run_url = "{{URL::asset('api/v1/run')}}";
    var app_list_url = "{{URL::asset('api/v1/list')}}";
    var change_sign_url = "{{URL::asset('api/v1/sign')}}";
    var upload_image_url = "{{URL::asset('api/v1/images')}}";
    var upload_file_url = "{{URL::asset('api/v1/files')}}";
    var msg_noread_url = "{{URL::asset('api/v1/msgbox/noread')}}";
    var find_user_url = "{{URL::asset('api/v1/users/find')}}";
    var find_group_url = "{{URL::asset('api/v1/groups/find')}}";


    var friend_url =  "{{URL::asset('api/v1/friends')}}";
    var friend_delete_url =  "{{URL::asset('api/v1/friend/delete')}}";
    var friend_move_url =  "{{URL::asset('api/v1/friend/move')}}";
    var friend_move_by_name_url =  "{{URL::asset('api/v1/friend/move/name')}}";

    var group_url =  "{{URL::asset('api/v1/groups')}}";
    var group_destroy_url = "{{URL::asset('api/v1/groups/destroy')}}";
    var group_add_url = "{{URL::asset('api/v1/groups/add')}}";
    var members_url = "{{URL::asset('api/v1/members')}}";

    var AccessToken = localStorage.getItem('Authorization');
    var check_token_url = "{{URL::asset('api/v1/users')}}";
    var login_web_view_url = "{{URL::asset('login')}}";

    var current_window = {
        data:{
            id:0
        }
    };
    layui.use(['mobile'], function(){
        var mobile = layui.mobile
            ,layim = mobile.layim
        var $ = layui.zepto;
        var layer = layui['layer-mobile'];
        // $(document).on('ajaxBeforeSend', function(e, xhr, options){
        //     xhr.setRequestHeader('Authorization', localStorage.getItem('Authorization'));
        // })
        $.ajaxSettings = $.extend($.ajaxSettings, {
            beforeSend: function (xhr, options){
                xhr.setRequestHeader('Authorization', localStorage.getItem('Authorization'));
            },
            complete:function (xhr, options){
                console.log(xhr.status);
                AccessToken = xhr.getResponseHeader("Authorization");
                if (AccessToken) {
                    localStorage.setItem('Authorization', AccessToken);
                    xhr.setRequestHeader('Authorization', AccessToken);
                }
            },
            error: function (xhr, errorType, error){
                switch (xhr.status){
                    case 401:
                        layer.msg('身份认证信息已过期，请重新登录', {
                            icon: 0
                        }, function () {});
                        window.location.href = login_web_view_url;
                        break;
                    case   504:
                        layer.msg('数据获取/输入失败，服务器没有响应。504', {
                            icon: 0
                        }, function () {});
                        break;
                    case   500:
                        layer.msg('服务器有误', {
                            icon: 0
                        }, function () {});
                        break;
                    default:break;
                }
            }
        });
        $.ajax({
            url: check_token_url,
            type: 'GET',
            success: function success(e) {
                localStorage.removeItem("Mine");
                localStorage.setItem("Mine", JSON.stringify(e));
                Mine = e;
            },
            error: function error(msg) {
                if(msg.status == 401){
                    window.location.href = login_web_view_url;
                }
                layer.msg(msg.responseJSON.message, {
                    icon: 5,
                    time: 2000
                }, function () {});
            }
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
                    //扩展“更多”的自定义列表，下文会做进一步介绍（如果无需扩展，剔除该项即可）
                    ,moreList: [{
                        alias: 'find'
                        ,title: '发现'
                        ,iconUnicode: '&#xe628;' //图标字体的unicode，可不填
                        ,iconClass: '' //图标字体的class类名
                    },{
                        alias: 'logout'
                        ,title: '退出登录'
                        ,iconUnicode: '	&#x2718;' //图标字体的unicode，可不填
                        ,iconClass: '' //图标字体的class类名
                    }
                    ],
                    //可同时配置多个
                    tool: [{
                        alias: 'code' //工具别名
                        ,title: '代码' //工具名称
                        ,iconUnicode: '&#xe64e;' //图标字体的unicode，可不填
                        ,iconClass: '' //图标字体的class类名
                    }],

                    brief:false,
                    title:'骆寻IM',
                    chatTitleColor:'#36373C',
                    isNewFriend:true,
                    tabIndex:0,
                    isgroup:false,
                    notice:false,
                    voice:"default.mp3",
                    maxLength:3000,
                    copyright:false,
                });
            },
            error : function (msg ) {
                layer.msg(msg.responseJSON.message,{icon: 5});
            },
        })
        socket = new WebSocket('wss://'+domain);
        socket.onopen = function(){
            console.log("websocket握手成功!");
        };
        //监听收到的消息
        socket.onmessage = function(e) {
            var data = JSON.parse(e.data),
                type = data.type || '',
                message = data.data || '';
            switch (type) {
                case 'init':
                    var client_id = data.client_id || '';
                    $.ajax({
                        url: app_run_url,
                        type: 'POST',
                        data: {
                            'type': 'init',
                            'client_id': client_id
                        },
                        success: function(res){
                            console.log('init succeed')
                        },
                        error : function (msg ) {
                            layer.msg(msg.responseJSON.message,{icon: 5});
                        },
                    })
                    break;
                case 'online':
                    console.log(data.id+"已上线");
                    layim.setFriendStatus(data.id, 'online');
                    break;
                case 'offline':
                    console.log(data.id+"已下线");
                    layim.setFriendStatus(data.id, 'offline');
                    break;
                case 'hide':
                    console.log(data.id+"已隐身");
                    layim.setFriendStatus(data.id, 'hide');
                    break;
                case 'chatMessage':
                    if(message.from_id ===  Mine.id){
                        break;
                    }
                    if(message.type === 'friend' && message.from_id !== current_window.data.id){
                        $('body .layim-friend'+message.id+' .layim-msg-status').show();
                    }
                    if(message.type === 'group' && message.from_id !== current_window.data.id){
                        $('body .layim-group'+message.id+' .layim-msg-status').show();
                    }
                    console.log('这是一个在线消息！');
                    //layim.setChatStatus('<span style="color:#FF5722;">在线</span>');
                    layim.getMessage(data.data);
                    break;
                case 'addFriend':
                    console.log("增加一个新朋友！");
                    layim.addList(data.data);
                    break;
                //从主面板移除好友
                case 'delFriend':
                    layim.removeList({
                        type: 'friend',
                        id: message.id //好友或者群组ID
                    });
                    console.log('已删除好友'+message.id);
                    break;
                // 添加 分组信息
                case 'addGroup':
                    console.log(data.data);
                    layim.addList(data.data);
                    break;
                //从主面板移除群组
                case 'delGroup':
                    layim.removeList({
                        type: 'group',
                        id: message.id //好友或者群组ID
                    });
                    console.log('该群组被解散：'+message.id);
                    break;
                case 'notice' :
                    console.log(data.content);
                    break;
                case 'message' :
                    console.log(data.data);
                    // Lmsgbox(data.data);
                    layim.msgbox(data.data);
                    break;
            }
        };
        layim.on('sendMessage', function(res){
            //console.log(res);
            // if(res.to.type === 'friend'){
            //     layim.setChatStatus('<span style="color:#333333;">对方正在输入...</span>');
            // }
            // 发送消息
            var mine = JSON.stringify(res.mine);
            var to = JSON.stringify(res.to);
            //var login_data = '{"data":{"mine":'+mine+', "to":'+to+'}}';
            //console.log(login_data);
            $.ajax({
                url: app_run_url,
                type: 'POST',
                data: {
                    type: 'chatMessage',
                    mine: mine,
                    to : to
                },
                success: function(res){
                    //console.log(res);

                },
                error : function (msg ) {
                    //console.log(msg);
                    layer.msg(msg.responseJSON.message, {
                        icon: 0,
                    }, function(){

                    });
                },
            })
        });
        layim.on('ready', function(res){
            $("li[layim-event$='about']").hide();
            $(".layim-tab-content").on("click", ".layui-layim-list", function(e) {
                $(e.target).siblings(".layim-msg-status").hide();
                $(e.target).children(".layim-msg-status").hide();
            });
            $("body").on("click", ".layui-layer-close", function(e) {
                current_window.data.id = 0;
            });
        });
        layim.on('chatChange', function(res){
            current_window = res;
            var type = res.data.type;
            if(type === 'friend'){
                $('body .layim-friend'+res.data.id+' .layim-msg-status').hide();
                //layim.setChatStatus('<span style="color:#FF5722;">在线</span>'); //模拟标注好友在线状态
            } else if(type === 'group'){
                //模拟系统消息
                // layim.getMessage({
                //     system: true //系统消息
                //     ,id: 111111111
                //     ,type: "group"
                //     ,content: '贤心加入群聊'
                // });
            }
        });
        //监听点击更多列表
        layim.on('moreList', function(obj){
            switch(obj.alias){ //alias即为上述配置对应的alias
                case 'find': //发现
                    layer.msg('自定义发现动作');
                    break;
                case 'logout': //发现
                    localStorage.removeItem('Authorization');
                    location.reload();
                    break;
                case 'share':
                    layim.panel({
                        title: 'share' //分享
                        ,tpl: '<div style="padding: 10px;">自定义模版，@{{d.data.test}}</div>' //模版
                        ,data: { //数据
                            test: '123'
                        }
                    });
                    break;
            }
        });
        //监听自定义工具栏点击，以上述扩展的工具为例
        layim.on('tool(code)', function(insert, send, obj){ //事件中的tool为固定字符，而code则为过滤器，对应的是工具别名（alias）
            layer.prompt({
                title: '插入代码'
                ,formType: 2
                ,shade: 0
            }, function(text, index){
                layer.close(index);
                insert(text); //将内容插入到编辑器，主要由insert完成
                //send(); //自动发送
                console.log(this); //获取当前工具的DOM对象
                console.log(obj); //获得当前会话窗口的DOM对象、基础信息
            });
        });
        //监听点击“新的朋友”
        layim.on('newFriend', function(){
            //弹出面板
            layim.panel({
                title: '新的朋友' //标题
                ,tpl: '<div style="padding: 10px;">开发中..，@{{d.data.test}}</div>' //模版，基于laytpl语法
                ,data: { //数据
                    test: '么么哒'
                }
            });

            //也可以直接跳转页面，如：
            //location.href = './newfriend'
        });
    });
</script>
@stop
