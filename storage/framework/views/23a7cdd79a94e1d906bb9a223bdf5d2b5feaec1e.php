<script>
    function titleFormatter(value, row, index) {
        return row.name.substring(0, 30)
    }

    function actionFormatter(value, row, index) {

        //编辑
        html = '<button class="btn btn-primary btn-xs margin-r-5 edit" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></button>';

        return html;
    }

    function updateRow(field, row, old, $el) {
        $.ajax({
            url: '/admin/tags/' + row.id + '/save',
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

    /* 启动排序 */
    $('#btn_sort').click(function () {
        if ($('#btn_sort').hasClass('active')) {
            $('#btn_sort').removeClass('active');
            $('#btn_sort').text('排序');
            $('#table tbody').sortable('disable');
            $('#table tbody').enableSelection();
            toast('info', '<b>已禁用排序功能</b>')
        }
        else {
            $('#btn_sort').addClass('active');
            $('#btn_sort').text('排序(已启用)');
            $('#table tbody').sortable('enable');
            $('#table tbody').disableSelection();
            toast('info', '<b>已启用排序功能</b>')
        }
    });
</script>