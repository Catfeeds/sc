@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                编辑图片
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">编辑图片</li>
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

                            {!! Form::model($content, ['id' => 'form', 'method' => 'PUT', 'url' => $base_url . '/' . $content->id, 'class' => 'form-horizontal']) !!}

                            @include('admin.images.form',['element'=>json_encode([['name'=>'url','item'=>'1'],['name'=>'poster_url','item'=>'0']])])
                            @include('admin.widgets.oss',['module_name'=>'image'])

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
