@extends('default.layouts.master')

@section('title', $site->title)

@section('head')
    <link href="{{ asset('themes/default/css/index.css') }}" rel="stylesheet">
    <script src="{{ asset('themes/default/js/index.js') }}"></script>
@endsection

@section('body')
    @include('default.layouts.header')

    <ul>
        @foreach($site->categories as $category)
            <li><a href="{{ "category-$category->id.html" }}">{{ $category->name }}</a></li>
        @endforeach
    </ul>

    @include('default.layouts.footer')
@endsection

@section('js')
    <script>

    </script>
@endsection