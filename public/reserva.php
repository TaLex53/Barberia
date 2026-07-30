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
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                            <div class="flex items-center gap-3 text-sm text-gray-400 relative group">
                                <span class="material-symbols-outlined text-gray-500 text-[18px]">schedule</span>
                                <span class="underline text-gray-400 hover:text-white cursor-pointer">Ver horario de
                                    atención</span>

                                <!-- Popover Schedule -->
                                <div
                                    class="absolute left-8 top-full mt-2 w-56 bg-[#0a0a0a] rounded-xl shadow-xl p-4 text-gray-300 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 transform origin-top-left scale-95 group-hover:scale-100 border border-white/10">
                                    <ul class="space-y-3 text-[13px] font-bold">
                                        <li class="flex justify-between border-b border-white/5 pb-2">
                                            <span>Lunes</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-white/5 pb-2">
                                            <span>Martes</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-white/5 pb-2">
                                            <span>Miércoles</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-white/5 pb-2">
                                            <span>Jueves</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-white/5 pb-2">
                                            <span>Viernes</span> <span class="text-gray-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-white/5 pb-2">
                                            <span>Sábado</span> <span class="text-gray-500 font-medium">11:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between"><span>Domingo</span> <span
                                                class="text-gray-500 font-medium">11:00 - 20:00</span></li>
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
                                        <button type="button"
                                            class="text-xs font-semibold text-gray-300 hover:text-white underline transition-colors mb-6 text-left"
                                            onclick="Swal.fire({title: '<?php echo addslashes($s['nombre']); ?>', text: '<?php echo addslashes($s['descripcion'] ?? ''); ?>', background: '#111', color: '#fff', confirmButtonColor: '#fff', confirmButtonText: '<span style=\'color:#000\'>OK</span>'})">Más
                                            información</button>
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
    <div id="booking-modal" class="fixed inset-0 z-[100] bg-[#e5e7eb] hidden flex-col w-full h-full">

        <!-- Modal Header -->
        <div class="bg-white flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
            <div class="flex items-center gap-4">
                <!-- Using an image or text for the logo -->
                <img src="assets/img/cutlevel_reserva.png" alt="Cut Level" class="h-8 object-contain hidden sm:block">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Cut Level Studio</h2>
                    <p class="text-xs text-gray-500">Sucursal Puerto Varas</p>
                </div>
            </div>
            <button onclick="closeBookingModal()"
                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body: 2 Columns -->
        <div class="flex-grow flex flex-col lg:flex-row max-w-[1400px] w-full mx-auto gap-6 p-4 sm:p-8 overflow-hidden">

            <!-- Left Column: Steps -->
            <div
                class="lg:w-8/12 flex flex-col bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden h-full">

                <div class="px-6 py-5 border-b border-gray-100 shrink-0">
                    <h2 class="text-lg font-bold text-gray-800" id="step-title">Selecciona fecha y hora de tu servicio
                    </h2>
                </div>

                <!-- Stepper Progress Tabs -->
                <div class="flex items-center justify-between border-b border-gray-200 px-6 shrink-0">
                    <button
                        class="step-tab flex-1 py-4 text-sm font-bold flex justify-center items-center gap-2 border-b-2 transition-all border-[#111827] text-[#111827]"
                        id="tab-1">
                        <span
                            class="w-5 h-5 rounded-full border border-current flex items-center justify-center text-[10px]">1</span>
                        Fecha y hora
                    </button>
                    <button
                        class="step-tab flex-1 py-4 text-sm font-bold flex justify-center items-center gap-2 border-b-2 transition-all border-transparent text-gray-400"
                        id="tab-2">
                        <span
                            class="w-5 h-5 rounded-full border border-current flex items-center justify-center text-[10px]">2</span>
                        Profesional
                    </button>
                    <button
                        class="step-tab flex-1 py-4 text-sm font-bold flex justify-center items-center gap-2 border-b-2 transition-all border-transparent text-gray-400"
                        id="tab-3">
                        <span
                            class="w-5 h-5 rounded-full border border-current flex items-center justify-center text-[10px]">3</span>
                        Datos de contacto
                    </button>
                </div>

                <div class="p-6 overflow-y-auto no-scrollbar grow">
                    <!-- STEP 1: DATE & TIME -->
                    <div id="step-1" class="booking-step">
                        <div class="space-y-8">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-sm font-bold text-gray-800" id="month-display">Mes</div>
                                </div>
                                <div class="flex gap-2 overflow-x-auto pb-4 no-scrollbar items-center" id="date-picker">
                                    <!-- JS Generated Dates -->
                                </div>
                            </div>

                            <div>
                                <div id="time-loading" class="text-gray-400 text-xs font-bold uppercase hidden">Buscando
                                    horarios...</div>
                                <div id="time-empty" class="text-red-500 text-xs font-bold uppercase hidden">Día sin
                                    disponibilidad.</div>

                                <div id="time-container" class="space-y-6 hidden">
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-start gap-4 border-t border-gray-100 pt-6">
                                        <div class="sm:w-20 shrink-0 mt-2">
                                            <span class="text-xs italic text-gray-500 font-serif">Mañana</span>
                                        </div>
                                        <div class="flex flex-wrap gap-3" id="time-grid-manana"></div>
                                    </div>
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-start gap-4 border-t border-gray-100 pt-6">
                                        <div class="sm:w-20 shrink-0 mt-2">
                                            <span class="text-xs italic text-gray-500 font-serif">Tarde</span>
                                        </div>
                                        <div class="flex flex-wrap gap-3" id="time-grid-tarde"></div>
                                    </div>
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-start gap-4 border-t border-gray-100 pt-6">
                                        <div class="sm:w-20 shrink-0 mt-2">
                                            <span class="text-xs italic text-gray-500 font-serif">Noche</span>
                                        </div>
                                        <div class="flex flex-wrap gap-3" id="time-grid-noche"></div>
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="barber-grid">
                            <?php foreach ($barberos as $b): ?>
                                <div class="selectable-card barber-card cursor-pointer rounded-xl p-4 border border-gray-200 bg-white hover:border-[#111827] hover:shadow-md transition-all duration-300 relative"
                                    data-id="<?php echo $b['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($b['nombre'] . ' ' . $b['apellido']); ?>">
                                    <div
                                        class="checkmark hidden absolute top-3 right-3 w-5 h-5 rounded-full bg-[#111827] flex items-center justify-center">
                                        <span
                                            class="material-symbols-outlined text-white text-[12px] font-black">done</span>
                                    </div>
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div
                                            class="w-16 h-16 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                                            <?php if (!empty($b['foto'])): ?>
                                                <img src="<?php echo htmlspecialchars($b['foto']); ?>"
                                                    class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-3xl text-gray-300">person</span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="text-gray-800 font-bold text-sm tracking-wide">
                                            <?php echo htmlspecialchars($b['nombre']); ?>
                                        </h4>
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
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Nombre *</label>
                                    <input type="text" id="client-name" required
                                        class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 text-sm focus:ring-1 focus:ring-[#111827] focus:border-[#111827] transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Apellido *</label>
                                    <input type="text" id="client-lastname" required
                                        class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 text-sm focus:ring-1 focus:ring-[#111827] focus:border-[#111827] transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Email *</label>
                                    <input type="email" id="client-email" required
                                        class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 text-sm focus:ring-1 focus:ring-[#111827] focus:border-[#111827] transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Teléfono *</label>
                                    <input type="tel" id="client-phone" required placeholder="+56 9 1234 5678"
                                        oninput="formatChileanPhone(this)"
                                        class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 text-sm focus:ring-1 focus:ring-[#111827] focus:border-[#111827] transition-all outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Notas / Observaciones
                                    (Opcional)</label>
                                <textarea id="client-obs" rows="2"
                                    class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 text-sm focus:ring-1 focus:ring-[#111827] focus:border-[#111827] transition-all outline-none"></textarea>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 border-t border-gray-100 pt-4">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0">
                                        <input type="checkbox" required id="client-terms"
                                            class="peer appearance-none w-5 h-5 border border-gray-300 rounded bg-white checked:bg-[#111827] checked:border-[#111827] transition-all cursor-pointer">
                                        <span
                                            class="material-symbols-outlined absolute text-white text-[14px] opacity-0 peer-checked:opacity-100 pointer-events-none font-bold">check</span>
                                    </div>
                                    <span class="text-xs text-gray-600 leading-relaxed">
                                        Acepto las Políticas de reserva y privacidad de Cut Level.
                                    </span>
                                </label>
                            </div>
                        </form>
                    </div>

                </div>
            </div> <!-- End Left Column -->

            <!-- Right Column: Summary Card -->
            <div class="lg:w-4/12 hidden lg:flex flex-col gap-4">
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 text-center">
                        <h3 class="text-sm font-bold text-gray-700">Información de tus servicios</h3>
                    </div>

                    <div class="p-4">
                        <div class="bg-[#f3f4f6] rounded-lg border border-[#111827]/20 p-5">
                            <h4 id="summary-service-name"
                                class="font-bold text-gray-800 text-sm mb-3 border-b border-gray-300/30 pb-2">Selecciona
                                un servicio</h4>

                            <div class="space-y-2 mt-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500 text-[16px]">payments</span>
                                    <span id="summary-price" class="text-sm font-medium text-gray-700">$0</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="material-symbols-outlined text-gray-500 text-[16px]">calendar_today</span>
                                    <span id="summary-date" class="text-sm font-medium text-gray-700">-</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500 text-[16px]">schedule</span>
                                    <span id="summary-time" class="text-sm font-medium text-gray-700">-</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500 text-[16px]">person</span>
                                    <span id="summary-barber" class="text-sm font-medium text-gray-700">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Right Column -->

        </div> <!-- End Modal Body Grid -->

        <!-- Modal Footer (Action Buttons) -->
        <div class="bg-white border-t border-gray-200 px-6 py-4 shrink-0 flex justify-end gap-3 items-center">
            <button onclick="prevStep()" id="prev-btn"
                class="hidden px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-bold hover:bg-gray-50 transition-colors">
                Atrás
            </button>
            <button id="next-btn" onclick="nextStep()"
                class="px-6 py-2.5 rounded-lg bg-[#96ac9f] text-white text-sm font-bold hover:bg-[#7e9587] transition-colors hidden">
                Siguiente
            </button>
            <button id="submit-btn" onclick="submitBooking()"
                class="px-6 py-2.5 rounded-lg bg-[#111827] text-white text-sm font-bold hover:bg-[#1f2937] transition-colors hidden shadow-md">
                Confirmar Cita
            </button>
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
                <p class="text-[10px] text-gray-500 mt-1">Desarrollado por <span class="font-bold text-gray-400">Cut
                        Level</span></p>
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

        function updateSummary() {
            if (selectedServices.length === 0) {
                sumService.textContent = 'Selecciona un servicio';
                sumPrice.textContent = '$0';
            } else {
                let names = selectedServices.map(s => s.name).join(' + ');
                let total = selectedServices.reduce((acc, s) => acc + s.price, 0);
                let dur = selectedServices.reduce((acc, s) => acc + s.duration, 0);

                sumService.textContent = names;
                sumPrice.textContent = '$' + new Intl.NumberFormat('es-CL').format(total);
                sumTime.textContent = selectedTimeDisplay ? selectedTimeDisplay + ' (' + dur + ' min)' : '-';
            }
            sumDate.textContent = selectedDateDisplay || '-';
            sumBarber.textContent = selectedBarberName || '-';
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

        // Step Navigation
        function updateIndicators() {
            for (let i = 1; i <= 3; i++) {
                const tab = document.getElementById('tab-' + i);
                if (i <= currentStep) {
                    tab.classList.remove('border-transparent', 'text-gray-400');
                    tab.classList.add('border-[#111827]', 'text-[#111827]');
                    tab.querySelector('span').classList.remove('border-current', 'text-[10px]');
                    tab.querySelector('span').classList.add('bg-[#111827]', 'text-white', 'border-transparent');
                    tab.querySelector('span').innerHTML = i < currentStep ? '<span class="material-symbols-outlined text-[12px] font-bold">check</span>' : i;
                } else {
                    tab.classList.remove('border-[#111827]', 'text-[#111827]');
                    tab.classList.add('border-transparent', 'text-gray-400');
                    tab.querySelector('span').classList.remove('bg-[#111827]', 'text-white', 'border-transparent');
                    tab.querySelector('span').classList.add('border-current', 'text-[10px]');
                    tab.querySelector('span').innerHTML = i;
                }
            }
        }

        function showStep(step) {
            document.querySelectorAll('.booking-step').forEach(el => el.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');

            // Buttons logic
            const prevBtn = document.getElementById('prev-btn');
            nextBtn.classList.add('hidden');
            submitBtn.classList.add('hidden');

            if (step > 1) {
                prevBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.add('hidden');
            }

            if (step === 1) {
                initDatePicker();
                if (selectedTimeId) nextBtn.classList.remove('hidden');
            }
            if (step === 2) {
                if (selectedBarberId) nextBtn.classList.remove('hidden');
            }
            if (step === 3) submitBtn.classList.remove('hidden');

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
            document.querySelectorAll('.barber-card').forEach(c => c.classList.remove('selected', 'border-[#111827]', 'shadow-md'));

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
                    c.classList.remove('selected', 'border-[#111827]', 'shadow-md');
                    c.querySelector('.checkmark').classList.add('hidden');
                });
                card.classList.add('selected', 'border-[#111827]', 'shadow-md');
                card.querySelector('.checkmark').classList.remove('hidden');

                selectedBarberId = card.dataset.id;
                selectedBarberName = card.dataset.name;
                updateSummary();

                nextBtn.classList.remove('hidden');
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
                dateBtn.className = 'date-btn min-w-[50px] flex flex-col items-center justify-center gap-2 cursor-pointer transition-all shrink-0 py-2';
                dateBtn.innerHTML = `
                    <span class="text-[11px] font-bold text-gray-400 tracking-wide">${dayName}</span>
                    <div class="day-circle w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-gray-800 transition-colors">
                        ${String(dayNum).padStart(2, '0')}
                    </div>
                `;

                dateBtn.onclick = () => {
                    document.querySelectorAll('.day-circle').forEach(d => {
                        d.classList.remove('bg-[#111827]', 'text-white');
                        d.classList.add('text-gray-800');
                    });
                    const circle = dateBtn.querySelector('.day-circle');
                    circle.classList.add('bg-[#111827]', 'text-white');
                    circle.classList.remove('text-gray-800');

                    selectedDateISO = isoDate;
                    selectedDateDisplay = `${dayNum} de ${monthName.toLowerCase()} de ${yyyy}`;

                    selectedTimeId = null;
                    selectedTimeDisplay = '';
                    selectedBarberId = null;
                    selectedBarberName = '';

                    updateSummary();
                    nextBtn.classList.add('hidden');

                    loadAvailableTimes(selectedDateISO);
                };
                picker.appendChild(dateBtn);
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
                        timeBtn.className = 'time-slot-btn px-4 py-2 rounded-lg border border-gray-300 bg-white flex items-center justify-center cursor-pointer hover:border-[#111827] transition-all active:scale-95 group text-sm font-bold text-gray-700';
                        timeBtn.innerHTML = `<span>${h.hora_display.toLowerCase()}</span>`;

                        timeBtn.onclick = () => {
                            document.querySelectorAll('.time-slot-btn').forEach(t => {
                                t.classList.remove('border-[#111827]', 'bg-[#111827]', 'text-white');
                                t.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
                            });
                            timeBtn.classList.add('border-[#111827]', 'bg-[#111827]', 'text-white');
                            timeBtn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');

                            selectedTimeId = h.id;
                            selectedTimeDisplay = h.hora_display;

                            // Reset barber
                            selectedBarberId = null;
                            selectedBarberName = '';

                            updateSummary();
                            nextBtn.classList.remove('hidden');
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
            nextBtn.classList.add('hidden');

            // Reset selection
            selectedBarberId = null;
            selectedBarberName = '';
            cards.forEach(c => {
                c.classList.remove('selected', 'border-white', 'bg-white/20');
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
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                })
                .catch(err => {
                    loading.classList.add('hidden');
                });
        }

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