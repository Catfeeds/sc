<script>
    $.ajax({
        type: 'get',
        async: false,
        url: '/admin/galleries/categories',
        success: function (data) {
            $('#tree').treeview({
                data: data,
                searchResultColor: 'white',
                levels: 4,
                onNodeSelected: function (event, data) {
                    $('#category_id').val(data.id);
                    $('#table').bootstrapTable('refresh');
                }
            });

            if (getNodeIndex(parseInt(getQueryString('category_id')), data) >= 0) {
                $('#tree').treeview('selectNode', [nodeIndex, {silent: false}]);
            }
            else {
                $('#tree').treeview('selectNode', [0, {silent: false}]);
            }
        }
    });

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

    function picFormatter(value, row, index) {
        return '<a href="gallery/images/' + row.id + '">管理(' + row.pic_num + '）' + '</a>';
    }

    function actionFormatter(value, row, index) {
        //编辑
        var html = '<button class="btn btn-primary btn-xs margin-r-5 edit" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></button>';

        //评论
        html += '<button class="btn btn-info btn-xs margin-r-5 comment" data-toggle="modal" data-target="#modal_comment"><i class="fa fa-comment" data-toggle="tooltip" data-placement="top" title="查看评论"></i></button>';

        //推送
        html += '<button class="btn btn-info btn-xs push" data-toggle="modal" data-target="#modal_push"><i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="推送"></i></button>';

        return html;
    }

    function updateRow(field, row, old, $el) {
        $.ajax({
            url: '/admin/galleries/' + row.id + '/save',
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

        'click .tag': function (e, value, row, index) {
            $.ajax({
                url: '/admin/galleries/' + row.id + '/tag',
                type: 'post',
                data: {'_token': '<?php echo e(csrf_token()); ?>', 'tag': '<?php echo e(App\Models\Tag::RECOMMEND); ?>'},
                success: function (data) {
                    $('#table').bootstrapTable('refresh');
                },
                error: function () {
                    toast('error', '操作失败');
                }
            })
        },

        'click .comment': function (e, value, row, index) {
            $('#modal_title').text('查看评论');
            $('#window_msg').hide();

            var url = '/admin/galleries/comments/' + row.id;
            $.ajax({
                url: url,
                type: "get",
                data: {'_token': '<?php echo e(csrf_token()); ?>'},
                dataType: 'html',
                success: function (html) {
                    $('#contents').html(html);
                }
            });
        },

        'click .push': function (e, value, row, index) {
            $('#push_id').val(row.id);
            $('#push_title').val(row.title);
        }
    };

    /* 更新图集图片的大小、宽、高 */
    var btn = $('#btn_update');
    btn.click(function () {
        btn.prop("disabled", true);
        $.get({
            url: '/admin/galleries/image/update',
            data: '',
            dataType: 'json',
            success: function (data) {
                btn.prop("disabled", false);
                alert(data.message);
            }
        });
    })

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