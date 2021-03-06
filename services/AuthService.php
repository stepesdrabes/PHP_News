<?php

include_once 'database/UserRepository.php';

class AuthService
{
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

    public static function is_admin(): bool {
        $user = self::get_current_user();

        if($user == null) {
            return false;
        }

        return $user['is_admin'];
    }

    public static function logout()
    {
        unset($_SESSION['id']);
        $_SESSION['logged_in'] = false;
    }

    public static function get_current_user()
    {
        return UserRepository::get_user_by_id($_SESSION['id']);
    }

    public static function is_logged_in(): bool
    {
        return $_SESSION['logged_in'];
    }
}