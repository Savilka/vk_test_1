<?php

namespace App\Database;
use PDO;

class Db
{
    public static function connect()
    {
        $conStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $_ENV['HOST'],
            $_ENV['PORT'],
            $_ENV['DATABASE'],
            $_ENV['USER'],
            $_ENV['PASSWORD']
        );

        $conn = new PDO($conStr);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}