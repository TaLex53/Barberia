<?php
$indexFile = __DIR__ . '/public/index.php';
if (is_file($indexFile)) {
    $content = file_get_contents($indexFile);
    
    // Fix missing asset paths
    $content = str_replace('src="cutleveel.jpg"', 'src="assets/img/cutleveel.jpg"', $content);
    $content = str_replace('src="quienes_somos.mov"', 'src="assets/video/quienes_somos.mov"', $content);
    $content = str_replace('src="Barberia.mov"', 'src="assets/video/Barberia.mov"', $content);
    $content = str_replace('src="strike.jpg"', 'src="assets/img/strike.jpg"', $content);
    
    file_put_contents($indexFile, $content);
    echo "Fixed missing assets in index.php.\n";
}
?>
