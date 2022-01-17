<?php

use JetBrains\PhpStorm\NoReturn;

include_once 'services/AuthService.php';

class App
{
    private static string $accent_color = '#58c931';

    public static function init()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['logged_in'])) {
            $_SESSION['logged_in'] = false;
        }

        if (AuthService::is_logged_in() && AuthService::get_current_user() == null) {
            session_regenerate_id();
            $_SESSION['logged_in'] = false;
        }

        ?>

        <script>
            document.querySelector(':root').style.setProperty('--highlight-color', '<?= self::$accent_color ?>');

            window.addEventListener("load", () => {
                animate_main();
            });

            function animate_main() {
                const main = document.getElementsByTagName("main")[0];

                if (main != null) {
                    main.classList.add("active");
                }
            }
        </script>

        <?php
    }

    public static function accent_color_svg($file_path): string
    {
        return str_replace('#000000', self::$accent_color, file_get_contents($file_path));
    }

    #[NoReturn] public static function refresh()
    {
        header("location: {$_SERVER['REQUEST_URI']}");
        exit;
    }

    #[NoReturn] public static function redirect($url)
    {
        header("location: $url");
        exit;
    }
}