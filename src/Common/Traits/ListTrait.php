<?php

namespace App\Common\Traits;

use ReflectionClass;

trait ListTrait
{
    /**
     * @return array<mixed>
     */
    public static function getConstantsList(): array
    {
        $class = new ReflectionClass(__CLASS__);
        $constants = $class->getConstants();
        $result = [];

        foreach ($constants as $constant) {
            $result[uniqid()] = $constant;

        }

        return $result;
    }
}
