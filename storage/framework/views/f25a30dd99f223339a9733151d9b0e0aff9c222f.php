<ul id="tabs" class="nav nav-tabs">
    <li class="active">
        <a href="#info" data-toggle="tab">基本信息</a>
    </li>
</ul>
<div class="tab-content">
    <div id="info" class="tab-pane fade in active padding-t-15">
        <div class="form-group">
            <?php echo Form::label('name', '姓名:',['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('name', null, ['class' => 'form-control']); ?>

            </div>
            <?php echo Form::label($password, '密码:',['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::password($password, ['class' => 'form-control' ,'placeholder'=>$placeholder]); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('nick_name', '昵称:', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('nick_name', null, ['class' => 'form-control']); ?>

            </div>
            <?php echo Form::label('title', '职称:', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('title', null, ['class' => 'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <?php echo Form::label('type', '会员类型:', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::select('type', \App\Models\Member::TYPES, null, ['class' => 'form-control']); ?>

            </div>
            <?php echo Form::label('mobile', '手机号:', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-5">
                <?php echo Form::text('mobile', null, ['class' => 'form-control']); ?>

            </div>
        </div>

        <div class="form-group" style="display: none;">
            <?php echo Form::label('avatar_url', '头像地址:', ['class' => 'control-label col-sm-1']); ?>

            <div class="col-sm-11">
                <?php echo Form::text('avatar_url', null, ['class' => 'form-control']); ?>

            </div>
        </div>

        <div class="form-group">
            <label for="image_file" class="control-label col-sm-1">上传头像:</label>
            <div class=" col-sm-11">
                <input id="image_file" name="image_file" type="file" data-preview-file-type="text"
                       data-upload-url="/admin/files/upload?type=image">
            </div>
        </div>
    </div>
</div>

<div class="box-footer">
    <button type="button" class="btn btn-default" onclick="window.history.back();">取　消</button>
    <button type="submit" class="btn btn-info pull-right" id="submit">确　定</button>
</div>

<script>
    var image_url = $('#avatar_url').val();
    var images = [];

    if (image_url == null || image_url.length > 0) {
        images = ['<img height="240" src="' + $('#avatar_url').val() + '">'];
    }

    $('#image_file').fileinput({
        language: 'zh',
        uploadExtraData: {_token: '<?php echo e(csrf_token()); ?>'},
        allowedFileExtensions: ['jpg', 'gif', 'png'],
        initialPreview: images,
        initialPreviewAsData: false,
        initialPreviewConfig: [{key: 1}],
        deleteUrl: '/admin/files/delete?_token=<?php echo e(csrf_token()); ?>',
        maxFileSize: 10240,
        maxFileCount: 1,
        resizeImage: true,
        maxImageWidth: 640,
        maxImageHeight: 960,
        resizePreference: 'width',
        fileActionSettings: {
            showZoom: false
        },
    });

    $('#image_file').on('fileuploaded', function (event, data) {
        $('#avatar_url').val(data.response.data);
    });

    $('#image_file').on('filedeleted', function (event, key) {
        $('#avatar_url').val('');
    });

    $('#submit').click(function () {
        var image_file = $('#image_file').fileinput('getFileStack');

        if (image_file.length > 0) {
            return toast('info', '请先上传头像');
        }
    })
</script>