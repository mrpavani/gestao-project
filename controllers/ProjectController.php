<?php
class ProjectController
{
    private $projectModel;

    public function __construct($db)
    {
        $this->projectModel = new Project($db);
    }

    // Parse moeda formatada (ex: "R$ 1.234,56") para float
    private function parseCurrency($value)
    {
        if ($value === null || $value === '')
            return 0;
        // se já for número puro
        if (is_numeric($value))
            return (float) $value;
        $s = trim($value);
        // remover prefixos como R$
        $s = preg_replace('/[^0-9,\.\-]/u', '', $s);
        // se contém vírgula e ponto (ex: 1.234,56) -> remover pontos e trocar vírgula por ponto
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
            return floatval($s);
        }
        // se contém vírgula apenas (ex: 1234,56)
        if (strpos($s, ',') !== false) {
            $s = str_replace(',', '.', $s);
            return floatval($s);
        }
        // se contém ponto apenas (ex: 1234.56)
        if (strpos($s, '.') !== false) {
            return floatval($s);
        }
        // caso seja apenas dígitos sem separador, tratar como centavos
        $digits = preg_replace('/\D+/', '', $s);
        if ($digits === '')
            return 0;
        return intval($digits) / 100.0;
    }

    public function index()
    {
        $stmt = $this->projectModel->read();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $projects;
    }

    public function show($id)
    {
        $this->projectModel->id = $id;
        $this->projectModel->readOne();
        return $this->projectModel;
    }

    public function create($data)
    {
        // normalizar valores monetários que podem vir com máscara (ex: "R$ 1.234,56")
        if (isset($data['maintenance_monthly_value'])) {
            $data['maintenance_monthly_value'] = $this->parseCurrency($data['maintenance_monthly_value']);
        }
        if (isset($data['total_budget'])) {
            $data['total_budget'] = $this->parseCurrency($data['total_budget']);
        }

        // Validação servidor: se houver manutenção, validar datas e duração
        if (!empty($data['maintenance_start_date'])) {
            // maintenance must start after project end
            if (strtotime($data['maintenance_start_date']) <= strtotime($data['end_date'])) {
                return ["success" => false, "message" => "Data de início da manutenção deve ser posterior ao término do projeto."];
            }
            if (!empty($data['maintenance_end_date'])) {
                // calcular meses de manutenção (inclusivo)
                $d1 = new DateTime($data['maintenance_start_date']);
                $d2 = new DateTime($data['maintenance_end_date']);
                if ($d2 < $d1) {
                    return ["success" => false, "message" => "Data de término da manutenção deve ser posterior à data de início da manutenção."];
                }
                $years = intval($d2->format('Y')) - intval($d1->format('Y'));
                $months = intval($d2->format('m')) - intval($d1->format('m'));
                $monthsTotal = $years * 12 + $months;
                // se dia do fim for >= dia do início, contar mês completo
                if (intval($d2->format('d')) >= intval($d1->format('d'))) {
                    $monthsTotal += 1;
                }
                if ($monthsTotal < 6 || $monthsTotal > 12) {
                    return ["success" => false, "message" => "A manutenção deve ter duração mínima de 6 meses e máxima de 12 meses."];
                }
            }
            if (empty($data['maintenance_monthly_value']) || floatval($data['maintenance_monthly_value']) <= 0) {
                return ["success" => false, "message" => "Valor mensal da manutenção deve ser informado e maior que zero."];
            }
        }
        $this->projectModel->customer_id = (isset($data['customer_id']) && $data['customer_id'] !== '') ? $data['customer_id'] : null;
        $this->projectModel->project_name = $data['project_name'];
        $this->projectModel->project_type = $data['project_type'];
        $this->projectModel->description = $data['description'];
        $this->projectModel->start_date = $data['start_date'];
        $this->projectModel->end_date = $data['end_date'];
        $this->projectModel->maintenance_start_date = isset($data['maintenance_start_date']) ? $data['maintenance_start_date'] : null;
        $this->projectModel->maintenance_end_date = isset($data['maintenance_end_date']) ? $data['maintenance_end_date'] : null;
        $this->projectModel->maintenance_monthly_value = isset($data['maintenance_monthly_value']) ? $data['maintenance_monthly_value'] : 0;
        $this->projectModel->payment_received_date = isset($data['payment_received_date']) ? $data['payment_received_date'] : null;
        $this->projectModel->total_budget = $data['total_budget'];
        $this->projectModel->status = $data['status'];

        if ($this->projectModel->create()) {
            $projectId = $this->projectModel->getLastInsertId();

            // Salvar credenciais se fornecidas
            if (isset($data['credentials']) && is_array($data['credentials'])) {
                $this->saveCredentials($projectId, $data['credentials']);
            }

            return ["success" => true, "message" => "Projeto criado com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao criar projeto."];
    }

    public function update($id, $data)
    {
        // normalizar valores monetários que podem vir com máscara
        if (isset($data['maintenance_monthly_value'])) {
            $data['maintenance_monthly_value'] = $this->parseCurrency($data['maintenance_monthly_value']);
        }
        if (isset($data['total_budget'])) {
            $data['total_budget'] = $this->parseCurrency($data['total_budget']);
        }

        // Validação servidor: se houver manutenção, validar datas e duração
        if (!empty($data['maintenance_start_date'])) {
            if (strtotime($data['maintenance_start_date']) <= strtotime($data['end_date'])) {
                return ["success" => false, "message" => "Data de início da manutenção deve ser posterior ao término do projeto."];
            }
            if (!empty($data['maintenance_end_date'])) {
                $d1 = new DateTime($data['maintenance_start_date']);
                $d2 = new DateTime($data['maintenance_end_date']);
                if ($d2 < $d1) {
                    return ["success" => false, "message" => "Data de término da manutenção deve ser posterior à data de início da manutenção."];
                }
                $years = intval($d2->format('Y')) - intval($d1->format('Y'));
                $months = intval($d2->format('m')) - intval($d1->format('m'));
                $monthsTotal = $years * 12 + $months;
                if (intval($d2->format('d')) >= intval($d1->format('d'))) {
                    $monthsTotal += 1;
                }
                if ($monthsTotal < 6 || $monthsTotal > 12) {
                    return ["success" => false, "message" => "A manutenção deve ter duração mínima de 6 meses e máxima de 12 meses."];
                }
            }
            if (empty($data['maintenance_monthly_value']) || floatval($data['maintenance_monthly_value']) <= 0) {
                return ["success" => false, "message" => "Valor mensal da manutenção deve ser informado e maior que zero."];
            }
        }
        $this->projectModel->id = $id;
        $this->projectModel->customer_id = (isset($data['customer_id']) && $data['customer_id'] !== '') ? $data['customer_id'] : null;
        $this->projectModel->project_name = $data['project_name'];
        $this->projectModel->project_type = $data['project_type'];
        $this->projectModel->description = $data['description'];
        $this->projectModel->observation = isset($data['observation']) ? $data['observation'] : '';
        $this->projectModel->start_date = $data['start_date'];
        $this->projectModel->end_date = $data['end_date'];
        $this->projectModel->maintenance_start_date = isset($data['maintenance_start_date']) ? $data['maintenance_start_date'] : null;
        $this->projectModel->maintenance_end_date = isset($data['maintenance_end_date']) ? $data['maintenance_end_date'] : null;
        $this->projectModel->maintenance_monthly_value = isset($data['maintenance_monthly_value']) ? $data['maintenance_monthly_value'] : 0;
        $this->projectModel->payment_received_date = isset($data['payment_received_date']) ? $data['payment_received_date'] : null;
        $this->projectModel->total_budget = $data['total_budget'];
        $this->projectModel->status = $data['status'];

        if ($this->projectModel->update()) {

            // Atualizar credenciais se fornecidas
            if (isset($data['credentials']) && is_array($data['credentials'])) {
                $this->saveCredentials($id, $data['credentials']);
            }

            return ["success" => true, "message" => "Projeto atualizado com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao atualizar projeto."];
    }

    public function delete($id)
    {
        $this->projectModel->id = $id;
        if ($this->projectModel->delete()) {
            return ["success" => true, "message" => "Projeto excluído com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao excluir projeto."];
    }

    public function getSummary()
    {
        return $this->projectModel->getProjectSummary();
    }

    public function getStatusReport()
    {
        $stmt = $this->projectModel->getProjectsByStatus();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCredentials($projectId)
    {
        return $this->projectModel->getCredentials($projectId);
    }


    public function saveCredentials($projectId, $credentials)
    {
        // Limpar credenciais antigas (estratégia simples replace-all)
        $this->projectModel->deleteCredentials($projectId);

        foreach ($credentials as $cred) {
            // Validar campos mínimos
            if (empty($cred['access_type']))
                continue;

            $this->projectModel->addCredential($projectId, $cred);
        }
        return true;
    }
}
