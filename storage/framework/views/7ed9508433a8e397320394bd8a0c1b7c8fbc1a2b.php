<table id="table" data-toggle="table">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="trues"></th>
        <th data-field="id" data-align="center" data-width="50">ID</th>
        <th data-field="name" data-formatter="titleFormatter">标签名称</th>
        <th data-field="type_name">标签类型</th>
        <th data-field="created_at" data-align="center" data-width="120">创建时间</th>
        <th data-field="updated_at" data-align="center" data-width="120">更新时间</th>
        <th data-field="action" data-align="center" data-width="192" data-formatter="actionFormatter"
            data-events="actionEvents">操作
        </th>
    </tr>
    </thead>
</table>
<script>
    $('#table').bootstrapTable({
        method: 'get',
        url: '<?php echo e($base_url); ?>' + '/table',
        pagination: true,
        pageSize: 25,
        pageList: [25, 50, 100, 200],
        sidePagination: 'server',
        clickToSelect: true,
        striped: true,
        onLoadSuccess: function (data) {
            $('#btn_sort').removeClass('active');
            $('#btn_sort').text('排序');
            $('#modal_query').modal('hide');
            $('#table tbody').sortable({
                cursor: 'move',
                axis: 'y',
                revert: true,
                start: function (e, ui) {
                    select_index = ui.item.attr('data-index');
                    original_y = e.pageY;
                },
                sort: function (e, ui) {
                    if (e.pageY > original_y) {
                        place_index = $(this).find('tr').filter('.ui-sortable-placeholder').prev('tr').attr('data-index');
                        move_down = 1;
                    }
                    else {
                        place_index = $(this).find('tr').filter('.ui-sortable-placeholder').next('tr').attr('data-index');
                        move_down = 0;
                    }
                },
                update: function (e, ui) {
                    var select_id = data.rows[select_index].id;
                    var place_id = data.rows[place_index].id;

                    if (select_id == place_id) {
                        return;
                    }

                    $.ajax({
                        url: '<?php echo e($base_url); ?>' + '/sort',
                        type: 'get',
                        async: true,
                        data: {select_id: select_id, place_id: place_id, move_down: move_down},
                        success: function (data) {
                            if (data.status_code != 200) {
                                $('#table tbody').sortable('cancel');
                                $('#table').bootstrapTable('refresh');
                            }
                        },
                    });
                }
            });
            $('#table tbody').sortable('disable');
        },
        onEditableSave: function (field, row, old, $el) {
            updateRow(field, row, old, $el);
        },
        queryParams: function (params) {
            var object = $('#form_query input,#form_query select').serializeObject();
            object['state'] = $('#state').val();
            object['offset'] = params.offset;
            object['limit'] = params.limit;
            return object;
        },
    });

</script>