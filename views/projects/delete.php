<?php
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../controllers/ProjectController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $projectController->delete($id);

    if ($result['success']) {
        header("Location: ../../projects.php?deleted=1");
    } else {
        header("Location: ../../projects.php?error=" . urlencode($result['message']));
    }
} else {
    header("Location: ../../projects.php");
}
exit();
