<?php

namespace App\Entity;

class Entity
{
    public function __get($name) 
    {
        if(!$this->hasProp($name)) {
            throw new \Exception('Undefined property ' . $name . '.');
        }
    }

    public function __set($name, $value) 
    {
        if(!$this->hasProp($name)) {
            throw new \Exception('Undefined property ' . $name . '.');
        }
    }

    protected function hasProp(string $propName): bool
    {
        return (bool)array_key_exists($propName, static::AVAILABLE_PROPS);
    }

    protected function setTypeOfPropValue($propName, $value)
    {
        $extendTypes = ['date'];

        if(!in_array(static::AVAILABLE_PROPS[$propName], $extendTypes)) {
            settype($value, static::AVAILABLE_PROPS[$propName]);
        } else {
            switch(static::AVAILABLE_PROPS[$propName]) {
                case 'date': 
                    $value = new \DateTime($value);
                    break;
                default:
                    // Do nothing or throw exception (future)
            }
        }

        return $value;
    }
}