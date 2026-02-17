<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mi Tienda Online' ?></title>
    <!-- Anti-flicker: apply theme before first paint -->
    <script>
        (function(){
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col transition-colors duration-200">

    <!-- Navbar -->
    <?php
    $cartCount = 0;
    try {
        $cartModel = new \App\Infrastructure\Persistence\Models\CartModel();
        $userId    = session()->get('user_id');
        $cartRow   = $userId
            ? $cartModel->where('user_id', $userId)->first()
            : $cartModel->where('session_id', session_id())->first();
        $cartCount = $cartRow ? (int)$cartRow->items_count : 0;
    } catch (\Throwable $e) {}
    ?>
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="text-xl font-bold text-indigo-600 dark:text-indigo-400 flex-shrink-0">Mi Tienda</a>

                <!-- Desktop nav -->
                <div class="hidden sm:flex items-center gap-4">
                    <a href="/products" class="text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Productos</a>

                    <a href="/cart" class="relative text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-count <?= $cartCount > 0 ? '' : 'hidden' ?> absolute -top-2 -right-2 bg-indigo-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                            <?= $cartCount > 0 ? $cartCount : '' ?>
                        </span>
                    </a>

                    <!-- Dark mode toggle -->
                    <button onclick="toggleTheme()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition" title="Cambiar tema">
                        <!-- Moon: shown in light mode -->
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <!-- Sun: shown in dark mode -->
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    <?php if (session()->get('is_logged_in')): ?>
                        <span class="text-sm text-gray-600 dark:text-gray-300">Hola, <?= esc(session()->get('user_name')) ?></span>
                        <?php if (in_array(session()->get('user_role'), ['super-admin', 'admin'])): ?>
                            <a href="/admin" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-medium">Admin</a>
                        <?php endif; ?>
                        <a href="/account/orders" class="text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Mis pedidos</a>
                        <a href="/logout" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800">Salir</a>
                    <?php else: ?>
                        <a href="/login" class="text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Iniciar sesión</a>
                        <a href="/register" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">Registrarse</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile: carrito + toggle + hamburguesa -->
                <div class="flex sm:hidden items-center gap-3">
                    <a href="/cart" class="relative text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-count <?= $cartCount > 0 ? '' : 'hidden' ?> absolute -top-2 -right-2 bg-indigo-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                            <?= $cartCount > 0 ? $cartCount : '' ?>
                        </span>
                    </a>
                    <button onclick="toggleTheme()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                            class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden sm:hidden border-t dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="px-4 py-3 space-y-2">
                <a href="/products" class="block py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Productos</a>
                <?php if (session()->get('is_logged_in')): ?>
                    <div class="py-2 text-sm text-gray-500 dark:text-gray-400 border-t dark:border-gray-700">Hola, <strong><?= esc(session()->get('user_name')) ?></strong></div>
                    <?php if (in_array(session()->get('user_role'), ['super-admin', 'admin'])): ?>
                        <a href="/admin" class="block py-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium">Panel admin</a>
                    <?php endif; ?>
                    <a href="/account/orders" class="block py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Mis pedidos</a>
                    <a href="/logout" class="block py-2 text-sm text-red-600 dark:text-red-400 border-t dark:border-gray-700">Cerrar sesión</a>
                <?php else: ?>
                    <a href="/login" class="block py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 border-t dark:border-gray-700">Iniciar sesión</a>
                    <a href="/register" class="block py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg mb-4">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('dev_reset_url')): ?>
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg mb-4">
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
    <footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; <?= date('Y') ?> Mi Tienda Online. Todos los derechos reservados.
        </div>
    </footer>

    <script>
    function toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }
    </script>

</body>
</html>
