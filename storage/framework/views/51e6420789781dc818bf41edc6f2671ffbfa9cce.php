<?php if(Session::has('flash_success')): ?>
    <div class="alert alert-success">
        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fa fa-check"></i><?php echo e(session('flash_success')); ?>

    </div>
<?php elseif(Session::has('flash_warning')): ?>
    <div class="alert alert-warning">
        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fa fa-warning"></i><?php echo e(session('flash_warning')); ?>

    </div>
<?php elseif(Session::has('flash_info')): ?>
    <div class="alert alert-info">
        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fa fa-info"></i><?php echo e(session('flash_info')); ?>

    </div>
<?php elseif(Session::has('flash_error')): ?>
    <div class="alert alert-danger">
        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fa fa-ban"></i><?php echo e(session('flash_error')); ?>

    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
            <p><?php echo e($error); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
    </div>
<?php endif; ?>

