<?php
class Project
{
    private $conn;
    private $table = "projects";

    public $id;
    public $customer_id;
    public $project_name;
    public $project_type;
    public $project_value;
    public $description;
    public $observation;
    public $start_date;
    public $end_date;
    public $maintenance_start_date;
    public $maintenance_end_date;
    public $maintenance_monthly_value;
    public $payment_received_date;
    public $total_budget;
    public $current_spent;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                SET customer_id = :customer_id,
                    project_name = :project_name,
                    project_type = :project_type,
                    project_value = :project_value,
                    description = :description,
                    start_date = :start_date,
                    end_date = :end_date,
                    maintenance_start_date = :maintenance_start_date,
                    maintenance_end_date = :maintenance_end_date,
                    maintenance_monthly_value = :maintenance_monthly_value,
                    payment_received_date = :payment_received_date,
                    total_budget = :total_budget,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Convert empty strings to NULL for database insertion
        $customer_id = (empty($this->customer_id) || $this->customer_id === '') ? null : $this->customer_id;
        $start_date = (empty($this->start_date) || $this->start_date === '') ? null : $this->start_date;
        $end_date = (empty($this->end_date) || $this->end_date === '') ? null : $this->end_date;
        $maintenance_start_date = (empty($this->maintenance_start_date) || $this->maintenance_start_date === '') ? null : $this->maintenance_start_date;
        $maintenance_end_date = (empty($this->maintenance_end_date) || $this->maintenance_end_date === '') ? null : $this->maintenance_end_date;
        $payment_received_date = (empty($this->payment_received_date) || $this->payment_received_date === '') ? null : $this->payment_received_date;

        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":project_name", $this->project_name);
        $stmt->bindParam(":project_type", $this->project_type);
        $stmt->bindParam(":project_value", $this->project_value);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->bindParam(":maintenance_start_date", $maintenance_start_date);
        $stmt->bindParam(":maintenance_end_date", $maintenance_end_date);
        $stmt->bindParam(":maintenance_monthly_value", $this->maintenance_monthly_value);
        $stmt->bindParam(":payment_received_date", $payment_received_date);
        $stmt->bindParam(":total_budget", $this->total_budget);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    public function read()
    {
        // Seleciona campos explicitamente e garante que valores numéricos não retornem NULL
        $query = "SELECT id, project_name, project_type, project_value, description, start_date, end_date,
                         COALESCE(total_budget, 0) AS total_budget,
                         COALESCE(current_spent, 0) AS current_spent,
                         COALESCE(maintenance_monthly_value, 0) AS maintenance_monthly_value,
                         status, created_at, updated_at
                  FROM " . $this->table . "
                  ORDER BY start_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->customer_id = $row['customer_id'] ?? null;
        $this->project_name = $row['project_name'];
        $this->project_type = $row['project_type'];
        $this->project_value = (float) (isset($row['project_value']) ? $row['project_value'] : 0);
        $this->description = $row['description'];
        $this->start_date = $row['start_date'];
        $this->end_date = $row['end_date'];
        $this->maintenance_start_date = $row['maintenance_start_date'] ?? null;
        $this->maintenance_end_date = $row['maintenance_end_date'] ?? null;
        $this->maintenance_monthly_value = (float) (isset($row['maintenance_monthly_value']) ? $row['maintenance_monthly_value'] : 0);
        $this->payment_received_date = $row['payment_received_date'] ?? null;
        // Garantir que valores numéricos sejam sempre definidos e tipados
        $this->total_budget = (float) (isset($row['total_budget']) ? $row['total_budget'] : 0);
        $this->current_spent = (float) (isset($row['current_spent']) ? $row['current_spent'] : 0);
        $this->status = $row['status'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . "
                SET customer_id = :customer_id,
                    project_name = :project_name,
                    project_type = :project_type,
                    project_value = :project_value,
                    description = :description,
                    start_date = :start_date,
                    end_date = :end_date,
                    maintenance_start_date = :maintenance_start_date,
                    maintenance_end_date = :maintenance_end_date,
                    maintenance_monthly_value = :maintenance_monthly_value,
                    payment_received_date = :payment_received_date,
                    total_budget = :total_budget,
                    status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Convert empty strings to NULL for database update
        $customer_id = (empty($this->customer_id) || $this->customer_id === '') ? null : $this->customer_id;
        $start_date = (empty($this->start_date) || $this->start_date === '') ? null : $this->start_date;
        $end_date = (empty($this->end_date) || $this->end_date === '') ? null : $this->end_date;
        $maintenance_start_date = (empty($this->maintenance_start_date) || $this->maintenance_start_date === '') ? null : $this->maintenance_start_date;
        $maintenance_end_date = (empty($this->maintenance_end_date) || $this->maintenance_end_date === '') ? null : $this->maintenance_end_date;
        $payment_received_date = (empty($this->payment_received_date) || $this->payment_received_date === '') ? null : $this->payment_received_date;

        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":project_name", $this->project_name);
        $stmt->bindParam(":project_type", $this->project_type);
        $stmt->bindParam(":project_value", $this->project_value);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->bindParam(":maintenance_start_date", $maintenance_start_date);
        $stmt->bindParam(":maintenance_end_date", $maintenance_end_date);
        $stmt->bindParam(":maintenance_monthly_value", $this->maintenance_monthly_value);
        $stmt->bindParam(":payment_received_date", $payment_received_date);
        $stmt->bindParam(":total_budget", $this->total_budget);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    public function search($keywords)
    {
        $query = "SELECT * FROM " . $this->table . "
                WHERE project_name LIKE ? OR description LIKE ?
                ORDER BY start_date DESC";

        $stmt = $this->conn->prepare($query);

        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);

        $stmt->execute();
        return $stmt;
    }

    public function getProjectSummary()
    {
        // Resumo geral + separado por tipo de projeto + cards customizados
        // Using CAST to VARCHAR to safely handle empty strings and NULL
        $query = "SELECT 
            COUNT(*) as total_projects,
            COALESCE(SUM(total_budget), 0) as total_budget,
            COALESCE(SUM(current_spent), 0) as total_spent,
            COALESCE(AVG(CASE WHEN start_date IS NOT NULL AND CAST(start_date AS CHAR) != '' AND end_date IS NOT NULL AND CAST(end_date AS CHAR) != '' THEN DATEDIFF(end_date, start_date) ELSE NULL END), 0) as avg_duration,

            -- Projetos em planejamento (proposta enviada, sem datas)
            COUNT(CASE WHEN status = 'planejamento' AND (start_date IS NULL OR CAST(start_date AS CHAR) = '') AND (end_date IS NULL OR CAST(end_date AS CHAR) = '') THEN 1 END) as count_planejamento,
            SUM(CASE WHEN status = 'planejamento' AND (start_date IS NULL OR CAST(start_date AS CHAR) = '') AND (end_date IS NULL OR CAST(end_date AS CHAR) = '') THEN total_budget ELSE 0 END) as total_budget_planejamento,

            -- Orçamento: soma dos projetos aguardando aprovação (sem datas)
            SUM(CASE WHEN status = 'planejamento' AND (start_date IS NULL OR CAST(start_date AS CHAR) = '') AND (end_date IS NULL OR CAST(end_date AS CHAR) = '') THEN total_budget ELSE 0 END) as total_orcamento_propostas,

            -- Recebíveis: soma de projetos aprovados + sustentação, exceto manutenção de projetos em planejamento
            COALESCE(SUM(CASE WHEN status != 'planejamento' AND project_type IN ('desenvolvimento_sustentacao','desenvolvimento') THEN total_budget ELSE 0 END), 0) +
            COALESCE(SUM(CASE WHEN status != 'planejamento' AND project_type = 'sustentacao' AND maintenance_start_date IS NOT NULL AND CAST(maintenance_start_date AS CHAR) != '' AND maintenance_end_date IS NOT NULL AND CAST(maintenance_end_date AS CHAR) != '' THEN maintenance_monthly_value * (TIMESTAMPDIFF(MONTH, maintenance_start_date, maintenance_end_date) + 1) ELSE 0 END), 0) as total_recebiveis,

            -- Sustentação: soma mensal dentro do período cadastrado, só de projetos aprovados
            SUM(CASE WHEN status != 'planejamento' AND project_type = 'sustentacao' THEN maintenance_monthly_value ELSE 0 END) as total_sustentacao_mensal,

            -- Contadores antigos
            SUM(CASE WHEN project_type IN ('desenvolvimento_sustentacao', 'desenvolvimento') THEN total_budget ELSE 0 END) as total_budget_projects,
            SUM(CASE WHEN project_type = 'sustentacao' THEN total_budget ELSE 0 END) as total_budget_maintenance,
            SUM(CASE WHEN project_type IN ('desenvolvimento_sustentacao', 'desenvolvimento') THEN current_spent ELSE 0 END) as total_spent_projects,
            SUM(CASE WHEN project_type = 'sustentacao' THEN current_spent ELSE 0 END) as total_spent_maintenance,
            COUNT(CASE WHEN project_type IN ('desenvolvimento_sustentacao', 'desenvolvimento') THEN 1 END) as count_projects,
            COUNT(CASE WHEN project_type = 'sustentacao' THEN 1 END) as count_maintenance
        FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Tipar corretamente os campos numéricos antes de retornar
        foreach ($result as $k => $v) {
            if (strpos($k, 'count') === 0)
                $result[$k] = (int) $v;
            else
                $result[$k] = (float) $v;
        }
        return $result;
    }

    public function getProjectsByStatus()
    {
        $query = "SELECT status, COUNT(*) as count 
                  FROM " . $this->table . "
                  GROUP BY status";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    // Credentials methods
    public function getCredentials($project_id)
    {
        $query = "SELECT * FROM project_access_credentials WHERE project_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $project_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCredential($project_id, $data)
    {
        $query = "INSERT INTO project_access_credentials 
                  SET project_id = :project_id,
                      access_type = :access_type,
                      server_url = :server_url,
                      username = :username,
                      password = :password,
                      port = :port,
                      notes = :notes";

        $stmt = $this->conn->prepare($query);

        // Clean nulls
        $notes = $data['notes'] ?? '';
        $port = isset($data['port']) && $data['port'] !== '' ? $data['port'] : null;

        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':access_type', $data['access_type']);
        $stmt->bindParam(':server_url', $data['server_url']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':port', $port);
        $stmt->bindParam(':notes', $notes);

        return $stmt->execute();
    }

    public function deleteCredentials($project_id)
    {
        $query = "DELETE FROM project_access_credentials WHERE project_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $project_id);
        return $stmt->execute();
    }
}
