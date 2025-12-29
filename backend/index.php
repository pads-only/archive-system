<?php

require_once __DIR__ . '/services/ErrorHandler.php';
require_once __DIR__ . '/config/Database.php';

set_exception_handler("ErrorHandler::handleException");
set_exception_handler("ErrorHandler::handleError");

header("Content-type: application/json; charsetUTF-8");

// $path = explode("/", $_SERVER["REQUEST_URI"]);
// print_r($path);
// if ($path[3] != "user") {
//     http_response_code(404);
//     exit;
// }

// $database = new Database('localhost', 'archive_system', 'root', '');
// $conn = $database->getConnection();
