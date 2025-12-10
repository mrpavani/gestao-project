<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ProjectController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../models/Project.php';

$database = new Database();
$db = $database->getConnection();
$pc = new ProjectController($db);

$data = [
    'project_name' => 'Teste Mascara CLI',
    'project_type' => 'site_apenas',
    'description' => 'Criado por script de teste',
    'start_date' => '2025-12-09',
    'end_date' => '2026-01-09',
    'customer_id' => null,
    'maintenance_start_date' => null,
    'maintenance_end_date' => null,
    'maintenance_monthly_value' => null,
    'payment_received_date' => null,
    'total_budget' => 'R$ 1.234,56',
    'status' => 'planejamento'
];

$result = $pc->create($data);
print_r($result);
// buscar último projeto inserido
$stmt = $db->query("SELECT id, project_name, total_budget, maintenance_monthly_value FROM projects ORDER BY id DESC LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($row);
