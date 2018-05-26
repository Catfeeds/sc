@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                系统日志
            </h1>
            <ol class="breadcrumb">
                <li><a href="/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">系统日志</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-2">
                    <div class="box box-success">
                        <div class="box-body">
                            <div id="tree">
                                <ul class="list-group">
                                    @foreach( $files as $file)
                                        <li class="list-group-item node-tree" data-nodeid="1">
                                            <span class="indent"></span><span class="icon glyphicon">
                                            </span><span class="icon node-icon"></span><i>{{ $file }}</i>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="box box-info">
                        <div class="box-body">
                            @include('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'])
                            @include('admin.layouts.flash')
                            @include('admin.layouts.modal', ['id' => 'modal_comment'])
                            @include('admin.logs.script')
                            <pre id="table" data-toggle="table" style="font-size: 18px">

                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection