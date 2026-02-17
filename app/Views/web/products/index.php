<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Toggle filtros móvil -->
<div class="lg:hidden mb-4">
    <button onclick="document.getElementById('filters-panel').classList.toggle('hidden')"
            class="flex items-center justify-center gap-2 w-full bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
        </svg>
        Filtros y ordenar
    </button>
</div>

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Sidebar filtros -->
    <aside id="filters-panel" class="hidden lg:block lg:w-64 shrink-0">
        <form action="/products" method="GET" class="space-y-6">
            <?php if ($search): ?>
                <input type="hidden" name="q" value="<?= esc($search) ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Buscar</label>
                <input type="text" name="q" value="<?= esc($search ?? '') ?>" placeholder="Nombre o SKU..."
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Categoría</label>
                <div class="space-y-1">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <input type="radio" name="category" value="" <?= empty($categoryFilter) ? 'checked' : '' ?>>
                        Todas
                    </label>
                    <?php foreach ($categories as $cat): ?>
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <input type="radio" name="category" value="<?= $cat->id ?>"
                                <?= ($categoryFilter ?? '') == $cat->id ? 'checked' : '' ?>>
                            <?= esc($cat->name) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Precio (COP)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="min_price" value="<?= esc($minPrice ?? '') ?>" placeholder="Mín"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="number" name="max_price" value="<?= esc($maxPrice ?? '') ?>" placeholder="Máx"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Ordenar por</label>
                <select name="sort" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="newest" <?= ($sort ?? '') === 'newest' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Menor precio</option>
                    <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Mayor precio</option>
                    <option value="popular" <?= ($sort ?? '') === 'popular' ? 'selected' : '' ?>>Más vendidos</option>
                    <option value="name" <?= ($sort ?? '') === 'name' ? 'selected' : '' ?>>Nombre A-Z</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                Aplicar filtros
            </button>
            <a href="/products" class="block text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Limpiar filtros</a>
        </form>
    </aside>

    <!-- Productos -->
    <div class="flex-1">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100"><?= esc($title) ?></h1>
            <span class="text-sm text-gray-500 dark:text-gray-400"><?= count($products) ?> producto(s)</span>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No se encontraron productos.</p>
                <a href="/products" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-sm font-medium">Ver todos los productos</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($products as $product): ?>
                    <?= view('components/product_card', ['product' => $product]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
