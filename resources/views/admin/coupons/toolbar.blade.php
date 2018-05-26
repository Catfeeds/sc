<div class="cb-toolbar">操作:</div>
<div class="btn-group margin-bottom">
    <input type="hidden" name="state" id="state" value=""/>
    <button class="btn btn-primary btn-xs margin-r-5" id="btn_create"
            onclick="window.location.href='{{ $base_url }}' + '/create?category_id=' + $('#category_id').val();">批量生成
    </button>
</div>

<div class="btn-group margin-bottom pull-right">
    <button type="button" class="btn btn-info btn-xs margin-r-5 filter" data-active="btn-info" value="">全部</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-primary"
            value="{{ \App\Models\Coupon::STATE_UNUSED }}">未使用
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-success"
            value="{{ \App\Models\Coupon::STATE_USED }}">已使用
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-warning"
            value="{{ \App\Models\Coupon::STATE_INVALID }}">失效
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5" data-toggle="modal" data-target="#modal_query"><span
                class="fa fa-search"></span></button>
</div>