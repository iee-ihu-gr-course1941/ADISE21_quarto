<?php

error_reporting(E_ERROR);

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db 	  = $database->connect();

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;

echo json_encode($data);

if ($user->validate_token()) {
    echo json_encode(array('message' => 'Token is valid'));
} else {
    http_response_code(401);
    echo json_encode(array('message' => 'Invalid Token'));
    die();
}
