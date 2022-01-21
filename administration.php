<?php

include_once 'App.php';
App::init();

include_once 'database/CategoryRepository.php';
include_once 'database/UserRepository.php';
include_once 'database/ArticleRepository.php';
include_once 'services/AuthService.php';
include_once 'services/StatusMessageService.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!AuthService::is_logged_in() || AuthService::get_current_user()['is_admin'] == false) {
        StatusMessageService::create_error_popup('Nemáte administrátorské oprávnění!');
        App::redirect('index.php');
    }

    if (!isset($_POST['app-color'], $_POST['app-name'])) {
        StatusMessageService::create_error_popup('Zadejte všechny hodnoty');
    }

    App::save_settings_value('accentColor', $_POST['app-color']);
    App::save_settings_value('appName', $_POST['app-name']);
    StatusMessageService::create_success_popup('Nastavení aplikace uloženo');

    App::refresh();
}

$categories = CategoryRepository::get_categories();
$users = UserRepository::get_users();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles/style.css">

    <title>Administrace | Zprávičky</title>
</head>
<body class="<?= App::get_color_scheme() ?>">

<?php if (AuthService::is_admin()): ?>
    <div id="popup-background">
        <form id="popup-window" method="post" autocomplete="off">
            <div class="title-row"
                 style="width: 100%; display: flex; justify-content: space-between; align-items: center">
                <h1>Přidat kategorii</h1>
                <i class="fi fi-br-cross-circle" id="close-popup" onclick="toggle_popup()"></i>
            </div>

            <div style="height: 16px"></div>

            <input class="input-field" type="text" name="category-name" placeholder="Název kategorie...">

            <div style="height: 100%"></div>

            <div style="display: flex; width: 100%">
                <button type="button" onclick="toggle_popup()">Zavřít</button>
                <div style="width: 32px"></div>
                <button>Vytvořit kategorii</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <div class="title-column" style="display: flex; flex-direction: column;">
        <h1>Administrace</h1>

        <?php if (AuthService::is_logged_in()) {
            echo '<p>' . (AuthService::get_current_user()['is_admin'] == false ? 'Editujete své vlastní články' : 'Jste administrátorem stránky') . '</p>';
        } ?>
    </div>

    <?php if (!AuthService::is_logged_in()) { ?>
        <p>Tato sekce je k dispozici pouze po
            <a style="color: var(--highlight-color); text-decoration: underline" href="login.php">přihlášení!</a>
        </p>
    <?php } else {
        if (AuthService::get_current_user()['is_admin'] == false) {
            include 'administration/author_access.php';
        } else {
            include 'administration/admin_access.php';
        }
    }
    ?>
</main>

<?php if (AuthService::is_admin()): ?>
    <script>
        const popupWindow = document.getElementById("popup-window");
        const popup = document.getElementById("popup-background");

        function toggle_popup() {
            popupWindow.classList.toggle("active");
            popup.classList.toggle("active");
        }
    </script>
<?php endif; ?>

</body>
</html>