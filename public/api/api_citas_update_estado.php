<?php
session_start();
require_once '../../app/config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$estado = $data['estado'] ?? null;

if (!$id || !$estado) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$allowed_states = ['Agendada', 'Completada', 'Cancelada'];
if (!in_array($estado, $allowed_states)) {
    echo json_encode(['success' => false, 'error' => 'Estado no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
    $stmt->execute([
        'estado' => $estado,
        'id' => $id
    ]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el estado: ' . $e->getMessage()]);
}
