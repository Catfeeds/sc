<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                会员管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">会员管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.layouts.flash', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.members.toolbar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.members.query', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.modal', ['id' => 'modal_message', 'title' => '查看消息'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <table id="table" data-toggle="table" style="word-break:break-all;">
                                <thead>
                                <tr>
                                    <th data-field="id" data-width="50" data-align="center">ID</th>
                                    <th data-field="name" data-width="100" data-align="center">用户姓名</th>
                                    <th data-field="nick_name" data-width="100" data-align="center">昵称</th>
                                    <th data-field="points" data-width="60" data-align="center">积分</th>
                                    <th data-field="mobile" data-align="center" data-width="100">手机号</th>
                                    <th data-field="ip" data-width="100" data-align="center">IP</th>
                                    <th data-field="source" data-width="60" data-align="center">注册来源</th>
                                    <th data-field="type_name" data-align="center" data-width="60"  data-formatter="typeFormatter">会员类型</th>
                                    <th data-field="state_name" data-width="60" data-align="center" data-formatter="stateFormatter">状态</th>
                                    <th data-field="created_at" data-width="120" data-align="center">注册时间</th>
                                    <th data-field="action" data-width="100" data-align="center" data-formatter="actionFormatter" data-events="actionEvents"> 操作</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $('#articles_query').click(function () {
            $('#table').bootstrapTable('selectPage', 1);
        });

        $('#table').bootstrapTable({
            method: 'get',
            url: '/admin/members/table',
            pagination: true,
            pageNumber: 1,
            pageSize: 20,
            pageList: [20, 50, 100, 500],
            sidePagination: 'server',
            clickToSelect: true,
            striped: true,
            queryParams: function (params) {
                var object = $('#form_query input,#form_query select').serializeObject();
                object['state'] = $('#state').val();
                object['_token'] = '<?php echo e(csrf_token()); ?>';
                object['offset'] = params.offset;
                object['limit'] = params.limit;
                return object;
            },
        });

        function actionFormatter(value, row, index) {
            var state_html = '';
            switch (row.state_name) {
                case '已启用':
                    state_html = '<a class="disabled" href="javascript:void(0)"><button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal" title="禁用">禁</button></a>';
                    break;
                case '退款待审核':
                    state_html = '<a class="disabled" href="javascript:void(0)"><button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal" title="禁用">禁</button></a>';
                    break;
                case '已退款':
                    state_html = '<a class="disabled" href="javascript:void(0)"><button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal" title="禁用">禁</button></a>';
                    break;
                case '已禁用':
                    state_html = '<a class="disabled" href="javascript:void(0)"><button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal" title="启用">启</button></a>';
                    break;
            }
            return [
                '<a class="edit" href="javascript:void(0)"><button class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button></a>',
                '<span> </span>',
                state_html,
                '<span> </span>',
                '<a class="wallet" href="javascript:void(0)"><button class="btn btn-success btn-xs"  data-toggle="modal" data-target="#modal_message"><i class="glyphicon glyphicon-yen" data-toggle="tooltip" data-placement="top" title="钱包"></i></button></a>',
                '<span> </span>',
                '<a class="message" href="javascript:void(0)"><button class="btn btn-info btn-xs"  data-toggle="modal" data-target="#modal_message"><i class="fa fa-comment" data-toggle="tooltip" data-placement="top" title="回馈消息"></i></button></a>',
            ].join('');
        }

        $("#modal_remove").click(function () {
            var row_id = $(this).data('id');

            if (typeof(row_id) == "undefined") {
                return false;
            }

            $.ajax({
                type: 'get',
                data: {'_token': '<?php echo e(csrf_token()); ?>'},
                url: '/admin/members/state/' + row_id,
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        window.actionEvents = {
            'click .edit': function (e, value, row, index) {
                window.location.href = '/admin/members/' + row.id + '/edit';
            },
            'click .disabled': function (e, value, row, index) {
                var html = '';
                switch (row.state_name) {
                    case '已启用':
                        html = '禁用';
                        break;
                    case '已禁用':
                        html = '启用';
                        break;
                }

                $('#msg').html('您确认' + html + '该用户吗？');
                $('#modal_remove').show();
                $('#modal_remove').data('id', row.id);
            },
            'click .wallet': function (e, value, row, index) {
                var url = '/admin/members/wallet/' + row.id;
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
            'click .message': function (e, value, row, index) {
                var url = '/admin/members/messages/' + row.id;
                $.ajax({
                    url: url,
                    type: "get",
                    data: {'_token': '<?php echo e(csrf_token()); ?>'},
                    dataType: 'html',
                    success: function (html) {
                        $('#contents').html(html);
                    }
                });
            }
        };

        function typeFormatter(value, row, index) {
            var style = 'label-primary';
            switch (row.type_name) {
                case '黄金会员':
                    style = 'label-warning';
                    break;
                case '普通会员':
                    style = 'label-primary';
                    break;
            }
            return [
                '<span class="label ' + style + '">' + row.type_name + '</span>',
            ].join('');
        }

        function stateFormatter(value, row, index) {
            var style = 'label-primary';
            switch (row.state_name) {
                case '已启用':
                    style = 'label-success';
                    break;
                case '已禁用':
                    style = 'label-danger';
                    break;
                case '退款待审核':
                    style = 'label-primary';
                    break;
                case '已退款':
                    style = 'label-warning';
                    break;
            }
            return [
                '<span class="label ' + style + '">' + row.state_name + '</span>',
            ].join('');
        }

    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>