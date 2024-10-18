<?php
session_start();
require_once 'db_connection.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, priority, status, user_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $priority, $status, $user_id])) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error al crear la tarea";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nueva Tarea - Sistema de Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Añadir Nueva Tarea</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="priority" class="form-label">Prioridad</label>
                <select class="form-select" id="priority" name="priority" required>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="completada">Completada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear Tarea</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>