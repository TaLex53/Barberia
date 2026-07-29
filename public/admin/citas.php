<?php
session_start();
require_once '../../app/config/conexion.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: barberiasettings");
    exit;
}

// Fetch lists for the modal form
try {
    $barberos = $pdo->query("SELECT id, nombre, apellido FROM barberos WHERE activo = 1")->fetchAll();
    $servicios = $pdo->query("SELECT id, nombre, precio FROM servicios WHERE activo = 1")->fetchAll();
    $horarios = $pdo->query("SELECT id, hora, turno FROM horarios ORDER BY hora ASC")->fetchAll();
} catch (PDOException $e) {
    // Manejo básico
    $barberos = []; $servicios = []; $horarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas y Calendario | Cut Level</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- FullCalendar CSS/JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>

    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-heading { font-family: 'Bebas Neue', cursive; letter-spacing: 0.02em; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #000000; }
        ::-webkit-scrollbar-thumb { background: #333333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #555555; }
        
        .sidebar-collapsed { width: 5rem !important; }
        .sidebar-collapsed .sidebar-text, 
        .sidebar-collapsed .user-info, 
        .sidebar-collapsed .logo-text { display: none; }
        .sidebar-collapsed nav a, 
        .sidebar-collapsed .logout-btn { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed nav a .material-symbols-outlined,
        .sidebar-collapsed .logout-btn .material-symbols-outlined { margin: 0; }

        /* FullCalendar Custom Overrides for Dark Theme */
        :root {
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #0a0a0a;
            --fc-neutral-text-color: #94a3b8; /* slate-400 */
            --fc-border-color: rgba(255, 255, 255, 0.05);
            --fc-button-text-color: #fff;
            --fc-button-bg-color: rgba(255, 255, 255, 0.1);
            --fc-button-border-color: rgba(255, 255, 255, 0.1);
            --fc-button-hover-bg-color: rgba(255, 255, 255, 0.2);
            --fc-button-hover-border-color: rgba(255, 255, 255, 0.2);
            --fc-button-active-bg-color: #fff;
            --fc-button-active-border-color: #fff;
            --fc-button-active-text-color: #000;
            --fc-event-bg-color: rgba(255,255,255,0.1);
            --fc-event-border-color: rgba(255,255,255,0.2);
            --fc-event-text-color: #fff;
            --fc-today-bg-color: rgba(255,255,255,0.02);
            --fc-list-event-hover-bg-color: rgba(255,255,255,0.05);
        }
        .fc-theme-standard .fc-scrollgrid { border: 1px solid var(--fc-border-color); }
        .fc-theme-standard td, .fc-theme-standard th { border: 1px solid var(--fc-border-color); }
        .fc .fc-toolbar-title { font-family: 'Bebas Neue', cursive; font-size: 1.5rem; letter-spacing: 0.05em; text-transform: uppercase; color: #fff; }
        .fc .fc-button { border-radius: 0.5rem; font-family: 'Montserrat', sans-serif; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 1rem; }
        .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { color: #000 !important; }
        .fc .fc-col-header-cell-cushion { color: #fff; padding: 0.5rem; }
        .fc .fc-daygrid-day-number { color: #94a3b8; font-weight: 600; padding: 0.5rem; }
        .fc-daygrid-event { padding: 2px 4px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .fc-timegrid-event { border-radius: 4px; font-size: 0.70rem; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none !important; }
        .fc-timegrid-event .fc-event-main { padding: 2px 4px; display: flex; flex-direction: column; overflow: hidden; line-height: 1.1; }
        .fc-event:hover { opacity: 0.9; transform: scale(1.02); z-index: 50 !important; }
    </style>
</head>
<body class="bg-[#050505] text-slate-100 h-screen overflow-hidden flex selection:bg-white selection:text-black">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-[#0a0a0a] border-r border-white/5 flex flex-col flex-shrink-0 relative z-20 transition-all duration-300">
        <!-- Logo -->
        <div class="h-20 flex items-center justify-center border-b border-white/5 px-4 overflow-hidden">
            <a href="dashboard" class="flex items-center justify-center w-full">
                <img src="../assets/img/cutlevel.png" class="h-10 w-auto max-w-full object-contain transition-all duration-300 logo-img" alt="Logo">
            </a>
        </div>

        <!-- User Info -->
        <div class="p-6 border-b border-white/5 user-info">
            <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-bold mb-1">Bienvenido!</p>
            <p class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars(ucfirst($_SESSION['username'] ?? 'Admin')); ?></p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
            <a href="dashboard" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">home</span>
                <span class="sidebar-text">Inicio</span>
            </a>
            <a href="citas" class="flex items-center gap-3 px-4 py-3 bg-white text-black rounded-xl font-bold text-xs uppercase tracking-[0.1em] shadow-[0_0_20px_rgba(255,255,255,0.15)] transition-all">
                <span class="material-symbols-outlined text-[18px]">event_note</span>
                <span class="sidebar-text">Citas</span>
            </a>
            <a href="servicios" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">design_services</span>
                <span class="sidebar-text">Servicios</span>
            </a>
            <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                <span class="sidebar-text">Reportes</span>
            </a>
            <a href="barberos" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">content_cut</span>
                <span class="sidebar-text">Barberos</span>
            </a>
            <a href="horarios" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">schedule</span>
                <span class="sidebar-text">Horarios</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-white/5">
            <a href="logout" class="logout-btn flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-red-500/10 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                <span class="sidebar-text">Salir</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Topbar -->
        <header class="h-20 bg-[#0a0a0a] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0 relative z-10">
            <div class="flex items-center gap-4">
                <button id="sidebarToggle" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <a href="dashboard" class="block">
                    <h1 class="text-2xl font-heading uppercase tracking-widest text-white mt-1 hover:text-slate-300 transition-colors">Citas</h1>
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 relative">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                <h2 class="text-3xl font-heading uppercase tracking-widest text-white">Calendario de Citas</h2>
                <div class="flex items-center gap-3">
                    <a href="horarios" class="bg-white/10 text-white px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-[0.1em] hover:bg-white/20 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">schedule</span>
                        Horarios
                    </a>
                    <button id="btnNuevaCita" class="bg-white text-black px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-[0.1em] hover:bg-slate-200 transition-colors flex items-center gap-2 shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Agendar Cita
                    </button>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-4 md:p-6 shadow-xl w-full h-[calc(100vh-220px)] min-h-[500px]">
                <div id="calendar" class="w-full h-full"></div>
            </div>

        </div>
    </main>

    <!-- Modal Form (Nueva Cita) -->
    <div id="citaModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center opacity-0 transition-opacity duration-300 overflow-y-auto p-4">
        <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl w-full max-w-4xl p-6 shadow-2xl transform scale-95 transition-transform duration-300 my-auto">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-2xl font-heading text-white tracking-widest uppercase">Agendar Nueva Cita</h3>
                <button id="btnCerrarCita" class="text-slate-500 hover:text-white transition-colors bg-white/5 p-2 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form id="formCita" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Datos del Cliente -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-400">person</span> 
                        Datos del Cliente
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Nombre *</label>
                            <input type="text" id="clienteNombre" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Apellido *</label>
                            <input type="text" id="clienteApellido" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Email *</label>
                            <input type="email" id="clienteEmail" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Teléfono *</label>
                            <input type="tel" id="clienteTelefono" required placeholder="+56 9 1234 5678" oninput="formatChileanPhone(this)" class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">RUT (Opcional)</label>
                            <input type="text" id="clienteRut" class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Nacimiento (Opcional)</label>
                            <input type="date" id="clienteNacimiento" class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Detalles de la Cita -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-400">content_cut</span> 
                        Detalles de la Cita
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Barbero *</label>
                            <select id="citaBarbero" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors appearance-none cursor-pointer">
                                <option value="" disabled selected>Seleccione...</option>
                                <?php foreach($barberos as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nombre'] . ' ' . $b['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Servicio *</label>
                            <select id="citaServicio" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors appearance-none cursor-pointer">
                                <option value="" disabled selected>Seleccione...</option>
                                <?php foreach($servicios as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?> ($<?= number_format($s['precio'], 0, ',', '.') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Fecha de Cita *</label>
                            <input type="date" id="citaFecha" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Hora *</label>
                            <select id="citaHora" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-white/30 transition-colors appearance-none cursor-pointer">
                                <option value="" disabled selected>Seleccione...</option>
                                <?php 
                                $currentTurno = '';
                                foreach($horarios as $h): 
                                    if($currentTurno != $h['turno']) {
                                        if($currentTurno != '') echo '</optgroup>';
                                        echo '<optgroup label="'.$h['turno'].'" class="text-slate-400 uppercase text-xs">';
                                        $currentTurno = $h['turno'];
                                    }
                                    $timeObj = DateTime::createFromFormat('H:i:s', $h['hora']);
                                    $timeStr = $timeObj->format('h:i A');
                                ?>
                                    <option value="<?= $h['id'] ?>"><?= $timeStr ?></option>
                                <?php endforeach; 
                                if($currentTurno != '') echo '</optgroup>';
                                ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Observaciones</label>
                        <textarea id="clienteObservaciones" rows="3" placeholder="Escribe aquí información relevante..." class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors resize-none"></textarea>
                    </div>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-white/10 flex justify-between items-center">
                    <div id="modalStatus" class="text-xs font-bold uppercase tracking-wider"></div>
                    <div class="flex gap-3">
                        <button type="button" id="btnCancelarCita" class="px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl transition-colors">Cancelar</button>
                        <button type="submit" id="btnGuardarCita" class="bg-white text-black px-8 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-200 transition-colors shadow-[0_0_15px_rgba(255,255,255,0.15)]">Agendar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('flex');
            } else {
                sidebar.classList.toggle('sidebar-collapsed');
            }
        });
        if (window.innerWidth < 768) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }

        // FullCalendar Setup
        let calendar;
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridDay',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridDay,rollingWeek,dayGridMonth,listWeek'
                },
                views: {
                    rollingWeek: {
                        type: 'timeGrid',
                        duration: { days: 7 },
                        buttonText: 'Semana'
                    }
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    day: 'Día',
                    list: 'Agenda'
                },
                slotMinTime: "09:00:00",
                slotMaxTime: "21:00:00",
                slotDuration: "00:15:00",
                slotLabelInterval: "01:00",
                expandRows: true,
                eventMinHeight: 40, // Ensures short events (20 mins) are readable
                allDaySlot: false,
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                dayMaxEvents: true, // allow "more" link when too many events
                events: '../api/api_citas_load.php', // Load events from our API
                dateClick: function(info) {
                    // Open modal pre-filling the date
                    document.getElementById('citaFecha').value = info.dateStr;
                    openModal();
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    
                    const optionsDate = { weekday: 'long', day: '2-digit', month: 'long' };
                    const dateStr = info.event.start.toLocaleDateString('es-ES', optionsDate);
                    const startTime = info.event.start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    const endTime = info.event.end ? info.event.end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) : '';
                    const timeRange = endTime ? `${startTime} a ${endTime}` : startTime;

                    // Capitalize first letter of dateStr
                    const formattedDate = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);

                    // Determine color based on status for the circle
                    let statusColor = '#0ea5e9'; // Agendada (default blue)
                    if (props.estado === 'Completada') statusColor = '#10b981'; // Green
                    if (props.estado === 'Cancelada') statusColor = '#ef4444'; // Red

                    Swal.fire({
                        html: `
                            <div class="text-left font-sans mt-2">
                                <h2 class="text-2xl font-bold text-emerald-400 mb-4">${props.nombre} ${props.apellido}</h2>
                                <p class="text-slate-300 text-[15px] mb-1">${formattedDate}</p>
                                <p class="text-slate-300 text-[15px] mb-4">${timeRange}</p>
                                
                                <div class="flex items-center gap-3 mb-6 bg-white/5 p-2 rounded-xl w-max border border-white/5">
                                    <div class="w-3 h-3 rounded-full ml-1" style="background-color: ${statusColor}; box-shadow: 0 0 10px ${statusColor}"></div>
                                    <select onchange="cambiarEstadoCita(${info.event.id}, this.value)" class="bg-transparent text-[15px] font-bold text-white focus:outline-none cursor-pointer pr-4 appearance-none" style="color: ${statusColor}">
                                        <option class="bg-[#111] text-white" value="Agendada" ${props.estado === 'Agendada' || !props.estado ? 'selected' : ''}>Agendada</option>
                                        <option class="bg-[#111] text-emerald-400" value="Completada" ${props.estado === 'Completada' ? 'selected' : ''}>Completada</option>
                                        <option class="bg-[#111] text-red-500" value="Cancelada" ${props.estado === 'Cancelada' ? 'selected' : ''}>Cancelada</option>
                                    </select>
                                    <span class="material-symbols-outlined text-[16px] text-white/50 pointer-events-none -ml-2">expand_more</span>
                                </div>

                                <div class="border-t border-white/10 pt-4 space-y-2 mt-4 text-sm text-slate-400">
                                    <p><strong class="text-white">Servicio:</strong> ${info.event.title.split(' - ')[0]}</p>
                                    <p><strong class="text-white">Profesional:</strong> ${props.barbero}</p>
                                    <p><strong class="text-white">Email:</strong> ${props.email || 'No registrado'}</p>
                                    <p><strong class="text-white">Teléfono:</strong> ${props.telefono || 'No registrado'}</p>
                                    <p><strong class="text-white">Observaciones:</strong> ${props.observaciones || 'Ninguna'}</p>
                                </div>
                            </div>
                        `,
                        background: '#111',
                        showCloseButton: true,
                        showConfirmButton: false,
                        width: '400px',
                        padding: '1.5rem',
                        customClass: {
                            closeButton: 'text-slate-400 hover:text-white focus:outline-none'
                        }
                    });
                }
            });
            calendar.render();
        });

        // Modal Logic
        const modal = document.getElementById('citaModal');
        const btnNuevaCita = document.getElementById('btnNuevaCita');
        const btnCerrar = document.getElementById('btnCerrarCita');
        const btnCancelar = document.getElementById('btnCancelarCita');
        const formCita = document.getElementById('formCita');
        const modalStatus = document.getElementById('modalStatus');

        const openModal = () => {
            modalStatus.innerHTML = '';
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
            }, 10);
        };

        const closeModal = () => {
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                formCita.reset(); // Clear form
            }, 300);
        };

        btnNuevaCita.addEventListener('click', () => {
            // Pre-fill today's date if empty
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('citaFecha').value = today;
            openModal();
        });
        
        btnCerrar.addEventListener('click', closeModal);
        btnCancelar.addEventListener('click', closeModal);

        // Submitting Form via AJAX
        formCita.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnGuardar = document.getElementById('btnGuardarCita');
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = 'Guardando...';
            
            const data = {
                nombre: document.getElementById('clienteNombre').value,
                apellido: document.getElementById('clienteApellido').value,
                email: document.getElementById('clienteEmail').value,
                telefono: document.getElementById('clienteTelefono').value,
                rut: document.getElementById('clienteRut').value,
                nacimiento: document.getElementById('clienteNacimiento').value,
                observaciones: document.getElementById('clienteObservaciones').value,
                barbero_id: document.getElementById('citaBarbero').value,
                servicio_id: document.getElementById('citaServicio').value,
                fecha: document.getElementById('citaFecha').value,
                horario_id: document.getElementById('citaHora').value
            };

            fetch('../api/api_citas_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    modalStatus.innerHTML = '<span class="text-emerald-400 flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">check_circle</span> Cita guardada</span>';
                    calendar.refetchEvents(); // Reload calendar
                    setTimeout(closeModal, 1500);
                } else {
                    modalStatus.innerHTML = `<span class="text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">error</span> ${res.error}</span>`;
                }
            })
            .catch(err => {
                modalStatus.innerHTML = '<span class="text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">error</span> Error de conexión</span>';
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = 'Agendar';
            });
        });

        // Global function to change status
        window.cambiarEstadoCita = function(id, estado) {
            Swal.fire({
                title: 'Actualizando...',
                background: '#111',
                color: '#fff',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('../api/api_citas_update_estado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, estado: estado })
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    Swal.fire({
                        title: 'Actualizado',
                        icon: 'success',
                        background: '#111',
                        color: '#fff',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    calendar.refetchEvents(); // Recargar calendario para actualizar colores
                } else {
                    Swal.fire('Error', res.error, 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        };

    </script>
</body>
</html>
