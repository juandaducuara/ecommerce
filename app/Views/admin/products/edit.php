<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="flex items-center gap-4 mb-6">
    <a href="/admin/products" class="text-gray-500 hover:text-gray-700">&larr;</a>
    <h1 class="text-2xl font-bold text-gray-800">Editar Producto</h1>
</div>

<form action="/admin/products/<?= $product->id ?>" method="POST">
    <?= csrf_field() ?>

    <?= $this->include('admin/products/_form') ?>

    <div class="flex justify-end gap-3 mt-6">
        <a href="/admin/products" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
            Guardar Cambios
        </button>
    </div>
</form>

<!-- Gestión de imágenes (AJAX, separado del formulario principal) -->
<div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Imágenes</h2>

    <div id="image-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <?php foreach ($images as $img): ?>
            <div class="relative group border rounded-lg overflow-hidden" id="image-<?= $img->id ?>">
                <img src="/<?= esc($img->path) ?>" alt="<?= esc($img->alt_text) ?>"
                    class="w-full h-32 object-cover">
                <?php if ($img->is_primary): ?>
                    <span class="absolute top-1 left-1 bg-indigo-600 text-white text-xs px-1.5 py-0.5 rounded">Principal</span>
                <?php endif; ?>
                <button type="button"
                    class="delete-img-btn absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition bg-red-600 text-white text-xs px-1.5 py-0.5 rounded hover:bg-red-700"
                    data-image-id="<?= $img->id ?>">X</button>
            </div>
        <?php endforeach; ?>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Subir imágenes</label>
        <input type="file" id="image-upload-input" multiple accept="image/*"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="text-xs text-gray-500 mt-1">PNG, JPG o WebP. La primera imagen será la principal.</p>
        <div id="upload-status" class="mt-2 text-sm hidden"></div>
    </div>
</div>

<script>
(function () {
    const productId = <?= (int) $product->id ?>;
    let csrfName  = '<?= csrf_token() ?>';
    let csrfValue = '<?= csrf_hash() ?>';

    function updateCsrf(data) {
        if (data && data.csrf) {
            csrfName  = data.csrf.name;
            csrfValue = data.csrf.value;
        }
    }

    // Subir imágenes
    document.getElementById('image-upload-input').addEventListener('change', function () {
        const files = this.files;
        if (!files.length) return;

        const formData = new FormData();
        for (const file of files) formData.append('images[]', file);
        formData.append(csrfName, csrfValue);

        const status = document.getElementById('upload-status');
        status.textContent = 'Subiendo...';
        status.className = 'mt-2 text-sm text-gray-500';
        status.classList.remove('hidden');

        fetch('/admin/products/' + productId + '/images/upload', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            updateCsrf(data);
            if (data.success) {
                data.images.forEach(img => addImageCard(img));
                status.textContent = 'Imágenes subidas correctamente.';
                status.className = 'mt-2 text-sm text-green-600';
                document.getElementById('image-upload-input').value = '';
            } else {
                status.textContent = data.error || 'Error al subir imágenes.';
                status.className = 'mt-2 text-sm text-red-600';
            }
        })
        .catch(() => {
            status.textContent = 'Error de conexión.';
            status.className = 'mt-2 text-sm text-red-600';
        });
    });

    // Eliminar imagen (delegado en el grid)
    document.getElementById('image-grid').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-img-btn');
        if (!btn) return;
        if (!confirm('¿Eliminar esta imagen?')) return;

        const imageId = btn.dataset.imageId;
        const body = new URLSearchParams({ [csrfName]: csrfValue });

        fetch('/admin/products/' + productId + '/images/' + imageId + '/delete', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: body.toString(),
        })
        .then(r => r.json())
        .then(data => {
            updateCsrf(data);
            if (data.success) {
                document.getElementById('image-' + imageId)?.remove();
            } else {
                alert(data.error || 'Error al eliminar la imagen.');
            }
        })
        .catch(() => alert('Error de conexión.'));
    });

    function addImageCard(img) {
        const div = document.createElement('div');
        div.className = 'relative group border rounded-lg overflow-hidden';
        div.id = 'image-' + img.id;
        div.innerHTML =
            '<img src="' + img.path + '" class="w-full h-32 object-cover">' +
            (img.is_primary ? '<span class="absolute top-1 left-1 bg-indigo-600 text-white text-xs px-1.5 py-0.5 rounded">Principal</span>' : '') +
            '<button type="button" class="delete-img-btn absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition bg-red-600 text-white text-xs px-1.5 py-0.5 rounded hover:bg-red-700" data-image-id="' + img.id + '">X</button>';
        document.getElementById('image-grid').appendChild(div);
    }
})();
</script>

<?= $this->endSection() ?>
