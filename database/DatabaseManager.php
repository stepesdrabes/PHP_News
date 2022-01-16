<?php

class DatabaseManager
{
    private static DatabaseManager|null $instance = null;

    private string $host = 'localhost';
    private int $port = 3306;
    private string $database_name = 'zpravicky';
    private string $username = 'root';
    private string $password = '';

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
        $this->generate_default_structure();

        $this->mysql_open();
    }

    private function generate_default_structure()
    {
        $conn = new PDO("mysql:host=$this->host", $this->username, $this->password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->database_name'");

        if(((bool) $stmt->fetchColumn()) === TRUE) {
            return;
        }

        $file_path = 'database/database_scheme.sql';
        $sql = file_get_contents($file_path);

        $conn->exec($sql);
    }

    private function mysql_open()
    {
        $this->db_handle = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->database_name", $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $this->db_handle->query('SET NAMES utf8');
    }

    public
    function prepare($sql): bool|PDOStatement
    {
        return $this->get_db_handle()->prepare($sql);
    }

    private
    function get_db_handle(): PDO
    {
        if ($this->db_handle == null) {
            die("Something went wrong with the database!");
        }

        return $this->db_handle;
    }

    public
    function get_last_id(): int
    {
        return $this->get_db_handle()->lastInsertId();
    }

    /*public function delete_row(string $table_name, int $id): bool
    {
        $statement = $this->prepare("DELETE FROM $table_name WHERE id=:id");
        $statement->bindValue(":id", $id);

        return $statement->execute();
    }*/
}