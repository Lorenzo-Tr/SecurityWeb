<?php
session_start();
if (!empty($_SESSION['username'])) {
	header("Location: profile.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form action="../php/register.php" method="post">
            <h1 class="title">Create Account</h1>
			<?php if(isset($_SESSION['register_error'])): ?>
                <p class="infos display" id="infos_login"><?= $_SESSION['register_error'] ?? "" ?></p>
			<?php endif; ?>
            <p class="infos display" id="infos_register"></p>
            <input name="register_firstname" type="text" placeholder="First name"/>
            <input name="register_lastname" type="text" placeholder="Last name"/>
            <input name="register_username" type="text" placeholder="Username"/>
            <input name="register_password" type="password" placeholder="Password"/>
            <button type="submit" id="register">Register</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form action="../php/login.php" method="post">
            <h1 class="title">Sign in</h1>
			<?php if(isset($_SESSION['login_error']) ): ?>
                <p class="infos display" id="infos_login"><?= $_SESSION['login_error'] ?? "" ?></p>
            <?php unset($_SESSION['login_error']) ?>
			<?php endif; ?>
            <?php if(isset($_SESSION['account_block'])): ?>
                <p class="infos blocked display" id="infos_login">Your account have been blocked - unlock in <?= $_SESSION['account_block'] ?? "" ?></p>
            <?php endif; ?>
            <input name="login_username" type="text" placeholder="Username"/>
            <input name="login_password" type="password" placeholder="Password"/>
            <a href="#">Forgot your password?</a>
            <button type="submit" id="login">Login</button>
        </form>
    </div>
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>Vous possédez déjà un compte connectez-vous.</p>
                <button class="ghost" id="signIn">Login</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Entrer vos informations et commencer à utiliser nos services</p>
                <button class="ghost" id="signUp">Register</button>
            </div>
        </div>
    </div>
</div>
<script>
    // changement de class pour faire l'animation entre login et register
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    <?php if(isset($_SESSION['register_error'])): ?>
    container.classList.add("right-panel-active");
    <?php unset($_SESSION['register_error']) ?>
    <?php endif; ?>

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
</script>
</body>
</html>
