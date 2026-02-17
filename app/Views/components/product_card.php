<a href="/products/<?= esc($product->slug) ?>" class="group">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden hover:shadow-md transition">
        <!-- Imagen -->
        <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
            <?php if ($product->primary_image): ?>
                <img src="/<?= esc($product->primary_image) ?>" alt="<?= esc($product->name) ?>"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            <?php endif; ?>

            <?php if (isset($product->compare_price) && $product->compare_price > $product->price): ?>
                <?php $discount = round(100 - ($product->price / $product->compare_price * 100)); ?>
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                    -<?= $discount ?>%
                </span>
            <?php endif; ?>

            <?php if (isset($product->stock_available) && $product->stock_available <= 0): ?>
                <span class="absolute top-2 right-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">Agotado</span>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="p-4">
            <?php if (isset($product->category_name)): ?>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mb-1"><?= esc($product->category_name) ?></p>
            <?php endif; ?>

            <h3 class="font-medium text-gray-800 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition line-clamp-2">
                <?= esc($product->name) ?>
            </h3>

            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-lg font-bold text-gray-800 dark:text-gray-100">$<?= number_format($product->price, 0, ',', '.') ?></span>
                <?php if (isset($product->compare_price) && $product->compare_price > $product->price): ?>
                    <span class="text-sm text-gray-400 dark:text-gray-500 line-through">$<?= number_format($product->compare_price, 0, ',', '.') ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>
