<?php
session_start();
require_once '../../app/config/conexion.php';

// Verificación de seguridad
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: barberiasettings");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $activo = isset($_POST['activo']) && $_POST['activo'] == '1' ? 1 : 0;

    // Lógica de subida de archivo
    $fotoPath = null;
    $uploadDir = '../uploads/barberos/';
    $dbPath = 'uploads/barberos/';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Sanitizar el nombre del archivo
        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES['foto']['name']));
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            $fotoPath = $dbPath . $filename;
        }
    }

    try {
        if ($accion === 'crear') {
            $stmt = $pdo->prepare("INSERT INTO barberos (nombre, apellido, foto, activo) VALUES (:nombre, :apellido, :foto, :activo)");
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'foto' => $fotoPath,
                'activo' => $activo
            ]);
            $_SESSION['success_msg'] = "Barbero creado exitosamente.";
        } 
        elseif ($accion === 'editar') {
            if ($id) {
                if ($fotoPath) {
                    $stmt = $pdo->prepare("UPDATE barberos SET nombre = :nombre, apellido = :apellido, foto = :foto, activo = :activo WHERE id = :id");
                    $stmt->execute([
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'foto' => $fotoPath,
                        'activo' => $activo,
                        'id' => $id
                    ]);
                } else {
                    $stmt = $pdo->prepare("UPDATE barberos SET nombre = :nombre, apellido = :apellido, activo = :activo WHERE id = :id");
                    $stmt->execute([
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'activo' => $activo,
                        'id' => $id
                    ]);
                }
                $_SESSION['success_msg'] = "Barbero actualizado exitosamente.";
            }
        } 
        elseif ($accion === 'eliminar') {
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM barberos WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $_SESSION['success_msg'] = "Barbero eliminado exitosamente.";
            }
        }
    } catch (\PDOException $e) {
        $_SESSION['error_msg'] = "Error en la base de datos: " . $e->getMessage();
    }

    // Redirigir de vuelta al panel
    header("Location: barberos");
    exit;
} else {
    // Si no es POST, redirigir
    header("Location: barberos");
    exit;
}
?>
