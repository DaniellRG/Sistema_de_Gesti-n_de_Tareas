<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'task_management';
$username = 'tu_usuario';
$password = 'tu_contraseña';

// Opciones de PDO para manejar errores y establecer el modo de recuperación de datos
$options = [
    
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    // En caso de error, mostrar un mensaje y terminar el script
    die("Error de conexión: " . $e->getMessage());
}