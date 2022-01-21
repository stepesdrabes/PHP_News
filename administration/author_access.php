<?php

if (!AuthService::is_logged_in()) {
    StatusMessageService::create_error_popup('Nejste přihlášeni!');
    App::redirect('index.php');
}

$current_user = AuthService::get_current_user();
$author_articles = ArticleRepository::get_author_articles($current_user['id']);
$articles_count = count($author_articles);

?>

<div id="articles">
    <h2><?= $articles_count . ' ' . ($articles_count == 1 ? "článek" : ($articles_count < 5 && $articles_count != 0 ? 'články' : 'článků')) ?></h2>

    <?php if ($articles_count > 0): ?>
        <div class="line"></div>

        <?php foreach ($author_articles as $article):
            $article_category = CategoryRepository::get_category($article['category_id']);
            $timestamp = date("d.m.Y H:i:s", strtotime($article['timestamp']));
            $author = UserRepository::get_user_by_id($article['user_id']);
            $full_name = $author['firstname'] . ' ' . $author['surname'];
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

                        <div style="margin: auto"></div>

                        <button>Upravit článek</button>
                    </div>
                </div>
            </div>

            <div class="line"></div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 32px; pointer-events: none; user-select: none">
            <div style="width: 10%; min-width: 200px;">
                <?= App::accent_color_svg('images/no_data.svg', [ '#000000' => App::get_settings_value('accentColor')]) ?>
            </div>

            <h3>Nenapsali jste zatím žádný článek</h3>
        </div>
    <?php endif; ?>
</div>