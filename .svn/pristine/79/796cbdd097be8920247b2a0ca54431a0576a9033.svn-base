@foreach($_GET as $k => $v)
    <input type="hidden" id="{{ $k }}" name="{{ $k }}" value="{{ $v }}">
@endforeach
<ul id="tabs" class="nav nav-tabs">
    @foreach($module->groups as $group)
        @if (count($group->editors) > 0)
            <li class="{{ $loop->first ? 'active' : '' }}">
                <a href="#{{ 'tab_' . $group->name }}" data-toggle="tab">{{ $group->name }}</a>
            </li>
        @endif
    @endforeach
</ul>

<div class="tab-content">
    @foreach($module->groups as $group)
        @if (count($group->editors) > 0)
            <div id="{{ 'tab_' . $group->name }}"
                 class="tab-pane fade in {{ $loop->first ? 'active' : '' }} padding-t-15">
                <?php $position = 0; $index = 0; ?>
                @foreach($group->editors as $editor)
                    @if ($editor->show)
                        @if ($position == 0)
                            <div class="form-group">
                                @endif
                                @if($editor->type == \App\Models\ModuleField::EDITOR_TYPE_HTML)
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::textarea($editor->name, null, ['class' => 'form-control']) !!}
                                    </div>
                                    <script>
                                        CKEDITOR.replace('{{ $editor->name }}', {
                                            height: '{{ $editor->rows * 20 }}',
                                            filebrowserUploadUrl: '/admin/files/upload?type=image&_token={{  csrf_token() }}'
                                        });
                                    </script>
                                @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_DATETIME)
                                    {!! Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']) !!}
                                    <div class="col-sm-{{ $editor->columns }}">
                                        <div class='input-group date'>
                                            {!! Form::text($editor->name, null, ['class' => 'form-control']) !!}
                                            <span class="input-group-addon"> <span
                                                        class="glyphicon glyphicon-calendar"></span> </span>
                                        </div>
                                    </div>
                                @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_SELECT_SINGLE)
                                    {!! Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']) !!}
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::select($editor->name, string_to_option($editor->options), null, ['class' => 'form-control', $editor->readonly ? 'readonly' : '']) !!}
                                    </div>
                                @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_SELECT_MULTI)
                                    {!! Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']) !!}
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::select("$editor->name[]", array_to_option($editor->options), array_to_option($editor->selected)?array_to_option($editor->selected):'', ['class' => 'form-control select2','multiple'=>'multiple']) !!}
                                    </div>
                                @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_TEXTAREA)
                                    {!! Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']) !!}
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::textarea($editor->name, null, ['class' => 'form-control', 'rows' => $editor->rows, $editor->readonly ? 'readonly' : '']) !!}
                                    </div>
                                @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_IMAGES)
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::hidden($editor->name, null, ['class' => 'form-control', 'id' => $editor->name]) !!}
                                    </div>
                                @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_VIDEOS)
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::hidden($editor->name, null, ['class' => 'form-control', 'id' => $editor->name]) !!}
                                    </div>

                                @else
                                    {!! Form::label($editor->name, $editor->label . ':', ['class' => 'control-label col-sm-1']) !!}
                                    <div class="col-sm-{{ $editor->columns }}">
                                        {!! Form::text($editor->name, null, ['class' => 'form-control', $editor->required ? 'required' : '', $editor->readonly ? 'readonly' : '']) !!}
                                    </div>
                                @endif
                                <?php $position += $editor->columns + 1; if ($loop->last || $position + $group->editors[$index + 1]->columns + 1 > 12) {
                                    $position = 0;
                                } ?>
                                @if($position == 0 || $position == 12)
                            </div>
                        @endif

                        @if($editor->type == \App\Models\ModuleField::EDITOR_TYPE_IMAGE)
                            <div class="form-group">
                                <label class="control-label col-sm-1">上传图片:</label>
                                <div class="col-sm-11">
                                    <h4>您所选择的文件列表：</h4>
                                    <div id="ossfile-{{ $editor->name }}"></div>

                                    <br/>
                                    <div class="show-oss-img-{{ $editor->name }}"></div>
                                    <div id="container-{{ $editor->name }}">
                                        <a id="selectfiles_{{ $editor->name }}" href="javascript:void(0)"
                                           class='oss-btn'>上传图片</a>
                                        <div class="oss-img-uri-{{ $editor->name }}">
                                            <input type="hidden" value="" class="{{ $editor->name }}">
                                        </div>
                                    </div>
                                    <br/>
                                </div>
                            </div>
                            <script>
                                var {{ $editor->name }}_preview = $('#{{ $editor->name }}').val();
                                if ({{ $editor->name }}_preview.length > 0) {
                                    {{ $editor->name }}_preview = ['<img height="240" src="' + {{ $editor->name }}_preview + '" class="kv-preview-data file-preview-image">'];
                                }
                            </script>
                        @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_VIDEO)
                            <div class="form-group">
                                <label for="{{ $editor->name . '_file' }}" class="control-label col-sm-1">上传视频:</label>
                                <div class="col-sm-11">
                                    <input id="{{ $editor->name . '_file' }}" name="{{ $editor->name . '_file' }}"
                                           type="file"
                                           class="file" data-upload-url="/admin/files/upload?type=video">
                                </div>
                            </div>
                            <script>
                                var {{ $editor->name }}_preview = $('#{{ $editor->name }}').val();
                                if ({{ $editor->name }}_preview.length > 0) {
                                    {{ $editor->name }}_preview = ['<video height="300" controls="controls" src="' + {{ $editor->name }}_preview + '"></video>'];
                                }

                            </script>
                        @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_AUDIO)
                            <div class="form-group">
                                <label for="{{ $editor->name . '_file' }}" class="control-label col-sm-1">上传音频:</label>
                                <div class="col-sm-11">
                                    <input id="{{ $editor->name . '_file' }}" name="{{ $editor->name . '_file' }}"
                                           type="file"
                                           class="file" data-upload-url="/admin/files/upload?type=audio">
                                </div>
                            </div>
                            <script>
                                var {{ $editor->name }}_preview = $('#{{ $editor->name }}').val();
                                if ({{ $editor->name }}_preview.length > 0) {
                                    {{ $editor->name }}_preview = ['<audio height="100" controls="controls" src="' + {{ $editor->name }}_preview + '"></audio>'];
                                }

                            </script>
                        @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_IMAGES)
                            <div class="form-group">
                                <label for="image_file" class="control-label col-sm-1">上传图集:</label>
                                <div class=" col-sm-11">
                                    <input id="{{ $editor->name . '_file' }}" name="{{ $editor->name . '_file' }}[]"
                                           type="file" class="file file-loading"
                                           data-upload-url="/admin/files/upload?type=image" multiple>
                                </div>
                            </div>
                            <script>
                                var {{ $editor->name }}_preview = [];
                                var {{ $editor->name }}_config = [];
                                @if(isset($content))

                                @endif

                            </script>
                        @elseif($editor->type == \App\Models\ModuleField::EDITOR_TYPE_VIDEOS)
                            <div class="form-group">
                                <label for="image_file" class="control-label col-sm-1">上传视频:</label>
                                <div class=" col-sm-11">
                                    <input id="{{ $editor->name . '_file' }}" name="{{ $editor->name . '_file' }}[]"
                                           type="file" class="file file-loading"
                                           data-upload-url="/admin/files/upload?type=video" multiple>
                                </div>
                            </div>
                            <script>
                                var {{ $editor->name }}_preview = [];
                                var {{ $editor->name }}_config = [];
                                @if(isset($content))



                                @endif

                            </script>
                        @endif
                    @endif
                    <?php $index++ ?>
                @endforeach
            </div>
        @endif
    @endforeach
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