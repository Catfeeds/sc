<?php $__env->startSection('css'); ?>
    <style>
        .manage {
            cursor: pointer;
        }

    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                直播管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">直播管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.flash', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.modal', ['id' => 'modal_enroll'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.lives.toolbar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.lives.query', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.lives.table', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.lives.script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>