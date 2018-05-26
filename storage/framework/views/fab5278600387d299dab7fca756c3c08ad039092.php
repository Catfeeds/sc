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
            <?php echo Form::label('name', '图集名：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('name', null, ['class'=>'form-control']); ?>

            </div>

            <?php echo Form::label('sort', '序号:：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('sort', null, ['class'=>'form-control']); ?>

            </div>

        </div>

        <div class="form-group">
            <?php echo Form::label('subname', '拥有者：', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <select id="subname" class="js-data-example-ajax form-control" name="subname">
                    <option value="0" selected="selected">请选择</option>
                </select>
            </div>
        </div>

        <?php echo Form::hidden('subname', null, ['id'=>'_subname']); ?>

        <?php echo Form::hidden('cover', null, ['id'=>'cover']); ?>


        <div class="form-group">
            <?php echo Form::label('cover', '封面图：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <div id="show">
                    <?php if(!empty($content->cover)): ?>
                        <img id="show-cover" src="<?php echo e(config('site.oss.host').'/'.$content->cover); ?>" width="100">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('intro', '详细描述：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::textarea('intro', null, ['class'=>'form-control', 'rows' =>'3']); ?>

            </div>
        </div>

        <script>
            var cover_preview = $('#cover').val();
            if (cover_preview.length > 0) {
                cover_preview = ['<img height="240" src="' + cover_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            <?php echo Form::label('source', '来源：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('source', null, ['class'=>'form-control', 'readonly']); ?>

            </div>
            <?php echo Form::label('uploader_id', '上传者：', ['class'=>'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('uploader_id', null, ['class'=>'form-control', 'readonly']); ?>

            </div>
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

    //远程筛选
    $("#subname").select2({
        ajax: {
            url: "/subname/lists",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {

                params.page = params.page || 1;

                var select2Data = $.map(data, function (obj) {
                    return obj;
                });

                return {
                    results: select2Data,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true
        },

        escapeMarkup: function (markup) {
            //console.log(markup)
            return markup;
        }, // let our custom formatter work
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.name;
        }

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'><img src='<?php echo e(config('site.oss.host')); ?>/" + repo.cover + "' width='50'/></div>" +
            "<div class='select2-result-repository__title'>" + repo.subname + "</div>" +
            "</div>";

        return markup;
    }

    function formatRepoSelection(repo) {
        $('#_subname').val(repo.subname);
        $('#cover').val(repo.cover);
        console.log(repo.cover);
        $('#show-cover').prop('src', '<?php echo e(config('site.oss.host')); ?>/' + repo.cover);

        return repo.subname;
    }

    function selector(element, placeHolder, multiple, initial, allowClear) {
        if (initial !== false) initial = initial || JSON.parse(element.val());
        element.select2({
            ajax: {
                url: element.data('matchUrl'),
                dataType: 'json',
                quietMillis: 100,
                data: function (term, page) {
                    return {
                        q: term,
                        page_limit: 10
                    };
                },
                results: function (data) {
                    var results = [];
                    $.each(data, function (index, item) {
                        results.push({
                            id: item.id,
                            name: item.name
                        });
                    });

                    return {
                        results: results
                    };
                }
            },
            initSelection: function (element, callback) {
                if (!initial) return;
                if (multiple) {
                    element.val('');
                } else {
                    element.val(initial.id);
                }
                callback(initial);
            },
            formatSelection: function (item) {
                return item.name;
            },
            formatResult: function (item) {
                return item.name;
            },
            placeholder: placeHolder,
            width: 'off',
            multiple: multiple || false,
            maximumSelectionSize: 20,
            allowClear: allowClear || allowClear === undefined
        });
    }

</script>