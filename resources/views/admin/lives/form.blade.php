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
            {!! Form::label('title', '直播标题:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('title', null, ['class' => 'form-control']) !!}
            </div>
            {!! Form::label('stream_url', '直播地址:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('stream_url', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('cover_url', '封面图:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                <div class="col-sm-5">
                    <h5>您所选择的文件列表：</h5>
                    <div id="ossfile-cover"></div>
                    <br/>
                    <div class="show-oss-img-cover">
                        @if(!empty($live->cover_url))
                            <img src="{{ get_file_url($live->cover_url) }}" width="100px">
                        @endif
                    </div>
                    <div id="container-cover">
                        <a id="selectfiles_cover" href="javascript:void(0)"
                           class='oss-btn'>上传图片</a>
                        <div class="oss-img-uri-cover">
                            <input type="hidden" value="" class="cover_url">
                        </div>
                    </div>
                    <br/>
                </div>
                <input class="form-control" name="cover_url" id="cover_url" type="hidden"
                       value="{{ isset($live->cover_url) ? $live->cover_url :'' }}">
            </div>
            <input type="hidden" value="{{ $element }}" id="element" name="element">

            {!! Form::label('poster_url', '海报图:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                <div class="col-sm-5">
                    <h5>您所选择的文件列表：</h5>
                    <div id="ossfile-poster_url"></div>

                    <br/>
                    <div class="show-oss-img-poster_url">
                        @if(!empty($live->poster_show_url))
                            <img src="{{ get_file_url($live->poster_show_url) }}" width="100px">
                        @endif
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
                <input class="form-control" name="poster_url" id="poster_url" type="hidden"
                       value="{{ isset($live->poster_url) ? $live->poster_url :'' }}">
            </div>
        </div>
        <script>
            var cover_preview = $('#cover_url').val();
            if (cover_preview.length > 0) {
                cover_preview = ['<img height="240" src="' + cover_preview + '" class="kv-preview-data file-preview-image">'];
            }

            var poster_url_preview = $('#poster_url').val();
            if (poster_url_preview.length > 0) {
                poster_url_preview = ['<img height="240" src="' + poster_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            {!! Form::label('intro', '直播介绍:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::textarea('intro', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('start_at', '直播时间:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                <div class="input-group date">
                    {!! Form::text('start_at', null, ['class'=>'form-control']) !!}
                    <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
                </div>
            </div>

            {!! Form::label('duration', '直播时长:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('duration', null, ['class' => 'form-control', 'placeholder' => "（单位：分钟）"]) !!}
            </div>

            {!! Form::label('price', '价格:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('price', null, ['class' => 'form-control', 'placeholder' => "元"]) !!}
            </div>
        </div>

        <div class="form-group">

            {!! Form::label('member_id', '讲师：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::select('member_id', $member, null, ['class'=>'form-control']) !!}
            </div>

            {!! Form::label('state', '状态：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::select('state', $states, null, ['class'=>'form-control']) !!}
            </div>
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