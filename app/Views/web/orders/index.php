<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Mis pedidos</h1>
    <a href="/products" class="text-sm text-indigo-600 hover:text-indigo-800">Seguir comprando →</a>
</div>

<?php if (empty($orders)): ?>
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-500 mb-6">Todavía no tienes pedidos</p>
        <a href="/products" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            Ver productos
        </a>
    </div>
<?php else: ?>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Pedido</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Fecha</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Estado</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Pago</th>
                    <th class="px-4 py-3 text-right text-gray-600 font-medium">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <span class="font-mono font-medium text-indigo-600"><?= esc($order->order_number) ?></span>
                        <div class="text-xs text-gray-400"><?= $order->items_count ?> producto(s)</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <?= date('d/m/Y', strtotime($order->created_at)) ?>
                        <div class="text-xs text-gray-400"><?= date('H:i', strtotime($order->created_at)) ?></div>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $statusLabels = [
                            'pending'    => ['text' => 'Pendiente',   'class' => 'bg-yellow-100 text-yellow-800'],
                            'processing' => ['text' => 'En proceso',  'class' => 'bg-blue-100 text-blue-800'],
                            'confirmed'  => ['text' => 'Confirmado',  'class' => 'bg-green-100 text-green-800'],
                            'shipped'    => ['text' => 'Enviado',     'class' => 'bg-purple-100 text-purple-800'],
                            'delivered'  => ['text' => 'Entregado',   'class' => 'bg-emerald-100 text-emerald-800'],
                            'cancelled'  => ['text' => 'Cancelado',   'class' => 'bg-red-100 text-red-800'],
                            'refunded'   => ['text' => 'Reembolsado', 'class' => 'bg-gray-100 text-gray-800'],
                        ];
                        $s = $statusLabels[$order->status] ?? ['text' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                        ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $s['class'] ?>"><?= $s['text'] ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $payLabels = [
                            'pending'  => ['text' => 'Pendiente', 'class' => 'text-yellow-600'],
                            'paid'     => ['text' => '✓ Pagado',  'class' => 'text-green-600'],
                            'failed'   => ['text' => 'Fallido',   'class' => 'text-red-600'],
                            'refunded' => ['text' => 'Reembolsado','class'=> 'text-gray-600'],
                        ];
                        $p = $payLabels[$order->payment_status] ?? ['text' => $order->payment_status, 'class' => 'text-gray-600'];
                        ?>
                        <span class="text-sm font-medium <?= $p['class'] ?>"><?= $p['text'] ?></span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800">
                        $<?= number_format($order->total, 0, ',', '.') ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/account/orders/<?= esc($order->order_number) ?>"
                           class="text-indigo-600 hover:text-indigo-800 font-medium">Ver →</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
