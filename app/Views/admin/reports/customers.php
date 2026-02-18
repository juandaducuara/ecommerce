<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$trendLabels = array_map(fn($r) => $r->label, $newCustomersTrend);
$trendValues = array_map(fn($r) => (int)$r->new_customers, $newCustomersTrend);
?>

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

<!-- KPIs -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nuevos clientes</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($kpis->new_customers ?? 0) ?></p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">En el período</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total clientes</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($kpis->total_customers ?? 0) ?></p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Acumulado</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Compradores activos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($kpis->active_buyers ?? 0) ?></p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Con pedido pagado</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">LTV promedio</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">$<?= number_format($kpis->avg_ltv ?? 0, 0, ',', '.') ?></p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Valor de vida del cliente</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Tendencia de nuevos clientes -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Nuevos clientes por período</h2>
        <?php if (empty($newCustomersTrend)): ?>
            <p class="text-sm text-gray-400 text-center py-10">Sin datos para este período.</p>
        <?php else: ?>
            <div class="relative h-48">
                <canvas id="customersChart"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <!-- Segmentación por fidelidad -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Fidelización</h2>
        <?php
        $retTotal = ($retention->one_time ?? 0) + ($retention->occasional ?? 0) + ($retention->loyal ?? 0);
        $segments = [
            ['label' => 'Un solo pedido',   'value' => $retention->one_time ?? 0,   'color' => 'bg-gray-400',    'text' => 'Compradores únicos'],
            ['label' => '2–4 pedidos',      'value' => $retention->occasional ?? 0, 'color' => 'bg-indigo-400',  'text' => 'Compradores ocasionales'],
            ['label' => '5+ pedidos',       'value' => $retention->loyal ?? 0,      'color' => 'bg-emerald-400', 'text' => 'Clientes leales'],
        ];
        ?>
        <div class="space-y-4">
            <?php foreach ($segments as $seg): ?>
                <?php $pct = $retTotal > 0 ? round($seg['value'] / $retTotal * 100) : 0; ?>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600 dark:text-gray-300"><?= $seg['label'] ?></span>
                        <span class="font-medium text-gray-700 dark:text-gray-200"><?= $seg['value'] ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="<?= $seg['color'] ?> h-2.5 rounded-full" style="width:<?= $pct ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= $seg['text'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Gráfico donut de fidelización -->
        <?php if ($retTotal > 0): ?>
        <div class="relative h-32 mt-4">
            <canvas id="retentionChart"></canvas>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Top clientes -->
<div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top 10 clientes por gasto</h2>
    </div>
    <?php if (empty($topCustomers)): ?>
        <p class="text-sm text-gray-400 text-center py-10">Sin datos de compras.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr>
                        <th class="text-left px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">#</th>
                        <th class="text-left px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Cliente</th>
                        <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Pedidos</th>
                        <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Ticket prom.</th>
                        <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Gasto total</th>
                        <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Último pedido</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    <?php foreach ($topCustomers as $i => $c): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-xs font-bold text-gray-400 dark:text-gray-500"><?= $i + 1 ?></td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800 dark:text-gray-100">
                                    <?= esc($c->first_name . ' ' . $c->last_name) ?>
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500"><?= esc($c->email) ?></p>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-300"><?= $c->order_count ?></td>
                            <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-300">
                                $<?= number_format($c->avg_order, 0, ',', '.') ?>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                $<?= number_format($c->total_spent, 0, ',', '.') ?>
                            </td>
                            <td class="px-5 py-3 text-right text-xs text-gray-500 dark:text-gray-400">
                                <?= date('d/m/Y', strtotime($c->last_order_at)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Clientes con entregas pendientes -->
<?php if (!empty($pendingDelivery)): ?>
<div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-orange-400"></span>
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Clientes con pedidos pendientes de entrega</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                <tr>
                    <th class="text-left px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Cliente</th>
                    <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Pedidos activos</th>
                    <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Total</th>
                    <th class="text-right px-5 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Pedido más antiguo</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                <?php foreach ($pendingDelivery as $c): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 dark:text-gray-100"><?= esc($c->first_name . ' ' . $c->last_name) ?></p>
                            <p class="text-xs text-gray-400 dark:text-gray-500"><?= esc($c->email) ?></p>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded text-xs font-medium">
                                <?= $c->pending_orders ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800 dark:text-gray-100">
                            $<?= number_format($c->pending_total, 0, ',', '.') ?>
                        </td>
                        <td class="px-5 py-3 text-right text-xs text-gray-500 dark:text-gray-400">
                            <?= date('d/m/Y', strtotime($c->oldest_order)) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    <?php if (!empty($newCustomersTrend)): ?>
    new Chart(document.getElementById('customersChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($trendLabels) ?>,
            datasets: [{
                label: 'Nuevos clientes',
                data: <?= json_encode($trendValues) ?>,
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderColor: 'rgba(99,102,241,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } },
                y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 }, stepSize: 1 } }
            }
        }
    });
    <?php endif; ?>

    <?php if (($retention->one_time ?? 0) + ($retention->occasional ?? 0) + ($retention->loyal ?? 0) > 0): ?>
    new Chart(document.getElementById('retentionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Un pedido', '2–4 pedidos', '5+ pedidos'],
            datasets: [{
                data: [<?= $retention->one_time ?? 0 ?>, <?= $retention->occasional ?? 0 ?>, <?= $retention->loyal ?? 0 ?>],
                backgroundColor: ['#9ca3af', '#818cf8', '#34d399'],
                borderWidth: 2,
                borderColor: isDark ? '#1f2937' : '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { size: 9 }, boxWidth: 10, padding: 6 } }
            }
        }
    });
    <?php endif; ?>
})();
</script>

<?= $this->endSection() ?>
