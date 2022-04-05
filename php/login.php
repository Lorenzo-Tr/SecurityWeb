<?php
session_start();
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
try {
    $q = $db->pdo->prepare("SELECT * FROM ip_address WHERE user_id=? OR ip=? ORDER BY id DESC");
    $q->execute([$user->id ?? '' , $_SERVER['REMOTE_ADDR']]);
    $ip = $q->fetch(PDO::FETCH_OBJ);
} catch (PDOException $e){

}

if(($ip->try ?? 0) >= 5){
    $currentSleepMs = (int)(15 * pow($ip->try - 4, 2));
    $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $future_date = new DateTime($ip->created_at, new DateTimeZone('Europe/Paris'));
    $future_date->add(new DateInterval('PT' . $currentSleepMs . 'M'));

    if ($future_date > $now){
        $interval = $future_date->diff($now);
        echo $interval->format("%H:%i:%s");
        $_SESSION['account_block'] = $interval->format("%H:%I:%S");
        header('Location: ../public/connection.php');
        exit();
    }
}

if ($user) {
    // Account exist
    $encrypt = $db_encrypt->get('encrypt', [], ['user_id' => $user->id]);
    $db->change_encrypt_key(hex2bin($encrypt->key), hex2bin($encrypt->iv));
//    $ip = $db->get('ip_address', [], ['ip' => $db->encrypt_data($_SERVER['REMOTE_ADDR'])]);
    $ip_encrypt = $db->encrypt_data($_SERVER['REMOTE_ADDR']);
    $useragent_encrypt = $db->encrypt_data($_SERVER['HTTP_USER_AGENT']);

    if (password_verify($user_login['password'], $user->password)) {
        $db->create('ip_address', ['user_id' => $user->id, 'ip' => $ip_encrypt, 'user_agent' => $useragent_encrypt, 'try' => '0']);
        isset($_SESSION['account_block']) ?? $_SESSION['account_block'] = '';
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user->id;
        header('Location: ../public/profile.php');
    } else {
        $db->create('ip_address', [`user_id` => $user->id, `ip` => $ip_encrypt, `user_agent` => $useragent_encrypt, `try` => $ip->try + 1]);
        $_SESSION['login_error'] = "wrong_password";
        header('Location: ../public/connection.php');
    }
} else {
//    $ip = $db->get('ip_address', [], ['ip' => $_SERVER['REMOTE_ADDR']], ['created_at' => 'DESC']);
    if($ip){
        $db->create('ip_address', ['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'try' => $ip->try + 1]);
    }else{
        $db->create('ip_address', ['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'try' => 0]);
    }
    $_SESSION['login_error'] = "no_account_found";
    header('Location: ../public/connection.php');
}


unset($db, $db_encrypt);
exit();


