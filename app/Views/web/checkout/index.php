<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="text-2xl font-bold text-gray-800 mb-6">Finalizar pedido</h1>

<form method="post" action="/checkout" id="checkout-form">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Formulario izquierda -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Dirección de envío -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Dirección de envío</h2>

                <?php if (!empty($savedAddresses)): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Usar dirección guardada</label>
                    <select id="saved-address" class="border rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">— Nueva dirección —</option>
                        <?php foreach ($savedAddresses as $addr): ?>
                            <option value="<?= $addr->id ?>"
                                data-fn="<?= esc($addr->first_name) ?>"
                                data-ln="<?= esc($addr->last_name) ?>"
                                data-ph="<?= esc($addr->phone) ?>"
                                data-a1="<?= esc($addr->address_line_1) ?>"
                                data-a2="<?= esc($addr->address_line_2) ?>"
                                data-ci="<?= esc($addr->city) ?>"
                                data-st="<?= esc($addr->state) ?>"
                                data-pc="<?= esc($addr->postal_code) ?>"
                                <?= ($defaultAddress && $defaultAddress->id == $addr->id) ? 'selected' : '' ?>>
                                <?= esc($addr->first_name . ' ' . $addr->last_name) ?> — <?= esc($addr->address_line_1) ?>, <?= esc($addr->city) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" required
                               value="<?= old('first_name', $defaultAddress->first_name ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" required
                               value="<?= old('last_name', $defaultAddress->last_name ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono / Celular <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" required
                               value="<?= old('phone', $defaultAddress->phone ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección <span class="text-red-500">*</span></label>
                        <input type="text" name="address_line_1" id="address_line_1" required
                               placeholder="Calle, número, barrio"
                               value="<?= old('address_line_1', $defaultAddress->address_line_1 ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apto / Interior / Referencia</label>
                        <input type="text" name="address_line_2" id="address_line_2"
                               value="<?= old('address_line_2', $defaultAddress->address_line_2 ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad <span class="text-red-500">*</span></label>
                        <input type="text" name="city" id="city" required
                               value="<?= old('city', $defaultAddress->city ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento <span class="text-red-500">*</span></label>
                        <input type="text" name="state" id="state" required
                               value="<?= old('state', $defaultAddress->state ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código postal</label>
                        <input type="text" name="postal_code" id="postal_code"
                               value="<?= old('postal_code', $defaultAddress->postal_code ?? '') ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas del pedido</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Instrucciones especiales de entrega (opcional)"
                                  class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 text-sm"><?= old('notes') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Método de envío -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Método de envío</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="shipping_method" value="standard" class="shipping-radio" checked>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">Envío estándar</div>
                            <div class="text-sm text-gray-500">7 - 15 días hábiles</div>
                        </div>
                        <div class="font-bold text-gray-800 shipping-price" id="price-standard">
                            <?= ($cart->subtotal ?? 0) >= 150000 ? '<span class="text-green-600">GRATIS</span>' : '$8.900' ?>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="shipping_method" value="express" class="shipping-radio">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">Envío express</div>
                            <div class="text-sm text-gray-500">2 - 5 días hábiles</div>
                        </div>
                        <div class="font-bold text-gray-800 shipping-price" id="price-express">
                            <?= ($cart->subtotal ?? 0) >= 150000 ? '<span class="text-green-600">GRATIS</span>' : '$15.900' ?>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="shipping_method" value="pickup" class="shipping-radio">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">Recogida en tienda</div>
                            <div class="text-sm text-gray-500">Disponible en 24 horas</div>
                        </div>
                        <div class="font-bold text-green-600">GRATIS</div>
                    </label>
                </div>
            </div>

            <!-- Método de pago -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Método de pago</h2>
                <div class="space-y-3">

                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="payment_method" value="card" id="pay-card" class="payment-radio" checked>
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <div class="flex-1 font-medium text-gray-800">Tarjeta de crédito / débito</div>
                        <div class="text-xs text-gray-400">Visa, Mastercard, Amex</div>
                    </label>

                    <!-- Datos tarjeta -->
                    <div id="card-fields" class="border border-indigo-200 rounded-lg p-4 bg-indigo-50 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de tarjeta</label>
                            <input type="text" id="card-number-display" placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre en la tarjeta</label>
                            <input type="text" placeholder="Como aparece en la tarjeta"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vencimiento</label>
                                <input type="text" placeholder="MM/AA" maxlength="5"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                <input type="text" placeholder="123" maxlength="4"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">🔒 Modo sandbox — no ingreses datos reales</p>
                    </div>

                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="payment_method" value="pse" id="pay-pse" class="payment-radio">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        <div class="flex-1 font-medium text-gray-800">PSE — Débito bancario</div>
                        <div class="text-xs text-gray-400">Pago en línea</div>
                    </label>

                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="payment_method" value="cod" id="pay-cod" class="payment-radio">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <div class="flex-1 font-medium text-gray-800">Contraentrega</div>
                        <div class="text-xs text-gray-400">Pago al recibir</div>
                    </label>

                </div>
            </div>
        </div>

        <!-- Resumen derecha -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border p-5 sticky top-4">
                <h2 class="font-bold text-gray-800 mb-4">Resumen del pedido</h2>

                <!-- Items -->
                <div class="space-y-2 mb-4">
                    <?php foreach ($items as $item): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 flex-1 pr-2"><?= esc($item->name) ?> <span class="text-gray-400">×<?= $item->quantity ?></span></span>
                        <span class="font-medium text-gray-800 whitespace-nowrap">$<?= number_format($item->total_price, 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t pt-3 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>$<?= number_format($cart->subtotal ?? 0, 0, ',', '.') ?></span>
                    </div>

                    <?php if (($cart->discount ?? 0) > 0): ?>
                    <div class="flex justify-between text-green-600">
                        <span>Descuento cupón</span>
                        <span>−$<?= number_format($cart->discount, 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="flex justify-between text-gray-600">
                        <span>Envío</span>
                        <span id="summary-shipping">
                            <?= ($cart->subtotal ?? 0) >= 150000 ? '<span class="text-green-600">GRATIS</span>' : '$8.900' ?>
                        </span>
                    </div>

                    <div class="border-t pt-2 flex justify-between font-bold text-gray-800 text-base">
                        <span>Total a pagar</span>
                        <span id="summary-total">
                            <?php
                            $subtotal = $cart->subtotal ?? 0;
                            $discount = $cart->discount ?? 0;
                            $shipping = $subtotal >= 150000 ? 0 : 8900;
                            echo '$' . number_format($subtotal - $discount + $shipping, 0, ',', '.');
                            ?>
                        </span>
                    </div>

                    <div class="text-xs text-gray-400 text-center">IVA incluido en el precio</div>
                </div>

                <button type="submit" id="submit-btn"
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition mt-4 text-lg">
                    Confirmar pedido
                </button>

                <div class="mt-3 flex items-center justify-center gap-1 text-xs text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Compra segura y protegida
                </div>
            </div>
        </div>

    </div>
</form>

<script>
const subtotal = <?= (float)($cart->subtotal ?? 0) ?>;
const discount = <?= (float)($cart->discount ?? 0) ?>;
const freeShipping = subtotal >= 150000;

const SHIPPING = {standard: 8900, express: 15900, pickup: 0};

function updateSummary() {
    const method = document.querySelector('.shipping-radio:checked')?.value || 'standard';
    const cost   = freeShipping ? 0 : SHIPPING[method];
    const total  = subtotal - discount + cost;

    const fmt = n => '$' + n.toLocaleString('es-CO');

    document.getElementById('summary-shipping').innerHTML =
        cost === 0 ? '<span class="text-green-600">GRATIS</span>' : fmt(cost);
    document.getElementById('summary-total').textContent = fmt(total);
}

document.querySelectorAll('.shipping-radio').forEach(r => r.addEventListener('change', updateSummary));

// Show/hide card fields
document.querySelectorAll('.payment-radio').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('card-fields').style.display =
            document.getElementById('pay-card').checked ? 'block' : 'none';
    });
});

// Format card number
document.getElementById('card-number-display')?.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').substring(0, 16);
    this.value = v.match(/.{1,4}/g)?.join(' ') || v;
});

// Fill address from saved
document.getElementById('saved-address')?.addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    if (!opt.value) return;
    document.getElementById('first_name').value    = opt.dataset.fn || '';
    document.getElementById('last_name').value     = opt.dataset.ln || '';
    document.getElementById('phone').value         = opt.dataset.ph || '';
    document.getElementById('address_line_1').value = opt.dataset.a1 || '';
    document.getElementById('address_line_2').value = opt.dataset.a2 || '';
    document.getElementById('city').value          = opt.dataset.ci || '';
    document.getElementById('state').value         = opt.dataset.st || '';
    document.getElementById('postal_code').value   = opt.dataset.pc || '';
});

// Auto-fill if default address exists
window.addEventListener('load', () => {
    const sel = document.getElementById('saved-address');
    if (sel?.value) sel.dispatchEvent(new Event('change'));
});

// Prevent double submit
document.getElementById('checkout-form')?.addEventListener('submit', () => {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Procesando…';
});
</script>

<?= $this->endSection() ?>
