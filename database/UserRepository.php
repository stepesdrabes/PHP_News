<?php

include_once 'DatabaseManager.php';

class UserRepository
{
    public static function create_user($username, $firstname, $surname, $password): bool
    {
        $sql = "INSERT INTO users (username, firstname, surname, password_hash) VALUES (:username, :firstname, :surname, :password)";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":username", strtolower($username));
        $statement->bindValue(":firstname", ucfirst(strtolower($firstname)));
        $statement->bindValue(":surname", ucfirst(strtolower($surname)));
        $statement->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));

        return $statement->execute();
    }

    public static function change_user_profile_picture($user_id, $file_name): bool
    {
        $current_user = self::get_user_by_id($user_id);

        if ($current_user == null) {
            return false;
        }

        $file_path = $current_user['file_name'];

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $sql = "UPDATE users SET file_name=:file_name WHERE id=:id";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":file_name", $file_name);
        $statement->bindValue(":id", $user_id);

        return $statement->execute();
    }

    public static function get_user_by_id($id)
    {
        $sql = "SELECT * FROM users WHERE id=:id";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":id", strtolower($id));
        $statement->execute();

        return $statement->fetch();
    }

    public static function get_user_by_username($username)
    {
        $sql = "SELECT * FROM users WHERE username=:username";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":username", strtolower($username));
        $statement->execute();

        return $statement->fetch();
    }

    public static function user_exists($username): bool
    {
        return self::get_user_by_username($username) != null;
    }

    public static function get_users(): array
    {
        $sql = "SELECT * FROM users";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll();

        if ($rows === false) {
            return [];
        }

        return $rows;
    }
}