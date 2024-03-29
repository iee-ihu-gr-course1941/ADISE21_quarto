
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

$session = new Session($db);

$data = json_decode(file_get_contents('php://input'));

$user               = new User($db);
$user->id           = $data->id;
$user->access_token = $data->access_token;

if (!$user->validate_token()) {
    http_response_code(401);
    echo json_encode(array('message' => 'Invalid token'));
    die();
}
if (isset($_GET['session_id'])) {
    $session->id = $_GET['session_id'];
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Id not provided'));
    die();
}

if ($session->read_one()) {
    $session = array(
      'id'	    => $session->id,
      'player1_id'    => $session->player1_id,
      'player2_id'    => $session->player2_id,
      'turn'          => $session->turn,
      'winner'        => $session->winner,
      'next_piece_id' => $session->next_piece_id,
    );

    echo json_encode($session);
} else {
    http_response_code(404);
    echo json_encode(array('message' => 'Unable to read one'));
    die();
}
