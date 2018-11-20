<?php

namespace Ruth\GraphQL;

use Illuminate\Support\Collection;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\GraphQL as GraphQLCore;
use GraphQL\Type\Schema;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Ruth\GraphQL\Base\GraphQLBase;
use Symfony\Component\Finder\Finder;
use GraphQL\Validator\Rules\DisableIntrospection;
use GraphQL\Validator\Rules\QueryComplexity;

class GraphQL
{
    public static $types = [];
    public static $queries = [];
    public static $mutations = [];

    /**
     * Build graphql server and execute a query against it
     *
     * @param string|null $query
     * @param string|null $variables
     * @param string|null $operationName
     * @return \GraphQL\GraphQL
     */
    public static function executeQuery($query = null, $variables = null, $operationName = null)
    {
        if (is_null($query)) {
            throw new Exception('No Graphql query provided');
        }

        if (empty(static::$queries)) {
            static::loadQueries(PingPong::class);
        }

        $rules = [];

        if (config('graphql.introspection') === false) {
            $rules[] = new DisableIntrospection;
        }

        if (($maxComplexity = config('graphql.max_query_complexity')) > 0) {
            $rules[] = new QueryComplexity($maxComplexity);
        }
        
        if (($maxDepth = config('graphql.max_query_depth')) > 0) {
            $rules[] = new QueryDepth($maxDepth);
        }

        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => static::$queries,
            ]),
            'mutation' => new ObjectType([
                'name' => 'RootMutation',
                'fields' => static::$mutations,
            ]),
        ]);

        return GraphQLCore::executeQuery(
            $schema,
            $query,
            null,
            null,
            $variables,
            $operationName,
            null,
            $rules
        )->toArray();
    }

    /**
     * Fetches an type from the type list if exists
     *
     * @param string $class
     * @return object|null
     */
    public static function type($class)
    {
        if (array_key_exists($class, static::$types)) {
            return static::$types[$class];
        }
    }

    /**
     * Convert a className into a valid GraphQl Endpoint name
     *
     * @param string $class
     * @return string
     */
    public static function generateName($class)
    {
        if (isset($class->name) && !is_null($class->name)) {
            return $class->name;
        }

        if (is_object($class)) {
            $class = get_class($class);
        }

        return preg_replace(
            '/^(?:(?:.+)\\\)?(.+)/i',
            '$1',
            preg_replace(
                '/(Query|Type|Mutation)$/',
                '',
                $class
            )
        );
    }

    /**
     * Load query classes
     *
     * @param string[] $queries
     * @return arrayd
     */
    public static function loadQueries($queries)
    {
        return static::$queries = static::convertClassToGraphObject($queries);
    }

    /**
     * Load mutation classes
     *
     * @param string[] $mutations
     * @return array
     */
    public static function loadMutations($mutations)
    {
        return static::$mutations = static::convertClassToGraphObject($mutations);
    }

    /**
     * Loads and converts type classNames into GraphQL Types.
     *
     * @param string[] $types
     * @return array
     */
    public static function loadTypes($types)
    {
        return static::$types = Collection::make($types)->mapWithKeys(function ($type) {
            $class = new $type;

            return [$type => new ObjectType([
                'name' => static::generateName($class),
                'description' => $class->description ?? null,
                'fields' => $class->fields(),
            ])];
        })->toArray();
    }

    /**
     * Convert classPaths into graphql simple objects
     *
     * @param string[] $items
     * @return void
     */
    public static function convertClassToGraphObject($items)
    {
        return Collection::make($items)->mapWithKeys(function ($item) {
            $class = new $item;

            $name = static::generateName($class);

            $fields = $class->fields();

            if (is_array($fields)) {
                $type = new ObjectType([
                    'name' => $name,
                    'fields' => function () use ($fields) {
                        return $fields;
                    },
                ]);
            } else {
                $type = $fields;
            }

            $object = [
                'type' => $type,
                'args' => method_exists($class, 'args') ? $class->args() : [],
            ];

            if (method_exists($class, 'resolve')) {
                $object['resolve'] = function ($value, $args, $context, $info) use ($class) {
                    return $class->resolve($value, (object) $args, $context, $info);
                };
            }

            if (method_exists($class, 'complexity')) {
                $object['complexity'] = function ($childrenComplexity, $args) use ($class) {
                    return $class->resolve($childrenComplexity, $args);
                };
            }

            // dd($object, $class, method_exists($class, 'resolve'));

            return [$name => $object];
        })->toArray();
    }
}
