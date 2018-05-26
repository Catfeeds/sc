<div class="form-group">
    <?php echo Form::label('name', '名称:',['class' => 'control-label col-sm-1']); ?>

    <div class="col-sm-5">
        <?php echo Form::text('name', null, ['class' => 'form-control']); ?>

    </div>
    <?php echo Form::label('description', '备注:', ['class' => 'control-label col-sm-1']); ?>

    <div class="col-sm-5">
        <?php echo Form::text('description', null, ['class' => 'form-control']); ?>

    </div>
</div>

<div class="form-group">
    <label for="role" class="control-label col-sm-1">关联权限:</label>
    <div class="col-sm-11">
        <?php if(isset($perms)): ?>
            <div class="checkbox">
                <?php for($i=0; $i<$count; $i++): ?>
                    <label>
                        <input type="checkbox" <?php echo e(in_array($permissions[$i]['id'], $perms) ? 'checked' : ''); ?> name="permission_id[]"
                               value="<?php echo e($permissions[$i]['id']); ?>"><?php echo e($permissions[$i]['description']); ?>

                    </label>
                    <?php if($i < $count - 1 && $permissions[$i]['group'] != $permissions[$i+1]['group'] ): ?>
                        <br />
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <div class="checkbox">
                <?php for($i=0; $i<$count; $i++): ?>
                    <label>
                        <input type="checkbox" name="permission_id[]" value="<?php echo e($permissions[$i]['id']); ?>"><?php echo e($permissions[$i]['description']); ?>

                    </label>
                    <?php if($i < $count - 1 && $permissions[$i]['group'] != $permissions[$i+1]['group'] ): ?>
                        <br />
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<div class="box-footer">
    <button type="button" class="btn btn-default" onclick="window.history.back();">取　消</button>
    <button type="submit" class="btn btn-info pull-right">确　定</button>
</div>

<script>
    $(function () {
        $('#begin_time').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            locale: 'zh-cn'
        });
        $('#end_time').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            locale: 'zh-cn'
        });
    });
</script>