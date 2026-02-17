<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Columna principal -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Info básica -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información básica</h2>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" id="name"
                        value="<?= old('name', $product->name ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>

                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                    <input type="text" name="sku" id="sku"
                        value="<?= old('sku', $product->sku ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>

                <div>
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Descripción corta</label>
                    <input type="text" name="short_description" id="short_description"
                        value="<?= old('short_description', $product->short_description ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        maxlength="255">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción completa</label>
                    <textarea name="description" id="description" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= old('description', $product->description ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Precios -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Precios</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Precio (COP) *</label>
                    <input type="number" name="price" id="price" step="1" min="0"
                        value="<?= old('price', $product->price ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
                <div>
                    <label for="compare_price" class="block text-sm font-medium text-gray-700 mb-1">Precio comparación</label>
                    <input type="number" name="compare_price" id="compare_price" step="1" min="0"
                        value="<?= old('compare_price', $product->compare_price ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Para mostrar descuento</p>
                </div>
                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
                    <input type="number" name="cost" id="cost" step="1" min="0"
                        value="<?= old('cost', $product->cost ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">No visible al cliente</p>
                </div>
            </div>
        </div>

        <!-- Inventario -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventario</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                    <input type="number" name="quantity" id="quantity" min="0"
                        value="<?= old('quantity', $stock->quantity ?? 0) ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Alerta stock bajo</label>
                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0"
                        value="<?= old('low_stock_threshold', $stock->low_stock_threshold ?? 5) ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="allow_backorder" value="1"
                            class="rounded border-gray-300"
                            <?= old('allow_backorder', $stock->allow_backorder ?? 0) ? 'checked' : '' ?>>
                        Permitir pedidos sin stock
                    </label>
                </div>
            </div>
        </div>

        <!-- Imágenes -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Imágenes</h2>

            <?php if (!empty($images)): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <?php foreach ($images as $img): ?>
                        <div class="relative group border rounded-lg overflow-hidden">
                            <img src="/<?= esc($img->path) ?>" alt="<?= esc($img->alt_text) ?>"
                                class="w-full h-32 object-cover">
                            <?php if ($img->is_primary): ?>
                                <span class="absolute top-1 left-1 bg-indigo-600 text-white text-xs px-1.5 py-0.5 rounded">Principal</span>
                            <?php endif; ?>
                            <form action="/admin/products/<?= $product->id ?>/images/<?= $img->id ?>/delete" method="POST"
                                class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition"
                                onsubmit="return confirm('¿Eliminar esta imagen?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="bg-red-600 text-white text-xs px-1.5 py-0.5 rounded hover:bg-red-700">X</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subir imágenes</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-xs text-gray-500 mt-1">PNG, JPG o WebP. La primera imagen será la principal.</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Organización -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Organización</h2>
            <div class="space-y-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select name="category_id" id="category_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"
                                <?= old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' ?>>
                                <?= esc($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                    <input type="number" name="weight" id="weight" step="0.01" min="0"
                        value="<?= old('weight', $product->weight ?? '') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Estado -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Estado</h2>
            <div class="space-y-3">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1"
                        class="rounded border-gray-300"
                        <?= old('is_active', $product->is_active ?? 1) ? 'checked' : '' ?>>
                    Producto activo
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_featured" value="1"
                        class="rounded border-gray-300"
                        <?= old('is_featured', $product->is_featured ?? 0) ? 'checked' : '' ?>>
                    Producto destacado
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="requires_shipping" value="1"
                        class="rounded border-gray-300"
                        <?= old('requires_shipping', $product->requires_shipping ?? 1) ? 'checked' : '' ?>>
                    Requiere envío
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_taxable" value="1"
                        class="rounded border-gray-300"
                        <?= old('is_taxable', $product->is_taxable ?? 1) ? 'checked' : '' ?>>
                    Aplica impuesto (IVA)
                </label>
            </div>
        </div>
    </div>
</div>
