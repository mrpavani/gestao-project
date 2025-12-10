<?php
class Expense
{
    private $conn;
    private $table = "project_costs";

    public $id;
    public $project_id;
    public $cost_type;
    public $description;
    public $amount;
    public $cost_date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                SET project_id = :project_id,
                    cost_type = :cost_type,
                    description = :description,
                    amount = :amount,
                    cost_date = :cost_date";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->project_id = htmlspecialchars(strip_tags($this->project_id));
        $this->cost_type = htmlspecialchars(strip_tags($this->cost_type));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->amount = floatval($this->amount);
        $this->cost_date = htmlspecialchars(strip_tags($this->cost_date));

        // Bind de valores
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':cost_type', $this->cost_type);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':cost_date', $this->cost_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readByProjectId()
    {
        $query = "SELECT * FROM " . $this->table . "
                WHERE project_id = :project_id
                ORDER BY cost_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->execute();

        return $stmt;
    }

    public function getTotal()
    {
        $query = "SELECT COALESCE(SUM(amount), 0) as total FROM " . $this->table . "
                WHERE project_id = :project_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($result['total']);
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . "
                SET cost_type = :cost_type,
                    description = :description,
                    amount = :amount,
                    cost_date = :cost_date
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->cost_type = htmlspecialchars(strip_tags($this->cost_type));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->amount = floatval($this->amount);
        $this->cost_date = htmlspecialchars(strip_tags($this->cost_date));

        // Bind de valores
        $stmt->bindParam(':cost_type', $this->cost_type);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':cost_date', $this->cost_date);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
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
