<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Mi Cuenta</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Información personal</h2>

        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre</dt>
                <dd class="text-sm text-gray-800 dark:text-gray-100 font-medium"><?= esc($user->first_name . ' ' . $user->last_name) ?></dd>
            </div>
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="text-sm text-gray-800 dark:text-gray-100 font-medium"><?= esc($user->email) ?></dd>
            </div>
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Teléfono</dt>
                <dd class="text-sm text-gray-800 dark:text-gray-100 font-medium"><?= esc($user->phone ?? 'No registrado') ?></dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Miembro desde</dt>
                <dd class="text-sm text-gray-800 dark:text-gray-100 font-medium"><?= esc($user->created_at) ?></dd>
            </div>
        </dl>
    </div>
</div>

<?= $this->endSection() ?>
