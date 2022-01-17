<?php

include_once 'database/UserRepository.php';
include_once 'services/AuthService.php';
AuthService::init();

$authors = UserRepository::get_users();

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

    <title>Autoři | Zprávičky</title>
</head>
<body>

<?php include 'success_message.php'; ?>

<header>
    <?php include 'nav_bar.php' ?>
</header>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <h1>Autoři</h1>

    <?php if (count($authors) > 0): ?>
        <div id="authors">
            <?php foreach ($authors as $author):
                $full_name = $author['firstname'] . ' ' . $author['surname'];
                $timestamp = date("d.m.Y", strtotime($author['timestamp']));
                ?>
                <a href="author.php?id=<?= $author['id'] ?>">
                    <div class="author">
                        <img class="profile-picture" alt="<?= $full_name ?>" src="<?= $author['file_name'] ?>">

                        <div class="names" style="display: flex; flex-direction: column; align-items: center">
                            <h3><?= $full_name ?></h3>
                            <p style="color: #a8a8a8; font-size: 0.8rem;"><?= $author['username'] ?></p>
                        </div>

                        <p>Účet založen: <?= $timestamp ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 32px; pointer-events: none; user-select: none">
            <img style="width: 10%; min-width: 200px;" src="images/no_data.svg" alt="No data">
            <h3>Neexistuje žádný autor</h3>
        </div>
    <?php endif; ?>
</main>

</body>
</html>