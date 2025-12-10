<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/UserController.php';

$database = new Database();
$db = $database->getConnection();
$uc = new UserController($db);

$username = $argv[1] ?? 'admin';
$password = $argv[2] ?? 'admin123';
$role = $argv[3] ?? 'admin';

$res = $uc->register($username, $password, $role);
if ($res['success']) {
    echo "User created: ID=" . $res['id'] . " username={$username}\n";
} else {
    echo "Failed to create user\n";
}
