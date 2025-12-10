<?php
class CredentialController
{
    private $credentialModel;

    public function __construct($db)
    {
        $this->credentialModel = new Credential($db);
    }

    public function create($data)
    {
        $this->credentialModel->project_id = $data['project_id'];
        $this->credentialModel->access_type = $data['access_type'];
        $this->credentialModel->server_url = $data['server_url'];
        $this->credentialModel->username = $data['username'];
        $this->credentialModel->password = $data['password'];
        $this->credentialModel->port = $data['port'] ?? '';
        $this->credentialModel->notes = $data['notes'] ?? '';

        if ($this->credentialModel->create()) {
            return ["success" => true, "message" => "Credencial registrada com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao registrar credencial."];
    }

    public function getByProjectId($projectId)
    {
        $this->credentialModel->project_id = $projectId;
        $stmt = $this->credentialModel->readByProjectId();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($data)
    {
        $this->credentialModel->id = $data['id'];
        $this->credentialModel->access_type = $data['access_type'];
        $this->credentialModel->server_url = $data['server_url'];
        $this->credentialModel->username = $data['username'];
        $this->credentialModel->password = $data['password'];
        $this->credentialModel->port = $data['port'] ?? '';
        $this->credentialModel->notes = $data['notes'] ?? '';

        if ($this->credentialModel->update()) {
            return ["success" => true, "message" => "Credencial atualizada com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao atualizar credencial."];
    }

    public function delete($id)
    {
        $this->credentialModel->id = $id;

        if ($this->credentialModel->delete()) {
            return ["success" => true, "message" => "Credencial removida com sucesso!"];
        }
        return ["success" => false, "message" => "Erro ao remover credencial."];
    }

    public function getById($id)
    {
        $this->credentialModel->id = $id;
        return $this->credentialModel->getById();
    }
}
