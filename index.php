<?php

include_once 'App.php';
App::init();

include_once 'database/CategoryRepository.php';
include_once 'database/ArticleRepository.php';
include_once 'services/StatusMessageService.php';

$last_params = $_SESSION['current_params'] ?? [];
$current_params = $_GET;

$_SESSION['last_params'] = $last_params;
$_SESSION['current_params'] = $current_params;

$category = null;

if (isset($_GET['category'])) {
    $category = CategoryRepository::get_category($_GET['category']);

    if ($category == null) {
        StatusMessageService::create_error_popup('Kategorie s ID ' . $_GET['category'] . ' nebyla nalezena!');
        App::refresh();
    }
}

if ($last_params != $current_params) {
    unset($_SESSION['current_location']);
}

$articles = $category == null ? ArticleRepository::get_all_articles() : ArticleRepository::get_category_articles($category['id']);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles/style.css">

    <title>Hlavní stránka | Zprávičky</title>
</head>
<body class="<?= App::get_color_scheme() ?>">

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <div style="display: flex; align-items: center; gap: 16px">
        <a href="index.php">
            <h1>Články</h1>
        </a>

        <?php if ($category != null): ?>
            <i style="color: #a8a8a8; font-size: 16px" class="fi fi-br-arrow-small-right"></i>
            <p style="color: #a8a8a8; font-weight: 500"> <?= $category['name'] ?></p>
        <?php endif; ?>
    </div>

    <?php if (count($articles) == 0): ?>
        <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 32px; pointer-events: none; user-select: none">
            <div style="width: 10%; min-width: 200px;">
                <?= App::accent_color_svg('images/no_data.svg', [ '#000000' => App::get_settings_value('accentColor')]) ?>
            </div>

            <h3><?= $category == null ? 'Nic zde není!' : "V této kategorii se nenachází žádný článek" ?></h3>
        </div>
    <?php else: ?>
        <div id="articles">
            <div class="line"></div>

            <?php foreach ($articles as $article):
                $timestamp = date("d.m.Y H:i:s", strtotime($article['timestamp']));
                $author = UserRepository::get_user_by_id($article['user_id']);
                $full_name = $author['firstname'] . ' ' . $author['surname'];
                $article_category = CategoryRepository::get_category($article['category_id']);
                ?>
                <div class="article-row">
                    <img src="<?= $article['image_path'] ?>" alt="image">

                    <div class="article">
                        <div class="title-row">
                            <a style="flex: 1" href="article.php?id=<?= $article['id'] ?>">
                                <h1><?= $article['title'] ?></h1>
                            </a>

                            <a href="index.php?category=<?= $article_category['id'] ?>">
                                <div class="category-chip">
                                    <p><?= $article_category['name'] ?></p>
                                </div>
                            </a>
                        </div>

                        <p style="color: var(--label); width: 75%"><?= substr(strip_tags($article['content']), 0, 256) . ' ...' ?></p>

                        <div style="display: flex; align-items: center; gap: 8px">
                            <p>Vytvořeno <?= $timestamp ?> autorem</p>

                            <img class="profile-picture" src="<?= $author['file_name'] ?>" alt="">

                            <a href="author.php?id=<?= $author['id'] ?>">
                                <h4><?= $author['firstname'] . ' ' . $author['surname'] ?></h4>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="line"></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>