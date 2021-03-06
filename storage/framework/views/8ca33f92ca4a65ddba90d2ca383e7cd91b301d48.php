<script>
    function stateFormatter(value, row, index) {
        var style = 'label-primary';
        switch (row.state_name) {
            case '未开始':
                style = 'label-primary';
                break;
            case '待准备':
                style = 'label-warning';
                break;
            case '准备完毕':
                style = 'label-default';
                break;
            case '进行中':
                style = 'label-success';
                break;
            case '已结束':
                style = 'label-danger';
                break;
        }
        return [
            '<span class="label ' + style + '">' + row.state_name + '</span>',
        ].join('');
    }

    function manageFormatter(value, row, index) {
        var style = 'label-primary';
        var manage = '';
        var oc = '';
        switch (row.state_name) {
            case '未开始':
                style = 'label-primary';
                manage = '结束报名';
                break;
            case '待准备':
                style = 'label-danger';
                manage = '准备直播';
                break;
            case '准备完毕':
                style = 'label-info';
                manage = '开启直播';
                oc = 'start("' + row.title + '")';
                break;
            case '进行中':
                style = 'label-primary';
                manage = '结束直播';
                oc = 'close("' + row.title + '")';
                break;
            case '已结束':
                style = 'label-warning';
                break;
        }
        return [
            '<span class="label ' + style + ' manage" onclick=' + oc + '>' + manage + '</span>',
        ].join('');
    }

    window.stateEvents = {
        'click .manage': function (e, value, row, index) {

            if (row.state_name == '未开始') {
                $.ajax({
                    url: '/admin/lives/state',
                    type: 'post',
                    data: {
                        '_token': '<?php echo e(csrf_token()); ?>',
                        'id': row.id,
                        'state': '<?php echo e(\App\Models\Live::STATE_PREPARE); ?>'
                    },
                    success: function (data) {
                        $('#table').bootstrapTable('refresh');
                    },
                    error: function () {
                        toast('error', '操作失败');
                    }
                })
            } else if (row.state_name == '待准备') {
                $.ajax({
                    url: '/admin/lives/state',
                    type: 'post',
                    data: {
                        '_token': '<?php echo e(csrf_token()); ?>',
                        'id': row.id,
                        'state': '<?php echo e(\App\Models\Live::STATE_PREPARED); ?>'
                    },
                    success: function (data) {
                        $('#table').bootstrapTable('refresh');
                    },
                    error: function () {
                        toast('error', '准备失败，请联系技术人员');
                    }
                })
            } else if (row.state_name == '准备完毕') {
                start_open = true;
                $('#msg').html('您确认开启直播：' + row.title + '吗？');
                $('#modal_remove').show();

                $.ajax({
                    url: '/admin/lives/state',
                    type: 'post',
                    data: {
                        '_token': '<?php echo e(csrf_token()); ?>',
                        'id': row.id,
                        'state': '<?php echo e(\App\Models\Live::STATE_ONGOING); ?>'
                    },
                    success: function (data) {
                        $('#table').bootstrapTable('refresh');
                    },
                    error: function () {
                        toast('error', '操作失败');
                    }
                })
            } else {
                start_open = true;
                $('#msg').html('您确认关闭直播：' + row.title + '吗？');
                $('#modal_remove').show();

                $.ajax({
                    url: '/admin/lives/state',
                    type: 'post',
                    data: {'_token': '<?php echo e(csrf_token()); ?>', 'id': row.id, 'state': '<?php echo e(\App\Models\Live::STATE_END); ?>'},
                    success: function (data) {
                        $('#table').bootstrapTable('refresh');
                    },
                    error: function () {
                        toast('error', '结束失败，请联系技术人员');
                    }
                })
            }
        }
    }

    function titleFormatter(value, row, index) {
        return row.title + (row.is_top ? '<span class="badge badge-default pull-right"> 置顶</span>' : '')
    }

    function actionFormatter(value, row, index) {
        //编辑
        var html = '<button class="btn btn-primary btn-xs margin-r-5 edit" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></button>';

        //置顶
        html += '<button class="btn btn-primary btn-xs margin-r-5 top" data-toggle="tooltip" data-placement="top" title="' + (row.is_top ? '取消置顶' : '置顶') + '"><i class="fa ' + (row.top ? 'fa-chevron-circle-down' : 'fa-chevron-circle-up') + '"></i></button>';

        //查看报名人数
        html += '<button class="btn btn-info btn-xs margin-r-5 enroll" data-toggle="modal" data-target="#modal_enroll"><i class="fa fa-comment" data-toggle="tooltip" data-placement="top" title="查看报名情况"></i></button>';

        //推送
        html += '<button class="btn btn-info btn-xs push" data-toggle="modal" data-target="#modal_push"><i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="推送"></i></button>';

        return html;
    }

    function updateRow(field, row, old, $el) {
        $.ajax({
            url: '/admin/lives/' + row.id + '/save',
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

        'click .top': function (e, value, row, index) {
            $.ajax({
                url: '/admin/lives/' + row.id + '/top',
                type: 'post',
                data: {'_token': '<?php echo e(csrf_token()); ?>'},
                success: function (data) {
                    $('#table').bootstrapTable('refresh');
                },
                error: function () {
                    toast('error', '操作失败');
                }
            })
        },

        'click .tag': function (e, value, row, index) {
            $.ajax({
                url: '/admin/lives/' + row.id + '/tag',
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

        'click .enroll': function (e, value, row, index) {
            $('#modal_title').text('查看直播报名情况');
            $('#window_msg').hide();

            var url = '/admin/lives/enroll/' + row.id;
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
        },
    };

    /* 删除 */
    var remove_open = false;
    $("#modal_remove").click(function () {
        if (remove_open == true) {
            return false;
        }

        var state = 0;
        var rows = $('#table').bootstrapTable('getSelections');

        var id = [];
        for (var i = 0; i < rows.length; i++) {
            id[id.length] = rows[i].id;
        }

        $.ajax({
            url: '<?php echo e($base_url); ?>' + '/state',
            type: 'POST',
            data: {'_token': '<?php echo e(csrf_token()); ?>', 'id': id, 'state': state},
            success: function () {
                $('#modal').modal('hide');
                $('#table').bootstrapTable('refresh');
            }
        });
    });

    function remove() {
        remove_open = false;
        var rows = $('#table').bootstrapTable('getSelections');
        if (rows.length > 0) {
            $('#msg').html('您确认删除这<strong><span class="text-danger">' + rows.length + '</span></strong>条信息吗？');
            $('#modal_remove').show();
        } else {
            $('#msg').html('请选择要删除的数据！');
            $('#modal_remove').hide();
        }
    }

    /* 修改状态 */
    $('.state').click(function () {
        var state = $(this).val();

        var rows = $('#table').bootstrapTable('getSelections');
        var id = [];
        for (var i = 0; i < rows.length; i++) {
            id[id.length] = rows[i].id;
        }

        if (id.length > 0) {
            $.ajax({
                url: '<?php echo e($base_url); ?>' + '/state',
                type: 'POST',
                data: {'_token': '<?php echo e(csrf_token()); ?>', 'id': id, 'state': state},
                success: function () {
                    $('#table').bootstrapTable('refresh');
                }

            });
        }
    });


    /* 筛选 */
    $('.filter').click(function () {
        var value = $(this).val();
        $('#state').val(value);
        $('#table').bootstrapTable('selectPage', 1);

        //改变按钮样式
        $('.filter').removeClass('btn-primary btn-info btn-success btn-danger btn-warning');
        $('.filter').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass($(this).data('active'));
    });

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