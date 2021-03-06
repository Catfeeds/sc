<?php $__env->startSection('content'); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                版本管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">版本管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.layouts.flash', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <div id="modal_remove"></div>
                            <div class="btn-group margin-b-5">
                                <button class="btn btn-primary btn-xs margin-r-5 margin-b-5" id="create"
                                        onclick="javascript:window.location.href='/admin/apps/create'">添加版本
                                </button>
                            </div>

                            <table data-toggle="table"
                                   data-url="apps/table"
                                   data-pagination="true">
                                <thead>
                                <tr>
                                    <th data-field="id" data-align="center" data-width="30">ID</th>
                                    <th data-field="name" data-width="200">名称</th>
                                    <th data-field="android_version" data-align="center" data-width="120">安卓版本号</th>
                                    <th data-field="android_force" data-align="center" data-width="120"
                                        data-formatter="booleanFormatter">安卓强制更新
                                    </th>
                                    <th data-field="ios_version" data-align="center" data-width="120">ios版本号</th>
                                    <th data-field="ios_force" data-align="center" data-width="120"
                                        data-formatter="booleanFormatter">ios强制更新
                                    </th>
                                    <th data-field="state" data-align="center" data-width="100"
                                        data-formatter="booleanFormatter">版本状态
                                    </th>
                                    <th data-field="action" data-align="center" data-formatter="actionFormatter"
                                        data-events="actionEvents" data-width="100">操作
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
                // '<a class="remove" href="javascript:void(0)"><button class="btn btn-primary btn-xs">删除</button></a>',
            ].join('');
        }

        $("#modal_remove").click(function () {
            var row_id = $(this).data('id');
            $.ajax({
                type: 'get',
                data: {'_token': '<?php echo e(csrf_token()); ?>'},
                url: '/admin/apps/' + row_id + '/delete',
                success: function (data) {
                    window.location.href = '/admin/apps';
                }
            });
        });

        window.actionEvents = {
            'click .like': function (e, value, row, index) {

                alert('You click like icon, row: ' + JSON.stringify(row));
                console.log(value, row, index);
            },
            'click .edit': function (e, value, row, index) {
                window.location.href = '/admin/apps/' + row.id + '/edit';
            },
            'click .remove': function (e, value, row, index) {
                $('#modal_remove').data('id', row.id);
            },
        };

    </script>
    <?php echo $__env->make('admin.contents.script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>