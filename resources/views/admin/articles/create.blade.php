@extends('admin.layouts.master')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                新增文章
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">新增文章</li>
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

                            @if(!empty($content))

                                {!! Form::open(['url' => $base_url. '/' . $content, 'class' => 'form-horizontal']) !!}
                            @else
                                {!! Form::open(['url' => $base_url, 'class' => 'form-horizontal']) !!}

                            @endif

                            @include('admin.articles.form',['element'=>json_encode([['name'=>'image_url','item'=>'0'],['name'=>'video_url','item'=>'0']])])
                            @include('admin.widgets.oss',['module_name'=>'article'])

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

