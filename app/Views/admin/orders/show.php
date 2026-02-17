<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
    $statusLabels = [
        'pending' => 'Pendiente', 'processing' => 'En proceso', 'confirmed' => 'Confirmado',
        'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado',
    ];
    $statusColors = [
        'pending' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
        'processing' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
        'confirmed' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
        'shipped' => 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
        'delivered' => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400',
        'cancelled' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400',
        'refunded' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    ];
    $paymentLabels = [
        'pending' => 'Pendiente', 'paid' => 'Pagado', 'failed' => 'Fallido',
        'refunded' => 'Reembolsado', 'partially_refunded' => 'Reembolso parcial',
    ];
    $paymentColors = [
        'pending' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
        'paid' => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400',
        'failed' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400',
        'refunded' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
        'partially_refunded' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
    ];
    $shipmentLabels = [
        'pending' => 'Pendiente', 'picked_up' => 'Recogido', 'in_transit' => 'En tránsito',
        'out_for_delivery' => 'En reparto', 'delivered' => 'Entregado', 'failed' => 'Fallido', 'returned' => 'Devuelto',
    ];
?>

<div class="flex items-center gap-4 mb-6">
    <a href="/admin/orders" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">&larr;</a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Pedido <?= esc($order->order_number) ?></h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Creado el <?= date('d/m/Y H:i', strtotime($order->created_at)) ?></p>
    </div>
    <div class="ml-auto flex gap-2">
        <span class="<?= $statusColors[$order->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' ?> px-3 py-1 rounded-lg text-sm font-medium">
            <?= $statusLabels[$order->status] ?? $order->status ?>
        </span>
        <span class="<?= $paymentColors[$order->payment_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' ?> px-3 py-1 rounded-lg text-sm font-medium">
            Pago: <?= $paymentLabels[$order->payment_status] ?? $order->payment_status ?>
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Columna principal -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Items del pedido -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Productos (<?= $order->items_count ?>)</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr>
                        <th class="text-left px-4 py-2 text-gray-600 dark:text-gray-300 font-medium">Producto</th>
                        <th class="text-center px-4 py-2 text-gray-600 dark:text-gray-300 font-medium">Cant.</th>
                        <th class="text-right px-4 py-2 text-gray-600 dark:text-gray-300 font-medium">Precio unit.</th>
                        <th class="text-right px-4 py-2 text-gray-600 dark:text-gray-300 font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    <?php foreach ($order->items as $item): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800 dark:text-gray-100"><?= esc($item->name) ?></span>
                                <br><span class="text-xs text-gray-400 dark:text-gray-500 font-mono"><?= esc($item->sku) ?></span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300"><?= $item->quantity ?></td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">$<?= number_format($item->unit_price, 0, ',', '.') ?></td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-100">$<?= number_format($item->total_price, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="px-4 py-4 bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700 space-y-1 text-sm">
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                    <span>Subtotal</span>
                    <span>$<?= number_format($order->subtotal, 0, ',', '.') ?></span>
                </div>
                <?php if ($order->discount > 0): ?>
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>Descuento<?= $order->coupon_code ? ' (' . esc($order->coupon_code) . ')' : '' ?></span>
                        <span>-$<?= number_format($order->discount, 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                    <span>Envío</span>
                    <span>$<?= number_format($order->shipping_cost, 0, ',', '.') ?></span>
                </div>
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                    <span>Impuestos (IVA)</span>
                    <span>$<?= number_format($order->tax, 0, ',', '.') ?></span>
                </div>
                <div class="flex justify-between font-bold text-gray-800 dark:text-gray-100 text-base pt-2 border-t dark:border-gray-600">
                    <span>Total</span>
                    <span>$<?= number_format($order->total, 0, ',', '.') ?> <?= esc($order->currency) ?></span>
                </div>
            </div>
        </div>

        <!-- Pagos -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Pagos</h2>
            </div>
            <?php if (empty($order->payments)): ?>
                <p class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">No hay pagos registrados.</p>
            <?php else: ?>
                <div class="divide-y dark:divide-gray-700">
                    <?php foreach ($order->payments as $payment): ?>
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        <?= strtoupper(esc($payment->gateway)) ?>
                                        <?= $payment->method ? '— ' . esc($payment->method) : '' ?>
                                    </span>
                                    <?php if ($payment->transaction_id): ?>
                                        <br><span class="text-xs text-gray-400 dark:text-gray-500 font-mono">TX: <?= esc($payment->transaction_id) ?></span>
                                    <?php endif; ?>
                                    <?php if ($payment->card_last_four): ?>
                                        <br><span class="text-xs text-gray-500 dark:text-gray-400">**** <?= esc($payment->card_last_four) ?> <?= esc($payment->card_brand) ?></span>
                                    <?php endif; ?>
                                    <?php if ($payment->error_message): ?>
                                        <br><span class="text-xs text-red-500 dark:text-red-400"><?= esc($payment->error_message) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">$<?= number_format($payment->amount, 0, ',', '.') ?></span>
                                    <br>
                                    <?php
                                        $pStatusColors = [
                                            'pending' => 'text-yellow-600 dark:text-yellow-400', 'processing' => 'text-blue-600 dark:text-blue-400',
                                            'approved' => 'text-green-600 dark:text-green-400', 'declined' => 'text-red-600 dark:text-red-400',
                                            'error' => 'text-red-600 dark:text-red-400', 'refunded' => 'text-gray-500 dark:text-gray-400',
                                            'cancelled' => 'text-gray-500 dark:text-gray-400',
                                        ];
                                    ?>
                                    <span class="text-xs font-medium <?= $pStatusColors[$payment->status] ?? 'text-gray-500 dark:text-gray-400' ?>">
                                        <?= ucfirst($payment->status) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Envíos -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Envíos</h2>
            </div>

            <?php if (!empty($order->shipments)): ?>
                <div class="divide-y dark:divide-gray-700">
                    <?php foreach ($order->shipments as $shipment): ?>
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-gray-100"><?= esc($shipment->carrier) ?></span>
                                    <?php if ($shipment->tracking_number): ?>
                                        <br>
                                        <?php if ($shipment->tracking_url): ?>
                                            <a href="<?= esc($shipment->tracking_url) ?>" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 font-mono hover:underline">
                                                <?= esc($shipment->tracking_number) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono"><?= esc($shipment->tracking_number) ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($shipment->estimated_delivery): ?>
                                        <br><span class="text-xs text-gray-500 dark:text-gray-400">Entrega estimada: <?= date('d/m/Y', strtotime($shipment->estimated_delivery)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs font-medium">
                                    <?= $shipmentLabels[$shipment->status] ?? $shipment->status ?>
                                </span>
                            </div>
                            <form action="/admin/orders/<?= $order->id ?>/shipments/<?= $shipment->id ?>/status" method="POST" class="flex gap-2">
                                <?= csrf_field() ?>
                                <select name="shipment_status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <?php foreach ($shipmentLabels as $val => $label): ?>
                                        <option value="<?= $val ?>" <?= $shipment->status === $val ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-1 rounded-lg text-xs hover:bg-gray-200 dark:hover:bg-gray-600">Actualizar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Agregar envío -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">Registrar nuevo envío</p>
                <form action="/admin/orders/<?= $order->id ?>/shipments" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?= csrf_field() ?>
                    <input type="text" name="carrier" placeholder="Transportadora *" required
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="tracking_number" placeholder="Nro. de seguimiento"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="url" name="tracking_url" placeholder="URL de rastreo"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="date" name="estimated_delivery" placeholder="Fecha estimada"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                            Registrar envío
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Actualizar estado -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Estado del pedido</h3>
            <form action="/admin/orders/<?= $order->id ?>/status" method="POST" class="space-y-3">
                <?= csrf_field() ?>
                <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach ($statusLabels as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $order->status === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="admin_notes" rows="3" placeholder="Notas internas..."
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= esc($order->admin_notes ?? '') ?></textarea>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg text-sm hover:bg-indigo-700">
                    Actualizar estado
                </button>
            </form>
        </div>

        <!-- Actualizar estado de pago -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Estado de pago</h3>
            <form action="/admin/orders/<?= $order->id ?>/payment-status" method="POST" class="space-y-3">
                <?= csrf_field() ?>
                <select name="payment_status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach ($paymentLabels as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $order->payment_status === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600">
                    Actualizar pago
                </button>
            </form>
        </div>

        <!-- Info del cliente -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Cliente</h3>
            <dl class="space-y-2 text-sm">
                <?php if ($customer): ?>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Nombre</dt>
                        <dd class="text-gray-800 dark:text-gray-100 font-medium"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></dd>
                    </div>
                <?php endif; ?>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="text-gray-800 dark:text-gray-100"><?= esc($order->customer_email) ?></dd>
                </div>
                <?php if ($order->customer_phone): ?>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="text-gray-800 dark:text-gray-100"><?= esc($order->customer_phone) ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>

        <?php if ($order->customer_notes): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Notas del cliente</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300"><?= nl2br(esc($order->customer_notes)) ?></p>
            </div>
        <?php endif; ?>

        <!-- Timestamps -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Historial</h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Creado</dt>
                    <dd class="text-gray-700 dark:text-gray-300"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></dd>
                </div>
                <?php if ($order->paid_at): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Pagado</dt>
                        <dd class="text-gray-700 dark:text-gray-300"><?= date('d/m/Y H:i', strtotime($order->paid_at)) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($order->shipped_at): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Enviado</dt>
                        <dd class="text-gray-700 dark:text-gray-300"><?= date('d/m/Y H:i', strtotime($order->shipped_at)) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($order->delivered_at): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Entregado</dt>
                        <dd class="text-gray-700 dark:text-gray-300"><?= date('d/m/Y H:i', strtotime($order->delivered_at)) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($order->cancelled_at): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Cancelado</dt>
                        <dd class="text-red-600 dark:text-red-400"><?= date('d/m/Y H:i', strtotime($order->cancelled_at)) ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
