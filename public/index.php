<?php include '../app/components/header.php'; ?>

<!-- Hero Section with Dynamic Slider -->
<section id="inicio" class="relative min-h-screen flex items-center justify-center pt-24 overflow-hidden bg-black">

    <!-- Video Background Container -->
    <div class="absolute inset-0 z-0 bg-black overflow-hidden">
        <video id="bg-video-1" autoplay loop muted playsinline
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-[2000ms] opacity-50">
            <source src="assets/video/background_video.mp4" type="video/mp4">
        </video>
        <video id="bg-video-2" autoplay loop muted playsinline
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-[2000ms] opacity-0">
            <source src="assets/video/manicura_videobackground.mp4" type="video/mp4">
        </video>
        <!-- Gradients to ensure text readability -->
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/75 to-black/90 z-10 pointer-events-none">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent z-10 pointer-events-none">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10 text-center py-20 reveal active">
        <h1 class="text-6xl sm:text-8xl md:text-9xl font-heading text-white uppercase tracking-tight leading-none mb-8">
            CUT LEVEL <span class="text-gradient italic drop-shadow-md">BARBERÍA</span>
        </h1>

        <!-- Tagline Badge -->
        <div
            class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full bg-white/5 border border-white/20 backdrop-blur-xl mb-8 shadow-2xl">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
            <span class="text-xs font-bold uppercase tracking-[0.3em] text-white">SALÓN DE BARBERÍA | PELUQUERÍA |
                MANICURE</span>
        </div>

        <p
            class="text-center text-balance text-slate-300 text-lg md:text-xl font-medium tracking-wide max-w-2xl mx-auto mb-12 leading-relaxed">
            Estética elegante y sofisticada para el hombre moderno. Exclusividad, profesionalismo y máxima atención a
            cada detalle.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 md:gap-6">
            <a href="reserva" target="_blank"
                class="w-full sm:w-auto h-12 px-8 bg-white text-black font-black uppercase tracking-[0.2em] rounded-xl flex items-center justify-center hover:bg-slate-200 hover:scale-105 active:scale-95 transition-all shadow-[0_10px_40px_-5px_rgba(255,255,255,0.25)] text-xs">
                Reservar Cita
            </a>
            <a href="#servicios"
                class="w-full sm:w-auto h-12 px-8 border border-white/25 text-white font-bold uppercase tracking-[0.2em] rounded-xl flex items-center justify-center hover:bg-white/10 hover:border-white transition-all text-xs backdrop-blur-md">
                Ver Servicios
            </a>
        </div>
    </div>
</section>

<!-- Misión & Visión Section -->
<section id="mision-vision" class="py-24 bg-[#030303] relative border-t border-white/5 overflow-hidden">
    <div
        class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-indigo-900/10 rounded-full blur-[150px] pointer-events-none mix-blend-screen animate-slow-pulse">
    </div>
    <div
        class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-stone-800/20 rounded-full blur-[150px] pointer-events-none mix-blend-screen animate-slow-pulse">
    </div>
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-24 reveal">
        <!-- Misión -->
        <div class="space-y-6">
            <h3 class="text-4xl md:text-5xl font-heading text-white uppercase tracking-tight">Misión</h3>
            <div class="w-12 h-0.5 bg-gradient-to-r from-slate-400 to-slate-200"></div>
            <p class="text-slate-300 text-sm md:text-base leading-relaxed font-light">
                En <strong>CUTLEVEL</strong>, nuestra misión es ofrecer la mejor experiencia de <strong>barbería premium
                    en Puerto Varas</strong>. Nos especializamos en <strong>cuidado personal masculino</strong>,
                <strong>cortes de pelo</strong> de alto nivel y <strong>perfilado de barba</strong>, combinando técnica
                avanzada, estilo y atención personalizada. Nuestro propósito es que cada cliente logre una imagen
                impecable y disfrute de la mejor barbería de la zona: un espacio moderno y exclusivo diseñado
                meticulosamente para potenciar tu estética masculina.
            </p>
        </div>
        <!-- Visión -->
        <div class="space-y-6">
            <h3 class="text-4xl md:text-5xl font-heading text-white uppercase tracking-tight">Visión</h3>
            <div class="w-12 h-0.5 bg-gradient-to-r from-slate-400 to-slate-200"></div>
            <p class="text-slate-300 text-sm md:text-base leading-relaxed font-light">
                Consolidarnos como la <strong>barbería referente en Puerto Varas</strong> y líder en toda la
                <strong>Región de Los Lagos</strong>, reconocidos por nuestra calidad inigualable, profesionalismo y
                vanguardia en <strong>estética masculina</strong>. Aspiramos a construir una comunidad exclusiva donde
                las tendencias en <strong>cortes de cabello</strong>, el cuidado personal y la confianza se fusionen,
                convirtiéndonos en el destino definitivo para el hombre moderno que exige excelencia en cada detalle.
            </p>
        </div>
    </div>
</section>

<!-- Quién Soy (Sobre Mí) Editorial Section -->
<section id="quien-soy" class="py-20 bg-[#050505] relative border-t border-white/5 overflow-hidden">
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-slate-900/10 rounded-full blur-[200px] pointer-events-none mix-blend-screen animate-slow-pulse">
    </div>
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center reveal">
        <!-- Image Left Column -->
        <div class="relative lg:order-first flex justify-center">

            <!-- Wrapper to position indicator relative to phone -->
            <div class="relative">

                <!-- Swipe Indicator -->
                <div
                    class="absolute -right-8 sm:-right-12 top-1/2 -translate-y-1/2 flex flex-col items-center gap-4 text-white/50 hidden sm:flex pointer-events-none">
                    <svg class="w-5 h-5 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] whitespace-nowrap"
                        style="writing-mode: vertical-rl; transform: rotate(180deg);">
                        Desliza para ver más
                    </span>
                    <svg class="w-5 h-5 animate-bounce" style="animation-delay: 0.1s" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </div>

                <!-- iPhone Mockup -->
                <div
                    class="w-[240px] sm:w-[280px] h-[480px] sm:h-[560px] rounded-[2.5rem] sm:rounded-[3rem] overflow-hidden border-[10px] sm:border-[12px] border-[#111111] bg-black shadow-2xl relative ring-1 ring-white/10">
                    <!-- iPhone Notch -->
                    <div
                        class="absolute top-0 left-1/2 -translate-x-1/2 w-[80px] sm:w-[100px] h-[20px] sm:h-[24px] bg-[#111111] rounded-b-[14px] sm:rounded-b-[16px] z-20">
                    </div>

                    <!-- Status Bar -->
                    <div
                        class="absolute top-2 inset-x-0 z-20 px-4 sm:px-5 flex items-center justify-between text-white drop-shadow-md">
                        <!-- Left: Time -->
                        <span
                            class="text-[10px] sm:text-[11px] font-sans font-semibold tracking-wider ml-1 mt-0.5">9:41</span>

                        <!-- Right: Icons -->
                        <div class="flex items-center gap-[4px] sm:gap-[5px] mr-1">
                            <!-- iOS Signal -->
                            <svg width="14" height="9" viewBox="0 0 17 11" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect y="7" width="3" height="4" rx="1" fill="currentColor" />
                                <rect x="4.5" y="5" width="3" height="6" rx="1" fill="currentColor" />
                                <rect x="9" y="2.5" width="3" height="8.5" rx="1" fill="currentColor" />
                                <rect x="13.5" width="3" height="11" rx="1" fill="currentColor" />
                            </svg>
                            <!-- iOS WiFi -->
                            <svg width="13" height="9" viewBox="0 2 16 10" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8 11.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5z" />
                                <path
                                    d="M11.64 7.36c-2-2-5.28-2-7.28 0l1.06 1.06c1.42-1.42 3.74-1.42 5.16 0l1.06-1.06z" />
                                <path
                                    d="M14.46 4.54c-3.56-3.56-9.36-3.56-12.92 0l1.06 1.06c2.98-2.98 7.82-2.98 10.8 0l1.06-1.06z" />
                            </svg>
                            <!-- iOS Battery -->
                            <svg width="20" height="10" viewBox="0 0 25 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="opacity-90">
                                <rect x="0.5" y="0.5" width="21" height="11" rx="3.5" stroke="currentColor"
                                    stroke-width="1.5" stroke-opacity="0.5" />
                                <rect x="2.5" y="2.5" width="17" height="7" rx="1.5" fill="currentColor" />
                                <path d="M23 4.5C23 4.5 24 5 24 6C24 7 23 7.5 23 7.5V4.5Z" fill="currentColor"
                                    fill-opacity="0.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Instagram Reels UI Overlay -->
                    <div class="absolute inset-0 z-20 pointer-events-none">
                        <!-- Right Actions -->
                        <div class="absolute bottom-16 right-1.5 flex flex-col gap-4 items-center">
                            <div class="flex flex-col items-center gap-[2px] drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
                                <svg class="text-white w-[20px] h-[20px]" viewBox="0 0 24 24">
                                    <path
                                        d="M16.792 3.904A4.989 4.989 0 0 1 21.5 9.122c0 3.072-2.652 4.959-5.197 7.222-2.512 2.243-3.865 3.469-4.303 3.752-.477-.309-2.143-1.823-4.303-3.752C5.141 14.072 2.5 12.167 2.5 9.122a4.989 4.989 0 0 1 4.708-5.218 4.21 4.21 0 0 1 3.675 1.941c.84 1.175.98 1.514 1.117 1.514s.277-.339 1.117-1.514a4.212 4.212 0 0 1 3.675-1.941Z"
                                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"></path>
                                </svg>
                                <span class="text-white text-[9px] font-bold">Me gusta</span>
                            </div>
                            <div class="flex flex-col items-center gap-[2px] drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
                                <svg class="text-white w-[20px] h-[20px]" viewBox="0 0 24 24">
                                    <path d="M20.656 17.008a9.993 9.993 0 1 0-3.59 3.615L22 22Z" fill="none"
                                        stroke="currentColor" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                                <span class="text-white text-[9px] font-bold">1</span>
                            </div>
                            <div class="flex flex-col items-center gap-[2px] drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
                                <svg class="text-white w-[20px] h-[20px]" viewBox="0 0 24 24">
                                    <line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                        x1="22" x2="9.218" y1="3" y2="10.083"></line>
                                    <polygon fill="none"
                                        points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334"
                                        stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon>
                                </svg>
                                <span class="text-white text-[9px] font-bold">5</span>
                            </div>
                            <div class="flex flex-col items-center gap-[2px] drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
                                <svg class="text-white w-[20px] h-[20px]" viewBox="0 0 24 24">
                                    <polygon fill="none" points="20 21 12 13.44 4 21 4 3 20 3 20 21"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"></polygon>
                                </svg>
                            </div>
                        </div>

                        <!-- Bottom Left Profile -->
                        <a href="https://www.instagram.com/cutlevel.cl/" target="_blank"
                            class="absolute bottom-6 left-4 flex items-center gap-3 drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] pointer-events-auto hover:opacity-80 transition-opacity">
                            <div
                                class="w-10 h-10 rounded-full bg-white p-0.5 overflow-hidden flex items-center justify-center">
                                <img src="assets/img/cutleveel.jpg" class="w-full h-auto object-contain">
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-white font-bold text-sm tracking-wide">cutlevel.cl</span>
                                <span class="material-symbols-outlined text-white text-[14px]"
                                    style="font-variation-settings: 'FILL' 1;">verified</span>
                            </div>
                        </a>
                    </div>

                    <!-- Video Scroll Track -->
                    <div id="iphone-video-track"
                        class="absolute top-0 left-0 w-full h-full flex flex-col z-0 pointer-events-auto cursor-grab"
                        style="will-change: transform;">
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/quienes_somos.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/Barberia.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/barberia_1.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/barberia_2.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/barberia_3.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/barberia_4.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                        <div class="w-full h-full flex-shrink-0 relative">
                            <video src="assets/video/barberia_5.mp4" autoplay loop muted playsinline
                                class="w-full h-full object-cover pointer-events-none"></video>
                        </div>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30 z-10 pointer-events-none">
                    </div>
                </div>

            </div>
        </div>

        <!-- Text Right Column -->
        <div class="space-y-8 lg:pl-8">
            <span class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Barbería & Estilismo
                Premium</span>
            <h2 class="text-5xl md:text-6xl font-heading text-white uppercase tracking-tight leading-tight">
                El Arte de la <br><span class="text-gradient italic drop-shadow-md">Elegancia Masculina</span>
            </h2>
            <div class="space-y-4 text-slate-300 text-base leading-relaxed text-balance">
                <p>
                    Forjada en Puerto Varas, Cutlevel nace para satisfacer a quienes exigen excelencia. Somos un espacio
                    premium dedicado a la barbería, peluquería y manicure, donde la elegancia masculina y la atención al
                    detalle lo son todo.
                </p>
                <p>
                    Diseñamos una experiencia de confort y profesionalismo acompañada siempre de un buen café o té y una
                    asesoría de imagen experta para garantizar resultados impecables y llenos de confianza.
                </p>
                <p class="font-bold text-white">
                    Cutlevel. Eleva tu imagen al siguiente nivel.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Nosotros (Equipo) Section -->
<section id="nosotros" class="py-24 bg-[#050505] relative border-t border-white/10 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 relative z-10 reveal">
        <div class="text-center mb-16 space-y-4">
            <span class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Nosotros</span>
            <h2 class="text-5xl md:text-6xl font-heading text-white uppercase tracking-tight">Barberos & <span
                    class="text-gradient italic drop-shadow-md">Profesionales</span></h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Nicolás Cerda -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div class="aspect-[4/5] w-full relative overflow-hidden border-b border-white/5 bg-[#1a1a1a]">
                    <img src="assets/img/nicolas.webp" alt="Nicolás Cerda"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-2xl font-heading text-white uppercase tracking-tight mb-2">Nicolás Cerda</h3>
                    <p class="text-slate-400 text-xs font-light leading-relaxed mb-6">Barbería, colorimetría, visos y
                        permanente.</p>
                    <div class="mt-auto space-y-4">
                        <a href="reserva" target="_blank"
                            class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                            Agendar
                        </a>
                        <a href="https://www.instagram.com/elucutz_studio" target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 text-slate-500 hover:text-white transition-colors text-xs font-medium">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                            @elucutz_studio
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jorge Valenzuela -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div class="aspect-[4/5] w-full relative overflow-hidden border-b border-white/5 bg-[#1a1a1a]">
                    <img src="assets/img/valen.webp" alt="Jorge Valenzuela"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-2xl font-heading text-white uppercase tracking-tight mb-2">Jorge Valenzuela</h3>
                    <p class="text-slate-400 text-xs font-light leading-relaxed mb-6">Fades y diseños freestyle.</p>
                    <div class="mt-auto space-y-4">
                        <a href="reserva" target="_blank"
                            class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                            Agendar
                        </a>
                        <a href="https://www.instagram.com/valenzuelacutz_" target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 text-slate-500 hover:text-white transition-colors text-xs font-medium">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                            @valenzuelacutz_
                        </a>
                    </div>
                </div>
            </div>


            <!-- Alexandra Orellana -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div class="aspect-[4/5] w-full relative overflow-hidden border-b border-white/5 bg-[#1a1a1a]">
                    <img src="assets/img/enya.webp" alt="Alexandra Orellana"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-2xl font-heading text-white uppercase tracking-tight mb-2">Alexandra Orellana</h3>
                    <p class="text-slate-400 text-xs font-light leading-relaxed mb-6">Servicios especializados de
                        peluquería y belleza para mujeres.</p>
                    <div class="mt-auto space-y-4">
                        <a href="reserva" target="_blank"
                            class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                            Agendar
                        </a>
                        <a href="https://www.instagram.com/enyabeauty.cl/" target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 text-slate-500 hover:text-white transition-colors text-xs font-medium">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                            @enyabeauty.cl
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Collage de Cortes Section -->
<section id="galeria" class="py-24 bg-[#030303] relative border-t border-white/5 overflow-hidden">
    <div
        class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white/[0.03] via-transparent to-transparent pointer-events-none">
    </div>
    <div class="max-w-7xl mx-auto px-6 relative z-10 reveal">
        <div class="text-center mb-16 space-y-4">
            <span class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Nuestro Trabajo</span>
            <h2 class="text-4xl md:text-6xl font-heading text-white uppercase tracking-tight">Galería de <span
                    class="text-gradient italic drop-shadow-md">Estilos</span></h2>
        </div>

        <div
            class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 grid-flow-dense gap-3 sm:gap-4 lg:gap-5 auto-rows-[140px] md:auto-rows-[160px] lg:auto-rows-[180px]">
            <?php
            $gallery_items = [
                ["src" => "corte_1.webp", "title" => "Corte de Pelo", "class" => "col-span-2 row-span-2"],
                ["src" => "corte_2.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-2", "img_class" => "scale-[1.35] origin-[50%_70%] group-hover:scale-[1.45]"],
                ["src" => "corte_3.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-1"],
                ["src" => "corte_4.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-1", "img_class" => "object-[50%_70%] group-hover:scale-110"],
                ["src" => "corte_5.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-1"],
                ["src" => "corte_6.webp", "title" => "Corte de Pelo", "class" => "col-span-2 row-span-2"],
                ["src" => "corte_7.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-1"],
                ["src" => "corte_8.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-1"],
                ["src" => "corte_9.webp", "title" => "Corte de Pelo", "class" => "col-span-1 row-span-1"],
                ["src" => "manicure.webp", "title" => "Manicure", "class" => "col-span-1 row-span-1"],
                ["src" => "manicure_2.webp", "title" => "Manicure", "class" => "col-span-1 row-span-1"],
            ];
            foreach ($gallery_items as $item):
                ?>
                <div
                    class="group relative overflow-hidden rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 hover:shadow-[0_0_30px_rgba(255,255,255,0.15)] hover:border-white/30 transition-all duration-500 <?= $item['class'] ?>">
                    <img src="assets/img/<?= $item['src'] ?>" alt="<?= $item['title'] ?>"
                        class="w-full h-full object-cover transition-transform duration-700 <?= $item['img_class'] ?? 'group-hover:scale-110' ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-16 flex justify-center">
            <a href="https://www.instagram.com/cutlevel.cl/" target="_blank"
                class="h-12 px-8 bg-white text-black font-bold uppercase tracking-[0.2em] rounded-xl flex items-center justify-center hover:bg-slate-200 hover:scale-105 active:scale-95 transition-all shadow-[0_10px_40px_-5px_rgba(255,255,255,0.25)] text-xs gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                </svg>
                Ver más en Instagram
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="servicios" class="py-32 bg-[#020202] relative overflow-hidden border-t border-white/5">
    <div
        class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-900/10 rounded-full blur-[150px] pointer-events-none">
    </div>
    <div class="max-w-7xl mx-auto px-6 relative z-10 reveal">
        <div class="text-center mb-20 space-y-4">
            <span class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Servicios & Valores</span>
            <h2 class="text-5xl md:text-7xl font-heading text-white uppercase tracking-tight">Nuestra <span
                    class="text-gradient italic drop-shadow-md">Oferta</span></h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Corte de Pelo -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div
                    class="py-12 bg-gradient-to-b from-white/[0.05] to-transparent relative overflow-hidden flex items-center justify-center border-b border-white/5">
                    <span
                        class="material-symbols-outlined text-white/10 text-[80px] drop-shadow-[0_0_15px_rgba(255,255,255,0.2)] absolute transition-transform duration-700 group-hover:scale-110">content_cut</span>
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-xl font-heading text-white uppercase tracking-tight mb-2">Corte Clásico</h3>
                    <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-6">~ 45
                        MINUTOS</span>

                    <ul class="text-left space-y-3 mb-8 flex-1">
                        <li class="flex justify-between items-end border-b border-white/5 pb-2">
                            <span class="text-slate-400 text-[11px]">Lunes - Jueves (10-16h)</span>
                            <span class="text-white font-bold text-sm">$11.990</span>
                        </li>
                        <li class="flex justify-between items-end border-b border-white/5 pb-2">
                            <span class="text-slate-400 text-[11px]">Lunes - Jueves (16-20h)</span>
                            <span class="text-white font-bold text-sm">$14.990</span>
                        </li>
                        <li class="flex justify-between items-end pb-2">
                            <span class="text-slate-400 text-[11px]">Viernes Sabado y Fest</span>
                            <span class="text-white font-bold text-sm">$15.990</span>
                        </li>
                    </ul>

                    <a href="reserva" target="_blank"
                        class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                        Agendar
                    </a>
                </div>
            </div>

            <!-- Ritual de Barba -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div
                    class="py-12 bg-gradient-to-b from-white/[0.05] to-transparent relative overflow-hidden flex items-center justify-center border-b border-white/5">
                    <span
                        class="material-symbols-outlined text-white/10 text-[80px] drop-shadow-[0_0_15px_rgba(255,255,255,0.2)] absolute transition-transform duration-700 group-hover:scale-110">air</span>
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-xl font-heading text-white uppercase tracking-tight mb-2">Ritual de Barba</h3>
                    <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-6">~ 30
                        MINUTOS</span>

                    <div class="flex-1 flex flex-col items-center justify-center mb-8">
                        <span class="text-4xl font-heading text-white">$13.990</span>
                    </div>

                    <a href="reserva" target="_blank"
                        class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                        Agendar
                    </a>
                </div>
            </div>

            <!-- Afeitado Express -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div
                    class="py-12 bg-gradient-to-b from-white/[0.05] to-transparent relative overflow-hidden flex items-center justify-center border-b border-white/5">
                    <span
                        class="material-symbols-outlined text-white/10 text-[80px] drop-shadow-[0_0_15px_rgba(255,255,255,0.2)] absolute transition-transform duration-700 group-hover:scale-110">dry_cleaning</span>
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-xl font-heading text-white uppercase tracking-tight mb-2">Afeitado Express</h3>
                    <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-6">~ 20
                        MINUTOS</span>

                    <div class="flex-1 flex flex-col items-center justify-center mb-8">
                        <span class="text-4xl font-heading text-white">$9.990</span>
                    </div>

                    <a href="reserva" target="_blank"
                        class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                        Agendar
                    </a>
                </div>
            </div>

            <!-- Diseños -->
            <div
                class="bg-white/[0.02] backdrop-blur-xl border border-white/5 rounded-2xl overflow-hidden hover:bg-white/[0.04] hover:border-white/20 hover:shadow-[0_0_40px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-500 group flex flex-col relative">
                <div
                    class="py-12 bg-gradient-to-b from-white/[0.05] to-transparent relative overflow-hidden flex items-center justify-center border-b border-white/5">
                    <span
                        class="material-symbols-outlined text-white/10 text-[80px] drop-shadow-[0_0_15px_rgba(255,255,255,0.2)] absolute transition-transform duration-700 group-hover:scale-110">brush</span>
                </div>
                <div class="p-6 flex flex-col flex-1 text-center">
                    <h3 class="text-xl font-heading text-white uppercase tracking-tight mb-2">Diseños (Freestyle)</h3>
                    <span
                        class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-6">PERSONALIZADO</span>

                    <div class="flex-1 flex flex-col items-center justify-center mb-8">
                        <span class="text-4xl font-heading text-white">$6.000</span>
                    </div>

                    <a href="reserva" target="_blank"
                        class="w-full py-3 border border-white/20 rounded-xl text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all block">
                        Agendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Closing Invitation Button -->
        <div class="mt-16 flex justify-center">
            <a href="reserva" target="_blank"
                class="h-12 px-8 border border-white/25 text-white font-bold uppercase tracking-[0.2em] rounded-xl flex items-center justify-center hover:bg-white/10 hover:border-white transition-all text-xs backdrop-blur-md hover:scale-105 shadow-[0_0_20px_rgba(255,255,255,0.05)]">
                Ver servicios
            </a>
        </div>
    </div>
</section>

<!-- Brand Marquee Section -->
<section class="py-12 bg-black border-t border-white/10 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-6 mb-8 text-center relative z-10">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-[0.3em]">Convenios</h2>
    </div>
    <div class="marquee-container flex overflow-hidden whitespace-nowrap relative select-none gap-16 sm:gap-24">
        <?php
        $convenios = [
            ["src" => "assets/img/magno.png", "alt" => "Magno Barber Products", "class" => "h-16 sm:h-20"],
            ["src" => "assets/img/strike.jpg", "alt" => "Strike Barber Products", "class" => "h-16 sm:h-20"],
            ["src" => "assets/img/club_deportivo.png", "alt" => "Club Deportivo Alerce Histórico", "class" => "h-24 sm:h-28 scale-110"],
        ];
        ?>
        <div
            class="marquee-content flex shrink-0 min-w-full items-center justify-around gap-16 sm:gap-24 animate-marquee">
            <?php for ($i = 0; $i < 3; $i++):
                foreach ($convenios as $c): ?>
                    <img src="<?= $c['src'] ?>" alt="<?= $c['alt'] ?>"
                        class="<?= $c['class'] ?> w-auto object-contain transition-all hover:scale-115">
                    <?php endforeach; endfor; ?>
        </div>
        <div class="marquee-content flex shrink-0 min-w-full items-center justify-around gap-16 sm:gap-24 animate-marquee"
            aria-hidden="true">
            <?php for ($i = 0; $i < 3; $i++):
                foreach ($convenios as $c): ?>
                    <img src="<?= $c['src'] ?>" alt="<?= $c['alt'] ?>"
                        class="<?= $c['class'] ?> w-auto object-contain transition-all hover:scale-115">
                    <?php endforeach; endfor; ?>
        </div>
    </div>
</section>

<!-- Convenios CTA Section -->
<section id="convenios-cta" class="py-32 bg-black relative border-t border-white/10">
    <div
        class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/5 via-transparent to-transparent z-0 pointer-events-none">
    </div>
    <div class="max-w-4xl mx-auto px-6 relative z-10 text-center space-y-8">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Alianzas Corporativas</span>
        <h2 class="text-4xl md:text-6xl font-heading text-white uppercase tracking-tight">
            ¿Tienes una empresa y quieres realizar un <span class="text-gradient italic drop-shadow-md">Convenio?</span>
        </h2>
        <p class="text-slate-300 text-lg leading-relaxed font-light max-w-2xl mx-auto">
            Ofrece a tus colaboradores un beneficio exclusivo en barbería y cuidado personal premium. Contáctanos para
            conocer nuestros planes corporativos.
        </p>

        <div class="pt-8 flex justify-center">
            <a href="https://wa.me/56920860076" target="_blank" rel="noopener noreferrer"
                class="h-14 px-10 bg-[#25D366] text-white font-black uppercase tracking-[0.2em] rounded-xl flex items-center justify-center hover:scale-105 active:scale-95 transition-all shadow-[0_10px_40px_-10px_rgba(37,211,102,0.4)] text-sm gap-3">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                Contáctanos
            </a>
        </div>
    </div>
</section>

<!-- Contacto Section -->
<section id="contacto" class="py-32 bg-[#080808] relative border-t border-white/10">
    <div class="max-w-7xl mx-auto px-6 text-center space-y-12">
        <div class="max-w-3xl mx-auto space-y-4">
            <span class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Contacto & Reservas</span>
            <h2 class="text-5xl md:text-7xl font-heading text-white uppercase tracking-tight">
                Ubicación
            </h2>
            <div class="mt-8 max-w-2xl mx-auto flex items-center justify-center gap-3">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Horarios de Atención:</span>
                <span class="text-slate-300 text-sm font-medium">Lunes a sábado y festivos de 09:00 a 20:00 hrs</span>
            </div>
        </div>

        <!-- Mapa Container -->
        <div class="max-w-5xl mx-auto text-left mt-16">
            <h3 class="text-xl font-bold text-white mb-4">Puerto Varas</h3>

            <!-- Mapa Embed -->
            <div
                class="w-full h-[350px] sm:h-[450px] rounded-2xl overflow-hidden border border-white/10 shadow-[0_0_50px_rgba(255,255,255,0.03)] relative">
                <iframe
                    src="https://maps.google.com/maps?q=Av.%20Col%C3%B3n%200600,%20Puerto%20Varas&t=&z=16&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <p class="text-slate-400 mt-4 text-sm font-medium">
                Av. Colón 0600, Puerto Varas
            </p>
        </div>

    </div>
</section>



<script>
    document.addEventListener("DOMContentLoaded", () => {
        const video1 = document.getElementById('bg-video-1');
        const video2 = document.getElementById('bg-video-2');
        if (video1 && video2) {
            let showFirst = true;
            setInterval(() => {
                showFirst = !showFirst;
                if (showFirst) {
                    video1.classList.remove('opacity-0');
                    video1.classList.add('opacity-50');
                    video2.classList.remove('opacity-50');
                    video2.classList.add('opacity-0');
                } else {
                    video2.classList.remove('opacity-0');
                    video2.classList.add('opacity-50');
                    video1.classList.remove('opacity-50');
                    video1.classList.add('opacity-0');
                }
            }, 6000); // Cambia cada 6 segundos
        }

        const iphoneTrack = document.getElementById('iphone-video-track');
        if (iphoneTrack) {
            let currentIndex = 0;
            const totalVideos = 7;
            let startY = 0;
            let isDragging = false;
            let currentTranslate = 0;
            let prevTranslate = 0;

            const setPositionByIndex = () => {
                currentTranslate = currentIndex * -iphoneTrack.clientHeight;
                prevTranslate = currentTranslate;
                // Authentic Instagram Reel springy ease-out transition
                iphoneTrack.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
                iphoneTrack.style.transform = `translateY(${currentTranslate}px)`;
            };

            // Auto scroll every 6s
            setInterval(() => {
                if (!isDragging) {
                    currentIndex = (currentIndex + 1) % totalVideos;
                    setPositionByIndex();
                }
            }, 6000);

            // Drag to scroll logic for desktop mouse users
            iphoneTrack.addEventListener('mousedown', (e) => {
                // Ignore on touch devices so it never blocks mobile page scrolling
                if (window.matchMedia('(pointer: coarse)').matches) return;
                isDragging = true;
                startY = e.pageY;
                iphoneTrack.style.transition = 'none'; // instant follow mouse
                iphoneTrack.classList.remove('cursor-grab');
                iphoneTrack.classList.add('cursor-grabbing');
            });

            const stopDrag = (e) => {
                if (!isDragging) return;
                isDragging = false;
                iphoneTrack.classList.add('cursor-grab');
                iphoneTrack.classList.remove('cursor-grabbing');

                const movedBy = e.pageY - startY;

                // Snap to next/prev if dragged enough (50px threshold)
                if (movedBy < -50 && currentIndex < totalVideos - 1) {
                    currentIndex += 1;
                }
                if (movedBy > 50 && currentIndex > 0) {
                    currentIndex -= 1;
                }
                setPositionByIndex();
            };

            iphoneTrack.addEventListener('mouseleave', stopDrag);
            iphoneTrack.addEventListener('mouseup', stopDrag);
            iphoneTrack.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const currentY = e.pageY;
                const movedBy = currentY - startY;
                iphoneTrack.style.transform = `translateY(${prevTranslate + movedBy}px)`;
            });

            // Touch support for mobile: Swipe horizontally or tap to change reel, let vertical swipe scroll the webpage smoothly!
            let touchStartX = 0;
            let touchStartY = 0;
            iphoneTrack.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, { passive: true });

            iphoneTrack.addEventListener('touchend', (e) => {
                const touchEndX = e.changedTouches[0].screenX;
                const touchEndY = e.changedTouches[0].screenY;
                const diffX = touchEndX - touchStartX;
                const diffY = touchEndY - touchStartY;

                // Horizontal swipe changes video reel (left = next, right = prev)
                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 40) {
                    if (diffX < 0 && currentIndex < totalVideos - 1) currentIndex++;
                    else if (diffX > 0 && currentIndex > 0) currentIndex--;
                    setPositionByIndex();
                }
                // Quick tap advances to next reel
                else if (Math.abs(diffX) < 10 && Math.abs(diffY) < 10) {
                    currentIndex = (currentIndex + 1) % totalVideos;
                    setPositionByIndex();
                }
            }, { passive: true });

            // Handle window resize to fix translate value
            window.addEventListener('resize', setPositionByIndex);
        }
    });
</script>

<?php include '../app/components/footer.php'; ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });
</script>