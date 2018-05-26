<table id="table" data-toggle="table">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="trues"></th>
        <?php $__currentLoopData = $module->columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
            <?php if($column->show): ?>
                <th data-field="<?php echo e($column->type == App\Models\ModuleField::TYPE_ENTITY ? str_replace('_id', '_name', $column->name) : $column->name); ?>"
                    data-align="<?php echo e($column->align === App\Models\ModuleField::COLUMN_ALIGN_LEFT ? 'left' : ($column->align === App\Models\ModuleField::COLUMN_ALIGN_CENTER ? 'center' : 'right')); ?>"
                    data-width="<?php echo e($column->width); ?>"
                    data-formatter="<?php echo e($column->formatter); ?>"
                    data-editable="<?php echo e($column->editable); ?>"><?php echo e($column->label); ?>

                </th>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
        <th data-field="action" data-align="center" data-width="192" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
