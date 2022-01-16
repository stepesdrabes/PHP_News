<?php

$nav_categories = [
    'index.php' => 'Články',
    'categories.php' => 'Kategorie',
    'authors.php' => 'Autoři',
    'administration.php' => 'Administrace',
    'add_article.php' => 'Přidat článek',
];

$last_location = $_SESSION['current_location'] ?? null;
$current_location = basename($_SERVER["SCRIPT_FILENAME"], '.php') . '.php';

$_SESSION['last_location'] = $last_location;
$_SESSION['current_location'] = $current_location;

$same_location = $current_location === $last_location;
$from_user_info = $_SESSION['from_user_info'] ?? false;

unset($_SESSION['from_user_info']);

?>

<div id="navigation-bar">
    <a href="index.php">
        <h1>Zprávičky</h1>
    </a>

    <nav>
        <?php foreach ($nav_categories as $file => $category_name): ?>
            <a id="<?= $file ?>"
               class="<?= ($current_location === $file && $same_location) || ($last_location === $file && !$same_location) ? 'selected' : '' ?><?= ($current_location === $file && !$same_location) ? ' new-select' : '' ?>"
               href="<?= $file ?>">
                <div class="text-box">
                    <p style="padding: 10px;"><?= $category_name ?></p>
                </div>

                <div class="underline"></div>
            </a>
        <?php endforeach; ?>
    </nav>

    <div style="flex: 1"></div>

    <div id="auth-info">
        <?php if (AuthService::is_logged_in()):
            $user = AuthService::get_current_user();
            $full_name = $user['firstname'] . ' ' . $user['surname'];
            $img_src = $user['image'];
            ?>

            <h4><?= $full_name ?></h4>

            <img class="profile-picture" alt="<?= $full_name ?>" src="<?= $img_src ?>">

            <div id="user-popup"<?= $from_user_info ? ' class="active"' : '' ?>>
                <img class="profile-picture" alt="" src="<?= $img_src ?>">

                <div class="names" style="display: flex; flex-direction: column; align-items: center">
                    <h4><?= $full_name ?></h4>
                    <p class="username"><?= $user['username'] ?></p>
                </div>

                <a href="author.php?id=<?= $user['id'] ?>&from_user_info" style="width: 100%">
                    <button>Profil</button>
                </a>

                <a href="logout.php" style="width: 100%">
                    <button>Odhlásit se</button>
                </a>
            </div>
        <?php else: ?>
            <a href="login.php">
                <button>Přihlášení</button>
            </a>

            <a href="register.php">
                <button>Registrace</button>
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
    window.addEventListener("load", () => {
        <?php if (!$same_location): ?>
        let newLocation = document.getElementById("<?= $current_location ?>");

        if (newLocation != null) {
            newLocation.classList.add("selected");
        }

        let lastLocation = document.getElementById("<?= $last_location ?>");

        if (lastLocation != null) {
            lastLocation.classList.remove("selected");
        }
        <?php endif; ?>

        <?php if($from_user_info): ?>
        let userPopup = document.getElementById("user-popup");
        userPopup.classList.remove("active");

        setTimeout(function () {
            canOpen = true;
        }, 300);
        <?php endif; ?>
    });

    const navigationBar = document.getElementById("navigation-bar");

    window.addEventListener("scroll", function () {
        navigationBar.classList.toggle("smaller", window.scrollY > 0);
    })

    const authInfo = document.getElementById("auth-info");
    const userPopup = document.getElementById("user-popup");

    let canOpen = <?= $from_user_info ? 'false' : 'true' ?>;

    authInfo.addEventListener("mouseenter", function () {
        if (!userPopup.classList.contains("active") && canOpen) {
            userPopup.classList.add("active");
        }
    }, false);

    authInfo.addEventListener("mouseleave", function () {
        if (canOpen) {
            userPopup.classList.remove("active");
            canOpen = false;

            setTimeout(function () {
                canOpen = true;
            }, 300);
        }
    }, false);

</script>