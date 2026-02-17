<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 md:p-12 mb-10 text-white">
    <h1 class="text-3xl md:text-4xl font-bold mb-3">Bienvenido a Mi Tienda Online</h1>
    <p class="text-indigo-100 mb-6 max-w-xl">Encuentra los mejores productos de tecnología con envío a todo Colombia.</p>
    <div class="flex gap-3">
        <a href="/products" class="bg-white text-indigo-600 px-6 py-2 rounded-lg font-medium hover:bg-indigo-50 transition">
            Ver productos
        </a>
    </div>
</div>

<!-- Búsqueda rápida -->
<div class="mb-10">
    <form action="/products" method="GET" class="max-w-xl mx-auto">
        <div class="flex">
            <input type="text" name="q" placeholder="Buscar productos..."
                class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 rounded-l-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-r-lg hover:bg-indigo-700 transition font-medium">
                Buscar
            </button>
        </div>
    </form>
</div>

<!-- Categorías -->
<?php if (!empty($categories)): ?>
<section class="mb-10">
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Categorías</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <?php foreach ($categories as $cat): ?>
            <a href="/categories/<?= esc($cat->slug) ?>"
                class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-4 text-center hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500 transition">
                <?php if ($cat->icon): ?>
                    <i class="bi <?= esc($cat->icon) ?> text-2xl text-indigo-500 dark:text-indigo-400 mb-2 block"></i>
                <?php endif; ?>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-100"><?= esc($cat->name) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Productos destacados -->
<?php if (!empty($featured)): ?>
<section class="mb-10">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Productos destacados</h2>
        <a href="/products?sort=popular" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Ver todos</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($featured as $product): ?>
            <?= view('components/product_card', ['product' => $product]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Productos recientes -->
<?php if (!empty($latest)): ?>
<section class="mb-10">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Nuevos productos</h2>
        <a href="/products" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Ver todos</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($latest as $product): ?>
            <?= view('components/product_card', ['product' => $product]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
