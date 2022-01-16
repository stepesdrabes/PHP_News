<?php

use JetBrains\PhpStorm\NoReturn;

include_once 'database/ArticleRepository.php';
include_once 'database/CategoryRepository.php';
include_once 'services/SuccessMessageService.php';
include_once 'services/AuthService.php';
AuthService::init();

$error_message = null;

$article_title = $_SESSION['article_draft_title'] ?? null;
$content = $_SESSION['article_draft_content'] ?? null;
$category = $_SESSION['article_draft_category'] ?? null;

$draft = $article_title != null || $content != null || $category != null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $button = $_POST['button'] ?? 1;

    if ($button == 1) {
        validate_values();
    } else {
        save_values();
    }
}

#[NoReturn] function save_values()
{
    $article_title = $_POST['title'] ?? null;
    $content = $_POST['content'] ?? null;
    $category = $_POST['category'] ?? null;

    $_SESSION['article_draft_title'] = $article_title;
    $_SESSION['article_draft_content'] = $content;
    $_SESSION['article_draft_category'] = $category;

    SuccessMessageService::create_popup_message(
        'fi-br-check',
        'Úspěch',
        'Rozepsaný článek byl úspěšně uložen',
        '#55d066',
        '#226329'
    );

    header('location: index.php');
    exit;
}

#[NoReturn] function validate_values()
{
    global $error_message;
    global $article_title;
    global $content;
    global $category;

    $article_title = $_POST['title'] ?? null;
    $content = $_POST['content'] ?? null;
    $category = $_POST['category'] ?? null;

    if (!AuthService::is_logged_in()) {
        $error_message = "Nejsi přihlášen!";
        return;
    }

    $user_id = AuthService::get_current_user()['id'];

    if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['category'])) {
        $error_message = "Vyplňte všechna pole!";
        return;
    }

    if (strlen($article_title) < 16 || strlen($article_title) > 256) {
        $error_message = "Nadpis musí být alespoň 16 a maximálně 256 znaků dlouhý";
        return;
    }

    $article_id = ArticleRepository::create_article($user_id, $category, $article_title, $content);

    if ($article_id == null) {
        $error_message = "Nastala chyba při vytváření článku";
        return;
    }

    SuccessMessageService::create_popup_message(
        'fi-br-check',
        'Úspěch',
        'Článek byl úspěšně vytvořen',
        '#55d066',
        '#226329'
    );

    unset($_SESSION['article_draft_title']);
    unset($_SESSION['article_draft_content']);
    unset($_SESSION['article_draft_category']);

    header('location: article.php?id=' . $article_id);
    exit;
}

$categories = CategoryRepository::get_categories();

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

    <script src="https://cdn.tiny.cloud/1/t1wch2d6qdr4qguzt2odfy24on1ilk20is1ux0uewe4vc3oy/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: '#article-content'
        });
    </script>

    <title>Přidat článek | Zprávičky</title>
</head>
<body>

<?php include 'success_message.php'; ?>

<header>
    <?php include 'nav_bar.php' ?>
</header>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] || $error_message != null ? ' class="active"' : '' ?>>
    <h1>Přidat nový článek</h1>

    <?php if (!AuthService::is_logged_in()): ?>
        <p>Tato sekce je k dispozici pouze po
            <a style="color: var(--highlight-color); text-decoration: underline" href="login.php">přihlášení!</a>
        </p>
    <?php else: ?>
        <form id="add-article-form" method="post">
            <input type="text" class="input-field" name="title"
                   placeholder="Nadpis článku"<?= $article_title != null ? ' value="' . $article_title . '"' : '' ?>>

            <select class="input-field" name="category">
                <option value="">Vyberte kategorii...</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($error_message != null): ?>
                <p class="error-message"><?= $error_message ?></p>
            <?php endif; ?>

            <textarea style="min-height: 600px" id="article-content"
                      name="content"><?= $content == null ? 'Tohle je text vašeho zbrusu nového článku!' : $content ?></textarea>

            <div style="display: flex; justify-content: space-between">
                <button name="button" style="width: 160px" value="0">Uložit rozepsaný</button>
                <button name="button" style="width: 160px" value="1">Přidat článek</button>
            </div>
        </form>
    <?php endif; ?>
</main>

</body>

</html>