@extends($layoutView)

@section('title', $class)

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="text-right">
                <label>
                    <a href="{{ route("admin.${table}.new") }}" class="btn btn-default">New Data</a>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap">
                    <thead>
                    <tr>
                        @foreach ($columns as $name => $type)
                            <td>{{ $name }}</td>
                        @endforeach
                        <td>&nbsp;</td>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($collection as $model)
                        <tr>
                            @foreach ($columns as $name => $type)
                                <td>
                                    @if (!empty($enum[$name]))
                                        {{ array_search($model->{$name}, $enum[$name])  }}
                                    @else
                                        {{ $model->{$name} }}
                                    @endif
                                </td>
                            @endforeach
                            <td style="display:inline-flex">
                                {!! Form::model($model, ['route' => ["admin.{$table}.destroy", $model->id], 'method' => 'DELETE']) !!}
                                <a href="{{ route("admin.${table}.show", [$model->id]) }}" class="btn-link">View</a>
                                <a href="{{ route("admin.${table}.edit", [$model->id]) }}" class="btn-link">Edit</a>
                                {!! Form::submit('Delete', ['class' => 'btn-link', 'style' => 'margin:0; padding:0;']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}">No Data ...</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-right">
                {!! $collection->render() !!}
            </div>
        </div>
    </div>
@endsection
