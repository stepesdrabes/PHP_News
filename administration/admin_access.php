<?php

if (!AuthService::is_logged_in()) {
    StatusMessageService::create_error_popup('Nejste přihlášeni!');
    App::redirect('index.php');
}

$current_user = AuthService::get_current_user();

if ($current_user['is_admin'] == false) {
    StatusMessageService::create_error_popup('Nemáte administrátorské oprávnění!');
    App::redirect('index.php');
}

$users = UserRepository::get_users();
$categories = CategoryRepository::get_categories();

?>

<div id="settings-box">
    <h2>Obecné nastavení</h2>

    <form method="post">
        <div class="checkboxes">
            <input class="input-field" type="text" name="app-name" value="<?= App::get_settings_value('appName') ?>">

            <div class="checkbox">
                <input type="color" name="app-color" value="<?= App::get_settings_value('accentColor') ?>">
                <label>Barva aplikace</label>
            </div>

            <!--<div class="checkbox">
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider round"></span>
                </label>
            </div>-->
        </div>

        <button>Uložit nastavení</button>
    </form>

    <div class="line"></div>
    <h2>Uživatelé</h2>

    <?php if (count($users) > 0): ?>
        <div id="authors" style="width: 100%">
            <?php foreach ($users as $author):
                $full_name = $author['firstname'] . ' ' . $author['surname'];
                $timestamp = date("d.m.Y", strtotime($author['timestamp']));
                ?>
                <a href="author.php?id=<?= $author['id'] ?>">
                    <div class="author">
                        <img class="profile-picture" alt="<?= $full_name ?>"
                             src="<?= $author['file_name'] ?>">

                        <div class="names"
                             style="display: flex; flex-direction: column; align-items: center">
                            <h3><?= $full_name ?></h3>
                            <p style="color: #a8a8a8; font-size: 0.8rem;"><?= $author['username'] ?></p>
                        </div>

                        <p>Účet založen: <?= $timestamp ?></p>

                        <div class="tools">
                            <div class="tool-button">
                                <i class="fi fi-br-crown"></i>
                                <p class="tool-description">Udělit administrátora</p>
                            </div>

                            <div class="tool-button" style="color: var(--error-color);">
                                <i class="fi fi-br-trash"></i>
                                <p class="tool-description">Smazat uživatele</p>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 32px; pointer-events: none; user-select: none">
            <div style="width: 10%; min-width: 200px;">
                <?= App::accent_color_svg('images/no_data.svg', ['#000000' => App::get_settings_value('accentColor')]) ?>
            </div>

            <h3>Neexistuje žádný autor</h3>
        </div>
    <?php endif; ?>

    <div class="line"></div>
    <h2>Kategorie</h2>

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

    <button style="width: 150px" onclick="toggle_popup()">Přidat kategorii</button>
</div>
