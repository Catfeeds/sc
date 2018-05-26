<div class="form-group">
    {!! Form::label('username', '用户名:', ['class' => 'control-label col-sm-1']) !!}
    <div class="col-sm-5">
        {!! Form::text('username', null, ['class' => 'form-control']) !!}
    </div>
    {!! Form::label('name', '姓名:',['class' => 'control-label col-sm-1']) !!}
    <div class="col-sm-5">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label($password, '密码:', ['class' => 'control-label col-sm-1']) !!}
    <div class="col-sm-5">
        {!! Form::password($password, ['class' => 'form-control']) !!}
    </div>
    {!! Form::label('role_id', '选择角色:',['class' => 'control-label col-sm-1']) !!}
    <div class="col-sm-5">
        {!! Form::select('role_id', $roles, isset($user) ? $user->roles()->first()->id : '',['class' => 'form-control']) !!}
    </div>
</div>

<div class="box-footer">
    <button type="button" class="btn btn-default" onclick="window.history.back();">取　消</button>
    <button type="submit" class="btn btn-info pull-right">确　定</button>
</div>

<script>
    $(function () {
        $('#begin_time').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            locale: 'zh-cn'
        });
        $('#end_time').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            locale: 'zh-cn'
        });
    });
</script>