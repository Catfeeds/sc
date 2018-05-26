@foreach($_GET as $k => $v)
    <input type="hidden" id="{{ $k }}" name="{{ $k }}" value="{{ $v }}">
@endforeach
<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#tab_image_basic" data-toggle="tab">基本信息</a>
    </li>
</ul>

<div class="tab-content">
    <div id="tab_image_basic" class="tab-pane fade in active padding-t-15">
        <div class="form-group">
            {!! Form::label('title', '标题：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('title', null, ['class'=>'form-control']) !!}
            </div>
            {!! Form::label('author', '作者：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('author', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('tag', '标签：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                @foreach($tags as $key=>$value)
                    <input type="checkbox" name="tag[]" value="{{ $value->id }}" class="fl is_block"
                           @if(!empty($content->tags))
                           @foreach($content->tags as $_key => $_value)
                           @if($value->id == $_value)
                           checked="checked"
                            @endif
                            @endforeach
                            @endif
                    >
                    {!! Form::label('tag', $value->name, ['class' => 'control-label has_space fl is_block']) !!}
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-11">
                {!! Form::hidden('url', null, ['class'=>'form-control', 'readonly', 'id'=>'url']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('url', '图片：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                <h5>您所选择的文件列表：</h5>
                <div id="ossfile-url"></div>

                <br/>
                <div class="show-oss-img-url">
                    @if(!empty($content->img_url))
                        <img src="{{ get_file_url($content->img_url) }}" width="100px">
                    @endif
                </div>
                <div id="container-url">
                    <a id="selectfiles_url" href="javascript:void(0)"
                       class='oss-btn'>上传图片</a>
                    <div class="oss-img-uri-url">
                        <input type="hidden" value="" class="url">
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <input type="hidden" value="{{ $element }}" id="element" name="element">
        <script>
            var url_preview = $('#url').val();
            if (url_preview.length > 0) {
                url_preview = ['<img height="240" src="' + url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            {!! Form::label('view_num', '浏览量：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('view_num', null, ['class'=>'form-control']) !!}
            </div>
            {!! Form::label('comment_num', '评论数：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('comment_num', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('like_num', '点赞数：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('like_num', null, ['class'=>'form-control']) !!}
            </div>
            {!! Form::label('collect_num', '收藏数：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('collect_num', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('intro', '详细描述：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::textarea('intro', null, ['class'=>'form-control', 'rows'=>'3', 'cols'=>'50']) !!}
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