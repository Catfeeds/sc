<?php $__currentLoopData = $_GET; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <input type="hidden" id="<?php echo e($k); ?>" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>

<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#tabHome" data-toggle="tab">基本信息</a>
    </li>

    <li>
        <a href="#tabAndroid" data-toggle="tab">android</a>
    </li>

    <li>
        <a href="#tabIos" data-toggle="tab">ios</a>
    </li>
</ul>

<div id="tabContents" class="tab-content">
    <div id="tabHome" class="tab-pane fade in active padding-t-15">
        <div class="form-group">
            <?php echo Form::label('name', '名称：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('name', null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <?php echo Form::hidden('logo_url', null, ['class'=>'form-control', 'readonly', 'id'=>'logo_url']); ?>


        <div class="form-group">
            <?php echo Form::label('logo_url', 'logo地址：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-logo_url"></div>
                <br/>
                <div class="show-oss-img-logo_url">
                    <?php if(isset($apps)): ?>
                        <img src="<?php echo e(empty($apps->logo_url) ? '' : get_file_url($apps->logo_url)); ?>"
                             width="100px">
                    <?php endif; ?>
                </div>
                <div id="container-logo_url">
                    <a id="selectfiles_logo_url" href="javascript:void(0)"
                       class='oss-btn'>上传图片</a>
                    <div class="oss-img-uri-logo_url">
                        <input type="hidden" value="" class="logo_url">
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <input type="hidden" value="<?php echo e($element); ?>" id="element" name="element">
        <script>
            var logo_url_preview = $('#logo_url').val();
            if (logo_url_preview.length > 0) {
                logo_url_preview = ['<img height="240" src="' + logo_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>
        <div class="form-group">
            <?php echo Form::label(null, '版本状态：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::select('state', $states, null, ['class'=>'form-control']); ?>

            </div>
        </div>
    </div>

    <div id="tabAndroid" class="tab-pane fade padding-t-15">
        <div class="form-group">
            <?php echo Form::label('android_version', '版本号：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('android_version', null, ['class'=>'form-control']); ?>

            </div>
            <?php echo Form::label('android_force', '强制更新：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::select('android_force',['是','否'], null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <?php echo Form::hidden('android_url', null, ['class'=>'form-control', 'readonly', 'id'=>'android_url']); ?>


        <div class="form-group">
            <?php echo Form::label('android_url', '程序地址：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-android_url"></div>
                <br/>
                <div class="show-oss-img-android_url">
                </div>
                <div id="container-android_url">
                    <a id="selectfiles_android_url" href="javascript:void(0)"
                       class='oss-btn'>上传程序</a>
                    <div class="oss-img-uri-android_url">
                        <input type="hidden" value="" class="android_url">
                    </div>
                </div>
                <br/>
            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label(null, '更新说明：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::textarea('android_content', null, ['class' => 'form-control', 'rows' => 6, 'id'=>'android_content']); ?>

            </div>
        </div>
    </div>

    <div id="tabIos" class="tab-pane fade padding-t-15">
        <div class="form-group">
            <?php echo Form::label('ios_version', '版本号：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('ios_version', null, ['class'=>'form-control']); ?>

            </div>
            <?php echo Form::label('ios_force', '强制更新：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::select('ios_force',['是','否'], null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('ios_url', '程序地址：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('ios_url', null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label(null, '更新说明：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::textarea('ios_content', null, ['class' => 'form-control', 'rows' => 6]); ?>

            </div>
        </div>
    </div>
</div>

<div class="box-footer">
    <button type="button" class="btn btn-default"
            onclick="location.href='admin/apps';"> 取　消
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