<?php

class Database
{
    private static ?database $instance = null;
    public PDO $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO('mysql:host=localhost;dbname=kari;charset=utf8', 'root', '');
            $this->connection->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->SetAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->exec("SET CHARACTER SET utf8");

        } catch (PDOException $e) {
            die('connection failed' . $e->getMessage());
        }
    }

    public static function get_instance(): database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function connection(): PDO {
        return $this->connection;
    }
}

