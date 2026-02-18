<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="flex items-center gap-4 mb-6">
    <a href="/admin/products" class="text-gray-500 hover:text-gray-700">&larr;</a>
    <h1 class="text-2xl font-bold text-gray-800">Crear Producto</h1>
</div>

<form action="/admin/products" method="POST">
    <?= csrf_field() ?>

    <?php
        $stock = (object) ['quantity' => 0, 'low_stock_threshold' => 5, 'allow_backorder' => 0];
        $images = [];
    ?>

    <?= $this->include('admin/products/_form') ?>

    <div class="flex justify-end gap-3 mt-6">
        <a href="/admin/products" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
            Crear Producto
        </button>
    </div>
</form>

<?= $this->endSection() ?>
