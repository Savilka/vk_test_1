<?php

namespace App\Models;

class Event extends BaseModel
{
    protected static array $fields = ['id', 'name', 'status', 'user', 'add_date'];
    protected static array $additionalFilterFields = ['add_date_to', 'add_date_from'];
}