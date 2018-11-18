<?php

namespace Ruth\GraphQL;

use GraphQL\Type\Definition\Type as BaseType;

class Type extends BaseType
{
    public static function listOf($wrappedType)
    {
        return parent::listOf(GraphQL::type($wrappedType));
    }
}
