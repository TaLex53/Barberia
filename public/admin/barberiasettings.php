<?php
session_start();
$error = '';

if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../app/config/conexion.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Por favor ingresa usuario y contraseña.";
        header("Location: barberiasettings");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM user_details WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard");
            exit;
        } else {
            $_SESSION['login_error'] = "Credenciales incorrectas.";
            header("Location: barberiasettings");
            exit;
        }
    } catch (\PDOException $e) {
        $_SESSION['login_error'] = "Error de base de datos.";
        header("Location: barberiasettings");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Settings</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-heading { font-family: 'Bebas Neue', cursive; letter-spacing: 0.02em; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-black text-slate-100 min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

    <!-- Decoration Gradients -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none opacity-20">
         <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-white/10 blur-[150px] rounded-full"></div>
         <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-white/5 blur-[150px] rounded-full"></div>
    </div>

    <!-- Logo Section -->
    <div class="mb-12 flex flex-col items-center relative z-10">
        <a href="./" class="group flex flex-col items-center">
            <img src="../assets/img/cutlevel.png" alt="Cut Level Logo" class="h-16 md:h-20 w-auto mb-4 group-hover:scale-105 transition-transform duration-500">
        </a>
        <h1 class="text-3xl font-heading text-white uppercase tracking-widest text-center">
            Settings <span class="text-slate-500 italic">Panel</span>
        </h1>
    </div>

    <!-- Login Card -->
    <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl w-full max-w-md p-8 md:p-10 shadow-2xl relative z-10">
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded-xl text-red-500 text-xs font-bold text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" class="space-y-6">
            

            <!-- Email / User -->
            <div class="space-y-2">
                <label for="username" class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                    Usuario
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </div>
                    <input type="text" id="username" name="username" 
                        class="block w-full pl-12 pr-4 py-3.5 bg-black border border-white/20 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all text-sm"
                        placeholder="usuario" required>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                    Contraseña
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                    </div>
                    <input type="password" id="password" name="password" 
                        class="block w-full pl-12 pr-12 py-3.5 bg-black border border-white/20 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all text-sm"
                        placeholder="contraseña" required>
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[20px]" id="toggleIcon">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full mt-8 py-4 bg-white text-black font-black uppercase tracking-[0.2em] rounded-xl transition-all duration-300 text-xs shadow-lg hover:bg-slate-200 hover:scale-[1.02] active:scale-[0.98]">
                Ingresar
            </button>
            
            <div class="pt-6 mt-6 border-t border-white/10 text-center">
                <a href="./" class="text-xs text-slate-500 hover:text-white transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Volver al sitio
                </a>
            </div>
        </form>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            toggleIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    </script>
</body>
</html>
