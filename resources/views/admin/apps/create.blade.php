@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                版本管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">版本管理</a></li>
                <li class="active">新增</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <!-- right column -->
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-info">
                        <div class="box-body">
                            @include('admin.errors.list')

                            {!! Form::open(['url' => '/admin/apps','class' => 'form-horizontal']) !!}

                            @include('admin.apps._form', ['element'=>json_encode([['name'=>'logo_url','item'=>'0'],['name'=>'android_url','item'=>'0']])])
                            @include('admin.widgets.oss',['module_name'=>'app'])
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection