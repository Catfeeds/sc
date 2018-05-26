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
            {!! Form::label('name', '班级名称：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-5">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('student_num', '学生人数：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('student_num', null, ['class' => 'form-control']) !!}
            </div>

            {!! Form::label('opened_at', '开班日期:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                <div class="input-group date">
                    {!! Form::text('opened_at', null, ['class'=>'form-control']) !!}
                    <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
                </div>
            </div>
        </div>

        @can('create-grade')
            <div class="form-group">
                {!! Form::label('school_id', '所属学校：', ['class' => 'control-label col-sm-1']) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('member_id', '班主任：', ['class' => 'control-label col-sm-1']) !!}
                <div class="col-sm-2">
                    {!! Form::select('member_id', $members, null, ['class' => 'form-control']) !!}
                </div>
            </div>
        @endcan

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
    });
</script>