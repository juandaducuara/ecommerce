<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$statusColors = [
    'pending'    => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300',
    'processing' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300',
    'confirmed'  => 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-300',
    'shipped'    => 'bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300',
    'delivered'  => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300',
    'cancelled'  => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300',
    'refunded'   => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
];
$statusLabels = [
    'pending' => 'Pendiente', 'processing' => 'En proceso', 'confirmed' => 'Confirmado',
    'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado',
];
$gatewayLabels = ['payu' => 'PayU', 'mercadopago' => 'MercadoPago', 'manual' => 'Manual'];
$dayNames = [1 => 'Dom', 2 => 'Lun', 3 => 'Mar', 4 => 'Mié', 5 => 'Jue', 6 => 'Vie', 7 => 'Sáb'];

$trendLabels   = array_map(fn($r) => $r->label, $trend);
$trendRevenue  = array_map(fn($r) => (float)$r->revenue, $trend);
$trendOrders   = array_map(fn($r) => (int)$r->orders, $trend);
?>

<!-- Filtros de período -->
<div class="flex flex-wrap items-center gap-3 mb-6">
    <div class="flex bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden text-sm shadow-sm">
        <?php foreach (['today' => 'Hoy', 'week' => '7 días', 'month' => 'Este mes', 'year' => 'Este año', 'custom' => 'Personalizado'] as $p => $label): ?>
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
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ingresos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            $<?= number_format($kpis->total_revenue ?? 0, 0, ',', '.') ?>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Pagos aprobados</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pedidos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            <?= number_format($kpis->total_orders ?? 0) ?>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            <?= $kpis->paid_orders ?? 0 ?> pagados · <?= $kpis->cancelled_orders ?? 0 ?> cancelados
        </p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ticket promedio</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            $<?= number_format($kpis->avg_order_value ?? 0, 0, ',', '.') ?>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Por pedido pagado</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Descuentos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
            $<?= number_format($kpis->total_discounts ?? 0, 0, ',', '.') ?>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            Envíos: $<?= number_format($kpis->total_shipping ?? 0, 0, ',', '.') ?>
        </p>
    </div>
</div>

<!-- Tendencia de ingresos (Chart) -->
<div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5 mb-6">
    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Tendencia de ingresos</h2>
    <?php if (empty($trend)): ?>
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-10">No hay datos para este período.</p>
    <?php else: ?>
        <div class="relative h-56">
            <canvas id="revenueChart"></canvas>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Estado de pedidos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Estado de pedidos</h2>
        <?php if (empty($statusBreakdown)): ?>
            <p class="text-sm text-gray-400 text-center py-6">Sin datos</p>
        <?php else: ?>
            <?php
            $maxCount = max(array_map(fn($r) => $r->count, $statusBreakdown));
            ?>
            <div class="space-y-3">
                <?php foreach ($statusBreakdown as $row): ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="<?= $statusColors[$row->status] ?? 'bg-gray-100 text-gray-600' ?> px-2 py-0.5 rounded font-medium">
                                <?= $statusLabels[$row->status] ?? $row->status ?>
                            </span>
                            <span class="text-gray-500 dark:text-gray-400"><?= $row->count ?> · $<?= number_format($row->total, 0, ',', '.') ?></span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="brand-bg h-1.5 rounded-full" style="width:<?= $maxCount > 0 ? round($row->count / $maxCount * 100) : 0 ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Por gateway de pago -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Por pasarela de pago</h2>
        <?php if (empty($byGateway)): ?>
            <p class="text-sm text-gray-400 text-center py-6">Sin datos</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php
                $totalGw = array_sum(array_map(fn($r) => $r->revenue, $byGateway));
                foreach ($byGateway as $gw):
                    $pct = $totalGw > 0 ? round($gw->revenue / $totalGw * 100) : 0;
                ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-200">
                                <?= esc($gatewayLabels[$gw->gateway] ?? $gw->gateway) ?>
                            </span>
                            <span class="text-gray-500 dark:text-gray-400"><?= $pct ?>% · <?= $gw->transactions ?> txn</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="brand-bg h-2 rounded-full" style="width:<?= $pct ?>%"></div>
                        </div>
                        <p class="text-right text-xs text-gray-500 dark:text-gray-400 mt-0.5">$<?= number_format($gw->revenue, 0, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Por día de la semana -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Pedidos por día</h2>
        <?php if (empty($byDayOfWeek)): ?>
            <p class="text-sm text-gray-400 text-center py-6">Sin datos</p>
        <?php else: ?>
            <?php $maxDow = max(array_map(fn($r) => $r->orders, $byDayOfWeek)); ?>
            <div class="space-y-2">
                <?php foreach ($byDayOfWeek as $dow): ?>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-8 text-gray-500 dark:text-gray-400 flex-shrink-0">
                            <?= $dayNames[$dow->dow] ?? $dow->dow ?>
                        </span>
                        <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-4 relative">
                            <div class="brand-bg h-4 rounded-full" style="width:<?= $maxDow > 0 ? round($dow->orders / $maxDow * 100) : 0 ?>%"></div>
                        </div>
                        <span class="w-6 text-right text-gray-700 dark:text-gray-200 font-medium"><?= $dow->orders ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tabla de tendencia detallada -->
<?php if (!empty($trend)): ?>
<div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalle por período</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                <tr>
                    <th class="text-left px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Período</th>
                    <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Pedidos</th>
                    <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Pagados</th>
                    <th class="text-right px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Ingresos</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                <?php foreach (array_reverse($trend) as $row): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300"><?= esc($row->label) ?></td>
                        <td class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-300"><?= $row->orders ?></td>
                        <td class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-300"><?= $row->paid_count ?></td>
                        <td class="px-4 py-2.5 text-right font-medium text-gray-900 dark:text-white">
                            $<?= number_format($row->revenue, 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700 font-semibold">
                <tr>
                    <td class="px-4 py-2.5 text-xs text-gray-600 dark:text-gray-300">Total</td>
                    <td class="px-4 py-2.5 text-right text-xs text-gray-700 dark:text-gray-200"><?= array_sum(array_map(fn($r) => $r->orders, $trend)) ?></td>
                    <td class="px-4 py-2.5 text-right text-xs text-gray-700 dark:text-gray-200"><?= $kpis->paid_orders ?? 0 ?></td>
                    <td class="px-4 py-2.5 text-right text-xs text-gray-900 dark:text-white">$<?= number_format($kpis->total_revenue ?? 0, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
    const labels  = <?= json_encode($trendLabels) ?>;
    const revenue = <?= json_encode($trendRevenue) ?>;
    const orders  = <?= json_encode($trendOrders) ?>;
    if (!labels.length) return;

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Ingresos (COP)',
                    data: revenue,
                    backgroundColor: 'rgba(99,102,241,0.7)',
                    borderColor: 'rgba(99,102,241,1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y',
                    order: 2,
                },
                {
                    label: 'Pedidos',
                    data: orders,
                    type: 'line',
                    borderColor: 'rgba(16,185,129,0.9)',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.4,
                    yAxisID: 'y1',
                    order: 1,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { labels: { color: textColor, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            if (ctx.datasetIndex === 0) return ' $' + ctx.parsed.y.toLocaleString('es-CO');
                            return ' ' + ctx.parsed.y + ' pedidos';
                        }
                    }
                }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } },
                y: {
                    position: 'left',
                    grid: { color: gridColor },
                    ticks: {
                        color: textColor, font: { size: 10 },
                        callback: v => '$' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v)
                    }
                },
                y1: {
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: textColor, font: { size: 10 } }
                }
            }
        }
    });
})();
</script>

<?= $this->endSection() ?>
