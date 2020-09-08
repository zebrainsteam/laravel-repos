<?php

namespace Zebrainsteam\LaravelRepos\Resolvers;

use Repositories\Core\Contracts\RepositoryInterface;
use Repositories\Core\Contracts\ResolverInterface;
use Repositories\Core\Resolvers\ChainResolver;
use Repositories\Core\Resolvers\ExistingRepositoryResolver;

class AutoResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(string $className): RepositoryInterface
    {
        $resolver = new ChainResolver([
            new ExistingRepositoryResolver(),
            new EloquentAwareResolver()
        ]);

        return $resolver->resolve($className);
    }
}
