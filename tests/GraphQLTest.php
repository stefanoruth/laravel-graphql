<?php

namespace Ruth\GraphQL\Tests;

use PHPUnit\Framework\TestCase;
use Ruth\GraphQL\GraphQL;

class GraphQLTest extends TestCase
{
    public function testFormatClassNaming()
    {
        $this->assertEquals('Foo', GraphQL::generateName('Foo'));
        $this->assertEquals('Baz', GraphQL::generateName('Foo\Bar\Baz'));
        $this->assertEquals('Baz', GraphQL::generateName('Foo\Bar\BazQuery'));
        $this->assertEquals('BazFoo', GraphQL::generateName('Foo\Bar\BazFooQuery'));
        $this->assertEquals('Foo', GraphQL::generateName('FooMutation'));
        $this->assertEquals('Foo', GraphQL::generateName('FooType'));
        $this->assertEquals('User', GraphQL::generateName('App\GraphQL\Type\UserType'));
    }
}
