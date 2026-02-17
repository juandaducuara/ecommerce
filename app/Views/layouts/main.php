<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mi Tienda Online' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="text-xl font-bold text-indigo-600">Mi Tienda Online</a>

                <div class="flex items-center gap-4">
                    <?php if (session()->get('is_logged_in')): ?>
                        <span class="text-sm text-gray-600">Hola, <?= esc(session()->get('user_name')) ?></span>

                        <?php if (in_array(session()->get('user_role'), ['super-admin', 'admin'])): ?>
                            <a href="/admin" class="text-sm text-indigo-600 hover:text-indigo-800">Admin</a>
                        <?php endif; ?>

                        <a href="/logout" class="text-sm text-red-600 hover:text-red-800">Cerrar sesión</a>
                    <?php else: ?>
                        <a href="/login" class="text-sm text-gray-600 hover:text-indigo-600">Iniciar sesión</a>
                        <a href="/register" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('dev_reset_url')): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-4">
                <strong>[DEV]</strong> Link de reset:
                <a href="<?= esc(session()->getFlashdata('dev_reset_url')) ?>" class="underline break-all">
                    <?= esc(session()->getFlashdata('dev_reset_url')) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> Mi Tienda Online. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>
