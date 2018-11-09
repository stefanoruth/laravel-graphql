<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\Base\Query;
use GraphQL\Type\Definition\Type;

class ListUsersQuery extends Query
{
    public function args()
    {
        return [
            'count' => Type::int(),
        ];
    }

    public function fields()
    {
        return [
            'users' => [
                'type' => Type::listOf(GraphQL::type(UserType::class)),
            ],
        ];
    }

    public function resolve($value, $args, $context, $info)
    {
        $users = [
                ['name' => 'Foo'],
                ['name' => 'Bar'],
                ['name' => 'Baz'],
                ['name' => 'John'],
                ['name' => 'Jane'],
                ['name' => 'Doe'],
        ];

        return [
            'users' => array_splice($users, 0, $args->count ?? 5),
        ];
    }
}
