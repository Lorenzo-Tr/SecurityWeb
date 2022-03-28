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
$username = $user_login['username'];
$user_login['username'] = hash('sha512', $user_login['username']);

$db->change_encrypt_key();
$user_details = $db->encrypt_data($user_details);

try {
    $q1 = $db->run('INSERT INTO `user` (username, password) VALUES (?, ?);', [
        $user_login['username'],
        $user_login['password']
    ]);

    $user_id = $db->pdo->lastInsertId();

    $q2 = $db->run('INSERT INTO `user_details` (user_id, lastname, firstname,email) VALUES (?,?,?,?);', [
        $user_id,
        $user_details['lastname'],
        $user_details['firstname'],
        $user_details['mail']
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


