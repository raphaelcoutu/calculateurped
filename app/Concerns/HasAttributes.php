<?php

namespace App\Concerns;

trait HasAttributes
{
    protected $attributes = [];

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->getAttributeFromArray($key);
        }

        return null;
    }

    public function getAttributeFromArray($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return ! is_null($this->getAttribute($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->setAttribute($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
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
