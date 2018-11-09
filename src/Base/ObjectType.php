<?php

namespace Ruth\GraphQL\Base;

use GraphQL\Type\Definition\ObjectType as GraphQLObjectType;
use Ruth\GraphQL\ConvertToType;

abstract class ObjectType
{
    use ConvertToType;
}
