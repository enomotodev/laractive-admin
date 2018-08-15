<?php

namespace Enomotodev\LaractiveAdmin\Http\Controllers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

abstract class Controller
{
    /**
     * @var string
     */
    public $model;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var array
     */
    protected $validate = [];

    /**
     * @var array
     */
    protected $enum = [];

    /**
     * @var int
     */
    protected $paginate = 30;

    /**
     * @var array
     */
    public static $actions = [];

    /**
     * The default layout view.
     *
     * @var string
     */
    public static $defaultLayoutView = 'laractive-admin::layout';

    /**
     * The default index view.
     *
     * @var string
     */
    public static $defaultIndexView = 'laractive-admin::index';

    /**
     * The default index view.
     *
     * @var string
     */
    public static $defaultShowView = 'laractive-admin::show';

    /**
     * The default new view.
     *
     * @var string
     */
    public static $defaultNewView = 'laractive-admin::new';

    /**
     * The default edit view.
     *
     * @var string
     */
    public static $defaultEditView = 'laractive-admin::edit';

    /**
     * @var \Illuminate\Session\SessionManager
     */
    protected $session;

    /**
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Throwable
     */
    public function index()
    {
        $model = $this->model::newModelInstance();
        $columns = $this->getColumnsFromTable($model);
        $collection = $this->getQueryBuilderForIndex()->paginate($this->paginate);

        return new HtmlString(
            view()->make(static::$defaultIndexView, [
                'table' => $this->getTable(),
                'columns' => $columns,
                'collection' => $collection,
                'enum' => $this->enum,
            ])->render()
        );
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Throwable
     */
    public function show(int $id)
    {
        $model = $this->model::findOrFail($id);
        $commentColumns = array_filter($this->getColumnsFromTable($model->comments()->getRelated()), function ($type, $name) use ($model) {
            return ! in_array($name, ['id', 'updated_at', $model->comments()->getForeignKeyName(), $model->comments()->getMorphType()]);
        }, ARRAY_FILTER_USE_BOTH);

        return new HtmlString(
            view()->make(static::$defaultShowView, [
                'table' => $this->getTable(),
                'model' => $model,
                'commentColumns' => $commentColumns,
                'enum' => $this->enum,
            ])->render()
        );
    }

    /**
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Throwable
     */
    public function new()
    {
        $model = $this->model::newModelInstance();
        $columns = $this->getColumnsFromTable($model);
        $relations = $this->getRelations();

        return new HtmlString(
            view()->make(static::$defaultNewView, [
                'table' => $this->getTable(),
                'columns' => $columns,
                'model' => $model,
                'relations' => $relations,
                'files' => $this->files,
                'enum' => $this->enum,
            ])->render()
        );
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        $inputs = $this->validate ? $request->validate($this->validate) : array_filter($request->post(), function ($item) {
            return $item !== null;
        });
        if (isset($inputs['password'])) {
            $inputs['password'] = \Hash::make($inputs['password']);
        }
        $inputs = $this->getInputsWithFiles($request, $inputs);
        $model = $this->model::create($inputs);
        foreach ($this->getRelations() as $key => $relation) {
            if ($relation['type'] !== 'BelongsToMany') {
                continue;
            }

            if (! empty($inputs[$key])) {
                $model->{$relation['relation_name']}()->sync($inputs[$key]);
            }
        }

        $request->session()->flash('message', 'Create');

        return redirect(route("admin.{$this->getTable()}.show", [$model->id]));
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Throwable
     */
    public function edit(int $id)
    {
        $model = $this->model::findOrFail($id);
        $columns = $this->getColumnsFromTable($model);
        $relations = $this->getRelations();

        return new HtmlString(
            view()->make(static::$defaultEditView, [
                'table' => $this->getTable(),
                'columns' => $columns,
                'model' => $model,
                'relations' => $relations,
                'files' => $this->files,
                'enum' => $this->enum,
            ])->render()
        );
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, int $id)
    {
        $model = $this->model::findOrFail($id);
        $inputs = $this->validate ? $request->validate($this->validate) : array_filter($request->post(), function ($item) {
            return $item !== null;
        });
        if (isset($inputs['password'])) {
            $inputs['password'] = \Hash::make($inputs['password']);
        }
        $inputs = $this->getInputsWithFiles($request, $inputs);
        $model->update($inputs);
        foreach ($this->getRelations() as $key => $relation) {
            if ($relation['type'] !== 'BelongsToMany') {
                continue;
            }

            if (! empty($inputs[$key])) {
                $model->{$relation['relation_name']}()->sync($inputs[$key]);
            }
        }

        $request->session()->flash('message', 'Update');

        return redirect(route("admin.{$this->getTable()}.show", [$model->id]));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, int $id)
    {
        $model = $this->model::findOrFail($id);
        $model->delete();

        $request->session()->flash('message', 'Delete');

        return redirect(route("admin.{$this->getTable()}.index"));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function comments(Request $request, int $id)
    {
        $model = $this->model::findOrFail($id);
        $inputs = $request->validate([
            'body' => 'required',
        ]);
        $model->comments()->create([
            'body' => $inputs['body'],
        ]);

        $request->session()->flash('message', 'Create comment');

        return redirect(route("admin.{$this->getTable()}.show", [$model->id]));
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        try {
            return (new ReflectionClass($this))->getShortName();
        } catch (ReflectionException $e) {
            return '';
        }
    }

    /**
     * @return string
     */
    protected function getTable()
    {
        return (new $this->model)->getTable();
    }

    /**
     * @return array
     */
    protected function getRelations()
    {
        $model = (new $this->model)->newInstance();

        $relations = [];

        try {
            $methods = (new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                if (
                    $method->class != get_class($model) ||
                    ! empty($method->getParameters()) ||
                    $method->getName() == __FUNCTION__
                ) {
                    continue;
                }

                try {
                    $return = $method->invoke($model);

                    if ($return instanceof BelongsTo) {
                        $relations[$return->getForeignKey()] = [
                            'type' => (new ReflectionClass($return))->getShortName(),
                            'model' => (new ReflectionClass($return->getRelated()))->getName(),
                        ];
                    } elseif ($return instanceof BelongsToMany) {
                        $relations[$return->getRelatedPivotKeyName()] = [
                            'type' => (new ReflectionClass($return))->getShortName(),
                            'model' => (new ReflectionClass($return->getRelated()))->getName(),
                            'relation_name' => $return->getRelationName(),
                        ];
                    }
                } catch (ReflectionException $e) {
                    // Ignore exception
                }
            }
        } catch (ReflectionException $e) {
            // Ignore exception
        }

        return $relations;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array
     *
     * @throws \Throwable
     */
    protected function getColumnsFromTable($model)
    {
        $table = $model->getConnection()->getTablePrefix().$model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager();
        $databasePlatform = $schema->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $database = null;
        if (strpos($table, '.')) {
            list($database, $table) = explode('.', $table);
        }

        $listTableColumns = $schema->listTableColumns($table, $database);

        $columns = [];
        if ($listTableColumns) {
            foreach ($listTableColumns as $column) {
                $name = $column->getName();
                $columns[$name] = $this->convertColumnTypeName($column->getType()->getName());
            }
        }

        return $columns;
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  array $inputs
     * @return array
     */
    protected function getInputsWithFiles($request, $inputs)
    {
        if (! empty($this->files)) {
            $files = array_filter($inputs, function ($item, $key) {
                return in_array($key, $this->files) && $item instanceof UploadedFile;
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($files as $key => $file) {
                $fileName = Str::random(32).'.'.$request->{$key}->extension();
                $request->{$key}->storePubliclyAs("public/{$this->getTable()}", $fileName);

                $inputs[$key] = $fileName;
            }
        }

        return $inputs;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getQueryBuilderForIndex()
    {
        return $this->model::orderBy('id', 'desc');
    }

    /**
     * @param  string $typeName
     * @return string
     */
    private function convertColumnTypeName($typeName)
    {
        $mapping = [
            'string' => 'string',
            'text' => 'text',
            'date' => 'date',
            'time' => 'time',
            'datetimetz' => 'datetime',
            'datetime' => 'datetime',
            'integer' => 'integer',
            'bigint' => 'integer',
            'smallint' => 'integer',
            'boolean' => 'boolean',
            'decimal' => 'float',
            'float' => 'float',
        ];
        $defaultType = 'mixed';

        return isset($mapping[$typeName]) ? $mapping[$typeName] : $defaultType;
    }
}
