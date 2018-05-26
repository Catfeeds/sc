<?php $__env->startSection('css'); ?>
    <style>
        .dd button[data-action=collapse] {
            display: none;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                编辑<?php echo e($module->title); ?>

            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">编辑<?php echo e($module->title); ?></li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <!-- right column -->
                <div class="col-md-10">
                    <!-- Horizontal Form -->
                    <div class="box box-info">
                        <div class="box-body">

                            <?php echo $__env->make('admin.errors.list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php echo Form::model($content, ['id' => 'form', 'method' => 'PUT', 'url' => $base_url . '/' . $content->id, 'class' => 'form-horizontal']); ?>


                            <?php echo $__env->make('admin.courses.form', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php echo Form::close(); ?>



                            
                            <div class="modal fade common" id="modal_chapter1" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">添加 章</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form1" action="" method="post" class="form-horizontal">
                                                            <?php echo e(csrf_field()); ?>

                                                            <input id="method" name="_method" type="hidden" value="POST">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">章标题</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" name="title" class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">取消</button>
                                                                <button type="submit" class="btn btn-info pull-right">提交</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="modal fade common" id="modal_chapter2" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">添加 节</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form2" action="" method="post" class="form-horizontal">
                                                            <?php echo e(csrf_field()); ?>

                                                            <input id="method" name="_method" type="hidden" value="POST">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">节标题</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" name="title" class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">取消</button>
                                                                <button type="submit" class="btn btn-info pull-right">提交</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="modal fade common" id="modal_lesson" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">添加 课时</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form3" action="" method="post" class="form-horizontal">
                                                            <?php echo e(csrf_field()); ?>

                                                            <input id="method" name="_method" type="hidden" value="POST">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">课时标题：</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="title" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">课时类型：</label>
                                                                    <select class="col-sm-3" style="margin-left: 14px;"
                                                                            id="lesson_type" name="lesson_type">
                                                                        <option value="0" selected="selected">正常课时
                                                                        </option>
                                                                        <option value="1">章节总结</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">课时摘要：</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="intro"
                                                                               class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">课时正文：</label>
                                                                    <div class="col-sm-9">
                                                                        <textarea class="form-control" rows="3"
                                                                                  name="content" cols="50"
                                                                                  id="content"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">课时时长：</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="duration"
                                                                               class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">媒体资源：</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="media_url"
                                                                               class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2">是否免费：</label>
                                                                    <select class="col-sm-2" id="free" name="free"
                                                                            style="margin-left: 14px;">
                                                                        <option value="0">否</option>
                                                                        <option value="1" selected="selected">是</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">取消</button>
                                                                <button type="submit" class="btn btn-info pull-right">提交</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $('#btn_create1').click(function () {
                                    $('#form1').attr('action', '/admin/courses/<?php echo e($content->id); ?>/chapter');
                                    $('#method').val('POST');
                                });
                                $('#btn_create2').click(function () {
                                    $('#form2').attr('action', '/admin/courses/<?php echo e($content->id); ?>/section');
                                    $('#method').val('POST');
                                });
                                $('#btn_create3').click(function () {
                                    $('#form3').attr('action', '/admin/courses/<?php echo e($content->id); ?>/lesson');
                                    $('#method').val('POST');
                                });
                            </script>
                            

                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $('input[name=icon]').iconpicker();

        $('.dd').nestable();

        $('.btn-menu-remove').click(function () {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/chapters/' + id + '/del',
                method: 'post',
                data: {'_token': '<?php echo e(csrf_token()); ?>', '_method': 'delete'},
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        $('.btn-menu-edit').click(function () {
            var id = $(this).data('id');
            var title = $(this).data('title');
            $('#form_edit').attr('action', '/admin/chapters/' + id + '/edit');
            $('#form_edit input[name=name]').val(title);
            $("#modal_edit").modal('show');
        });

        $('#menu-nestable').on('change', function () {
            var data = $('#menu-nestable').nestable('serialize');
            console.log(data);
            $.ajax({
                url: '/admin/chapters/<?php echo e($course_id); ?>/sorts',
                method: 'post',
                data: {'_token': '<?php echo e(csrf_token()); ?>', 'data': data},
                success: function (data) {
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>