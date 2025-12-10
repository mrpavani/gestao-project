<?php
class ExpenseController
{
    private $expenseModel;
    private $projectModel;

    public function __construct($db)
    {
        $this->expenseModel = new Expense($db);
        $this->projectModel = new Project($db);
    }

    // Parse moeda formatada (ex: "R$ 1.234,56") para float-string
    private function parseCurrency($value)
    {
        if ($value === null || $value === '') return 0;
        if (is_numeric($value)) return (float)$value;
        $s = trim($value);
        $s = preg_replace('/[^0-9,\.\-]/u', '', $s);
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
            return floatval($s);
        }
        if (strpos($s, ',') !== false) {
            $s = str_replace(',', '.', $s);
            return floatval($s);
        }
        if (strpos($s, '.') !== false) {
            return floatval($s);
        }
        $digits = preg_replace('/\D+/', '', $s);
        if ($digits === '') return 0;
        return intval($digits) / 100.0;
    }

    public function create($data)
    {
        // aceitar valores com máscara (ex: "R$ 1.234,56")
        $this->expenseModel->project_id = $data['project_id'];
        $this->expenseModel->cost_type = $data['cost_type'];
        $this->expenseModel->description = $data['description'];
        $this->expenseModel->amount = $this->parseCurrency(isset($data['amount']) ? $data['amount'] : 0);
        $this->expenseModel->cost_date = $data['cost_date'];

        if ($this->expenseModel->create()) {
            // Atualizar o total de gastos do projeto
            $this->updateProjectSpent($data['project_id']);
            return ["success" => true, "message" => "Gasto registrado com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao registrar gasto."];
    }

    public function getByProjectId($projectId)
    {
        $this->expenseModel->project_id = $projectId;
        $stmt = $this->expenseModel->readByProjectId();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalByProjectId($projectId)
    {
        $this->expenseModel->project_id = $projectId;
        return $this->expenseModel->getTotal();
    }

    public function update($data)
    {
        $this->expenseModel->id = $data['id'];
        $this->expenseModel->cost_type = $data['cost_type'];
        $this->expenseModel->description = $data['description'];
        // aceitar máscara de moeda ao atualizar
        $this->expenseModel->amount = $this->parseCurrency(isset($data['amount']) ? $data['amount'] : 0);
        $this->expenseModel->cost_date = $data['cost_date'];

        if ($this->expenseModel->update()) {
            // Obter project_id do gasto para atualizar total
            $expense = $this->expenseModel->getById();
            $this->updateProjectSpent($expense['project_id']);
            return ["success" => true, "message" => "Gasto atualizado com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao atualizar gasto."];
    }

    public function delete($id, $projectId)
    {
        $this->expenseModel->id = $id;

        if ($this->expenseModel->delete()) {
            // Atualizar total de gastos do projeto
            $this->updateProjectSpent($projectId);
            return ["success" => true, "message" => "Gasto removido com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao remover gasto."];
    }

    private function updateProjectSpent($projectId)
    {
        // Obter total de gastos
        $this->expenseModel->project_id = $projectId;
        $total = $this->expenseModel->getTotal();

        // Atualizar projeto
        $this->projectModel->id = $projectId;
        $this->projectModel->readOne();
        $this->projectModel->current_spent = $total;
        $this->projectModel->update();
    }
}
