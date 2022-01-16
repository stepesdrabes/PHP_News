<?php

include_once 'database/UserRepository.php';

class AuthService
{
    public static function init()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['logged_in'])) {
            $_SESSION['logged_in'] = false;
        }
    }

    public static function check_password($username, $password): bool
    {
        $user = UserRepository::get_user_by_username($username);

        if ($user == null) {
            return false;
        }

        return password_verify($password, $user['password_hash']);
    }

    public static function login($username): array
    {
        $user = UserRepository::get_user_by_username($username);

        $_SESSION['id'] = $user['id'];
        $_SESSION['logged_in'] = true;

        return $user;
    }

    public static function logout()
    {
        unset($_SESSION['id']);
        $_SESSION['logged_in'] = false;
    }

    public static function get_current_user()
    {
        $user = UserRepository::get_user_by_id($_SESSION['id']);

        $img = "https://www.jollysoft.cz/static/images/pavel-foltyn-jollysoft-profile.jpeg";
        $user['image'] = $img;

        return $user;
    }

    public static function is_logged_in(): bool
    {
        return $_SESSION['logged_in'];
    }
}