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

        @foreach ($columns as $name => $type)
            @continue($name === $model->getKeyName() || $name === 'created_at' || $name === 'updated_at')

            <div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}">
                <label>{!! Form::label($name) !!}</label>
                @if ($name === 'password')
                    {!! Form::password($name, ['class' => 'form-control']) !!}
                @elseif (in_array($name, $files))
                    {!! Form::file($name) !!}
                    @if (!empty($model->{$name}))
                        <img src="{{ Storage::url("{$table}/".$model->{$name}) }}" style="margin: 5px; max-height: 100px;" />
                    @endif
                @elseif (isset($relations[$name]) && $relations[$name]['type'] === 'BelongsTo')
                    {!! Form::select($name, $relations[$name]['model']::pluck('name', 'id')->toArray(), null, ['class' => 'form-control']) !!}
                @elseif (!empty($enum[$name]))
                    @foreach ($enum[$name] as $key => $value)
                        <div class="radio">
                            <label>
                                {!! Form::radio($name, $value, null) !!}{{ $key }}
                            </label>
                        </div>
                    @endforeach
                @elseif ($type === 'text')
                    {!! Form::textarea($name, null, ['class' => 'form-control']) !!}
                @elseif ($type === 'boolean')
                    <label class="checkbox-inline">
                        {!! Form::checkbox($name, 1, null) !!}
                    </label>
                @elseif ($type === 'datetime')
                    {!! Form::text($name, null, ['class' => 'form-control datetimepicker']) !!}
                @elseif ($type === 'date')
                    {!! Form::text($name, null, ['class' => 'form-control datepicker']) !!}
                @elseif ($type === 'time')
                    {!! Form::text($name, null, ['class' => 'form-control timepicker']) !!}
                @else
                    {!! Form::text($name, null, ['class' => 'form-control']) !!}
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
