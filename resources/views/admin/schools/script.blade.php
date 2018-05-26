<script>

    function stateFormatter(value, row, index) {
        var style = 'label-primary';
        switch (row.state_name) {
            case '未发布':
                style = 'label-primary';
                break;
            case '已发布':
                style = 'label-success';
                break;
            case '已撤回':
                style = 'label-warning';
                break;
            case '已删除':
                style = 'label-danger';
                break;
        }
        return [
            '<span class="label ' + style + '">' + row.state_name + '</span>',
        ].join('');
    }

    function titleFormatter(value, row, index) {
        return row.name.substring(0, 30)
    }

    function actionFormatter(value, row, index) {

        @can('edit-school')
            //编辑
            html = '<button class="btn btn-primary btn-xs margin-r-5 edit" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></button>';
            return html;
        @endcan
    }

    function updateRow(field, row, old, $el) {
        $.ajax({
            url: '/admin/schools/' + row.id + '/save',
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
    };
</script>