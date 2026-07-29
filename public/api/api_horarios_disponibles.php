<?php
require_once '../../app/config/conexion.php';
date_default_timezone_set('America/Santiago');
header('Content-Type: application/json');

$barbero_id = $_GET['barbero_id'] ?? null;
$fecha = $_GET['fecha'] ?? null; // format YYYY-MM-DD

if (!$barbero_id || !$fecha) {
    echo json_encode([]);
    exit;
}

try {
    // Get all active horarios
    $stmt = $pdo->query("SELECT * FROM horarios ORDER BY hora ASC");
    $all_horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get occupied horarios for this barbero and date
    $stmt2 = $pdo->prepare("SELECT horario_id FROM citas WHERE barbero_id = :barbero_id AND fecha_cita = :fecha AND estado != 'Cancelada'");
    $stmt2->execute(['barbero_id' => $barbero_id, 'fecha' => $fecha]);
    $occupied = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    $disponibles = [];
    $is_today = ($fecha === date('Y-m-d'));
    $current_time = date('H:i:s');

    foreach ($all_horarios as $h) {
        // If it's today and the hour is already past, skip it
        if ($is_today && $h['hora'] <= $current_time) {
            continue;
        }

        $timeObj = DateTime::createFromFormat('H:i:s', $h['hora']);
        $disponibles[] = [
            'id' => $h['id'],
            'hora' => $timeObj->format('H:i'), // format 24h for value
            'hora_display' => $timeObj->format('h:i A'), // format 12h for display
            'turno' => $h['turno'],
            'ocupado' => in_array($h['id'], $occupied)
        ];
    }

    echo json_encode($disponibles);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
