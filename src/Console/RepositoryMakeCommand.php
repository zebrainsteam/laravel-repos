<?php

namespace Zebrainsteam\LaravelRepos\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--w|with-interface} {interface?} {--i|from-interface}';

    /**
     * @var string
     */
    protected $stub;

    /**
     * @var string
     */
    protected $defaultNamespacePrefix = 'Repositories';

    /**
     * @var string
     */
    protected $interfaceClassName = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $name = $this->getNameInput();
        $interfaceName = $this->argument('interface');
        $withInterface = $this->option('with-interface');
        $fromInterface = $this->option('from-interface');

        if (!empty($withInterface)) {
            // create repository from abstract class by implementing interface
            $this->stub = '/stubs/repository-from-abstract-with-interface.stub';
            $this->createInterface($name, $interfaceName);
        } elseif (!empty($fromInterface)) {
            // create repository by implementing interface
            $this->stub = '/stubs/repository-from-interface.stub';
            $this->createInterface($name, $interfaceName);
        } else {
            // create repository from abstract class
            $this->stub = '/stubs/repository-from-abstract.stub';
        }

        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.$this->stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['from-interface', 'i', InputOption::VALUE_NONE, 'Generate a repository via interface inheritance'],
            ['with-interface', 'w', InputOption::VALUE_NONE, 'Generate an interface and inherited repository'],
        ];
    }

    /**
     * Parse the interface class name and format according to the root namespace.
     *
     * @param $name
     * @return string
     */
    protected function qualifyInterfaceClass($name)
    {
        $interfaceName = $name;
        $name = ltrim($name, '\\/');

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getInterfaceNamespace(trim($rootNamespace, '\\'),  $interfaceName).'\\'.$name
        );
    }

    /**
     * Get namespace for interface class.
     *
     * @param $rootNamespace
     * @param $interfaceName
     * @return string
     */
    protected function getInterfaceNamespace($rootNamespace, $interfaceName)
    {
        $interfaceNamespace = $rootNamespace;
        if ($interfaceName[0] != '/') {
            $interfaceNamespace .= '\Contracts\Repository';
        }

        return $interfaceNamespace;
    }

    /**
     * Create interface for new repository
     *
     * @param string $repositoryName
     * @param string|null $interfaceName
     * @return int
     */
    protected function createInterface(string $repositoryName, $interfaceName)
    {
        if (empty($interfaceName)) {
            $interfaceName = $repositoryName . 'Contract';
        }
        $this->interfaceClassName = $this->qualifyInterfaceClass($interfaceName);
        return Artisan::call('make:repository-interface', [
            'name' => $interfaceName
        ]);
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        if (!empty($this->interfaceClassName)) {
            $replace = $this->buildInterfaceReplacements();

            return str_replace(
                array_keys($replace), array_values($replace), parent::buildClass($name)
            );
        } else {
            return parent::buildClass($name);
        }
    }

    /**
     * Build the replacements for interface.
     *
     * @return null[]|string[]
     */
    protected function buildInterfaceReplacements()
    {
        return [
            '{{ interface }}' => $this->interfaceClassName,
            '{{interface}}' => $this->interfaceClassName,
        ];
    }
}
