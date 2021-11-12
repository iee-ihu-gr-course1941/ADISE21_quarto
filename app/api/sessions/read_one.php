
<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/Session.php';
include_once '../../models/User.php';

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

$session->id = isset($_GET['id']) ? $_GET['id'] : die();

try {
    if ($session->read_one()) {
        if ($session->id === null) {
            http_response_code(404);
            echo json_encode(array('message' => 'User not found'));
            die();
        }

        $session = array(
          'id'	       => $session->id,
          'player1_id' => $session->player1_id,
          'player2_id' => $session->player2_id,
          'turn'       => $session->turn,
          'winner'     => $session->winner,
        );

        echo json_encode($session);
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Unable to read one'));
        die();
    }
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to read one'));
    die();
}
