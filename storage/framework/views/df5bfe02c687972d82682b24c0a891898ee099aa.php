<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                系统日志
            </h1>
            <ol class="breadcrumb">
                <li><a href="/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">系统日志</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-2">
                    <div class="box box-success">
                        <div class="box-body">
                            <div id="tree">
                                <ul class="list-group">
                                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <li class="list-group-item node-tree" data-nodeid="1">
                                            <span class="indent"></span><span class="icon glyphicon">
                                            </span><span class="icon node-icon"></span><i><?php echo e($file); ?></i>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.flash', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.layouts.modal', ['id' => 'modal_comment'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.logs.script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <div id="table" data-toggle="table" style="font-size: 18px">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>