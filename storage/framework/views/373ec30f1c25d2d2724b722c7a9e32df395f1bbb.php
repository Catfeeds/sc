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
            <?php echo Form::label('title', '标题：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('title', null, ['class'=>'form-control']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('subtitle', '子标题：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('subtitle', null, ['class'=>'form-control']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('intro', '简介：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::textarea('intro', null, ['class'=>'form-control', 'rows' =>'3']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('type', '课程难度：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::select('type', string_to_option('初级,中级,高级'), null, ['class'=>'form-control']); ?>

            </div>
            <?php echo Form::label('member_id', '讲师：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-2">
                <?php echo Form::select('member_id', $member, null, ['class'=>'form-control']); ?>

            </div>

            <?php echo Form::label(null, '是否免费', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-1">
                <?php echo e(Form::radio('is_free', '1', null, ['class'=>'', 'id'=>'isfree'])); ?> 是
                <?php echo e(Form::radio('is_free', '0', null, ['class'=>'', 'id'=>'nofree'])); ?> 否
            </div>

            <div id="prices"
                 <?php if(isset($content)): ?>
                 style="display:<?php echo e($content->is_free ? "none" : "block"); ?>"
                 <?php else: ?>
                 style="display: none;"
                    <?php endif; ?>
            >
                <?php echo Form::label(null, '课程价格：', ['class'=>'control-label col-sm-1']); ?>

                <div class="col-sm-2">
                    <?php echo Form::text('price', null, ['class'=>'form-control', 'placeholder'=>"单位（元）", 'id'=>'price']); ?>

                </div>
            </div>

            <script>
                var nofree = $('#nofree');
                var isfree = $('#isfree');
                var prices = $('#prices');
                var price = $('#price');

                        <?php if(isset($content)): ?>
                        <?php echo e($price = $content->price); ?>

                var old = <?php echo e($price); ?>

                        <?php endif; ?>

                    nofree.click(function () {
                        prices.css('display', 'block');
                    })

                isfree.click(function () {
                    prices.css('display', 'none');
                    price.val(old);
                })
            </script>
        </div>
        <div class="form-group">
            <div class="col-sm-11">
                <?php echo Form::hidden('cover_url', null, ['class'=>'form-control', 'readonly', 'id'=>'cover_url']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('cover_url', '封面图：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-cover_url"></div>

                <br/>
                <div class="show-oss-img-cover_url">
                    <?php if(isset($content)): ?>
                        <img src="<?php echo e(empty($content->cover_url) ? '' : get_file_url($content->cover_url)); ?>"
                             width="100px">
                    <?php endif; ?>
                </div>
                <div id="container-cover_url">
                    <a id="selectfiles_cover_url" href="javascript:void(0)"
                       class='oss-btn'>上传图片</a>
                    <div class="oss-img-uri-cover_url">
                        <input type="hidden" value="" class="cover_url">
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <input type="hidden" value="<?php echo e($element); ?>" id="element" name="element">
        <script>
            var cover_url_preview = $('#cover_url').val();
            if (cover_url_preview.length > 0) {
                cover_url_preview = ['<img height="240" src="' + cover_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            <div class="col-sm-11">
                <?php echo Form::hidden('poster_url', null, ['class'=>'form-control', 'readonly', 'id'=>'poster_url']); ?>

            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('poster_url', '海报图：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-poster_url"></div>

                <br/>
                <div class="show-oss-img-poster_url">
                    <?php if(!empty($content->poster_show_url)): ?>
                        <img src="<?php echo e(get_file_url($content->poster_show_url)); ?>" width="100px">
                    <?php endif; ?>
                </div>
                <div id="container-poster_url">
                    <a id="selectfiles_poster_url" href="javascript:void(0)"
                       class='oss-btn'>上传图片</a>
                    <div class="oss-img-uri-poster_url">
                        <input type="hidden" value="" class="poster_url">
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <script>
            var poster_url_preview = $('#poster_url').val();
            if (poster_url_preview.length > 0) {
                poster_url_preview = ['<img height="240" src="' + poster_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

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