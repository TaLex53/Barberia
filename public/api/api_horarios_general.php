<?php
require_once '../../app/config/conexion.php';
date_default_timezone_set('America/Santiago');
header('Content-Type: application/json');

$fecha = $_GET['fecha'] ?? null; // format YYYY-MM-DD

if (!$fecha) {
    echo json_encode([]);
    exit;
}

try {
    // Get all active horarios
    $stmt = $pdo->query("SELECT * FROM horarios ORDER BY hora ASC");
    $all_horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total number of active barbers
    $stmt_barberos = $pdo->query("SELECT COUNT(*) FROM barberos WHERE activo = 1");
    $total_barberos = (int)$stmt_barberos->fetchColumn();

    // If no barbers active, no slots available
    if ($total_barberos === 0) {
        echo json_encode([]);
        exit;
    }

    // Get count of occupied appointments per horario for this date
    $stmt2 = $pdo->prepare("SELECT horario_id, COUNT(*) as ocupados FROM citas WHERE fecha_cita = :fecha AND estado != 'Cancelada' GROUP BY horario_id");
    $stmt2->execute(['fecha' => $fecha]);
    $occupied_counts = [];
    while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $occupied_counts[$row['horario_id']] = (int)$row['ocupados'];
    }

    $disponibles = [];
    $is_today = ($fecha === date('Y-m-d'));
    $current_time = date('H:i:s');

    foreach ($all_horarios as $h) {
        // If it's today and the hour is already past, skip it
        if ($is_today && $h['hora'] <= $current_time) {
            continue;
        }

        $ocupados = $occupied_counts[$h['id']] ?? 0;
        
        // Is it completely occupied? (If appointments >= total barbers)
        $is_occupied = ($ocupados >= $total_barberos);

        $timeObj = DateTime::createFromFormat('H:i:s', $h['hora']);
        $disponibles[] = [
            'id' => $h['id'],
            'hora' => $timeObj->format('H:i'),
            'hora_display' => $timeObj->format('g:i a'),
            'turno' => $h['turno'],
            'ocupado' => $is_occupied
        ];
    }

    echo json_encode($disponibles);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
