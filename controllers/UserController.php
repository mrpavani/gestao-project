<?php

require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
        $this->userModel->createTableIfNotExists();
    }

    public function register($username, $password, $role = 'user')
    {
        return $this->userModel->create($username, $password, $role);
    }

    public function authenticate($username, $password)
    {
        $user = $this->userModel->getByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function index()
    {
        return $this->userModel->getAll();
    }

    public function delete($id)
    {
        return $this->userModel->delete($id);
    }
}
