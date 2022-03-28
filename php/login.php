<?php
require_once "../database/Database.php";
require_once "const.php";

$db = new Database(DB_NAME, USER, PASSWORD);
$db_encrypt = new Database(DB_NAME_ENCRYPT, USER_ENCRYPT, PASSWORD_ENCRYPT);

$user_login = [
    'username' => $_POST['login_username'],
    'password' => $_POST['login_password'],
];

// hash du password and username
$username = $user_login['username'];
$user_login['username'] = hash('sha512', $user_login['username']);

$user = $db->get('user', [], ['username' => $user_login['username']]);


session_start();
if ($user) {
    // Account exist
    $encrypt = $db_encrypt->get('encrypt', [], ['user_id' => $user->id]);
    $db->change_encrypt_key(hex2bin($encrypt->key), hex2bin($encrypt->iv));
    $ip = $db->get('ip_address', [], ['ip' => $db->encrypt_data($_SERVER['REMOTE_ADDR'])]);

    if (password_verify($user_login['password'], $user->password)) {
        $_SESSION['username'] = $username;
        header('Location: ../public/profile.php');
    } else {
        $db->update('ip_address', ['ip' => $db->encrypt_data($_SERVER['REMOTE_ADDR']), 'user_agent' => $db->encrypt_data($_SERVER['HTTP_USER_AGENT'])]);
        var_dump($db->run("UPDATE `ip_address` SET `try`=`try`+1 WHERE ip=?",[$db->encrypt_data($_SERVER['REMOTE_ADDR'])]));
        $_SESSION['login_error'] = "wrong_password";
        header('Location: ../public/connection.php');
    }
} else {
    $db->replace('ip_address', ['ip' => $db->encrypt_data($_SERVER['REMOTE_ADDR']), 'user_agent' => $db->encrypt_data($_SERVER['HTTP_USER_AGENT'])]);
    $db->run("UPDATE `ip_address` SET `try`=`try`+1 WHERE ip=?",[$db->encrypt_data($_SERVER['REMOTE_ADDR'])]);
    $_SESSION['login_error'] = "no_account_found";
    header('Location: ../public/connection.php');
}
unset($db, $db_encrypt);
exit();


