<?php
session_start();
require_once 'db_connection.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Manejar la eliminación de tareas
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    header("Location: dashboard.php");
    exit();
}

// Obtener los filtros y el orden
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Construir la consulta SQL con filtros y orden
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if ($priority_filter) {
    $sql .= " AND priority = ?";
    $params[] = $priority_filter;
}

if ($status_filter) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

// Agregar la cláusula ORDER BY
$sql .= " ORDER BY ";
switch ($sort) {
    case 'priority':
        $sql .= "FIELD(priority, 'alta', 'media', 'baja')";
        break;
    case 'status':
        $sql .= "FIELD(status, 'pendiente', 'completada')";
        break;
    default:
        $sql .= "created_at";
}
$sql .= " $order";

// Obtener las tareas del usuario con filtros y orden aplicados
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

// Función para generar enlaces de ordenamiento
function sortLink($field, $label, $currentSort, $currentOrder) {
    $newOrder = ($currentSort === $field && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    $params = $_GET;
    $params['sort'] = $field;
    $params['order'] = $newOrder;
    $url = '?' . http_build_query($params);
    $arrow = ($currentSort === $field) ? ($currentOrder === 'ASC' ? '↑' : '↓') : '';
    return "<a href='$url' class='btn btn-sm btn-outline-secondary'>$label $arrow</a>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Dashboard de Tareas</h1>
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="add_task.php" class="btn btn-primary">Añadir Nueva Tarea</a>
            </div>
            <div class="col-md-6 text-end">
                <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <form action="" method="get" class="d-flex">
                    <select name="priority" class="form-select me-2">
                        <option value="">Todas las prioridades</option>
                        <option value="baja" <?= $priority_filter == 'baja' ? 'selected' : '' ?>>Baja</option>
                        <option value="media" <?= $priority_filter == 'media' ? 'selected' : '' ?>>Media</option>
                        <option value="alta" <?= $priority_filter == 'alta' ? 'selected' : '' ?>>Alta</option>
                    </select>
                    <select name="status" class="form-select me-2">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?= $status_filter == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="completada" <?= $status_filter == 'completada' ? 'selected' : '' ?>>Completada</option>
                    </select>
                    <button type="submit" class="btn btn-secondary">Filtrar</button>
                </form>
            </div>
        </div>
        
        <div class="mb-3">
            <strong>Ordenar por:</strong>
            <?= sortLink('created_at', 'Fecha', $sort, $order) ?>
            <?= sortLink('priority', 'Prioridad', $sort, $order) ?>
            <?= sortLink('status', 'Estado', $sort, $order) ?>
        </div>
        
        <div class="row">
            <?php if (empty($tasks)): ?>
                <div class="col-12">
                    <p class="text-center">No se encontraron tareas con los filtros seleccionados.</p>
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="task_detail.php?id=<?= $task['id'] ?>"><?= htmlspecialchars($task['title']) ?></a>
                                </h5>
                                <p class="card-text"><?= htmlspecialchars($task['description']) ?></p>
                                <p class="card-text"><strong>Prioridad:</strong> <?= htmlspecialchars($task['priority']) ?></p>
                                <p class="card-text"><strong>Estado:</strong> <span id="status-<?= $task['id'] ?>"><?= htmlspecialchars($task['status']) ?></span></p>
                                <p class="card-text"><strong>Creada:</strong> <?= htmlspecialchars($task['created_at']) ?></p>
                                <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="dashboard.php?delete=<?= $task['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarea?')">Eliminar</a>
                                <?php if ($task['status'] == 'pendiente'): ?>
                                    <button class="btn btn-sm btn-success mark-complete" data-task-id="<?= $task['id'] ?>">Marcar como completada</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const markCompleteButtons = document.querySelectorAll('.mark-complete');
        
        markCompleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                
                fetch('update_task_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `task_id=${taskId}&status=completada`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusSpan = document.getElementById(`status-${taskId}`);
                        statusSpan.textContent = 'completada';
                        this.remove();
                    } else {
                        alert('Error al actualizar el estado de la tarea');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el estado de la tarea');
                });
            });
        });
    });
    </script>
</body>
</html>