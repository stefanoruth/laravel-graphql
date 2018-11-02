<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\TypeMakeCommand;
use Ruth\GraphQL\QueryMakeCommand;
use Ruth\GraphQL\MutationMakeCommand;
use Illuminate\Support\ServiceProvider;

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
}
