<?php
$files = array_merge(
    glob(__DIR__ . '/public/*.php'),
    glob(__DIR__ . '/app/components/*.php')
);

$replacements = [
    "require_once 'conexion.php'" => "require_once '../app/config/conexion.php'",
    "require_once 'header.php'" => "require_once '../app/components/header.php'",
    "require_once 'footer.php'" => "require_once '../app/components/footer.php'",
    "include 'header.php'" => "include '../app/components/header.php'",
    "include 'footer.php'" => "include '../app/components/footer.php'",
    'src="cutlevel.png"' => 'src="assets/img/cutlevel.png"',
    'src="iconlevel.png"' => 'src="assets/img/iconlevel.png"',
    'src="favicon.png"' => 'src="assets/img/favicon.png"',
    'href="favicon.png"' => 'href="assets/img/favicon.png"',
    'src="background_video.mp4"' => 'src="assets/video/background_video.mp4"',
    'src="barberia_1.mp4"' => 'src="assets/video/barberia_1.mp4"',
    'src="barberia_2.mp4"' => 'src="assets/video/barberia_2.mp4"',
    'src="barberia_3.mp4"' => 'src="assets/video/barberia_3.mp4"',
    'src="barberia_4.mp4"' => 'src="assets/video/barberia_4.mp4"',
    'src="barberia_5.mp4"' => 'src="assets/video/barberia_5.mp4"',
    'src="corte_1.jpeg"' => 'src="assets/img/corte_1.jpeg"',
    'src="corte_2.jpeg"' => 'src="assets/img/corte_2.jpeg"',
    'src="corte_3.jpeg"' => 'src="assets/img/corte_3.jpeg"',
    'src="corte_4.jpeg"' => 'src="assets/img/corte_4.jpeg"',
    'src="corte_5.jpeg"' => 'src="assets/img/corte_5.jpeg"',
    'src="corte_6.jpeg"' => 'src="assets/img/corte_6.jpeg"',
    'src="corte_7.jpeg"' => 'src="assets/img/corte_7.jpeg"',
    'src="corte_8.jpeg"' => 'src="assets/img/corte_8.jpeg"',
    'src="corte_9.jpeg"' => 'src="assets/img/corte_9.jpeg"',
    'src="nicolas.png"' => 'src="assets/img/nicolas.png"',
    'src="valen.png"' => 'src="assets/img/valen.png"',
    'src="enya.png"' => 'src="assets/img/enya.png"',
    'src="magno.png"' => 'src="assets/img/magno.png"'
];

foreach ($files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $newContent = strtr($content, $replacements);
        
        // Also update uploads dir reference in acciones_barberos.php
        $newContent = str_replace("\$uploadDir = 'uploads/barberos/';", "\$uploadDir = 'uploads/barberos/';", $newContent); 
        // Wait, uploads is inside public, and scripts are inside public, so 'uploads/barberos/' is still correct relative to public/acciones_barberos.php.

        file_put_contents($file, $newContent);
        echo "Updated $file\n";
    }
}
?>
