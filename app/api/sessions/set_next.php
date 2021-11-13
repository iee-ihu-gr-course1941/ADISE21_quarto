<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
	Content-Type, 
	Access-Control-Allow-Methods,
	Authorization, 
	X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';
include_once '../../models/Piece.php';

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
$session->next_piece_id = $data->next_piece_id;
$session->id            = $data->session_id;

if ($session->next_piece_id === null || $session->next_piece_id === "") {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to set next piece'));
    die();
}

try {
    if (Session::is_playing($data->id, $session)
      && $piece->is_available($data->session_id)
      && !($session->is_turn($data->id))
      && $session->set_next()) {
        echo json_encode(array('message' => 'Next piece is set!'));
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Unable to set next piece'));
        die();
    }
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to set next piece'));
    die();
}
