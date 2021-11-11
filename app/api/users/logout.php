<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db 	  = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents('php://input'));

if ($data->id === "" || $data->id === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'Id cant be null'));
    die();
} else {
    $user->id = $data->id;
}

if ($data->access_token === "" || $data->access_token === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'Access token cant be null'));
    die();
} else {
    $user->access_token = $data->access_token;
}

try {
    $user->unset_token();
    echo json_encode(array('message' => 'Success'));
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to logout'));
    die();
}
