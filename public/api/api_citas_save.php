<?php
session_start();
require_once '../../app/config/conexion.php';
require_once '../../app/config/env.php';

// Load .env
loadEnv(__DIR__ . '/../../.env');

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
$servicios = $data['servicios'] ?? [];
$fecha = $data['fecha'] ?? null;
$horario_id = $data['horario_id'] ?? null;

// Basic validation
if (!$nombre || !$apellido || !$email || !$telefono || !$barbero_id || empty($servicios) || !$fecha || !$horario_id) {
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
    
    $first_cita_id = null;
    foreach ($servicios as $srv) {
        $citaStmt->execute([
            'cliente_id' => $cliente_id,
            'barbero_id' => $barbero_id,
            'servicio_id' => $srv['id'],
            'fecha' => $fecha,
            'horario_id' => $horario_id
        ]);
        if (!$first_cita_id) {
            $first_cita_id = $pdo->lastInsertId();
        }
    }

    $pdo->commit();

    // --- Fetch details for email ---
    $stmtBarbero = $pdo->prepare("SELECT nombre FROM barberos WHERE id = :id");
    $stmtBarbero->execute(['id' => $barbero_id]);
    $barberoNombre = $stmtBarbero->fetchColumn() ?: 'Barbero';

    $servicioNombres = array_map(function($s) { return $s['name']; }, $servicios);
    $servicioNombre = implode(' + ', $servicioNombres);

    $stmtHorario = $pdo->prepare("SELECT hora FROM horarios WHERE id = :id");
    $stmtHorario->execute(['id' => $horario_id]);
    $horarioHora = $stmtHorario->fetchColumn() ?: '00:00';

    // Format date in Spanish manually
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $timestamp = strtotime($fecha);
    $fechaFormateada = $dias[date('w', $timestamp)] . ', ' . date('d', $timestamp) . ' de ' . $meses[date('n', $timestamp) - 1] . ' de ' . date('Y', $timestamp);
    $horaFormateada = substr($horarioHora, 0, 5); // 00:00

    $reservaId = str_pad($cliente_id, 6, "0", STR_PAD_LEFT);
    $cita_id = $first_cita_id;
    if (!$cita_id) $cita_id = rand(100000, 999999);
    $numeroReserva = str_pad($cita_id, 8, "0", STR_PAD_LEFT);

    // --- Send Email via Resend API using cURL ---
    $resendApiKey = getenv('RESEND_API_KEY');
    if ($resendApiKey && $email) {
        $htmlTemplate = '
        <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; background-color: #f5f5f5; padding-bottom: 40px; text-align: center;">
            
            <!-- Top White Section (Header + Title) -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto; background-color: #ffffff; width: 100%; max-width: 600px; text-align: left;">
                <tr>
                    <td style="padding: 20px; border-bottom: 1px solid #eaeaea;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="font-weight: bold; font-size: 20px; color: #333;">
                                    <img src="https://cutlevelstudio.cl/assets/img/cutlevel.png" alt="Cut Level" style="max-height: 25px; display: block;">
                                </td>
                                <td align="right">
                                    <a href="https://cutlevelstudio.cl/reserva" style="color: #666; text-decoration: underline; font-size: 14px;">Ir a Reservar</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px 20px; text-align: center;">
                        <h1 style="margin: 0; font-size: 24px; color: #333; font-weight: 700;">Cut Level</h1>
                        <p style="margin: 10px 0 0 0; color: #666; font-size: 15px;">' . htmlspecialchars($nombre . ' ' . $apellido) . ', tu reserva fue recibida exitosamente.</p>
                    </td>
                </tr>
            </table>

            <!-- Gray Area Content -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto; width: 100%; max-width: 600px; text-align: left;">
                <tr><td height="25"></td></tr>
                
                <!-- Reservation Header Data -->
                <tr>
                    <td style="padding: 0 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="55" valign="middle">
                                    <div style="background-color: #1a1a1a; width: 44px; height: 44px; border-radius: 50%; display: table-cell; vertical-align: middle; text-align: center;">
                                        <img src="https://img.icons8.com/ios/50/ffffff/calendar--v1.png" width="22" style="display: block; margin: 0 auto;">
                                    </div>
                                </td>
                                <td valign="middle">
                                    <h2 style="margin: 0 0 4px 0; font-size: 18px; color: #333; font-weight: 500;">Datos de la reserva</h2>
                                    <div style="color: #777; font-size: 13px;">Reserva #' . $numeroReserva . '</div>
                                </td>
                                <td align="right" valign="middle">
                                    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; padding: 6px 14px; border-radius: 20px; font-size: 13px; color: #555; display: inline-block;">
                                        <img src="https://img.icons8.com/ios/50/666666/iphone.png" width="14" style="vertical-align: middle; margin-right: 4px; margin-bottom: 2px;">
                                        <a href="tel:+56920860076" style="color: #555; text-decoration: none;">+56 9 2086 0076</a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td height="20"></td></tr>
                
                <!-- Details Card -->
                <tr>
                    <td style="padding: 0 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <tr>
                                <td style="padding: 25px;">
                                    <h3 style="margin: 0 0 8px 0; font-size: 17px; color: #333; font-weight: 600;">' . htmlspecialchars($servicioNombre) . '</h3>
                                    <p style="margin: 0 0 20px 0; color: #666; font-size: 15px;">' . $fechaFormateada . ' a las ' . $horaFormateada . ' horas</p>
                                    
                                    <hr style="border: 0; border-top: 1px solid #eaeaea; margin-bottom: 20px;">
                                    
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="30" style="padding-bottom: 12px;">
                                                <img src="https://img.icons8.com/ios/50/666666/marker--v1.png" width="18" style="display: block;">
                                            </td>
                                            <td style="padding-bottom: 12px; color: #555; font-size: 14px;">Av. Colón 0600, Puerto Varas</td>
                                        </tr>
                                        <tr>
                                            <td width="30">
                                                <img src="https://img.icons8.com/ios/50/666666/user--v1.png" width="18" style="display: block;">
                                            </td>
                                            <td style="color: #555; font-size: 14px;">' . htmlspecialchars($barberoNombre) . '</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td height="20"></td></tr>
                
                <!-- Notes Card -->
                <tr>
                    <td style="padding: 0 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <tr>
                                <td style="padding: 25px;">
                                    <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #777; font-weight: normal;">NOTAS</h4>
                                    <p style="margin: 0 0 16px 0; color: #555; font-size: 14px; line-height: 1.6;">Información importante para tu <span style="background-color: #fce79a; padding: 2px 6px; border-radius: 3px; color: #333;">cita</span>: - Muchas gracias por agendar con nosotros y vivir nuestra experiencia</p>
                                    <p style="margin: 0 0 16px 0; color: #555; font-size: 14px; line-height: 1.6;">- Pasado 15 minutos de la hora de reserva nos permitimos cancelar la hora tomada</p>
                                    <p style="margin: 0; color: #555; font-size: 14px; line-height: 1.6;">- Nos vemos!</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>';

        $mailData = [
            'from' => 'Cut Level <contacto@cutlevelstudio.cl>',
            'to' => $email, // The client's email
            'bcc' => 'contacto@cutlevelstudio.cl', // Also send to support
            'subject' => 'Confirmación de Reserva en Cut Level',
            'html' => $htmlTemplate
        ];
        
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mailData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $resendApiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
    }
    // --------------------------------------------

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
}
?>
