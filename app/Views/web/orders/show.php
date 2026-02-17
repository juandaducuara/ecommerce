<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav class="mb-4 text-sm text-gray-500">
    <a href="/account/orders" class="hover:text-indigo-600">Mis pedidos</a>
    <span class="mx-2">/</span>
    <span class="text-gray-800 font-medium"><?= esc($order->order_number) ?></span>
</nav>

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pedido <?= esc($order->order_number) ?></h1>
        <p class="text-sm text-gray-500">Realizado el <?= date('d \d\e F \d\e Y', strtotime($order->created_at)) ?></p>
    </div>

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
    <span class="px-4 py-2 rounded-full text-sm font-bold <?= $s['class'] ?>"><?= $s['text'] ?></span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Columna principal -->
    <div class="lg:col-span-2 space-y-4">

        <!-- Productos -->
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h2 class="font-bold text-gray-700 mb-4">Productos</h2>
            <div class="space-y-3">
                <?php foreach ($items as $item): ?>
                <div class="flex items-center gap-3 py-2 border-b last:border-0">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800"><?= esc($item->name) ?></p>
                        <p class="text-xs text-gray-400">SKU: <?= esc($item->sku) ?> · Cant: <?= $item->quantity ?></p>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-gray-800">$<?= number_format($item->total_price, 0, ',', '.') ?></div>
                        <div class="text-xs text-gray-400">$<?= number_format($item->unit_price, 0, ',', '.') ?> c/u</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Totales -->
            <div class="mt-4 space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span>
                    <span>$<?= number_format($order->subtotal, 0, ',', '.') ?></span>
                </div>
                <?php if ($order->discount > 0): ?>
                <div class="flex justify-between text-green-600">
                    <span>Descuento<?= $order->coupon_code ? ' (' . esc($order->coupon_code) . ')' : '' ?></span>
                    <span>−$<?= number_format($order->discount, 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between text-gray-500">
                    <span>Envío</span>
                    <?php if ($order->shipping_cost > 0): ?>
                        <span>$<?= number_format($order->shipping_cost, 0, ',', '.') ?></span>
                    <?php else: ?>
                        <span class="text-green-600">GRATIS</span>
                    <?php endif; ?>
                </div>
                <div class="border-t pt-2 flex justify-between font-bold text-gray-800 text-base">
                    <span>Total</span>
                    <span>$<?= number_format($order->total, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Historial de pagos -->
        <?php if (!empty($payments)): ?>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h2 class="font-bold text-gray-700 mb-3">Historial de pago</h2>
            <div class="space-y-2">
                <?php foreach ($payments as $payment):
                    $payStatus = [
                        'pending'  => ['text' => 'Pendiente', 'class' => 'text-yellow-600 bg-yellow-50'],
                        'approved' => ['text' => 'Aprobado',  'class' => 'text-green-700 bg-green-50'],
                        'declined' => ['text' => 'Rechazado', 'class' => 'text-red-700 bg-red-50'],
                        'error'    => ['text' => 'Error',     'class' => 'text-red-700 bg-red-50'],
                    ][$payment->status] ?? ['text' => $payment->status, 'class' => 'text-gray-600 bg-gray-50'];
                ?>
                <div class="flex items-center justify-between p-3 rounded-lg <?= $payStatus['class'] ?>">
                    <div>
                        <div class="font-medium text-sm"><?= $payStatus['text'] ?></div>
                        <div class="text-xs opacity-75">
                            <?= ucfirst($payment->gateway ?? 'N/A') ?>
                            <?= $payment->transaction_id ? '· Ref: ' . esc($payment->transaction_id) : '' ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold">$<?= number_format($payment->amount, 0, ',', '.') ?></div>
                        <?php if ($payment->processed_at): ?>
                            <div class="text-xs opacity-75"><?= date('d/m/Y H:i', strtotime($payment->processed_at)) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Columna lateral -->
    <div class="space-y-4">

        <!-- Estado del pedido -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <h2 class="font-bold text-gray-700 mb-3">Estado del pedido</h2>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Estado</span>
                    <span class="font-semibold <?= $s['class'] ?> px-2 py-0.5 rounded-full text-xs"><?= $s['text'] ?></span>
                </div>
                <?php
                $payLabels = [
                    'pending'  => ['text' => 'Pendiente', 'class' => 'text-yellow-600'],
                    'paid'     => ['text' => 'Pagado',    'class' => 'text-green-600'],
                    'failed'   => ['text' => 'Fallido',   'class' => 'text-red-600'],
                ];
                $p = $payLabels[$order->payment_status] ?? ['text' => $order->payment_status, 'class' => 'text-gray-600'];
                ?>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pago</span>
                    <span class="font-semibold <?= $p['class'] ?>"><?= $p['text'] ?></span>
                </div>
                <?php if ($order->paid_at): ?>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Pagado el</span>
                    <span class="text-gray-600"><?= date('d/m/Y H:i', strtotime($order->paid_at)) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order->shipped_at): ?>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Enviado el</span>
                    <span class="text-gray-600"><?= date('d/m/Y', strtotime($order->shipped_at)) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order->delivered_at): ?>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Entregado el</span>
                    <span class="text-gray-600"><?= date('d/m/Y', strtotime($order->delivered_at)) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dirección de envío -->
        <?php if ($order->shipping_data): ?>
        <?php $addr = json_decode($order->shipping_data, true); ?>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <h2 class="font-bold text-gray-700 mb-2">Dirección de entrega</h2>
            <div class="text-sm text-gray-600 space-y-0.5">
                <p class="font-medium text-gray-800"><?= esc(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')) ?></p>
                <p><?= esc($addr['address_line_1'] ?? '') ?></p>
                <?php if (!empty($addr['address_line_2'])): ?>
                    <p><?= esc($addr['address_line_2']) ?></p>
                <?php endif; ?>
                <p><?= esc($addr['city'] ?? '') ?>, <?= esc($addr['state'] ?? '') ?></p>
                <?php if (!empty($addr['postal_code'])): ?>
                    <p>CP: <?= esc($addr['postal_code']) ?></p>
                <?php endif; ?>
                <p>Tel: <?= esc($addr['phone'] ?? '') ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($order->customer_notes): ?>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <h2 class="font-bold text-gray-700 mb-2">Notas</h2>
            <p class="text-sm text-gray-600"><?= esc($order->customer_notes) ?></p>
        </div>
        <?php endif; ?>

    </div>
</div>

<div class="mt-6">
    <a href="/account/orders" class="text-sm text-indigo-600 hover:text-indigo-800">← Volver a mis pedidos</a>
</div>

<?= $this->endSection() ?>
