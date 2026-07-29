<?php
// Detección automática del entorno (Local XAMPP vs Servidor en Vivo cPanel)
if (strpos(__DIR__, 'xampp') !== false || strpos(__DIR__, 'C:') !== false || (isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']))) {
    // Entorno Local (XAMPP)
    $host = '127.0.0.1';
    $db   = 'cutlevel_barber';
    $user = 'root';
    $pass = '';
} else {
    // Entorno Producción (cPanel / Hosting)
    $host = 'localhost';
    $db   = 'cutlevel_barber';
    $user = 'cutlevel_cutlevel';
    $pass = 'level2026%!';
}
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
