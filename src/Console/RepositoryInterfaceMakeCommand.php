<?php

namespace Zebrainsteam\LaravelRepos\Console;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $defaultNamespacePrefix = 'Contracts\Repository';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository interface';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Interface';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($rawName) ||
            $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository-interface.stub';
    }
}
