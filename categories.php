<?php

include_once 'database/CategoryRepository.php';
include_once 'services/SuccessMessageService.php';
include_once 'services/AuthService.php';
AuthService::init();

$categories = CategoryRepository::get_categories();

$create_category_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_values();
}

function validate_values()
{
    global $create_category_error;

    if (!isset($_POST['category-name'])) {
        $create_category_error = 'Vyplňte všechny pole';
        return;
    }

    $category_name = trim($_POST['category-name']);

    if (strlen($category_name) < 3 || strlen($category_name) > 32) {
        $create_category_error = 'Název kategorie musí být delší než 3 znaky a kratší než 32 znaků';
        return;
    }

    if (!CategoryRepository::create_category($category_name)) {
        $create_category_error = 'Nastala chyba při vatváření kategorie';
        return;
    }

    SuccessMessageService::create_popup_message(
        'fi-br-check',
        'Úspěch',
        'Kategorie "' . $category_name . '" byla úspěšně vytvořena!',
        '#55d066',
        '#226329'
    );

    header('location: categories.php');
    exit;
}

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

    <title>Kategorie | Zprávičky</title>
</head>
<body>

<?php if (AuthService::is_logged_in()): ?>
    <div id="popup-background" class="<?= $create_category_error == null ? '' : 'active' ?>">
        <form id="popup-window" method="post" autocomplete="off">
            <div class="title-row"
                 style="width: 100%; display: flex; justify-content: space-between; align-items: center">
                <h1>Přidat kategorii</h1>
                <i class="fi fi-br-cross-circle" id="close-popup" onclick="toggle_popup()"></i>
            </div>

            <div style="height: 16px"></div>

            <input class="input-field" type="text" name="category-name" placeholder="Název kategorie...">

            <?php if ($create_category_error != null): ?>
                <p class="error-message"><?= $create_category_error ?></p>
            <?php endif; ?>

            <div style="height: 100%"></div>

            <div style="display: flex; width: 100%">
                <button type="button" onclick="toggle_popup()">Zavřít</button>
                <div style="width: 32px"></div>
                <button>Vytvořit kategorii</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php include 'success_message.php'; ?>

<header>
    <?php include 'nav_bar.php' ?>
</header>

<main<?= $_SESSION['current_location'] == $_SESSION['last_location'] ? ' class="active"' : '' ?>>
    <h1>Kategorie</h1>

    <?php if (count($categories) > 0): ?>
        <div id="categories">
            <?php foreach ($categories as $category): ?>
                <a href="index.php?category=<?= $category['id'] ?>">
                    <div class="category-chip">
                        <p><?= $category['name'] ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 32px; pointer-events: none; user-select: none">
            <img style="width: 10%; min-width: 200px;" src="images/no_data.svg" alt="No data">
            <h3>Neexistuje žádná kategorie</h3>
        </div>
    <?php endif; ?>

    <?php if (AuthService::is_logged_in()): ?>
        <button style="width: 150px" onclick="toggle_popup()">Přidat kategorii</button>
    <?php endif; ?>
</main>

</body>

<?php if (AuthService::is_logged_in()): ?>
    <script>
        const popupWindow = document.getElementById("popup-window");
        const popup = document.getElementById("popup-background");

        function toggle_popup() {
            popupWindow.classList.toggle("active");
            popup.classList.toggle("active");
        }
    </script>
<?php endif; ?>

</html>