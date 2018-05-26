<?php $__currentLoopData = $_GET; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <input type="hidden" id="<?php echo e($k); ?>" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#tab_course_basic" data-toggle="tab">基本信息</a>
    </li>
</ul>

<div class="tab-content">
    <div id="tab_course_basic" class="tab-pane fade in active padding-t-15">

        <div class="form-group">
            <?php echo Form::label('name', '学校名称：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('name', null, ['class' => 'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('address', '学校地址：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('address', null, ['class' => 'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('phone', '联系电话：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('phone', null, ['class' => 'form-control']); ?>

            </div>
        </div>

        <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('create-school')): ?>
            <div class="form-group">
                <?php echo Form::label('member_id', '校长：', ['class' => 'control-label col-sm-1']); ?>

                <div class="col-sm-2">
                    <?php echo Form::select('member_id', $member, null, ['class' => 'form-control']); ?>

                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<div class="box-footer">
    <button type="button" class="btn btn-default"
            onclick="location.href='<?php echo e(isset($back_url) ? $back_url : $base_url); ?>';"> 取　消
    </button>
    <button type="submit" class="btn btn-info pull-right" id="submit">保　存</button>
</div>