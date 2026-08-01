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
    <aside id="sidebar" class="hidden md:flex w-64 bg-[#0a0a0a] border-r border-white/5 flex-col flex-shrink-0 relative z-20 transition-all duration-300">
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
            <a href="reportes" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
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
                <button id="sidebarToggle" class="hidden md:block text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <a href="dashboard" class="block">
                    <h1 class="text-2xl font-heading uppercase tracking-widest text-white mt-1 hover:text-slate-300 transition-colors">Inicio</h1>
                </a>
            </div>
            <!-- Mobile Menu Button on the right -->
            <button id="mobile-menu-btn" class="md:hidden text-slate-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">menu</span>
            </button>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8 pb-24 md:pb-8 relative">
            
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
                <div class="lg:col-span-2 bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl h-[400px] flex flex-col overflow-hidden">
                    <h2 class="text-xs font-bold uppercase tracking-[0.1em] text-white mb-6 flex-shrink-0">Cumplimiento de Citas (%)</h2>
                    <div id="mainChart" class="flex-1 w-full min-h-[0]"></div>
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

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

        // Chart Data Calculation
        const totalCitas = <?= $totalCitas ?: 0 ?>;
        const citasPendientes = <?= $citasPendientes ?: 0 ?>;
        const citasCanceladas = <?= $citasCanceladas ?: 0 ?>;
        const citasCompletadas = <?= $citasCompletadas ?: 0 ?>;

        const pCompletadas = totalCitas ? ((citasCompletadas / totalCitas) * 100).toFixed(1) : 0;
        const pPendientes = totalCitas ? ((citasPendientes / totalCitas) * 100).toFixed(1) : 0;
        const pCanceladas = totalCitas ? ((citasCanceladas / totalCitas) * 100).toFixed(1) : 0;

        const chartOptions = {
            series: [{
                name: 'Porcentaje',
                data: [pCompletadas, pPendientes, pCanceladas]
            }],
            chart: {
                type: 'bar',
                height: '100%',
                parentHeightOffset: 0,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'Montserrat, sans-serif'
            },
            colors: ['#10b981', '#f59e0b', '#ef4444'], // emerald-500, amber-500, red-500
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '40%',
                    distributed: true
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val + "%";
                },
                style: {
                    fontSize: '12px',
                    fontFamily: 'Montserrat, sans-serif',
                    fontWeight: 'bold',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Completadas', 'Pendientes', 'Canceladas'],
                labels: {
                    style: { 
                        colors: ['#10b981', '#f59e0b', '#ef4444'], 
                        fontSize: '10px', 
                        fontWeight: 700, 
                        cssClass: 'uppercase tracking-widest' 
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                max: 100,
                labels: {
                    style: { colors: '#64748b', fontSize: '10px' },
                    formatter: (value) => { return value + "%" }
                }
            },
            grid: {
                borderColor: 'rgba(255,255,255,0.05)',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                padding: {
                    bottom: 15
                }
            },
            legend: { show: false },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val + "%"
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#mainChart"), chartOptions);
        chart.render();
    </script>
    <!-- Mobile App Footer Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-[#0a0a0a] border-t border-white/10 z-50 flex justify-around items-center h-16 px-2">
        <a href="dashboard" class="flex flex-col items-center justify-center w-full h-full text-white">
            <span class="material-symbols-outlined text-[20px] mb-1">home</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Inicio</span>
        </a>
        <a href="citas" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px] mb-1">event_note</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Citas</span>
        </a>
        <a href="servicios" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px] mb-1">design_services</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Servicios</span>
        </a>
        <a href="reportes" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px] mb-1">bar_chart</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Reportes</span>
        </a>
        <a href="barberos" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px] mb-1">content_cut</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Staff</span>
        </a>
        <a href="horarios" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px] mb-1">schedule</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Horario</span>
        </a>
    </nav>

    <!-- Mobile Drawer Menu (Admin) -->
    <div id="mobile-drawer"
        class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-2xl flex flex-col justify-between p-8 translate-x-full transition-transform duration-500 md:hidden">
        <div class="flex justify-between items-center">
            <span class="text-xl font-heading text-white uppercase tracking-widest">Panel Admin</span>
            <button id="close-drawer-btn" class="p-2 text-white hover:text-slate-300">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <nav class="flex flex-col gap-6 text-center font-heading text-3xl uppercase tracking-widest">
            <a href="dashboard" class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Inicio</a>
            <a href="citas" class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Citas</a>
            <a href="servicios" class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Servicios</a>
            <a href="reportes" class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Reportes</a>
            <a href="barberos" class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Staff</a>
            <a href="horarios" class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Horario</a>
        </nav>

        <div class="pb-8">
            <a href="logout"
                class="w-full h-16 flex items-center justify-center rounded-2xl bg-red-500/10 text-red-500 border border-red-500/20 font-black uppercase tracking-[0.2em] shadow-xl hover:bg-red-500/20 transition-all text-lg">
                Salir del Panel
            </a>
        </div>
    </div>

    <script>
        // Mobile Drawer Logic
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-drawer-btn');
        const drawer = document.getElementById('mobile-drawer');
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

        if (menuBtn && closeBtn && drawer) {
            menuBtn.addEventListener('click', () => drawer.classList.remove('translate-x-full'));
            closeBtn.addEventListener('click', () => drawer.classList.add('translate-x-full'));
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', () => drawer.classList.add('translate-x-full'));
            });
        }
    </script>
</body>
</html>
