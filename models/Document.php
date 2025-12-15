<?php
class Document
{
    private $conn;
    private $table = "project_documents";

    public $id;
    public $project_id;
    public $file_name;
    public $file_path;
    public $uploaded_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Upload documento
    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                SET project_id = :project_id,
                    file_name = :file_name,
                    file_path = :file_path";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->file_name = htmlspecialchars(strip_tags($this->file_name));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));

        // Bind values
        $stmt->bindParam(":project_id", $this->project_id);
        $stmt->bindParam(":file_name", $this->file_name);
        $stmt->bindParam(":file_path", $this->file_path);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ler documentos de um projeto
    public function readByProject($project_id)
    {
        $query = "SELECT * FROM " . $this->table . "
                WHERE project_id = ?
                ORDER BY uploaded_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $project_id);
        $stmt->execute();

        return $stmt;
    }

    // Ler um documento
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->project_id = $row['project_id'];
            $this->file_name = $row['file_name'];
            $this->file_path = $row['file_path'];
            $this->uploaded_at = $row['uploaded_at'];
            return true;
        }
        return false;
    }

    // Deletar documento
    public function delete()
    {
        // Primeiro pegar o caminho do arquivo para deletar do disco
        $this->readOne();
        if (file_exists($this->file_path)) {
            unlink($this->file_path);
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
