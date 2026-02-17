<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-sm border p-8">
        <h1 class="text-2xl font-bold text-center mb-2">Recuperar contraseña</h1>
        <p class="text-center text-sm text-gray-600 mb-6">
            Ingresa tu email y te enviaremos instrucciones para restablecer tu contraseña.
        </p>

        <form action="/forgot-password" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email') ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required autofocus>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium transition">
                Enviar instrucciones
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="/login" class="text-indigo-600 hover:text-indigo-800 font-medium">Volver al login</a>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
