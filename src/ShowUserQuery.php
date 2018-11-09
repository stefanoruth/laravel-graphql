<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\Base\Query;
use GraphQL\Type\Definition\Type;
use Ruth\GraphQL\UserType;

class ShowUserQuery extends Query
{
    public $description = 'Show a single user';

    public function args()
    {
        return [
            'id' => Type::int(),
        ];
    }

    public function fields()
    {
        return GraphQL::type(UserType::class);
    }

    public function resolve($value, $args, $context, $info)
    {
        return ['id' => $args->id ?? null, 'name' => 'Foo'];
    }
}
