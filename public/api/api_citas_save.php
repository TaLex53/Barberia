<?php
session_start();
require_once '../../app/config/conexion.php';

header('Content-Type: application/json');

// Public endpoint for bookings

// Receive JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

// Extract variables
$nombre = trim($data['nombre'] ?? '');
$apellido = trim($data['apellido'] ?? '');
$email = trim($data['email'] ?? '');
$telefono = trim($data['telefono'] ?? '');
$observaciones = trim($data['observaciones'] ?? '');

$barbero_id = $data['barbero_id'] ?? null;
$servicio_id = $data['servicio_id'] ?? null;
$fecha = $data['fecha'] ?? null;
$horario_id = $data['horario_id'] ?? null;

// Basic validation
if (!$nombre || !$apellido || !$email || !$telefono || !$barbero_id || !$servicio_id || !$fecha || !$horario_id) {
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Check if client exists by email, if not create
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        $cliente_id = $cliente['id'];
        // Optional: Update client info here if we want to keep it fresh
        $updateStmt = $pdo->prepare("UPDATE clientes SET nombre=:nombre, apellido=:apellido, telefono=:telefono, observaciones=:obs WHERE id=:id");
        $updateStmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono,
            'obs' => $observaciones,
            'id' => $cliente_id
        ]);
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO clientes (nombre, apellido, email, telefono, observaciones) VALUES (:nombre, :apellido, :email, :telefono, :obs)");
        $insertStmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'telefono' => $telefono,
            'obs' => $observaciones
        ]);
        $cliente_id = $pdo->lastInsertId();
    }

    // 2. Check if the timeslot is already booked for that barbero on that date
    $checkSlot = $pdo->prepare("SELECT id FROM citas WHERE barbero_id = :barbero_id AND fecha_cita = :fecha AND horario_id = :horario_id AND estado != 'Cancelada'");
    $checkSlot->execute([
        'barbero_id' => $barbero_id,
        'fecha' => $fecha,
        'horario_id' => $horario_id
    ]);
    if ($checkSlot->fetch()) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'El barbero ya tiene una cita en ese horario']);
        exit;
    }

    // 3. Create Cita
    $citaStmt = $pdo->prepare("INSERT INTO citas (cliente_id, barbero_id, servicio_id, fecha_cita, horario_id, estado) VALUES (:cliente_id, :barbero_id, :servicio_id, :fecha, :horario_id, 'Agendada')");
    $citaStmt->execute([
        'cliente_id' => $cliente_id,
        'barbero_id' => $barbero_id,
        'servicio_id' => $servicio_id,
        'fecha' => $fecha,
        'horario_id' => $horario_id
    ]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
}
?>
