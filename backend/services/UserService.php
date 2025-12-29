<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/UserModel.php';

//this is where the query and input validation happens
class UserService
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    //get all user
    public function getAllUser()
    {
        //sql query
        $sql = "SELECT *
                FROM users";

        //call the query method to the db
        $stmt = $this->conn->query($sql);

        //empty array to store the result of the query
        $data = [];

        //use will loop to get all the data and assign it to data as an array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        // return the array data
        return $data;
    }

    //get user by id
    public function getUserById($id)
    {
        $sql = "SELECT *
                FROM users
                WHERE user_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    //add user
    public function create(UserModel $user)
    {
        // //validation
        // if (empty($user->name) || empty($this->email) || empty($this->password)) {
        //     http_response_code(422);
        //     echo json_encode([
        //         "message: " => "All fields are required"
        //     ]);
        //     return;
        // }
        //query
        $sql = "INSERT INTO users (name, email, password)
                VALUES (:name, :email, :password)";
        //prepare
        $stmt = $this->conn->prepare($sql);

        //bind value
        $stmt->bindValue(":name", $user->name);
        $stmt->bindValue(":email", $user->email);
        $stmt->bindValue(":password", $user->password);

        //execute
        $stmt->execute();

        return $this->conn->lastInsertId();
    }
    //update user info
    //delete user

}
