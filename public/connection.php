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
        <form action="../include/register.php" method="post">
            <h1 class="title">Create Account</h1>
            <input name="register_firstname" type="text" placeholder="First name"/>
            <input name="register_lastname" type="text" placeholder="Last name"/>
            <input name="register_mail" type="email" placeholder="Email"/>
            <input name="register_username" type="username" placeholder="Username"/>
            <input name="register_password" type="password" placeholder="Password"/>
            <p class="infos" id="infosregister"></p>
            <button id="btnregister">Register</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form action="../include/login.php" method="post">
            <h1 class="title">Sign in</h1>
            <input id="register_username" type="Username" placeholder="Username"/>
            <input id="register_pass" type="password" placeholder="Password"/>
            <p class="infos" id="infoslogin"></p>
            <a href="#">Forgot your password?</a>
            <button id="btnlogin">Login</button>
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
    // fonction ajax
    function post(url, v, f) {
        let http = new XMLHttpRequest();
        http.addEventListener('load', f);
        http.open("POST", url, true);
        http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        http.send(v);
    }


    // changement de class pour faire l'animation entre login et register
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
</script>
</body>
</html>
