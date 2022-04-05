<?php
require_once "../database/Database.php";
require_once "const.php";
session_start();

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
    var_dump($upload_file);
    if (move_uploaded_file($_FILES["update_ID_Card"]["tmp_name"], $upload_file)) {
        echo "The file ". basename( $_FILES["upload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    die();
	if (move_uploaded_file($_FILES['update_ID_Card']['tmp_name'], $upload_file)) {
		$path = UPLOAD_DIR . "id_card/" . $_FILES['update_ID_Card']['name'];
		var_dump($path);

		$size = $_FILES['update_ID_Card']['size'];
		$type = $_FILES['update_ID_Card']['type'];
		$name = $_FILES['update_ID_Card']['name'];
	}
    var_dump($_FILES);

	unset($db, $db_encrypt);
	exit();
	// do other things if successfully inserted
} catch (PDOException $e) {
	unset($db, $db_encrypt);
    $_SESSION['update_error'] = "pdo_error";
	header('Location: ../public/connection.php');
	exit();
}


