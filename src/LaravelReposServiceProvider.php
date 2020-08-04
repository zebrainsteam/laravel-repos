<?php


namespace Zebrainsteam\LaravelRepos;

use Illuminate\Support\ServiceProvider;
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
    }

    public function register()
    {
    }
}
