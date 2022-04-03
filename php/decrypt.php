<?php
require_once "../database/Database.php";
require_once "const.php";

$db = new Database(DB_NAME, USER, PASSWORD);
$db_encrypt = new Database(DB_NAME_ENCRYPT, USER_ENCRYPT, PASSWORD_ENCRYPT);

$encrypt = $db_encrypt->get('encrypt', [], ['user_id' => $_SESSION['user_id']]);
$db->change_encrypt_key(hex2bin($encrypt->key), hex2bin($encrypt->iv));

$user = $db->get('user_details', [], ['user_id' => $_SESSION['user_id']]);
$user = (object)$db->decrypt_data((array)$user);