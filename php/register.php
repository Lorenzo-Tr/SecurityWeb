<?php
require_once "../database/Database.php";
require_once "const.php";

$db = new Database(DB_NAME, USER, PASSWORD);
$db_encrypt = new Database(DB_NAME_ENCRYPT, USER_ENCRYPT, PASSWORD_ENCRYPT);
$user_details = [
	'firstname' => htmlentities($_POST['register_firstname']),
	'lastname' => htmlentities($_POST['register_lastname']),
];

$user_login = [
	'username' => htmlentities($_POST['register_username']),
	'password' => htmlentities($_POST['register_password']),
];

if(!preg_match('/^(?=\S*[a-z])(?=\S*[A-Z])(?=\S*\d)(?=\S*[^\w\s])\S{10,}$/', $user_login['password'])){
	$_SESSION['register_error'] = "Not secure password hint : 10 character, 1 minuscule, 1 majuscule, 1 digit, 1 special character";
//	var_dump($_SESSION['register_error']);
//	die();
	header('Location: ../public/connection.php');
	exit();
}

// hash du password and username
$user_login['password'] = password_hash($user_login['password'], PASSWORD_BCRYPT, ['cost' => 12,]);
$username = $user_login['username'];
// TODO: ADD SALT with current date
$user_login['username'] = hash('sha512', $user_login['username']);

$db->change_encrypt_key();
$user_details = $db->encrypt_data($user_details);

try {
    $q1 = $db->run('INSERT INTO `user` (username, password) VALUES (?, ?);', [
        $user_login['username'],
        $user_login['password']
    ]);

    $user_id = $db->pdo->lastInsertId();

    $q2 = $db->run('INSERT INTO `user_details` (user_id, lastname, firstname) VALUES (?,?,?);', [
        $user_id,
		$user_details['lastname'],
		$user_details['firstname']
    ]);

    $q3 = $db_encrypt->run('INSERT INTO `encrypt` (`user_id`, `key`, `iv`) VALUES (?,?,?);', [
        $user_id,
        bin2hex($db->key),
        bin2hex($db->iv)
    ]);

    unset($db, $db_encrypt);
    session_start();
    if($q1 && $q2 && $q3){
        $_SESSION['username'] = $username;
		$_SESSION['user_id'] = $user_id;
        header('Location: ../public/profile.php');
    } else {
        $_SESSION['register_error'] = "sql_error";
        header('Location: ../public/connection.php');
    }
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


