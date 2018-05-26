<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                添加会员
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/members">会员管理</a></li>
                <li class="active">添加</li>
            </ol>
        </section>

        <section class="content">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-body">
                                <?php echo $__env->make('admin.errors.list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                <?php echo Form::open(['url' => '/admin/members', 'class' => 'form-horizontal']); ?>


                                <?php echo $__env->make('admin.members.form', ['password' => 'password','placeholder'=>''], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                                <?php echo Form::close(); ?>

                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </section>


    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>