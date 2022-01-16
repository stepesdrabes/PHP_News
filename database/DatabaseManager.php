<?php

class DatabaseManager
{
    private static DatabaseManager|null $instance = null;

    public static function get_instance(): DatabaseManager
    {
        if (self::$instance == null) {
            self::$instance = new DatabaseManager();
        }

        return self::$instance;
    }

    private PDO|null $db_handle = null;

    public function __construct()
    {
        $this->mysql_open();
    }

    private function mysql_open()
    {
        $this->db_handle = new PDO('mysql:host=localhost;dbname=zpravicky', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $this->db_handle->query('SET NAMES utf8');
    }

    public function prepare($sql): bool|PDOStatement
    {
        return $this->get_db_handle()->prepare($sql);
    }

    private function get_db_handle(): PDO
    {
        if ($this->db_handle == null) {
            die("Something went wrong with the database!");
        }

        return $this->db_handle;
    }

    public function get_last_id(): int
    {
        return $this->get_db_handle()->lastInsertId();
    }

    public function delete_row(string $table_name, int $id): bool
    {
        $statement = $this->prepare("DELETE FROM $table_name WHERE id=:id");
        $statement->bindValue(":id", $id);

        return $statement->execute();
    }
}