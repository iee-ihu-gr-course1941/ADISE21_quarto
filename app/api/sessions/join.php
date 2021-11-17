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

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;

if (!$user->validate_token()) {
    http_response_code(401);
    echo json_encode(array('message' => 'Invalid Token'));
    die();
}

if ($data->session_id === "" || $data->session_id === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'Session id cant be empty'));
    die();
} else {
    $session->id = $data->session_id;
}

$session->player2_id = $data->id;

try {
    if (!($session->is_playing($session->player2_id)) && $session->join()) {
        echo json_encode(array('message' => 'Player 2 joined successfully'));
    } else {
        http_response_code(403);
        echo json_encode(array('message' => 'Unable to join game'));
        die();
    }
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to join game'));
    die();
}
