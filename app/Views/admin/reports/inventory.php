<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$movementLabels = [
    'in'          => ['label' => 'Entrada',    'color' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'],
    'out'         => ['label' => 'Salida',     'color' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'],
    'adjustment'  => ['label' => 'Ajuste',     'color' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300'],
    'reservation' => ['label' => 'Reserva',    'color' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300'],
    'release'     => ['label' => 'Liberación', 'color' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300'],
    'sale'        => ['label' => 'Venta',      'color' => 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'],
    'return'      => ['label' => 'Devolución', 'color' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300'],
];
?>

<!-- KPIs de inventario -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total productos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($kpis->total_products ?? 0) ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Unidades en stock</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($kpis->total_units ?? 0) ?></p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= number_format($kpis->total_reserved ?? 0) ?> reservadas</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sin stock</p>
        <p class="text-2xl font-bold <?= ($kpis->out_of_stock ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?> mt-1">
            <?= number_format($kpis->out_of_stock ?? 0) ?>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Agotados</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Bajo stock</p>
        <p class="text-2xl font-bold <?= ($kpis->low_stock ?? 0) > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' ?> mt-1">
            <?= number_format($kpis->low_stock ?? 0) ?>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Bajo umbral mínimo</p>
    </div>
</div>

<!-- Valor del inventario -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor al costo</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">$<?= number_format($stockValue->cost_value ?? 0, 0, ',', '.') ?></p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Inventario valorizado</p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor de venta</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">$<?= number_format($stockValue->sale_value ?? 0, 0, ',', '.') ?></p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Potencial de ingresos</p>
        </div>
    </div>
</div>

<!-- Filtros de lista de stock -->
<div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b dark:border-gray-700 flex flex-wrap items-center gap-3">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex-1">Estado de stock</h2>

        <form method="get" class="flex flex-wrap items-center gap-2">
            <!-- Filtro de estado -->
            <div class="flex text-xs border dark:border-gray-600 rounded-lg overflow-hidden">
                <?php foreach (['all' => 'Todos', 'low' => 'Bajo stock', 'out' => 'Agotados', 'untracked' => 'Sin tracking'] as $f => $fl): ?>
                    <button type="submit" name="filter" value="<?= $f ?>"
                            class="px-3 py-1.5 <?= $filter === $f ? 'brand-bg text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                        <?= $fl ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Búsqueda -->
            <div class="flex">
                <input type="text" name="search" value="<?= esc($search) ?>"
                       placeholder="Buscar producto o SKU…"
                       class="text-xs border dark:border-gray-600 rounded-l-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 w-44">
                <button type="submit"
                        class="brand-bg text-white px-3 py-1.5 rounded-r-lg text-xs">Buscar</button>
            </div>
            <?php if ($search || $filter !== 'all'): ?>
                <a href="/admin/reports/inventory" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($stockList)): ?>
        <p class="text-sm text-gray-400 text-center py-10">No se encontraron productos.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Producto</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Categoría</th>
                        <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Precio</th>
                        <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Stock</th>
                        <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Reservado</th>
                        <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Disponible</th>
                        <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Mínimo</th>
                        <th class="text-center px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    <?php foreach ($stockList as $p):
                        $available = $p->available ?? 0;
                        $threshold = $p->low_stock_threshold ?? 0;
                        $tracked   = $p->track_inventory ?? 0;

                        if (!$tracked) {
                            $badge = ['bg-gray-100 dark:bg-gray-700 text-gray-500', 'Sin tracking'];
                        } elseif ($available <= 0) {
                            $badge = ['bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300', 'Agotado'];
                        } elseif ($available <= $threshold) {
                            $badge = ['bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300', 'Bajo stock'];
                        } else {
                            $badge = ['bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300', 'OK'];
                        }
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 <?= !$p->is_active ? 'opacity-60' : '' ?>">
                            <td class="px-4 py-2.5">
                                <a href="/admin/products/<?= $p->id ?>/edit"
                                   class="font-medium text-gray-800 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    <?= esc($p->name) ?>
                                </a>
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-mono"><?= esc($p->sku) ?></p>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-500 dark:text-gray-400"><?= esc($p->category ?? '—') ?></td>
                            <td class="px-4 py-2.5 text-right text-xs text-gray-700 dark:text-gray-300">
                                $<?= number_format($p->price, 0, ',', '.') ?>
                            </td>
                            <td class="px-4 py-2.5 text-right text-xs text-gray-700 dark:text-gray-300"><?= $p->quantity ?? '—' ?></td>
                            <td class="px-4 py-2.5 text-right text-xs text-gray-500 dark:text-gray-400"><?= $p->reserved ?? 0 ?></td>
                            <td class="px-4 py-2.5 text-right text-xs font-semibold <?= $available <= 0 ? 'text-red-600 dark:text-red-400' : ($available <= $threshold ? 'text-orange-600 dark:text-orange-400' : 'text-gray-800 dark:text-gray-100') ?>">
                                <?= $tracked ? $available : '∞' ?>
                            </td>
                            <td class="px-4 py-2.5 text-right text-xs text-gray-500 dark:text-gray-400"><?= $threshold ?></td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="<?= $badge[0] ?> px-2 py-0.5 rounded text-xs font-medium"><?= $badge[1] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Últimos movimientos de stock -->
<?php if (!empty($movements)): ?>
<div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Últimos movimientos de inventario</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                <tr>
                    <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Fecha</th>
                    <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Producto</th>
                    <th class="text-center px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Tipo</th>
                    <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Cantidad</th>
                    <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Antes</th>
                    <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Después</th>
                    <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Referencia</th>
                    <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                <?php foreach ($movements as $m):
                    $mvInfo = $movementLabels[$m->type] ?? ['label' => $m->type, 'color' => 'bg-gray-100 text-gray-600'];
                ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            <?= date('d/m/Y H:i', strtotime($m->created_at)) ?>
                        </td>
                        <td class="px-4 py-2.5">
                            <p class="font-medium text-gray-800 dark:text-gray-100"><?= esc($m->product_name) ?></p>
                            <p class="text-gray-400 font-mono"><?= esc($m->sku) ?></p>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="<?= $mvInfo['color'] ?> px-2 py-0.5 rounded font-medium text-xs">
                                <?= $mvInfo['label'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold <?= in_array($m->type, ['in', 'release', 'return']) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>">
                            <?= in_array($m->type, ['in', 'release', 'return']) ? '+' : '-' ?><?= abs($m->quantity) ?>
                        </td>
                        <td class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400"><?= $m->quantity_before ?></td>
                        <td class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-200 font-medium"><?= $m->quantity_after ?></td>
                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">
                            <?php if ($m->reference_type): ?>
                                <span class="capitalize"><?= esc($m->reference_type) ?></span>
                                <?php if ($m->reference_id): ?>#<?= $m->reference_id ?><?php endif; ?>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">
                            <?= $m->first_name ? esc($m->first_name . ' ' . $m->last_name) : 'Sistema' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
