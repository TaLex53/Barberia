<?php
$file = __DIR__ . '/public/index.php';
$content = file_get_contents($file);

// 1. Add background ambient glows to sections
$glow1 = '<div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-indigo-900/10 rounded-full blur-[150px] pointer-events-none mix-blend-screen"></div>';
$glow2 = '<div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-stone-800/20 rounded-full blur-[150px] pointer-events-none mix-blend-screen"></div>';

// Replace basic section backgrounds
$content = str_replace(
    '<section id="mision-vision" class="py-24 bg-black relative border-t border-white/10">',
    '<section id="mision-vision" class="py-24 bg-[#030303] relative border-t border-white/5 overflow-hidden">' . "\n" . $glow1 . "\n" . $glow2,
    $content
);

$content = str_replace(
    '<section id="quien-soy" class="py-20 bg-[#080808] relative border-t border-white/10">',
    '<section id="quien-soy" class="py-20 bg-[#050505] relative border-t border-white/5 overflow-hidden">' . "\n" . '<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-slate-900/10 rounded-full blur-[200px] pointer-events-none mix-blend-screen"></div>',
    $content
);

$content = str_replace(
    '<section id="galeria" class="py-24 bg-[#080808] relative border-t border-white/10">',
    '<section id="galeria" class="py-24 bg-[#030303] relative border-t border-white/5 overflow-hidden">' . "\n" . '<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white/[0.03] via-transparent to-transparent pointer-events-none"></div>',
    $content
);

$content = str_replace(
    '<section id="servicios" class="py-32 bg-black relative overflow-hidden border-t border-white/10">',
    '<section id="servicios" class="py-32 bg-[#020202] relative overflow-hidden border-t border-white/5">' . "\n" . '<div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-900/10 rounded-full blur-[150px] pointer-events-none"></div>',
    $content
);

// 2. Enhance Service Cards to Glassmorphism
$content = str_replace(
    'bg-[#0a0a0a] border border-white/10 rounded-2xl overflow-hidden hover:border-white/30 hover:shadow-[0_0_30px_rgba(255,255,255,0.05)] hover:-translate-y-2 transition-all duration-500 group flex flex-col',
    'bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative',
    $content
);
$content = str_replace(
    'bg-gradient-to-b from-[#1a1a1a] to-[#050505]',
    'bg-gradient-to-b from-white/[0.05] to-transparent',
    $content
);
$content = str_replace(
    'text-white/5 text-[80px]',
    'text-white/10 text-[80px] drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]',
    $content
);

// 3. Enhance Gallery Cards Glow
$content = str_replace(
    'bg-[#1a1a1a] border border-white/10',
    'bg-white/5 backdrop-blur-md border border-white/10 hover:shadow-[0_0_30px_rgba(255,255,255,0.15)] hover:border-white/30 transition-all duration-500',
    $content
);

file_put_contents($file, $content);
echo "Aesthetics updated.\n";
?>
