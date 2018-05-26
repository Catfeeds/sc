<script src="/oss-upload/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script src="/oss-upload/uploader.js"></script>
<script src="/oss-upload/upload.js"></script>
<script>
    var element = $('#element').val();

    var element = jQuery.parseJSON(element);
    var uploaderId = "{{ Auth::user()->id }}";
    var module = "{{ $module_name }}";
    var host = "{{ config('site.oss.host') }}";
    var options = {
        multiple: true,
        max_file_size: '20mb',
        mime_types: [ //只允许上传图片
            {title: "Image files", extensions: "jpg,gif,png,bmp"}
        ],
    };

    var val_name0 = $('#selectfiles_' + element[0].name).siblings('.oss-img-uri-' + element[0].name).children().attr('class');
    var val_name1 = $('#selectfiles_' + element[1].name).siblings('.oss-img-uri-' + element[1].name).children().attr('class');
    var elementId0 = "selectfiles_" + element[0].name;
    var elementId1 = "selectfiles_" + element[1].name;
    var element_item0 = element[0].item;
    var element_item1 = element[1].item;
    var ossfileid0 = "ossfile-" + element[0].name;
    var ossfileid1 = "ossfile-" + element[1].name;

    var callback = function (uri, file) {
        //显示图片
        var url = host + '/' + uri;
        var img = '<img src="' + url + '" width="100px">';
        $('.show-oss-img-' + element[0].name).html(img);

        var _URL = window.URL || window.webkitURL;
        var Img = new Image();
        Img.onload = function () {
            if (element_item0 == 0) {
                fileData = uri;
            } else {
                fileData = uri + '|' + file.size + '|' + this.width + '|' + this.height;
            }
            $("#" + val_name0).val(fileData);
        };

        Img.src = _URL.createObjectURL(file);
    };

    var callback2 = function (uri, file) {
        //显示图片
        var url = host + '/' + uri;
        var img = '<img src="' + url + '" width="100px">';
        $('.show-oss-img-' + element[1].name).html(img);

        var _URL = window.URL || window.webkitURL;
        var Img = new Image();
        Img.onload = function () {
            if (element_item1 == 0) {
                fileData = uri;
            } else {
                fileData = uri + '|' + file.size + '|' + this.width + '|' + this.height;
            }
            $("#" + val_name1).val(fileData);
        };

        Img.src = _URL.createObjectURL(file);

    };

    uploader(elementId0, module, callback, uploaderId, options, ossfileid0);
    uploader(elementId1, module, callback2, uploaderId, options, ossfileid1);
</script>