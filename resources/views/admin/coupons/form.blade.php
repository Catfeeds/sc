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
            {!! Form::label('num', '生成个数:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-3">
                {!! Form::text('num', null, ['class' => 'form-control']) !!}
            </div>
            {!! Form::label('deadline', '失效时间:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-3">
                <div class="input-group date">
                    {!! Form::text('deadline', null, ['class'=>'form-control']) !!}
                    <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('refer_type', '适用类型:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-3">
                {!! Form::select('refer_type', \App\Models\Coupon::REFER_TYPES, null, ['class'=>'form-control']) !!}
            </div>
            {!! Form::label('type', '优惠方式:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-3">
                {!! Form::select('type', \App\Models\Coupon::TYPES, null, ['class'=>'form-control']) !!}
            </div>
            {!! Form::label('discount', '打折率/抵价金额:', ['class' => 'control-label col-sm-1']) !!}
            <div class="col-sm-3">
                {!! Form::text('discount', null, ['class' => 'form-control', 'placeholder' => '请根据优惠方式填写']) !!}
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
    });

</script>