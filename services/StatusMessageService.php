<?php

class StatusMessageService
{
    public static function create_popup_message($icon_class, $title, $message, $background_color, $text_color)
    {
        $current_millis = round(microtime(true) * 1000);

        $_SESSION['success_message_timestamp'] = $current_millis;
        $_SESSION['success_message_shown'] = false;
        $_SESSION['success_message_icon_class'] = $icon_class;
        $_SESSION['success_message_text'] = $message;
        $_SESSION['success_message_title'] = $title;
        $_SESSION['success_message_background_color'] = $background_color;
        $_SESSION['success_message_text_color'] = $text_color;
    }

    public static function create_success_popup($message)
    {
        self::create_popup_message(
            'fi-br-check',
            'Úspěch',
            $message,
            '#55d066',
            '#226329'
        );
    }

    public static function create_error_popup($message)
    {
        self::create_popup_message(
            'fi-br-cross',
            'Chyba',
            $message,
            '#fa3f4c',
            '#6e1421'
        );
    }
}