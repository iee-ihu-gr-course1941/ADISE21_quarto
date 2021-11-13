<?php

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

try {
    $placements = $placement->read();
    echo json_encode(array('placements' => $placements));
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read'));
    die();
}