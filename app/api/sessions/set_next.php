<?php

error_reporting(E_ERROR);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
  Content-Type, 
  Access-Control-Allow-Methods,
  Authorization, 
  X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';
include_once '../../models/Piece.php';

$method = $_SERVER['REQUEST_METHOD'];
if (!($method=='PUT')) {
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

$piece = new Piece($db);
$piece->id = $data->next_piece_id;

$session = new Session($db);

if (isset($_GET['session_id'])) {
    $session->id = $_GET['session_id'];
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Session id not provided'));
    die();
}
$session->next_piece_id = $data->next_piece_id;

if ($session->next_piece_id === null || $session->next_piece_id === "") {
    http_response_code(400);
    echo json_encode(array('message' => 'Next piece id not set'));
    die();
}

if ($session->is_in_session($data->id, $session)
  && $session->is_piece_available()
  && !($session->is_turn($data->id))
  && $session->set_next()) {
    echo json_encode(array('message' => 'Next piece is set!'));
} else {
    http_response_code(403);
    echo json_encode(array('message' => 'Unable to set next piece'));
    die();
}
