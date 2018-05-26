<script src="{{ url('js/zoom/zoom.min.js') }}"></script>
<script>
    $('#table').bootstrapTable({
        method: 'get',
        url: '/admin/members/certify/table',
        pagination: true,
        pageNumber: 1,
        pageSize: 20,
        pageList: [10, 25, 50, 100],
        sidePagination: 'server',
        clickToSelect: true,
        striped: true,
        queryParams: function (params) {
            var object = $('#form_query input,#form_query select').serializeObject();
            object['state'] = $('#state').val();
            object['_token'] = '{{ csrf_token() }}';
            object['offset'] = params.offset;
            object['limit'] = params.limit;
            return object;
        },
    });

    function stateFormatter(value, row, index) {
        var style = 'label-primary';
        switch (row.state_name) {
            case '待审核':
                style = 'label-primary';
                break;
            case '审核通过':
                style = 'label-success';
                break;
            case '拒绝认证':
                style = 'label-warning';
                break;
        }
        return [
            '<span class="label ' + style + '">' + row.state_name + '</span>',
        ].join('');
    }

    function actionFormatter(value, row, index) {

        //通过
        var html = '<button class="btn btn-primary btn-xs margin-r-5 pass" data-placement="top" title="通过认证"><i class="glyphicon glyphicon-ok"></i></button>';

        //拒绝
        html += '<button class="btn btn-primary btn-xs margin-r-5 refuse" data-toggle="modal"  data-target="#modal_certify" title="拒绝认证"><i class="glyphicon glyphicon-remove"></i></button>';

        return html;
    }

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        locale: "zh-CN",
        toolbarPlacement: 'bottom',
        showClear: true,
    });

    window.actionEvents = {

        'click .pass': function (e, value, row) {
            $.ajax({
                url: '/admin/members/certify/state',
                type: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    id: row.id,
                    reason: '',
                    state: {{ \App\Models\Certification::STATE_SUCCESS }}},
                success: function (data) {
                    $('#table').bootstrapTable('refresh');
                },
                error: function () {
                    toast('error', '操作失败');
                }
            })
        },

        'click .refuse': function (e, value, row) {
            $('#modal_title').text('教师认证拒绝原因');
            $('#window_msg').hide();

            $.ajax({
                url: '/admin/members/certify/refuse',
                type: "get",
                data: {'_token': '{{ csrf_token() }}', id: row.id},
                dataType: 'html',
                success: function (html) {
                    $('#contents').html(html);
                }
            });
        },

    };

    /* 筛选 */
    $('.filter').click(function () {
        var value = $(this).val();
        $('#state').val(value);
        $('#table').bootstrapTable('selectPage', 1);

        //改变按钮样式
        $('.filter').removeClass('btn-primary btn-info btn-success btn-danger btn-warning');
        $('.filter').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass($(this).data('active'));
    });

    /* 放大教师手持身份证照片 */
    function photoFormatter(value, row) {
        return [
            '<a href="' + row.card_photo + '" target="_blank"><img src="' + row.card_photo_thumb + '" alt=""/></a>'
        ].join('');
    }

</script>