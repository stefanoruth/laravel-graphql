<?php

namespace Ruth\GraphQL;

class ObjectType extends Base
{
    protected $name = null;
    protected $description = null;
    protected $model = null;

    public function getName()
    {
        if (!is_null($this->name)) {
            return $this->name;
        }

        return get_class($this);
    }
}
