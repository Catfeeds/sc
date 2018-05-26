<div class="modal fade common" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                <h4 class="modal-title" id="modal_title">图集：批量导入</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="window_msg" style="display: none;">
                    <p></p>
                </div>
                <div id="contents">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-info">
                                <div class="box-body">
                                    {!! Form::open(['url' => '/admin/galleries/batch/import', 'class' => 'form-horizontal']) !!}
                                    <div class="tab-content">
                                        <div id="info" class="tab-pane fade in active padding-t-15">
                                            <div class="form-group">
                                                {!! Form::label('file_url', '文件地址:', ['class' => 'control-label col-sm-1', 'style' => 'width: 10%']) !!}
                                                <div class="col-sm-10">
                                                    {!! Form::text('file_url', null, ['class' => 'form-control', 'readonly']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="excel_file" class="control-label col-sm-1"
                                                       style="margin: 25px 0px 35px;width: 10%">导入文件:</label>
                                                <div class=" col-sm-10" style="margin: 25px 0px 35px;">
                                                    <input id="excel_file" name="excel_file" type="file"
                                                           data-preview-file-type="text"
                                                           data-upload-url="/admin/files/upload?type=file">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-footer">
                                        <button type="button" class="btn btn-default" onclick="window.history.back();">
                                            取　消
                                        </button>
                                        <button type="submit" class="btn btn-info pull-right" id="submit">确　定</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var file_url = $('#file_url').val();
    var files = [];

    if (file_url == null || file_url.length > 0) {
        files = "<div class='file-preview-text'>" +
            "<h3><i class='glyphicon glyphicon-file'></i></h3>" +
            "Filename.xlsx" + "</div>";
    }

    $('#excel_file').fileinput({
        language: 'zh',
        uploadExtraData: {_token: '{{ csrf_token() }}'},
        allowedFileExtensions: ['xlsx', 'xls'],
        initialPreview: files,
        initialPreviewAsData: false,
        initialPreviewConfig: [{key: 1}],
        deleteUrl: '/admin/files/delete?_token={{csrf_token()}}',
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

    $('#excel_file').on('fileuploaded', function (event, data) {
        $('#file_url').val(data.response.data);
    });

    $('#excel_file').on('filedeleted', function (event, key) {
        $('#file_url').val('');
    });

    $('#submit').click(function () {
        var excel_file = $('#excel_file').fileinput('getFileStack');

        if (excel_file.length > 0) {
            return toast('info', '请先上传头像');
        }
    })
</script>