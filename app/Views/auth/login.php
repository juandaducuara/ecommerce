<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-sm border p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Iniciar sesión</h1>

        <form action="/login" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email') ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required autofocus>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    Recordarme
                </label>
                <a href="/forgot-password" class="text-sm text-indigo-600 hover:text-indigo-800">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium transition">
                Iniciar sesión
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿No tienes cuenta?
            <a href="/register" class="text-indigo-600 hover:text-indigo-800 font-medium">Regístrate</a>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
