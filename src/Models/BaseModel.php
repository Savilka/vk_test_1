<?php

namespace App\Models;

class BaseModel
{
    protected  static array $fields = [];
    public static function getFields(): array
    {
        return static::$fields;
    }

}