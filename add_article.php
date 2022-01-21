<!doctype html>

<?php

use JetBrains\PhpStorm\NoReturn;

include_once 'App.php';
App::init();

include_once 'database/ArticleRepository.php';
include_once 'database/CategoryRepository.php';
include_once 'services/StatusMessageService.php';
include_once 'services/AuthService.php';

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

    StatusMessageService::create_success_popup('Rozepsaný článek byl úspěšně uložen');

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

    if (!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        StatusMessageService::create_error_popup('Nebyl vybrán žádný obrázek k nahrání!');
        App::refresh();
    }

    if (!file_exists('uploads')) {
        mkdir('uploads');
    }

    $target_dir = 'uploads/';
    $guid = bin2hex(openssl_random_pseudo_bytes(16));
    $target_file = $target_dir . $guid . basename($_FILES['image']['name']);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['image']['tmp_name']);

    if ($check === false) {
        StatusMessageService::create_error_popup('Nahraný soubor není obrázek!');
        App::refresh();
    }

    if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif") {
        StatusMessageService::create_error_popup('Nepodporovaný formát! Podporované formáty jsou JPG, PNG a GIF');
        App::refresh();
    }

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        StatusMessageService::create_error_popup('Nastala chyba při nahrávání souboru!');
        App::refresh();
    }

    $article_id = ArticleRepository::create_article($user_id, $category, $article_title, $target_file, $content);

    if ($article_id == null) {
        $error_message = "Nastala chyba při vytváření článku";
        return;
    }

    StatusMessageService::create_success_popup('Článek byl úspěšně vytvořen');

    unset($_SESSION['article_draft_title']);
    unset($_SESSION['article_draft_content']);
    unset($_SESSION['article_draft_category']);

    App::refresh();
}

$categories = CategoryRepository::get_categories();

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
<body class="<?= App::get_color_scheme() ?>">

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] || $error_message != null ? ' class="active"' : '' ?>>
    <h1>Přidat nový článek</h1>

    <?php if (!AuthService::is_logged_in()): ?>
        <p>Tato sekce je k dispozici pouze po
            <a style="color: var(--highlight-color); text-decoration: underline" href="login.php">přihlášení!</a>
        </p>
    <?php else: ?>
        <form id="add-article-form" method="post" enctype="multipart/form-data">
            <div class="choose-image-box" id="choose-image-box">
                <label for="uploaded-image-input" class="choose-article-picture" id="uploaded-image-preview">
                    <i class="fi fi-br-camera"></i>
                    Vyberte kliknutím obrázek článku
                </label>
            </div>
            <input id="uploaded-image-input" type="file" name="image" accept="image/*"/>

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

<script>
    document.getElementById("uploaded-image-input").addEventListener("change", () => previewSelectedImage());

    function previewSelectedImage() {
        const chooseFile = document.getElementById("uploaded-image-input");
        const imageBox = document.getElementById("choose-image-box");

        const files = chooseFile.files[0];
        if (files) {
            const fileReader = new FileReader();

            fileReader.readAsDataURL(files);
            fileReader.addEventListener("load", function () {
                imageBox.innerHTML = `
                    <label for="uploaded-image-input" class="choose-article-picture" id="uploaded-image-preview">
                        <i class="fi fi-br-camera"></i>
                        Vyberte kliknutím obrázek článku
                    </label>

                    <div class="tools">
                        <div class="tool-button" id="trash-button">
                            <i class="fi fi-br-trash"></i>
                        </div>
                    </div>`;

                const imagePreview = document.getElementById("uploaded-image-preview");

                imagePreview.style.backgroundImage = 'url("' + this.result + '")';
                imagePreview.innerHTML = "";

                document.getElementById("trash-button").addEventListener("click", () => removeImage());
            });
        }
    }

    function removeImage() {
        const chooseFile = document.getElementById("uploaded-image-input");
        const imagePreview = document.getElementById("uploaded-image-preview");
        const imageBox = document.getElementById("choose-image-box");

        chooseFile.value = "";
        imagePreview.style.backgroundImage = "";

        imageBox.innerHTML = `
            <label for="uploaded-image-input" class="choose-article-picture" id="uploaded-image-preview">
                <i class="fi fi-br-camera"></i>
                Vyberte kliknutím obrázek článku
            </label>`;
    }
</script>

</html>