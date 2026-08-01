<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: barberiasettings");
    exit;
}

require_once '../../app/config/conexion.php';

try {
    // Ingresos Hoy
    $stmtHoy = $pdo->query("
        SELECT COALESCE(SUM(s.precio), 0) 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.estado = 'Completada' AND DATE(c.fecha_cita) = CURDATE()
    ");
    $ingresosHoy = $stmtHoy->fetchColumn();

    // Ingresos Semana
    $stmtSemana = $pdo->query("
        SELECT COALESCE(SUM(s.precio), 0) 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.estado = 'Completada' AND YEARWEEK(c.fecha_cita, 1) = YEARWEEK(CURDATE(), 1)
    ");
    $ingresosSemana = $stmtSemana->fetchColumn();

    // Ingresos Mes
    $stmtMes = $pdo->query("
        SELECT COALESCE(SUM(s.precio), 0) 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.estado = 'Completada' AND MONTH(c.fecha_cita) = MONTH(CURDATE()) AND YEAR(c.fecha_cita) = YEAR(CURDATE())
    ");
    $ingresosMes = $stmtMes->fetchColumn();

    // Ingresos Año
    $stmtAno = $pdo->query("
        SELECT COALESCE(SUM(s.precio), 0) 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.estado = 'Completada' AND YEAR(c.fecha_cita) = YEAR(CURDATE())
    ");
    $ingresosAno = $stmtAno->fetchColumn();

    // Filtro para el servicio más agendado
    $filtro = $_GET['filtro'] ?? 'historico';
    $whereFiltro = "c.estado = 'Completada'";
    
    if ($filtro === 'dia') {
        $whereFiltro .= " AND DATE(c.fecha_cita) = CURDATE()";
    } elseif ($filtro === 'semana') {
        $whereFiltro .= " AND YEARWEEK(c.fecha_cita, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($filtro === 'mes') {
        $whereFiltro .= " AND MONTH(c.fecha_cita) = MONTH(CURDATE()) AND YEAR(c.fecha_cita) = YEAR(CURDATE())";
    } elseif ($filtro === 'ano') {
        $whereFiltro .= " AND YEAR(c.fecha_cita) = YEAR(CURDATE())";
    }

    $stmtPop = $pdo->query("
        SELECT s.nombre, COUNT(c.id) as total
        FROM citas c
        JOIN servicios s ON c.servicio_id = s.id
        WHERE $whereFiltro
        GROUP BY s.id
        ORDER BY total DESC
        LIMIT 1
    ");
    $servicioMasPopular = $stmtPop->fetch(PDO::FETCH_ASSOC);
    $nombreServicioPopular = $servicioMasPopular ? $servicioMasPopular['nombre'] : 'Ninguno';
    $totalServicioPopular = $servicioMasPopular ? $servicioMasPopular['total'] : 0;

    // Ingresos últimos 7 días para el gráfico
    $stmtChart = $pdo->query("
        SELECT DATE(c.fecha_cita) as fecha, COALESCE(SUM(s.precio), 0) as total
        FROM citas c
        JOIN servicios s ON c.servicio_id = s.id
        WHERE c.estado = 'Completada' AND c.fecha_cita >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(c.fecha_cita)
        ORDER BY fecha ASC
    ");
    $chartData = $stmtChart->fetchAll(PDO::FETCH_ASSOC);
    
    // Rellenar días faltantes con 0
    $last7Days = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last7Days[$date] = 0; 
    }
    foreach ($chartData as $row) {
        if (isset($last7Days[$row['fecha']])) {
            $last7Days[$row['fecha']] = (float)$row['total'];
        }
    }
    
    $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    $chartLabels = [];
    $chartSeries = [];
    foreach ($last7Days as $date => $total) {
        $timestamp = strtotime($date);
        $chartLabels[] = $diasSemana[date('w', $timestamp)] . ' ' . date('d', $timestamp);
        $chartSeries[] = $total;
    }

} catch (PDOException $e) {
    $ingresosHoy = $ingresosSemana = $ingresosMes = $ingresosAno = 0;
    $nombreServicioPopular = 'Error';
    $totalServicioPopular = 0;
    $chartLabels = [];
    $chartSeries = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | Cut Level</title>
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
            <a href="dashboard" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
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
            <a href="reportes" class="flex items-center gap-3 px-4 py-3 bg-white text-black rounded-xl font-bold text-xs uppercase tracking-[0.1em] shadow-[0_0_20px_rgba(255,255,255,0.15)] transition-all">
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
                    <h1 class="text-2xl font-heading uppercase tracking-widest text-white mt-1 hover:text-slate-300 transition-colors">Reportes</h1>
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
                <h2 class="text-3xl font-heading uppercase tracking-widest text-white">Reportes</h2>
                <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 hidden sm:block text-right mt-1">
                    Inicio / <span class="text-white">Reportes</span>
                </div>
            </div>

            <!-- Revenue Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">today</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-heading text-white tracking-widest">$<span class="kpi-number"><?= $ingresosHoy ?></span></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Ingresos Hoy</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Generado en el día actual</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">date_range</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-heading text-white tracking-widest">$<span class="kpi-number"><?= $ingresosSemana ?></span></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Ingresos Semana</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Semana en curso</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-heading text-white tracking-widest">$<span class="kpi-number"><?= $ingresosMes ?></span></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Ingresos Mes</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Mes actual</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-colors shadow-xl">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">calendar_today</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-heading text-white tracking-widest">$<span class="kpi-number"><?= $ingresosAno ?></span></h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Ingresos Año</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Año en curso</p>
                    </div>
                </div>
            </div>

            <!-- Charts & Top Service Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Chart: Last 7 Days Revenue -->
                <div class="lg:col-span-2 bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl h-[400px] flex flex-col overflow-hidden">
                    <div class="flex items-center gap-2 mb-6 text-white flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px] text-emerald-400">trending_up</span>
                        <h2 class="text-xs font-bold uppercase tracking-[0.1em]">Ingresos Últimos 7 Días</h2>
                    </div>
                    <div id="revenueChart" class="flex-1 w-full min-h-[0]"></div>
                </div>

                <!-- Secondary Panel: Top Service -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-2 text-white">
                            <span class="material-symbols-outlined text-[18px] text-amber-400">star</span>
                            <h2 class="text-xs font-bold uppercase tracking-[0.1em]">Servicio Más Agendado</h2>
                        </div>
                        <!-- Filtro -->
                        <div class="relative">
                            <select onchange="window.location.href='reportes?filtro='+this.value" class="h-8 bg-black border border-white/10 rounded-lg pl-3 pr-8 text-[10px] font-bold uppercase tracking-wider text-slate-400 focus:outline-none focus:border-white/30 transition-colors cursor-pointer appearance-none">
                                <option value="historico" <?= $filtro == 'historico' ? 'selected' : '' ?>>Histórico</option>
                                <option value="dia" <?= $filtro == 'dia' ? 'selected' : '' ?>>Hoy</option>
                                <option value="semana" <?= $filtro == 'semana' ? 'selected' : '' ?>>Semana</option>
                                <option value="mes" <?= $filtro == 'mes' ? 'selected' : '' ?>>Mes</option>
                                <option value="ano" <?= $filtro == 'ano' ? 'selected' : '' ?>>Año</option>
                            </select>
                            <span class="material-symbols-outlined text-slate-500 text-[14px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col items-center justify-center text-center space-y-4">
                        <div class="w-24 h-24 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center relative shadow-[0_0_30px_rgba(245,158,11,0.15)]">
                            <span class="material-symbols-outlined text-amber-400 text-[48px]">diamond</span>
                        </div>
                        
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($nombreServicioPopular) ?></h3>
                            <p class="text-xs text-slate-400 font-medium bg-white/5 px-4 py-1.5 rounded-full inline-block">
                                <?= $totalServicioPopular ?> citas completadas
                            </p>
                        </div>
                        
                        <div class="mt-8 pt-6 border-t border-white/5 w-full">
                            <p class="text-[10px] text-slate-500 uppercase tracking-[0.1em] font-bold leading-relaxed">
                                Este es el servicio favorito de tus clientes. ¡Sigue así!
                            </p>
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
        
        function animateValue(obj, start, end, duration) {
            if (!obj) return;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                // format as money
                const currentVal = Math.floor(progress * (end - start) + start);
                obj.innerHTML = currentVal.toLocaleString('es-CL');
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate KPIs
        window.addEventListener('DOMContentLoaded', () => {
            const kpis = document.querySelectorAll('.kpi-number');
            kpis.forEach(kpi => {
                const targetValue = parseInt(kpi.innerText) || 0;
                animateValue(kpi, 0, targetValue, 1000);
            });
        });

        // Revenue Chart
        const chartLabels = <?= json_encode($chartLabels) ?>;
        const chartSeries = <?= json_encode($chartSeries) ?>;

        const options = {
            series: [{
                name: 'Ingresos',
                data: chartSeries
            }],
            chart: {
                type: 'area',
                height: '100%',
                parentHeightOffset: 0,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'Montserrat, sans-serif'
            },
            colors: ['#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: chartLabels,
                labels: {
                    style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontSize: '10px' },
                    formatter: (value) => { return "$" + value.toLocaleString('es-CL') }
                }
            },
            grid: {
                borderColor: 'rgba(255,255,255,0.05)',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                padding: { bottom: 15 }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return "$" + val.toLocaleString('es-CL')
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#revenueChart"), options);
        chart.render();
    </script>
    <!-- Mobile App Footer Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-[#0a0a0a] border-t border-white/10 z-50 flex justify-around items-center h-16 px-2">
        <a href="dashboard" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-white transition-colors">
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
        <a href="reportes" class="flex flex-col items-center justify-center w-full h-full text-white">
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
