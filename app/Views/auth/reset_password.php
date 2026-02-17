<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-sm border p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Restablecer contraseña</h1>

        <form action="/reset-password" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                <input type="password" name="password" id="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required minlength="8">
                <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
            </div>

            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirm" id="password_confirm"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required minlength="8">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium transition">
                Restablecer contraseña
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="/login" class="text-indigo-600 hover:text-indigo-800 font-medium">Volver al login</a>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
