<?php

error_reporting(E_ERROR);

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';

$database = new Database();
$db 	  = $database->connect();

$session = new Session($db);

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;

echo json_encode($data);

try {
    if (!$user->validate_token()) {
        http_response_code(401);
        echo json_encode(array('message' => 'Invalid Token'));
        die();
    }

    $sessions = $session->read();
    echo json_encode($sessions);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read'));
    die();
}
