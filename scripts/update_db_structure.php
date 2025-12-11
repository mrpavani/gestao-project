<?php
require_once __DIR__ . '/../config/database.php';

echo "Iniciando atualização do banco de dados...\n";

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Atualizar ENUM da tabela projects
    // Nota: 'site_manutencao' -> 'desenvolvimento_sustentacao'
    //       'site_apenas' -> 'desenvolvimento'
    //       'manutencao' -> 'sustentacao' (mas este já existe como 'manutencao', vamos renomear conceito ou manter mapeamento?)
    // O usuário pediu: "desenvolvimento + sustentação", "desenvolvimento", "sustentação".
    // Vou alterar a coluna para aceitar os novos valores.

    // Primeiro, vamos alterar a coluna para aceitar qualquer valor (temporariamente ou apenas expandir o ENUM)
    // MySQL permite alterar ENUM.

    // Vou adicionar os novos valores ao ENUM existente para não quebrar dados antigos, 
    // ou converter os dados antigos?
    // Melhor: Adicionar novos valores. 
    // ENUM('site_manutencao', 'site_apenas', 'manutencao', 'desenvolvimento_sustentacao', 'desenvolvimento', 'sustentacao')

    $sql = "ALTER TABLE projects MODIFY COLUMN project_type ENUM('site_manutencao', 'site_apenas', 'manutencao', 'desenvolvimento_sustentacao', 'desenvolvimento', 'sustentacao') NOT NULL";
    $db->exec($sql);
    echo "ENUM atualizado com novos valores.\n";

    // Opcional: Migrar dados antigos para novos tipos?
    // 'site_manutencao' => 'desenvolvimento_sustentacao'
    // 'site_apenas' => 'desenvolvimento'
    // 'manutencao' => 'sustentacao'

    $db->exec("UPDATE projects SET project_type = 'desenvolvimento_sustentacao' WHERE project_type = 'site_manutencao'");
    $db->exec("UPDATE projects SET project_type = 'desenvolvimento' WHERE project_type = 'site_apenas'");
    $db->exec("UPDATE projects SET project_type = 'sustentacao' WHERE project_type = 'manutencao'");
    echo "Dados antigos migrados para novos tipos.\n";

    // Agora podemos restringir o ENUM apenas para os novos valores (opcional, mas limpo)
    // $sql = "ALTER TABLE projects MODIFY COLUMN project_type ENUM('desenvolvimento_sustentacao', 'desenvolvimento', 'sustentacao') NOT NULL";
    // $db->exec($sql);

    // 2. Garantir tabela de credenciais
    $sqlCreds = "CREATE TABLE IF NOT EXISTS project_access_credentials (
        id INT PRIMARY KEY AUTO_INCREMENT,
        project_id INT NOT NULL,
        access_type VARCHAR(100) NOT NULL COMMENT 'ex: FTP, SSH, Database, CMS, etc',
        server_url VARCHAR(255),
        username VARCHAR(255),
        password VARCHAR(255),
        port INT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
    )";
    $db->exec($sqlCreds);
    echo "Tabela project_access_credentials verificada/criada.\n";

    echo "Atualização concluída com sucesso!\n";

} catch (PDOException $e) {
    echo "Erro durante atualização: " . $e->getMessage() . "\n";
}
