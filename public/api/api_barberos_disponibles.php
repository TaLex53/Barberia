<?php
require_once '../../app/config/conexion.php';
date_default_timezone_set('America/Santiago');
header('Content-Type: application/json');

$fecha = $_GET['fecha'] ?? null;
$horario_id = $_GET['horario_id'] ?? null;

if (!$fecha || !$horario_id) {
    echo json_encode([]);
    exit;
}

try {
    // Get all active barbers
    $stmt_barberos = $pdo->query("SELECT id FROM barberos WHERE activo = 1");
    $all_barberos = $stmt_barberos->fetchAll(PDO::FETCH_COLUMN);

    // Get barbers that already have an appointment at this time
    $stmt2 = $pdo->prepare("SELECT barbero_id FROM citas WHERE fecha_cita = :fecha AND horario_id = :horario_id AND estado != 'Cancelada'");
    $stmt2->execute(['fecha' => $fecha, 'horario_id' => $horario_id]);
    $occupied_barberos = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // Find difference (available barbers)
    $disponibles = array_diff($all_barberos, $occupied_barberos);

    // Return as array of values (to reset keys)
    echo json_encode(array_values($disponibles));

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
