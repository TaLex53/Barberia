<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: barberiasettings");
    exit;
}

require_once '../../app/config/conexion.php';

try {
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM citas");
    $totalCitas = $stmtTotal->fetchColumn();

    $stmtPendientes = $pdo->query("SELECT COUNT(*) FROM citas WHERE estado = 'Agendada'");
    $citasPendientes = $stmtPendientes->fetchColumn();

    $stmtCanceladas = $pdo->query("SELECT COUNT(*) FROM citas WHERE estado = 'Cancelada'");
    $citasCanceladas = $stmtCanceladas->fetchColumn();

    $stmtCompletadas = $pdo->query("SELECT COUNT(*) FROM citas WHERE estado = 'Completada'");
    $citasCompletadas = $stmtCompletadas->fetchColumn();
} catch (PDOException $e) {
    $totalCitas = $citasPendientes = $citasCanceladas = $citasCompletadas = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Cut Level</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-white text-black rounded-xl font-bold text-xs uppercase tracking-[0.1em] shadow-[0_0_20px_rgba(255,255,255,0.15)] transition-all">
                <span class="material-symbols-outlined text-[18px]">home</span>
                <span class="sidebar-text">Inicio</span>
            </a>
            <a href="citas" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
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
            <a href="barberos" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">content_cut</span>
                <span class="sidebar-text">Barberos</span>
            </a>
            <a href="horarios" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
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
                    <h1 class="text-2xl font-heading uppercase tracking-widest text-white mt-1 hover:text-slate-300 transition-colors">Inicio</h1>
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8 relative">
            
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-heading uppercase tracking-widest text-white">Dashboard</h2>
                <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 hidden sm:block text-right mt-1">
                    Inicio / <span class="text-white">Dashboard</span>
                </div>
            </div>

            <!-- Metrics Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-white text-black flex items-center justify-center shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </div>
                        <div>
                            <h3 id="kpi-creadas" class="text-3xl font-heading text-white tracking-widest"><?= $totalCitas ?></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Citas Creadas</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Total de citas registradas</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">pending_actions</span>
                        </div>
                        <div>
                            <h3 id="kpi-pendientes" class="text-3xl font-heading text-white tracking-widest"><?= $citasPendientes ?></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Citas Pendientes</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Citas por completar hoy</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-red-500/10 text-red-400 border border-red-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">cancel</span>
                        </div>
                        <div>
                            <h3 id="kpi-canceladas" class="text-3xl font-heading text-white tracking-widest"><?= $citasCanceladas ?></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Citas Canceladas</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Citas anuladas por clientes</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div>
                            <h3 id="kpi-completadas" class="text-3xl font-heading text-white tracking-widest"><?= $citasCompletadas ?></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Citas Completadas</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Finalizadas con éxito</p>
                    </div>
                </div>
            </div>

            <!-- Charts Structure Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Chart -->
                <div class="lg:col-span-2 bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl h-[400px] flex flex-col">
                    <h2 class="text-xs font-bold uppercase tracking-[0.1em] text-white mb-6">Cumplimiento de Citas (%)</h2>
                    <div class="flex-1 border border-white/5 border-dashed rounded-xl flex items-center justify-center">
                        <span class="text-slate-600 text-[10px] font-bold uppercase tracking-widest">Gráfico Principal (Vacío)</span>
                    </div>
                </div>

                <!-- Secondary Panel / Filters -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl flex flex-col">
                    <div class="flex items-center gap-2 mb-8 text-white">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        <h2 class="text-xs font-bold uppercase tracking-[0.1em]">Filtros de Búsqueda</h2>
                    </div>
                    
                    <div class="space-y-6 flex-1">
                        <!-- Select 1 -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500">Año</label>
                            <div class="relative">
                                <select id="filter-year" class="w-full h-11 bg-black border border-white/10 rounded-xl px-4 text-xs text-white focus:outline-none focus:border-white/30 transition-colors cursor-pointer appearance-none">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                                <span class="material-symbols-outlined text-slate-500 text-[18px] absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <!-- Select 2 -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500">Mes</label>
                            <div class="relative">
                                <select id="filter-month" class="w-full h-11 bg-black border border-white/10 rounded-xl px-4 text-xs text-white focus:outline-none focus:border-white/30 transition-colors cursor-pointer appearance-none">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                                <span class="material-symbols-outlined text-slate-500 text-[18px] absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <!-- Select 3 -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500">Barbero</label>
                            <div class="relative">
                                <select id="filter-barber" class="w-full h-11 bg-black border border-white/10 rounded-xl px-4 text-xs text-white focus:outline-none focus:border-white/30 transition-colors cursor-pointer appearance-none">
                                    <option value="all">Todos los barberos</option>
                                    <option value="nicolas">Nicolás Cerda</option>
                                    <option value="jorge">Jorge Valenzuela</option>
                                    <option value="alexandra">Alexandra Orellana</option>
                                </select>
                                <span class="material-symbols-outlined text-slate-500 text-[18px] absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                // On mobile, completely hide/show it
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('flex');
            } else {
                // On desktop, toggle the mini-sidebar collapsed mode
                sidebar.classList.toggle('sidebar-collapsed');
            }
        });
        
        // Auto-hide on mobile initially
        if (window.innerWidth < 768) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }
        // Filters logic
        const filterYear = document.getElementById('filter-year');
        const filterMonth = document.getElementById('filter-month');
        const filterBarber = document.getElementById('filter-barber');
        
        const kpiCreadas = document.getElementById('kpi-creadas');
        const kpiPendientes = document.getElementById('kpi-pendientes');
        const kpiCanceladas = document.getElementById('kpi-canceladas');
        const kpiCompletadas = document.getElementById('kpi-completadas');

        // Set current date by default
        const now = new Date();
        filterYear.value = now.getFullYear().toString();
        filterMonth.value = (now.getMonth() + 1).toString();

        // Keep filters in UI but remove fake math overriding.
        // We will animate the numbers from 0 to the actual PHP values printed in HTML.
        
        function animateValue(obj, start, end, duration) {
            if (!obj) return;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate on load
        window.addEventListener('DOMContentLoaded', () => {
            animateValue(kpiCreadas, 0, parseInt(kpiCreadas.innerText) || 0, 500);
            animateValue(kpiPendientes, 0, parseInt(kpiPendientes.innerText) || 0, 500);
            animateValue(kpiCanceladas, 0, parseInt(kpiCanceladas.innerText) || 0, 500);
            animateValue(kpiCompletadas, 0, parseInt(kpiCompletadas.innerText) || 0, 500);
        });
    </script>
</body>
</html>
