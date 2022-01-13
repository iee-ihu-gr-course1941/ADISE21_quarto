<?php

error_reporting(E_ERROR);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';

$method = $_SERVER['REQUEST_METHOD'];
if (!($method=='GET')) {
    http_response_code(405);
    die();
}

$database = new Database();
$db 	  = $database->connect();

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;
if (!$user->validate_token()) {
    http_response_code(401);
    echo json_encode(array('message' => 'Invalid Token'));
    die();
}

$session = new Session($db);

if (isset($_GET['session_id'])) {
    $session->id = $_GET['session_id'];
} else {
    http_response_code(403);
    echo json_encode(array('message' => 'Session id not provided'));
    die();
}
$remaining_pieces = $session->remaining_pieces();

if (count($remaining_pieces)>0) {
    echo json_encode($remaining_pieces);
} else {
    http_response_code(403);
    echo json_encode(array('message' => 'Unable to read remaining pieces'));
    die();
}
