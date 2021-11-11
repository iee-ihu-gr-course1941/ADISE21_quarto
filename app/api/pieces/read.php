<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/Piece.php';

$database = new Database();
$db 	  = $database->connect();

$piece = new Piece($db);

try {
    $pieces = $piece->read();
    echo json_encode($pieces);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read one'));
    die();
}
