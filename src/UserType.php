<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\Base\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType extends ObjectType
{
    public $description = 'User Model';

    public function fields()
    {
        return [
            'id' => Type::int(),
            'email' => Type::string(),
            'name' => Type::string(),
        ];
    }
}
