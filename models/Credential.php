<?php
class Credential
{
    private $conn;
    private $table = "project_access_credentials";

    public $id;
    public $project_id;
    public $access_type;
    public $server_url;
    public $username;
    public $password;
    public $port;
    public $notes;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                SET project_id = :project_id,
                    access_type = :access_type,
                    server_url = :server_url,
                    username = :username,
                    password = :password,
                    port = :port,
                    notes = :notes";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->project_id = htmlspecialchars(strip_tags($this->project_id));
        $this->access_type = htmlspecialchars(strip_tags($this->access_type));
        $this->server_url = htmlspecialchars(strip_tags($this->server_url));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->port = htmlspecialchars(strip_tags($this->port));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':access_type', $this->access_type);
        $stmt->bindParam(':server_url', $this->server_url);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':port', $this->port);
        $stmt->bindParam(':notes', $this->notes);

        return $stmt->execute();
    }

    public function readByProjectId()
    {
        $query = "SELECT * FROM " . $this->table . "
                WHERE project_id = :project_id
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->execute();

        return $stmt;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . "
                SET access_type = :access_type,
                    server_url = :server_url,
                    username = :username,
                    password = :password,
                    port = :port,
                    notes = :notes
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->access_type = htmlspecialchars(strip_tags($this->access_type));
        $this->server_url = htmlspecialchars(strip_tags($this->server_url));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->port = htmlspecialchars(strip_tags($this->port));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind
        $stmt->bindParam(':access_type', $this->access_type);
        $stmt->bindParam(':server_url', $this->server_url);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':port', $this->port);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getById()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
