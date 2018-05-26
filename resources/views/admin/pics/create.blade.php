@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                新增首页信息
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">新增首页信息</li>
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

                            {!! Form::open(['url' => $base_url, 'class' => 'form-horizontal']) !!}

                            @include('admin.pics.form',['element'=>json_encode([['name'=>'image','item'=>'0'],['name'=>'poster_url','item'=>'0']])])
                            @include('admin.widgets.oss',['module_name'=>'pic'])
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

