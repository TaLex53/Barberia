<?php
session_start();
require_once '../../app/config/conexion.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: barberiasettings");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios | Cut Level</title>
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
            <a href="servicios" class="flex items-center gap-3 px-4 py-3 bg-white text-black rounded-xl font-bold text-xs uppercase tracking-[0.1em] shadow-[0_0_20px_rgba(255,255,255,0.15)] transition-all">
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
            <a href="logout" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-red-500/10 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
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
                    <h1 class="text-2xl font-heading uppercase tracking-widest text-white mt-1 hover:text-slate-300 transition-colors">Servicios</h1>
                </a>
            </div>
            <!-- Mobile Menu Button on the right -->
            <button id="mobile-menu-btn" class="md:hidden text-slate-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">menu</span>
            </button>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 md:pb-8 relative">
            
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-3xl font-heading uppercase tracking-widest text-white">Gestión de Servicios</h2>
                <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 hidden sm:block text-right mt-1">
                    Inicio / <span class="text-white">Servicios</span>
                </div>
            </div>
            <div class="mb-8 flex justify-end">
                <button id="btnNuevoServicio" class="bg-white text-black px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-[0.1em] hover:bg-slate-200 transition-colors flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Nuevo Servicio
                </button>
            </div>

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/50 rounded-xl text-emerald-400 text-xs font-bold flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <?php 
                        echo htmlspecialchars($_SESSION['success_msg']); 
                        unset($_SESSION['success_msg']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded-xl text-red-500 text-xs font-bold flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <?php 
                        echo htmlspecialchars($_SESSION['error_msg']); 
                        unset($_SESSION['error_msg']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Datatable Panel -->
            <div class="bg-transparent md:bg-[#0a0a0a] border-none md:border md:border-white/5 rounded-2xl md:p-6 shadow-none md:shadow-xl flex flex-col">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse block md:table">
                        <thead class="hidden md:table-header-group">
                            <tr>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Servicio</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Categoría</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Duración</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Precio</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Estado</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="block md:table-row-group text-sm">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM servicios ORDER BY id ASC");
                                $servicios = $stmt->fetchAll();
                                
                                if (count($servicios) > 0) {
                                    foreach ($servicios as $servicio) {
                                        $activo = (int)$servicio['activo'];
                                        $statusClass = $activo === 1 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20';
                                        $dotClass = $activo === 1 ? 'bg-emerald-400' : 'bg-red-400';
                                        $statusText = $activo === 1 ? 'Activo' : 'Inactivo';
                            ?>
                            <tr class="block md:table-row hover:bg-white/5 transition-colors group cursor-context-menu mb-4 md:mb-0 border border-white/10 md:border-none rounded-2xl md:rounded-none p-4 md:p-0 bg-[#0f0f0f] md:bg-transparent relative" 
                                data-id="<?php echo $servicio['id']; ?>"
                                data-nombre="<?php echo htmlspecialchars($servicio['nombre']); ?>"
                                data-duracion="<?php echo $servicio['duracion_minutos']; ?>"
                                data-precio="<?php echo $servicio['precio']; ?>"
                                data-descripcion="<?php echo htmlspecialchars($servicio['descripcion'] ?? ''); ?>"
                                data-categoria="<?php echo htmlspecialchars($servicio['categoria'] ?? 'Servicios Esenciales'); ?>"
                                data-activo="<?php echo $servicio['activo']; ?>">
                                
                                <td class="block md:table-cell py-2 md:py-4 px-0 md:px-4 md:w-auto w-[85%]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 flex-shrink-0">
                                            <span class="material-symbols-outlined text-[16px]">content_cut</span>
                                        </div>
                                        <div>
                                            <p class="text-white font-semibold text-base md:text-sm"><?php echo htmlspecialchars($servicio['nombre']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="flex md:table-cell justify-between items-center py-2 md:py-4 px-0 md:px-4 text-slate-400 font-medium text-xs border-t border-white/5 md:border-none mt-3 md:mt-0 pt-3 md:pt-4">
                                    <span class="md:hidden text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Categoría</span>
                                    <span class="text-right"><?php echo htmlspecialchars($servicio['categoria'] ?? 'Servicios Esenciales'); ?></span>
                                </td>
                                <td class="flex md:table-cell justify-between items-center py-2 md:py-4 px-0 md:px-4 text-slate-400 font-medium">
                                    <span class="md:hidden text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Duración</span>
                                    <span><?php echo $servicio['duracion_minutos']; ?> min</span>
                                </td>
                                <td class="flex md:table-cell justify-between items-center py-2 md:py-4 px-0 md:px-4 text-white font-bold tracking-wider">
                                    <span class="md:hidden text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Precio</span>
                                    <span>$<?php echo number_format($servicio['precio'], 0, ',', '.'); ?></span>
                                </td>
                                <td class="flex md:table-cell justify-between items-center py-2 md:py-4 px-0 md:px-4">
                                    <span class="md:hidden text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Estado</span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border <?php echo $statusClass; ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?php echo $dotClass; ?>"></span>
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td class="absolute md:static top-4 right-4 md:py-4 md:px-4 text-right">
                                    <button onclick='openModal("editar", {"id": "<?php echo $servicio["id"]; ?>", "nombre": "<?php echo addslashes($servicio["nombre"]); ?>", "categoria": "<?php echo addslashes($servicio["categoria"] ?? "Servicios Esenciales"); ?>", "duracion": "<?php echo $servicio["duracion_minutos"]; ?>", "precio": "<?php echo $servicio["precio"]; ?>", "descripcion": "<?php echo addslashes(str_replace(array("\r", "\n"), array("", "\\n"), $servicio["descripcion"] ?? "")); ?>", "activo": "<?php echo $servicio["activo"]; ?>"})' class="text-slate-500 hover:text-white transition-colors p-2 bg-black md:bg-transparent rounded-lg border border-white/5 md:border-none flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                                </td>
                            </tr>
                            <?php
                                    }
                                } else {
                                    echo '<tr class="block md:table-row"><td colspan="6" class="py-8 text-center text-slate-500 block md:table-cell">No hay servicios registrados.</td></tr>';
                                }
                            } catch (\PDOException $e) {
                                echo '<tr class="block md:table-row"><td colspan="6" class="py-8 text-center text-red-500 block md:table-cell">Error al cargar datos.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Form (Crear/Editar) -->
    <div id="servicioModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-xl font-heading text-white tracking-widest uppercase">Nuevo Servicio</h3>
                <button id="btnCerrarModal" class="text-slate-500 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="acciones_servicios" method="POST" class="space-y-4">
                <input type="hidden" name="accion" id="formAccion" value="crear">
                <input type="hidden" name="id" id="formId" value="">
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Nombre del Servicio</label>
                    <input type="text" name="nombre" id="formNombre" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Descripción (Opcional)</label>
                    <textarea name="descripcion" id="formDescripcion" rows="2" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors placeholder:text-gray-600" placeholder="Ej: Servicio de corte premium con masaje capilar..."></textarea>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Categoría</label>
                    <select name="categoria" id="formCategoria" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors appearance-none">
                        <option value="Servicios Esenciales">Servicios Esenciales</option>
                        <option value="Más reservados">Más reservados</option>
                        <option value="Transformación & Estilo">Transformación & Estilo</option>
                        <option value="Niños/Estudiantes">Niños/Estudiantes</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Duración (minutos)</label>
                        <input type="number" name="duracion_minutos" id="formDuracion" required min="1" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Precio ($)</label>
                        <input type="number" name="precio" id="formPrecio" required min="0" step="100" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Estado</label>
                    <select name="activo" id="formActivo" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors appearance-none">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" id="btnCancelarModal" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white transition-colors">Cancelar</button>
                    <button type="submit" class="bg-white text-black px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-200 transition-colors">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete Confirmation -->
    <div id="deleteConfirmModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-[#0a0a0a] border border-red-500/20 rounded-2xl w-full max-w-sm p-6 shadow-2xl transform scale-95 transition-transform duration-300 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-500/50"></div>
            
            <div class="mx-auto w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center mb-4 border border-red-500/20">
                <span class="material-symbols-outlined text-red-500 text-[24px]">warning</span>
            </div>
            
            <h3 class="text-xl font-heading text-white tracking-widest uppercase mb-2">¿Eliminar Servicio?</h3>
            <p id="deleteModalText" class="text-xs text-slate-400 mb-6 font-medium leading-relaxed"></p>
            
            <div class="flex justify-center gap-3">
                <button type="button" id="btnCancelarDelete" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl transition-colors">Cancelar</button>
                <button type="button" id="btnConfirmarDelete" class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/20 px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors shadow-[0_0_15px_rgba(239,68,68,0.15)]">Sí, Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Hidden form for deleting -->
    <form id="deleteForm" action="acciones_servicios" method="POST" class="hidden">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id" id="deleteId" value="">
    </form>

    <!-- Context Menu -->
    <div id="contextMenu" class="hidden fixed bg-[#1a1a1a] border border-white/10 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] py-2 w-48 z-50 overflow-hidden transform scale-95 opacity-0 transition-all duration-150 origin-top-left pointer-events-none">
        <button id="btnContextCrear" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors flex items-center gap-3">
            <span class="material-symbols-outlined text-[18px]">add</span> Crear
        </button>
        <button id="btnContextEditar" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors flex items-center gap-3">
            <span class="material-symbols-outlined text-[18px]">edit</span> Editar
        </button>
        <div class="border-t border-white/5 my-1"></div>
        <button id="btnContextEliminar" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-red-400 hover:bg-red-500/10 transition-colors flex items-center gap-3">
            <span class="material-symbols-outlined text-[18px]">delete</span> Eliminar
        </button>
    </div>

    <script>
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

        // Modal Logic
        const modal = document.getElementById('servicioModal');
        const btnNuevo = document.getElementById('btnNuevoServicio');
        const btnCerrar = document.getElementById('btnCerrarModal');
        const btnCancelar = document.getElementById('btnCancelarModal');
        
        const formAccion = document.getElementById('formAccion');
        const formId = document.getElementById('formId');
        const formNombre = document.getElementById('formNombre');
        const formCategoria = document.getElementById('formCategoria');
        const formDuracion = document.getElementById('formDuracion');
        const formPrecio = document.getElementById('formPrecio');
        const formDescripcion = document.getElementById('formDescripcion');
        const formActivo = document.getElementById('formActivo');
        const modalTitle = document.getElementById('modalTitle');

        const openModal = (accion, data = null) => {
            formAccion.value = accion;
            if (accion === 'editar' && data) {
                modalTitle.textContent = 'Editar Servicio';
                formId.value = data.id;
                formNombre.value = data.nombre;
                formCategoria.value = data.categoria || 'Servicios Esenciales';
                formDuracion.value = data.duracion;
                formPrecio.value = data.precio;
                formDescripcion.value = data.descripcion || '';
                formActivo.value = data.activo;
            } else {
                modalTitle.textContent = 'Nuevo Servicio';
                formId.value = '';
                formNombre.value = '';
                formCategoria.value = 'Servicios Esenciales';
                formDuracion.value = '45';
                formPrecio.value = '10000';
                formDescripcion.value = '';
                formActivo.value = '1';
            }
            
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
            }, 300);
        };

        btnNuevo.addEventListener('click', () => openModal('crear'));
        btnCerrar.addEventListener('click', closeModal);
        btnCancelar.addEventListener('click', closeModal);

        // Delete Modal Logic
        const deleteModal = document.getElementById('deleteConfirmModal');
        const btnCancelarDelete = document.getElementById('btnCancelarDelete');
        const btnConfirmarDelete = document.getElementById('btnConfirmarDelete');
        const deleteModalText = document.getElementById('deleteModalText');

        const openDeleteModal = (data) => {
            deleteModalText.innerHTML = `Estás a punto de eliminar el servicio <span class="text-white font-bold">${data.nombre}</span>.<br>Esta acción no se puede deshacer.`;
            document.getElementById('deleteId').value = data.id;
            
            deleteModal.classList.remove('hidden');
            setTimeout(() => {
                deleteModal.classList.remove('opacity-0');
                deleteModal.querySelector('div').classList.remove('scale-95');
            }, 10);
        };

        const closeDeleteModal = () => {
            deleteModal.classList.add('opacity-0');
            deleteModal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                deleteModal.classList.add('hidden');
            }, 300);
        };

        btnCancelarDelete.addEventListener('click', closeDeleteModal);
        
        btnConfirmarDelete.addEventListener('click', () => {
            document.getElementById('deleteForm').submit();
        });

        // Context Menu Logic
        const contextMenu = document.getElementById('contextMenu');
        const rows = document.querySelectorAll('tbody tr.cursor-context-menu');
        
        let currentRowData = null;

        rows.forEach(row => {
            row.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                
                currentRowData = {
                    id: row.dataset.id,
                    nombre: row.dataset.nombre,
                    categoria: row.dataset.categoria,
                    duracion: row.dataset.duracion,
                    precio: row.dataset.precio,
                    descripcion: row.dataset.descripcion,
                    activo: row.dataset.activo
                };
                
                let x = e.clientX;
                let y = e.clientY;
                
                contextMenu.classList.remove('hidden');
                contextMenu.style.left = `${x}px`;
                contextMenu.style.top = `${y}px`;
                
                setTimeout(() => {
                    contextMenu.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
                    contextMenu.classList.add('scale-100', 'opacity-100');
                }, 10);

                const rect = contextMenu.getBoundingClientRect();
                if (x + rect.width > window.innerWidth) x -= rect.width;
                if (y + rect.height > window.innerHeight) y -= rect.height;
                
                contextMenu.style.left = `${x}px`;
                contextMenu.style.top = `${y}px`;
            });
        });

        const hideContextMenu = () => {
            contextMenu.classList.remove('scale-100', 'opacity-100');
            contextMenu.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                contextMenu.classList.add('hidden');
            }, 150);
        };

        document.addEventListener('click', (e) => {
            if (!contextMenu.contains(e.target)) hideContextMenu();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                hideContextMenu();
                closeModal();
                closeDeleteModal();
            }
        });

        // Actions from Context Menu
        document.getElementById('btnContextCrear').addEventListener('click', () => {
            hideContextMenu();
            openModal('crear');
        });

        document.getElementById('btnContextEditar').addEventListener('click', () => {
            hideContextMenu();
            if (currentRowData) openModal('editar', currentRowData);
        });

        document.getElementById('btnContextEliminar').addEventListener('click', () => {
            hideContextMenu();
            if (currentRowData) openDeleteModal(currentRowData);
        });
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
        <a href="servicios" class="flex flex-col items-center justify-center w-full h-full text-white">
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
