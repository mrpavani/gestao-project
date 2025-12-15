<?php
require_once __DIR__ . '/../models/Document.php';

class DocumentController
{
    private $db;
    private $document;

    public function __construct($db)
    {
        $this->db = $db;
        $this->document = new Document($db);
    }

    public function upload($projectId, $file)
    {
        // Validar arquivo
        $allowedTypes = ['application/pdf'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Apenas arquivos PDF são permitidos.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'O arquivo é muito grande (Máx: 10MB).'];
        }

        // Criar diretório se não existir
        $uploadDir = __DIR__ . '/../uploads/documents/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Gerar nome único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->document->project_id = $projectId;
            $this->document->file_name = $file['name'];
            $this->document->file_path = 'uploads/documents/' . $uniqueName; // Caminho relativo para salvar no banco

            if ($this->document->create()) {
                return ['success' => true, 'message' => 'Documento enviado com sucesso!'];
            } else {
                unlink($targetPath); // Remove arquivo se falhar no banco
                return ['success' => false, 'message' => 'Erro ao salvar informações do documento.'];
            }
        }

        return ['success' => false, 'message' => 'Erro ao fazer upload do arquivo.'];
    }

    public function delete($id)
    {
        $this->document->id = $id;
        if ($this->document->delete()) {
            return ['success' => true, 'message' => 'Documento excluído com sucesso!'];
        }
        return ['success' => false, 'message' => 'Erro ao excluir documento.'];
    }

    public function getByProject($projectId)
    {
        return $this->document->readByProject($projectId);
    }
}
