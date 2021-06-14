<?php

namespace Zebrainsteam\LaravelRepos\Tests;

use Illuminate\Contracts\Console\Kernel;
use Repositories\Core\RepositoryFactory;
use Repositories\Core\Resolvers\ContainerAwareResolver;

class LaravelReposServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function container_aware_resolver_is_bound()
    {
        $containerAwareResolver = new ContainerAwareResolver($this->app);
        $this->assertEquals($containerAwareResolver, app(ContainerAwareResolver::class));
    }

    /**
     * @test
     */
    public function repository_factory_is_bound()
    {
        $repositoryFactory = app('repository-factory');
        $this->assertInstanceOf(RepositoryFactory::class, $repositoryFactory);
    }

    /**
     * @test
     */
    public function registers_console_commands()
    {
        /** @var Kernel $kernel */
        $kernel   = $this->app->make(Kernel::class);
        $commands = $kernel->all();
        $this->assertArrayHasKey('make:repository-interface', $commands);
        $this->assertArrayHasKey('make:repository', $commands);
    }
}
