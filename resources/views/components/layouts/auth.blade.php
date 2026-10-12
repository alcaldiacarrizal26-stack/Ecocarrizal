<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Acceso - Ecosocialismo Carrizal' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen flex flex-col justify-center items-center p-4">
    
    <div class="w-full max-w-md">
        {{ $slot }}
    </div>

</body>
</html>