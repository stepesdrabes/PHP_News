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
                        <img class="profile-picture" alt="<?= $full_name ?>"
                             src="https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/271deea8-e28c-41a3-aaf5-2913f5f48be6/de7834s-6515bd40-8b2c-4dc6-a843-5ac1a95a8b55.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcLzI3MWRlZWE4LWUyOGMtNDFhMy1hYWY1LTI5MTNmNWY0OGJlNlwvZGU3ODM0cy02NTE1YmQ0MC04YjJjLTRkYzYtYTg0My01YWMxYTk1YThiNTUuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.BopkDn1ptIwbmcKHdAOlYHyAOOACXW0Zfgbs0-6BY-E">

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