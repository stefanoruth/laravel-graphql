<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\GraphQL;
use Ruth\GraphQL\Console\TypeMakeCommand;
use Ruth\GraphQL\Console\QueryMakeCommand;
use Ruth\GraphQL\Console\MutationMakeCommand;
use Illuminate\Support\ServiceProvider;
use Ruth\GraphQL\Base\GraphQLBase;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app->singleton(GraphQL::class);
        
        if ($this->app->runningInConsole()) {
            $this->loadCommands();

            $this->publishes([
                __DIR__.'/../config/graphql.php' => config_path('graphql.php'),
            ]);
        }

        if (config('graphql.routes')) {
            $this->loadRoutesFrom(__DIR__.'/routes.php');
        }

        $this->loadClasses(
            $this->app->path('GraphQL/Type'),
            $this->app->path('GraphQL/Query'),
            $this->app->path('GraphQL/Mutation')
        );
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/graphql.php',
            'graphql'
        );
    }

    /**
     * Load all Commands in package
     *
     * @return void
     */
    public function loadCommands()
    {
        $this->commands([
            MutationMakeCommand::class,
            QueryMakeCommand::class,
            TypeMakeCommand::class,
        ]);
    }

    /**
     * Autoload Types, Queries and
     *
     * @param string $typePath
     * @param string $queryPath
     * @param string $mutationPath
     * @return void
     */
    public function loadClasses($typePath, $queryPath, $mutationPath)
    {
        GraphQL::loadTypes($this->loadDir($typePath));
        GraphQL::loadQueries($this->loadDir($queryPath));
        GraphQL::loadMutations($this->loadDir($mutationPath));
    }


    /**
     * Find all classes in a directory
     *
     * @param  array|string  $paths
     * @return void
     */
    public function loadDir($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();

        $files = [];

        foreach ((new Finder)->in($paths)->files() as $command) {
            $command = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getPathname(), app_path() . DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($command, GraphQLBase::class) && !(new \ReflectionClass($command))->isAbstract()) {
                $files[] = $command;
            }
        }

        return $files;
    }
}
