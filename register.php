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

    if (!isset($_POST['username'], $_POST['firstname'], $_POST['surname'], $_POST['password'], $_POST['confirm-password'])) {
        $error_message = 'Vyplňte všechna pole';
        return;
    }

    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $surname = $_POST['surname'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if (empty($username) || empty($firstname) || empty($surname) || empty($password) || empty($confirm_password)) {
        $error_message = 'Vyplňte všechna pole!';
        return;
    }

    if (strlen($username) < 4 || strlen($username) > 32) {
        $error_message = "Uživatelské jméno musí být delší než 4 znaky a kratší než 32 znaků";
        return;
    }

    if (!ctype_alnum($username)) {
        $error_message = "Uživatelské jméno smí obsahovat pouze malé znaky a-z a 0-9";
        return;
    }

    if (UserRepository::user_exists($username)) {
        $error_message = "Uživatel s tímto jménem již existuje";
        return;
    }

    if ($password !== $confirm_password) {
        $error_message = "Hesla se neshodují";
        return;
    }

    $result = UserRepository::create_user($username, $firstname, $surname, $password);

    if (!$result) {
        $error_message = "Nastala chyba při registraci";
        return;
    }

    $user = AuthService::login($username);

    StatusMessageService::create_success_popup('Registrace byla úspěšná! Vítej, ' . $user['username']);
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

    <title>Registrace | Zprávičky</title>
</head>
<body>

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <div class="auth-container">
        <form method="post" autocomplete="off">
            <h2>Registrace</h2>
            <input class="input-field" type="text" minlength="4" maxlength="32" name="username"
                   placeholder="Uživatelské jméno">

            <div class="fulname-row" style="width: 100%; display: flex; gap: 16px">
                <input class="input-field" type="text" name="firstname" placeholder="Jméno">
                <input class="input-field" type="text" name="surname" placeholder="Příjmení">
            </div>

            <input class="input-field" type="password" name="password" minlength="6" placeholder="Heslo">
            <input class="input-field" type="password" name="confirm-password" minlength="6"
                   placeholder="Potvrzení hesla">

            <?php if ($error_message != null): ?>
                <p class="error-message"><?= $error_message ?></p>
            <?php endif; ?>

            <button style="background-color: var(--highlight-color); color: #FFFFFF">Zaregistrovat se</button>

            <p style="font-size: 0.8rem; width: 100%; text-align: center">
                Již máš účet
                <a style="color: var(--highlight-color); text-decoration: underline; margin: 16px 0" href="login.php">
                    Přihlaš se!
                </a>
            </p>
        </form>
    </div>
</main>

</body>

</html>