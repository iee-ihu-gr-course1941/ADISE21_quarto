
<?php

error_reporting(E_ERROR);
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

$method = $_SERVER['REQUEST_METHOD'];
if (!($method=='POST')) {
    http_response_code(405);
    die();
}

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

$session->player1_id = $data->id;

if (!($session->is_playing($data->id)) && $session->create()) {
    echo json_encode(array('message' => 'Session created'));
} else {
    http_response_code(403);
    echo json_encode(array('message' => 'Unable to create session'));
    die();
}
