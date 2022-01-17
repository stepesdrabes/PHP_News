<?php

include_once 'App.php';
App::init();

include_once 'database/ArticleRepository.php';
include_once 'database/CategoryRepository.php';
include_once 'services/StatusMessageService.php';

if (empty($_GET['id'])) {
    StatusMessageService::create_error_popup('V požadavku nebylo zadáno žádné ID!');
    App::redirect('index.php');
}

$article_id = $_GET['id'];
$article = ArticleRepository::get_article($article_id);

if ($article == null) {
    StatusMessageService::create_error_popup('Článek s ID "' . $article_id . '" nebyl nalezen!');
    App::redirect('index.php');
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
    <link rel="stylesheet" href="styles/style.css">

    <title>Článek | Zprávičky</title>
</head>
<body>

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

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