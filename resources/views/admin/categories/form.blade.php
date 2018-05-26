<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#tabHome" data-toggle="tab">基本信息</a>
    </li>
    <li>
        <a href="#tabContent" data-toggle="tab">正文</a>
    </li>
</ul>
<div id="tabContents" class="tab-content">
    <div id="tabHome" class="tab-pane fade in active padding-t-15">
        <div class="form-group">
            {!! Form::label('name', '栏目名称:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('description', '摘要:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::textarea('description', null, ['rows'=>'4','class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            <label for="image_url" class="control-label col-sm-1">缩略图:</label>
            <div class="col-sm-11">
                <input class="form-control" name="image_url" id="image_url" type="text" readonly
                       value="{{ isset($content->image_url) ? $content->image_url :'' }}">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-1">上传图片:</label>
            <div class="col-sm-11">
                <h4>您所选择的文件列表：</h4>
                <div id="ossfile-image_url"></div>

                <br/>
                <div class="show-oss-img-image_url">
                    @if(!empty($content->image_url))
                        <img src="{{ config('site.oss.host') }}/{{ $content->image_url }}" width="100px">
                    @endif
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
        <input type="hidden" value="{{ $element }}" id="element" name="element">

        <script>
            var image_url_preview = $('#image_url').val();
            if (image_url_preview.length > 0) {
                image_url_preview = ['<img height="240" src="' + image_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            <label for="cover_url" class="control-label col-sm-1">封面图:</label>
            <div class="col-sm-11">
                <input class="form-control" name="cover_url" id="cover_url" type="text" readonly
                       value="{{ isset($content->cover_url) ? $content->cover_url :'' }}">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-1">上传图片:</label>
            <div class="col-sm-11">
                <h4>您所选择的文件列表：</h4>
                <div id="ossfile-cover_url"></div>

                <br/>
                <div class="show-oss-img-cover_url">
                    @if(!empty($content->cover_url))
                        <img src="{{ config('site.oss.host') }}/{{ $content->cover_url }}" width="100px">
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
        <script>
            var cover_url_preview = $('#cover_url').val();
            if (cover_url_preview.length > 0) {
                cover_url_preview = ['<img height="240" src="' + cover_url_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

    </div>
    <div id="tabContent" class="tab-pane fade padding-t-15">
        <div class="form-group">
            <div class="col-sm-12">
                {!! Form::textarea('content', null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
</div>

<div id="tabGallery" class="tab-pane fade">
</div>
</div>

<div class="box-footer">
    <button type="button" class="btn btn-default" onclick="window.history.back();">取　消</button>
    <button type="submit" class="btn btn-info pull-right" id="submit">保　存</button>
</div><!-- /.box-footer -->
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
</script></script>