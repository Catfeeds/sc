@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{ $gallery->name }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active"><a href="admin/galleries"> 图集管理</a></li>
                <li class="active">图片管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-body">
                            @include('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'])
                            @include('admin.layouts.flash')
                            @include('admin.layouts.modal', ['id' => 'modal_comment'])
                            @include('admin.images.toolbar')
                            @include('admin.images.query')
                            @include('admin.images.table')
                            @include('admin.contents.script')
                            @include('admin.images.script')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection