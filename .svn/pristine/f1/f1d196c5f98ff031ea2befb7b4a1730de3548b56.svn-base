<script>
    var btn = $('.node-tree');
    btn.click(function () {
        $(this).attr('style', 'color:#FFFFFF;background-color:#428bca;');
        $(this).siblings('.node-tree').attr('style', '');
        var select = $(this).children('i').text();

        $.ajax({
            type: 'get',
            async: false,
            url: '/admin/system/logs/table',
            data: {
                file_name: select
            },
            success: function (data) {
                $('#table').text(data);
            }
        });
    })
</script>