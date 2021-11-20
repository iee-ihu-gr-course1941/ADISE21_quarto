<?php

error_reporting(E_ERROR);

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

$user->id           = $data->id;
$user->access_token = $data->access_token;

try {
    if (!$user->validate_token()) {
        http_response_code(401);
        echo json_encode(array('message' => 'Invalid Token'));
        die();
    }

    if ($user->unset_token()) {
        echo json_encode(array('message' => 'Success'));
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Unable to logout'));
        die();
    }
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to logout'));
    die();
}
