<?php

namespace Zebrainsteam\LaravelRepos\Tests;

use Orchestra\Testbench\TestCase;
use Zebrainsteam\LaravelRepos\EloquentRepository;
use Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver;
use Zebrainsteam\LaravelRepos\Tests\Support\EloquentModel;
use InvalidArgumentException;

class EloquentAwareResolverTest extends TestCase
{
    /**
     * @test
     */
    public function eloquent_repository_is_created()
    {
        $resolver = new EloquentAwareResolver();

        $repository = $resolver->resolve(EloquentModel::class);
        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @test
     */
    public function exception_is_thrown_if_non_existent_model_resolved()
    {
        $resolver = new EloquentAwareResolver();

        $this->expectException(InvalidArgumentException::class);
        $repository = $resolver->resolve("NonExistentModel");
    }
}
