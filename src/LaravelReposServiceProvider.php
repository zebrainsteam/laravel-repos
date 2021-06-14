<?php

namespace Zebrainsteam\LaravelRepos;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Repositories\Core\Contracts\ResolverInterface;
use Repositories\Core\RepositoryFactory;
use Repositories\Core\Resolvers\ChainResolver;
use Repositories\Core\Resolvers\ContainerAwareResolver;
use Zebrainsteam\LaravelRepos\Console\RepositoryInterfaceMakeCommand;
use Zebrainsteam\LaravelRepos\Console\RepositoryMakeCommand;

class LaravelReposServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryInterfaceMakeCommand::class,
                RepositoryMakeCommand::class,
            ]);
        }

        $this->publishes([
            $this->getConfigFile() => config_path('repositories.php'),
        ], 'config');
    }

    /**
     * @throws \Exception
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'repositories'
        );

        $this->app->singleton(ContainerAwareResolver::class, function ($app) {
            return new ContainerAwareResolver($app);
        });

        if (empty(config('repositories.resolvers'))
            || empty(config('repositories.bindings'))
        ) {
            throw new \Exception('Invalid repository config');
        }

        $this->app->singleton('repository-factory', function () {
            foreach (config('repositories.resolvers') as $resolverClass) {
                $resolver = App::get($resolverClass);

                if ($resolver instanceof ResolverInterface) {
                    $resolvers[] = $resolver;
                } else {
                    throw new \Exception('Invalid resolver class ' . $resolverClass);
                }
            }

            $resolver = new ChainResolver($resolvers);

            return new RepositoryFactory($resolver, config('repositories.bindings'));
        });
    }

    /**
     * @return string
     */
    protected function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'repositories.php';
    }
}
