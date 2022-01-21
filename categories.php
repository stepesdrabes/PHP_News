<?php

include_once 'App.php';
App::init();

include_once 'database/CategoryRepository.php';
include_once 'services/StatusMessageService.php';
include_once 'services/AuthService.php';

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
        $create_category_error = 'Nastala chyba při vytváření kategorie';
        return;
    }

    StatusMessageService::create_success_popup('Kategorie "' . $category_name . '" byla úspěšně vytvořena');
    App::refresh();
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles/style.css">

    <title>Kategorie | Zprávičky</title>
</head>
<body class="<?= App::get_color_scheme() ?>">

<?php
include 'status_message.php';
include 'nav_bar.php';
?>

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
            <div style="width: 10%; min-width: 200px;">
                <?= App::accent_color_svg('images/no_data.svg', ['#000000' => App::get_settings_value('accentColor')]) ?>
            </div>

            <h3>Neexistuje žádná kategorie</h3>
        </div>
    <?php endif; ?>
</main>

</body>

</html>