<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto py-8">

    <!-- Icono de éxito -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">¡Pedido confirmado!</h1>
        <p class="text-gray-500 dark:text-gray-400">Gracias por tu compra. Te enviaremos un correo con los detalles.</p>
    </div>

    <!-- Número de orden -->
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-xl p-4 text-center mb-6">
        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Número de pedido</p>
        <p class="text-2xl font-bold text-indigo-800 dark:text-indigo-300 tracking-wide"><?= esc($order->order_number) ?></p>
    </div>

    <!-- Estado -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-sm">
            <div>
                <div class="text-gray-500 dark:text-gray-400 mb-1">Estado del pedido</div>
                <div class="font-semibold <?= $order->status === 'confirmed' ? 'text-green-600 dark:text-green-400' : 'text-orange-500 dark:text-orange-400' ?>">
                    <?= match($order->status) {
                        'pending'    => 'Pendiente',
                        'processing' => 'En proceso',
                        'confirmed'  => 'Confirmado',
                        default      => ucfirst($order->status),
                    } ?>
                </div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 mb-1">Pago</div>
                <div class="font-semibold <?= $order->payment_status === 'paid' ? 'text-green-600 dark:text-green-400' : 'text-orange-500 dark:text-orange-400' ?>">
                    <?= match($order->payment_status) {
                        'paid'    => '✓ Pagado',
                        'pending' => 'Pendiente',
                        default   => ucfirst($order->payment_status),
                    } ?>
                </div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 mb-1">Productos</div>
                <div class="font-semibold text-gray-800 dark:text-gray-100"><?= $order->items_count ?></div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 mb-1">Total pagado</div>
                <div class="font-bold text-gray-800 dark:text-gray-100 text-base">$<?= number_format($order->total, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Dirección de envío -->
    <?php if ($order->shipping_data): ?>
    <?php $addr = json_decode($order->shipping_data, true); ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 mb-4">
        <h2 class="font-bold text-gray-700 dark:text-gray-200 mb-3">Dirección de entrega</h2>
        <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
            <p class="font-medium text-gray-800 dark:text-gray-100"><?= esc($addr['first_name'] . ' ' . $addr['last_name']) ?></p>
            <p><?= esc($addr['address_line_1']) ?><?= !empty($addr['address_line_2']) ? ', ' . esc($addr['address_line_2']) : '' ?></p>
            <p><?= esc($addr['city']) ?>, <?= esc($addr['state']) ?>  <?= esc($addr['postal_code'] ?? '') ?></p>
            <p>Tel: <?= esc($addr['phone']) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Resumen de items -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 mb-6">
        <h2 class="font-bold text-gray-700 dark:text-gray-200 mb-3">Productos</h2>
        <div class="space-y-2">
            <?php foreach ($items as $item): ?>
            <div class="flex justify-between items-center text-sm py-1 border-b dark:border-gray-700 last:border-0">
                <div>
                    <span class="text-gray-800 dark:text-gray-100 font-medium"><?= esc($item->name) ?></span>
                    <span class="text-gray-400 dark:text-gray-500 ml-1">×<?= $item->quantity ?></span>
                </div>
                <span class="font-semibold text-gray-700 dark:text-gray-200">$<?= number_format($item->total_price, 0, ',', '.') ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-3 pt-3 border-t dark:border-gray-700 space-y-1 text-sm">
            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                <span>Subtotal</span>
                <span>$<?= number_format($order->subtotal, 0, ',', '.') ?></span>
            </div>
            <?php if ($order->discount > 0): ?>
            <div class="flex justify-between text-green-600 dark:text-green-400">
                <span>Descuento</span>
                <span>−$<?= number_format($order->discount, 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($order->shipping_cost > 0): ?>
            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                <span>Envío</span>
                <span>$<?= number_format($order->shipping_cost, 0, ',', '.') ?></span>
            </div>
            <?php elseif ($order->shipping_cost == 0): ?>
            <div class="flex justify-between text-green-600 dark:text-green-400">
                <span>Envío</span>
                <span>GRATIS</span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between font-bold text-gray-800 dark:text-gray-100 text-base pt-1 border-t dark:border-gray-700 mt-1">
                <span>Total</span>
                <span>$<?= number_format($order->total, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Información si es contraentrega -->
    <?php if ($order->payment_status === 'pending'): ?>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4 mb-6 text-sm text-yellow-800 dark:text-yellow-300">
        <div class="flex gap-2">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <strong>Pago contra entrega</strong><br>
                Tendrás que pagar $<?= number_format($order->total, 0, ',', '.') ?> en efectivo al recibir tu pedido.
                Ten el monto exacto listo.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Botones -->
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="/account/orders/<?= esc($order->order_number) ?>"
           class="flex-1 bg-indigo-600 text-white text-center py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
            Ver detalles del pedido
        </a>
        <a href="/products"
           class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-center py-3 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            Seguir comprando
        </a>
    </div>

</div>

<?= $this->endSection() ?>
