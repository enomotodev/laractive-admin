@extends($layoutView)

@section('title', $class)

@section('content')
    {!! Form::model($model, ['route' => ["admin.{$table}.update", $model->id], 'files' => true, 'method' => 'PUT']) !!}
        @include('laractive-admin::form')
    {!! Form::close() !!}
@endsection
