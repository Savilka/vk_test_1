<?php

namespace App\Models;

class BaseModel
{
    protected  static array $fields = [];
    protected  static array $additionalFilterFields = [];
    public static function getFields(): array
    {
        return static::$fields;
    }

    public static function getFilterFields(): array
    {
        return static::$additionalFilterFields;
    }

}