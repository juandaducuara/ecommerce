<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<!-- Filtros de período -->
<div class="flex flex-wrap items-center gap-3 mb-6">
    <div class="flex bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden text-sm shadow-sm">
        <?php foreach (['week' => '7 días', 'month' => 'Este mes', 'year' => 'Este año', 'custom' => 'Personalizado'] as $p => $label): ?>
            <a href="?period=<?= $p ?>"
               class="px-3 py-2 <?= $period === $p ? 'brand-bg text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($period === 'custom'): ?>
    <form method="get" class="flex items-center gap-2">
        <input type="hidden" name="period" value="custom">
        <input type="date" name="date_from" value="<?= esc($dateFrom) ?>"
               class="text-sm border dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
        <span class="text-gray-400">→</span>
        <input type="date" name="date_to" value="<?= esc($dateTo) ?>"
               class="text-sm border dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
        <button type="submit" class="brand-bg text-white px-4 py-2 rounded-lg text-sm font-medium">Aplicar</button>
    </form>
    <?php endif; ?>
</div>

<!-- KPIs de productos -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ingresos brutos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            $<?= number_format($productKpis->gross_revenue ?? 0, 0, ',', '.') ?>
        </p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Unidades vendidas</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            <?= number_format($productKpis->units_sold ?? 0) ?>
        </p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Productos únicos vendidos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            <?= number_format($productKpis->products_sold ?? 0) ?>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Top 10 por ingresos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top 10 por ingresos</h2>
            <span class="text-xs text-gray-400">Pedidos pagados</span>
        </div>
        <?php if (empty($topByRevenue)): ?>
            <p class="text-sm text-gray-400 text-center py-10">Sin ventas en este período.</p>
        <?php else: ?>
            <?php $maxRev = max(array_map(fn($r) => $r->total_revenue, $topByRevenue)); ?>
            <div class="divide-y dark:divide-gray-700">
                <?php foreach ($topByRevenue as $i => $p): ?>
                    <div class="px-5 py-3">
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 w-4 flex-shrink-0 mt-0.5"><?= $i + 1 ?></span>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <a href="/admin/products" class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate hover:text-indigo-600 dark:hover:text-indigo-400">
                                        <?= esc($p->product_name) ?>
                                    </a>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-shrink-0">
                                        $<?= number_format($p->total_revenue, 0, ',', '.') ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">SKU: <?= esc($p->sku) ?></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500"><?= $p->total_qty ?> uds</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500"><?= $p->order_count ?> pedidos</span>
                                </div>
                                <div class="mt-1.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="brand-bg h-1.5 rounded-full" style="width:<?= $maxRev > 0 ? round($p->total_revenue / $maxRev * 100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Top 10 por unidades -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top 10 por unidades</h2>
            <span class="text-xs text-gray-400">Pedidos pagados</span>
        </div>
        <?php if (empty($topByQty)): ?>
            <p class="text-sm text-gray-400 text-center py-10">Sin ventas en este período.</p>
        <?php else: ?>
            <?php $maxQty = max(array_map(fn($r) => $r->total_qty, $topByQty)); ?>
            <div class="divide-y dark:divide-gray-700">
                <?php foreach ($topByQty as $i => $p): ?>
                    <div class="px-5 py-3">
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 w-4 flex-shrink-0 mt-0.5"><?= $i + 1 ?></span>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                                        <?= esc($p->product_name) ?>
                                    </p>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-shrink-0">
                                        <?= number_format($p->total_qty) ?> uds
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">SKU: <?= esc($p->sku) ?></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">$<?= number_format($p->total_revenue, 0, ',', '.') ?></span>
                                </div>
                                <div class="mt-1.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-emerald-500" style="width:<?= $maxQty > 0 ? round($p->total_qty / $maxQty * 100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ingresos por categoría + Chart -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Ingresos por categoría</h2>
        <?php if (empty($byCategory)): ?>
            <p class="text-sm text-gray-400 text-center py-6">Sin datos</p>
        <?php else: ?>
            <?php $maxCat = max(array_map(fn($r) => $r->revenue, $byCategory)); ?>
            <div class="space-y-3">
                <?php foreach ($byCategory as $cat): ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-200"><?= esc($cat->category) ?></span>
                            <span class="text-gray-500 dark:text-gray-400"><?= $cat->units_sold ?> uds · <?= $cat->products_count ?> prods</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="brand-bg h-2 rounded-full" style="width:<?= $maxCat > 0 ? round($cat->revenue / $maxCat * 100) : 0 ?>%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-200 w-24 text-right">$<?= number_format($cat->revenue, 0, ',', '.') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Gráfico de categorías -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Distribución por categoría</h2>
        <?php if (empty($byCategory)): ?>
            <p class="text-sm text-gray-400 text-center py-6">Sin datos</p>
        <?php else: ?>
            <div class="relative h-48">
                <canvas id="categoryChart"></canvas>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Productos sin ventas + bajo stock -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Bajo stock -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Bajo stock</h2>
            <a href="/admin/reports/inventory?filter=low" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver inventario →</a>
        </div>
        <?php if (empty($lowStock)): ?>
            <p class="text-sm text-gray-400 text-center py-8">Sin alertas de stock</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                        <tr>
                            <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Producto</th>
                            <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Disp.</th>
                            <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Mín.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <?php foreach ($lowStock as $s): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2.5">
                                    <p class="font-medium text-gray-800 dark:text-gray-100"><?= esc($s->name) ?></p>
                                    <p class="text-gray-400"><?= esc($s->sku) ?></p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <span class="<?= $s->available <= 0 ? 'text-red-600 dark:text-red-400 font-bold' : 'text-orange-600 dark:text-orange-400 font-semibold' ?>">
                                        <?= $s->available ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400"><?= $s->low_stock_threshold ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sin ventas -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sin ventas en el período</h2>
        </div>
        <?php if (empty($noSales)): ?>
            <p class="text-sm text-gray-400 text-center py-8">¡Todos los productos tuvieron ventas!</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                        <tr>
                            <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Producto</th>
                            <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Precio</th>
                            <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <?php foreach ($noSales as $p): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2.5">
                                    <a href="/admin/products/<?= $p->id ?>/edit"
                                       class="font-medium text-gray-800 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        <?= esc($p->name) ?>
                                    </a>
                                    <p class="text-gray-400"><?= esc($p->sku) ?></p>
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-300">
                                    $<?= number_format($p->price, 0, ',', '.') ?>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <?php if (!$p->is_active): ?>
                                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 px-1.5 py-0.5 rounded">Inactivo</span>
                                    <?php else: ?>
                                        <span class="text-gray-700 dark:text-gray-300"><?= $p->stock ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
    <?php if (!empty($byCategory)): ?>
    const catLabels  = <?= json_encode(array_map(fn($r) => $r->category, $byCategory)) ?>;
    const catRevenue = <?= json_encode(array_map(fn($r) => (float)$r->revenue, $byCategory)) ?>;

    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const colors = ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#14b8a6','#f97316'];

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catRevenue,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: isDark ? '#1f2937' : '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { color: textColor, font: { size: 10 }, boxWidth: 12, padding: 8 } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' $' + ctx.parsed.toLocaleString('es-CO')
                    }
                }
            }
        }
    });
    <?php endif; ?>
})();
</script>

<?= $this->endSection() ?>
