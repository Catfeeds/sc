@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                编辑班级信息
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/admin/grades"><i class="fa fa-dashboard"></i>班级</a></li>
                <li class="active">编辑班级信息</li>
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

                            {!! Form::model($grade, ['id' => 'form', 'method' => 'PUT', 'url' => $base_url . '/' . $grade->id, 'class' => 'form-horizontal']) !!}

                            @include('admin.grades.form')

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
