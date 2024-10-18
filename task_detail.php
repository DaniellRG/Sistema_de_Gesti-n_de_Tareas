<?php
session_start();
require_once 'db_connection.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se proporcionó un ID de tarea
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = $_GET['id'];

// Obtener los detalles de la tarea
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch();

// Si la tarea no existe o no pertenece al usuario actual, redirigir al dashboard
if (!$task) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Tarea - Sistema de Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Detalles de la Tarea</h1>
        <div class="card">
            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($task['title']) ?></h2>
                <p class="card-text"><strong>Descripción:</strong> <?= htmlspecialchars($task['description']) ?></p>
                <p class="card-text"><strong>Prioridad:</strong> <?= htmlspecialchars($task['priority']) ?></p>
                <p class="card-text"><strong>Estado:</strong> <?= htmlspecialchars($task['status']) ?></p>
                <p class="card-text"><strong>Fecha de creación:</strong> <?= htmlspecialchars($task['created_at']) ?></p>
                <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-primary">Editar Tarea</a>
                <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>