<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
try {
    $_hsm       = new \App\Infrastructure\Persistence\Models\SettingModel();
    $_hg        = $_hsm->getGroup('general');
    $_heroTitle = $_hg['hero_title']          ?? ('Bienvenido a ' . ($_hg['store_name'] ?? 'nuestra tienda'));
    $_heroSub   = $_hg['hero_subtitle']       ?? 'Encuentra los mejores productos con envío a todo Colombia.';
    $_brandCol  = $_hg['brand_primary_color'] ?? '#4f46e5';
} catch (\Throwable $_he) {
    $_heroTitle = 'Bienvenido a nuestra tienda';
    $_heroSub   = 'Encuentra los mejores productos con envío a todo Colombia.';
    $_brandCol  = '#4f46e5';
}
// Tono suave para fondos de iconos (hex + alfa 15%)
$_brandTint = $_brandCol . '26';
?>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="relative overflow-hidden rounded-2xl mb-10 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">

    <!-- Dot pattern decorativo -->
    <div class="absolute inset-0 pointer-events-none select-none"
         style="background-image: radial-gradient(circle, <?= esc($_brandCol) ?>22 1.5px, transparent 1.5px); background-size: 28px 28px;">
    </div>

    <!-- Orbs de color suave (sin degradado duro) -->
    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full pointer-events-none"
         style="background: <?= esc($_brandCol) ?>18; filter: blur(60px);"></div>
    <div class="absolute -bottom-20 -left-16 w-64 h-64 rounded-full pointer-events-none"
         style="background: <?= esc($_brandCol) ?>12; filter: blur(50px);"></div>

    <div class="relative px-6 md:px-16 py-14 md:py-20 text-center">

        <!-- Pill badge -->
        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-5 brand-bg text-white shadow-sm">
            <i class="bi bi-lightning-fill"></i> ¡Ofertas disponibles!
        </span>

        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 leading-tight max-w-3xl mx-auto">
            <?= esc($_heroTitle) ?>
        </h1>
        <p class="text-base md:text-lg text-gray-500 dark:text-gray-400 mb-8 max-w-xl mx-auto leading-relaxed">
            <?= esc($_heroSub) ?>
        </p>

        <!-- Buscador integrado -->
        <form action="/products" method="GET" class="max-w-lg mx-auto mb-8">
            <div class="flex rounded-xl overflow-hidden shadow-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                <input type="text" name="q" placeholder="¿Qué estás buscando?"
                    class="flex-1 border-0 px-5 py-3.5 text-gray-700 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:outline-none focus:ring-0 text-sm bg-white">
                <button type="submit"
                    class="brand-bg text-white px-6 py-3.5 font-semibold hover:opacity-90 transition text-sm whitespace-nowrap border-l border-white/20">
                    <i class="bi bi-search mr-1"></i> Buscar
                </button>
            </div>
        </form>

        <a href="/products"
           class="brand-bg text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition shadow-md inline-flex items-center gap-2 text-sm">
            Ver catálogo completo <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>


<!-- ═══════════════ BENEFICIOS ═══════════════ -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-10">
    <?php foreach ([
        ['bi-truck',            'Envío a Colombia',   'Despachos rápidos'],
        ['bi-shield-check',     'Compra segura',      'Pagos protegidos'],
        ['bi-arrow-return-left','Devoluciones',       'Hasta 30 días'],
        ['bi-headset',          'Soporte 24/7',       'Siempre disponibles'],
    ] as [$icon, $label, $sub]): ?>
    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center"
             style="background-color: <?= esc($_brandTint) ?>;">
            <i class="bi <?= $icon ?> brand-text text-lg"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 leading-snug"><?= $label ?></p>
            <p class="text-xs text-gray-400 dark:text-gray-500"><?= $sub ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>


<!-- ═══════════════ CATEGORÍAS ═══════════════ -->
<?php if (!empty($categories)): ?>
<section class="mb-10">
    <div class="flex justify-between items-end mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Explora por categoría</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Encuentra lo que buscas fácilmente</p>
        </div>
        <a href="/products" class="text-sm brand-text font-medium hover:opacity-80 transition flex items-center gap-1">
            Ver todo <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <?php foreach ($categories as $cat): ?>
            <a href="/categories/<?= esc($cat->slug) ?>"
               class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 text-center
                      hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 relative overflow-hidden">
                <!-- Línea de color arriba en hover -->
                <div class="absolute top-0 left-0 right-0 h-0.5 brand-bg scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>

                <div class="w-12 h-12 rounded-xl mx-auto mb-3 flex items-center justify-center"
                     style="background-color: <?= esc($_brandTint) ?>;">
                    <i class="bi <?= $cat->icon ? esc($cat->icon) : 'bi-grid' ?> text-2xl brand-text"></i>
                </div>
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 block leading-snug"><?= esc($cat->name) ?></span>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block"><?= (int)$cat->product_count ?> productos</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>


<!-- ═══════════════ PRODUCTOS DESTACADOS ═══════════════ -->
<?php if (!empty($featured)): ?>
<section class="mb-10">
    <div class="flex justify-between items-end mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Productos destacados</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Los favoritos de nuestros clientes</p>
        </div>
        <a href="/products?sort=popular" class="text-sm brand-text font-medium hover:opacity-80 transition flex items-center gap-1">
            Ver todos <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($featured as $product): ?>
            <?= view('components/product_card', ['product' => $product]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>


<!-- ═══════════════ NUEVOS PRODUCTOS ═══════════════ -->
<?php if (!empty($latest)): ?>
<section class="mb-10">
    <div class="flex justify-between items-end mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Nuevos productos</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Los últimos en llegar a nuestra tienda</p>
        </div>
        <a href="/products" class="text-sm brand-text font-medium hover:opacity-80 transition flex items-center gap-1">
            Ver todos <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($latest as $product): ?>
            <?= view('components/product_card', ['product' => $product]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
