<?php

include_once 'services/AuthService.php';
AuthService::init();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="scripts/animations.js"></script>
    <link rel="stylesheet" href="styles/style.css">

    <title>Administrace | Zprávičky</title>
</head>
<body>

<?php include 'success_message.php'; ?>

<header>
    <?php include 'nav_bar.php' ?>
</header>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <h1>Administrace</h1>

    <?php if (!AuthService::is_logged_in()): ?>
        <p>Tato sekce je k dispozici pouze po <a style="color: var(--highlight-color); text-decoration: underline" href="login.php">přihlášení!</a></p>
    <?php else: ?>

    <?php endif; ?>
</main>

</body>
</html>