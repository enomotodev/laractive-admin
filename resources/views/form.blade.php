<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @foreach ($columns as $column)
            @continue($column->Extra === 'auto_increment' || $column->Field === 'created_at' || $column->Field === 'updated_at')

            <div class="form-group{{ $errors->has($column->Field) ? ' has-error' : '' }}">
                <label>{!! Form::label($column->Field) !!}</label>
                @if ($column->Field === 'password')
                    {!! Form::password($column->Field, ['class' => 'form-control']) !!}
                @elseif (in_array($column->Field, $files))
                    {!! Form::file($column->Field) !!}
                    @if (!empty($model->{$column->Field}))
                        <img src="{{ Storage::url("{$table}/".$model->{$column->Field}) }}" style="margin: 5px; max-height: 100px;" />
                    @endif
                @elseif (isset($relations[$column->Field]) && $relations[$column->Field]['type'] === 'BelongsTo')
                    {!! Form::select($column->Field, $relations[$column->Field]['model']::pluck('name', 'id')->toArray(), null, ['class' => 'form-control']) !!}
                @elseif ($column->Type === 'text')
                    {!! Form::textarea($column->Field, null, ['class' => 'form-control']) !!}
                @elseif ($column->Type === 'tinyint(1)')
                    <label class="checkbox-inline">
                        {!! Form::checkbox($column->Field, 1, null) !!}
                    </label>
                @else
                    {!! Form::text($column->Field, null, ['class' => 'form-control']) !!}
                @endif
            </div>
        @endforeach
        @foreach ($relations as $key => $relation)
            @continue($relation['type'] !== 'BelongsToMany')

            <div class="form-group{{ $errors->has($key) ? ' has-error' : '' }}">
                <label>{!! Form::label(array_slice(explode("\\", $relation['model']), -1)[0]) !!}</label>
                @foreach ($relation['model']::all() as $relationModel)
                    <label class="checkbox-inline">
                        {!! Form::checkbox("{$key}[]", $relationModel->id, in_array($relationModel->id, $model->{$relation['relation_name']}->pluck('id')->toArray())) !!}
                        {{ $relationModel->name }}
                    </label>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <label>
            <a href="{{ route("admin.${table}.index") }}" class="btn btn-default">Back</a>
        </label>
        <label>
            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        </label>
    </div>
</div>
