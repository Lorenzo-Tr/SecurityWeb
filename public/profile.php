<?php
session_start();
if (empty($_SESSION['username'])) {
    header("Location: /");
    exit();
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" id="container">
    <img src="https://avatars.dicebear.com/api/big-smile/<?= substr($_SESSION['username'], 0, 16); ?>.svg?mouth[]=openedSmile&mouth[]=teethSmile&hair[]=wavyBob&hair[]=shortHair&hair[]=curlyBob&hair[]=straightHair&hair[]=curlyShortHair&eyes=cheery&eyes=normal&eyes=starstruck&eyes=winking&hairColor=variant01&&hairColor=variant02&hairColor=variant03&hairColor=variant07"
         alt="user avatar">
</div>
</body>
</html>
