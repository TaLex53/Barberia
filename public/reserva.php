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
            background: #050505;
            color: #fff;
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
            background: #000000;
        }

        ::-webkit-scrollbar-thumb {
            background: #333333;
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
            background-color: #ffffff;
            color: #000;
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

<body class="selection:bg-white selection:text-black">

    <!-- Header Navigation -->
    <nav
        class="w-full bg-[#080808] border-b border-white/10 px-6 py-4 flex justify-between items-center sticky top-0 z-40">
        <a href="inicio" class="flex items-center gap-4 group">
            <img src="assets/img/cutlevel.png" class="h-10 w-auto group-hover:scale-105 transition-transform"
                alt="Cut Level">
        </a>
        <a href="inicio"
            class="flex items-center gap-2 text-white/50 hover:text-white text-[10px] font-bold uppercase tracking-widest transition-colors">
            <span class="material-symbols-outlined text-sm">close</span> Volver al inicio
        </a>
    </nav>

    <!-- Studio Profile Section -->
    <section class="max-w-[1400px] mx-auto px-4 sm:px-6 mt-8 mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Banner & Info -->
            <div
                class="lg:col-span-8 bg-[#0a0a0a] rounded-3xl border border-white/5 overflow-hidden shadow-2xl relative">
                <!-- Cover Image -->
                <div class="h-48 sm:h-64 bg-black relative w-full overflow-hidden">
                    <img src="assets/img/salon.webp" alt="Cut Level Salón"
                        class="w-full h-full object-cover opacity-90 transition-transform duration-700 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-[#0a0a0a]/30 to-transparent"></div>
                </div>

                <!-- Profile Content -->
                <div class="px-8 pb-8 relative -mt-16">
                    <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-end">
                        <div
                            class="w-32 h-32 rounded-2xl bg-black border-4 border-[#0a0a0a] shadow-xl overflow-hidden flex-shrink-0 z-10 flex items-center justify-center p-2">
                            <img src="assets/img/cutlevel.png" alt="Cut Level Logo" class="w-full object-contain">
                        </div>
                        <div class="pb-2 space-y-1 z-10">
                            <h1 class="text-3xl sm:text-4xl font-heading tracking-wide text-white uppercase">Cut Level
                                Barbería</h1>
                            <div class="flex items-center gap-2 text-yellow-500 text-sm">
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
                                <span class="text-white/80 font-semibold ml-1">5.0 <span
                                        class="text-white/40 font-normal underline cursor-pointer text-xs">(Excelencia)</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-slate-300 text-sm leading-relaxed max-w-2xl font-medium">
                        Elevamos el cuidado masculino en el sur. <br><br>
                        Más que un servicio tradicional, en Cut Level creamos un ritual de distinción en Puerto Varas.
                        Combinamos técnicas de vanguardia, un ambiente exclusivo y un cuidado riguroso por el detalle
                        para el hombre contemporáneo.
                    </div>
                </div>
            </div>

            <!-- Right Column: Map & Barbers -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-[#0a0a0a] rounded-3xl border border-white/5 shadow-2xl relative flex flex-col">
                    <div
                        class="rounded-t-3xl overflow-hidden h-48 opacity-80 hover:opacity-100 transition-opacity shrink-0">
                        <!-- Simulated Map -->
                        <iframe
                            src="https://maps.google.com/maps?q=Av.%20Col%C3%B3n%200600,%20Puerto%20Varas&t=&z=16&ie=UTF8&iwloc=&output=embed"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                    <div class="p-6 pt-6 flex-1 flex flex-col">
                        <ul class="space-y-4 text-[13px] text-slate-300">
                            <li class="flex gap-3 items-center">
                                <span class="material-symbols-outlined text-white/50 text-lg">location_on</span>
                                <span>Av. Colón 0600, Puerto Varas</span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <span class="material-symbols-outlined text-white/50 text-lg">smartphone</span>
                                <span>+56 9 2086 0076</span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <i
                                    class="fa-brands fa-whatsapp text-white/50 text-lg flex items-center justify-center w-[18px]"></i>
                                <a href="https://wa.me/56920860076" target="_blank"
                                    class="hover:text-white transition-colors">¡Contáctanos por Whatsapp!</a>
                            </li>
                            <li class="flex gap-3 items-center relative group">
                                <span class="material-symbols-outlined text-white/50 text-lg">schedule</span>
                                <span class="underline hover:text-white cursor-pointer">Ver horario de atención</span>

                                <!-- Popover Schedule -->
                                <div
                                    class="absolute left-8 top-full mt-2 w-56 bg-white rounded-xl shadow-2xl p-4 text-slate-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 transform origin-top-left scale-95 group-hover:scale-100">
                                    <ul class="space-y-3 text-[13px] font-bold">
                                        <li class="flex justify-between border-b border-slate-100 pb-2">
                                            <span>Lunes</span> <span class="text-slate-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-slate-100 pb-2">
                                            <span>Martes</span> <span class="text-slate-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-slate-100 pb-2">
                                            <span>Miércoles</span> <span class="text-slate-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-slate-100 pb-2">
                                            <span>Jueves</span> <span class="text-slate-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-slate-100 pb-2">
                                            <span>Viernes</span> <span class="text-slate-500 font-medium">08:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between border-b border-slate-100 pb-2">
                                            <span>Sábado</span> <span class="text-slate-500 font-medium">11:00 -
                                                20:00</span>
                                        </li>
                                        <li class="flex justify-between"><span>Domingo</span> <span
                                                class="text-slate-500 font-medium">11:00 - 20:00</span></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>

                        <hr class="border-white/10 my-6">

                        <!-- Profesionales Card -->
                        <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-white/70 mb-6">Profesionales</h3>
                        <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                            <?php foreach ($barberos as $b): ?>
                                <div class="flex flex-col items-center gap-2 min-w-[70px]">
                                    <div
                                        class="w-14 h-14 rounded-full bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden">
                                        <?php if (!empty($b['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($b['foto']); ?>"
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-2xl text-white/50">person</span>
                                        <?php endif; ?>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold uppercase text-white/80 tracking-wider"><?php echo htmlspecialchars($b['nombre']); ?></span>
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
                <div class="sticky top-24 bg-[#0a0a0a] rounded-3xl border border-white/5 p-6 shadow-2xl">

                    <!-- Search Bar -->
                    <div class="relative mb-6">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/40 text-[20px]">search</span>
                        <input type="text" id="service-search" placeholder="¿Qué servicio buscas?"
                            onkeyup="filterServices(this.value)"
                            class="w-full bg-transparent border border-white/20 rounded-full pl-12 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-white/50 transition-all placeholder:text-white/30">
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
                                    class="block px-4 py-3.5 text-sm font-medium text-slate-400 hover:text-white transition-colors">
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
                        <div class="bg-[#1a1a1a] hover:bg-[#252525] border border-white/5 rounded-xl px-6 py-4 flex items-center justify-between cursor-pointer transition-colors"
                            onclick="toggleCategory(<?php echo $catIndex; ?>)">
                            <h2 class="text-[15px] font-semibold text-white/90"><?php echo htmlspecialchars($categoria); ?>
                            </h2>
                            <span id="icon-cat-<?php echo $catIndex; ?>"
                                class="text-white/50 text-2xl font-light leading-none transition-transform duration-300">+</span>
                        </div>
                        <!-- Services Grid -->
                        <div id="grid-cat-<?php echo $catIndex; ?>"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 mb-2 hidden">
                            <?php foreach ($servicios_cat as $s): ?>
                                <div
                                    class="service-card bg-[#121212] border border-white/10 rounded-2xl p-6 flex flex-col justify-between hover:border-white/30 transition-all shadow-lg group">
                                    <div>
                                        <h3 class="text-base font-bold text-white mb-2">
                                            <?php echo htmlspecialchars($s['nombre']); ?>
                                        </h3>
                                        <div class="flex flex-col gap-0.5 mb-4">
                                            <span
                                                class="text-xs text-slate-400 font-medium"><?php echo $s['duracion_minutos']; ?>
                                                min</span>
                                            <span
                                                class="text-base font-black text-white">$<?php echo number_format($s['precio'], 0, ',', '.'); ?></span>
                                        </div>
                                        <?php if (!empty($s['descripcion'])): ?>
                                            <p class="text-xs text-slate-400 line-clamp-2 mb-2 leading-relaxed">
                                                <?php echo htmlspecialchars($s['descripcion']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <button type="button"
                                            class="text-xs font-semibold text-white/50 hover:text-white underline transition-colors mb-6 text-left"
                                            onclick="Swal.fire({title: '<?php echo addslashes($s['nombre']); ?>', text: '<?php echo addslashes($s['descripcion'] ?? ''); ?>', background: '#111', color: '#fff', confirmButtonColor: '#3b82f6'})">Más
                                            información</button>
                                    </div>
                                    <div class="flex justify-end mt-auto">
                                        <button
                                            class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold px-5 py-2.5 rounded-lg border border-white/5 transition-all w-full sm:w-auto"
                                            onclick="openBookingModal(<?php echo $s['id']; ?>, '<?php echo addslashes($s['nombre']); ?>', <?php echo $s['duracion_minutos']; ?>, <?php echo $s['precio']; ?>)">
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
    <div id="booking-modal"
        class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-md hidden items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <div
            class="bg-[#0a0a0a] border border-white/10 rounded-[2.5rem] shadow-2xl w-full max-w-4xl relative overflow-hidden flex flex-col max-h-[90vh]">

            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-white/10 shrink-0">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-white text-3xl"
                        style="font-variation-settings: 'FILL' 1;">content_cut</span>
                    <h2 class="text-xl font-heading text-white uppercase tracking-wide">Agendar Cita</h2>
                </div>
                <button onclick="closeBookingModal()"
                    class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-white/10 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Modal Body: 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 h-full overflow-hidden">

                <!-- Left Column: Steps -->
                <div class="lg:col-span-8 p-6 sm:p-10 overflow-y-auto no-scrollbar border-r border-white/5">

                    <!-- Stepper Progress -->
                    <div class="flex items-center justify-center mb-10 max-w-lg mx-auto">
                        <div class="flex items-center step-item relative">
                            <div id="indicator-1"
                                class="w-10 h-10 rounded-full bg-white text-black flex items-center justify-center font-black text-sm transition-all shadow-[0_0_20px_rgba(255,255,255,0.4)]">
                                1</div>
                            <div class="w-10 sm:w-16 h-1 bg-white/20 mx-2"></div>
                        </div>
                        <div class="flex items-center step-item relative">
                            <div id="indicator-2"
                                class="w-10 h-10 rounded-full border-2 border-white/20 text-white/40 flex items-center justify-center font-bold text-sm transition-all">
                                2</div>
                            <div class="w-10 sm:w-16 h-1 bg-white/20 mx-2"></div>
                        </div>
                        <div class="flex items-center step-item relative">
                            <div id="indicator-3"
                                class="w-10 h-10 rounded-full border-2 border-white/20 text-white/40 flex items-center justify-center font-bold text-sm transition-all">
                                3</div>
                        </div>
                    </div>

                    <!-- STEP 1: DATE & TIME -->
                    <div id="step-1" class="booking-step">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-heading text-white uppercase tracking-wide mb-1">Fecha y Hora
                                </h2>
                                <p class="text-slate-400 text-sm">Selecciona tu horario disponible.</p>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar" id="date-picker">
                                <!-- JS Generated Dates -->
                            </div>

                            <div>
                                <div id="time-loading" class="text-slate-400 text-xs font-bold uppercase hidden">
                                    Buscando horarios...</div>
                                <div id="time-empty" class="text-red-400 text-xs font-bold uppercase hidden">Día sin
                                    disponibilidad.</div>

                                <div id="time-container" class="space-y-6 hidden">
                                    <div>
                                        <h3
                                            class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <span class="w-8 h-px bg-white/10"></span> Mañana <span
                                                class="grow h-px bg-white/10"></span>
                                        </h3>
                                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3"
                                            id="time-grid-manana"></div>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <span class="w-8 h-px bg-white/10"></span> Tarde <span
                                                class="grow h-px bg-white/10"></span>
                                        </h3>
                                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3"
                                            id="time-grid-tarde"></div>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <span class="w-8 h-px bg-white/10"></span> Noche <span
                                                class="grow h-px bg-white/10"></span>
                                        </h3>
                                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3"
                                            id="time-grid-noche"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: BARBERO -->
                    <div id="step-2" class="booking-step hidden">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-heading text-white uppercase tracking-wide mb-1">Profesional
                                </h2>
                                <p class="text-slate-400 text-sm">¿Con quién deseas atenderte?</p>
                            </div>
                            <button onclick="prevStep()"
                                class="text-xs font-bold text-white/50 uppercase tracking-widest hover:text-white flex items-center"><span
                                    class="material-symbols-outlined text-sm mr-1">arrow_back</span> Atrás</button>
                        </div>

                        <div id="barber-loading" class="text-slate-400 text-xs font-bold uppercase hidden">Buscando
                            profesionales...</div>
                        <div id="barber-empty" class="text-red-400 text-xs font-bold uppercase hidden">Nadie disponible
                            a esta hora.</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="barber-grid">
                            <?php foreach ($barberos as $b): ?>
                                <div class="selectable-card barber-card cursor-pointer rounded-2xl p-5 border border-white/5 bg-white/5 hover:bg-white/10 transition-all duration-300 relative"
                                    data-id="<?php echo $b['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($b['nombre'] . ' ' . $b['apellido']); ?>">
                                    <div
                                        class="checkmark hidden absolute top-3 right-3 w-5 h-5 rounded-full bg-white flex items-center justify-center">
                                        <span
                                            class="material-symbols-outlined text-black text-[12px] font-black">done</span>
                                    </div>
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div
                                            class="w-14 h-14 rounded-full bg-[#111] border border-white/10 flex items-center justify-center overflow-hidden">
                                            <?php if (!empty($b['foto'])): ?>
                                                <img src="<?php echo htmlspecialchars($b['foto']); ?>"
                                                    class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-2xl text-white/50">person</span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="text-white font-bold text-xs uppercase tracking-wide">
                                            <?php echo htmlspecialchars($b['nombre']); ?>
                                        </h4>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- STEP 3: CONTACT INFO -->
                    <div id="step-3" class="booking-step hidden">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-3xl font-heading text-white uppercase tracking-wide mb-1">Tus Datos</h2>
                                <p class="text-slate-400 text-sm">Para confirmar la reserva.</p>
                            </div>
                            <button onclick="prevStep()"
                                class="text-xs font-bold text-white/50 uppercase tracking-widest hover:text-white flex items-center"><span
                                    class="material-symbols-outlined text-sm mr-1">arrow_back</span> Atrás</button>
                        </div>

                        <form id="booking-form" class="space-y-5" onsubmit="event.preventDefault(); submitBooking();">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-2">Nombre</label>
                                    <input type="text" id="client-name" required
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-2">Apellido</label>
                                    <input type="text" id="client-lastname" required
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-2">Email</label>
                                    <input type="email" id="client-email" required
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-2">Teléfono</label>
                                    <input type="tel" id="client-phone" required placeholder="+56 9 1234 5678"
                                        oninput="formatChileanPhone(this)"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-2">Notas
                                    / Observaciones (Opcional)</label>
                                <textarea id="client-obs" rows="2"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-1 focus:ring-white focus:border-white transition-all outline-none"></textarea>
                            </div>

                            <div class="mt-6 flex flex-col gap-4 border-t border-white/10 pt-6">
                                <label class="flex items-start gap-4 cursor-pointer group">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0">
                                        <input type="checkbox" required id="client-terms"
                                            class="peer appearance-none w-6 h-6 border-2 border-white/40 rounded-md bg-black checked:bg-white checked:border-white transition-all cursor-pointer shadow-[0_0_10px_rgba(255,255,255,0.1)] group-hover:border-white/60">
                                        <span
                                            class="material-symbols-outlined absolute text-black text-[16px] opacity-0 peer-checked:opacity-100 pointer-events-none font-black">check</span>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-white/90 leading-relaxed group-hover:text-white transition-colors">
                                        Acepto las Políticas de reserva y privacidad de Cut Level y recibir e-mails,
                                        mensajes de WhatsApp y otras comunicaciones.
                                    </span>
                                </label>
                                <p class="text-[10px] text-white/50 leading-relaxed pl-10">
                                    Este sitio está protegido por reCAPTCHA y se aplican la <a
                                        href="https://policies.google.com/privacy" target="_blank"
                                        class="underline hover:text-white transition-colors">Política de Privacidad</a>
                                    y las <a href="https://policies.google.com/terms" target="_blank"
                                        class="underline hover:text-white transition-colors">Condiciones de Servicio</a>
                                    de Google.
                                </p>
                            </div>
                        </form>
                    </div>

                </div> <!-- End Left Column -->

                <!-- Right Column: Summary Card -->
                <div class="lg:col-span-4 bg-[#111] p-8 hidden lg:block">
                    <h3 class="text-[11px] font-bold uppercase tracking-widest text-white/40 mb-6">Información de tu
                        servicio</h3>

                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 relative overflow-hidden">
                        <!-- Decorative background -->
                        <div
                            class="absolute -top-12 -right-12 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none">
                        </div>

                        <h4 id="summary-service-name" class="font-bold text-white text-base mb-1">Selecciona un servicio
                        </h4>
                        <div id="summary-price" class="text-lg font-black text-white mb-6">$0</div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-white/40 text-[18px]">calendar_today</span>
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-white/40 tracking-wider">Fecha
                                    </div>
                                    <div id="summary-date" class="text-sm font-medium text-white/90">-</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-white/40 text-[18px]">schedule</span>
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-white/40 tracking-wider">Hora &
                                        Duración</div>
                                    <div id="summary-time" class="text-sm font-medium text-white/90">-</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-white/40 text-[18px]">person</span>
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-white/40 tracking-wider">
                                        Profesional</div>
                                    <div id="summary-barber" class="text-sm font-medium text-white/90">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Right Column -->

            </div> <!-- End Modal Body Grid -->

            <!-- Modal Footer (Action Buttons) -->
            <div class="p-6 border-t border-white/10 bg-[#080808] shrink-0 flex justify-end gap-4">
                <button onclick="closeBookingModal()"
                    class="px-6 py-3 rounded-xl border border-white/10 text-white text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">
                    Cancelar
                </button>
                <button id="next-btn" onclick="nextStep()"
                    class="px-8 py-3 rounded-xl bg-white text-black text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all shadow-[0_0_20px_rgba(255,255,255,0.3)] hidden">
                    Continuar
                </button>
                <button id="submit-btn" onclick="submitBooking()"
                    class="px-8 py-3 rounded-xl bg-white text-black text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all shadow-[0_0_20px_rgba(255,255,255,0.3)] hidden">
                    Confirmar Cita
                </button>
            </div>

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

        let selectedServiceId = null;
        let selectedServiceName = '';
        let selectedServiceDuration = 0;
        let selectedServicePrice = 0;

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
            sumService.textContent = selectedServiceName;
            sumPrice.textContent = '$' + new Intl.NumberFormat('es-CL').format(selectedServicePrice);
            sumDate.textContent = selectedDateDisplay || '-';
            sumTime.textContent = selectedTimeDisplay ? selectedTimeDisplay + ' (' + selectedServiceDuration + ' min)' : '-';
            sumBarber.textContent = selectedBarberName || '-';
        }

        // Step Navigation
        function updateIndicators() {
            for (let i = 1; i <= 3; i++) {
                const ind = document.getElementById('indicator-' + i);
                if (i < currentStep) {
                    ind.className = 'w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center font-bold text-sm transition-all';
                    ind.innerHTML = '<span class="material-symbols-outlined text-sm font-black">check</span>';
                } else if (i === currentStep) {
                    ind.className = 'w-10 h-10 rounded-full bg-white text-black flex items-center justify-center font-black text-sm transition-all shadow-[0_0_20px_rgba(255,255,255,0.4)]';
                    ind.innerHTML = i;
                } else {
                    ind.className = 'w-10 h-10 rounded-full border-2 border-white/20 text-white/40 flex items-center justify-center font-bold text-sm transition-all';
                    ind.innerHTML = i;
                }
            }
        }

        function showStep(step) {
            document.querySelectorAll('.booking-step').forEach(el => el.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');

            // Buttons logic
            nextBtn.classList.add('hidden');
            submitBtn.classList.add('hidden');

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
        window.openBookingModal = (id, name, duration, price) => {
            selectedServiceId = id;
            selectedServiceName = name;
            selectedServiceDuration = duration;
            selectedServicePrice = price;

            currentStep = 1;
            selectedDateISO = null;
            selectedDateDisplay = '';
            selectedTimeId = null;
            selectedTimeDisplay = '';
            selectedBarberId = null;
            selectedBarberName = '';

            updateSummary();

            // Reset barber selection
            document.querySelectorAll('.barber-card').forEach(c => c.classList.remove('selected', 'border-white', 'bg-white/20'));

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
                    c.classList.remove('selected', 'border-white', 'bg-white/20');
                    c.querySelector('.checkmark').classList.add('hidden');
                });
                card.classList.add('selected', 'border-white', 'bg-white/20');
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
            if (picker.children.length > 0) return; // already init

            const today = new Date();
            const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

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
                dateBtn.className = 'min-w-[80px] h-24 rounded-2xl border border-white/10 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all hover:bg-white/5 active:scale-95 shrink-0';
                dateBtn.innerHTML = `
                    <span class="text-[9px] font-black uppercase text-white/40 tracking-widest">${dayName}</span>
                    <span class="text-2xl font-heading text-white">${dayNum}</span>
                    <span class="text-[9px] font-bold uppercase text-white">${monthName}</span>
                `;

                dateBtn.onclick = () => {
                    document.querySelectorAll('#date-picker > div').forEach(d => d.classList.remove('border-white', 'bg-white/15', 'scale-105'));
                    dateBtn.classList.add('border-white', 'bg-white/15', 'scale-105');

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
                        timeBtn.className = 'time-slot-btn h-10 rounded-xl border border-white/10 bg-white/5 flex flex-col items-center justify-center cursor-pointer hover:border-white transition-all active:scale-95 group';
                        timeBtn.innerHTML = `<span class="text-white font-bold text-xs group-hover:text-black transition-colors">${h.hora_display}</span>`;

                        timeBtn.onclick = () => {
                            document.querySelectorAll('.time-slot-btn').forEach(t => {
                                t.classList.remove('border-white', 'bg-white', 'scale-105');
                                t.querySelector('span').classList.remove('text-black');
                                t.querySelector('span').classList.add('text-white');
                            });
                            timeBtn.classList.add('border-white', 'bg-white', 'scale-105');
                            timeBtn.querySelector('span').classList.remove('text-white');
                            timeBtn.querySelector('span').classList.add('text-black');

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
                servicio_id: selectedServiceId,
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