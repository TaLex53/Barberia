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
    $hora = trim($_POST['hora'] ?? '');
    $turno = trim($_POST['turno'] ?? '');

    try {
        if ($accion === 'crear') {
            $stmt = $pdo->prepare("INSERT INTO horarios (hora, turno) VALUES (:hora, :turno)");
            $stmt->execute([
                'hora' => $hora,
                'turno' => $turno
            ]);
            $_SESSION['success_msg'] = "Horario creado exitosamente.";
        } 
        elseif ($accion === 'editar') {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE horarios SET hora = :hora, turno = :turno WHERE id = :id");
                $stmt->execute([
                    'hora' => $hora,
                    'turno' => $turno,
                    'id' => $id
                ]);
                $_SESSION['success_msg'] = "Horario actualizado exitosamente.";
            }
        } 
        elseif ($accion === 'eliminar') {
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM horarios WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $_SESSION['success_msg'] = "Horario eliminado exitosamente.";
            }
        }
    } catch (\PDOException $e) {
        $_SESSION['error_msg'] = "Error en la base de datos: " . $e->getMessage();
    }

    header("Location: horarios");
    exit;
} else {
    header("Location: horarios");
    exit;
}
?>
