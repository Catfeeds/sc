@extends('default.layouts.master')

@section('title', $page->title . ' - ' . $site->title)

@section('head')
    <link href="{{ asset('themes/default/css/detail.css') }}" rel="stylesheet">
    <script src="{{ asset('themes/default/js/detail.js') }}"></script>
@endsection

@section('body')
    @include('default.layouts.header')

    <h2>移动端</h2>
    <h2>{{ $page->title }}</h2>
    <div>
        {!! $page->content !!}
    </div>

    @include('default.layouts.footer')
@endsection

@section('js')
    <script src="{{ asset('/js/access.js') }}"></script>
@endsection