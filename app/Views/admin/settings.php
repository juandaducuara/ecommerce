<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$s  = $general ?? [];
$sc = $social  ?? [];

$storeName    = $s['store_name']          ?? 'Mi Tienda Online';
$storeLogo    = $s['store_logo']          ?? '';
$brandColor   = $s['brand_primary_color'] ?? '#4f46e5';
$tagline      = $s['tagline']             ?? '';
$heroTitle    = $s['hero_title']          ?? '';
$heroSubtitle = $s['hero_subtitle']       ?? '';
?>

<!-- Standalone forms for logo (outside the main form to avoid nesting) -->
<form id="logo-upload-form" action="/admin/settings/logo" method="POST" enctype="multipart/form-data" class="hidden">
    <?= csrf_field() ?>
</form>
<form id="logo-delete-form" action="/admin/settings/logo/delete" method="POST" class="hidden">
    <?= csrf_field() ?>
</form>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <nav class="-mb-px flex gap-6 text-sm font-medium overflow-x-auto" id="settings-tabs">
        <button onclick="showTab('brand')"   id="tab-brand"   class="tab-btn whitespace-nowrap pb-3 border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400">Marca</button>
        <button onclick="showTab('hero')"    id="tab-hero"    class="tab-btn whitespace-nowrap pb-3 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700">Landing</button>
        <button onclick="showTab('contact')" id="tab-contact" class="tab-btn whitespace-nowrap pb-3 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700">Contacto</button>
        <button onclick="showTab('social')"  id="tab-social"  class="tab-btn whitespace-nowrap pb-3 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700">Redes sociales</button>
    </nav>
</div>

<!-- Main form: all text fields across all tabs -->
<form action="/admin/settings" method="POST">
    <?= csrf_field() ?>

    <!-- ===== TAB: MARCA ===== -->
    <div id="section-brand" class="tab-section space-y-6">

        <!-- Identidad -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Identidad de la tienda</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la tienda *</label>
                    <input type="text" name="store_name" value="<?= esc($storeName) ?>" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Aparece en el navbar, footer y título del sitio.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Eslogan / Tagline</label>
                    <input type="text" name="tagline" value="<?= esc($tagline) ?>" maxlength="120"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Frase corta que describe la tienda.</p>
                </div>
            </div>
        </div>

        <!-- Logo (file input/button reference logo-upload-form via form= attribute) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Logo</h2>
            <div class="flex flex-col sm:flex-row gap-6">
                <!-- Preview actual -->
                <div class="flex-shrink-0">
                    <div id="logo-preview-wrap" class="w-40 h-20 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden">
                        <?php if ($storeLogo): ?>
                            <img src="/<?= esc($storeLogo) ?>" alt="Logo" id="logo-preview-img" class="max-h-16 max-w-full object-contain">
                        <?php else: ?>
                            <span class="text-xs text-gray-400" id="logo-placeholder">Sin logo</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">160×64 px recomendado</p>
                </div>
                <!-- Acciones -->
                <div class="flex-1 space-y-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <input type="file" name="logo" id="logo-file" accept="image/*"
                            form="logo-upload-form"
                            onchange="previewLogo(this)"
                            class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                                   file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm
                                   file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100
                                   dark:bg-gray-700 dark:text-gray-300">
                        <button type="submit" form="logo-upload-form"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 whitespace-nowrap">
                            Subir logo
                        </button>
                    </div>
                    <?php if ($storeLogo): ?>
                        <div>
                            <button type="submit" form="logo-delete-form"
                                onclick="return confirm('¿Eliminar el logo actual?')"
                                class="text-sm text-red-600 dark:text-red-400 hover:underline">
                                Eliminar logo
                            </button>
                        </div>
                    <?php endif; ?>
                    <p class="text-xs text-gray-500">PNG, JPG, SVG o WebP. Si no hay logo se muestra el nombre de la tienda.</p>
                </div>
            </div>
        </div>

        <!-- Color de marca -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Color principal de marca</h2>
            <div class="flex flex-col sm:flex-row sm:items-end gap-6">
                <div class="flex items-center gap-4">
                    <input type="color" name="brand_primary_color" id="color-picker"
                        value="<?= esc($brandColor) ?>"
                        class="w-14 h-14 rounded-xl border border-gray-300 dark:border-gray-600 cursor-pointer p-1"
                        oninput="updateColorPreview(this.value)">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código hex</label>
                        <input type="text" id="color-hex" value="<?= esc($brandColor) ?>"
                            class="w-28 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            oninput="syncColorPicker(this.value)">
                    </div>
                </div>
                <!-- Mini preview -->
                <div class="flex-1 max-w-xs">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Vista previa</p>
                    <div id="cp-bar" class="rounded-lg px-4 py-3 text-white text-sm font-semibold mb-2"
                         style="background-color:<?= esc($brandColor) ?>"><?= esc($storeName) ?></div>
                    <div class="flex items-center gap-3">
                        <span id="cp-link" class="text-sm font-semibold" style="color:<?= esc($brandColor) ?>">Enlace</span>
                        <span id="cp-badge" class="text-xs px-2 py-0.5 rounded-full text-white"
                              style="background-color:<?= esc($brandColor) ?>">Badge</span>
                        <button type="button" id="cp-btn" class="text-xs px-3 py-1 rounded-lg text-white"
                                style="background-color:<?= esc($brandColor) ?>">Botón</button>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-4">Se aplica al logo en la barra de navegación, hero de la landing, botones y acentos.</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                Guardar cambios
            </button>
        </div>
    </div><!-- /section-brand -->

    <!-- ===== TAB: LANDING ===== -->
    <div id="section-hero" class="tab-section hidden space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Sección hero de la landing</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título principal</label>
                    <input type="text" name="hero_title" value="<?= esc($heroTitle) ?>" maxlength="100"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtítulo</label>
                    <textarea name="hero_subtitle" rows="3" maxlength="255"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= esc($heroSubtitle) ?></textarea>
                </div>
            </div>

            <!-- Preview del hero -->
            <div class="mt-6">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Vista previa del hero</p>
                <div id="hero-preview" class="rounded-xl p-6 text-white"
                     style="background:linear-gradient(135deg, <?= esc($brandColor) ?>, #7c3aed)">
                    <h3 id="hp-title" class="text-xl font-bold mb-1"><?= esc($heroTitle ?: 'Título del hero') ?></h3>
                    <p  id="hp-sub"   class="text-sm opacity-90"><?= esc($heroSubtitle ?: 'Subtítulo del hero') ?></p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                Guardar landing
            </button>
        </div>
    </div><!-- /section-hero -->

    <!-- ===== TAB: CONTACTO ===== -->
    <div id="section-contact" class="tab-section hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Información de contacto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email de contacto</label>
                    <input type="email" name="store_email" value="<?= esc($s['store_email'] ?? '') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                    <input type="text" name="store_phone" value="<?= esc($s['store_phone'] ?? '') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección</label>
                    <input type="text" name="store_address" value="<?= esc($s['store_address'] ?? '') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                Guardar contacto
            </button>
        </div>
    </div><!-- /section-contact -->

    <!-- ===== TAB: REDES SOCIALES ===== -->
    <div id="section-social" class="tab-section hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Redes sociales</h2>
            <div class="space-y-4">
                <?php foreach ([
                    'facebook_url'    => 'Facebook',
                    'instagram_url'   => 'Instagram',
                    'twitter_url'     => 'Twitter / X',
                    'whatsapp_number' => 'WhatsApp',
                ] as $field => $label): ?>
                <div class="flex items-center gap-3">
                    <span class="w-28 text-sm text-gray-600 dark:text-gray-400 font-medium"><?= $label ?></span>
                    <input type="text" name="<?= $field ?>" value="<?= esc($sc[$field] ?? '') ?>"
                        class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                Guardar redes
            </button>
        </div>
    </div><!-- /section-social -->

</form>

<script>
// ---- Tabs ----
function showTab(name) {
    document.querySelectorAll('.tab-section').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-indigo-600','text-indigo-600','dark:text-indigo-400');
        btn.classList.add('border-transparent','text-gray-500','dark:text-gray-400');
    });
    document.getElementById('section-' + name).classList.remove('hidden');
    const b = document.getElementById('tab-' + name);
    b.classList.add('border-indigo-600','text-indigo-600');
    b.classList.remove('border-transparent','text-gray-500','dark:text-gray-400');
    localStorage.setItem('settings-tab', name);
}
const savedTab = localStorage.getItem('settings-tab');
if (savedTab && document.getElementById('section-' + savedTab)) showTab(savedTab);

// ---- Color picker ----
function updateColorPreview(hex) {
    document.getElementById('color-hex').value = hex;
    ['cp-bar','cp-badge','cp-btn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.backgroundColor = hex;
    });
    const link = document.getElementById('cp-link');
    if (link) link.style.color = hex;
    const hp = document.getElementById('hero-preview');
    if (hp) hp.style.background = 'linear-gradient(135deg, ' + hex + ', #7c3aed)';
    const bar = document.getElementById('cp-bar');
    if (bar) bar.textContent = document.querySelector('[name=store_name]')?.value || 'Mi Tienda';
}
function syncColorPicker(hex) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        document.getElementById('color-picker').value = hex;
        updateColorPreview(hex);
    }
}
document.querySelector('[name=store_name]')?.addEventListener('input', function() {
    const bar = document.getElementById('cp-bar');
    if (bar) bar.textContent = this.value || 'Mi Tienda';
});

// ---- Hero preview en vivo ----
document.querySelector('[name=hero_title]')?.addEventListener('input', function() {
    const el = document.getElementById('hp-title');
    if (el) el.textContent = this.value || 'Título del hero';
});
document.querySelector('[name=hero_subtitle]')?.addEventListener('input', function() {
    const el = document.getElementById('hp-sub');
    if (el) el.textContent = this.value || 'Subtítulo del hero';
});

// ---- Logo preview local ----
function previewLogo(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const wrap = document.getElementById('logo-preview-wrap');
        const ph = document.getElementById('logo-placeholder');
        if (ph) ph.remove();
        let img = document.getElementById('logo-preview-img');
        if (!img) {
            img = document.createElement('img');
            img.id = 'logo-preview-img';
            img.className = 'max-h-16 max-w-full object-contain';
            wrap.appendChild(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

<?= $this->endSection() ?>
