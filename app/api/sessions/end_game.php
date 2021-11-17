
<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
	Content-Type, 
	Access-Control-Allow-Methods,
	Authorization, 
	X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';

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

$session     = new Session($db);
$session->id = $data->session_id;

if ($session->id === "" || $session->id === null) {
    http_response_code(400);
    echo json_encode(array('message' => 'id cant be empty'));
    die();
}



try {
    if (Session::is_playing($data->id, $session) && $session->end_game()) {
        echo json_encode(array('message' => 'Game ended'));
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Unable to end game'));
        die();
    }
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to end game: '.$e));
    die();
}
