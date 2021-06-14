<?php

namespace Zebrainsteam\LaravelRepos\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zebrainsteam\LaravelRepos\LaravelReposServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelReposServiceProvider::class,
        ];
    }
}
