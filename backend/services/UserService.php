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

    /**
     * 
     * /**
     * create user object and fills it with data 
     * from the request and pass it to create() in the userserive
     */

    //add user
    public function create($data)
    {
        // hashed the password before assigning to Usermodel object
        $password = $data['password'];
        $hash_pwd = password_hash($password, PASSWORD_DEFAULT);
        /**
         * get the data and ccreate instance of user model
         */
        $user = new UserModel();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $hash_pwd;
        /**
         * used transaction to ensure when a new user is created
         * a role will be assign to that user
         */
        $this->conn->beginTransaction();

        //query
        $sql = "INSERT INTO users 
                (name, email, password)
                VALUES (:name, :email, :password)
                ";
        //prepare
        $stmt = $this->conn->prepare($sql);

        //bind value
        $stmt->bindValue(":name", $user->name);
        $stmt->bindValue(":email", $user->email);
        $stmt->bindValue(":password", $user->password);

        //execute
        $stmt->execute();

        //get the id of the newly created user
        $new_user_id = $this->conn->lastInsertId();

        /**
         * this will add role to user
         */
        $sql_user_role = "INSERT INTO user_roles (user_id, role_id)
                          VALUES (:user_id, :role_id)
                          ";

        $stmt_user_role = $this->conn->prepare($sql_user_role);

        $stmt_user_role->bindValue(":user_id", $this->conn->lastInsertId());
        $stmt_user_role->bindValue(":role_id", 1);

        $stmt_user_role->execute();

        //commit the sql queries

        $this->conn->commit();

        return $new_user_id;
    }
    //update user info
    public function update($current_user_id, $new)
    {
        /**
         * get the current user by id
         */
        $current_user = $this->getUserById($current_user_id);

        //validate if user exist
        if (!$current_user) {
            http_response_code(404);
            echo json_encode([
                "message: " => "No user found",
            ]);
            return;
        }

        /**
         * if new value is provided for password hash it
         */
        if (isset($new["password"])) {
            $hash_pwd = password_hash($new["password"], PASSWORD_DEFAULT);
        }

        /**
         * assign to new instance of usermodel if new data is set
         * if user set value for name, email or password then
         */

        $user = new UserModel();
        $user->name = isset($new["name"]) ? $new["name"] : $current_user["name"];
        $user->email = isset($new["email"]) ? $new["email"] : $current_user["email"];
        $user->password = isset($new["password"]) ? $hash_pwd : $current_user["password"];


        $sql = "UPDATE users
                SET name = :name, email = :email, password = :password
                WHERE user_id = :id
                ";

        $stmt = $this->conn->prepare($sql);

        /**
         * bind the value from new data
         * if name has new value then bind that to the name 
         */
        $stmt->bindValue(":name", $user->name ?? $current_user["name"]);
        $stmt->bindValue(":email", $user->email ?? $current_user["email"]);
        $stmt->bindValue(":password", $user->password ?? $current_user["password"]);

        $stmt->bindValue(":id", $current_user["user_id"]);
        //execute
        $stmt->execute();

        // return the count of affected row in database
        return $stmt->rowCount();
    }
    //delete user
    /**
     * 
     * used transaction to delete user since 
     * user_id is foreign key to user_roles
     * used rollback() in case of error in deleting user
     * rollback will reverse the changes
     */
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            $sql_role = "DELETE
                        FROM user_roles
                        WHERE user_id = :id
                        ";
            $role_stmt = $this->conn->prepare($sql_role);
            $role_stmt->bindValue(":id", $id);
            $role_stmt->execute();

            $sql = "DELETE 
                    FROM users 
                    WHERE user_id = :id
                    ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Throwable $th) {
            echo json_encode([
                "message: " => "There's an error deleting the user $id! Rollback executed!"
            ]);
            $this->conn->rollBack();
        }

        return $stmt->rowCount();
    }
}
