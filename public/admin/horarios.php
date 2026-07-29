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
    <title>Horarios | Cut Level</title>
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
            <a href="dashboard" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">home</span>
                <span class="sidebar-text">Inicio</span>
            </a>
            <a href="citas" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">event_note</span>
                <span class="sidebar-text">Citas</span>
            </a>
            <a href="servicios" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">design_services</span>
                <span class="sidebar-text">Servicios</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                <span class="sidebar-text">Reportes</span>
            </a>
            <a href="barberos" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-semibold text-xs uppercase tracking-[0.1em] transition-all">
                <span class="material-symbols-outlined text-[18px]">content_cut</span>
                <span class="sidebar-text">Barberos</span>
            </a>
            <a href="horarios" class="flex items-center gap-3 px-4 py-3 bg-white text-black rounded-xl font-bold text-xs uppercase tracking-[0.1em] shadow-[0_0_20px_rgba(255,255,255,0.15)] transition-all">
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
                <button id="sidebarToggle" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <a href="dashboard" class="block">
                    <h1 class="text-2xl font-heading uppercase tracking-widest text-white mt-1 hover:text-slate-300 transition-colors">Horarios</h1>
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 relative">
            
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="citas" class="text-slate-500 hover:text-white transition-colors p-2 bg-white/5 rounded-xl hover:bg-white/10">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <h2 class="text-3xl font-heading uppercase tracking-widest text-white mt-1">Gestión de Horarios</h2>
                </div>
                <div class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 hidden sm:block text-right mt-1">
                    Citas / <span class="text-white">Horarios</span>
                </div>
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
            <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-2 text-white">
                        <span class="material-symbols-outlined text-[18px] text-amber-400">schedule</span>
                        <h3 class="text-xs font-bold uppercase tracking-[0.1em]">Lista de Horas</h3>
                    </div>
                    <button id="btnNuevoHorario" class="bg-white text-black px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-[0.1em] hover:bg-slate-200 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Nuevo Horario
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Hora</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Turno</th>
                                <th class="py-4 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM horarios ORDER BY hora ASC");
                                $horarios = $stmt->fetchAll();
                                
                                if (count($horarios) > 0) {
                                    foreach ($horarios as $horario) {
                                        $timeObj = DateTime::createFromFormat('H:i:s', $horario['hora']);
                                        $timeStr = $timeObj->format('h:i A');

                                        $turnoColor = 'bg-blue-500/10 text-blue-400 border-blue-500/20'; // Default Mañana
                                        if($horario['turno'] === 'Tarde') $turnoColor = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                                        if($horario['turno'] === 'Noche') $turnoColor = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                            ?>
                            <tr class="hover:bg-white/5 transition-colors group cursor-context-menu border-t border-white/5" 
                                data-id="<?php echo $horario['id']; ?>"
                                data-hora="<?php echo $horario['hora']; ?>"
                                data-turno="<?php echo $horario['turno']; ?>">
                                <td class="py-4 px-4 text-white font-bold tracking-wider text-lg"><?php echo $timeStr; ?></td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border <?php echo $turnoColor; ?>">
                                        <?php echo htmlspecialchars($horario['turno']); ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button onclick='openModal("editar", {"id": "<?php echo $horario["id"]; ?>", "hora": "<?php echo $horario["hora"]; ?>", "turno": "<?php echo $horario["turno"]; ?>"})' class="text-slate-500 hover:text-white transition-colors p-1"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                                </td>
                            </tr>
                            <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="3" class="py-8 text-center text-slate-500">No hay horarios registrados.</td></tr>';
                                }
                            } catch (\PDOException $e) {
                                echo '<tr><td colspan="3" class="py-8 text-center text-red-500">Error al cargar datos.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Form (Crear/Editar) -->
    <div id="horarioModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl w-full max-w-sm p-6 shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-xl font-heading text-white tracking-widest uppercase">Nuevo Horario</h3>
                <button id="btnCerrarModal" class="text-slate-500 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="acciones_horarios" method="POST" class="space-y-5">
                <input type="hidden" name="accion" id="formAccion" value="crear">
                <input type="hidden" name="id" id="formId" value="">
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Hora Exacta</label>
                    <input type="time" name="hora" id="formHora" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Turno</label>
                    <select name="turno" id="formTurno" required class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-white/30 transition-colors appearance-none">
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                    </select>
                </div>
                
                <div class="pt-2 flex justify-end gap-3">
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
            <h3 class="text-xl font-heading text-white tracking-widest uppercase mb-2">¿Eliminar Horario?</h3>
            <p id="deleteModalText" class="text-xs text-slate-400 mb-6 font-medium leading-relaxed"></p>
            <div class="flex justify-center gap-3">
                <button type="button" id="btnCancelarDelete" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl transition-colors">Cancelar</button>
                <button type="button" id="btnConfirmarDelete" class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/20 px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors shadow-[0_0_15px_rgba(239,68,68,0.15)]">Sí, Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Hidden form for deleting -->
    <form id="deleteForm" action="acciones_horarios" method="POST" class="hidden">
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
        const modal = document.getElementById('horarioModal');
        const btnNuevo = document.getElementById('btnNuevoHorario');
        const btnCerrar = document.getElementById('btnCerrarModal');
        const btnCancelar = document.getElementById('btnCancelarModal');
        
        const formAccion = document.getElementById('formAccion');
        const formId = document.getElementById('formId');
        const formHora = document.getElementById('formHora');
        const formTurno = document.getElementById('formTurno');
        const modalTitle = document.getElementById('modalTitle');

        const openModal = (accion, data = null) => {
            formAccion.value = accion;
            if (accion === 'editar' && data) {
                modalTitle.textContent = 'Editar Horario';
                formId.value = data.id;
                formHora.value = data.hora;
                formTurno.value = data.turno;
            } else {
                modalTitle.textContent = 'Nuevo Horario';
                formId.value = '';
                formHora.value = '09:00';
                formTurno.value = 'Mañana';
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
            deleteModalText.innerHTML = `Estás a punto de eliminar el bloque horario de <span class="text-white font-bold">${data.hora}</span> (${data.turno}).<br>Esta acción no se puede deshacer.`;
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
                    hora: row.dataset.hora,
                    turno: row.dataset.turno
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
</body>
</html>
