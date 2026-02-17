<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav class="mb-6 text-sm">
    <ol class="flex items-center gap-2 text-gray-500">
        <li><a href="/" class="hover:text-indigo-600">Inicio</a></li>
        <li>/</li>
        <li><a href="/products" class="hover:text-indigo-600">Productos</a></li>
        <?php foreach ($breadcrumb as $cat): ?>
            <li>/</li>
            <li><a href="/products?category=<?= $cat->id ?>" class="hover:text-indigo-600"><?= esc($cat->name) ?></a></li>
        <?php endforeach; ?>
        <li>/</li>
        <li class="text-gray-800 font-medium"><?= esc($product->name) ?></li>
    </ol>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <!-- Galería de imágenes -->
    <div>
        <?php if (!empty($images)): ?>
            <!-- Imagen principal -->
            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-4" id="main-image-container">
                <img src="/<?= esc($images[0]->path) ?>" alt="<?= esc($product->name) ?>"
                    id="main-image" class="w-full h-full object-cover">
            </div>

            <!-- Thumbnails -->
            <?php if (count($images) > 1): ?>
                <div class="grid grid-cols-5 gap-2">
                    <?php foreach ($images as $i => $img): ?>
                        <button onclick="document.getElementById('main-image').src='/<?= esc($img->path) ?>'"
                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 hover:border-indigo-500 transition <?= $i === 0 ? 'border-indigo-500' : 'border-transparent' ?>">
                            <img src="/<?= esc($img->path) ?>" alt="" class="w-full h-full object-cover">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="aspect-square bg-gray-100 rounded-xl flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info del producto -->
    <div>
        <p class="text-sm text-indigo-600 font-medium mb-1"><?= esc($product->category_name) ?></p>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2"><?= esc($product->name) ?></h1>
        <p class="text-sm text-gray-400 mb-4">SKU: <?= esc($product->sku) ?></p>

        <!-- Precio -->
        <div class="mb-6">
            <div class="flex items-baseline gap-3">
                <span class="text-3xl font-bold text-gray-800">$<?= number_format($product->price, 0, ',', '.') ?></span>
                <?php if ($product->compare_price && $product->compare_price > $product->price): ?>
                    <span class="text-lg text-gray-400 line-through">$<?= number_format($product->compare_price, 0, ',', '.') ?></span>
                    <?php $discount = round(100 - ($product->price / $product->compare_price * 100)); ?>
                    <span class="bg-red-100 text-red-700 text-sm font-bold px-2 py-0.5 rounded">-<?= $discount ?>%</span>
                <?php endif; ?>
            </div>
            <?php if ($product->is_taxable): ?>
                <p class="text-xs text-gray-500 mt-1">IVA incluido</p>
            <?php endif; ?>
        </div>

        <!-- Stock -->
        <div class="mb-6">
            <?php if ($product->stock_available > 0): ?>
                <span class="text-green-600 font-medium text-sm">En stock (<?= $product->stock_available ?> disponibles)</span>
            <?php else: ?>
                <span class="text-red-600 font-medium text-sm">Agotado</span>
            <?php endif; ?>
        </div>

        <!-- Descripción corta -->
        <?php if ($product->short_description): ?>
            <p class="text-gray-600 mb-6"><?= esc($product->short_description) ?></p>
        <?php endif; ?>

        <!-- Agregar al carrito -->
        <div class="flex items-center gap-4 mb-6">
            <div class="flex items-center border rounded-lg">
                <button type="button" id="qty-down" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-l-lg">−</button>
                <input type="number" value="1" min="1" max="<?= $product->stock_available > 0 ? $product->stock_available : 1 ?>"
                    class="w-16 text-center border-x py-2 focus:outline-none" id="quantity">
                <button type="button" id="qty-up" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-r-lg">+</button>
            </div>
            <button id="add-to-cart"
                data-product="<?= $product->id ?>"
                class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-indigo-700 transition
                    <?= $product->stock_available <= 0 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                <?= $product->stock_available <= 0 ? 'disabled' : '' ?>>
                <?= $product->stock_available > 0 ? 'Agregar al carrito' : 'Agotado' ?>
            </button>
        </div>

        <!-- Toast feedback -->
        <div id="cart-toast" class="hidden text-sm font-medium py-2 px-3 rounded-lg mb-2"></div>

        <script>
        const csrfToken = '<?= csrf_token() ?>';
        const csrfHash  = '<?= csrf_hash() ?>';

        document.getElementById('qty-down')?.addEventListener('click', () => {
            const q = document.getElementById('quantity');
            if (parseInt(q.value) > 1) q.value = parseInt(q.value) - 1;
        });
        document.getElementById('qty-up')?.addEventListener('click', () => {
            const q    = document.getElementById('quantity');
            const max  = parseInt(q.max) || 9999;
            if (parseInt(q.value) < max) q.value = parseInt(q.value) + 1;
        });

        document.getElementById('add-to-cart')?.addEventListener('click', function() {
            const btn = this;
            const qty = document.getElementById('quantity').value;
            btn.disabled = true;
            btn.textContent = 'Agregando…';

            const body = new URLSearchParams({
                product_id: btn.dataset.product,
                quantity:   qty,
                [csrfToken]: csrfHash,
            });

            fetch('/cart/add', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body,
            })
            .then(r => r.json())
            .then(data => {
                const toast = document.getElementById('cart-toast');
                toast.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');

                if (data.success) {
                    toast.classList.add('bg-green-100', 'text-green-800');
                    toast.textContent = data.message;
                    // Update cart badge in navbar
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.items_count;
                        el.classList.remove('hidden');
                    });
                } else {
                    toast.classList.add('bg-red-100', 'text-red-800');
                    toast.textContent = data.message;
                }

                btn.textContent = '<?= $product->stock_available > 0 ? 'Agregar al carrito' : 'Agotado' ?>';
                btn.disabled = false;
            })
            .catch(() => {
                btn.textContent = 'Agregar al carrito';
                btn.disabled = false;
            });
        });
        </script>

        <!-- Detalles -->
        <div class="border-t pt-4 space-y-2 text-sm text-gray-600">
            <?php if ($product->weight): ?>
                <p>Peso: <?= number_format($product->weight, 2) ?> kg</p>
            <?php endif; ?>
            <?php if ($product->requires_shipping): ?>
                <p>Envío disponible a todo Colombia</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Descripción completa -->
<?php if ($product->description): ?>
<div class="bg-white rounded-xl shadow-sm border p-6 mb-10">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Descripción</h2>
    <div class="prose text-gray-600 max-w-none">
        <?= nl2br(esc($product->description)) ?>
    </div>
</div>
<?php endif; ?>

<!-- Productos relacionados -->
<?php if (!empty($related)): ?>
<section>
    <h2 class="text-xl font-bold text-gray-800 mb-4">Productos relacionados</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($related as $product): ?>
            <?= view('components/product_card', ['product' => $product]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
