<?php

namespace Enomotodev\LaractiveAdmin\Http\Controllers;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
     */
    public function index()
    {
        $model = $this->model::newModelInstance();
        $columns = $this->getColumnsFromTable($model);
        $collection = $this->model::paginate();

        return new HtmlString(
            view()->make(static::$defaultIndexView, [
                'class' => $this->getClassName(),
                'table' => $this->getTable(),
                'layoutView' => static::$defaultLayoutView,
                'columns' => $columns,
                'collection' => $collection,
            ])->render()
        );
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Support\HtmlString
     */
    public function show(int $id)
    {
        $model = $this->model::findOrFail($id);
        $commentColumns = array_filter($this->getColumnsFromTable($model->comments()->getRelated()), function ($type, $name) use ($model) {
            return !in_array($name, ['id', 'updated_at', $model->comments()->getForeignKeyName(), $model->comments()->getMorphType()]);
        }, ARRAY_FILTER_USE_BOTH);

        return new HtmlString(
            view()->make(static::$defaultShowView, [
                'class' => $this->getClassName(),
                'table' => $this->getTable(),
                'layoutView' => static::$defaultLayoutView,
                'model' => $model,
                'commentColumns' => $commentColumns,
            ])->render()
        );
    }

    /**
     * @return \Illuminate\Support\HtmlString
     */
    public function new()
    {
        $model = $this->model::newModelInstance();
        $columns = $this->getColumnsFromTable($model);
        $relations = $this->getRelations();

        return new HtmlString(
            view()->make(static::$defaultNewView, [
                'class' => $this->getClassName(),
                'table' => $this->getTable(),
                'layoutView' => static::$defaultLayoutView,
                'columns' => $columns,
                'model' => $model,
                'relations' => $relations,
                'files' => $this->files,
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
        if ($this->files) {
            $files = array_filter($inputs, function ($item, $key) {
                return in_array($key, $this->files) && $item instanceof UploadedFile;
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($files as $key => $file) {
                $fileName = Str::random(32).".".$request->{$key}->extension();
                $request->{$key}->storePubliclyAs("public/{$this->getTable()}", $fileName);

                $inputs[$key] = $fileName;
            }
        }
        $model = $this->model::create($inputs);
        foreach ($this->getRelations() as $key => $relation) {
            if ($relation['type'] !== 'BelongsToMany') {
                continue;
            }

            if (!empty($inputs[$key])) {
                $model->{$relation['relation_name']}()->sync($inputs[$key]);
            }
        }

        $request->session()->flash('message', 'Create');

        return redirect(route("admin.{$this->getTable()}.show", [$model->id]));
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Support\HtmlString
     */
    public function edit(int $id)
    {
        $model = $this->model::findOrFail($id);
        $columns = $this->getColumnsFromTable($model);
        $relations = $this->getRelations();

        return new HtmlString(
            view()->make(static::$defaultEditView, [
                'class' => $this->getClassName(),
                'table' => $this->getTable(),
                'layoutView' => static::$defaultLayoutView,
                'columns' => $columns,
                'model' => $model,
                'relations' => $relations,
                'files' => $this->files,
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
        if ($this->files) {
            $files = array_filter($inputs, function ($item, $key) {
                return in_array($key, $this->files) && $item instanceof UploadedFile;
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($files as $key => $file) {
                $fileName = Str::random(32).".".$request->{$key}->extension();
                $request->{$key}->storePubliclyAs("public/{$this->getTable()}", $fileName);

                $inputs[$key] = $fileName;
            }
        }
        $model->update($inputs);
        foreach ($this->getRelations() as $key => $relation) {
            if ($relation['type'] !== 'BelongsToMany') {
                continue;
            }

            if (!empty($inputs[$key])) {
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
            foreach($methods as $method) {
                if (
                    $method->class != get_class($model) ||
                    !empty($method->getParameters()) ||
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
                }
            }
        } catch (ReflectionException $e) {
        }

        return $relations;
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        try {
            return (new ReflectionClass($this))->getShortName();
        } catch (ReflectionException $e) {
            return '';
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function getColumnsFromTable($model)
    {
        $table = $model->getConnection()->getTablePrefix().$model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager($table);
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

                switch ($column->getType()->getName()) {
                    case 'string':
                        $type = 'string';
                        break;
                    case 'text':
                        $type = 'text';
                        break;
                    case 'date':
                        $type = 'date';
                        break;
                    case 'time':
                        $type = 'time';
                        break;
                    case 'datetimetz':
                    case 'datetime':
                        $type = 'datetime';
                        break;
                    case 'integer':
                    case 'bigint':
                    case 'smallint':
                        $type = 'integer';
                        break;
                    case 'boolean':
                        $type = 'boolean';
                        break;
                    case 'decimal':
                    case 'float':
                        $type = 'float';
                        break;
                    default:
                        $type = 'mixed';
                        break;
                }

                $columns[$name] = $type;
            }
        }

        return $columns;
    }
}
