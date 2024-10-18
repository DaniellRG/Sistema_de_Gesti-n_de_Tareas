<?php
session_start();
require_once 'db_connection.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$task_id) {
    header("Location: dashboard.php");
    exit();
}

// Obtener la tarea
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$title, $description, $priority, $status, $task_id, $_SESSION['user_id']])) {
        header("Location: task_detail.php?id=" . $task_id);
        exit();
    } else {
        $error = "Error al actualizar la tarea";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea - Sistema de Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Editar Tarea</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="priority" 

 class="form-label">Prioridad</label>
                <select class="form-select" id="priority" name="priority" required>
                    <option value="baja" <?= $task['priority'] == 'baja' ? 'selected' : '' ?>>Baja</option>
                    <option value="media" <?= $task['priority'] == 'media' ? 'selected' : '' ?>>Media</option>
                    <option value="alta" <?= $task['priority'] == 'alta' ? 'selected' : '' ?>>Alta</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pendiente" <?= $task['status'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="completada" <?= $task['status'] == 'completada' ? 'selected' : '' ?>>Completada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
            <a href="task_detail.php?id=<?= $task['id'] ?>" class="btn btn-secondary">Volver a Detalles</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>