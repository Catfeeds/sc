<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                新增<?php echo e($module->title); ?>

            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">新增<?php echo e($module->title); ?></li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <!-- right column -->
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.errors.list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php if(!empty($content)): ?>

                                <?php echo Form::open(['url' => $base_url. '/' . $content, 'class' => 'form-horizontal']); ?>

                            <?php else: ?>
                                <?php echo Form::open(['url' => $base_url, 'class' => 'form-horizontal']); ?>


                            <?php endif; ?>

                            <?php echo $__env->make('admin.contents.form', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php echo Form::close(); ?>


                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>