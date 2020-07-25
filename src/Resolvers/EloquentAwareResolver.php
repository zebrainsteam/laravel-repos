<?php

namespace Zebrainsteam\LaravelRepos\Resolvers;

use Illuminate\Database\Eloquent\Model;
use Prozorov\Repositories\Contracts\RepositoryInterface;
use Prozorov\Repositories\Contracts\ResolverInterface;
use Prozorov\Repositories\Exceptions\CouldNotResolve;
use Zebrainsteam\LaravelRepos\EloquentRepository;

class EloquentAwareResolver implements ResolverInterface
{
    public function resolve(string $className): RepositoryInterface
    {
        if (!class_exists($className)) {
            $exception = new CouldNotResolve("Class " . $className . " doesn't exist");
            $exception->setRepositoryCode($className);

            throw $exception;
        }

        $modelClass = new $className;
        if (!$modelClass instanceof Model) {
            $exception = new CouldNotResolve("The passed argument must be an instance of " . Model::class);
            $exception->setRepositoryCode($className);

            throw $exception;
        }

        return new EloquentRepository($className);
    }
}
