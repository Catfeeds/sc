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
            {!! Form::label('user_id', '编号：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('user_id', $user_id, ['class' => 'form-control', 'readonly']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name', '姓名：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>

            {!! Form::label('sex', '性别：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::select('sex', ['男','女'],null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('age', '年龄：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('age', null, ['class' => 'form-control']) !!}
            </div>

            {!! Form::label('mobile', '手机号：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::text('mobile', null, ['class' => 'form-control']) !!}
            </div>


            {!! Form::label('grade_id', '班级：', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-2">
                {!! Form::select('grade_id', $grades, null, ['class' => 'form-control']) !!}
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