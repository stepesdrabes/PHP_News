<?php

include_once 'database/UserRepository.php';
include_once 'services/SuccessMessageService.php';
include_once 'services/AuthService.php';
AuthService::init();

if (empty($_GET['id'])) {
    SuccessMessageService::create_popup_message(
        'fi-br-cross',
        'Prázdné ID',
        'Nebylo zadáno žádné ID!',
        '#fa3f4c',
        '#6e1421'
    );

    header('location: index.php');
    exit;
}

$username = $_GET['id'];
$author = UserRepository::get_user_by_id($_GET['id']);

if (isset($_GET['from_user_info'])) {
    $_SESSION['from_user_info'] = true;
    header('location: author.php?id=' . $_GET['id']);
    exit;
}

if ($author == null) {
    SuccessMessageService::create_popup_message(
        'fi-br-cross',
        'Neplatný uživatel',
        'Uživatel "' . $username . '" nebyl nalezen!',
        '#fa3f4c',
        '#6e1421'
    );

    header('location: index.php');
    exit;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="scripts/animations.js"></script>
    <link rel="stylesheet" href="styles/style.css">

    <title>Hlavní stránka | Zprávičky</title>
</head>
<body>

<?php include 'success_message.php'; ?>

<header>
    <?php include 'nav_bar.php'; ?>
</header>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <h1><?= $author['firstname'] . ' ' . $author['surname'] ?></h1>
</main>

</body>
</html>