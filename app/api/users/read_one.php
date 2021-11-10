<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$user->id = isset($_GET['id']) ? $_GET['id'] : die();

$user->read_one();

// Create array
$post_arr = array(
	'id' => $user->id,
	'username' => $user->username,
);

echo json_encode($post_arr);
