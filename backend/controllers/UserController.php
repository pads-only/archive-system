<?php
require_once __DIR__ . '/../services/UserService.php';

class UserController
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = new UserService();
    }

    public function processRequest($method, $id = null)
    {
        if ($id) {
            $this->processResouceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResouceRequest($method, $id)
    {
        $user = $this->gateway->getUserById($id);

        //chck if the user exist in the database
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                "message" => "User does not exist"
            ]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode([$user]);
                break;
            case "PATCH":
                $data = json_decode(file_get_contents("php://input"), true);

                $current_user_id = $user['user_id'];

                $id = $this->gateway->update($current_user_id, $data);

                //set http response code to 402 which is created
                http_response_code(201);
                echo json_encode([
                    "message: " => "User has been updated!",
                    "row: " => $id
                ]);

                break;
            case "DELETE":
                $row = $this->gateway->delete($id);
                if ($row) {
                    echo json_encode([
                        "message: " => "The user $id has been deleted",
                        "row: " => $row,
                    ]);
                }

                break;
            default;
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }

    private function processCollectionRequest($method)
    {
        switch ($method) {
            case "GET":
                echo json_encode([$this->gateway->getAllUser()]);
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input"), true);

                //validate if theres a given data
                if (!$data || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                    http_response_code(422);
                    echo json_encode(['message' => 'All fields are required']);
                    break;
                }

                /**
                 * get the id from the newly created user
                 * this happens because create method returns lastInsertId();
                 */
                $id = $this->gateway->create($data);

                //set http response code to 402 which is created
                http_response_code(201);
                echo json_encode([
                    "message: " => "User has been created!",
                    "id: " => $id
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
}
