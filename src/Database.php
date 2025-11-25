<?php

class Database {
    private static ?\PDO $pdo = null;

    public static function pdo(): \PDO {
        if (self::$pdo === null) {

            $host = "localhost";
            $port = "3306";
            $db   = "control";
            $user = "root";
            $pass = "1112019"; 

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
        return self::$pdo;
    }
}
