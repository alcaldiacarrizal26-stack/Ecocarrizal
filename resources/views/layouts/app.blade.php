<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ecosocialismo - Alcaldía de Carrizal' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <!-- Incluir la Sidebar (Pasa dinámicamente la sección activa) -->
    <x-sidebar :active="$active ?? 'inicio'" />

    <!-- Área Principal -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Header superior para móviles y acciones rápidas -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30">
            <button 
                @click="sidebarOpen = !sidebarOpen" 
                class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl lg:hidden cursor-pointer"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Plataforma Digital de Ecosocialismo
            </div>
        </header>

        <!-- Contenido principal dinámico -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            {{ $slot }}
        </main>
    </div>

</body>
</html>