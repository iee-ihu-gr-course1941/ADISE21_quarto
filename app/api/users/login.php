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

if ($data->username === "" || $data->username === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'Username cant be null'));
    die();
} else {
    $user->username = $data->username;
}

if ($data->password === "" || $data->password === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'Password cant be null'));
    die();
} else {
    $user->password = $data->password;
}

if ($user->authenticate() && $user->set_token()) {
    echo json_encode(array(
    'id' 	   => $user->id,
    'access_token' => $user->access_token));
} else {
    http_response_code(401);
    echo json_encode(array('message' => 'Wrong Username or Password'));
    die();
}
