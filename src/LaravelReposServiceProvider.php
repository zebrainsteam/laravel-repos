<?php


namespace Zebrainsteam\LaravelRepos;

use Illuminate\Support\ServiceProvider;
use Zebrainsteam\LaravelRepos\Console\RepositoryInterfaceMakeCommand;

class LaravelReposServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryInterfaceMakeCommand::class,
            ]);
        }
    }

    public function register()
    {
    }
}
