@extends('pc.layouts.root')
@section('title', '聊天记录')
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

<div class="layim-chat-main">
    <ul id="LAY_view"></ul>
    <div id="LAY_page" style="margin: 0 10px;"></div>


<textarea title="消息模版" id="LAY_tpl" style="display:none;">
{{# layui.each(d.data, function(index, item){
  if(item.from_id == parent.layui.layim.cache().mine.id){ }}
    <li class="layim-chat-mine"><div class="layim-chat-user"><img src="{{ item.from_avatar }}"><cite><i>{{ layui.data.date(item.timestamp) }}</i>{{ item.from_name }}</cite></div><div class="layim-chat-text">{{ layui.layim.content(item.content) }}</div></li>
  {{# } else { }}
    <li><div class="layim-chat-user"><img src="{{ item.from_avatar }}"><cite>{{ item.from_name }}<i>{{ layui.data.date(item.timestamp) }}</i></cite></div><div class="layim-chat-text">{{ layui.layim.content(item.content) }}</div></li>
  {{# }
}); }}
</textarea>
<div id="test1"></div>
</div>
@endverbatim
<!--
上述模版采用了 laytpl 语法，不了解的同学可以去看下文档：http://www.layui.com/doc/modules/laytpl.html

-->

<script src="{{ URL::asset('layui/layui.js')}}"></script>
<!-- JS 脚本 -->
<script src="{{ mix('js/app.js') }}"></script>
<script>
    var chatlog_url = "{{URL::asset('api/v1/chatlogs')}}";
    layui.use(['layim', 'laypage'], function(){
        var layim = layui.layim
            ,layer = layui.layer
            ,laytpl = layui.laytpl
            ,$ = layui.jquery
            ,laypage = layui.laypage;
        //执行一个laypage实例
        //聊天记录的分页此处不做演示，你可以采用laypage，不了解的同学见文档：http://www.layui.com/doc/modules/laypage.html

        $.ajaxSetup({
            headers: {
                'Authorization': localStorage.getItem('Authorization')
            },
        });
        //开始请求聊天记录
        var parseQueryString = function() {

            var str = window.location.search;
            var objURL = {};

            str.replace(
                new RegExp( "([^?=&]+)(=([^&]*))?", "g" ),
                function( $0, $1, $2, $3 ){
                    objURL[ $1 ] = $3;
                }
            );
            return objURL;
        };

        var param =  parseQueryString();
        //获得URL参数。该窗口url会携带会话id和type，他们是你请求聊天记录的重要凭据

            //实际使用时，下述的res一般是通过Ajax获得，而此处仅仅只是演示数据格式


        console.log(param);

        $.ajax({
            // headers: {'Authorization': AccessToken},
            url: chatlog_url,
            type: 'POST',
            data:{
                id:param['id'],
                type: param['type'],
            },
            success: function(res){
                console.log(res.data);
                laypage.render({
                    elem: 'test1' //注意，这里的 test1 是 ID，不用加 # 号
                    ,count: res.data.total //数据总数，从服务端得到
                    ,limit: res.data.per_page //
                    ,jump: function(obj, first){
                        //obj包含了当前分页的所有参数，比如：
                        console.log(obj.curr); //得到当前页，以便向服务端请求对应页的数据。
                        console.log(obj.limit); //得到每页显示的条数
                        $.ajax({
                            // headers: {'Authorization': AccessToken},
                            url: chatlog_url,
                            type: 'POST',
                            data:{
                                id:param['id'],
                                type: param['type'],
                                page: obj.curr,
                            },
                            success: function(res){
                                var html = laytpl(LAY_tpl.value).render({
                                    data: res.data.data.reverse()
                                });
                                $('#LAY_view').html(html);
                            },
                            error : function (msg ) {
                                layer.msg(msg.responseJSON.message, {
                                    icon: 5,
                                    time: 2000
                                }, function(){
                                });
                            },

                        });
                        //首次不执行
                        if(!first){
                            //do something
                        }
                    }
                });
                var html = laytpl(LAY_tpl.value).render({
                    data: res.data.data.reverse()
                    ,count: res.data.last_page //数据总数，从服务端得到
                });
                $('#LAY_view').html(html);
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
</script>
</body>
</html>
@stop
