<?php
require_once "../database/Database.php";
require_once "const.php";

$db = new Database(DB_NAME, USER, PASSWORD);
$db_encrypt = new Database(DB_NAME_ENCRYPT, USER_ENCRYPT, PASSWORD_ENCRYPT);
$user_details = [
	'mail' => $_POST['update_mail'],
	'firstname' => $_POST['update_firstname'],
	'lastname' => $_POST['update_lastname'],
	'address' => $_POST['update_address']
];

$user_login['username'] = $_POST['update_username'];
$user_file = $_FILES['update_ID_Card'];

// hash du username
$user_login = hash('sha512', $user_login['username']);

$encrypt = $db_encrypt->get('encrypt', [], ['user_id' => $_SESSION['user_id']]);
$db->change_encrypt_key(hex2bin($encrypt->key), hex2bin($encrypt->iv));
$user_details = $db->encrypt_data($user_details);

try {
//	$db->update('user_details', $user_details, ['user_id' => $_SESSION['user_id']]);
//	$db->update('user', $user_login, ['user_id' => $_SESSION['user_id']]);

	$upload_file = UPLOAD_DIR . "id_card/" . basename($_FILES['update_ID_Card']['name']);
	$extension = ['jpg', 'pdf', 'jpeg', 'bmp'];
	$size = 5*1024*1024;
	if (move_uploaded_file($_FILES['update_ID_Card']['tmp_name'], $upload_file)) {
		$path = UPLOAD_DIR . "id_card/" . $_FILES['update_ID_Card']['name'];
		var_dump($path);
		die;
		$size = $_FILES['update_ID_Card']['size'];
		$type = $_FILES['update_ID_Card']['type'];
		$name = $_FILES['update_ID_Card']['name'];
	}

	unset($db, $db_encrypt);
	session_start();

		$_SESSION['username'] = $username;
		$_SESSION['user_id'] = $user_id;
		header('Location: ../public/profile.php');

		$_SESSION['register_error'] = "sql_error";
		header('Location: ../public/connection.php');

	exit();
	// do other things if successfully inserted
} catch (PDOException $e) {
	unset($db, $db_encrypt);
	session_start();
	if ($e->errorInfo[1] == 1062) {
		$_SESSION['register_error'] = "user_taken";
		// duplicate entry, do something else
	} else {
		$_SESSION['register_error'] = "sql_error";
		// an error other than duplicate entry occurred
	}
	header('Location: ../public/connection.php');
	exit();
}


