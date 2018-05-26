@extends('admin.layouts.master')
@section('css')
    <style>
        .select2-result-repository__avatar {
            float: left;
        }

        .select2-result-repository__title {
            float: left;
            height: 50px;
            line-height: 50px;
            padding-left: 30px;
        }

    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                编辑图集
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">编辑图集</li>
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

                            {!! Form::model($gallery, ['id' => 'form', 'method' => 'PUT', 'url' => $base_url . '/' . $gallery->id, 'class' => 'form-horizontal']) !!}
                            @include('admin.galleries.form')

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
