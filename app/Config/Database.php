<?php

namespace RezaFikkri\PLM\Config;

use PDO;

class Database
{
    private static ?PDO $dbc = null;

    public static function getConnection(): PDO
    {
        // implement singleton design pattern
        if (is_null(self::$dbc)) {
            // create new database connection
            self::$dbc = new PDO(
                dsn: "$_ENV[DB_DRIVER]:host=$_ENV[DB_HOST];port=$_ENV[DB_PORT];dbname=$_ENV[DB_NAME]",
                username: $_ENV['DB_USER'],
                password: $_ENV['DB_PASSWORD'],
            );
        }
        return self::$dbc;
    }
}
