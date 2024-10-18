<?php
session_start();
require_once 'db_connection.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$task = null;

// Obtener la tarea a editar
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if (!$task) {
        header("Location: dashboard.php");
        exit();
    }
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    // Validar los datos de entrada
    if (empty($title) || empty($priority) || empty($status)) {
        $error = 'Por favor, completa todos los campos requeridos.';
    } else {
        // Actualizar la tarea en la base de datos
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$title, $description, $priority, $status, $task['id'], $_SESSION['user_id']])) {
            $success = 'Tarea actualizada con éxito.';
            // Actualizar la tarea en la variable local
            $task = [
                'id' => $task['id'],
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'status' => $status
            ];
        } else {
            $error = 'Error al actualizar la tarea. Por favor, intenta de nuevo.';
        }
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
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($task): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="priority" class="form-label">Prioridad</label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="baja" <?php echo $task['priority'] == 'baja' ? 'selected' : ''; ?>>Baja</option>
                        <option value="media" <?php echo $task['priority'] == 'media' ? 'selected' : ''; ?>>Media</option>
                        <option value="alta" <?php echo $task['priority'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="pendiente" <?php echo $task['status'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en progreso" <?php echo $task['status'] == 'en progreso' ? 'selected' : ''; ?>>En Progreso</option>
                        <option value="completada" <?php echo $task['status'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
            </form>
        <?php else: ?>
            <p>La tarea no existe o no tienes permiso para editarla.</p>
            <a href="dashboard.php" class="btn btn-primary">Volver al Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>