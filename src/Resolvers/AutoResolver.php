<?php

namespace Zebrainsteam\LaravelRepos\Resolvers;

use Prozorov\Repositories\Contracts\RepositoryInterface;
use Prozorov\Repositories\Contracts\ResolverInterface;
use Prozorov\Repositories\Resolvers\ChainResolver;
use Prozorov\Repositories\Resolvers\SelfResolver;

class AutoResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(string $className): RepositoryInterface
    {
        $resolver = new ChainResolver([
            new SelfResolver(),
            new EloquentAwareResolver()
        ]);

        return $resolver->resolve($className);
    }
}
