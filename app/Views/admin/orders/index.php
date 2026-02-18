<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Pedidos</h1>
</div>

<!-- Estadísticas rápidas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
        <p class="text-sm text-yellow-700 dark:text-yellow-400">Pendientes</p>
        <p class="text-2xl font-bold text-yellow-800 dark:text-yellow-300"><?= $stats['pending'] ?></p>
    </div>
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <p class="text-sm text-blue-700 dark:text-blue-400">En proceso</p>
        <p class="text-2xl font-bold text-blue-800 dark:text-blue-300"><?= $stats['processing'] ?></p>
    </div>
    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
        <p class="text-sm text-purple-700 dark:text-purple-400">Enviados</p>
        <p class="text-2xl font-bold text-purple-800 dark:text-purple-300"><?= $stats['shipped'] ?></p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-4 mb-6">
    <form action="/admin/orders" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>"
                placeholder="Nro. orden, email o nombre..."
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Estado</label>
            <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                <?php
                    $statuses = [
                        'pending' => 'Pendiente', 'processing' => 'En proceso', 'confirmed' => 'Confirmado',
                        'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado',
                    ];
                    foreach ($statuses as $val => $label):
                ?>
                    <option value="<?= $val ?>" <?= ($statusFilter ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Pago</label>
            <select name="payment_status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                <?php
                    $paymentStatuses = [
                        'pending' => 'Pendiente', 'paid' => 'Pagado', 'failed' => 'Fallido',
                        'refunded' => 'Reembolsado', 'partially_refunded' => 'Reembolso parcial',
                    ];
                    foreach ($paymentStatuses as $val => $label):
                ?>
                    <option value="<?= $val ?>" <?= ($paymentFilter ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Desde</label>
            <input type="date" name="date_from" value="<?= esc($dateFrom ?? '') ?>"
                class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
            <input type="date" name="date_to" value="<?= esc($dateTo ?? '') ?>"
                class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 text-sm">Filtrar</button>
        <a href="/admin/orders" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2">Limpiar</a>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Pedido</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Cliente</th>
                <th class="text-right px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Total</th>
                <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Estado</th>
                <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Pago</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Fecha</th>
                <th class="text-right px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-gray-700">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No se encontraron pedidos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $statusColors = [
                            'pending'    => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                            'processing' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                            'confirmed'  => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
                            'shipped'    => 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                            'delivered'  => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                            'cancelled'  => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                            'refunded'   => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        ];
                        $paymentColors = [
                            'pending'            => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                            'paid'               => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                            'failed'             => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                            'refunded'           => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                            'partially_refunded' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
                        ];
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            <span class="font-mono font-medium text-gray-800 dark:text-gray-100"><?= esc($order->order_number) ?></span>
                            <br><span class="text-xs text-gray-400 dark:text-gray-500"><?= $order->items_count ?> item(s)</span>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($order->first_name): ?>
                                <span class="text-gray-800 dark:text-gray-100"><?= esc($order->first_name . ' ' . $order->last_name) ?></span>
                                <br>
                            <?php endif; ?>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?= esc($order->customer_email) ?></span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-100">
                            $<?= number_format($order->total, 0, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="<?= $statusColors[$order->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' ?> px-2 py-1 rounded text-xs font-medium">
                                <?= $statuses[$order->status] ?? $order->status ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="<?= $paymentColors[$order->payment_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' ?> px-2 py-1 rounded text-xs font-medium">
                                <?= $paymentStatuses[$order->payment_status] ?? $order->payment_status ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/admin/orders/<?= $order->id ?>"
                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-xs font-medium">Ver detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?= $pager->links('default', 'admin_pager') ?>
</div>

<?= $this->endSection() ?>
