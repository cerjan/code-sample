<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

use ReflectionClass;

trait DtoFactory
{
    public static function from(mixed $entity): self
    {
        $self = new self();

        $reflect = new ReflectionClass($self);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $prop) {
            $method = 'get' . ucfirst($prop->name);

            if (method_exists($entity, $method)) {
                if (class_exists($prop->getType()->getName())) {
                    if ($self::isDto($prop->getType()->getName())) {
                        $self->{$prop->name} = call_user_func_array([$prop->getType()->getName(), 'from'], [call_user_func([$entity, $method])]);
                    }
                } else {
                    $self->{$prop->name} = call_user_func([$entity, $method]);
                }
            }
        }

        return $self;
    }

    protected static function isDto(string $class): bool
    {
        $reflect = new ReflectionClass($class);

        return in_array('App\Http\Response\Dto\DtoFactory', $reflect->getTraitNames());
    }
}