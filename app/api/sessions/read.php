<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';

$database = new Database();
$db 	  = $database->connect();

$session = new Session($db);

$data = json_decode(file_get_contents('php://input'));

if ($data->id              === ""
    || $data->id           === null
    || $data->access_token === ""
    || $data->access_token === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'access token cant be empty'));
    die();
} else {
    $user               = new User($db);
    $user->id           = $data->id;
    $user->access_token = $data->access_token;

    if (!$user->validate_token()) {
        http_response_code(401);
        echo json_encode(array('message' => 'Invalid Token'));
        die();
    }
}

try {
    $sessions = $session->read();
    echo json_encode($sessions);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read'));
    die();
}
