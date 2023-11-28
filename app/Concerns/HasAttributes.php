<?php

namespace App\Concerns;

trait HasAttributes
{
    protected $attributes = [];

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttribute($key)
    {
        if(array_key_exists($key, $this->attributes)) {
            return $this->getAttributeFromArray($key);
        }

        return null;
    }

    public function getAttributeFromArray($key)
    {
        if(isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function offsetExists($offset)
    {
        return ! is_null($this->getAttribute($offset));
    }

    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

}
