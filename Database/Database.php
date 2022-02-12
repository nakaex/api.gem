<?php

namespace Api\Database;

use \PDO;
use \PDOException;

class Database
{
    private $dbh = null;

    function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $dsn = $_ENV['DSN'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->dbh = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }
    }
}
