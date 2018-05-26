<?php $__currentLoopData = $_GET; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <input type="hidden" id="<?php echo e($k); ?>" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
<ul id="tabs" class="nav nav-tabs">
    <?php $__currentLoopData = $module->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <?php if(count($group->editors) > 0): ?>
            <li class="<?php echo e($loop->first ? 'active' : ''); ?>">
                <a href="#<?php echo e('tab_' . $group->name); ?>" data-toggle="tab"><?php echo e($group->name); ?></a>
            </li>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
</ul>

<div class="tab-content">
    <?php $__currentLoopData = $module->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <?php if(count($group->editors) > 0): ?>
            <div id="<?php echo e('tab_' . $group->name); ?>"
                 class="tab-pane fade in <?php echo e($loop->first ? 'active' : ''); ?> padding-t-15">
                <?php $position = 0; $index = 0; ?>
                <?php $__currentLoopData = $group->editors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $editor): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <?php if($editor->show): ?>
                        <?php if($position == 0): ?>
                            <div class="form-group">
                                <?php endif; ?>
                                <?php if($editor->type == \App\Models\ModuleField::EDITOR_TYPE_HTML): ?>
                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::textarea($editor->name, null, ['class' => 'form-control']); ?>

                                    </div>
                                    <script>
                                        CKEDITOR.replace('<?php echo e($editor->name); ?>', {
                                            height: '<?php echo e($editor->rows * 20); ?>',
                                            filebrowserUploadUrl: '/admin/files/upload?type=image&_token=<?php echo e(csrf_token()); ?>'
                                        });
                                    </script>
                                <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_DATETIME): ?>
                                    <?php echo Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']); ?>

                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <div class='input-group date'>
                                            <?php echo Form::text($editor->name, null, ['class' => 'form-control']); ?>

                                            <span class="input-group-addon"> <span
                                                        class="glyphicon glyphicon-calendar"></span> </span>
                                        </div>
                                    </div>
                                <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_SELECT_SINGLE): ?>
                                    <?php echo Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']); ?>

                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::select($editor->name, string_to_option($editor->options), null, ['class' => 'form-control', $editor->readonly ? 'readonly' : '']); ?>

                                    </div>
                                <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_SELECT_MULTI): ?>
                                    <?php echo Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']); ?>

                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::select("$editor->name[]", array_to_option($editor->options), array_to_option($editor->selected)?array_to_option($editor->selected):'', ['class' => 'form-control select2','multiple'=>'multiple']); ?>

                                    </div>
                                <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_TEXTAREA): ?>
                                    <?php echo Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']); ?>

                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::textarea($editor->name, null, ['class' => 'form-control', 'rows' => $editor->rows, $editor->readonly ? 'readonly' : '']); ?>

                                    </div>
                                <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_IMAGES): ?>
                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::hidden($editor->name, null, ['class' => 'form-control', 'id' => $editor->name]); ?>

                                    </div>
                                <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_VIDEOS): ?>
                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::hidden($editor->name, null, ['class' => 'form-control', 'id' => $editor->name]); ?>

                                    </div>

                                <?php else: ?>
                                    <?php echo Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']); ?>

                                    <div class="col-sm-<?php echo e($editor->columns); ?>">
                                        <?php echo Form::text($editor->name, null, ['class' => 'form-control', $editor->required ? 'required' : '', $editor->readonly ? 'readonly' : '']); ?>

                                    </div>
                                <?php endif; ?>
                                <?php $position += $editor->columns + 1; if ($loop->last || $position + $group->editors[$index + 1]->columns + 1 > 12) {
                                    $position = 0;
                                } ?>
                                <?php if($position == 0 || $position == 12): ?>
                            </div>
                        <?php endif; ?>

                        <?php if($editor->type == \App\Models\ModuleField::EDITOR_TYPE_IMAGE): ?>
                            <div class="form-group">
                                <label class="control-label col-sm-1">上传图片:</label>
                                <div class="col-sm-11">
                                    <h4>您所选择的文件列表：</h4>
                                    <div id="ossfile-<?php echo e($editor->name); ?>"></div>

                                    <br/>
                                    <div class="show-oss-img-<?php echo e($editor->name); ?>"></div>
                                    <div id="container-<?php echo e($editor->name); ?>">
                                        <a id="selectfiles_<?php echo e($editor->name); ?>" href="javascript:void(0)"
                                           class='oss-btn'>上传图片</a>
                                        <div class="oss-img-uri-<?php echo e($editor->name); ?>">
                                            <input type="hidden" value="" class="<?php echo e($editor->name); ?>">
                                        </div>
                                    </div>
                                    <br/>
                                </div>
                            </div>
                            <script>
                                var <?php echo e($editor->name); ?>_preview = $('#<?php echo e($editor->name); ?>').val();
                                if (<?php echo e($editor->name); ?>_preview.length > 0) {
                                    <?php echo e($editor->name); ?>_preview = ['<img height="240" src="' + <?php echo e($editor->name); ?>_preview + '" class="kv-preview-data file-preview-image">'];
                                }
                            </script>
                        <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_VIDEO): ?>
                            <div class="form-group">
                                <label for="<?php echo e($editor->name . '_file'); ?>" class="control-label col-sm-1">上传视频:</label>
                                <div class="col-sm-11">
                                    <input id="<?php echo e($editor->name . '_file'); ?>" name="<?php echo e($editor->name . '_file'); ?>"
                                           type="file"
                                           class="file" data-upload-url="/admin/files/upload?type=video">
                                </div>
                            </div>
                            <script>
                                var <?php echo e($editor->name); ?>_preview = $('#<?php echo e($editor->name); ?>').val();
                                if (<?php echo e($editor->name); ?>_preview.length > 0) {
                                    <?php echo e($editor->name); ?>_preview = ['<video height="300" controls="controls" src="' + <?php echo e($editor->name); ?>_preview + '"></video>'];
                                }

                            </script>
                        <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_AUDIO): ?>
                            <div class="form-group">
                                <label for="<?php echo e($editor->name . '_file'); ?>" class="control-label col-sm-1">上传音频:</label>
                                <div class="col-sm-11">
                                    <input id="<?php echo e($editor->name . '_file'); ?>" name="<?php echo e($editor->name . '_file'); ?>"
                                           type="file"
                                           class="file" data-upload-url="/admin/files/upload?type=audio">
                                </div>
                            </div>
                            <script>
                                var <?php echo e($editor->name); ?>_preview = $('#<?php echo e($editor->name); ?>').val();
                                if (<?php echo e($editor->name); ?>_preview.length > 0) {
                                    <?php echo e($editor->name); ?>_preview = ['<audio height="100" controls="controls" src="' + <?php echo e($editor->name); ?>_preview + '"></audio>'];
                                }

                            </script>
                        <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_IMAGES): ?>
                            <div class="form-group">
                                <label for="image_file" class="control-label col-sm-1">上传图集:</label>
                                <div class=" col-sm-11">
                                    <input id="<?php echo e($editor->name . '_file'); ?>" name="<?php echo e($editor->name . '_file'); ?>[]"
                                           type="file" class="file file-loading"
                                           data-upload-url="/admin/files/upload?type=image" multiple>
                                </div>
                            </div>
                            <script>
                                var <?php echo e($editor->name); ?>_preview = [];
                                var <?php echo e($editor->name); ?>_config = [];
                                <?php if(isset($content)): ?>

                                <?php endif; ?>

                            </script>
                        <?php elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_VIDEOS): ?>
                            <div class="form-group">
                                <label for="image_file" class="control-label col-sm-1">上传视频:</label>
                                <div class=" col-sm-11">
                                    <input id="<?php echo e($editor->name . '_file'); ?>" name="<?php echo e($editor->name . '_file'); ?>[]"
                                           type="file" class="file file-loading"
                                           data-upload-url="/admin/files/upload?type=video" multiple>
                                </div>
                            </div>
                            <script>
                                var <?php echo e($editor->name); ?>_preview = [];
                                var <?php echo e($editor->name); ?>_config = [];
                                <?php if(isset($content)): ?>



                                <?php endif; ?>

                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php $index++ ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
</div>
<div class="box-footer">
    <button type="button" class="btn btn-default"
            onclick="location.href='<?php echo e(isset($back_url) ? $back_url : $base_url); ?>';"> 取　消
    </button>
    <button type="submit" class="btn btn-info pull-right" id="submit">保　存</button>
</div>

<script>
    $(document).ready(function () {
        $('.date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            locale: "zh-CN",
            toolbarPlacement: 'bottom',
            showClear: true,
        });

        $('#submit').click(function () {
            var ret = true;
            $('.file').each(function () {
                var files = $(this).fileinput('getFileStack');

                if (files.length > 0) {
                    return ret = toast('info', '请先上传文件!');
                }
            });

            return ret;
        });
    });


    $('.select2').select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });
</script>