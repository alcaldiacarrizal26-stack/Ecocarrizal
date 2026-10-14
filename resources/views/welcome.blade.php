<!DOCTYPE html>
<html lang="es" x-data="{ mobileMenu: false, activeTab: 'inicio' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Ambiental · Alcaldía de Carrizal</title>

    <!-- Tailwind CSS v4 CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --color-primary: #05772a;
            --color-secondary: #0e7970;
            --color-mint: #78ad8b;
            --color-soft-green: #aecbc5;
            --color-dark-green: #347a4b;
            --color-teal: #66a198;
            --color-gray-light: #c5c6c6;
            --color-white: #ffffff;
            --color-black: #0a0a0a;
        }

        .bg-primary { background-color: var(--color-primary); }
        .bg-primary\/10 { background-color: rgba(5, 119, 42, 0.1); }
        .bg-primary\/5 { background-color: rgba(5, 119, 42, 0.05); }
        .text-primary { color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }
        .hover\:bg-primary:hover { background-color: var(--color-primary); }
        .hover\:text-primary:hover { color: var(--color-primary); }
        .hover\:border-primary:hover { border-color: var(--color-primary); }
        .bg-secondary { background-color: var(--color-secondary); }
        .text-secondary { color: var(--color-secondary); }
        .hover\:bg-secondary:hover { background-color: var(--color-secondary); }

        .bg-gradient-soft {
            background: linear-gradient(135deg, #f0f7f4 0%, #e4f0eb 100%);
        }
        .text-gradient-primary {
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary), var(--color-mint));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-white text-slate-800 min-h-screen flex flex-col font-sans antialiased selection:bg-primary selection:text-white">

    <!-- Header Normalizado -->
    <x-welcome.header />

    <!-- Contenido Modular Dinámico por Pestañas -->
    <main class="flex-1">
        <div x-show="activeTab === 'inicio'" x-transition.opacity>
            <x-welcome.inicio />
        </div>

        <div x-show="activeTab === 'servicios'" x-cloak x-transition.opacity>
            <x-welcome.servicios />
        </div>

        <div x-show="activeTab === 'marco-legal'" x-cloak x-transition.opacity>
            <x-welcome.marco-legal />
        </div>

        <div x-show="activeTab === 'especies'" x-cloak x-transition.opacity>
            <x-welcome.especies />
        </div>

        <div x-show="activeTab === 'contacto'" x-cloak x-transition.opacity>
            <x-welcome.contacto />
        </div>
    </main>

    <!-- Footer Normalizado -->
    <x-welcome.footer />

</body>
</html>