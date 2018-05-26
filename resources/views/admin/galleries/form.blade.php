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
            {!! Form::label('name', '图集名：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('name', null, ['class'=>'form-control']) !!}
            </div>

            {!! Form::label('sort', '序号:：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('sort', null, ['class'=>'form-control']) !!}
            </div>

        </div>

        <div class="form-group">
            {!! Form::label('subname', '拥有者：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('subname', null, ['class'=>'form-control']) !!}
                <select id="_subname" class="js-data-example-ajax form-control" name="subname">
                </select>
            </div>
        </div>

        {!! Form::hidden('cover', null, ['id'=>'cover']) !!}

        <div class="form-group">
            {!! Form::label('cover', '封面图：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                <div id="show">
                    @if(!empty($gallery->cover))
                        <img id="show-cover" src="{{ config('site.oss.host')}}/{{ $gallery->cover }}" width="100">
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('intro', '详细描述：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-11">
                {!! Form::textarea('intro', null, ['class'=>'form-control', 'rows' =>'3']) !!}
            </div>
        </div>

        <script>
            var cover_preview = $('#cover').val();
            if (cover_preview.length > 0) {
                cover_preview = ['<img height="240" src="' + cover_preview + '" class="kv-preview-data file-preview-image">'];
            }
        </script>

        <div class="form-group">
            {!! Form::label('source', '来源：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('source', null, ['class'=>'form-control', 'readonly']) !!}
            </div>
            {!! Form::label('uploader_id', '上传者：', ['class'=>'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('uploader_id', null, ['class'=>'form-control', 'readonly']) !!}
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

    $("#_subname").append(new Option("{{ empty($gallery) ? '' : $gallery->subname }}", "{{ empty($gallery) ? '' : $gallery->subname }}", true, true));
    //远程筛选
    $('#_subname').select2({
        ajax: {
            url: "/admin/subname/lists",
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
            "<div class='select2-result-repository__avatar'><img src='{{ config('site.oss.host') }}/" + repo.cover + "' width='50'/></div>" +
            "<div class='select2-result-repository__title'>" + repo.subname + "</div>" +
            "</div>";

        return markup;
    }

    function formatRepoSelection(repo) {
        $('#_subname').val(repo.subname);
        $('#cover').val(repo.cover);
        if (repo.cover) {
            $('#show-cover').prop('src', '{{ config('site.oss.host') }}/' + repo.cover);
        }

        return repo.subname;
    }

</script>