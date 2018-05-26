<?php $__env->startSection('body'); ?>
    <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

        <?php echo $__env->make('admin.widgets.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php echo $__env->make('admin.widgets.menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php echo $__env->yieldContent('css'); ?>

        <?php echo $__env->yieldContent('content'); ?>

        <?php echo $__env->make('admin.widgets.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php echo $__env->make('admin.widgets.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php echo $__env->yieldContent('js'); ?>

    </div>
    </body>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.frame', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>