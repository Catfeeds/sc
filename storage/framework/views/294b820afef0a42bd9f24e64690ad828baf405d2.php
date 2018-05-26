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
                章节/课时管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">章节/课时管理</li>
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
                                                                    <?php echo Form::label(null, '章标题',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-8">
                                                                        <?php echo Form::text('title', null, ['class'=>'form-control']); ?>

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

                            
                            <div class="modal fade common" id="modal_edit" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">编辑 章</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form_edit" action="" method="post"
                                                              class="form-horizontal">
                                                            <?php echo e(csrf_field()); ?>

                                                            <input id="method" name="_method" type="hidden" value="PUT">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    <?php echo Form::label(null, '章标题',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-8">
                                                                        <?php echo Form::text('title', null, ['class'=>'form-control']); ?>

                                                                        <?php echo Form::hidden('course_id', null, ['class'=>'form-control']); ?>

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
                                                                    <?php echo Form::label(null, '课时标题：',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('title', null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('type', '课时类型：',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::select('type', ['正常课时','章节总结'], null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('intro', '课时摘要：',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('intro', null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('content', '课时正文：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::textarea('content', null, ['class'=>'form-control', 'rows'=>'3', 'cols'=>'50']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('duration', '课时时长：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('duration', null, ['class'=>'form-control', 'placeholder'=>'单位（分钟）']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('media_url', '媒体资源：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('media_url', null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('free', '是否免费：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::select('free', ['否','是'], null, ['class'=>'form-control']); ?>

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

                            
                            <div class="modal fade common" id="modal_edit-lesson" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h4 class="modal-title">编辑 课时</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form_edit-lesson" action="" method="post"
                                                              class="form-horizontal">
                                                            <?php echo e(csrf_field()); ?>

                                                            <?php echo Form::hidden('course_id', null, ['class'=>'form-control']); ?>

                                                            <input id="method" name="_method" type="hidden" value="PUT">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    <?php echo Form::label(null, '课时标题：',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('title', null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('type', '课时类型：',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::select('type', ['正常课时','章节总结'], null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('intro', '课时摘要：',['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('intro', null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('content', '课时正文：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::textarea('content', null, ['class'=>'form-control', 'rows'=>'3', 'cols'=>'50']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('duration', '课时时长：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('duration', null, ['class'=>'form-control', 'placeholder'=>'单位（分钟）']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('media_url', '媒体资源：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::text('media_url', null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <?php echo Form::label('free', '是否免费：', ['class'=>'control-label col-sm-2']); ?>

                                                                    <div class="col-sm-9">
                                                                        <?php echo Form::select('free', ['否','是'], null, ['class'=>'form-control']); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">
                                                                    取消
                                                                </button>
                                                                <button type="submit" class="btn btn-info pull-right">
                                                                    提交
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div id="tab_课时管理" class="tab-pane fade in padding-t-15" style="min-height: 300px;">
                                <div class="btn-group margin-b-5">
                                    <button class="btn btn-primary btn-xs margin-r-5 margin-b-5 btn_creates"
                                            id="btn_create1" data-toggle="modal" data-target="#modal_chapter1">添加章
                                    </button>

                                    <button class="btn btn-info btn-xs margin-r-5 margin-b-5 btn_creates"
                                            id="btn_create3" data-toggle="modal" data-target="#modal_lesson">添加课时
                                    </button>

                                    <a class="btn btn-primary btn-xs margin-r-5" href="/admin/courses">返回课程</a>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box box-info">
                                            <div class="box-body">
                                                <?php echo $__env->make('admin.layouts.flash', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                <div class="dd chapter-nestable" id="menu-nestable">
                                                    <?php echo e(count($chapterList) ? '' : '没有查询到章节信息'); ?>

                                                    <?php echo \App\Helpers\HtmlBuilder::chapterEditor($chapterList); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $('#btn_create1').click(function () {
                                    $('#form1').attr('action', '/admin/chapters/<?php echo e($content->id); ?>/create');
                                    $('#method').val('POST');
                                });

                                $('#btn_create3').click(function () {
                                    $('#form3').attr('action', '/admin/lessons/<?php echo e($content->id); ?>/create');
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
                url: '/admin/chapters/' + id,
                method: 'post',
                data: {'_token': '<?php echo e(csrf_token()); ?>', '_method': 'delete'},
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        $('.btn-menu-remove-lesson').click(function () {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/lessons/' + id,
                method: 'post',
                data: {'_token': '<?php echo e(csrf_token()); ?>', '_method': 'delete'},
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        $('.btn-menu-edit').click(function () {
            var id = $(this).data('id');
            var title = $(this).data('name');
            var course_id = $(this).data('course_id');
            $('#form_edit').attr('action', '/admin/chapters/' + id);
            $('#form_edit input[name=title]').val(title);
            $('#form_edit input[name=course_id]').val(course_id);
            $("#modal_edit").modal('show');
        });

        $('.btn-menu-edit-lesson').click(function () {
            var id = $(this).data('id');
            var title = $(this).data('name');
            var course_id = $(this).data('course_id');
            var intro = $(this).data('intro');
            var content = $(this).data('content');
            var duration = $(this).data('duration');
            var media_url = $(this).data('media_url');
            var free = $(this).data('free');
            var type = $(this).data('type');
            $('#form_edit-lesson').attr('action', '/admin/lessons/' + id);
            $('#form_edit-lesson input[name=title]').val(title);
            $('#form_edit-lesson input[name=course_id]').val(course_id);
            $('#form_edit-lesson input[name=intro]').val(intro);
            $('#form_edit-lesson textarea[name=content]').val(content);
            $('#form_edit-lesson input[name=duration]').val(duration);
            $('#form_edit-lesson input[name=media_url]').val(media_url);
            $('#form_edit-lesson select[name=free]').val(free);
            $('#form_edit-lesson select[name=type]').val(type);
            $("#modal_edit-lesson").modal('show');
        });

        $('#menu-nestable').on('change', function () {
            var data = $('#menu-nestable').nestable('serialize');
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