<?php
require_once '../app/config/conexion.php';

// Fetch Active Barberos
try {
    $stmtB = $pdo->query("SELECT * FROM barberos WHERE activo = 1");
    $barberos = $stmtB->fetchAll(PDO::FETCH_ASSOC);
    // Optimización automática: Si la foto está guardada en BD como .png/.jpg pero existe una versión .webp ligera en el servidor, usarla de inmediato para carga instantánea
    foreach ($barberos as &$b) {
        if (!empty($b['foto'])) {
            $webp_path = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $b['foto']);
            if (file_exists($webp_path)) {
                $b['foto'] = $webp_path;
            }
        }
    }
    unset($b);
} catch (PDOException $e) {
    $barberos = [];
}

// Fetch Active Servicios and Group by Category
try {
    // We attempt to fetch 'categoria'. If the column doesn't exist (fallback), we group them all in one.
    $stmtS = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY categoria, nombre");
    $servicios_flat = $stmtS->fetchAll(PDO::FETCH_ASSOC);

    $servicios_grouped = [];
    foreach ($servicios_flat as $s) {
        $cat = !empty($s['categoria']) ? $s['categoria'] : 'Servicios Generales';
        if (!isset($servicios_grouped[$cat])) {
            $servicios_grouped[$cat] = [];
        }
        $servicios_grouped[$cat][] = $s;
    }
} catch (PDOException $e) {
    $servicios_grouped = [];
}
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita | Cut Level Barbería</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#ffffff' } } }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #080808;
            color: #f3f4f6;
        }

        .font-heading {
            font-family: 'Bebas Neue', cursive;
            letter-spacing: 0.02em;
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-weight: normal;
            font-style: normal;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555555;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .service-item:hover .book-btn {
            background-color: #111827;
            color: #fff;
        }

        /* Modal Animations */
        .modal-enter {
            animation: modalFadeIn 0.3s ease-out forwards;
        }

        .modal-leave {
            animation: modalFadeOut 0.3s ease-out forwards;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes modalFadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        .booking-step {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Selection cards inside modal */
        .selectable-card {
            background: #111111;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 40px -15px rgba(0, 0, 0, 0.7);
        }

        .selectable-card:hover {
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-4px);
        }

        .selectable-card.selected {
            border-color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 0 50px -10px rgba(255, 255, 255, 0.2);
        }

        .selectable-card.selected .checkmark {
            display: flex !important;
        }

        .selectable-card.selected .empty-circle {
            display: none;
        }
    </style>
</head>

<body class="selection:bg-[#111827] selection:text-white">

    <!-- Header Navigation -->
    <nav class="w-full bg-[#080808] border-b border-white/10 sticky top-0 z-40 shadow-sm">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-4 flex justify-between items-center w-full">
            <a href="inicio" class="flex items-center gap-4 group">
                <img src="assets/img/cutlevel.png" class="h-10 w-auto group-hover:scale-105 transition-transform"
                    alt="Cut Level">
            </a>
            <a href="inicio"
                class="flex items-center gap-2 text-gray-400 hover:text-white text-[10px] font-bold uppercase tracking-widest transition-colors">
                <span class="material-symbols-outlined text-sm">close</span> Volver al inicio
            </a>
        </div>
    </nav>

    <!-- Studio Profile Section -->
    <section class="max-w-[1400px] mx-auto px-4 sm:px-6 mt-8 mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Banner & Info -->
            <div
                class="lg:col-span-8 bg-[#0a0a0a] rounded-3xl border border-white/10 overflow-hidden shadow-sm relative">
                <!-- Cover Image -->
                <div class="h-48 sm:h-64 bg-[#111] relative w-full overflow-hidden">
                    <img src="assets/img/salon.webp" alt="Cut Level Salón"
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                </div>

                <!-- Profile Content -->
                <div class="px-8 pb-8 relative">
                    <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-end">
                        <div
                            class="w-28 h-28 sm:w-32 sm:h-32 flex-shrink-0 z-10 flex items-center justify-center -mt-10 sm:-mt-16 bg-[#080808] rounded-full p-1 border border-white/10">
                            <img src="assets/img/favicon.png" alt="Cut Level Logo"
                                class="w-full h-full object-contain drop-shadow-xl rounded-full">
                        </div>
                        <div class="pb-2 space-y-1 z-10 mt-2 sm:mt-0">
                            <h1 class="text-3xl sm:text-4xl font-heading tracking-wide text-white uppercase">Cut Level
                                Barbería</h1>
                            <a href="https://www.google.com/search?q=cut+level+studio&oq=cut+level+studio&gs_lcrp=EgZjaHJvbWUyCAgAEEUYJxg5MggIARAAGBYYHjIHCAIQABjvBTIKCAMQABiABBiiBDIHCAQQABjvBTIHCAUQABjvBTIHCAYQABjvBTIGCAcQRRg80gEIMjI2MGowajeoAgCwAgA&sourceid=chrome&source=chrome.ob&ie=UTF-8#lrd=0x9618270041e03ea9:0x409318ce69951301,1,,,,"
                                target="_blank"
                                class="flex items-center gap-2 text-yellow-500 text-sm hover:opacity-80 transition-opacity">
                                <span class="material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="text-white font-semibold ml-1">5.0 <span
                                        class="text-gray-400 font-normal underline text-xs">(Ver opiniones en
                                        Google)</span></span>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4 max-w-2xl">
                        <p class="text-sm font-semibold text-gray-300">Elevamos el cuidado masculino en el sur.</p>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Más que un servicio tradicional, en Cut Level creamos un ritual de distinción en Puerto
                            Varas.
                            Combinamos técnicas de vanguardia, un ambiente exclusivo y un cuidado riguroso por el
                            detalle para el hombre contemporáneo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Maps & Contact -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-[#0a0a0a] rounded-3xl border border-white/10 shadow-sm relative flex flex-col">
                    <!-- Map Box -->
                    <div class="rounded-t-3xl overflow-hidden h-48 shrink-0 border-b border-white/10">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11922.956799049964!2d-72.98662999999999!3d-41.3194689!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9618270041e03ea9%3A0x409318ce69951301!2sCut%20Level%20Studio!5e0!3m2!1ses-419!2scl!4v1700000000000!5m2!1ses-419!2scl"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                    <div class="p-6 pt-6 flex-1 flex flex-col">
                        <!-- Contact Info -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 text-sm text-gray-300">
                                <span class="material-symbols-outlined text-gray-500 text-[18px]">location_on</span>
                                Av. Colón 0600, Puerto Varas
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-300">
                                <span class="material-symbols-outlined text-gray-500 text-[18px]">smartphone</span>
                                +56 9 2086 0076
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <svg class="w-[18px] h-[18px] text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                <a href="https://wa.me/56920860076" target="_blank"
                                    class="text-gray-300 hover:text-white font-medium transition-colors">¡Contáctanos
                                    por Whatsapp!</a>
                            </div>
                            <div class="flex flex-col text-sm text-gray-400 relative group w-max cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gray-500 text-[18px]">schedule</span>
                                    <span class="underline text-gray-400 group-hover:text-white transition-colors">Ver
                                        horario de atención</span>
                                </div>
                                <div id="horarios-list"
                                    class="opacity-0 invisible group-hover:opacity-100 group-hover:visible absolute top-full left-0 z-50 mt-3 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 transition-all duration-300">
                                    <ul class="space-y-3 text-[12px] font-bold text-gray-800">
                                        <li class="flex justify-between border-b border-gray-100 pb-2">
                                            <span>Lunes</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-gray-100 pb-2">
                                            <span>Martes</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-gray-100 pb-2">
                                            <span>Miércoles</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-gray-100 pb-2">
                                            <span>Jueves</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-gray-100 pb-2">
                                            <span>Viernes</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-gray-100 pb-2">
                                            <span>Sábado</span> <span class="text-gray-500 font-medium">11:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between">
                                            <span>Domingo</span> <span class="text-gray-500 font-medium">11:00 -
                                                20:00</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="border-white/10 my-6">

                        <!-- Profesionales Card -->
                        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Profesionales
                        </h3>
                        <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                            <?php foreach ($barberos as $b): ?>
                                <div class="flex flex-col items-center gap-2 min-w-[70px] group cursor-pointer">
                                    <div
                                        class="w-14 h-14 rounded-full overflow-hidden border-2 border-transparent group-hover:border-white transition-all bg-[#111]">
                                        <?php if (!empty($b['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($b['foto']); ?>"
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-2xl text-gray-500">person</span>
                                        <?php endif; ?>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-gray-300 uppercase tracking-wider group-hover:text-white transition-colors">
                                        <?php echo htmlspecialchars(explode(' ', $b['nombre'])[0]); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Selection Section -->
    <section class="max-w-[1400px] mx-auto px-4 sm:px-6 mb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Sidebar: Categories -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 bg-[#0a0a0a] rounded-3xl border border-white/10 p-6 shadow-sm">

                    <!-- Search Bar -->
                    <div class="relative mb-6">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-[20px]">search</span>
                        <input type="text" id="service-search" placeholder="¿Qué servicio buscas?"
                            onkeyup="filterServices(this.value)"
                            class="w-full bg-transparent border border-white/20 rounded-full pl-12 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-white transition-all placeholder:text-gray-500">
                    </div>

                    <!-- Category List -->
                    <ul class="flex flex-col">
                        <li>
                            <a href="#cat-all"
                                class="block px-4 py-3 bg-white/10 rounded-xl text-sm font-bold text-white mb-2 transition-colors">
                                Todos
                            </a>
                        </li>
                        <?php foreach (array_keys($servicios_grouped) as $index => $cat): ?>
                            <li class="border-b border-white/5 last:border-0">
                                <a href="#cat-<?php echo $index; ?>" onclick="openCategory(<?php echo $index; ?>)"
                                    class="block px-4 py-3.5 text-sm font-medium text-gray-400 hover:text-white transition-colors">
                                    <?php echo htmlspecialchars($cat); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Main: Services Accordion List -->
            <div class="lg:col-span-8 space-y-8" id="cat-all">
                <?php foreach ($servicios_grouped as $categoria => $servicios_cat):
                    $catIndex = array_search($categoria, array_keys($servicios_grouped));
                    ?>
                    <div id="cat-<?php echo $catIndex; ?>" class="scroll-mt-24 mb-4">
                        <!-- Category Header -->
                        <div class="bg-[#0a0a0a] hover:bg-white/5 border border-white/10 rounded-xl px-6 py-4 flex items-center justify-between cursor-pointer transition-colors"
                            onclick="toggleCategory(<?php echo $catIndex; ?>)">
                            <h2 class="text-[15px] font-semibold text-white"><?php echo htmlspecialchars($categoria); ?>
                            </h2>
                            <span id="icon-cat-<?php echo $catIndex; ?>"
                                class="text-gray-500 text-2xl font-light leading-none transition-transform duration-300">+</span>
                        </div>
                        <!-- Services Grid -->
                        <div id="grid-cat-<?php echo $catIndex; ?>"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 mb-2 hidden">
                            <?php foreach ($servicios_cat as $s): ?>
                                <div
                                    class="service-card bg-[#0a0a0a] border border-white/10 rounded-2xl p-6 flex flex-col justify-between hover:border-white transition-all shadow-sm group">
                                    <div>
                                        <h3 class="text-base font-bold text-white mb-2">
                                            <?php echo htmlspecialchars($s['nombre']); ?>
                                        </h3>
                                        <div class="flex flex-col gap-0.5 mb-4">
                                            <span
                                                class="text-xs text-gray-400 font-medium"><?php echo $s['duracion_minutos']; ?>
                                                min</span>
                                            <span
                                                class="text-base font-black text-white">$<?php echo number_format($s['precio'], 0, ',', '.'); ?></span>
                                        </div>
                                        <div class="mb-6">
                                            <div id="desc-<?php echo $s['id']; ?>"
                                                class="text-xs text-gray-400 hidden mt-2 mb-3 leading-relaxed">
                                                <?php echo !empty($s['descripcion']) ? nl2br(htmlspecialchars($s['descripcion'])) : 'Sin descripción disponible por el momento.'; ?>
                                            </div>
                                            <button type="button" id="btn-desc-<?php echo $s['id']; ?>"
                                                class="text-xs font-semibold text-gray-300 hover:text-white underline transition-colors text-left"
                                                onclick="toggleDescription(<?php echo $s['id']; ?>)">Más información</button>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-auto">
                                        <button id="btn-service-<?php echo $s['id']; ?>"
                                            class="service-toggle-btn bg-white hover:bg-gray-200 text-black text-xs font-bold px-5 py-2.5 rounded-lg border border-transparent transition-all w-full sm:w-auto"
                                            onclick="toggleService(<?php echo $s['id']; ?>, '<?php echo addslashes($s['nombre']); ?>', <?php echo $s['duracion_minutos']; ?>, <?php echo $s['precio']; ?>)">
                                            Agendar servicio
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- Modal Booking Flow -->
    <div id="booking-modal" class="fixed inset-0 z-[100] bg-[#080808] hidden flex-col w-full h-full">

        <!-- Modal Header -->
        <div class="bg-[#0a0a0a] flex items-center justify-between px-6 py-4 border-b border-white/10 shrink-0">
            <div class="flex items-center gap-4">
                <!-- Using an image or text for the logo -->
                <img src="assets/img/cutlevel_reserva.png" alt="Cut Level"
                    class="h-8 object-contain hidden sm:block filter invert">
                <!-- Desktop Title -->
                <div class="hidden sm:block">
                    <h2 class="text-sm font-bold text-white">Cut Level Studio</h2>
                    <p class="text-xs text-gray-400">Sucursal Puerto Varas</p>
                </div>
                <!-- Mobile Back Button -->
                <button type="button" onclick="handleMobileBack()" class="flex sm:hidden items-center text-gray-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-chevron-left mr-2 text-sm"></i>
                    <span class="font-medium text-base">Anterior</span>
                </button>
            </div>
            <button onclick="closeBookingModal()"
                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:bg-white/10 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body: 2 Columns -->
        <div class="flex-grow flex flex-col lg:flex-row max-w-[1400px] w-full mx-auto gap-4 lg:gap-6 p-4 sm:p-8 overflow-hidden">

            <!-- Mobile Summary Top Card (Expandable) -->
            <div id="mobile-summary-container" class="hidden lg:hidden bg-[#111] border border-white/10 rounded-xl overflow-hidden shadow-sm shrink-0">
                <!-- Header / Collapsed state -->
                <div class="p-4 flex items-start justify-between cursor-pointer select-none" onclick="document.getElementById('mobile-summary-details').classList.toggle('hidden'); document.getElementById('mobile-summary-chevron').classList.toggle('rotate-180');">
                    <div>
                        <h4 id="mobile-sum-service" class="font-bold text-white text-[15px] mb-1.5 leading-tight">Selecciona un servicio</h4>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">payments</span>
                            <span id="mobile-sum-price" class="text-white font-bold text-sm">$0</span>
                        </div>
                    </div>
                    <span id="mobile-summary-chevron" class="material-symbols-outlined text-gray-400 transition-transform duration-300 rotate-180">expand_more</span>
                </div>
                
                <!-- Expanded Details -->
                <div id="mobile-summary-details" class="px-4 pb-4 bg-[#0a0a0a] border-t border-white/10 pt-4">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">calendar_today</span>
                            <span id="mobile-sum-date" class="text-sm text-gray-300">-</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">schedule</span>
                            <span id="mobile-sum-time" class="text-sm text-gray-300">-</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">person</span>
                            <span id="mobile-sum-barber" class="text-sm text-gray-300">-</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">storefront</span>
                            <span class="text-sm text-gray-300">Cut Level Studio</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Column: Steps -->
            <div
                class="lg:w-8/12 flex flex-col bg-[#0a0a0a] rounded-lg border border-white/10 shadow-sm overflow-hidden h-full">

                <div class="px-6 py-5 border-b border-white/10 shrink-0">
                    <h2 class="text-lg font-bold text-white" id="step-title">Selecciona fecha y hora de tu servicio
                    </h2>
                </div>

                <!-- Stepper Progress Tabs -->
                <div class="flex items-center justify-between border-b border-white/10 px-6 shrink-0">
                    <button
                        onclick="goToStep(1)"
                        class="step-tab flex-1 py-3 sm:py-4 text-[10px] sm:text-sm font-bold flex flex-col sm:flex-row justify-center items-center gap-1 sm:gap-2 border-b-2 transition-all border-white text-white hover:bg-white/5 cursor-pointer text-center leading-tight"
                        id="tab-1">
                        <span
                            class="w-5 h-5 sm:w-5 sm:h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">1</span>
                        <span>Fecha y hora</span>
                    </button>
                    <button
                        onclick="goToStep(2)"
                        class="step-tab flex-1 py-3 sm:py-4 text-[10px] sm:text-sm font-bold flex flex-col sm:flex-row justify-center items-center gap-1 sm:gap-2 border-b-2 transition-all border-transparent text-gray-500 hover:bg-white/5 cursor-pointer text-center leading-tight"
                        id="tab-2">
                        <span
                            class="w-5 h-5 sm:w-5 sm:h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">2</span>
                        <span>Profesional</span>
                    </button>
                    <button
                        onclick="goToStep(3)"
                        class="step-tab flex-1 py-3 sm:py-4 text-[10px] sm:text-sm font-bold flex flex-col sm:flex-row justify-center items-center gap-1 sm:gap-2 border-b-2 transition-all border-transparent text-gray-500 hover:bg-white/5 cursor-pointer text-center leading-tight"
                        id="tab-3">
                        <span
                            class="w-5 h-5 sm:w-5 sm:h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">3</span>
                        <span>Datos de contacto</span>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto no-scrollbar grow">
                    <!-- STEP 1: DATE & TIME -->
                    <div id="step-1" class="booking-step">
                        <div class="space-y-8">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-sm font-bold text-white" id="month-display">Mes</div>
                                </div>
                                <div class="relative flex items-center group/slider">
                                    <button type="button" onclick="document.getElementById('date-picker').scrollBy({left: -200, behavior: 'smooth'})" class="absolute -left-3 z-10 w-8 h-8 flex items-center justify-center bg-[#111] border border-white/10 rounded-full text-white hover:bg-white hover:text-black transition-colors opacity-0 group-hover/slider:opacity-100 shadow-lg">
                                        <i class="fa-solid fa-chevron-left text-sm"></i>
                                    </button>
                                    
                                    <div class="flex gap-2 sm:gap-3 overflow-x-auto py-2 px-1 no-scrollbar items-center w-full scroll-smooth" id="date-picker">
                                        <!-- JS Generated Dates -->
                                    </div>

                                    <button type="button" onclick="document.getElementById('date-picker').scrollBy({left: 200, behavior: 'smooth'})" class="absolute -right-3 z-10 w-8 h-8 flex items-center justify-center bg-[#111] border border-white/10 rounded-full text-white hover:bg-white hover:text-black transition-colors opacity-0 group-hover/slider:opacity-100 shadow-lg">
                                        <i class="fa-solid fa-chevron-right text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <div id="time-loading" class="text-gray-400 text-xs font-bold uppercase hidden">Buscando
                                    horarios...</div>
                                <div id="time-empty" class="hidden flex flex-col items-center justify-center py-12 text-center">
                                    <div class="w-16 h-16 rounded-full border-2 border-white/20 flex items-center justify-center mb-4">
                                        <i class="fa-regular fa-clock text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-white font-semibold text-base mb-6">No hay horas disponibles para este día</h3>
                                    <button type="button" onclick="findNextAvailableDay()" class="bg-[#1f2937] hover:bg-[#374151] text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors border border-white/10">
                                        Ir a la siguiente hora disponible
                                    </button>
                                </div>

                                <div id="time-container" class="space-y-10 hidden pt-6">
                                    <div class="flex flex-col gap-5">
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm text-gray-300 font-medium">Mañana</span>
                                            <div class="flex-grow h-[1px] bg-white/10"></div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 sm:gap-3" id="time-grid-manana"></div>
                                    </div>
                                    <div class="flex flex-col gap-5">
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm text-gray-300 font-medium">Tarde</span>
                                            <div class="flex-grow h-[1px] bg-white/10"></div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 sm:gap-3" id="time-grid-tarde"></div>
                                    </div>
                                    <div class="flex flex-col gap-5">
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm text-gray-300 font-medium">Noche</span>
                                            <div class="flex-grow h-[1px] bg-white/10"></div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 sm:gap-3" id="time-grid-noche"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: BARBERO -->
                    <div id="step-2" class="booking-step hidden">
                        <div id="barber-loading" class="text-gray-400 text-xs font-bold uppercase hidden">Buscando
                            profesionales...</div>
                        <div id="barber-empty" class="text-red-500 text-xs font-bold uppercase hidden">Nadie disponible
                            a esta hora.</div>

                        <div class="flex flex-col border border-white/10 rounded-xl bg-[#111] overflow-hidden" id="barber-grid">
                            <?php foreach ($barberos as $b): ?>
                                <div class="barber-card cursor-pointer p-4 border-b last:border-b-0 border-white/10 hover:bg-white/5 transition-all duration-300 relative flex flex-row items-center gap-4 w-full"
                                    data-id="<?php echo $b['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($b['nombre'] . ' ' . $b['apellido']); ?>">
                                    
                                    <!-- Avatar -->
                                    <div class="w-12 h-12 rounded-full bg-[#222] border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                                        <?php if (!empty($b['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($b['foto']); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-2xl text-gray-300">person</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="flex flex-col flex-grow text-left">
                                        <h4 class="text-white font-bold text-sm tracking-wide m-0 p-0">
                                            <?php echo htmlspecialchars($b['nombre'] . ' ' . $b['apellido']); ?>
                                        </h4>
                                    </div>

                                    <!-- Checkmark -->
                                    <div class="checkmark hidden shrink-0 w-6 h-6 rounded-full bg-white flex items-center justify-center">
                                        <span class="material-symbols-outlined text-black text-[14px] font-black">done</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- STEP 3: CONTACT INFO -->
                    <div id="step-3" class="booking-step hidden">
                        <form id="booking-form" class="space-y-5 max-w-2xl"
                            onsubmit="event.preventDefault(); submitBooking();">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-300 mb-1">Nombre <span class="text-red-500">*</span></label>
                                    <input type="text" id="client-name" required
                                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-300 mb-1">Apellido <span class="text-red-500">*</span></label>
                                    <input type="text" id="client-lastname" required
                                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" id="client-email" required
                                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-300 mb-1">Teléfono <span class="text-red-500">*</span></label>
                                    <input type="tel" id="client-phone" required placeholder="+56 9 1234 5678"
                                        oninput="formatChileanPhone(this)"
                                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Notas / Observaciones
                                    (Opcional)</label>
                                <textarea id="client-obs" rows="2"
                                    class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none"></textarea>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 border-t border-white/10 pt-4">
                                <label class="flex items-start gap-3 cursor-pointer group relative">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0 w-5 h-5">
                                        <input type="checkbox" required id="client-terms"
                                            class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20 m-0 p-0">
                                        <div class="absolute inset-0 border-2 border-white/20 rounded bg-[#111] peer-checked:bg-white peer-checked:border-white transition-all pointer-events-none z-0"></div>
                                        <span class="material-symbols-outlined absolute text-black text-[16px] font-black opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none z-10 flex items-center justify-center w-full h-full text-center">check</span>
                                    </div>
                                    <span class="text-xs text-gray-400 leading-relaxed">
                                        Acepto las Políticas de Reserva y Privacidad de Cut Level Studio, y autorizo recibir notificaciones, recordatorios por WhatsApp y otras comunicaciones referentes a mi cita.
                                    </span>
                                </label>
                                <p class="text-[10px] text-gray-500 mt-1 pl-8">
                                    Este sitio está protegido por reCAPTCHA y se aplican la <a href="https://policies.google.com/privacy" target="_blank" class="underline hover:text-white">Política de Privacidad</a> y las <a href="https://policies.google.com/terms" target="_blank" class="underline hover:text-white">Condiciones de Servicio</a> de Google.
                                </p>
                            </div>
                        </form>
                    </div>

                </div>
            </div> <!-- End Left Column -->

            <!-- Right Column: Summary Card -->
            <div class="lg:w-4/12 hidden lg:flex flex-col gap-4">
                <div class="bg-[#0a0a0a] rounded-lg border border-white/10 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-white/10 text-center">
                        <h3 class="text-sm font-bold text-white">Información de tus servicios</h3>
                    </div>

                    <div class="p-4">
                        <div class="bg-[#111] rounded-lg border border-white/10 p-5">
                            <h4 id="summary-service-name"
                                class="font-bold text-white text-sm mb-3 border-b border-white/10 pb-2">Selecciona
                                un servicio</h4>

                            <div class="space-y-2 mt-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500 text-[16px]">payments</span>
                                    <span id="summary-price" class="text-sm font-medium text-gray-300">$0</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="material-symbols-outlined text-gray-500 text-[16px]">calendar_today</span>
                                    <span id="summary-date" class="text-sm font-medium text-gray-300">-</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500 text-[16px]">schedule</span>
                                    <span id="summary-time" class="text-sm font-medium text-gray-300">-</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500 text-[16px]">person</span>
                                    <span id="summary-barber" class="text-sm font-medium text-gray-300">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Right Column -->

        </div> <!-- End Modal Body Grid -->

        <!-- Modal Footer (Action Buttons) -->
        <div class="bg-[#0a0a0a] border-t border-white/10 shrink-0 w-full">
            <div class="max-w-[1400px] w-full mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                
                <!-- Left side -->
                <div class="flex items-center pr-2 overflow-hidden w-full sm:w-auto" id="footer-left-side">
                    <button id="prev-btn" onclick="prevStep()" style="display: none;"
                        class="hidden sm:block px-5 py-2.5 rounded-lg border border-white/20 text-gray-300 text-sm font-bold hover:bg-white/10 transition-colors shrink-0 mr-3">
                        Atrás
                    </button>
                    <!-- Mobile Summary Footer -->
                    <div id="mobile-footer-summary" class="flex flex-col sm:hidden text-left truncate w-full pr-2">
                        <span id="mobile-footer-service" class="text-gray-300 font-bold text-[13px] truncate">Selecciona un servicio</span>
                        <span id="mobile-footer-price" class="text-white font-bold text-[13px] truncate">$0</span>
                        <span id="mobile-footer-datetime" class="text-gray-400 text-[11px] truncate">-</span>
                    </div>
                </div>

                <!-- Right side -->
                <div id="footer-right-side" class="flex gap-3 items-center shrink-0 w-auto sm:w-auto justify-end">
                    <button id="next-btn" onclick="nextStep()" disabled
                        class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#1f2937] text-gray-500 cursor-not-allowed text-sm font-bold transition-colors hidden shadow-md">
                        Siguiente
                    </button>
                    <button id="submit-btn" onclick="submitBooking()"
                        class="w-full sm:w-auto px-6 py-3 sm:py-2.5 rounded-lg bg-white text-black text-sm font-bold hover:bg-gray-200 transition-colors hidden shadow-md flex justify-center">
                        Confirmar Cita
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Sticky Cart Footer -->
    <div id="cart-footer"
        class="fixed bottom-0 left-0 w-full bg-[#0a0a0a] border-t border-white/10 p-4 transform translate-y-full transition-transform duration-300 z-50 flex items-center justify-between shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">
        <div class="max-w-[1400px] mx-auto w-full flex items-center justify-between px-2 sm:px-6">
            <div>
                <h3 class="text-white font-bold text-sm sm:text-lg">
                    <span id="cart-count">0</span> servicios seleccionados
                </h3>
                <p class="text-gray-300 text-xs sm:text-sm font-semibold" id="cart-total">$0</p>
            </div>
            <button onclick="openBookingModal()"
                class="bg-white text-black font-bold uppercase tracking-widest text-xs sm:text-sm px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors shadow-md">
                Ir a reserva
            </button>
        </div>
    </div>

    <script>
        function toggleCategory(index) {
            const grid = document.getElementById('grid-cat-' + index);
            const icon = document.getElementById('icon-cat-' + index);
            if (grid.classList.contains('hidden')) {
                grid.classList.remove('hidden');
                icon.textContent = '−';
                icon.classList.add('rotate-180');
            } else {
                grid.classList.add('hidden');
                icon.textContent = '+';
                icon.classList.remove('rotate-180');
            }
        }

        function filterServices(query) {
            const lowerQuery = query.toLowerCase();
            const categories = document.querySelectorAll('.category-block');

            // Note: Since categories are dynamically generated, we need to iterate over them
            // using the existing DOM structure. We will search inside each grid.
            let catIndex = 0;
            while (true) {
                const headerBlock = document.getElementById('cat-' + catIndex);
                const grid = document.getElementById('grid-cat-' + catIndex);

                if (!headerBlock || !grid) break;

                const serviceCards = grid.querySelectorAll('.service-card');
                let hasVisibleMatch = false;

                serviceCards.forEach(card => {
                    const title = card.querySelector('h3').textContent.toLowerCase();
                    const desc = card.querySelector('p') ? card.querySelector('p').textContent.toLowerCase() : '';

                    if (title.includes(lowerQuery) || desc.includes(lowerQuery)) {
                        card.style.display = '';
                        hasVisibleMatch = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Expand category if there is a match and we are searching
                if (lowerQuery !== '' && hasVisibleMatch) {
                    headerBlock.style.display = '';
                    if (grid.classList.contains('hidden')) {
                        openCategory(catIndex);
                    }
                } else if (lowerQuery !== '' && !hasVisibleMatch) {
                    // Hide category if no matches at all
                    headerBlock.style.display = 'none';
                } else {
                    // Reset to default
                    headerBlock.style.display = '';
                }

                catIndex++;
            }
        }

        function openCategory(index) {
            const grid = document.getElementById('grid-cat-' + index);
            const icon = document.getElementById('icon-cat-' + index);
            if (grid && grid.classList.contains('hidden')) {
                grid.classList.remove('hidden');
                icon.textContent = '−';
                icon.classList.add('rotate-180');
            }
        }

        function formatChileanPhone(input) {
            let value = input.value.replace(/\D/g, ''); // Keep only digits
            if (value.length > 11) value = value.substring(0, 11); // max 11 digits (56 9 XXXX XXXX)

            // Auto format
            let formatted = '';
            if (value.length > 0) {
                // If they start typing 9, assume +56 9
                if (value.startsWith('9')) value = '56' + value;
                // If they type something else and it doesn't start with 56, prepend it
                else if (!value.startsWith('5') && value.length > 0) value = '569' + value;

                formatted = '+' + value.substring(0, 2);
            }
            if (value.length > 2) {
                formatted += ' ' + value.substring(2, 3);
            }
            if (value.length > 3) {
                formatted += ' ' + value.substring(3, 7);
            }
            if (value.length > 7) {
                formatted += ' ' + value.substring(7, 11);
            }
            input.value = formatted;
        }

        // Data State
        let currentStep = 1;

        let selectedServices = []; // Array of objects {id, name, duration, price}

        // Removed single selectedService variables

        let selectedDateISO = null;
        let selectedDateDisplay = '';
        let selectedTimeId = null;
        let selectedTimeDisplay = '';

        let selectedBarberId = null;
        let selectedBarberName = '';

        // Elements
        const modal = document.getElementById('booking-modal');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');

        // Summary Elements
        const sumService = document.getElementById('summary-service-name');
        const sumPrice = document.getElementById('summary-price');
        const sumDate = document.getElementById('summary-date');
        const sumTime = document.getElementById('summary-time');
        const sumBarber = document.getElementById('summary-barber');

        function formatTimeRange(startStr, durMin) {
            if (!startStr) return '';
            try {
                let parts = startStr.trim().split(' ');
                let timeParts = parts[0].split(':');
                let h = parseInt(timeParts[0]);
                let m = parseInt(timeParts[1]);
                let ampm = parts[1] ? parts[1].toLowerCase() : '';
                if (ampm === 'pm' && h < 12) h += 12;
                if (ampm === 'am' && h === 12) h = 0;
                let date = new Date();
                date.setHours(h, m + durMin, 0, 0);
                let eh = date.getHours();
                let em = date.getMinutes();
                let eampm = eh >= 12 ? 'pm' : 'am';
                eh = eh % 12;
                if (eh === 0) eh = 12;
                return startStr.toLowerCase() + ' - ' + eh + ':' + (em < 10 ? '0' : '') + em + ' ' + eampm;
            } catch(e) {
                return startStr + ' (' + durMin + ' min)';
            }
        }

        function updateSummary() {
            const mService = document.getElementById('mobile-sum-service');
            const mPrice = document.getElementById('mobile-sum-price');
            const mDate = document.getElementById('mobile-sum-date');
            const mTime = document.getElementById('mobile-sum-time');
            const mBarber = document.getElementById('mobile-sum-barber');
            
            const mfService = document.getElementById('mobile-footer-service');
            const mfPrice = document.getElementById('mobile-footer-price');
            const mfDatetime = document.getElementById('mobile-footer-datetime');

            if (selectedServices.length === 0) {
                sumService.textContent = 'Selecciona un servicio';
                sumPrice.textContent = '$0';
                if(mService) mService.textContent = 'Selecciona un servicio';
                if(mPrice) mPrice.textContent = '$0';
                if(mfService) mfService.textContent = 'Selecciona un servicio';
                if(mfPrice) mfPrice.textContent = '$0';
            } else {
                let names = selectedServices.map(s => s.name).join(' + ');
                let total = selectedServices.reduce((acc, s) => acc + s.price, 0);
                let dur = selectedServices.reduce((acc, s) => acc + s.duration, 0);

                sumService.textContent = names;
                sumPrice.textContent = '$' + new Intl.NumberFormat('es-CL').format(total);
                sumTime.textContent = selectedTimeDisplay ? selectedTimeDisplay + ' (' + dur + ' min)' : '-';
                
                if(mService) mService.textContent = names;
                if(mPrice) mPrice.textContent = '$' + new Intl.NumberFormat('es-CL').format(total);
                
                if (mTime) {
                    let timeRange = selectedTimeDisplay ? formatTimeRange(selectedTimeDisplay, dur) : '-';
                    mTime.textContent = timeRange;
                }
                
                if(mfService) mfService.textContent = names;
                if(mfPrice) mfPrice.textContent = '$' + new Intl.NumberFormat('es-CL').format(total);
            }
            sumDate.textContent = selectedDateDisplay || '-';
            sumBarber.textContent = selectedBarberName || '-';

            if (mDate) mDate.textContent = selectedDateDisplay || '-';
            if (mBarber) mBarber.textContent = selectedBarberName || '-';
            
            if (mfDatetime) {
                let dur = selectedServices.reduce((acc, s) => acc + s.duration, 0);
                let timeRange = selectedTimeDisplay ? formatTimeRange(selectedTimeDisplay, dur) : '';
                let dtString = (selectedDateDisplay || '') + (timeRange ? ' - ' + timeRange : '');
                mfDatetime.textContent = dtString || '-';
            }
        }

        // Cart Logic
        function toggleService(id, name, duration, price) {
            const index = selectedServices.findIndex(s => s.id === id);
            const btn = document.getElementById('btn-service-' + id);

            if (index > -1) {
                // Remove
                selectedServices.splice(index, 1);
                btn.innerHTML = 'Agendar servicio';
                btn.classList.remove('bg-transparent', 'text-white', 'border-white');
                btn.classList.add('bg-white', 'text-black', 'border-transparent');
            } else {
                // Add
                selectedServices.push({ id, name, duration, price });
                btn.innerHTML = 'Seleccionado <span class="material-symbols-outlined text-[12px] ml-1 align-middle">check</span>';
                btn.classList.remove('bg-white', 'text-black', 'border-transparent');
                btn.classList.add('bg-transparent', 'text-white', 'border-white');
            }

            updateCartFooter();
        }

        function updateCartFooter() {
            const footer = document.getElementById('cart-footer');
            const count = document.getElementById('cart-count');
            const total = document.getElementById('cart-total');

            if (selectedServices.length > 0) {
                footer.classList.remove('translate-y-full');
                count.textContent = selectedServices.length;
                let sum = selectedServices.reduce((acc, s) => acc + s.price, 0);
                total.textContent = '$' + new Intl.NumberFormat('es-CL').format(sum);
            } else {
                footer.classList.add('translate-y-full');
            }
        }

        function toggleDescription(id) {
            const descDiv = document.getElementById('desc-' + id);
            const btn = document.getElementById('btn-desc-' + id);
            if (descDiv.classList.contains('hidden')) {
                descDiv.classList.remove('hidden');
                btn.innerText = 'Menos información';
            } else {
                descDiv.classList.add('hidden');
                btn.innerText = 'Más información';
            }
        }

        // Stepper Navigation
        function updateIndicators() {
            for (let i = 1; i <= 3; i++) {
                const tab = document.getElementById('tab-' + i);
                if (i <= currentStep) {
                    tab.classList.remove('border-transparent', 'text-gray-500');
                    tab.classList.add('border-white', 'text-white');
                    tab.querySelector('span').classList.remove('border-current', 'text-[10px]');
                    tab.querySelector('span').classList.add('bg-white', 'text-black', 'border-transparent');
                    tab.querySelector('span').innerHTML = i < currentStep ? '<span class="material-symbols-outlined text-[12px] font-bold">check</span>' : i;
                } else {
                    tab.classList.remove('border-white', 'text-white');
                    tab.classList.add('border-transparent', 'text-gray-500');
                    tab.querySelector('span').classList.remove('bg-white', 'text-black', 'border-transparent');
                    tab.querySelector('span').classList.add('border-current', 'text-[10px]');
                    tab.querySelector('span').innerHTML = i;
                }
            }
        }

        function setNextBtnEnabled(enabled) {
            const nextBtn = document.getElementById('next-btn');
            if (enabled) {
                nextBtn.disabled = false;
                nextBtn.classList.remove('bg-[#1f2937]', 'text-gray-500', 'cursor-not-allowed');
                nextBtn.classList.add('bg-white', 'text-black', 'hover:bg-gray-200');
            } else {
                nextBtn.disabled = true;
                nextBtn.classList.remove('bg-white', 'text-black', 'hover:bg-gray-200');
                nextBtn.classList.add('bg-[#1f2937]', 'text-gray-500', 'cursor-not-allowed');
            }
        }

        function showStep(step) {
            document.querySelectorAll('.booking-step').forEach(el => el.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');

            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            const stepTitle = document.getElementById('step-title');

            if (step > 1) {
                if (prevBtn) prevBtn.style.display = '';
            } else {
                if (prevBtn) prevBtn.style.display = 'none';
            }

            if (step === 1) {
                if (stepTitle) stepTitle.textContent = 'Selecciona fecha y hora de tu servicio';
                initDatePicker();
                setNextBtnEnabled(!!selectedTimeId);
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            } else if (step === 2) {
                if (stepTitle) stepTitle.textContent = 'Selecciona el/los profesionales para tus servicios';
                setNextBtnEnabled(!!selectedBarberId);
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            } else if (step === 3) {
                if (stepTitle) stepTitle.textContent = 'Datos de contacto';
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            }

            const mobileSumContainer = document.getElementById('mobile-summary-container');
            const mFooterSum = document.getElementById('mobile-footer-summary');
            const footerRightSide = document.getElementById('footer-right-side');

            if (step === 3) {
                if (mobileSumContainer) {
                    mobileSumContainer.classList.remove('hidden');
                    mobileSumContainer.classList.add('block');
                }
                if (mFooterSum) {
                    mFooterSum.classList.add('hidden');
                    mFooterSum.classList.remove('flex');
                }
                if (footerRightSide) {
                    footerRightSide.classList.add('w-full');
                    footerRightSide.classList.remove('w-auto');
                }
            } else {
                if (mobileSumContainer) {
                    mobileSumContainer.classList.add('hidden');
                    mobileSumContainer.classList.remove('block');
                }
                if (mFooterSum) {
                    mFooterSum.classList.remove('hidden');
                    mFooterSum.classList.add('flex');
                }
                if (footerRightSide) {
                    footerRightSide.classList.add('w-auto');
                    footerRightSide.classList.remove('w-full');
                }
            }

            updateIndicators();
        }

        window.nextStep = () => {
            if (currentStep === 1 && !selectedTimeId) {
                Swal.fire({ icon: 'warning', title: 'Falta información', text: 'Por favor selecciona un horario.', background: '#111', color: '#fff' });
                return;
            }
            if (currentStep === 1) {
                // Moving to step 2, load barbers
                loadAvailableBarbers();
            }
            if (currentStep === 2 && !selectedBarberId) {
                Swal.fire({ icon: 'warning', title: 'Falta información', text: 'Por favor selecciona un profesional.', background: '#111', color: '#fff' });
                return;
            }
            if (currentStep < 3) {
                currentStep++;
                showStep(currentStep);
            }
        };

        window.prevStep = () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        };

        window.goToStep = (targetStep) => {
            if (targetStep < currentStep) {
                // Allow going backward anytime
                currentStep = targetStep;
                showStep(currentStep);
            } else if (targetStep > currentStep) {
                // If trying to jump forward, validate completion
                if (targetStep === 2 && !selectedTimeId) {
                    return;
                }
                if (targetStep === 3) {
                    if (!selectedTimeId || !selectedBarberId) {
                        return;
                    }
                }
                
                // If moving from step 1 directly to 2 or 3, load barbers if not loaded
                if (currentStep === 1 && targetStep >= 2) {
                    loadAvailableBarbers();
                }

                currentStep = targetStep;
                showStep(currentStep);
            }
        };

        window.handleMobileBack = () => {
            if (currentStep > 1) {
                prevStep();
            } else {
                closeBookingModal();
            }
        };

        // Open/Close Modal
        window.openBookingModal = () => {
            if (selectedServices.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Por favor selecciona al menos un servicio.', background: '#111', color: '#fff' });
                return;
            }

            currentStep = 1;
            selectedDateISO = null;
            selectedDateDisplay = '';
            selectedTimeId = null;
            selectedTimeDisplay = '';
            selectedBarberId = null;
            selectedBarberName = '';

            updateSummary();

            // Reset barber selection
            document.querySelectorAll('.barber-card').forEach(c => c.classList.remove('selected', 'border-white', 'shadow-md'));

            // Reset date picker to force re-render
            document.getElementById('date-picker').innerHTML = '';
            document.getElementById('time-grid-manana').innerHTML = '';
            document.getElementById('time-grid-tarde').innerHTML = '';
            document.getElementById('time-grid-noche').innerHTML = '';
            document.getElementById('time-container').classList.add('hidden');

            showStep(1);

            modal.classList.remove('hidden');
            modal.classList.remove('modal-leave');
            modal.classList.add('flex');
            modal.classList.add('modal-enter');
            document.body.style.overflow = 'hidden';
        };

        window.closeBookingModal = () => {
            modal.classList.remove('modal-enter');
            modal.classList.add('modal-leave');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        };

        // Barber Selection
        document.querySelectorAll('.barber-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.barber-card').forEach(c => {
                    c.classList.remove('selected', 'bg-white/10');
                    c.querySelector('.checkmark').classList.add('hidden');
                });
                card.classList.add('selected', 'bg-white/10');
                card.querySelector('.checkmark').classList.remove('hidden');

                selectedBarberId = card.dataset.id;
                selectedBarberName = card.dataset.name;
                updateSummary();

                setNextBtnEnabled(true);
            });
        });

        // Date Picker
        function initDatePicker() {
            const picker = document.getElementById('date-picker');
            const monthDisplay = document.getElementById('month-display');
            if (picker.children.length > 0) return; // already init

            const today = new Date();
            const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

            monthDisplay.textContent = months[today.getMonth()] + ' ' + today.getFullYear();

            for (let i = 0; i < 14; i++) {
                const date = new Date();
                date.setDate(today.getDate() + i);

                const dayName = days[date.getDay()];
                const dayNum = date.getDate();
                const monthName = months[date.getMonth()];

                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                const isoDate = `${yyyy}-${mm}-${dd}`;

                const dateBtn = document.createElement('div');
                dateBtn.className = 'date-btn w-[46px] py-2 rounded-full flex flex-col items-center justify-center gap-0.5 cursor-pointer transition-all shrink-0 border border-transparent hover:border-white/20';
                dateBtn.innerHTML = `
                    <span class="day-name text-[11px] font-medium text-gray-500 tracking-wide">${dayName}</span>
                    <span class="day-num text-lg font-bold text-white">${String(dayNum).padStart(2, '0')}</span>
                `;

                dateBtn.onclick = () => {
                    document.querySelectorAll('.date-btn').forEach(d => {
                        d.classList.remove('bg-white');
                        const nameSpan = d.querySelector('.day-name');
                        nameSpan.classList.remove('text-gray-800', 'font-bold');
                        nameSpan.classList.add('text-gray-500', 'font-medium');
                        const numSpan = d.querySelector('.day-num');
                        numSpan.classList.remove('text-black');
                        numSpan.classList.add('text-white');
                    });
                    
                    dateBtn.classList.add('bg-white');
                    const nameSpan = dateBtn.querySelector('.day-name');
                    nameSpan.classList.remove('text-gray-500', 'font-medium');
                    nameSpan.classList.add('text-gray-800', 'font-bold');
                    const numSpan = dateBtn.querySelector('.day-num');
                    numSpan.classList.remove('text-white');
                    numSpan.classList.add('text-black');

                    selectedDateISO = isoDate;
                    selectedDateDisplay = `${dayNum} de ${monthName.toLowerCase()} de ${yyyy}`;

                    selectedTimeId = null;
                    selectedTimeDisplay = '';
                    selectedBarberId = null;
                    selectedBarberName = '';

                    updateSummary();
                    setNextBtnEnabled(false);

                    loadAvailableTimes(selectedDateISO);
                };
                picker.appendChild(dateBtn);

                if (i === 0) {
                    dateBtn.click();
                }
            }
        }

        function loadAvailableTimes(fecha) {
            const container = document.getElementById('time-container');
            const gridM = document.getElementById('time-grid-manana');
            const gridT = document.getElementById('time-grid-tarde');
            const gridN = document.getElementById('time-grid-noche');

            const loading = document.getElementById('time-loading');
            const empty = document.getElementById('time-empty');

            gridM.innerHTML = '';
            gridT.innerHTML = '';
            gridN.innerHTML = '';
            container.classList.add('hidden');
            loading.classList.remove('hidden');
            empty.classList.add('hidden');

            fetch(`api/api_horarios_general.php?fecha=${fecha}`)
                .then(res => res.json())
                .then(data => {
                    loading.classList.add('hidden');
                    const disponibles = data.filter(h => !h.ocupado);

                    if (disponibles.length === 0) {
                        empty.classList.remove('hidden');
                        return;
                    }

                    container.classList.remove('hidden');

                    let countM = 0, countT = 0, countN = 0;

                    disponibles.forEach(h => {
                        const timeBtn = document.createElement('div');
                        timeBtn.className = 'time-slot-btn px-4 py-2 rounded-lg border border-white/10 bg-[#111] flex items-center justify-center cursor-pointer hover:border-white transition-all active:scale-95 group text-sm font-bold text-white';
                        timeBtn.innerHTML = `<span>${h.hora_display.toLowerCase()}</span>`;

                        timeBtn.onclick = () => {
                            document.querySelectorAll('.time-slot-btn').forEach(t => {
                                t.classList.remove('border-white', 'bg-white', 'text-black');
                                t.classList.add('border-white/10', 'bg-[#111]', 'text-white');
                            });
                            timeBtn.classList.add('border-white', 'bg-white', 'text-black');
                            timeBtn.classList.remove('border-white/10', 'bg-[#111]', 'text-white');

                            selectedTimeId = h.id;
                            selectedTimeDisplay = h.hora_display;

                            // Reset barber
                            selectedBarberId = null;
                            selectedBarberName = '';

                            updateSummary();
                            setNextBtnEnabled(true);
                        };

                        if (h.turno === 'Mañana') {
                            gridM.appendChild(timeBtn);
                            countM++;
                        } else if (h.turno === 'Tarde') {
                            gridT.appendChild(timeBtn);
                            countT++;
                        } else {
                            gridN.appendChild(timeBtn);
                            countN++;
                        }
                    });

                    gridM.parentElement.style.display = countM > 0 ? 'block' : 'none';
                    gridT.parentElement.style.display = countT > 0 ? 'block' : 'none';
                    gridN.parentElement.style.display = countN > 0 ? 'block' : 'none';
                })
                .catch(err => {
                    loading.classList.add('hidden');
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los horarios.', background: '#111', color: '#fff' });
                });
        }

        function loadAvailableBarbers() {
            const loading = document.getElementById('barber-loading');
            const empty = document.getElementById('barber-empty');
            const grid = document.getElementById('barber-grid');
            const cards = document.querySelectorAll('.barber-card');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');
            grid.classList.add('hidden');
            setNextBtnEnabled(false);

            // Reset selection
            selectedBarberId = null;
            selectedBarberName = '';
            cards.forEach(c => {
                c.classList.remove('selected', 'bg-white/10');
                c.querySelector('.checkmark').classList.add('hidden');
            });
            updateSummary();

            fetch(`api/api_barberos_disponibles.php?fecha=${selectedDateISO}&horario_id=${selectedTimeId}`)
                .then(res => res.json())
                .then(availableIds => {
                    loading.classList.add('hidden');

                    if (availableIds.length === 0) {
                        empty.classList.remove('hidden');
                        return;
                    }

                    grid.classList.remove('hidden');

                    // Show only available barbers
                    cards.forEach(card => {
                        const bId = card.dataset.id;
                        if (availableIds.includes(bId) || availableIds.includes(parseInt(bId))) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                })
                .catch(err => {
                    loading.classList.add('hidden');
                });
        }

        window.findNextAvailableDay = () => {
            const buttons = document.querySelectorAll('.date-btn');
            let foundSelected = false;
            let nextButton = null;

            for (let i = 0; i < buttons.length; i++) {
                if (buttons[i].classList.contains('bg-white')) {
                    foundSelected = true;
                } else if (foundSelected) {
                    nextButton = buttons[i];
                    break;
                }
            }

            if (nextButton) {
                // Smooth scroll to the next button
                const picker = document.getElementById('date-picker');
                picker.scrollTo({
                    left: nextButton.offsetLeft - picker.offsetLeft - 50,
                    behavior: 'smooth'
                });
                nextButton.click();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Agenda completada',
                    text: 'No hemos cargado más fechas disponibles en el sistema. Intenta nuevamente más adelante.',
                    background: '#111',
                    color: '#fff',
                    confirmButtonColor: '#374151'
                });
            }
        };

        // Form Submit
        window.submitBooking = () => {
            const form = document.getElementById('booking-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const payload = {
                nombre: document.getElementById('client-name').value,
                apellido: document.getElementById('client-lastname').value,
                email: document.getElementById('client-email').value,
                telefono: document.getElementById('client-phone').value,
                observaciones: document.getElementById('client-obs').value,
                barbero_id: selectedBarberId,
                servicios: selectedServices, // Send the full array of services
                fecha: selectedDateISO,
                horario_id: selectedTimeId
            };

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2">progress_activity</span>...';

            fetch('api/api_citas_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        closeBookingModal();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Reserva Confirmada!',
                            text: 'Te esperamos en Cut Level.',
                            background: '#111', color: '#fff',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            window.location.href = 'inicio';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Ups', text: data.error, background: '#111', color: '#fff' });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Confirmar Cita';
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un error de conexión.', background: '#111', color: '#fff' });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Confirmar Cita';
                });
        };
    </script>
    <?php include '../app/components/security_shield.php'; ?>
</body>

</html>