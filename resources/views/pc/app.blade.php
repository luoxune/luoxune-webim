@extends('pc.layouts.root')
@section('links')
    <link href="{{ URL::asset('plugins/NZPlugin/CSS/displaystyle.css') }}" rel="stylesheet"   />
    <script src="{{ URL::asset('plugins/NZPlugin/JS/syntaxhighlighter_3.0.83/shCore.js') }}"></script>
    <script src="{{ URL::asset('plugins/NZPlugin/JS/syntaxhighlighter_3.0.83/shBrushJScript.js') }}"></script>
    <link href="{{URL::asset('plugins/NZPlugin/JS/syntaxhighlighter_3.0.83/styles/shCoreRDark.css') }}" rel="stylesheet" />
    <script src="{{ URL::asset('plugins/NZPlugin/JS/NZ-Plugin/JS/NZ-Menu.min.js') }}"></script>
    <link href="{{ URL::asset('plugins/NZPlugin/JS/NZ-Plugin/CSS/NZ-Menu.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/5.15.2/css/all.min.css">
@stop
@section('content')
<style>
    .NZ-Menu{
        z-index: 19981022!important;
    }
</style>
<body>
<div id="app">
</div>
</body>
<script>
    var domain = 'im.luoxune.com/wss';
    var login_web_view_url = "{{ URL::asset('login') }}";
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

    var current_window = {
        data:{
            id:0
        }
    };
    var Mine = JSON.parse(localStorage.getItem("Mine"));

    var friendID = null ;//用来存放要右键菜单操作的元素ID(朋友);
    var groupName = null;//用来存放要右键菜单操作的元素ID(分组);
    var chatGroupID = null;//用来存放要右键菜单操作的元素ID(群组);
    layui.use('layim', function(layim){
        $.ajaxSetup({
            headers: {
                'Authorization': localStorage.getItem('Authorization')
            },
        });
        layim.config({
            init: {
                url: app_list_url
                ,type: 'get'
                ,data: {}
            }
            //获取群员接口（返回的数据格式见下文）
            ,members: {
                url: members_url //接口地址（返回的数据格式见下文）
                ,type: 'post' //默认get，一般可不填
                ,data: {} //额外参数
            }

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
            ,isAudio: true //开启聊天工具栏音频
            ,isVideo: true //开启聊天工具栏视频
            //,brief: true //是否简约模式（若开启则不显示主面板）
            //,title: 'WebIM' //自定义主面板最小化时的标题
            //,right: '100px' //主面板相对浏览器右侧距离
            //,minRight: '90px' //聊天面板最小化时相对浏览器右侧距离
            //,skin: ['aaa.jpg'] //新增皮肤
            //,isfriend: false //是否开启好友
            //,isgroup: false //是否开启群组
            //,min: true //是否始终最小化主面板，默认false
            //,notice: true //是否开启桌面消息提醒，默认false
            //,voice: false //声音提醒，默认开启，声音文件为：default.mp3
            ,initSkin: '3.jpg'
            ,msgbox: msgbox_view_url //消息盒子页面地址，若不开启，剔除该项即可
            ,find: find_user_url //发现页面地址，若不开启，剔除该项即可
            ,chatLog: chatlogs_view_url //聊天记录页面地址，若不开启，剔除该项即可
        });
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
                case 'notice' :
                    console.log(data.content);
                    break;
                case 'message' :
                    console.log(data.data);
                    Lmsgbox(data.data);
                    layim.msgbox(data.data);
                    break;
                case 'addFriend':
                    console.log("增加一个新朋友！");
                    layim.addList(data.data);
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
                // 添加 分组信息
                case 'addGroup':
                    console.log(data.data);
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
                //从主面板移除群组
                case 'delGroup':
                    layim.removeList({
                        type: 'group',
                        id: message.id //好友或者群组ID
                    });
                    console.log('该群组被解散：'+message.id);
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
        //修改个性签名
        layim.on('sign', function(value){
            $.ajax({
                // headers: {'Authorization': AccessToken},
                url: change_sign_url,
                type: 'PATCH',
                data: {
                    'sign' : value
                },
                success: function(res){
                    layer.msg("已修改签名");
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
        //在线状态切换
        layim.on('online', function(status){
            $.ajax({
                url: app_run_url,
                type: 'POST',
                data: {
                    type: 'online',
                    status: status,
                },
                success: function(res){

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
        //监听自定义工具栏点击，以添加代码为例
        layim.on('tool(code)', function(insert, send, obj){ //事件中的tool为固定字符，而code则为过滤器，对应的是工具别名（alias）
            layer.prompt({
                title: '插入代码'
                ,formType: 2
                ,shade: 0
            }, function(text, index){
                layer.close(index);
                insert('[pre class=layui-code]' + text + '[/pre]'); //将内容插入到编辑器，主要由insert完成
                //send(); //自动发送
            });
            console.log(this); //获取当前工具的DOM对象
            console.log(obj); //获得当前会话窗口的DOM对象、基础信息
        });
        function Lmsgbox(number){
            var content = $('.layim-tool-msgbox .layui-anim').html();
            console.log(content);
            if (content){
                $('.layim-tool-msgbox .layui-anim').show().html(parseInt(content)+parseInt(number));
            }else{
                $('.layim-tool-msgbox .layui-anim').show().html(number);
            }
        }
        layim.on('ready', function(res){
            document.oncontextmenu = function() {
                return false;
            }
            $(".layui-layim-user").click(function(){
                layer.open({
                    type: 2,
                    title: '修改个人资料',
                    maxmin: true,//开启最大化最小化按钮
                    area: ['500px', '700px'],
                    content: mine_info_view_url
                });
            });
            $('.layim-tool-msgbox').click(function() {
                $('.layim-tool-msgbox .layui-anim').hide()
            });
            //查询有无新消息
            $.post(msg_noread_url, function (e) {
                console.log(e);
                if (e > 0) {
                    // layim.msgbox(e);
                    Lmsgbox(e);
                }
            });
            $(".layim-tool-find").removeAttr('layim-event').click(function (){
                var c_index = layer.confirm('您是想查找好友还是群组？', {
                    btn: ['查找好友','查找群组'] //按钮
                }, function(){
                    layer.close(c_index);
                    var p_index = layer.prompt({title: '请输入手机号或者账号', formType: 3}, function(pass, index){
                        //console.log(pass);
                            $.ajax({
                                url: find_user_url,
                                type: 'POST',
                                data: {
                                    number : pass,
                                },
                                success: function(res){
                                    console.log(res);
                                    layer.close(p_index);
                                    layim.add({
                                        type: 'friend' //friend：申请加好友、group：申请加群
                                        ,username: res.username //好友昵称，若申请加群，参数为：groupname
                                        ,avatar: res.avatar //头像
                                        ,submit: function(group, remark, index){ //一般在此执行Ajax和WS，以通知对方
                                            $.ajax({
                                                url: friend_url,
                                                type: 'POST',
                                                data: {
                                                    uid: res.id,
                                                    divide: group,
                                                    remark:remark,
                                                },
                                                success: function(res){
                                                    console.log(res);
                                                    layer.msg(res.msg);
                                                    layer.close(index); //关闭改面板
                                                },
                                                error : function (msg ) {
                                                    layer.msg(msg.responseJSON.message);
                                                    layer.close(index); //关闭改面板
                                                },
                                            });
                                        }
                                    });
                                },
                                error : function (msg ) {
                                    layer.msg(msg.responseJSON.message);
                                },
                            });

                    });
                }, function(){
                    layer.close(c_index);
                    var p_index = layer.prompt({title: '请输入群号', formType:  3}, function(pass, index){
                        console.log(pass);
                        $.ajax({
                            url: find_group_url,
                            type: 'post',
                            data: {
                                group_number: pass,
                            },
                            success: function(res){
                                console.log(res);
                                layer.close(p_index);
                                var group_id = res.id;
                                layim.add({
                                    type: 'group' //friend：申请加好友、group：申请加群
                                    ,groupname: res.groupname //好友昵称，若申请加群，参数为：groupname
                                    ,avatar: res.avatar //头像
                                    ,submit: function(group, remark, index){ //一般在此执行Ajax和WS，以通知对方
                                        console.log(group);
                                        $.ajax({
                                            url: group_add_url,
                                            type: 'post',
                                            data: {
                                                group_id: group_id,
                                                remark:remark,
                                            },
                                            success: function(res){
                                                console.log(res);
                                                layer.msg(res.msg);
                                                layer.close(index); //关闭改面板
                                            },
                                            error : function (msg ) {
                                                layer.msg(msg.responseJSON.message);
                                                layer.close(index); //关闭改面板
                                            },
                                        });
                                    }
                                });
                            },
                            error : function (msg ) {
                                layer.msg(msg.responseJSON.message);
                            },
                        });

                    });
                });

            });
            $(".layim-tab-content").on("click", ".layui-layim-list", function(e) {
                $(e.target).siblings(".layim-msg-status").hide();
                $(e.target).children(".layim-msg-status").hide();
            });
            $("body").on("click", ".layui-layer-close", function(e) {
                current_window.data.id = 0;
            });
            SyntaxHighlighter.all();

            function locateFriend() {
                $("body").on("mousedown", this, function(e) {
                    console.log(e.which);
                    if(3 == e.which){
                        if(e.target.offsetParent.className == 'layui-layim-main'){
                            friendID = e.target.className.split(" ",1)[0].substring(12);
                            //console.log('friendid');
                            //console.log(friendID);
                        }
                        else{
                            friendID = e.target.parentElement.className.split(" ",1)[0].substring(12);
                            //console.log(friendID);
                        }
                    }
                });
            }
            function locateGroup() {
                $("body").on("mousedown", this, function(e) {
                    console.log(e.target);
                    if(3 == e.which){
                        if(e.target.offsetParent.className == 'layui-layim-main'){
                            console.log(e.target)
                            if($(e.target).attr('data-type') == 'friend'){
                                friendID = e.target.className.split(" ",1)[0].substring(12);
                            }else{
                                chatGroupID = e.target.className.split(" ",1)[0].substring(11);
                                //console.log(chatGroupID);
                            }
                        }
                        else{
                            console.log($(e.target.offsetParent).attr('data-type'))
                            if($(e.target.offsetParent).attr('data-type') == 'friend'){
                                friendID = e.target.parentElement.className.split(" ",1)[0].substring(12);
                            }else{
                                chatGroupID = e.target.parentElement.className.split(" ",1)[0].substring(11);
                                //console.log(chatGroupID);
                            }
                        }
                    }
                });
            }
            $("li[data-type$='friend']").NZ_Menu({
                items: [{
                    name: "查看资料",
                    icon: "fa-address-card",
                    event: function (){
                        layer.open({
                            type: 2,
                            title: '个人资料',
                            maxmin: true,//开启最大化最小化按钮
                            area: ['500px', '700px'],
                            content: friend_info_view_url+"?friend="+friendID
                        });
                    }
                }, {
                    name: "删除好友",
                    icon: "fa-trash",
                    event: function () {
                        layer.confirm('确定删除该好友？', {
                            btn: ['确定', '取消'],
                            title: '友情提示',
                            closeBtn: 0,
                            icon: 3
                        }, function(index){
                            $.ajax({
                                // headers: {'Authorization': AccessToken},
                                url: friend_url,
                                type: 'DELETE',
                                data: {
                                    friend: friendID
                                },
                                success: function(res){
                                    layer.msg(res.msg);
                                    layim.removeList({
                                        type: 'friend'
                                        , id: friendID
                                    });
                                    $.ajax({
                                        url: app_run_url,
                                        type: 'POST',
                                        data: {
                                            type: 'delFriend',
                                            to_id : friendID
                                        },
                                        success: function(res){
                                            console.log(res.msg);
                                            layer.msg(res.msg);
                                            layer.close(index);
                                        },
                                        error : function (msg ) {
                                            layer.msg(msg.responseJSON.message, {
                                                icon: 5,
                                                time: 2000
                                            }, function(){});
                                        },
                                    });
                                },
                                error : function (msg ) {
                                    layer.msg(msg.responseJSON.message, {
                                        icon: 5,
                                        time: 2000
                                    }, function(res){
                                    });
                                },
                            });
                        });
                    }
                }, {
                    name: "移动好友"
                    , icon: "fa-share"
                    , event: function () {
                        layer.prompt({title: '请输入分组名称', formType:  3}, function(pass, index){
                            console.log(pass);
                            $.ajax({
                                url: friend_move_by_name_url,
                                type: 'POST',
                                data: {
                                    divide: pass,
                                    friend: friendID
                                },
                                success: function(res){
                                    console.log(res);
                                    layer.msg(res.msg);
                                    layer.close(index);
                                },
                                error : function (msg ) {
                                    layer.msg(msg.responseJSON.message);
                                },
                            });

                        });
                    }
                    //blade
                    {{--, menu: [--}}
                    {{--        @foreach($divides as $key => $v)--}}
                    {{--            {--}}
                    {{--                name: "{{$v->name}}",--}}
                    {{--                event: function () {--}}
                    {{--                   moveFriend(friendID,{{$v->id}});--}}
                    {{--                }--}}
                    {{--            },--}}
                    {{--        @endforeach--}}
                    {{--]--}}
                }]
                ,showbefore: locateFriend
            });
            function moveFriend(friend,divide){
                $.ajax({
                    url: friend_move_url,
                    type: 'POST',
                    data: {
                        friend: friend,
                        divide:divide
                    },
                    success: function(res){
                        layer.msg(res.msg);
                    },
                    error : function (msg ) {
                        layer.msg(msg.responseJSON.message, {
                            icon: 5,
                            time: 2000
                        }, function(){});
                    },
                });
                console.log(friend+group);
            }
            $("li[lay-type$='group']").NZ_Menu({
                items: [
                    {
                        name: "创建群组",
                        icon: "fa-plus",
                        event: function () {
                            layer.open({
                                type: 2,
                                title: '创建群组',
                                maxmin: true,//开启最大化最小化按钮
                                area: ['500px', '700px'],
                                content: group_create_view_url
                            });
                        }
                    },
                ]
            });
            $("li[data-type$='group']").NZ_Menu({
                items: [
                    {
                        name: "创建群组",
                        icon: "fa-plus",
                        event: function () {
                            layer.open({
                                type: 2,
                                title: '创建群组',
                                maxmin: true,//开启最大化最小化按钮
                                area: ['500px', '700px'],
                                content: group_create_view_url
                            });
                        }
                    },
                    {
                    name: "查看群资料",
                        icon: "fa-users",
                        event: function () {
                        layer.open({
                            type: 2,
                            title: '查看群资料',
                            maxmin: true,//开启最大化最小化按钮
                            area: ['500px', '700px'],
                            content: group_info_view_url+'?group='+chatGroupID
                        });
                    }
                },
                    {
                        name: "编辑群资料",
                        icon: "fa-edit",
                        event: function () {
                            layer.open({
                                type: 2,
                                title: '编辑群资料',
                                maxmin: true,//开启最大化最小化按钮
                                area: ['500px', '700px'],
                                content: group_edit_view_url + '?group='+chatGroupID
                            });
                        }
                    },
                    {
                    name: "退出群组",
                        icon: "fa-trash",
                        event: function () {
                        layer.confirm('确定退出群组？', {
                            btn: ['确定', '取消'],
                            title: '友情提示',
                            closeBtn: 0,
                            icon: 3
                        }, function(index){
                            $.ajax({
                                // headers: {'Authorization': AccessToken},
                                url: group_url,
                                type: 'DELETE',
                                data: {
                                    group_id: chatGroupID
                                },
                                success: function(res){
                                    layer.msg(res.msg);
                                    layim.removeList({
                                        type: 'group'
                                        , id: chatGroupID
                                    });
                                },
                                error : function (msg ) {
                                    layer.msg(msg.responseJSON.message, {
                                        icon: 5,
                                        time: 2000
                                    }, function(res){
                                    });
                                },
                            });
                        });
                    }
                }, {
                    name: "解散群组"
                    , icon: "fa-times"
                    , event: function () {
                        layer.prompt({title: '请输入群组名称,以解散该群', formType:  3}, function(pass, index){
                            console.log(pass);
                            $.ajax({
                                url: group_destroy_url,
                                type: 'POST',
                                data: {
                                    groupname: pass,
                                    group_id: chatGroupID
                                },
                                success: function(res){
                                    console.log(res);
                                    layer.msg(res.msg);
                                    layer.close(index);
                                },
                                error : function (msg ) {
                                    layer.msg(msg.responseJSON.message);
                                },
                            });

                        });
                    }
                    //blade
                    {{--, menu: [--}}
                    {{--        @foreach($divides as $key => $v)--}}
                    {{--            {--}}
                    {{--                name: "{{$v->name}}",--}}
                    {{--                event: function () {--}}
                    {{--                   moveFriend(friendID,{{$v->id}});--}}
                    {{--                }--}}
                    {{--            },--}}
                    {{--        @endforeach--}}
                    {{--]--}}
                }
                ],showbefore: locateGroup()
            });
            function logout(){
                localStorage.removeItem('Authorization');
                location.reload();
            }
            $('.layui-layim-info').NZ_Menu({
                items: [{
                    name: "退出登录",
                    icon: "fa-paper-plane",
                    event: logout
                }]
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
    });
</script>
</html>
@stop
