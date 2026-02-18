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

<!-- Franja de color de marca (3px) -->
<div class="brand-bg w-full flex-shrink-0" style="height:3px"></div>

<!-- ═══════════════ NAVBAR ═══════════════ -->
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
<nav class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center gap-4">

            <!-- ── Marca / Logo ── -->
            <a href="/" class="flex items-center gap-2.5 flex-shrink-0 min-w-0">
                <?php if ($_storeLogo): ?>
                    <img src="/<?= esc($_storeLogo) ?>" alt="<?= esc($_storeName) ?>" class="h-9 w-auto object-contain">
                <?php else: ?>
                    <span class="w-8 h-8 rounded-xl brand-bg flex items-center justify-center text-white font-bold text-sm flex-shrink-0 select-none">
                        <?= mb_strtoupper(mb_substr($_storeName, 0, 1)) ?>
                    </span>
                <?php endif; ?>
                <span class="text-base font-bold text-gray-900 dark:text-white tracking-tight truncate"><?= esc($_storeName) ?></span>
            </a>

            <!-- ── Links centrales (desktop) ── -->
            <div class="hidden sm:flex items-center gap-0.5 flex-1">
                <a href="/products"
                   class="flex items-center gap-1.5 text-sm font-medium text-gray-600 dark:text-gray-300
                          hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800
                          px-3 py-2 rounded-lg transition">
                    <i class="bi bi-grid-3x3-gap text-base"></i>
                    Productos
                </a>
            </div>

            <!-- ── Acciones derecha (desktop) ── -->
            <div class="hidden sm:flex items-center gap-1">

                <!-- Carrito -->
                <a href="/cart"
                   class="relative flex items-center justify-center w-10 h-10 rounded-xl
                          text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white
                          hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <i class="bi bi-bag text-lg"></i>
                    <span class="cart-count <?= $cartCount > 0 ? '' : 'hidden' ?>
                                 absolute -top-0.5 -right-0.5 brand-bg text-white font-bold rounded-full
                                 flex items-center justify-center leading-none"
                          style="font-size:9px; width:16px; height:16px;">
                        <?= $cartCount > 0 ? $cartCount : '' ?>
                    </span>
                </a>

                <!-- Tema -->
                <button onclick="toggleTheme()"
                        class="flex items-center justify-center w-10 h-10 rounded-xl
                               text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white
                               hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        title="Cambiar tema">
                    <i class="bi bi-moon text-base dark:hidden"></i>
                    <i class="bi bi-sun text-base hidden dark:block"></i>
                </button>

                <!-- Separador vertical -->
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-700 mx-1"></div>

                <?php if (session()->get('is_logged_in')): ?>
                    <!-- Avatar con inicial -->
                    <div class="w-8 h-8 rounded-full brand-bg flex items-center justify-center
                                text-white font-bold text-sm flex-shrink-0 select-none ml-1">
                        <?= mb_strtoupper(mb_substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
                    </div>

                    <?php if (in_array(session()->get('user_role'), ['super-admin', 'admin'])): ?>
                        <a href="/admin"
                           class="text-sm font-semibold brand-text hover:opacity-75
                                  px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            Admin
                        </a>
                    <?php endif; ?>

                    <a href="/account/orders"
                       class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white
                              px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Pedidos
                    </a>
                    <a href="/logout"
                       class="text-sm text-red-500 dark:text-red-400
                              px-3 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        Salir
                    </a>
                <?php else: ?>
                    <a href="/login"
                       class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white
                              px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Iniciar sesión
                    </a>
                    <a href="/register"
                       class="brand-bg text-white text-sm font-semibold px-4 py-2 rounded-xl
                              hover:opacity-90 transition shadow-sm">
                        Registrarse
                    </a>
                <?php endif; ?>
            </div>

            <!-- ── Acciones derecha (móvil) ── -->
            <div class="flex sm:hidden items-center gap-0.5">
                <a href="/cart"
                   class="relative flex items-center justify-center w-9 h-9 rounded-xl
                          text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <i class="bi bi-bag text-lg"></i>
                    <span class="cart-count <?= $cartCount > 0 ? '' : 'hidden' ?>
                                 absolute -top-0.5 -right-0.5 brand-bg text-white font-bold rounded-full
                                 flex items-center justify-center leading-none"
                          style="font-size:9px; width:16px; height:16px;">
                        <?= $cartCount > 0 ? $cartCount : '' ?>
                    </span>
                </a>
                <button onclick="toggleTheme()"
                        class="flex items-center justify-center w-9 h-9 rounded-xl
                               text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <i class="bi bi-moon text-base dark:hidden"></i>
                    <i class="bi bi-sun text-base hidden dark:block"></i>
                </button>
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="flex items-center justify-center w-9 h-9 rounded-xl
                               text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <i class="bi bi-list text-xl"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- ── Menú móvil ── -->
    <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-3 py-3 space-y-0.5">

            <a href="/products"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium
                      text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="bi bi-grid-3x3-gap text-base text-gray-400 dark:text-gray-500"></i>
                Productos
            </a>

            <?php if (session()->get('is_logged_in')): ?>

                <!-- Usuario -->
                <div class="flex items-center gap-2.5 px-3 py-3 border-t border-gray-100 dark:border-gray-800 mt-1">
                    <div class="w-9 h-9 rounded-full brand-bg flex items-center justify-center
                                text-white font-bold text-sm flex-shrink-0 select-none">
                        <?= mb_strtoupper(mb_substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-none truncate">
                            <?= esc(session()->get('user_name')) ?>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-none">
                            <?= esc(session()->get('user_role')) ?>
                        </p>
                    </div>
                </div>

                <?php if (in_array(session()->get('user_role'), ['super-admin', 'admin'])): ?>
                    <a href="/admin"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold
                              brand-text hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <i class="bi bi-speedometer2 text-base"></i>
                        Panel admin
                    </a>
                <?php endif; ?>

                <a href="/account/orders"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm
                          text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <i class="bi bi-bag text-base text-gray-400 dark:text-gray-500"></i>
                    Mis pedidos
                </a>

                <a href="/logout"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm
                          text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20
                          transition border-t border-gray-100 dark:border-gray-800 mt-1">
                    <i class="bi bi-box-arrow-right text-base"></i>
                    Cerrar sesión
                </a>

            <?php else: ?>
                <div class="flex gap-2 px-1 pt-3 pb-1 border-t border-gray-100 dark:border-gray-800 mt-1">
                    <a href="/login"
                       class="flex-1 text-center py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                              border border-gray-200 dark:border-gray-700 rounded-xl
                              hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Iniciar sesión
                    </a>
                    <a href="/register"
                       class="flex-1 text-center py-2.5 text-sm font-semibold text-white
                              brand-bg rounded-xl hover:opacity-90 transition shadow-sm">
                        Registrarse
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Flash messages -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
            <i class="bi bi-check-circle text-base flex-shrink-0"></i>
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
            <i class="bi bi-exclamation-circle text-base flex-shrink-0"></i>
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl mb-4 text-sm">
            <ul class="list-disc list-inside space-y-0.5">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('dev_reset_url')): ?>
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-xl mb-4 text-sm">
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

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Columna 1: Marca -->
            <div class="space-y-3">
                <?php if ($_storeLogo): ?>
                    <img src="/<?= esc($_storeLogo) ?>" alt="<?= esc($_storeName) ?>" class="h-10 w-auto object-contain">
                <?php else: ?>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl brand-bg flex items-center justify-center text-white font-bold text-sm select-none">
                            <?= mb_strtoupper(mb_substr($_storeName, 0, 1)) ?>
                        </span>
                        <span class="text-base font-bold brand-text"><?= esc($_storeName) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($_tagline): ?>
                    <p class="text-sm text-gray-500 dark:text-gray-400"><?= esc($_tagline) ?></p>
                <?php endif; ?>
                <!-- Redes sociales -->
                <?php if ($_fbUrl || $_igUrl || $_twUrl || $_waNumber): ?>
                <div class="flex items-center gap-2 pt-1">
                    <?php if ($_fbUrl): ?>
                        <a href="<?= esc($_fbUrl) ?>" target="_blank" rel="noopener"
                           class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center
                                  text-gray-400 hover:bg-blue-100 hover:text-blue-600 dark:hover:bg-blue-900/30 dark:hover:text-blue-400 transition">
                            <i class="bi bi-facebook text-sm"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($_igUrl): ?>
                        <a href="<?= esc($_igUrl) ?>" target="_blank" rel="noopener"
                           class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center
                                  text-gray-400 hover:bg-pink-100 hover:text-pink-600 dark:hover:bg-pink-900/30 dark:hover:text-pink-400 transition">
                            <i class="bi bi-instagram text-sm"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($_twUrl): ?>
                        <a href="<?= esc($_twUrl) ?>" target="_blank" rel="noopener"
                           class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center
                                  text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white transition">
                            <i class="bi bi-twitter-x text-sm"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($_waNumber): ?>
                        <a href="https://wa.me/<?= esc(preg_replace('/\D/', '', $_waNumber)) ?>" target="_blank" rel="noopener"
                           class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center
                                  text-gray-400 hover:bg-green-100 hover:text-green-600 dark:hover:bg-green-900/30 dark:hover:text-green-400 transition">
                            <i class="bi bi-whatsapp text-sm"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Columna 2: Navegación -->
            <div>
                <h3 class="text-xs font-bold text-gray-900 dark:text-gray-200 uppercase tracking-widest mb-4">Tienda</h3>
                <ul class="space-y-2.5 text-sm text-gray-500 dark:text-gray-400">
                    <li>
                        <a href="/products" class="hover:text-gray-900 dark:hover:text-white transition flex items-center gap-2">
                            <i class="bi bi-grid-3x3-gap text-xs"></i> Todos los productos
                        </a>
                    </li>
                    <li>
                        <a href="/cart" class="hover:text-gray-900 dark:hover:text-white transition flex items-center gap-2">
                            <i class="bi bi-bag text-xs"></i> Mi carrito
                        </a>
                    </li>
                    <li>
                        <a href="/account/orders" class="hover:text-gray-900 dark:hover:text-white transition flex items-center gap-2">
                            <i class="bi bi-receipt text-xs"></i> Mis pedidos
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Contacto -->
            <?php if ($_storeEmail || $_storePhone || $_storeAddress): ?>
            <div>
                <h3 class="text-xs font-bold text-gray-900 dark:text-gray-200 uppercase tracking-widest mb-4">Contacto</h3>
                <ul class="space-y-2.5 text-sm text-gray-500 dark:text-gray-400">
                    <?php if ($_storeEmail): ?>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-envelope mt-0.5 flex-shrink-0 text-xs"></i>
                            <a href="mailto:<?= esc($_storeEmail) ?>" class="hover:text-gray-900 dark:hover:text-white transition break-all">
                                <?= esc($_storeEmail) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($_storePhone): ?>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-telephone mt-0.5 flex-shrink-0 text-xs"></i>
                            <a href="tel:<?= esc(preg_replace('/\s+/', '', $_storePhone)) ?>" class="hover:text-gray-900 dark:hover:text-white transition">
                                <?= esc($_storePhone) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($_storeAddress): ?>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-geo-alt mt-0.5 flex-shrink-0 text-xs"></i>
                            <span><?= esc($_storeAddress) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Barra inferior -->
    <div class="border-t border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-gray-400 dark:text-gray-600">
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
