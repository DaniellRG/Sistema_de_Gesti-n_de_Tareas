-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS task_management;

-- Usar la base de datos
USE task_management;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear la tabla de tareas
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('baja', 'media', 'alta') NOT NULL,
    status ENUM('pendiente', 'en progreso', 'completada') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertar un usuario de prueba
INSERT INTO users (name, email, password) VALUES ('Usuario de Prueba', 'test@example.com', 'password123');

-- Insertar algunas tareas de prueba
INSERT INTO tasks (user_id, title, description, priority, status) VALUES
(1, 'Completar informe', 'Finalizar el informe mensual de ventas', 'alta', 'pendiente'),
(1, 'Reunión con cliente', 'Preparar presentación para la reunión', 'media', 'en progreso'),
(1, 'Actualizar sitio web', 'Actualizar la sección de productos en el sitio web', 'baja', 'completada');