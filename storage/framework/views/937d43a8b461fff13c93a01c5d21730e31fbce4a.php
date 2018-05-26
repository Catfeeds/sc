<script src="/oss-upload/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script src="/oss-upload/upload.js"></script>
<script src="/oss-upload/uploader.js"></script>
<script>
    var val_name = $('#selectfiles_<?php echo e($elementId1); ?>').siblings('.oss-img-uri-<?php echo e($elementId1); ?>').children().attr('class');
    var val_name2 = $('#selectfiles_<?php echo e($elementId2); ?>').siblings('.oss-img-uri-<?php echo e($elementId2); ?>').children().attr('class');

    var ossfileid1 = "ossfile-<?php echo e($elementId1); ?>";
    var ossfileid2 = "ossfile-<?php echo e($elementId2); ?>";

    var elementId = "selectfiles_<?php echo e($elementId1); ?>";
    var elementId2 = "selectfiles_<?php echo e($elementId2); ?>";

    var showossimg1 = "show-oss-img-<?php echo e($elementId1); ?>";
    var showossimg2 = "show-oss-img-<?php echo e($elementId2); ?>";

    var container1 = "container-<?php echo e($elementId1); ?>";
    var container2 = "container-<?php echo e($elementId2); ?>";

    var uploaderId = "<?php echo e(Auth::user()->id); ?>";
    var module = "<?php echo e($module_name); ?>";
    var host = "<?php echo e(config('site.oss.host')); ?>";
    var options = {
        multiple: true,
        max_file_size: '20mb',
        mime_types: [ //只允许上传图片
            {title: "Image files", extensions: "jpg,gif,png,bmp"}
        ],
    };

            <?php if(isset($elementId3)): ?>
    var val_name3 = $('#selectfiles_<?php echo e($elementId3); ?>').siblings('.oss-img-uri-<?php echo e($elementId3); ?>').children().attr('class');
    var ossfileid3 = "ossfile-<?php echo e($elementId3); ?>";
    var elementId3 = "selectfiles_<?php echo e($elementId3); ?>";
    var showossimg3 = "show-oss-img-<?php echo e($elementId3); ?>";
    var container3 = "container-<?php echo e($elementId3); ?>";
    var callback3 = function (uri, file) {
        //显示图片
        var url = host + '/' + uri;
        var img = '<img src="' + url + '" width="100px">';
        $('.show-oss-img-<?php echo e($elementId3); ?>').html(img);

        var _URL = window.URL || window.webkitURL;
        var Img = new Image();
        Img.onload = function () {
            fileData = uri;
            $("#" + val_name3).val(fileData);
        };

        Img.src = _URL.createObjectURL(file);

    };
    uploader(elementId3, module, callback3, uploaderId, options, ossfileid3, showossimg3, container3);
            <?php endif; ?>
    var callBack = function (w, h) {
        return [w, h];
    }

    function getImgInfo(img, callBack) {
        var nWidth, nHeight
        if (img.naturalWidth) { // 现代浏览器
            nWidth = img.naturalWidth
            nHeight = img.naturalHeight
        } else { // IE6/7/8
            var image = new Image()
            image.src = img.src
            image.onload = function () {
                callBack(image.width, image.height)
            }
        }
        return [nWidth, nHeight]
    }

    var callback = function (uri, file) {
        //显示图片
        var url = host + '/' + uri;
        var img = '<img src="' + url + '" width="100px">';
        $('.show-oss-img-<?php echo e($elementId1); ?>').html(img);

        var _URL = window.URL || window.webkitURL;
        var Img = new Image();
        Img.onload = function () {
            fileData = uri;
            // fileData = uri + '|' + file.size + '|' + this.width + '|' + this.height;
            $("#" + val_name).val(fileData);
        };

        Img.src = _URL.createObjectURL(file);
    };

    var callback2 = function (uri, file) {
        //显示图片
        var url = host + '/' + uri;
        var img = '<img src="' + url + '" width="100px">';
        $('.show-oss-img-<?php echo e($elementId2); ?>').html(img);

        var _URL = window.URL || window.webkitURL;
        var Img = new Image();
        Img.onload = function () {
            fileData = uri;
            $("#" + val_name2).val(fileData);
        };

        Img.src = _URL.createObjectURL(file);

    };


    uploader(elementId, module, callback, uploaderId, options, ossfileid1, showossimg1, container1);

    uploader(elementId2, module, callback2, uploaderId, options, ossfileid2, showossimg2, container2);


</script>