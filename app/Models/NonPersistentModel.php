<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 18/09/17
 * Time: 12:48
 */

namespace Equinox\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

abstract class NonPersistentModel implements Arrayable, Jsonable
{

    /**
     * Holds an list of mutator methods, so we don't need to create every time we need one.
     * @var array
     */
    protected $getMutatorCache = [];

    /** @var array */
    protected $setMutatorCache = [];

    /**
     * Get an attribute from the model.
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        $value = $this->$key ?? null;

        if ($this->hasGetMutator($key)) {
            $method = $this->createGetMutator($key);

            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * Dynamically retrieve attributes on the model.
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Set a given attribute on the model.
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setAttribute(string $key, $value): self
    {
        if ($this->hasSetMutator($key)) {
            $method = $this->createSetMutator($key);

            return $this->{$method}($value);
        }

        $this->$key = $value;

        return $this;
    }

    /**
     * Dynamically set attributes on the model.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Return if the attribute exists on model.
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->$key);
    }

    /**
     * Removes an attribute from model.
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->$key);
    }

    /**
     * Convert the model instance to JSON.
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Transform the model into a json string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }


    /**
     * Generates the get mutator.
     * @param string $key
     * @return string
     */
    protected function createGetMutator(string $key): string
    {
        if (! array_key_exists($key, $this->getMutatorCache)) {
            $this->getMutatorCache[$key] = 'get' . Str::studly($key) . 'Attribute';
        }

        return $this->getMutatorCache[$key];
    }

    /**
     * Generates the set mutator.
     * @param string $key
     * @return string
     */
    protected function createSetMutator(string $key): string
    {
        if (! array_key_exists($key, $this->setMutatorCache)) {
            $this->setMutatorCache[$key] = 'set' . Str::studly($key) . 'Attribute';
        }

        return $this->setMutatorCache[$key];
    }

    /**
     * Determine if a get mutator exists for an attribute.
     * @param string $key
     * @return bool
     */
    public function hasGetMutator(string $key): bool
    {
        return method_exists($this, $this->createGetMutator($key));
    }

    /**
     * Determine if a set mutator exists for an attribute.
     * @param string $key
     * @return bool
     */
    public function hasSetMutator($key): bool
    {
        return method_exists($this, $this->createSetMutator($key));
    }
}
