<?php

namespace Ruth\GraphQL;

use Illuminate\Support\Collection;
use GraphQL\Type\Definition\ObjectType;

class GraphQL
{
    public static $types = [];

    public static function loadTypes($types)
    {
        return static::$types = Collection::make($types)->mapWithKeys(function ($type) {
            $class = new $type;

            return [$type => new ObjectType([
                'name' => static::generateName($class),
                'description' => $class->description,
                'fields' => $class->fields(),
            ])];
        })->toArray();
    }

    public static function type($class)
    {
        if (array_key_exists($class, static::$types)) {
            return static::$types[$class];
        }
    }

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

    public static function loadQueries($queries)
    {
        return static::load($queries);
    }

    public static function loadMutations($mutations)
    {
        return static::load($mutations);
    }

    public static function load($items)
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

            return [$name => [
                'type' => $type,
                'args' => method_exists($class, 'args') ? $class->args() : [],
                'resolve' => function ($value, $args, $context, $info) use ($class) {
                    return $class->resolve($value, (object) $args, $context, $info);
                },
            ]];
        })->toArray();
    }
}
