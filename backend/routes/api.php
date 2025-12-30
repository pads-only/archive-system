<?php
require_once __DIR__ . '/../services/ErrorHandler.php';
require_once __DIR__ . "/../controllers/UserController.php";

// set_exception_handler("ErrorHandler::handleException");
// set_exception_handler("ErrorHandler::handleError");

//will return the url decomposition
$uri = explode("/", $_SERVER["REQUEST_URI"]);
//get the method used
$method = $_SERVER["REQUEST_METHOD"];

$controller = new UserController();

if ($uri[3] != "users") {
    http_response_code(404);
    exit;
}

//check if the 4th index is an id
$id = $uri[4] ?? null;

//pass the $method and $id as paramater to processrequest method
$controller->processRequest($method, $id);
