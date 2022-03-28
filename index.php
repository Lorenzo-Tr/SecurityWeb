<?php
session_start();
var_dump($_SESSION['username']);
if (!isset($_SESSION['username'])) {
	header('Location: public/connection.php');
} else {
	header('Location: public/profile.php');
}

exit();