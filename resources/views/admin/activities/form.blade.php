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
            {!! Form::label(null, '活动标题:', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('title', null, ['class'=>'form-control']) !!}
            </div>

            {!! Form::label(null, '外链：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('web_url', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('start_at', '开始时间:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                <div class="input-group date">
                    {!! Form::text('start_at', null, ['class'=>'form-control', 'id'=>'start_at']) !!}
                    <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
                </div>
            </div>

            {!! Form::label('end_at', '结束时间:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                <div class="input-group date">
                    {!! Form::text('end_at', null, ['class'=>'form-control', 'id'=>'end_at']) !!}
                    <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-11">
                {!! Form::hidden('cover_url', null, ['class'=>'form-control', 'readonly', 'id'=>'cover_url']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('cover_url', '封面图:', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-cover_url"></div>
                <br/>
                <div class="show-oss-img-cover_url">
                    @if(!empty($content->cover_url))
                        <img src="{{ get_file_url($content->cover_url) }}" width="100px">
                    @endif
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
        <input type="hidden" value="{{ $element }}" id="element" name="element">

        <script>
            var cover_url_preview = $('#cover_url').val();
            if (cover_url_preview.length > 0) {
                cover_url_preview = ['<img height="240" src="' + cover_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            {!! Form::label(null, '正文：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::textarea('content', null, ['class' => 'form-control', 'rows' => 10]) !!}
            </div>
            <script>
                CKEDITOR.replace('content', {
                    height: '400',
                    filebrowserUploadUrl: '/admin/files/upload?type=image&_token={{  csrf_token() }}'
                });
            </script>
        </div>

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