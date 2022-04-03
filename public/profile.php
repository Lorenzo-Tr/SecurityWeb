<?php
session_start();
if (empty($_SESSION['username'])) {
    header("Location: /");
    exit();
}else{
    require "../php/decrypt.php";
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
<body class="no_center">

    <form enctype="multipart/form-data" action="../php/update.php" method="post" style="height: auto; padding: 20px 50px;">
        <a href="../php/logout.php">logout</a>
<!--        <img class="avatar" src="https://avatars.dicebear.com/api/big-smile/--><?//= $_SESSION['username'] ?><!--.svg?mouth[]=openedSmile&mouth[]=teethSmile&hair[]=wavyBob&hair[]=shortHair&hair[]=curlyBob&hair[]=straightHair&hair[]=curlyShortHair&eyes=cheery&eyes=normal&eyes=starstruck&eyes=winking&hairColor=variant01&&hairColor=variant02&hairColor=variant03&hairColor=variant07"-->
<!--             alt="user avatar">-->
        <h2>Hi, <?= $_SESSION['username']?></h2>
        <h1 class="title">Update Account</h1>
		<?php if(isset($_SESSION['update_error'])): ?>
            <p class="infos display" id="infos_update"><?= $_SESSION['update_error'] ?? "" ?></p>
			<?php unset($_SESSION['update_error']) ?>
		<?php endif; ?>
        <p class="infos display" id="infos_register"></p>
        <h2>Personal data</h2>
        <input name="update_firstname" type="text" placeholder="First name" value="<?= $user->firstname?>"/>
        <input name="update_lastname" type="text" placeholder="Last name" value="<?= $user->lastname?>"/>
        <input name="update_mail" type="email" placeholder="Email" value="<?= $user->email?>"/>
        <input name="update_address" type="text" placeholder="Address" value="<?= $user->address?>"/>
        <h2>Login</h2>
        <input name="update_username" type="text" placeholder="Username" value="<?= $_SESSION['username']?>"/>
        <h2>Document</h2>
        <input name="update_ID_Card" type="file" placeholder="Identity card" accept="image/*,.pdf"/>
        <button type="submit" id="register">Update</button>
    </form>

</body>
</html>
