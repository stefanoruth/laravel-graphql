<?php

namespace Ruth\GraphQL;

use GraphQL\Type\Definition\ObjectType;

class Query
{
    public function fields()
    {
        return [];
    }

    public function toType()
    {
        $name = get_class($this);

        return [$name => new ObjectType([
            'name' => $name,
            'fields' => $this->fields,
        ])];
    }
}
