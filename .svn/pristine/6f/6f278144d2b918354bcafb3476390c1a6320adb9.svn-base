<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-body">
                <div class="col-sm-12" style="padding: 0px 0px 30px 0px;">
                    {!! Form::textarea('reason', null, ['id'=>'reason','class' => 'form-control', 'rows' => '4']) !!}
                </div>
                <button type="submit" class="btn btn-lg btn-info btn-block center-block submit" onclick="confirm()">
                    提交
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function confirm() {
        toastr.options = {
            'closeButton': true,
            'showDuration': 100,
            'hideDuration': 0,
            'timeOut': 0,
            'extendedTimeOut': 0,
            'positionClass': 'toast-top-center',
        };
        toastr['info']('您确定提交吗？&nbsp;&nbsp;&nbsp;<span onclick="commit();" style="text-decoration: underline;">确定</span>');
    }

    function commit() {
        var reason = $.trim($('#reason').val());
        if (reason == '') {
            toast('warning', '请输入拒绝认证原因，再提交！');
            return false;
        }

        $.ajax({
            url: "/admin/members/certify/state",
            type: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id: {{ $id }},
                reason: reason,
                state: {{ \App\Models\Certification::STATE_FAILURE }}
            },
            success: function (data) {
                if (data.status_code == 200) {
                    $('#table').bootstrapTable('selectPage', 1);
                    $('#table').bootstrapTable('refresh', {silent: true});
                    $('#reason').val('');
                    toast('success', '拒绝成功！');

                } else {
                    toast('error', data.message);
                }
            },
            error: function () {
                toast('warning', '系统繁忙！');
            }
        });
    }

</script>