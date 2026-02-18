<?php
$_sm           = new \App\Infrastructure\Persistence\Models\SettingModel();
$_storeName    = $_sm->getValue('store_name',          'general', 'Mi Tienda Online');
$_storeLogo    = $_sm->getValue('store_logo',          'general', '');
$_storeFavicon = $_sm->getValue('store_favicon',       'general', '');
$_brandColor   = $_sm->getValue('brand_primary_color', 'general', '#4f46e5');
$_tagline      = $_sm->getValue('tagline',             'general', '');
$_storeEmail   = $_sm->getValue('store_email',         'general', '');
$_storePhone   = $_sm->getValue('store_phone',         'general', '');
$_storeAddress = $_sm->getValue('store_address',       'general', '');
$_fbUrl        = $_sm->getValue('facebook_url',        'social',  '');
$_igUrl        = $_sm->getValue('instagram_url',       'social',  '');
$_twUrl        = $_sm->getValue('twitter_url',         'social',  '');
$_waNumber     = $_sm->getValue('whatsapp_number',     'social',  '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? $_storeName) ?></title>
    <?php $_favicon = $_storeFavicon ?: $_storeLogo; ?>
    <?php if ($_favicon): ?>
        <link rel="icon" href="/<?= esc($_favicon) ?>">
    <?php endif; ?>
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
    <style>
        :root { --brand: <?= esc($_brandColor) ?>; }
        .brand-text   { color: var(--brand) !important; }
        .brand-bg     { background-color: var(--brand) !important; }
        .brand-border { border-color: var(--brand) !important; }
    </style>
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
                <a href="/" class="flex items-center gap-2 flex-shrink-0">
                    <?php if ($_storeLogo): ?>
                        <img src="/<?= esc($_storeLogo) ?>" alt="<?= esc($_storeName) ?>" class="h-9 w-auto object-contain">
                    <?php endif; ?>
                    <span class="text-xl font-bold brand-text"><?= esc($_storeName) ?></span>
                </a>

                <!-- Desktop nav -->
                <div class="hidden sm:flex items-center gap-4">
                    <a href="/products" class="text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Productos</a>

                    <a href="/cart" class="relative text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-count <?= $cartCount > 0 ? '' : 'hidden' ?> absolute -top-2 -right-2 brand-bg text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
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
                        <a href="/register" class="brand-bg text-white text-sm px-4 py-2 rounded-lg hover:opacity-90 transition">Registrarse</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile: carrito + toggle + hamburguesa -->
                <div class="flex sm:hidden items-center gap-3">
                    <a href="/cart" class="relative text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-count <?= $cartCount > 0 ? '' : 'hidden' ?> absolute -top-2 -right-2 brand-bg text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
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
    <footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Columna 1: Marca -->
                <div class="space-y-3">
                    <?php if ($_storeLogo): ?>
                        <img src="/<?= esc($_storeLogo) ?>" alt="<?= esc($_storeName) ?>" class="h-10 w-auto object-contain">
                    <?php else: ?>
                        <span class="text-lg font-bold brand-text"><?= esc($_storeName) ?></span>
                    <?php endif; ?>
                    <?php if ($_tagline): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= esc($_tagline) ?></p>
                    <?php endif; ?>
                    <!-- Redes sociales -->
                    <?php if ($_fbUrl || $_igUrl || $_twUrl || $_waNumber): ?>
                    <div class="flex items-center gap-3 pt-1">
                        <?php if ($_fbUrl): ?>
                            <a href="<?= esc($_fbUrl) ?>" target="_blank" rel="noopener"
                               class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Facebook">
                                <i class="bi bi-facebook text-xl"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($_igUrl): ?>
                            <a href="<?= esc($_igUrl) ?>" target="_blank" rel="noopener"
                               class="text-gray-400 hover:text-pink-600 dark:hover:text-pink-400 transition" title="Instagram">
                                <i class="bi bi-instagram text-xl"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($_twUrl): ?>
                            <a href="<?= esc($_twUrl) ?>" target="_blank" rel="noopener"
                               class="text-gray-400 hover:text-sky-500 dark:hover:text-sky-400 transition" title="Twitter / X">
                                <i class="bi bi-twitter-x text-xl"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($_waNumber): ?>
                            <a href="https://wa.me/<?= esc(preg_replace('/\D/', '', $_waNumber)) ?>" target="_blank" rel="noopener"
                               class="text-gray-400 hover:text-green-500 dark:hover:text-green-400 transition" title="WhatsApp">
                                <i class="bi bi-whatsapp text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Columna 2: Navegación -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-3">Tienda</h3>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="/products" class="hover:brand-text hover:text-indigo-600 dark:hover:text-indigo-400 transition">Todos los productos</a></li>
                        <li><a href="/cart"     class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Mi carrito</a></li>
                        <li><a href="/account/orders" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Mis pedidos</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Contacto -->
                <?php if ($_storeEmail || $_storePhone || $_storeAddress): ?>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-3">Contacto</h3>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <?php if ($_storeEmail): ?>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-envelope mt-0.5 flex-shrink-0"></i>
                                <a href="mailto:<?= esc($_storeEmail) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition break-all"><?= esc($_storeEmail) ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_storePhone): ?>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-telephone mt-0.5 flex-shrink-0"></i>
                                <a href="tel:<?= esc(preg_replace('/\s+/', '', $_storePhone)) ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition"><?= esc($_storePhone) ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_storeAddress): ?>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-geo-alt mt-0.5 flex-shrink-0"></i>
                                <span><?= esc($_storeAddress) ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Barra inferior -->
        <div class="border-t dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-gray-400 dark:text-gray-500">
                &copy; <?= date('Y') ?> <?= esc($_storeName) ?>. Todos los derechos reservados.
            </div>
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
