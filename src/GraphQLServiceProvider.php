<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\TypeMakeCommand;
use Ruth\GraphQL\QueryMakeCommand;
use Ruth\GraphQL\MutationMakeCommand;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(GraphQL::class);
        
        if ($this->app->runningInConsole()) {
            $this->loadCommands();

            $this->publishes([
                __DIR__.'/../config/graphql.php' => config_path('graphql.php'),
            ]);
        }

        if (config('graphql.routes')) {
            $this->loadRoutesFrom(__DIR__.'/routes.php');
        }
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

    public function loadCommands()
    {
        $this->commands([
            MutationMakeCommand::class,
            QueryMakeCommand::class,
            TypeMakeCommand::class,
        ]);
    }


    /**
     * Register all of the commands in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function load($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();

        foreach ((new Finder)->in($paths)->files() as $command) {
            $command = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getPathname(), app_path() . DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($command, Base::class) && !(new \ReflectionClass($command))->isAbstract()) {
                // Load
                Artisan::starting(function ($artisan) use ($command) {
                    $artisan->resolve($command);
                });
            }
        }
    }

    public function types()
    {
        $this->load($this->app->path('GraphQL/Type'));
    }

    public function queries()
    {
        $this->load($this->app->path('GraphQL/Query'));
    }

    public function mutations()
    {
        $this->load($this->app->path('GraphQL/Mutation'));
    }
}
