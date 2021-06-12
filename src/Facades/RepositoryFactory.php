<?php

namespace Zebrainsteam\LaravelRepos\Facades;

use Illuminate\Support\Facades\Facade;

class RepositoryFactory extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'repository-factory';
    }
}
