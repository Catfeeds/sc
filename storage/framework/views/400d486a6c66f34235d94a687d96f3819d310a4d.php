<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                角色管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">角色管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.layouts.flash', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.roles.toolbar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <table data-toggle="table"
                                   data-url="roles/table">
                                <thead>
                                <tr>
                                    <th data-field="id" data-align="center">ID</th>
                                    <th data-field="name" data-width="120" data-align="center">角色名称</th>
                                    <th data-field="role_users">关联用户</th>
                                    <th data-field="action" data-formatter="actionFormatter" data-events="actionEvents" data-align="center"
                                        data-width="150">操作
                                    </th>
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
        function actionFormatter(value, row, index) {
            return [
                '<a class="edit" href="javascript:void(0)"><button class="btn btn-primary btn-xs">编辑</button></a>',
                '<span> </span>',
                '<a class="remove" href="javascript:void(0)"><button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal">删除</button></a>',
            ].join('');
        }

        $("#modal_remove").click(function () {
            var row_id = $(this).data('id');
            $.ajax({
                url: '/admin/roles/' + row_id + '/delete',
                data: {'_token': '<?php echo e(csrf_token()); ?>'},
                success: function (data) {
                    window.location.href = '/admin/roles';
                }
            });
        });

        window.actionEvents = {
            'click .edit': function (e, value, row, index) {
                window.location.href = '/admin/roles/' + row.id + '/edit';
            },

            'click .remove': function (e, value, row, index) {
                $('#modal_remove').data('id', row.id);
            },
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>