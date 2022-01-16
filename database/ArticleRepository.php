<?php

include_once 'DatabaseManager.php';

class ArticleRepository
{
    public static function create_article($user_id, $category_id, $title, $content): ?int
    {
        $sql = "INSERT INTO articles (user_id, category_id, title, content) VALUES (:user_id, :category_id, :title, :content)";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":user_id", $user_id);
        $statement->bindValue(":category_id", $category_id);
        $statement->bindValue(":title", $title);
        $statement->bindValue(":content", $content);

        if ($statement->execute() === false) {
            return null;
        }

        return DatabaseManager::get_instance()->get_last_id();
    }

    public static function get_all_articles(): array
    {
        $sql = "SELECT * FROM articles ORDER BY timestamp ASC";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll();

        if ($rows === false) {
            return [];
        }

        return $rows;
    }

    public static function get_category_articles($category_id): array
    {
        $sql = "SELECT * FROM articles WHERE category_id=:category_id ORDER BY timestamp ASC";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":category_id", $category_id);
        $statement->execute();
        $rows = $statement->fetchAll();

        if ($rows === false) {
            return [];
        }

        return $rows;
    }

    public static function get_author_articles($user_id): array
    {
        $sql = "SELECT * FROM articles WHERE user_id=:user_id ORDER BY timestamp ASC";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":user_id", $user_id);
        $statement->execute();
        $rows = $statement->fetchAll();

        if ($rows === false) {
            return [];
        }

        return $rows;
    }

    public static function get_article($article_id): ?array
    {
        $sql = "SELECT * FROM articles WHERE id=:id";
        $statement = DatabaseManager::get_instance()->prepare($sql);
        $statement->bindValue(":id", $article_id);
        $statement->execute();
        $result = $statement->fetch();

        if ($result === false) {
            return null;
        }

        return $result;
    }
}