<?php
$file = __DIR__ . '/public/index.php';
$content = file_get_contents($file);

// 1. Add CSS for scroll reveal
$css = <<<HTML
    <style>
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        /* Gradient text utility */
        .text-gradient {
            background: linear-gradient(to right, #94a3b8, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Slow pulse for glows */
        @keyframes slowPulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .animate-slow-pulse {
            animation: slowPulse 8s ease-in-out infinite;
        }
    </style>
HTML;

// Inject CSS before </head>
// But wait, the head is in app/components/header.php!
// Let's inject it into header.php instead.
$headerFile = __DIR__ . '/app/components/header.php';
$headerContent = file_get_contents($headerFile);
if (strpos($headerContent, '.reveal {') === false) {
    $headerContent = str_replace('</head>', $css . "\n</head>", $headerContent);
    file_put_contents($headerFile, $headerContent);
}

// 2. Add JS for IntersectionObserver at the end of index.php
$js = <<<HTML
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
        
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });
</script>
HTML;

if (strpos($content, "IntersectionObserver") === false) {
    $content = $content . "\n" . $js;
}

// 3. Add .reveal classes and enhance elements in index.php
// Hero Text: add a subtle scale-in animation
$content = str_replace(
    'class="max-w-7xl mx-auto px-6 relative z-10 text-center py-20"',
    'class="max-w-7xl mx-auto px-6 relative z-10 text-center py-20 reveal active"', 
    $content
);

// Mision/Vision wrappers
$content = str_replace(
    'class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-24"',
    'class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-24 reveal"',
    $content
);

// Quien soy Wrapper
$content = str_replace(
    'class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center"',
    'class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center reveal"',
    $content
);

// Galeria wrapper
$content = str_replace(
    'class="max-w-7xl mx-auto px-6 relative z-10"',
    'class="max-w-7xl mx-auto px-6 relative z-10 reveal"',
    $content
);

// Add text gradients to italic spans
$content = str_replace(
    'text-slate-400 italic',
    'text-gradient italic drop-shadow-md',
    $content
);

// Make the glows animate slowly
$content = str_replace(
    'mix-blend-screen"',
    'mix-blend-screen animate-slow-pulse"',
    $content
);

file_put_contents($file, $content);
echo "Added more effects!\n";
?>
