<div class="cb-toolbar">操作:</div>
<div class="btn-group margin-bottom">
    <input type="hidden" name="state" id="state" value=""/>
    <button class="btn btn-primary btn-xs margin-r-5" id="btn_create"
            onclick="window.location.href='<?php echo e($base_url); ?>' + '/create';">新增
    </button>
    <button class="btn btn-danger btn-xs margin-r-5" id="btn_delete" value="<?php echo e(\App\Models\Live::STATE_DELETED); ?>"
            onclick="remove()" data-toggle="modal" data-target="#modal">删除
    </button>
</div>
<div class="btn-group margin-bottom pull-right">
    <button type="button" class="btn btn-info btn-xs margin-r-5 filter" data-active="btn-info" value="">全部</button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-primary"
            value="<?php echo e(\App\Models\Live::STATE_COMING); ?>">未开始
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-success"
            value="<?php echo e(\App\Models\Live::STATE_PREPARE); ?>">待准备
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-default"
            value="<?php echo e(\App\Models\Live::STATE_PREPARED); ?>">准备完毕
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-info"
            value="<?php echo e(\App\Models\Live::STATE_ONGOING); ?>">进行中
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-warning"
            value="<?php echo e(\App\Models\Live::STATE_END); ?>">已结束
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5 filter" data-active="btn-danger"
            value="<?php echo e(\App\Models\Live::STATE_DELETED); ?>">已删除
    </button>
    <button type="button" class="btn btn-default btn-xs margin-r-5" data-toggle="modal" data-target="#modal_query"><span
                class="fa fa-search"></span></button>
</div>
