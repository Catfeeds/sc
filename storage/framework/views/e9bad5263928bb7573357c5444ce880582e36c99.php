<div class="cb-toolbar">操作:</div>
<div class="btn-group margin-bottom">
    <input type="hidden" name="state" id="state" value=""/>
    <button class="btn btn-primary btn-xs margin-r-5" id="btn_create"
            onclick="window.location.href='<?php echo e($base_url); ?>' + '/create'">新增
    </button>

    <button class="btn btn-danger btn-xs margin-r-5" id="btn_delete" value="<?php echo e(\App\Models\Tag::STATE_DELETED); ?>"
            onclick="remove()" data-toggle="modal" data-target="#modal">删除
    </button>
    <button class="btn btn-default btn-xs margin-r-5" id="btn_sort">排序</button>
</div>

