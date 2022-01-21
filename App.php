<?php

use JetBrains\PhpStorm\NoReturn;

include_once 'services/AuthService.php';

class App
{
    private static array $settings = [];

    public static function init()
    {
        $json = file_get_contents('app_settings.json');
        self::$settings = json_decode($json, true);

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
            document.querySelector(':root').style.setProperty('--highlight-color', '<?= self::$settings['accentColor'] ?>');

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

    public static function accent_color_svg($file_path, $color_map): string
    {
        $svg = file_get_contents($file_path);

        foreach ($color_map as $color => $new_color) {
            $svg = str_replace($color, $new_color, $svg);
        }

        return $svg;
    }

    public static function get_settings_value($key): mixed
    {
        return self::$settings[$key];
    }

    public static function save_settings_value($key, $value)
    {
        self::$settings[$key] = $value;
        $json = json_encode(self::$settings, JSON_PRETTY_PRINT);
        file_put_contents('app_settings.json', $json);
    }

    public static function get_color_scheme(): string
    {
        $dark = $_SESSION['darkTheme'] ?? false;
        return $dark ? 'dark-colors' : 'light-colors';
    }

    public static function toggle_color_scheme()
    {
        $dark = $_SESSION['darkTheme'] ?? false;
        $_SESSION['darkTheme'] = !$dark;
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