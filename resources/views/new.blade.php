@extends($layoutView)

@section('title', $class)

@section('content')
    {!! Form::model($model, ['route' => "admin.{$table}.create", 'files' => true]) !!}
        @include('laractive-admin::form')
    {!! Form::close() !!}
@endsection
