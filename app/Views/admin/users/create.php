<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="/admin/users" class="text-gray-500 hover:text-gray-700">&larr;</a>
        <h1 class="text-2xl font-bold text-gray-800">Crear Usuario</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form action="/admin/users" method="POST">
            <?= csrf_field() ?>

            <?= $this->include('admin/users/_form') ?>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="/admin/users" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
