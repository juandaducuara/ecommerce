<a href="/products/<?= esc($product->slug) ?>" class="group">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden
                hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

        <!-- Imagen -->
        <div class="aspect-square bg-gray-50 dark:bg-gray-700 relative overflow-hidden">
            <?php if ($product->primary_image): ?>
                <img src="/<?= esc($product->primary_image) ?>" alt="<?= esc($product->name) ?>"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-200 dark:text-gray-600">
                    <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            <?php endif; ?>

            <!-- Badge descuento -->
            <?php if (isset($product->compare_price) && $product->compare_price > $product->price): ?>
                <?php $discount = round(100 - ($product->price / $product->compare_price * 100)); ?>
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-lg shadow-sm">
                    -<?= $discount ?>%
                </span>
            <?php endif; ?>

            <!-- Badge agotado -->
            <?php if (isset($product->stock_available) && $product->stock_available <= 0): ?>
                <div class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 flex items-center justify-center">
                    <span class="bg-gray-800 dark:bg-gray-700 text-white text-xs font-semibold px-3 py-1 rounded-lg">Agotado</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="p-3.5">
            <?php if (isset($product->category_name)): ?>
                <p class="text-xs brand-text font-semibold mb-1 uppercase tracking-wide"><?= esc($product->category_name) ?></p>
            <?php endif; ?>

            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 group-hover:brand-text transition line-clamp-2 leading-snug mb-2">
                <?= esc($product->name) ?>
            </h3>

            <div class="flex items-baseline justify-between gap-2">
                <span class="text-base font-bold text-gray-900 dark:text-white">
                    $<?= number_format($product->price, 0, ',', '.') ?>
                </span>
                <?php if (isset($product->compare_price) && $product->compare_price > $product->price): ?>
                    <span class="text-xs text-gray-400 dark:text-gray-500 line-through">
                        $<?= number_format($product->compare_price, 0, ',', '.') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>
