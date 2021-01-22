<div id="" class="layui-layer-content">
    <div class="layim-add-box">
        <div class="layim-add-img">
            <img class="layui-circle" src="//tva1.sinaimg.cn/crop.0.0.720.720.180/005JKVuPjw8ers4osyzhaj30k00k075e.jpg">
            <p>麻花疼</p>
        </div>
        <div class="layim-add-remark">
            <select class="layui-select" id="LAY_layimGroup">
                {{#  layui.each(d, function(index, item){ }}
                <option value="{{#item.id}}">item.name</option>
                {{#  }); }}
            </select>
            <textarea id="LAY_layimRemark" placeholder="验证信息" class="layui-textarea"></textarea>
        </div>
    </div>
</div>
