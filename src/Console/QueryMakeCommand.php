<?php

namespace Ruth\GraphQL\Console;

use Illuminate\Console\GeneratorCommand;

class QueryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:graphql:query';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new graphql query class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'GraphQL Query';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/query.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Query';
    }
}
