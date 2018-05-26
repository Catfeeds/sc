<script>
    function titleFormatter(value, row, index) {
        return row.name.substring(0, 30)
    }

    function actionFormatter(value, row, index) {

        //编辑
        html = '<button class="btn btn-primary btn-xs margin-r-5 edit" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></button>';

        //置顶
        html += '<button class="btn btn-primary btn-xs margin-r-5 top" data-toggle="tooltip" data-placement="top" title="' + (row.recommended ? '取消推荐' : '推荐') + '"><i class="fa ' + (row.recommended ? 'fa-chevron-circle-down' : 'fa-chevron-circle-up') + '"></i></button>';

        return html;
    }

    function updateRow(field, row, old, $el) {
        $.ajax({
            url: '/admin/pics/' + row.id + '/save',
            type: 'post',
            data: {'_token': '{{ csrf_token() }}', 'clicks': row.clicks},
            success: function (data, status) {
            },
            error: function (data) {
                alert('Error');
            },
        });
    }

    window.actionEvents = {
        'click .edit': function (e, value, row, index) {
            window.location.href = '{{ $base_url }}/' + row.id + '/edit';
        },

        'click .recommend': function (e, value, row, index) {
            $.ajax({
                url: '/admin/pics/' + row.id + '/recommend',
                type: 'post',
                data: {'_token': '{{ csrf_token() }}'},
                success: function (data) {
                    $('#table').bootstrapTable('refresh');
                },
                error: function () {
                    toast('error', '操作失败');
                }
            })
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