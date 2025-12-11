<?php
require_once __DIR__ . '/../config/database.php';

echo "Iniciando migração: Datas opcionais e novo status...\n";

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Make start_date and end_date nullable
    echo "Alterando start_date para nullable...\n";
    $db->exec("ALTER TABLE projects MODIFY COLUMN start_date DATE NULL");

    echo "Alterando end_date para nullable...\n";
    $db->exec("ALTER TABLE projects MODIFY COLUMN end_date DATE NULL");

    // 2. Add new status 'nao_aprovado' to ENUM
    echo "Adicionando status 'nao_aprovado' ao ENUM...\n";
    $db->exec("ALTER TABLE projects MODIFY COLUMN status ENUM('planejamento', 'em_andamento', 'concluido', 'atrasado', 'cancelado', 'nao_aprovado') DEFAULT 'planejamento'");

    echo "Migração concluída com sucesso!\n";

} catch (PDOException $e) {
    echo "Erro durante migração: " . $e->getMessage() . "\n";
}
