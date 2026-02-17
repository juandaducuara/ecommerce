<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-sm border p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Crear cuenta</h1>

        <form action="/register" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="first_name" id="first_name" value="<?= old('first_name') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required autofocus>
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                    <input type="text" name="last_name" id="last_name" value="<?= old('last_name') ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required>
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email') ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
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
                Crear cuenta
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿Ya tienes cuenta?
            <a href="/login" class="text-indigo-600 hover:text-indigo-800 font-medium">Inicia sesión</a>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
