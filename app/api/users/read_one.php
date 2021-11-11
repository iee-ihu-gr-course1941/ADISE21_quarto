<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$user->id = isset($_GET['id']) ? $_GET['id'] : die();

try{
	$user->read_one();

	if($user->id === null || $user->username === null){
		http_response_code(404);
		echo json_encode(array('message' => 'User not found'));
		die();
	}

	$user = array(
		'id'	   => $user->id,
		'username' => $user->username,
	);

	echo json_encode($user);
}catch(PDOException $e){
	http_response_code(400);
	echo json_encode(array('message' => 'Unable to read one'));
	die();
}
