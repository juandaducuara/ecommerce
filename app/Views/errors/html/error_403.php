<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-300 mb-4">403</h1>
        <p class="text-xl text-gray-600 mb-2">Acceso denegado</p>
        <p class="text-gray-500 mb-6"><?= esc($message ?? 'No tienes permisos para acceder a esta página.') ?></p>
        <a href="/" class="text-indigo-600 hover:text-indigo-800 font-medium">Volver al inicio</a>
    </div>
</body>
</html>
