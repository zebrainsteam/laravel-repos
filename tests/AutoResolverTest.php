<?php

namespace Zebrainsteam\LaravelRepos\Tests;

use Orchestra\Testbench\TestCase;
use Zebrainsteam\LaravelRepos\EloquentRepository;
use Zebrainsteam\LaravelRepos\Resolvers\AutoResolver;
use Zebrainsteam\LaravelRepos\Tests\Support\EloquentModel;
use Zebrainsteam\LaravelRepos\Tests\Support\ModelWithRepositoryCreator;
use Zebrainsteam\LaravelRepos\Tests\Support\SelfRepository;
use Zebrainsteam\LaravelRepos\Tests\Support\SimpleModel;
use InvalidArgumentException;

class AutoResolverTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'Zebrainsteam\LaravelRepos\LaravelReposServiceProvider'
        ];
    }

    /**
     * @test
     */
    public function exception_is_thrown_if_simple_model_resolved()
    {
        $resolver = new AutoResolver();

        $this->expectException(InvalidArgumentException::class);
        $resolver->resolve(SimpleModel::class);
    }

    /**
     * @test
     */
    public function eloquent_repository_is_created()
    {
        $resolver = new AutoResolver();

        $repository = $resolver->resolve(EloquentModel::class);
        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @test
     */
    public function self_repository_is_created()
    {
        $resolver = new AutoResolver();

        $repository = $resolver->resolve(ModelWithRepositoryCreator::class);
        $this->assertInstanceOf(SelfRepository::class, $repository);
    }
}
