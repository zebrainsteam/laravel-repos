<?php


namespace Zebrainsteam\LaravelRepos;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Prozorov\Repositories\Contracts\ResolverInterface;
use Prozorov\Repositories\RepositoryFactory;
use Prozorov\Repositories\Resolvers\ChainResolver;
use Prozorov\Repositories\Resolvers\ContainerAwareResolver;
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
            __DIR__.'/config/repositories.php' => config_path('repositories.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(ContainerAwareResolver::class, function ($app) {
            return new ContainerAwareResolver($app);
        });

        if (empty(config('repositories.common.resolvers'))
            || empty(config('repositories.common.bindings'))
        ) {
            throw new \Exception('Invalid repository config');
        }

        $this->app->singleton(RepositoryFactory::class, function ($app) {
            foreach (config('repositories.common.resolvers') as $resolverClass) {
                $resolver = App::get($resolverClass);

                if ($resolver instanceof ResolverInterface) {
                    $resolvers[] = $resolver;
                } else {
                    throw new \Exception('Invalid resolver class ' . $resolverClass);
                }
            }

            $resolver = new ChainResolver($resolvers);

            return new RepositoryFactory($resolver, config('repositories.common.bindings'));
        });
    }
}
