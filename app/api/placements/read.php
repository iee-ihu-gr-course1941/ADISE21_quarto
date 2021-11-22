<?php

error_reporting(E_ALL ^ E_WARNING);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/Placement.php';
include_once '../../models/User.php';

$database = new Database();
$db 	  = $database->connect();

$placement = new Placement($db);

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;
if (!$user->validate_token()) {
    http_response_code(401);
    echo json_encode(array('message' => 'Invalid Token'));
    die();
}


if (isset($_GET['session_id'])) {
    $placement->session_id = $_GET['session_id'];
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Session id not provided'));
    die();
}

$placements= $placement->read();

if (count($placements) > 0) {
    echo json_encode($placements);
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read'));
    die();
}
