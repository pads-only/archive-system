<?php
class Database
{
    #database properties
    private $host = "localhost";
    private $dbname = "archive_system";
    private $username = "root";
    private $password = "";
    public $connection;

    public function getConnection()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            return $this->connection;
        } catch (PDOException $e) {
            http_response_code(500);
            die("Database Error: " . $e->getMessage());
        }
    }
}
