<script>

    function titleFormatter(value, row, index) {
        return row.name.substring(0, 30)
    }

    function actionFormatter(value, row, index) {

        <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('edit-grade')): ?>
            //编辑
            html = '<button class="btn btn-primary btn-xs margin-r-5 edit" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></button>';
            return html;
        <?php endif; ?>
    }

    function updateRow(field, row, old, $el) {
        $.ajax({
            url: '/admin/grades/' + row.id + '/save',
            type: 'post',
            data: {'_token': '<?php echo e(csrf_token()); ?>', 'clicks': row.clicks},
            success: function (data, status) {
            },
            error: function (data) {
                alert('Error');
            },
        });
    }

    window.actionEvents = {
        'click .edit': function (e, value, row, index) {
            window.location.href = '<?php echo e($base_url); ?>/' + row.id + '/edit';
        },
    };
</script>