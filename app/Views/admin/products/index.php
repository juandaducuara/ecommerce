<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
    <a href="/admin/products/create" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
        + Nuevo Producto
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form action="/admin/products" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Buscar</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>"
                placeholder="Nombre o SKU..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Categoría</label>
            <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($categoryFilter ?? '') == $cat->id ? 'selected' : '' ?>>
                        <?= esc($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Estado</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm">Filtrar</button>
        <a href="/admin/products" class="text-sm text-gray-500 hover:text-gray-700 py-2">Limpiar</a>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Producto</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">SKU</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Categoría</th>
                <th class="text-right px-4 py-3 text-gray-600 font-medium">Precio</th>
                <th class="text-right px-4 py-3 text-gray-600 font-medium">Stock</th>
                <th class="text-center px-4 py-3 text-gray-600 font-medium">Estado</th>
                <th class="text-right px-4 py-3 text-gray-600 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No se encontraron productos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800"><?= esc($product->name) ?></div>
                            <?php if ($product->is_featured): ?>
                                <span class="text-xs text-yellow-600">Destacado</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs"><?= esc($product->sku) ?></td>
                        <td class="px-4 py-3">
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                <?= esc($product->category_name ?? 'Sin categoría') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">
                            $<?= number_format($product->price, 0, ',', '.') ?>
                            <?php if ($product->compare_price && $product->compare_price > $product->price): ?>
                                <br><span class="text-xs text-gray-400 line-through">$<?= number_format($product->compare_price, 0, ',', '.') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <?php if ($product->stock_qty <= 0): ?>
                                <span class="text-red-600 font-medium">Agotado</span>
                            <?php elseif ($product->stock_qty <= 5): ?>
                                <span class="text-yellow-600 font-medium"><?= $product->stock_qty ?></span>
                            <?php else: ?>
                                <span class="text-green-600"><?= $product->stock_qty ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($product->is_active): ?>
                                <span class="bg-green-50 text-green-700 px-2 py-1 rounded text-xs font-medium">Activo</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="/admin/products/<?= $product->id ?>/edit"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>
                                <form action="/admin/products/<?= $product->id ?>/delete" method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
