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
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents('php://input'));

if($data->username === "" || $data->username === null){
	http_response_code(400);
	echo json_encode(array('message' => 'Username cant be empty'));
	die();
}else{
	$user->username = $data->username;
}

if($data->password === "" || $data->password === null){
	http_response_code(400);
	echo json_encode(array('message' => 'Password cant be empty'));
	die();
}else{
	$user->password = $data->password;
}

if ($user->sign_up()) {
	echo json_encode(
		array('message' => 'User created')
	);
} else {
	echo json_encode(
		array('message' => 'Username already exists')
	);
}
