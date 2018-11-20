<?php

namespace Ruth\GraphQL;

use GraphQL\Type\Definition\Type as BaseType;

class Type extends BaseType
{
    public static function listOf($wrappedType)
    {
        if (is_string($wrappedType)) {
            return parent::listOf(GraphQL::type($wrappedType));
        }

        return parent::listOf($wrappedType);
    }

    public static function of($wrappedType)
    {
        return GraphQL::type($wrappedType);
    }
}
