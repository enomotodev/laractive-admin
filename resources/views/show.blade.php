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
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
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
