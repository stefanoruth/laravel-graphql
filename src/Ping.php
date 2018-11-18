<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\Base\Query;
use GraphQL\Type\Definition\Type;

class Ping extends Query
{
    public function fields()
    {
        return Type::string();
    }

    public function resolve()
    {
        return 'pong';
    }
}
