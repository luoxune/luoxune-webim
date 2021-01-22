@extends('pc.layouts.root')
@section('title', '消息盒子')
@section('links')
@stop
@section('content')
    <style>
        .layim-msgbox{margin: 15px;}
        .layim-msgbox li{position: relative; margin-bottom: 10px; padding: 0 130px 10px 60px; padding-bottom: 10px; line-height: 22px; border-bottom: 1px dotted #e2e2e2;}
        .layim-msgbox .layim-msgbox-tips{margin: 0; padding: 10px 0; border: none; text-align: center; color: #999;}
        .layim-msgbox .layim-msgbox-system{padding: 0 10px 10px 10px;}
        .layim-msgbox li p span{padding-left: 5px; color: #999;}
        .layim-msgbox li p em{font-style: normal; color: #FF5722;}

        .layim-msgbox-avatar{position: absolute; left: 0; top: 0; width: 50px; height: 50px;}
        .layim-msgbox-user{padding-top: 5px;}
        .layim-msgbox-content{margin-top: 3px;}
        .layim-msgbox .layui-btn-small{padding: 0 15px; margin-left: 5px;}
        .layim-msgbox-btn{position: absolute; right: 0; top: 12px; color: #999;}
    </style>
<body>

<ul class="layim-msgbox" id="LAY_view"></ul>

@verbatim
<textarea title="消息模版" id="LAY_tpl" style="display:none;">

{{# layui.each(d.data, function(index, item){
  if(item.from){ }}
    <li data-uid="{{ item.from }}" data-fromGroup="{{ item.from_group }}" data-type="{{ item.type }}">
      <a href="/u/{{ item.from }}/" target="_blank">
        <img src="{{ item.user.avatar }}" class="layui-circle layim-msgbox-avatar">
      </a>
      <p class="layim-msgbox-user">
        <a href="/u/{{ item.from }}/" target="_blank">{{ item.user.username||'' }}</a>
        <span>{{ item.time }}</span>
      </p>
      <p class="layim-msgbox-content">
        {{ item.content }}
        <span>{{ item.remark ? '附言: '+item.remark : '' }}</span>
      </p>
      <p class="layim-msgbox-btn">
            {{#  if(item.agree === 0){ }}
                <button class="layui-btn layui-btn-small" data-type="agree">同意</button>
                <button class="layui-btn layui-btn-small layui-btn-primary" data-type="refuse">拒绝</button>
            {{#  } else if(item.agree === 1){ }}
                已同意
            {{#  } else { }}
                已拒绝
            {{#  } }}
      </p>
    </li>
  {{# } else { }}
    <li class="layim-msgbox-system">
      <p><em>系统：</em>{{ item.content }}<span>{{ item.time }}</span></p>
    </li>
  {{# }
}); }}
</textarea>
@endverbatim

<!--
上述模版采用了 laytpl 语法，不了解的同学可以去看下文档：http://www.layui.com/doc/modules/laytpl.html
-->
<script>
    var getmsg_json_url =  "{{URL::asset('api/v1/msgbox')}}";
    var msg_read_url =  "{{URL::asset('api/v1/msgbox')}}";
    var friend_agree_url =  "{{URL::asset('api/v1/friends')}}";
    var friend_refuse_url =  "{{URL::asset('api/v1/friends/refuse')}}";
    var fromGroup = null;
    var group_agree_url =  "{{URL::asset('api/v1/groups/agree')}}";
    var group_refuse_url =  "{{URL::asset('api/v1/groups/refuse')}}";
    layui.use(['layim', 'flow'], function(){
        var layim = layui.layim
            ,layer = layui.layer
            ,laytpl = layui.laytpl
            ,$ = layui.jquery
            ,flow = layui.flow;
        var cache = {}; //用于临时记录请求到的数据
        $.ajaxSetup({
            headers: {
                'Authorization': localStorage.getItem('Authorization')
            },
        });
        //请求消息
        var renderMsg = function(page, callback){
            //实际部署时，请将下述 getmsg.json 改为你的接口地址
            $.ajax({
                // headers: {'Authorization': AccessToken},
                url: getmsg_json_url,
                type: 'POST',
                success: function(res){
                    console.log(res)
                    if(res.code != 0){
                        return layer.msg(res.msg);
                    }

                    //记录来源用户信息
                    layui.each(res.data, function(index, item){
                        cache[item.from] = item.user;
                    });

                    callback && callback(res.data, res.pages);
                },
                error : function (msg ) {
                    layer.msg(msg.responseJSON.message, {
                        icon: 5,
                        time: 2000
                    }, function(){

                    });
                },

            });
        };

        //消息信息流
        flow.load({
            elem: '#LAY_view' //流加载容器
            ,isAuto: false
            ,end: '<li class="layim-msgbox-tips">暂无更多新消息</li>'
            ,done: function(page, next){ //加载下一页
                renderMsg(page, function(data, pages){
                    var html = laytpl(LAY_tpl.value).render({
                        data: data
                        ,page: page
                    });
                    next(html, page < pages);
                });
            }
        });

        //打开页面即把消息标记为已读
        /*
        $.post('/message/read', {
          type: 1
        });
        */
        $.ajax({
            url: msg_read_url,
            type: 'PATCH',
            success: function(res){
            },
            error : function (msg ) {
                layer.msg(msg.responseJSON.message, {
                    icon: 5,
                    time: 2000
                }, function(){});
            },
        });
        //操作
        var active = {
            //同意
            agree: function(othis){
                var li = othis.parents('li')
                    ,uid = li.data('uid')
                    ,from_group = li.data('fromgroup')
                    ,type = li.data('type')
                    ,user = cache[uid];
                fromGroup = from_group;
                console.log(type);
                if(type  === 0 ){
                    //选择分组
                    parent.layui.layim.setFriendGroup({
                        type: 'friend'
                        ,username: user.username
                        ,avatar: user.avatar
                        ,group: parent.layui.layim.cache().friend //获取好友分组数据
                        ,submit: function(group, index){
                            //将好友追加到主面板
                            $.ajax({
                                url: friend_agree_url,
                                type: 'PUT',
                                data:{
                                    uid: uid //对方用户ID
                                    ,from_divide: from_group //对方设定的好友分组
                                    ,divide: group //我设定的好友分组
                                },
                                success: function(res){
                                    //将好友追加到主面板
                                    parent.layui.layim.addList({
                                        type: 'friend'
                                        ,avatar: user.avatar //好友头像
                                        ,username: user.username //好友昵称
                                        ,groupid: group //所在的分组id
                                        ,id: uid //好友ID
                                        ,sign: user.sign //好友签名
                                    });
                                    othis.parent().html('已同意');
                                    parent.layer.close(index);
                                },
                                error : function (msg ) {
                                    layer.msg(msg.responseJSON.message, {
                                        icon: 5,
                                        time: 2000
                                    }, function(){});
                                },
                            });
                        }
                    });
                }
                if(type  === 1){
                    $.ajax({
                        url: group_agree_url,
                        type: 'POST',
                        data:{
                            uid: uid //对方用户ID
                            ,group_id: from_group //申请加群的群号
                        },
                        success: function(res){
                            othis.parent().html('已同意');
                        },
                        error : function (msg ) {
                            layer.msg(msg.responseJSON.message, {
                                icon: 5,
                                time: 2000
                            }, function(){});
                        },
                    });
                }

            }

            //拒绝
            ,refuse: function(othis){
                var li = othis.parents('li')
                    ,uid = li.data('uid')
                    ,type = li.data('type')
                    ,from_group = li.data('fromgroup');
                if(type  === 0){
                    layer.confirm('确定拒绝吗？', function(index){
                        $.ajax({
                            url: friend_refuse_url,
                            type: 'POST',
                            data:{
                                uid: uid ,//对方用户ID
                                from_divide: from_group
                            },
                            success: function(res){
                                layer.close(index);
                                othis.parent().html('<em>已拒绝</em>');
                            },
                            error : function (msg ) {
                                layer.msg(msg.responseJSON.message, {
                                    icon: 5,
                                    time: 2000
                                }, function(){});
                            },
                        });
                    });
                }
                if(type  === 1){
                    layer.confirm('确定拒绝吗？', function(index){
                        $.ajax({
                            url: group_refuse_url,
                            type: 'POST',
                            data:{
                                uid: uid ,//对方用户ID
                                group_id: from_group
                            },
                            success: function(res){
                                layer.close(index);
                                othis.parent().html('<em>已拒绝</em>');
                            },
                            error : function (msg ) {
                                layer.msg(msg.responseJSON.message, {
                                    icon: 5,
                                    time: 2000
                                }, function(){});
                            },
                        });
                    });
                }

            }
        };

        $('body').on('click', '.layui-btn', function(){
            var othis = $(this), type = othis.data('type');
            active[type] ? active[type].call(this, othis) : '';
        });
    });
</script>
</body>
</html>
@stop
