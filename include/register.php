<?php
require_once "../database/Database.php";
require_once "const.php";

$db = new Database(DB_NAME, USER, PASSWORD);
$db_encrypt = new Database(DB_NAME_ENCRYPT, USER_ENCRYPT, PASSWORD_ENCRYPT);

$user_details = [
	'mail' => $_POST['register_mail'],
	'firstname' => $_POST['register_firstname'],
	'lastname' => $_POST['register_lastname'],
];

$user_login = [
	'username' => $_POST['register_username'],
	'password' => $_POST['register_password'],
];

// hash du password and username
$user_login['password'] = password_hash($user_login['password'], PASSWORD_BCRYPT, ['cost' => 12,]);
$user_login['username'] = hash('sha512', $user_login['username']);

$db->change_encrypt_key();
$user_details = $db->encrypt_data($user_details);

$db->run('INSERT INTO `user` (username, password) VALUES (?, ?);', [
	$user_login['username'],
	$user_login['password']
]);

$user_id = $db->pdo->lastInsertId();

$db->run('INSERT INTO `user_details` (user_id, lastname, firstname,email) VALUES (?,?,?,?);', [
	$user_id,
	$user_details['lastname'],
	$user_details['firstname'],
	$user_details['mail']
]);

$db_encrypt->run('INSERT INTO `encrypt` (`user_id`, `key`, `iv`) VALUES (?,?,?)', [
	$user_id,
	bin2hex($db->key),
	bin2hex($db->iv)
]);
