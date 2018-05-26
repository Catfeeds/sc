@extends('admin.layouts.master')
@section('css')
    <style>
        .dd button[data-action=collapse] {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                章节/课时管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">章节/课时管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <!-- right column -->
                <div class="col-md-10">
                    <!-- Horizontal Form -->
                    <div class="box box-info">
                        <div class="box-body">

                            @include('admin.errors.list')

                            {{-- 章 添加 --}}
                            <div class="modal fade common" id="modal_chapter1" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">添加 章</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form1" action="" method="post" class="form-horizontal">
                                                            {{ csrf_field() }}
                                                            <input id="method" name="_method" type="hidden" value="POST">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    {!! Form::label(null, '章标题',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-8">
                                                                        {!! Form::text('title', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">取消</button>
                                                                <button type="submit" class="btn btn-info pull-right">提交</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--章 编辑--}}
                            <div class="modal fade common" id="modal_edit" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">编辑 章</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form_edit" action="" method="post"
                                                              class="form-horizontal">
                                                            {{ csrf_field() }}
                                                            <input id="method" name="_method" type="hidden" value="PUT">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    {!! Form::label(null, '章标题',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-8">
                                                                        {!! Form::text('title', null, ['class'=>'form-control']) !!}
                                                                        {!! Form::hidden('course_id', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">取消</button>
                                                                <button type="submit" class="btn btn-info pull-right">提交</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 课时 添加 --}}
                            <div class="modal fade common" id="modal_lesson" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                                            <h4 class="modal-title">添加 课时</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form3" action="" method="post" class="form-horizontal">
                                                            {{ csrf_field() }}
                                                            <input id="method" name="_method" type="hidden" value="POST">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    {!! Form::label(null, '课时标题：',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('title', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('type', '课时类型：',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::select('type', ['正常课时','章节总结'], null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('intro', '课时摘要：',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('intro', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('content', '课时正文：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::textarea('content', null, ['class'=>'form-control', 'rows'=>'3', 'cols'=>'50']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('duration', '课时时长：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('duration', null, ['class'=>'form-control', 'placeholder'=>'单位（分钟）']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('media_url', '媒体资源：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('media_url', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('free', '是否免费：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::select('free', ['否','是'], null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">取消</button>
                                                                <button type="submit" class="btn btn-info pull-right">提交</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--课时 编辑--}}
                            <div class="modal fade common" id="modal_edit-lesson" tabindex="-1" role="dialog">
                                <div class="modal-dialog" style="width:640px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h4 class="modal-title">编辑 课时</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="box box-info">
                                                        <form id="form_edit-lesson" action="" method="post"
                                                              class="form-horizontal">
                                                            {{ csrf_field() }}
                                                            {!! Form::hidden('course_id', null, ['class'=>'form-control']) !!}
                                                            <input id="method" name="_method" type="hidden" value="PUT">
                                                            <div class="box-body">
                                                                <div class="form-group">
                                                                    {!! Form::label(null, '课时标题：',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('title', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('type', '课时类型：',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::select('type', ['正常课时','章节总结'], null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('intro', '课时摘要：',['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('intro', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('content', '课时正文：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::textarea('content', null, ['class'=>'form-control', 'rows'=>'3', 'cols'=>'50']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('duration', '课时时长：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('duration', null, ['class'=>'form-control', 'placeholder'=>'单位（分钟）']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('media_url', '媒体资源：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::text('media_url', null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {!! Form::label('free', '是否免费：', ['class'=>'control-label col-sm-2']) !!}
                                                                    <div class="col-sm-9">
                                                                        {!! Form::select('free', ['否','是'], null, ['class'=>'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-default" data-dismiss="modal">
                                                                    取消
                                                                </button>
                                                                <button type="submit" class="btn btn-info pull-right">
                                                                    提交
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--章节/课时 树形图--}}
                            <div id="tab_课时管理" class="tab-pane fade in padding-t-15" style="min-height: 300px;">
                                <div class="btn-group margin-b-5">
                                    <button class="btn btn-primary btn-xs margin-r-5 margin-b-5 btn_creates"
                                            id="btn_create1" data-toggle="modal" data-target="#modal_chapter1">添加章
                                    </button>

                                    <button class="btn btn-info btn-xs margin-r-5 margin-b-5 btn_creates"
                                            id="btn_create3" data-toggle="modal" data-target="#modal_lesson">添加课时
                                    </button>

                                    <a class="btn btn-primary btn-xs margin-r-5" href="/admin/courses">返回课程</a>
                                </div>
                                {{--手动添加结束--}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box box-info">
                                            <div class="box-body">
                                                @include('admin.layouts.flash')
                                                <div class="dd chapter-nestable" id="menu-nestable">
                                                    {{ count($chapterList) ? '' : '没有查询到章节信息' }}
                                                    {!! \App\Helpers\HtmlBuilder::chapterEditor($chapterList) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $('#btn_create1').click(function () {
                                    $('#form1').attr('action', '/admin/chapters/{{$content->id}}/create');
                                    $('#method').val('POST');
                                });

                                $('#btn_create3').click(function () {
                                    $('#form3').attr('action', '/admin/lessons/{{$content->id}}/create');
                                    $('#method').val('POST');
                                });
                            </script>

                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        $('input[name=icon]').iconpicker();

        $('.dd').nestable();

        $('.btn-menu-remove').click(function () {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/chapters/' + id,
                method: 'post',
                data: {'_token': '{{ csrf_token() }}', '_method': 'delete'},
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        $('.btn-menu-remove-lesson').click(function () {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/lessons/' + id,
                method: 'post',
                data: {'_token': '{{ csrf_token() }}', '_method': 'delete'},
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        $('.btn-menu-edit').click(function () {
            var id = $(this).data('id');
            var title = $(this).data('name');
            var course_id = $(this).data('course_id');
            $('#form_edit').attr('action', '/admin/chapters/' + id);
            $('#form_edit input[name=title]').val(title);
            $('#form_edit input[name=course_id]').val(course_id);
            $("#modal_edit").modal('show');
        });

        $('.btn-menu-edit-lesson').click(function () {
            var id = $(this).data('id');
            var title = $(this).data('name');
            var course_id = $(this).data('course_id');
            var intro = $(this).data('intro');
            var content = $(this).data('content');
            var duration = $(this).data('duration');
            var media_url = $(this).data('media_url');
            var free = $(this).data('free');
            var type = $(this).data('type');
            $('#form_edit-lesson').attr('action', '/admin/lessons/' + id);
            $('#form_edit-lesson input[name=title]').val(title);
            $('#form_edit-lesson input[name=course_id]').val(course_id);
            $('#form_edit-lesson input[name=intro]').val(intro);
            $('#form_edit-lesson textarea[name=content]').val(content);
            $('#form_edit-lesson input[name=duration]').val(duration);
            $('#form_edit-lesson input[name=media_url]').val(media_url);
            $('#form_edit-lesson select[name=free]').val(free);
            $('#form_edit-lesson select[name=type]').val(type);
            $("#modal_edit-lesson").modal('show');
        });

        $('#menu-nestable').on('change', function () {
            var data = $('#menu-nestable').nestable('serialize');
            $.ajax({
                url: '/admin/chapters/{{$course_id}}/sorts',
                method: 'post',
                data: {'_token': '{{ csrf_token() }}', 'data': data},
                success: function (data) {
                }
            });
        });
    </script>
@endsection