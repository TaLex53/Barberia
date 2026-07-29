<?php
session_start();
require_once '../../app/config/conexion.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: barberiasettings");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $duracion = (int)($_POST['duracion_minutos'] ?? 45);
    $precio = (float)($_POST['precio'] ?? 0);
    $activo = isset($_POST['activo']) && $_POST['activo'] == '1' ? 1 : 0;

    try {
        if ($accion === 'crear') {
            $stmt = $pdo->prepare("INSERT INTO servicios (nombre, duracion_minutos, precio, activo) VALUES (:nombre, :duracion, :precio, :activo)");
            $stmt->execute([
                'nombre' => $nombre,
                'duracion' => $duracion,
                'precio' => $precio,
                'activo' => $activo
            ]);
            $_SESSION['success_msg'] = "Servicio creado exitosamente.";
        } 
        elseif ($accion === 'editar') {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE servicios SET nombre = :nombre, duracion_minutos = :duracion, precio = :precio, activo = :activo WHERE id = :id");
                $stmt->execute([
                    'nombre' => $nombre,
                    'duracion' => $duracion,
                    'precio' => $precio,
                    'activo' => $activo,
                    'id' => $id
                ]);
                $_SESSION['success_msg'] = "Servicio actualizado exitosamente.";
            }
        } 
        elseif ($accion === 'eliminar') {
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $_SESSION['success_msg'] = "Servicio eliminado exitosamente.";
            }
        }
    } catch (\PDOException $e) {
        $_SESSION['error_msg'] = "Error en la base de datos: " . $e->getMessage();
    }

    header("Location: servicios");
    exit;
} else {
    header("Location: servicios");
    exit;
}
?>
