<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

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
    $statusLabels = [
        'pending' => 'Pendiente', 'processing' => 'En proceso', 'confirmed' => 'Confirmado',
        'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado',
    ];
?>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pedidos totales</p>
        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1"><?= number_format($totalOrders) ?></p>
        <div class="mt-3 flex gap-2 text-xs">
            <span class="bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-2 py-0.5 rounded"><?= $pendingOrders ?> pendientes</span>
            <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded"><?= $processingOrders ?> en proceso</span>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Productos</p>
        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1"><?= number_format($totalProducts) ?></p>
        <a href="/admin/products" class="mt-3 inline-block text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver catálogo &rarr;</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Clientes</p>
        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1"><?= number_format($totalUsers) ?></p>
        <a href="/admin/users" class="mt-3 inline-block text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver usuarios &rarr;</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ingresos este mes</p>
        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">$<?= number_format($monthRevenue, 0, ',', '.') ?></p>
        <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Pagos aprobados</p>
    </div>
</div>

<!-- Estado de pedidos + tabla reciente -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Pedidos recientes -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Pedidos recientes</h2>
            <a href="/admin/orders" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver todos</a>
        </div>
        <?php if (empty($recentOrders)): ?>
            <p class="px-6 py-8 text-sm text-gray-500 dark:text-gray-400 text-center">No hay pedidos aún.</p>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr>
                        <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Pedido</th>
                        <th class="text-left px-4 py-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Cliente</th>
                        <th class="text-right px-4 py-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Total</th>
                        <th class="text-center px-4 py-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    <?php foreach ($recentOrders as $order): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <a href="/admin/orders/<?= $order->id ?>" class="font-mono text-indigo-600 dark:text-indigo-400 hover:underline text-xs">
                                    <?= esc($order->order_number) ?>
                                </a>
                                <div class="text-xs text-gray-400 dark:text-gray-500"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">
                                <?= $order->first_name ? esc($order->first_name . ' ' . $order->last_name) : '—' ?>
                                <div class="text-gray-400 dark:text-gray-500"><?= esc($order->customer_email) ?></div>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-xs text-gray-800 dark:text-gray-100">
                                $<?= number_format($order->total, 0, ',', '.') ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="<?= $statusColors[$order->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' ?> px-2 py-0.5 rounded text-xs font-medium">
                                    <?= $statusLabels[$order->status] ?? $order->status ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Panel de estado -->
    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Estado de pedidos</h2>
            <div class="space-y-3">
                <a href="/admin/orders?status=pending" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-3 px-3 py-2 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Pendientes</span>
                    <span class="bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-400 text-xs font-bold px-2.5 py-0.5 rounded-full"><?= $pendingOrders ?></span>
                </a>
                <a href="/admin/orders?status=processing" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-3 px-3 py-2 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-300">En proceso</span>
                    <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 text-xs font-bold px-2.5 py-0.5 rounded-full"><?= $processingOrders ?></span>
                </a>
                <a href="/admin/orders?status=shipped" class="flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-3 px-3 py-2 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Enviados</span>
                    <span class="bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-400 text-xs font-bold px-2.5 py-0.5 rounded-full"><?= $shippedOrders ?></span>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">Accesos rápidos</h2>
            <div class="space-y-2">
                <a href="/admin/orders" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 py-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Gestión de pedidos
                </a>
                <a href="/admin/products" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 py-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Catálogo de productos
                </a>
                <a href="/admin/users" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 py-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Usuarios
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
