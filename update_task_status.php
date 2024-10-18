<?php
session_start();
require_once 'db_connection.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Verificar si se recibieron los datos necesarios
if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$task_id = $_POST['task_id'];
$status = $_POST['status'];

// Actualizar el estado de la tarea
$stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
if ($stmt->execute([$status, $task_id, $_SESSION['user_id']])) {
    echo json_encode(['success' => true, 'message' => 'Estado de la tarea actualizado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la tarea']);
}