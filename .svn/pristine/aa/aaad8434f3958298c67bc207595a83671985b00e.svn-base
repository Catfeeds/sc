@foreach($_GET as $k => $v)
    <input type="hidden" id="{{ $k }}" name="{{ $k }}" value="{{ $v }}">
@endforeach
<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#tab_course_basic" data-toggle="tab">基本信息</a>
    </li>
</ul>

<div class="tab-content">
    <div id="tab_course_basic" class="tab-pane fade in active padding-t-15">

        <div class="form-group">
            {!! Form::label('name', '模块标题:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('link', '链接地址:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::text('link', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('type', '模块类型:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::select('type', ['轮播','广告'], null, ['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('image', '图片:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                <div class="col-sm-11">
                    <h5>（宽：335 * 高：140）</h5>
                    <div id="ossfile-image"></div>
                    <br/>
                    <div class="show-oss-img-image">
                        @if(!empty($content->image))
                            <img src="{{ get_file_url($content->image) }}" width="100px">
                        @endif
                    </div>
                    <div id="container-image">
                        <a id="selectfiles_image" href="javascript:void(0)"
                           class='oss-btn'>上传图片</a>
                        <div class="oss-img-uri-image">
                            <input type="hidden" value="" class="image">
                        </div>
                    </div>
                    <br/>
                </div>
                <input class="form-control" name="image" id="image" type="hidden"
                       value="{{ isset($content->image) ? $content->image :'' }}">
            </div>
            <input type="hidden" value="{{ $element }}" id="element" name="element">
        </div>
        <script>
            var image_preview = $('#image').val();
            if (image_preview.length > 0) {
                image_preview = ['<img height="240" src="' + image_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>
    </div>
</div>

<div class="box-footer">
    <button type="button" class="btn btn-default"
            onclick="location.href='{{ isset($back_url) ? $back_url : $base_url }}';"> 取　消
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