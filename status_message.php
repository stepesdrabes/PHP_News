<?php

if (!isset($_SESSION['success_message_timestamp'], $_SESSION['success_message_title'], $_SESSION['success_message_text'], $_SESSION['success_message_icon_class'])) {
    return;
}

$max_age = 3000;

$shown_before = $_SESSION['success_message_shown'] ?? false;
$current_millis = round(microtime(true) * 1000);
$message_age = $current_millis - (int)$_SESSION['success_message_timestamp'];

$icon_class = $_SESSION['success_message_icon_class'];
$title = $_SESSION['success_message_title'];
$message = $_SESSION['success_message_text'];
$text_color = $_SESSION['success_message_text_color'];
$bg_color = $_SESSION['success_message_background_color'];

if ($message_age > $max_age) {
    return;
}

$_SESSION['success_message_shown'] = true;

?>

<div id="success-message" class="<?= $shown_before ? ' active' : '' ?>"
     style="color: <?= $text_color ?>; background-color: <?= $bg_color ?>">
    <i class="fi <?= $icon_class ?>"></i>

    <div class="text-box">
        <h3><?= $title ?></h3>
        <p><?= $message ?></p>
    </div>
</div>

<script>
    const successMessage = document.getElementById("success-message")

    window.addEventListener("load", () => {
        successMessage.classList.add("active");

        setTimeout(function () {
            successMessage.classList.remove("active");
        }, <?= $max_age - $message_age ?>);
    });
</script>