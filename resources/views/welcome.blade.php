<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcaldía de Carrizal - Ecosocialismo y Ambiente</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body 
    x-data="{ 
        loginModalOpen: false,
        selectedCategory: null,
        selectedSubType: null
    }"
    @open-login-modal.window="
        loginModalOpen = true; 
        selectedCategory = $event.detail?.category || null;
        selectedSubType = $event.detail?.subType || null;
    "
    class="font-sans antialiased text-gray-800 bg-white"
>
    <x-header />

    <main>
        <x-hero />
        <x-content />
    </main>

    <x-footer />

    {{-- Modal de Login/Registro se cargará aquí --}}
    <x-auth-modal />
</body>
</html>