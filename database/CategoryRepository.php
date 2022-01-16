<?php

class CategoryRepository
{
    public static function create_category($category_name): bool
    {
        $sql = "INSERT INTO categories (name) VALUES (:category_name)";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":category_name", $category_name);

        return $statement->execute();
    }

    public static function get_categories(): array
    {
        $sql = "SELECT * FROM categories";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll();

        if ($rows === false) {
            return [];
        }

        return $rows;
    }

    public static function get_category($category_id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id=:id";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":id", $category_id);
        $statement->execute();
        $row = $statement->fetch();

        if($row === false) {
            return null;
        }

        return $row;
    }
}