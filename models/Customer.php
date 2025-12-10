<?php

class Customer
{
    private $conn;
    private $table = 'customers';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all customers
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $result = $this->conn->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get customer by ID
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Create new customer
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . "
                SET name = :name,
                    phone = :phone,
                    email = :email,
                    observations = :observations";

        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':observations', $data['observations']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Cliente cadastrado com sucesso!',
                'id' => $this->conn->lastInsertId()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao cadastrar cliente.'
            ];
        }
    }

    // Update customer
    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table . "
                SET name = :name,
                    phone = :phone,
                    email = :email,
                    observations = :observations
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':observations', $data['observations']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Cliente atualizado com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao atualizar cliente.'
            ];
        }
    }

    // Delete customer
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Cliente removido com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao remover cliente.'
            ];
        }
    }

    // Get customer count
    public function getCount()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $result = $this->conn->query($query);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Get customer projects count
    public function getProjectsCount($customer_id)
    {
        $query = "SELECT COUNT(*) as count FROM projects WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}
