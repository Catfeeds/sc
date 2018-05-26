<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <?php echo $__env->make('admin.comments.reply', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="box-body">
                <table id="comment_table" data-toggle="table" style="word-break:break-all;">
                    <thead>
                    <tr>
                        <th data-field="id" data-width="60">ID</th>
                        <th data-field="nick_name" data-width="100">会员</th>
                        <th data-field="room_id" data-width="380">房间号</th>
                        <th data-field="state_name" data-width="60" data-formatter="stateFormatter">状态</th>
                        <th data-field="created_at" data-width="130">创建时间</th>
                        <th data-field="action" data-width="100" data-align="center"
                            data-formatter="commentActionFormatter"
                            data-events="commentActionEvents"> 操作
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $('#comment_table').bootstrapTable({
        method: 'get',
        url: '/admin/lives/enrollments',
        pagination: true,
        pageNumber: 1,
        pageSize: 8,
        pageList: [10, 25, 50, 100],
        sidePagination: 'server',
        clickToSelect: true,
        striped: true,
        queryParams: function (params) {
            params.live_id = '<?php echo e($live_id); ?>';
            params._token = '<?php echo e(csrf_token()); ?>';
            return params;
        },
    });

    function stateFormatter(value, row, index) {
        var style = 'label-primary';
        switch (row.state_name) {
            case '正常':
                style = 'label-success';
                break;
            case '已删除':
                style = 'label-danger';
                break;
        }
        return [
            '<span class="label ' + style + '">' + row.state_name + '</span>',
        ].join('');
    }

    function commentActionFormatter(value, row, index) {
        var disabled_del = '';
        switch (row.state_name) {
            case '已删除':
                disabled_del = 'disabled="disabled"';
                break;
        }
        return [
            '<a class="remove" href="javascript:void(0)"><button class="btn btn-danger btn-xs" ' + disabled_del + ' >删除</button></a>'
        ].join('');
    }

    window.commentActionEvents = {
        'click .remove': function (e, value, row, index) {
            var ids = [row.id];
            $.ajax({
                url: '/admin/comments/state',
                type: 'POST',
                data: {'_token': '<?php echo e(csrf_token()); ?>', 'ids': ids, 'state': '<?php echo e(\App\Models\Comment::STATE_DELETED); ?>'},
                success: function () {
                    $('#comment_table').bootstrapTable('selectPage', 1);
                    $('#comment_table').bootstrapTable('refresh');

                    toast('success', '删除成功！');
                }
            });
        },
    };

</script>