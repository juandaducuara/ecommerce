<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Panel de Administración</h1>
    <p class="text-gray-600 mt-1">Bienvenido, <?= esc(session()->get('user_name')) ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <p class="text-sm text-gray-500">Productos</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">-</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <p class="text-sm text-gray-500">Pedidos</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">-</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <p class="text-sm text-gray-500">Clientes</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">-</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <p class="text-sm text-gray-500">Ingresos</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">-</p>
    </div>
</div>

<div class="mt-8 bg-white rounded-xl shadow-sm border p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Acciones rápidas</h2>
    <div class="flex flex-wrap gap-3">
        <a href="/admin/users" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm hover:bg-indigo-100">Gestión de usuarios</a>
        <a href="/admin/products" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm hover:bg-indigo-100">Gestión de productos</a>
        <a href="/admin/orders" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm hover:bg-indigo-100">Gestión de pedidos</a>
    </div>
</div>

<?= $this->endSection() ?>
