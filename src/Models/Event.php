<?php

namespace App\Models;

class Event extends BaseModel
{
    protected static array $fields = ['id', 'name', 'status', 'user', 'add_date'];
}