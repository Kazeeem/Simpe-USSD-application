<?php

namespace App\Bases;

use App\Utility;
use \PDO;

class DB
{
    private $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=".Utility::SERVER_NAME.";dbname=".Utility::DB_NAME."";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->pdo = new PDO($dsn, Utility::DB_USER, Utility::DB_PASS, $options);
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function connectToDB()
    {
        return $this->pdo;
    }

    public function closeDB():void
    {
        $this->pdo = null;
    }
}