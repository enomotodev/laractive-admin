@extends($layoutView)

@section('title', $class)

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap">
                    <tbody>
                    @foreach ($model->getAttributes() as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>
                                @if (!empty($enum[$key]))
                                    {{ array_search($value, $enum[$key])  }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h4>Comments</h4>
            {!! Form::open(['route' => ["admin.{$table}.comments", $model->id]]) !!}
                <div class="row" style="position: relative;">
                    <div class="col-md-10">
                        <div class="form-group">
                            {!! Form::textarea('body', null, ['class' => 'form-control', 'rows' => '4']) !!}
                        </div>
                    </div>
                    <div class="col-md-2" style="position: absolute; bottom: 0; right: 0;">
                        <div class="form-group">
                            <label>
                                {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                            </label>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}

            <div class="table-responsive">
                <table class="table table-hover text-nowrap">
                    <thead>
                    <tr>
                        @foreach ($commentColumns as $name => $type)
                            <td>{{ $name }}</td>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($model->comments()->getResults() as $comment)
                        <tr>
                            @foreach ($commentColumns as $name => $type)
                                <td>{{ $comment->{$name} }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($commentColumns) }}">No Data ...</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <label>
                <a href="{{ route("admin.${table}.index") }}" class="btn btn-default">Back</a>
            </label>
            <label>
                <a href="{{ route("admin.${table}.edit", [$model->id]) }}" class="btn btn-default">Edit</a>
            </label>
        </div>
    </div>
@endsection
