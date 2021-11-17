<?php

error_reporting(E_ALL ^ E_WARNING);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';

$database = new Database();
$db 	  = $database->connect();

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;
try {
    if (!$user->validate_token()) {
        http_response_code(401);
        echo json_encode(array('message' => 'Invalid Token'));
        die();
    }

    $session = new Session($db);
    $session->id      = $data->session_id;
    $remaining_pieces = $session->remaining_pieces();
    echo json_encode($remaining_pieces);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read'));
    die();
}
