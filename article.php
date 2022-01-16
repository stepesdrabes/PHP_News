<?php

include_once 'database/ArticleRepository.php';
include_once 'database/CategoryRepository.php';
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

$article_id = $_GET['id'];
$article = ArticleRepository::get_article($article_id);

if ($article == null) {
    SuccessMessageService::create_popup_message(
        'fi-br-cross',
        'Neplatný článek',
        'Článek s ID "' . $article_id . '" nebyl nalezen!',
        '#fa3f4c',
        '#6e1421'
    );

    header('location: index.php');
    exit;
}

$category = CategoryRepository::get_category($article['category_id']);
$author = UserRepository::get_user_by_id($article['user_id']);
$timestamp = date("d.m.Y", strtotime($article['timestamp']));

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

    <title>Článek | Zprávičky</title>
</head>
<body>

<?php include 'success_message.php'; ?>

<header>
    <?php include 'nav_bar.php'; ?>
</header>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <div style="display: flex; align-items: center; gap: 16px">
        <a href="index.php">
            <h1>Články</h1>
        </a>

        <i style="color: #a8a8a8; font-size: 16px" class="fi fi-br-arrow-small-right"></i>
        <a href="index.php?category=<?= $category['id'] ?>">
            <p style="color: #a8a8a8; font-weight: 500"> <?= $category['name'] ?></p>
        </a>
        <i style="color: #a8a8a8; font-size: 16px" class="fi fi-br-arrow-small-right"></i>
        <p style="color: #a8a8a8; font-weight: 500"> <?= $article['title'] ?></p>
    </div>

    <div id="article-box">
        <h1><?= $article['title'] ?></h1>

        <div class="line"></div>

        <div id="article-content">
            <?= $article['content'] ?>
        </div>

        <div class="line"></div>

        <div style="display: flex; align-items: center; gap: 8px">
            <p>Vytvořeno <?= $timestamp ?> autorem</p>

            <a href="author.php?id=<?= $author['id'] ?>">
                <h4><?= $author['firstname'] . ' ' . $author['surname'] ?></h4>
            </a>
        </div>
    </div>
</main>

</body>
</html>