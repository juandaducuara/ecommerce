<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="text-2xl font-bold text-gray-800 mb-6">Tu Carrito</h1>

<?php if (empty($items)): ?>
    <!-- Carrito vacío -->
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="text-gray-500 text-lg mb-6">Tu carrito está vacío</p>
        <a href="/products" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            Ver productos
        </a>
    </div>

<?php else: ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Lista de items -->
    <div class="lg:col-span-2 space-y-4" id="cart-items">
        <?php foreach ($items as $item):
            $productData = json_decode($item->product_data ?? '{}', true);
            $image = $productData['image'] ?? null;
        ?>
        <div class="bg-white rounded-xl shadow-sm border p-4 flex gap-4 items-start" id="item-<?= $item->id ?>">
            <!-- Imagen -->
            <div class="w-20 h-20 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                <?php if ($image && !empty($image['path'])): ?>
                    <img src="/<?= esc($image['path']) ?>" alt="<?= esc($item->name) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Datos -->
            <div class="flex-1">
                <a href="/products/<?= esc($productData['slug'] ?? $item->slug) ?>"
                   class="font-medium text-gray-800 hover:text-indigo-600"><?= esc($item->name) ?></a>
                <p class="text-sm text-gray-500">SKU: <?= esc($productData['sku'] ?? '') ?></p>

                <div class="flex items-center justify-between mt-3">
                    <!-- Cantidad -->
                    <div class="flex items-center border rounded-lg" data-item-id="<?= $item->id ?>">
                        <button type="button" class="qty-btn px-3 py-1.5 text-gray-600 hover:bg-gray-100 rounded-l-lg"
                                data-action="decrease" data-item="<?= $item->id ?>">−</button>
                        <span class="w-10 text-center py-1.5 font-medium qty-display"><?= $item->quantity ?></span>
                        <button type="button" class="qty-btn px-3 py-1.5 text-gray-600 hover:bg-gray-100 rounded-r-lg"
                                data-action="increase" data-item="<?= $item->id ?>">+</button>
                    </div>

                    <!-- Precio -->
                    <div class="text-right">
                        <div class="font-bold text-gray-800 item-total" data-item="<?= $item->id ?>">
                            $<?= number_format($item->total_price, 0, ',', '.') ?>
                        </div>
                        <div class="text-xs text-gray-400">$<?= number_format($item->unit_price, 0, ',', '.') ?> c/u</div>
                    </div>

                    <!-- Eliminar -->
                    <button type="button" class="ml-4 text-red-400 hover:text-red-600 remove-btn"
                            data-item="<?= $item->id ?>" title="Eliminar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <a href="/products" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mt-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Seguir comprando
        </a>
    </div>

    <!-- Resumen -->
    <div class="space-y-4">

        <!-- Cupón -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <h3 class="font-semibold text-gray-700 mb-3">Código de cupón</h3>
            <div id="coupon-msg" class="hidden text-sm mb-2 rounded-lg p-2"></div>
            <div class="flex gap-2">
                <input type="text" id="coupon-code" placeholder="INGRESA TU CÓDIGO"
                       class="flex-1 border rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-indigo-300"
                       value="<?= $cart->coupon_id ? '' : '' ?>">
                <button type="button" id="apply-coupon"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
                    Aplicar
                </button>
            </div>
            <?php if ($cart->coupon_id): ?>
                <div class="mt-2 flex items-center justify-between bg-green-50 px-3 py-1.5 rounded-lg">
                    <span class="text-green-700 text-sm font-medium">Cupón aplicado</span>
                    <button type="button" id="remove-coupon" class="text-red-500 text-xs hover:text-red-700">Quitar</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Totales -->
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <h3 class="font-semibold text-gray-700 mb-3">Resumen del pedido</h3>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal (<?= $cart->items_count ?> productos)</span>
                    <span id="cart-subtotal">$<?= number_format($cart->subtotal ?? 0, 0, ',', '.') ?></span>
                </div>

                <?php if ($cart->discount > 0): ?>
                <div class="flex justify-between text-green-600" id="discount-row">
                    <span>Descuento cupón</span>
                    <span id="cart-discount">−$<?= number_format($cart->discount, 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>

                <div class="flex justify-between text-gray-500 text-xs">
                    <span>Envío</span>
                    <span class="text-indigo-600">Se calcula en el checkout</span>
                </div>

                <div class="border-t pt-2 flex justify-between font-bold text-gray-800 text-base">
                    <span>Total</span>
                    <span id="cart-total">$<?= number_format(($cart->total ?? 0) - ($cart->discount ?? 0), 0, ',', '.') ?></span>
                </div>
            </div>

            <a href="/checkout"
               class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg font-medium hover:bg-indigo-700 transition mt-4">
                Proceder al pago
            </a>

            <div class="mt-3 text-center text-xs text-gray-400">
                Envío gratis en compras mayores a $150.000
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="bg-gray-800 text-white px-4 py-3 rounded-lg shadow-lg text-sm max-w-xs" id="toast-msg"></div>
</div>

<script>
const csrfName  = '<?= csrf_token() ?>';
const csrfHash  = '<?= csrf_hash() ?>';

function showToast(msg, ok = true) {
    const t = document.getElementById('toast');
    const m = document.getElementById('toast-msg');
    t.className = 'fixed bottom-6 right-6 z-50';
    m.className = `px-4 py-3 rounded-lg shadow-lg text-sm max-w-xs text-white ${ok ? 'bg-gray-800' : 'bg-red-600'}`;
    m.textContent = msg;
    setTimeout(() => t.classList.add('hidden'), 3000);
}

function cartPost(url, body) {
    body[csrfName] = csrfHash;
    return fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: new URLSearchParams(body),
    }).then(r => r.json());
}

function updateCartBadge(count) {
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count;
        el.classList.toggle('hidden', count <= 0);
    });
}

// Quantity buttons
document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const itemId  = btn.dataset.item;
        const display = btn.closest('[data-item-id]').querySelector('.qty-display');
        let qty = parseInt(display.textContent);
        qty = btn.dataset.action === 'increase' ? qty + 1 : Math.max(0, qty - 1);

        cartPost('/cart/update', {item_id: itemId, quantity: qty})
            .then(data => {
                if (!data.success) { showToast(data.message, false); return; }
                if (qty <= 0) {
                    document.getElementById('item-' + itemId)?.remove();
                } else {
                    display.textContent = qty;
                    // update item total from server response
                }
                updateCartBadge(data.items_count);
                if (data.items_count <= 0) location.reload();
                else updateTotals(data);
            });
    });
});

// Remove buttons
document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const itemId = btn.dataset.item;
        cartPost('/cart/remove', {item_id: itemId})
            .then(data => {
                document.getElementById('item-' + itemId)?.remove();
                updateCartBadge(data.items_count);
                if (data.items_count <= 0) location.reload();
            });
    });
});

function updateTotals(data) {
    if (!data.subtotal) return;
    const fmt = n => '$' + Math.round(n).toLocaleString('es-CO');
    document.getElementById('cart-subtotal').textContent = fmt(data.subtotal);
    document.getElementById('cart-total').textContent    = fmt(data.total - (data.discount || 0));
}

// Coupon
document.getElementById('apply-coupon')?.addEventListener('click', () => {
    const code = document.getElementById('coupon-code').value.trim();
    if (!code) return;

    cartPost('/cart/coupon', {code})
        .then(data => {
            const msg = document.getElementById('coupon-msg');
            msg.classList.remove('hidden');
            if (data.success) {
                msg.className = 'text-sm mb-2 rounded-lg p-2 bg-green-50 text-green-700';
                msg.textContent = data.message;
                setTimeout(() => location.reload(), 1500);
            } else {
                msg.className = 'text-sm mb-2 rounded-lg p-2 bg-red-50 text-red-700';
                msg.textContent = data.message;
            }
        });
});

document.getElementById('remove-coupon')?.addEventListener('click', () => {
    cartPost('/cart/coupon/remove', {})
        .then(() => location.reload());
});
</script>

<?= $this->endSection() ?>
