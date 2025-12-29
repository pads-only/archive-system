<?php
// require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/routes/api.php';


header("Content-type: application/json; charsetUTF-8");

// $path = explode("/", $_SERVER["REQUEST_URI"]);
// $method = $_SERVER["REQUEST_METHOD"];

// var_dump($path);
// var_dump($method);

// print_r($path);
// if ($path[3] != "user") {
//     http_response_code(404);
//     exit;
// }

// $database = new Database('localhost', 'archive_system', 'root', '');
// $conn = $database->getConnection();
