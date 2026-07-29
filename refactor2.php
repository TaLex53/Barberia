<?php
// Refactor API files
$apiFiles = glob(__DIR__ . '/public/api/*.php');
foreach ($apiFiles as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $content = str_replace("require_once '../app/config/conexion.php'", "require_once '../../app/config/conexion.php'", $content);
        file_put_contents($file, $content);
        echo "Updated API $file\n";
    }
}

// Refactor Admin files
$adminFiles = glob(__DIR__ . '/public/admin/*.php');
foreach ($adminFiles as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $content = str_replace("require_once '../app/components/header.php'", "require_once '../../app/components/header.php'", $content);
        $content = str_replace("require_once '../app/components/footer.php'", "require_once '../../app/components/footer.php'", $content);
        $content = str_replace("include '../app/components/header.php'", "include '../../app/components/header.php'", $content);
        $content = str_replace("include '../app/components/footer.php'", "include '../../app/components/footer.php'", $content);
        $content = str_replace("require_once '../app/config/conexion.php'", "require_once '../../app/config/conexion.php'", $content);
        
        $content = str_replace('src="assets/img/', 'src="../assets/img/', $content);
        $content = str_replace('href="assets/img/', 'href="../assets/img/', $content);
        $content = str_replace('src="assets/video/', 'src="../assets/video/', $content);
        $content = str_replace("fetch('api_", "fetch('../api/api_", $content);
        
        // uploads dir in acciones_barberos
        $content = str_replace("\$uploadDir = 'uploads/barberos/';", "\$uploadDir = '../uploads/barberos/';", $content);
        
        // redirect in acciones_barberos.php goes to "barberos", which is still in the same admin/ dir so it's fine.
        
        // redirect in barberiasettings to "dashboard" remains "dashboard"
        
        file_put_contents($file, $content);
        echo "Updated Admin $file\n";
    }
}

// Refactor index.php
$indexFile = __DIR__ . '/public/index.php';
if (is_file($indexFile)) {
    $content = file_get_contents($indexFile);
    $content = preg_replace('/href="reserva"/', 'href="admin/reserva"', $content);
    $content = preg_replace('/href="barberiasettings"/', 'href="admin/barberiasettings"', $content);
    file_put_contents($indexFile, $content);
    echo "Updated Index\n";
}

// Update header.php links (in app/components/)
// Since header is included in admin/ files, links like href="dashboard" remain correct for admin pages!
// But if index.php includes header.php, it will break because index is in public/.
// Wait, index.php doesn't include header.php! It's a landing page that is fully self-contained. Let's verify.
?>
