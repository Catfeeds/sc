<div class="cb-toolbar">操作:</div>
<div class="btn-group margin-bottom">
    <input type="hidden" name="state" id="state" value=""/>
    <button class="btn btn-primary btn-xs margin-r-5" id="btn_create" onclick="window.location.href='<?php echo e($base_url); ?>' + '/create?category_id=' + $('#category_id').val();">新增</button>
    <button class="btn btn-success btn-xs margin-r-5 state" value="<?php echo e(\App\Models\Gallery::STATE_PUBLISHED); ?>">发布</button>
    <button class="btn btn-warning btn-xs margin-r-5 state" value="<?php echo e(\App\Models\Gallery::STATE_CANCELED); ?>">撤回</button>
    <button class="btn btn-danger btn-xs margin-r-5" id="btn_delete" value="<?php echo e(\App\Models\Gallery::STATE_DELETED); ?>" onclick="remove()" data-toggle="modal" data-target="#modal">删除</button>
    <button class="btn btn-default btn-xs margin-r-5" id="btn_sort">排序</button>
    <button class="btn btn-info btn-xs margin-r-5 import" id="btn_import" data-toggle="modal"
            data-target="#modal_import">批量导入
    </button>
    <button class="btn btn-info btn-xs margin-r-5" id="btn_update">更新数据</button>
</div>
<div class="btn-group margin-bottom pull-right">
    <button type="button" class="btn btn-info btn-xs margin-r-5 filter" data-active="btn-info" value="">全部</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-primary" value="<?php echo e(\App\Models\Gallery::STATE_NORMAL); ?>">未发布</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-success" value="<?php echo e(\App\Models\Gallery::STATE_PUBLISHED); ?>">已发布</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-warning" value="<?php echo e(\App\Models\Gallery::STATE_CANCELED); ?>">已撤回</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-danger" value="<?php echo e(\App\Models\Gallery::STATE_DELETED); ?>">已删除</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5" data-toggle="modal" data-target="#modal_query"><span class="fa fa-search"></span></button>
</div>
