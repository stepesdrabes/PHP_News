<?php

include_once 'App.php';
App::init();

include_once 'database/UserRepository.php';
include_once 'services/StatusMessageService.php';
include_once 'services/AuthService.php';

$error_message = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validate_values();
}

function validate_values()
{
    global $error_message;

    if (!isset($_POST['username'], $_POST['password'])) {
        $error_message = 'Vyplňte všechna pole!';
        return;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!AuthService::check_password($username, $password)) {
        $error_message = 'Neplatné uživatelské jméno nebo heslo';
        return;
    }

    $user = AuthService::login($username);

    StatusMessageService::create_success_popup('Přihlášení bylo úspěšné. Vítej zpět, ' . $user['username'] . '!');
    App::redirect('index.php');
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles/style.css">

    <title>Přihlášení | Zprávičky</title>
</head>
<body>

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <div class="auth-container">
        <form method="post" autocomplete="off">
            <h2>Přihlášení</h2>
            <input class="input-field" type="text" name="username" placeholder="Uživatelské jméno">
            <input class="input-field" type="password" name="password" placeholder="Heslo">

            <?php if ($error_message != null): ?>
                <p class="error-message"><?= $error_message ?></p>
            <?php endif; ?>

            <button style="background-color: var(--highlight-color); color: #FFFFFF">Přihlásit se</button>

            <p style="font-size: 0.8rem; width: 100%; text-align: center">
                Ještě nemáš účet?
                <a style="color: var(--highlight-color); text-decoration: underline; margin: 16px 0"
                   href="register.php">Zaregistruj se!</a>
            </p>
        </form>
    </div>
</main>

</body>
</html>