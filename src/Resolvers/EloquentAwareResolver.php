<?php

namespace Zebrainsteam\LaravelRepos\Resolvers;

use Illuminate\Database\Eloquent\Model;
use Repositories\Core\Contracts\ResolverInterface;
use Repositories\Core\Exceptions\CouldNotResolve;
use Repositories\Core\Contracts\RepositoryInterface;
use Zebrainsteam\LaravelRepos\EloquentRepository;

class EloquentAwareResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(string $className): RepositoryInterface
    {
        if (!class_exists($className)) {
            $exception = new CouldNotResolve("Class " . $className . " doesn't exist");
            $exception->setRepositoryCode($className);

            throw $exception;
        }

        if (in_array(Model::class, class_implements($className))) {
            $exception = new CouldNotResolve("The passed argument must be an instance of " . Model::class);
            $exception->setRepositoryCode($className);

            throw $exception;
        }

        return new EloquentRepository($className);
    }
}
