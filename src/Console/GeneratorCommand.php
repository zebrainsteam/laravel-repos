<?php


namespace Zebrainsteam\LaravelRepos\Console;

use Illuminate\Console\GeneratorCommand as Generator;

abstract class GeneratorCommand extends Generator
{
    /**
     * @var string
     */
    protected $defaultNamespacePrefix = '';

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
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $defaultNamespace = $rootNamespace;
        if ($this->getNameInput()[0] != '/' && !empty($this->defaultNamespacePrefix)) {
            $defaultNamespace .= '\\' . $this->defaultNamespacePrefix;
        }

        return $defaultNamespace;
    }
}
