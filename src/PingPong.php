<?php

namespace Ruth\GraphQL;

use Ruth\GraphQL\Base\Query;
use GraphQL\Type\Definition\Type;

class PingPong extends Query
{
    public $name = 'ping';

    public function fields()
    {
        return Type::string();
    }

    public function resolve()
    {
        return 'pong';
    }
}
