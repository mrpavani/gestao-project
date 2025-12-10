<?php

// Load model using path relative to this controller file so includes work
require_once __DIR__ . '/../models/Customer.php';

class CustomerController
{
    private $customerModel;

    public function __construct($db)
    {
        $this->customerModel = new Customer($db);
    }

    // Get all customers
    public function index()
    {
        return $this->customerModel->getAll();
    }

    // Get customer by ID
    public function show($id)
    {
        return $this->customerModel->getById($id);
    }

    // Create customer
    public function create($data)
    {
        return $this->customerModel->create($data);
    }

    // Update customer
    public function update($id, $data)
    {
        return $this->customerModel->update($id, $data);
    }

    // Delete customer
    public function delete($id)
    {
        return $this->customerModel->delete($id);
    }

    // Get customer count
    public function getCount()
    {
        return $this->customerModel->getCount();
    }

    // Get customer projects count
    public function getProjectsCount($customer_id)
    {
        return $this->customerModel->getProjectsCount($customer_id);
    }
}
