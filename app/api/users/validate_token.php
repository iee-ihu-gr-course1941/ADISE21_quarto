<?php

error_reporting(E_ERROR);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/User.php';

$method = $_SERVER['REQUEST_METHOD'];
if (!($method=='GET')) {
    http_response_code(405);
    die();
}

$database = new Database();
$db 	  = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;

if ($user->validate_token()) {
    echo json_encode(array('username' => $user->username));
} else {
    http_response_code(401);
    echo json_encode(array('message' => 'Invalid Token'));
    die();
}
