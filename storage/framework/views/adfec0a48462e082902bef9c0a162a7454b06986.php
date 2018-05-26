<?php $__currentLoopData = $_GET; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <input type="hidden" id="<?php echo e($k); ?>" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#tab_course_basic" data-toggle="tab">基本信息</a>
    </li>
    <li class="">
        <a href="#tab_course_img" data-toggle="tab">正文信息</a>
    </li>
</ul>

<div class="tab-content">
    <div id="tab_course_basic" class="tab-pane fade in active padding-t-15">
        <div class="form-group">
            <?php echo Form::label('title', '文章标题:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('title', null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('subtitle', '副标题:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('subtitle', null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('type', '类型:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::select('type', ['小图','多图','大图'], null, ['class'=>'form-control']); ?>

            </div>

            <?php echo Form::label('author', '作者:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::text('author', null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('origin', '内容来源:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-6">
                <?php echo Form::text('origin', null, ['class'=>'form-control']); ?>

            </div>

            <?php echo Form::label('tags', '标签:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-4">
                <?php echo Form::text('tags', null, ['class'=>'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('summary', '摘要:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::textarea('summary', null, ['class'=>'form-control', 'cols'=>'50', 'rows'=>'4']); ?>

            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-11">
                <?php echo Form::hidden('image_url', null, ['class'=>'form-control', 'readonly', 'id'=>'image_url']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('image_url', '缩略图:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-image_url"></div>
                <br/>
                <div class="show-oss-img-image_url">
                    <?php if(!empty($content->image_url)): ?>
                        <img src="<?php echo e(get_file_url($content->image_url)); ?>" width="100px">
                    <?php endif; ?>
                </div>
                <div id="container-image_url">
                    <a id="selectfiles_image_url" href="javascript:void(0)"
                       class='oss-btn'>上传图片</a>
                    <div class="oss-img-uri-image_url">
                        <input type="hidden" value="" class="image_url">
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <input type="hidden" value="<?php echo e($element); ?>" id="element" name="element">

        <script>
            var image_url_preview = $('#image_url').val();
            if (image_url_preview.length > 0) {
                image_url_preview = ['<img height="240" src="' + image_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            <div class="col-sm-11">
                <?php echo Form::hidden('video_url', null, ['class'=>'form-control', 'readonly', 'id'=>'video_url']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('video_url', '视频:', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-video_url"></div>
                <br/>
                <div class="show-oss-img-video_url">
                    <?php if(!empty($content->video_url)): ?>
                        <img src="<?php echo e(get_file_url($content->video_url)); ?>" width="100px">
                    <?php endif; ?>
                </div>
                <div id="container-video_url">
                    <a id="selectfiles_video_url" href="javascript:void(0)"
                       class='oss-btn'>上传视频</a>
                    <div class="oss-img-uri-video_url">
                        <input type="hidden" value="" class="video_url">
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <script>
            var video_url_preview = $('#video_url').val();
            if (video_url_preview.length > 0) {
                video_url_preview = ['<img height="240" src="' + video_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

    </div>

    <div id="tab_course_img" class="tab-pane fade  padding-t-15 in">
        <div class="form-group">
            <div class="col-sm-12">
                <?php echo Form::textarea('content', null, ['class' => 'form-control', 'rows' => 10]); ?>

            </div>
            <script>
                CKEDITOR.replace('content', {
                    height: '600',
                    filebrowserUploadUrl: '/admin/files/upload?type=image&_token=<?php echo e(csrf_token()); ?>'
                });
            </script>
        </div>


    </div>

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