<?php

use JetBrains\PhpStorm\NoReturn;

include_once 'App.php';
App::init();

include_once 'database/UserRepository.php';
include_once 'database/ArticleRepository.php';
include_once 'database/CategoryRepository.php';
include_once 'services/StatusMessageService.php';
include_once 'services/AuthService.php';

if (empty($_GET['id'])) {
    StatusMessageService::create_error_popup('V požadavku nebylo zadáno ID autora!');

    header('location: index.php');
    exit;
}

$error_message = null;
$username = $_GET['id'];
$author = UserRepository::get_user_by_id($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validate_values();
}

#[NoReturn] function validate_values()
{
    global $author;

    if (!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        StatusMessageService::create_error_popup('Nebyl vybrán žádný obrázek k nahrání!');

        header('location: author.php?id=' . $author['id']);
        exit;
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

    if (!UserRepository::change_user_profile_picture($author['id'], $target_file)) {
        StatusMessageService::create_error_popup('Nastala chyba při nastavování profilového obrázku!');
        App::refresh();
    }

    StatusMessageService::create_success_popup('Profilový obrázek byl změněn');

    App::refresh();
}

if (isset($_GET['from_user_info'])) {
    $_SESSION['from_user_info'] = true;
    App::redirect("author.php?id={$_GET['id']}");
}

if ($author == null) {
    StatusMessageService::create_error_popup('Uživatel "' . $username . '" nebyl nalezen!');
    App::redirect("location: author.php?id={$_GET['id']}");
}

$is_current_user = AuthService::is_logged_in() && AuthService::get_current_user() === $author;
$articles = ArticleRepository::get_author_articles($author['id']);
$articles_count = count($articles);

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
<body>

<?php if ($is_current_user): ?>
    <div id="popup-background" class="<?= $error_message == null ? '' : 'active' ?>">
        <form id="popup-window" method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="title-row"
                 style="width: 100%; display: flex; justify-content: space-between; align-items: center">
                <h1>Nahrát profilový obrázek</h1>
                <i class="fi fi-br-cross-circle" id="close-popup" onclick="toggle_popup()"></i>
            </div>

            <div style="height: 16px"></div>

            <label for="server-image-input" class="choose-picture" id="server-image-preview">
                <i class="fi fi-br-camera"></i>
                Nahrát
            </label>

            <input id="server-image-input" type="file" name="image" accept="image/*"/>

            <?php if ($error_message != null): ?>
                <p class="error-message"><?= $error_message ?></p>
            <?php endif; ?>

            <div style="flex: 1"></div>

            <div style="display: flex; width: 100%">
                <button type="button" onclick="toggle_popup()">Zavřít</button>
                <div style="width: 32px"></div>
                <button>Nahrát obrázek</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <div style="display: flex; align-items: center; gap: 16px">
        <a href="index.php">
            <h1>Autoři</h1>
        </a>

        <i style="color: #a8a8a8; font-size: 16px" class="fi fi-br-arrow-small-right"></i>
        <p style="color: #a8a8a8; font-weight: 500"><?= $author['firstname'] . ' ' . $author['surname'] ?></p>
    </div>

    <div id="author-profile-view">
        <img class="profile-picture" src="<?= $author['file_name'] ?>" alt="pfp">

        <?php if ($is_current_user): ?>
            <button style="width: auto" onclick="toggle_popup()">Změnit profilový obrázek</button>
        <?php endif; ?>

        <div class="names" style="display: flex; flex-direction: column; align-items: center">
            <h1><?= $author['firstname'] . ' ' . $author['surname'] ?></h1>
            <p style="color: #a8a8a8; font-size: 1rem;"><?= $author['username'] ?></p>
        </div>
    </div>

    <div id="articles">
        <div class="line"></div>

        <h2><?= $articles_count . ' ' . ($articles_count == 1 ? "článek" : ($articles_count < 5 && $articles_count != 0 ? 'články' : 'článků')) ?></h2>

        <?php if ($articles_count > 0): ?>
            <div class="line"></div>

            <?php foreach ($articles as $article):
                $article_category = CategoryRepository::get_category($article['category_id']);
                $timestamp = date("d.m.Y H:i:s", strtotime($article['timestamp']));
                $author = UserRepository::get_user_by_id($article['user_id']);
                $full_name = $author['firstname'] . ' ' . $author['surname'];
                ?>
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

                    <p style="color: #535353; width: 75%"><?= substr(strip_tags($article['content']), 0, 256) . ' ...' ?></p>

                    <div style="display: flex; align-items: center; gap: 8px">
                        <p>Vytvořeno <?= $timestamp ?> autorem</p>

                        <a href="author.php?id=<?= $author['id'] ?>">
                            <h4><?= $author['firstname'] . ' ' . $author['surname'] ?></h4>
                        </a>

                        <img class="profile-picture" src="<?= $author['file_name'] ?>" alt="">
                    </div>
                </div>

                <div class="line"></div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 32px; pointer-events: none; user-select: none">
                <div style="width: 10%; min-width: 200px;">
                    <?= App::accent_color_svg('images/no_data.svg') ?>
                </div>

                <h3>Tento autor nenapsal žádný článek</h3>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php if ($is_current_user): ?>
    <script>
        document.getElementById("server-image-input").addEventListener("change", () => previewSelectedImage());

        const popupWindow = document.getElementById("popup-window");
        const popup = document.getElementById("popup-background");

        function toggle_popup() {
            popupWindow.classList.toggle("active");
            popup.classList.toggle("active");

            const imagePreview = document.getElementById("server-image-preview");

            setTimeout(function () {
                imagePreview.style.backgroundImage = 'url("' + "data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg%27%3e%3crect width='100%25' height='100%25' fill='none' rx='100' ry='100' stroke='%23000000' stroke-width='5' stroke-dasharray='9' stroke-linecap='butt'/%3e%3c/svg%3e" + '")';
                imagePreview.innerHTML = '<i class="fi fi-br-camera"></i><p class="upload">Nahrát</p>';
            }, 300);
        }

        function previewSelectedImage() {
            const chooseFile = document.getElementById("server-image-input");
            const imagePreview = document.getElementById("server-image-preview");

            const files = chooseFile.files[0];
            if (files) {
                const fileReader = new FileReader();

                fileReader.readAsDataURL(files);
                fileReader.addEventListener("load", function () {
                    imagePreview.style.backgroundImage = 'url("' + this.result + '")';
                    imagePreview.innerHTML = "";
                });
            }
        }
    </script>
<?php endif; ?>

</body>
</html>