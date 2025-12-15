<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$sql = "
-- Tornar datas opcionais
ALTER TABLE projects MODIFY COLUMN start_date DATE NULL;
ALTER TABLE projects MODIFY COLUMN end_date DATE NULL;

-- Criar tabela de documentos
CREATE TABLE IF NOT EXISTS project_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
";

try {
    $db->exec($sql);
    echo "Migration executed successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
