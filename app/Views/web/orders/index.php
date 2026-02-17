<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mis pedidos</h1>
    <a href="/products" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Seguir comprando →</a>
</div>

<?php if (empty($orders)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Todavía no tienes pedidos</p>
        <a href="/products" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            Ver productos
        </a>
    </div>
<?php else: ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-300 font-medium">Pedido</th>
                    <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-300 font-medium">Fecha</th>
                    <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-300 font-medium">Estado</th>
                    <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-300 font-medium">Pago</th>
                    <th class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 font-medium">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-4 py-3">
                        <span class="font-mono font-medium text-indigo-600 dark:text-indigo-400"><?= esc($order->order_number) ?></span>
                        <div class="text-xs text-gray-400 dark:text-gray-500"><?= $order->items_count ?> producto(s)</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        <?= date('d/m/Y', strtotime($order->created_at)) ?>
                        <div class="text-xs text-gray-400 dark:text-gray-500"><?= date('H:i', strtotime($order->created_at)) ?></div>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $statusLabels = [
                            'pending'    => ['text' => 'Pendiente',   'class' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300'],
                            'processing' => ['text' => 'En proceso',  'class' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300'],
                            'confirmed'  => ['text' => 'Confirmado',  'class' => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300'],
                            'shipped'    => ['text' => 'Enviado',     'class' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300'],
                            'delivered'  => ['text' => 'Entregado',   'class' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300'],
                            'cancelled'  => ['text' => 'Cancelado',   'class' => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300'],
                            'refunded'   => ['text' => 'Reembolsado', 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'],
                        ];
                        $s = $statusLabels[$order->status] ?? ['text' => $order->status, 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'];
                        ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $s['class'] ?>"><?= $s['text'] ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $payLabels = [
                            'pending'  => ['text' => 'Pendiente', 'class' => 'text-yellow-600 dark:text-yellow-400'],
                            'paid'     => ['text' => '✓ Pagado',  'class' => 'text-green-600 dark:text-green-400'],
                            'failed'   => ['text' => 'Fallido',   'class' => 'text-red-600 dark:text-red-400'],
                            'refunded' => ['text' => 'Reembolsado','class'=> 'text-gray-600 dark:text-gray-400'],
                        ];
                        $p = $payLabels[$order->payment_status] ?? ['text' => $order->payment_status, 'class' => 'text-gray-600 dark:text-gray-400'];
                        ?>
                        <span class="text-sm font-medium <?= $p['class'] ?>"><?= $p['text'] ?></span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-gray-100">
                        $<?= number_format($order->total, 0, ',', '.') ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/account/orders/<?= esc($order->order_number) ?>"
                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Ver →</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
