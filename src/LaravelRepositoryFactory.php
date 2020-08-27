<?php

namespace Zebrainsteam\LaravelRepos;

use Illuminate\Support\Facades\App;
use Prozorov\Repositories\Contracts\ResolverInterface;
use Prozorov\Repositories\RepositoryFactory;
use Prozorov\Repositories\Resolvers\ChainResolver;

class LaravelRepositoryFactory
{
    /** Init repository factory with standard or custom config
     *
     * @param string $config
     * @return RepositoryFactory
     * @throws \Exception
     */
    public static function init($config = 'common'): RepositoryFactory
    {
        if ($config == 'common') {
            return App::get(RepositoryFactory::class);
        }

        if (empty(config('repositories.' . $config . '.resolvers'))
            || empty(config('repositories.' . $config . '.bindings'))
        ) {
            throw new \Exception('Invalid repository config');
        }

        foreach (config('repositories.' . $config . '.resolvers') as $resolverClass) {
            $resolver = App::get($resolverClass);

            if ($resolver instanceof ResolverInterface) {
                $resolvers[] = $resolver;
            } else {
                throw new \Exception('Invalid resolver class ' . $resolverClass);
            }

            $resolver = new ChainResolver($resolvers);

            return new RepositoryFactory($resolver, config('repositories.' . $config . '.bindings'));
        }
    }
}
