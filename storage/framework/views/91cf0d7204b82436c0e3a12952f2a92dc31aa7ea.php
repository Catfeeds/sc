<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                新增角色
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">系统管理</a></li>
                <li><a href="/roles">角色管理</a></li>
                <li class="active">新增</li>
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
                            <?php echo Form::open(['url' => '/admin/roles','class' => 'form-horizontal']); ?>


                            <?php echo $__env->make('admin.roles._form', ['submitButtonText' => '添加角色'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php echo Form::close(); ?>


                        </div>
                    </div><!-- /.box -->
                </div><!--/.col (right) -->
            </div>   <!-- /.row -->
        </section>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>