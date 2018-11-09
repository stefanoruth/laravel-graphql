<?php

namespace Ruth\GraphQL\Base;

use GraphQL\Type\Definition\ObjectType;
use ReflectionClass;
use Ruth\GraphQL\ConvertToType;

abstract class Query
{
    use ConvertToType;
}
