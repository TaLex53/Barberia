<?php
$dir = __DIR__ . '/public/admin/';
$files = ['dashboard.php', 'citas.php', 'barberos.php', 'horarios.php', 'servicios.php'];

$target = '<p class="text-sm font-semibold text-white truncate">admin@cutlevel.cl</p>';
$replacement = '<p class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars(ucfirst($_SESSION[\'username\'] ?? \'Admin\')); ?></p>';

foreach ($files as $file) {
    $path = $dir . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, $target) !== false) {
            $content = str_replace($target, $replacement, $content);
            file_put_contents($path, $content);
            echo "Updated $file\n";
        }
    }
}
echo "Done.\n";
?>
