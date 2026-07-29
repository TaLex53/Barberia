<?php
session_start();
require_once '../../app/config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([]);
    exit;
}

$start = $_GET['start'] ?? null; // ISO 8601 date
$end = $_GET['end'] ?? null;     // ISO 8601 date

if (!$start || !$end) {
    echo json_encode([]);
    exit;
}

try {
    // Extract dates
    $startDate = substr($start, 0, 10);
    $endDate = substr($end, 0, 10);

    // Query to fetch citas with joined details
    $sql = "
        SELECT 
            c.id, 
            c.fecha_cita, 
            c.estado,
            h.hora as horario_inicio,
            s.duracion_minutos,
            s.nombre as servicio_nombre,
            b.nombre as barbero_nombre,
            cl.nombre as cliente_nombre,
            cl.apellido as cliente_apellido,
            cl.email as cliente_email,
            cl.telefono as cliente_telefono,
            cl.observaciones as cliente_observaciones
        FROM citas c
        JOIN horarios h ON c.horario_id = h.id
        JOIN servicios s ON c.servicio_id = s.id
        JOIN barberos b ON c.barbero_id = b.id
        JOIN clientes cl ON c.cliente_id = cl.id
        WHERE c.fecha_cita BETWEEN :start AND :end
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start' => $startDate, 'end' => $endDate]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = [];

    foreach ($citas as $c) {
        // Build FullCalendar event object
        $startDateTime = $c['fecha_cita'] . 'T' . $c['horario_inicio'];
        
        // Calculate end time by adding duracion_minutos to startDateTime
        $dt = new DateTime($startDateTime);
        $dt->add(new DateInterval('PT' . $c['duracion_minutos'] . 'M'));
        $endDateTime = $dt->format('Y-m-d\TH:i:s');

        // Color coding based on status (or you could do it by barber)
        $color = '#3b82f6'; // blue (Agendada)
        if ($c['estado'] === 'Completada') $color = '#10b981'; // green
        if ($c['estado'] === 'Cancelada') $color = '#ef4444'; // red

        $events[] = [
            'id' => $c['id'],
            'title' => $c['servicio_nombre'] . ' - ' . $c['cliente_nombre'],
            'start' => $startDateTime,
            'end' => $endDateTime,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'barbero' => $c['barbero_nombre'],
                'cliente' => $c['cliente_nombre'] . ' ' . $c['cliente_apellido'],
                'nombre' => $c['cliente_nombre'],
                'apellido' => $c['cliente_apellido'],
                'email' => $c['cliente_email'],
                'telefono' => $c['cliente_telefono'],
                'observaciones' => $c['cliente_observaciones'],
                'estado' => $c['estado']
            ]
        ];
    }

    echo json_encode($events);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
