<?php
class Database
{
    /*
    Constructor Property Promotion
    starting php 8.0, we no longer need to declare a properties and pass it into the
    constructor and manually assign it
    */
    public function __construct(
        private $host,
        private $dbname,
        private $username,
        private $password,
    ) {}

    public function getConnection()
    {
        try {
            //dsn stands for data source name
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";

            return new PDO($dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
