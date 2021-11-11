<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents('php://input'));

if ($data->access_token === "" || $data->access_token === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'Access token cant be empty'));
    die();
} else {
    $user->access_token = $data->access_token;
}

if ($data->id === "" || $data->id === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'id cant be empty'));
    die();
} else {
    $user->id = $data->id;
}

try {
    if ($user->validate_token()) {
        echo json_encode(array('message' => 'Valid Token'));
    } else {
        http_response_code(401);
        echo json_encode(array('message' => 'Invalid Token'));
        die();
    }
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to validate token'));
    die();
}
