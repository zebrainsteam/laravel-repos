<?php

namespace Zebrainsteam\LaravelRepos;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Prozorov\Repositories\AbstractRepository;

class EloquentRepository extends AbstractRepository
{
    /**
     * @var string
     */
    protected $modelClass;

    public function __construct(string $modelClass)
    {
        if (!class_exists($modelClass)) {
            throw new \InvalidArgumentException("Class " . $modelClass . " doesn't exist");
        }
        $model = new $modelClass();
        if (!$model instanceof Model) {
            throw new \InvalidArgumentException("Class " . $modelClass . " must be inherited from " . Model::class);
        }

        $this->modelClass = $modelClass;
    }

    /**
     * @param $params
     * @return iterable|null
     */
    protected function doGet($params): ?iterable
    {
        return $this->modelClass::where($params)->get();
    }

    /**
     * @param array $filter
     * @return Model|object|static|null
     */
    protected function doFirst(array $filter)
    {
        return $this->modelClass::where($filter)->first();
    }

    /**
     * @param int $id
     * @param array|null $select
     * @return Model|Collection|static[]|static|null
     */
    protected function doGetById(int $id, array $select = null)
    {
        return $this->modelClass::find($id, is_null($select) ? ['*'] : $select);
    }

    /**
     * @param int $id
     * @param array|null $select
     * @return Model|Collection|static|static[]
     *
     * @throws ModelNotFoundException
     */
    protected function doGetByIdOrFail(int $id, array $select = null)
    {
        return $this->modelClass::findOrFail($id, is_null($select) ? ['*'] : $select);
    }

    /**
     * @param array $data
     * @return Model|Builder
     */
    protected function doCreate(array $data)
    {
        return $this->modelClass::create($data);
    }

    /**
     * @param $model
     * @param array $data
     *
     * @throws ModelNotFoundException
     */
    protected function doUpdate($model, array $data): void
    {
        $this->validateModel($model);
        $model->update($data);
    }

    /**
     * @param $model
     *
     * *@throws ModelNotFoundException
     */
    protected function doDelete($model): void
    {
        $this->validateModel($model);
        $model->delete();
    }

    /**
     * @param array $filter
     * @return bool
     */
    protected function doExists(array $filter): bool
    {
        return !empty($this->first($filter));
    }

    /**
     * @param array $filter
     * @return int
     */
    protected function doCount(array $filter = []): int
    {
        return $this->modelClass::where($filter)->count();
    }

    /**
     * @return void
     */
    protected function doOpenTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * @return void
     */
    protected function doCommitTransaction(): void
    {
        DB::commit();
    }

    /**
     * @return void
     */
    protected function doRollbackTransaction(): void
    {
        DB::rollBack();
    }

    /**
     * @param $model
     * @return void
     *
     * @throws InvalidArgumentException
     */
    private function validateModel($model): void
    {
        $modelClass = new $this->modelClass();
        if (!$model instanceof $modelClass) {
            throw new \InvalidArgumentException("The passed argument must be an instance of " . $this->modelClass);
        }
    }
}
