
<?php

error_reporting(E_ALL ^ E_WARNING);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
	Content-Type, 
	Access-Control-Allow-Methods,
	Authorization, 
	X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Placement.php';
include_once '../../models/User.php';
include_once '../../models/Session.php';

$database = new Database();
$db 	  = $database->connect();

$placement = new Placement($db);
$session   = new Session($db);

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
    $session->id = $_GET['session_id'];
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Session id not provided'));
    die();
}

$session->read_one();

$placement->session_id = $session->id;
$placement->piece_id   = intval($session->next_piece_id);
$placement->player_id  = intval($user->id);
$placement->pos_x      = $data->pos_x;
$placement->pos_y      = $data->pos_y;

if ($session->is_in_session($user->id)
  && $session->is_turn($user->id)
  && !($session->is_next_null())
  && $placement->is_valid()
  && $placement->create()) {
    if ($session->has_won()) {
        $session->set_winner($session->turn);
        $session->end_game();
        echo json_encode(array('message'=> ''. $session->turn ." has won!!"));
    } elseif ($session->is_draw()) {
        $session->end_game();
        echo json_encode(array('message'=>"Draw!"));
    } else {
        $session->set_next_null();
        $session->set_turn();
        echo json_encode(array('message' => 'Placement created'));
    }
} else {
    http_response_code(403);
    echo json_encode(array('message' => 'Unable to create placement'));
    die();
}
