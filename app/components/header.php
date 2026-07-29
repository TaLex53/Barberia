<!DOCTYPE html>
<html lang="es" class="scroll-smooth scroll-pt-28">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbería, Peluquería y Manicure en Puerto Varas | Cut Level</title>
    <meta name="description"
        content="La mejor barbería y peluquería en Puerto Varas. Especialistas en visagismo, cortes de cabello, perfilado de barba y manicure masculino. ¡Reserva en Cut Level!">
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff',
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=block"
        rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
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
    </style>
    <style>
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        /* Gradient text utility */
        .text-gradient {
            background: linear-gradient(to right, #94a3b8, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Slow pulse for glows */
        @keyframes slowPulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .animate-slow-pulse {
            animation: slowPulse 8s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-black text-slate-100 antialiased overflow-x-hidden">

    <!-- Header Navigation -->
    <header id="main-header"
        class="fixed top-0 left-0 w-full z-50 transition-all duration-300 bg-transparent border-b border-transparent">
        <!-- Top Utility Bar -->
        <div class="hidden md:flex w-full h-8 bg-transparent">
            <div class="max-w-7xl mx-auto px-6 w-full flex items-center justify-end gap-6 text-white/40">
                <a href="https://instagram.com/cutlevel.cl" target="_blank" rel="noopener noreferrer"
                    class="text-[9px] font-bold uppercase tracking-widest hover:text-white transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    INSTAGRAM
                </a>
                <a href="#"
                    class="text-[9px] font-bold uppercase tracking-widest hover:text-white transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path>
                    </svg>
                    TIKTOK
                </a>
                <a href="#"
                    class="text-[9px] font-bold uppercase tracking-widest hover:text-white transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                    </svg>
                    FACEBOOK
                </a>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="inicio" class="flex items-center group">
                <img src="assets/img/cutlevel.png" alt="Cut Level Barbería"
                    class="h-16 md:h-20 w-auto group-hover:scale-105 transition-all">
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-[0.2em] text-slate-300">
                <a href="inicio" class="hover:text-white transition-colors">Inicio</a>
                <a href="#servicios" class="hover:text-white transition-colors">Servicios</a>
                <a href="#nosotros" class="hover:text-white transition-colors">Nosotros</a>
                <a href="#galeria" class="hover:text-white transition-colors">Galería</a>
                <a href="#contacto" class="hover:text-white transition-colors">Contacto</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <a href="reserva" target="_blank"
                    class="hidden sm:flex items-center justify-center rounded-xl h-11 px-8 bg-white text-black text-xs font-black uppercase tracking-[0.2em] hover:bg-slate-200 hover:scale-105 transition-all shadow-lg shadow-white/10">
                    Reservar
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 text-white hover:text-slate-300 transition-colors">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-drawer"
        class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-2xl flex flex-col justify-between p-8 translate-x-full transition-transform duration-500 md:hidden">
        <div class="flex justify-between items-center">
            <span class="text-xl font-heading text-white">CUT LEVEL BARBERÍA</span>
            <button id="close-drawer-btn" class="p-2 text-white hover:text-slate-300">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <nav class="flex flex-col gap-6 text-center font-heading text-3xl uppercase tracking-widest">
            <a href="inicio"
                class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Inicio</a>
            <a href="#servicios"
                class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Servicios</a>
            <a href="#nosotros"
                class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Nosotros</a>
            <a href="#galeria"
                class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Galería</a>
            <a href="#contacto"
                class="mobile-nav-link text-white hover:text-slate-300 transition-colors">Contacto</a>
        </nav>

        <div class="pb-8">
            <div class="flex justify-center gap-6 mb-8 text-slate-400">
                <a href="https://instagram.com/cutlevel.cl" target="_blank" rel="noopener noreferrer"
                    class="hover:text-white transition-colors">
                    <svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                    </svg>
                </a>
                <a href="#" class="hover:text-white transition-colors">
                    <svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path
                            d="M12.525.02c1.31-.02 2.61-.01 3.91-.04.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 2.26-1.17 4.54-3.04 5.93-1.89 1.4-4.48 1.93-6.79 1.25-2.43-.72-4.43-2.71-5.06-5.18-.67-2.61-.09-5.46 1.61-7.51 1.67-2.02 4.29-3.07 6.87-3.09v4.06c-1.34.02-2.71.39-3.79 1.24-1.2.94-1.92 2.45-1.93 3.97-.02 1.52.68 3.02 1.83 4.02 1.13.97 2.73 1.34 4.19 1.05 1.54-.31 2.87-1.45 3.39-2.92.38-1.07.49-2.22.46-3.34V.02h3.21z" />
                    </svg>
                </a>
                <a href="#" class="hover:text-white transition-colors">
                    <svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                    </svg>
                </a>
            </div>
            <a href="reserva" target="_blank"
                class="w-full h-16 flex items-center justify-center rounded-2xl bg-white text-black font-black uppercase tracking-[0.2em] shadow-xl shadow-white/10 hover:bg-slate-200 transition-all text-lg">
                Reservar Cita
            </a>
        </div>
    </div>

    <script>
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

        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.remove('bg-transparent', 'border-transparent');
                header.classList.add('bg-black/85', 'backdrop-blur-2xl', 'border-white/10');
            } else {
                header.classList.add('bg-transparent', 'border-transparent');
                header.classList.remove('bg-black/85', 'backdrop-blur-2xl', 'border-white/10');
            }
        });
    </script>