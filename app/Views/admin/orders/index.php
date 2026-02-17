<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión de Pedidos</h1>
</div>

<!-- Estadísticas rápidas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <p class="text-sm text-yellow-700">Pendientes</p>
        <p class="text-2xl font-bold text-yellow-800"><?= $stats['pending'] ?></p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <p class="text-sm text-blue-700">En proceso</p>
        <p class="text-2xl font-bold text-blue-800"><?= $stats['processing'] ?></p>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
        <p class="text-sm text-purple-700">Enviados</p>
        <p class="text-2xl font-bold text-purple-800"><?= $stats['shipped'] ?></p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form action="/admin/orders" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Buscar</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>"
                placeholder="Nro. orden, email o nombre..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Estado</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
            <label class="block text-xs text-gray-500 mb-1">Pago</label>
            <select name="payment_status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
            <label class="block text-xs text-gray-500 mb-1">Desde</label>
            <input type="date" name="date_from" value="<?= esc($dateFrom ?? '') ?>"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Hasta</label>
            <input type="date" name="date_to" value="<?= esc($dateTo ?? '') ?>"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm">Filtrar</button>
        <a href="/admin/orders" class="text-sm text-gray-500 hover:text-gray-700 py-2">Limpiar</a>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Pedido</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Cliente</th>
                <th class="text-right px-4 py-3 text-gray-600 font-medium">Total</th>
                <th class="text-center px-4 py-3 text-gray-600 font-medium">Estado</th>
                <th class="text-center px-4 py-3 text-gray-600 font-medium">Pago</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Fecha</th>
                <th class="text-right px-4 py-3 text-gray-600 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No se encontraron pedidos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $statusColors = [
                            'pending'    => 'bg-yellow-50 text-yellow-700',
                            'processing' => 'bg-blue-50 text-blue-700',
                            'confirmed'  => 'bg-indigo-50 text-indigo-700',
                            'shipped'    => 'bg-purple-50 text-purple-700',
                            'delivered'  => 'bg-green-50 text-green-700',
                            'cancelled'  => 'bg-red-50 text-red-700',
                            'refunded'   => 'bg-gray-100 text-gray-600',
                        ];
                        $paymentColors = [
                            'pending'            => 'bg-yellow-50 text-yellow-700',
                            'paid'               => 'bg-green-50 text-green-700',
                            'failed'             => 'bg-red-50 text-red-700',
                            'refunded'           => 'bg-gray-100 text-gray-600',
                            'partially_refunded' => 'bg-orange-50 text-orange-700',
                        ];
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono font-medium text-gray-800"><?= esc($order->order_number) ?></span>
                            <br><span class="text-xs text-gray-400"><?= $order->items_count ?> item(s)</span>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($order->first_name): ?>
                                <span class="text-gray-800"><?= esc($order->first_name . ' ' . $order->last_name) ?></span>
                                <br>
                            <?php endif; ?>
                            <span class="text-xs text-gray-500"><?= esc($order->customer_email) ?></span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">
                            $<?= number_format($order->total, 0, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="<?= $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' ?> px-2 py-1 rounded text-xs font-medium">
                                <?= $statuses[$order->status] ?? $order->status ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="<?= $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600' ?> px-2 py-1 rounded text-xs font-medium">
                                <?= $paymentStatuses[$order->payment_status] ?? $order->payment_status ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/admin/orders/<?= $order->id ?>"
                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Ver detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
