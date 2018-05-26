@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                首页管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">首页管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-body">
                            @include('admin.layouts.confirm', ['message' => '您确认删除该条信息吗？'])
                            @include('admin.layouts.flash')
                            @include('admin.pics.toolbar')
                            @include('admin.pics.table')
                            @include('admin.pics.script')
                            @include('admin.contents.script')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection