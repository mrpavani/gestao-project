<?php

class User
{
    private $conn;
    private $table = 'users';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `username` VARCHAR(100) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` VARCHAR(50) DEFAULT 'user',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->conn->exec($sql);
    }

    public function create($username, $password, $role = 'user')
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO " . $this->table . " (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':role', $role);
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        }
        return ['success' => false];
    }

    public function getByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $query = "SELECT id, username, role, created_at FROM " . $this->table . " ORDER BY username ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT id, username, role, created_at FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
