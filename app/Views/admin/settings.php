<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$s  = $general ?? [];
$sc = $social  ?? [];
$sp = $payment ?? [];

$storeName    = $s['store_name']          ?? 'Mi Tienda Online';
$storeLogo    = $s['store_logo']          ?? '';
$brandColor   = $s['brand_primary_color'] ?? '#4f46e5';
$tagline      = $s['tagline']             ?? '';
$heroTitle    = $s['hero_title']          ?? '';
$heroSubtitle = $s['hero_subtitle']       ?? '';
$brandTint    = $brandColor . '1a';   // Tono suave para fondos de iconos

// Colores preset
$presetColors = [
    '#4f46e5' => 'Índigo',
    '#7c3aed' => 'Violeta',
    '#db2777' => 'Rosa',
    '#dc2626' => 'Rojo',
    '#d97706' => 'Ámbar',
    '#16a34a' => 'Verde',
    '#0891b2' => 'Cian',
    '#1d4ed8' => 'Azul',
    '#0f172a' => 'Oscuro',
    '#64748b' => 'Gris pizarra',
];
?>

<!-- Formularios auxiliares para logo (fuera del form principal) -->
<form id="logo-upload-form" action="/admin/settings/logo" method="POST" enctype="multipart/form-data" class="hidden">
    <?= csrf_field() ?>
</form>
<form id="logo-delete-form" action="/admin/settings/logo/delete" method="POST" class="hidden">
    <?= csrf_field() ?>
</form>

<!-- Descripción de página -->
<p class="text-sm text-gray-500 dark:text-gray-400 mb-6 -mt-1">
    Personaliza la apariencia, información y métodos de pago de tu tienda.
</p>

<!-- ═══ TABS (pill style) ═══ -->
<div class="flex items-center gap-2 overflow-x-auto pb-1 mb-6" id="settings-tabs">
    <?php
    $tabs = [
        'brand'   => ['Marca',         'bi-palette',         ],
        'hero'    => ['Landing',        'bi-display',         ],
        'contact' => ['Contacto',       'bi-telephone',       ],
        'social'  => ['Redes sociales', 'bi-share',           ],
        'payment' => ['Pagos',          'bi-credit-card',     ],
    ];
    foreach ($tabs as $key => [$label, $icon]):
    ?>
        <button onclick="showTab('<?= $key ?>')" id="tab-<?= $key ?>"
                class="tab-btn flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition
                       <?= $key === 'brand' ? 'brand-bg text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' ?>">
            <i class="bi <?= $icon ?> text-sm"></i>
            <?= $label ?>
        </button>
    <?php endforeach; ?>
</div>

<!-- ═══ FORMULARIO PRINCIPAL ═══ -->
<form action="/admin/settings" method="POST">
    <?= csrf_field() ?>

    <!-- ════════════════════════════════════════
         TAB: MARCA
    ════════════════════════════════════════ -->
    <div id="section-brand" class="tab-section space-y-5">

        <!-- Identidad -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: <?= esc($brandTint) ?>">
                    <i class="bi bi-shop brand-text text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Identidad de la tienda</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Nombre y eslogan que aparecen en el sitio.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Nombre de la tienda *</label>
                    <input type="text" name="store_name" value="<?= esc($storeName) ?>" required
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Aparece en navbar, footer y título del sitio.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Eslogan / Tagline</label>
                    <input type="text" name="tagline" value="<?= esc($tagline) ?>" maxlength="120"
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Frase corta descriptiva de tu tienda.</p>
                </div>
            </div>
        </div>

        <!-- Logo -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: <?= esc($brandTint) ?>">
                    <i class="bi bi-image brand-text text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Logo de la tienda</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">PNG, JPG, SVG o WebP. 160×64 px recomendado.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-6 items-start">

                <!-- Preview -->
                <div class="flex-shrink-0 flex flex-col items-center gap-2">
                    <div id="logo-preview-wrap"
                         class="w-44 h-24 border-2 border-dashed border-gray-200 dark:border-gray-600
                                rounded-xl flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden">
                        <?php if ($storeLogo): ?>
                            <img src="/<?= esc($storeLogo) ?>" alt="Logo" id="logo-preview-img"
                                 class="max-h-20 max-w-full object-contain p-2">
                        <?php else: ?>
                            <div class="text-center text-gray-300 dark:text-gray-600" id="logo-placeholder">
                                <i class="bi bi-image text-3xl block mb-1"></i>
                                <span class="text-xs">Sin logo</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex-1 space-y-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <input type="file" name="logo" id="logo-file" accept="image/*"
                            form="logo-upload-form"
                            onchange="previewLogo(this)"
                            class="flex-1 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2 text-sm
                                   file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium
                                   file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100
                                   dark:bg-gray-700 dark:text-gray-300">
                        <button type="submit" form="logo-upload-form"
                            class="brand-bg text-white px-4 py-2 rounded-xl text-sm font-medium hover:opacity-90 transition whitespace-nowrap flex items-center gap-1.5">
                            <i class="bi bi-cloud-upload text-sm"></i> Subir logo
                        </button>
                    </div>
                    <?php if ($storeLogo): ?>
                        <button type="submit" form="logo-delete-form"
                            onclick="return confirm('¿Eliminar el logo actual?')"
                            class="flex items-center gap-1.5 text-sm text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition">
                            <i class="bi bi-trash3 text-sm"></i> Eliminar logo actual
                        </button>
                    <?php endif; ?>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Si no hay logo, se mostrará la inicial del nombre de la tienda.</p>
                </div>
            </div>
        </div>

        <!-- Color de marca -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: <?= esc($brandTint) ?>">
                    <i class="bi bi-palette2 brand-text text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Color principal de marca</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Se aplica en botones, badges, navbar y acentos.</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Picker + hex -->
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <input type="color" name="brand_primary_color" id="color-picker"
                                value="<?= esc($brandColor) ?>"
                                class="w-16 h-16 rounded-xl border-2 border-gray-200 dark:border-gray-600 cursor-pointer p-1 bg-white dark:bg-gray-700"
                                oninput="updateColorPreview(this.value)">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Código hex</label>
                            <input type="text" id="color-hex" value="<?= esc($brandColor) ?>"
                                class="w-32 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                       rounded-xl px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                oninput="syncColorPicker(this.value)">
                        </div>
                    </div>

                    <!-- Swatches preset -->
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 font-medium">Colores rápidos</p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($presetColors as $hex => $name): ?>
                                <button type="button"
                                        onclick="document.getElementById('color-picker').value='<?= $hex ?>'; updateColorPreview('<?= $hex ?>');"
                                        title="<?= $name ?>"
                                        class="w-7 h-7 rounded-lg hover:scale-110 transition-transform shadow-sm border-2 border-white dark:border-gray-800 ring-1 ring-gray-200 dark:ring-gray-700"
                                        style="background-color: <?= $hex ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Vista previa -->
                <div class="flex-1">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-3 uppercase tracking-wide">Vista previa</p>
                    <div class="space-y-3">
                        <!-- Botón -->
                        <div class="flex items-center gap-3 flex-wrap">
                            <button type="button" id="cp-btn"
                                    class="text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-sm"
                                    style="background-color:<?= esc($brandColor) ?>">
                                Botón principal
                            </button>
                            <span id="cp-badge"
                                  class="text-xs font-bold px-3 py-1 rounded-full text-white"
                                  style="background-color:<?= esc($brandColor) ?>">
                                Badge
                            </span>
                            <span id="cp-link" class="text-sm font-semibold underline underline-offset-2"
                                  style="color:<?= esc($brandColor) ?>">
                                Enlace
                            </span>
                        </div>
                        <!-- Barra / navbar mini -->
                        <div id="cp-bar"
                             class="rounded-xl px-4 py-3 text-white text-sm font-semibold flex items-center gap-2"
                             style="background-color:<?= esc($brandColor) ?>">
                            <span class="w-5 h-5 rounded-md bg-white/20 flex items-center justify-center text-xs font-bold">
                                <?= mb_strtoupper(mb_substr($storeName, 0, 1)) ?>
                            </span>
                            <span id="cp-store-name"><?= esc($storeName) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="brand-bg text-white px-6 py-2.5 rounded-xl hover:opacity-90 text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-check2 text-base"></i> Guardar cambios
            </button>
        </div>
    </div><!-- /section-brand -->


    <!-- ════════════════════════════════════════
         TAB: LANDING
    ════════════════════════════════════════ -->
    <div id="section-hero" class="tab-section hidden space-y-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: <?= esc($brandTint) ?>">
                    <i class="bi bi-display brand-text text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Sección hero de la landing</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Texto principal que ve el cliente al entrar a la tienda.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Título principal</label>
                    <input type="text" name="hero_title" value="<?= esc($heroTitle) ?>" maxlength="100"
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Subtítulo</label>
                    <textarea name="hero_subtitle" rows="3" maxlength="255"
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                               resize-none"><?= esc($heroSubtitle) ?></textarea>
                </div>
            </div>

            <!-- Preview del hero (refleja el nuevo diseño limpio) -->
            <div class="mt-6">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 uppercase tracking-wide">Vista previa</p>
                <div id="hero-preview"
                     class="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 relative bg-white dark:bg-gray-900">
                    <!-- Dot pattern -->
                    <div class="absolute inset-0 pointer-events-none" id="hp-dots"
                         style="background-image: radial-gradient(circle, <?= esc($brandColor) ?>22 1.5px, transparent 1.5px); background-size: 20px 20px;"></div>
                    <!-- Orb -->
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full pointer-events-none" id="hp-orb"
                         style="background: <?= esc($brandColor) ?>18; filter: blur(30px);"></div>
                    <div class="relative p-6 text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider
                                     px-3 py-1 rounded-full mb-3 text-white" id="hp-badge"
                              style="background-color: <?= esc($brandColor) ?>">
                            <i class="bi bi-lightning-fill" style="font-size:9px"></i> Ofertas
                        </span>
                        <h3 id="hp-title" class="text-base font-extrabold text-gray-900 dark:text-white mb-1">
                            <?= esc($heroTitle ?: 'Título del hero') ?>
                        </h3>
                        <p id="hp-sub" class="text-sm text-gray-500 dark:text-gray-400">
                            <?= esc($heroSubtitle ?: 'Subtítulo descriptivo de tu tienda') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="brand-bg text-white px-6 py-2.5 rounded-xl hover:opacity-90 text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-check2 text-base"></i> Guardar landing
            </button>
        </div>
    </div><!-- /section-hero -->


    <!-- ════════════════════════════════════════
         TAB: CONTACTO
    ════════════════════════════════════════ -->
    <div id="section-contact" class="tab-section hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: <?= esc($brandTint) ?>">
                    <i class="bi bi-telephone brand-text text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Información de contacto</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Aparece en el footer del sitio.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Email de contacto</label>
                    <div class="relative">
                        <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="email" name="store_email" value="<?= esc($s['store_email'] ?? '') ?>"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                   rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Teléfono</label>
                    <div class="relative">
                        <i class="bi bi-telephone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text" name="store_phone" value="<?= esc($s['store_phone'] ?? '') ?>"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                   rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Dirección</label>
                    <div class="relative">
                        <i class="bi bi-geo-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text" name="store_address" value="<?= esc($s['store_address'] ?? '') ?>"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                   rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-5">
            <button type="submit"
                    class="brand-bg text-white px-6 py-2.5 rounded-xl hover:opacity-90 text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-check2 text-base"></i> Guardar contacto
            </button>
        </div>
    </div><!-- /section-contact -->


    <!-- ════════════════════════════════════════
         TAB: REDES SOCIALES
    ════════════════════════════════════════ -->
    <div id="section-social" class="tab-section hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: <?= esc($brandTint) ?>">
                    <i class="bi bi-share brand-text text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Redes sociales</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Los íconos aparecen en el footer del sitio.</p>
                </div>
            </div>
            <div class="space-y-3">
                <?php foreach ([
                    'facebook_url'    => ['Facebook',  'bi-facebook',  '#1877f2'],
                    'instagram_url'   => ['Instagram',  'bi-instagram', '#e1306c'],
                    'twitter_url'     => ['Twitter / X','bi-twitter-x', '#111827'],
                    'whatsapp_number' => ['WhatsApp',   'bi-whatsapp',  '#25d366'],
                ] as $field => [$label, $icon, $color]): ?>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: <?= $color ?>18; color: <?= $color ?>">
                        <i class="bi <?= $icon ?> text-base"></i>
                    </div>
                    <span class="w-28 text-sm text-gray-700 dark:text-gray-300 font-medium flex-shrink-0"><?= $label ?></span>
                    <input type="text" name="<?= $field ?>" value="<?= esc($sc[$field] ?? '') ?>"
                        placeholder="<?= $field === 'whatsapp_number' ? '+57 300 123 4567' : 'https://...' ?>"
                        class="flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                               placeholder-gray-300 dark:placeholder-gray-600">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex justify-end mt-5">
            <button type="submit"
                    class="brand-bg text-white px-6 py-2.5 rounded-xl hover:opacity-90 text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-check2 text-base"></i> Guardar redes
            </button>
        </div>
    </div><!-- /section-social -->


    <!-- ════════════════════════════════════════
         TAB: PAGOS
    ════════════════════════════════════════ -->
    <div id="section-payment" class="tab-section hidden space-y-5">

        <!-- PayU -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-sm">
                        <span class="text-white text-xs font-extrabold tracking-tight">PU</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">PayU</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Pasarela de pagos para Latinoamérica</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                    <input type="checkbox" name="payu_sandbox" value="1"
                        <?= !empty($sp['payu_sandbox']) ? 'checked' : '' ?>
                        class="rounded border-gray-300 dark:border-gray-600 text-yellow-500 focus:ring-yellow-400">
                    <span>Sandbox</span>
                    <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs px-2 py-0.5 rounded-lg font-semibold">TEST</span>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ([
                    ['payu_merchant_id', 'Merchant ID', 'text'],
                    ['payu_account_id',  'Account ID',  'text'],
                    ['payu_api_login',   'API Login',   'text'],
                ] as [$name, $label, $type]): ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide"><?= $label ?></label>
                    <input type="<?= $type ?>" name="<?= $name ?>" value="<?= esc($sp[$name] ?? '') ?>"
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <?php endforeach; ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">API Key</label>
                    <div class="relative">
                        <input type="password" name="payu_api_key" id="payu_api_key"
                            value="<?= esc($sp['payu_api_key'] ?? '') ?>"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                   rounded-xl px-3 py-2.5 pr-10 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" onclick="toggleSecret('payu_api_key')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <i class="bi bi-eye text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 flex items-center gap-1.5">
                <i class="bi bi-info-circle text-sm flex-shrink-0"></i>
                Obtén tus credenciales en <span class="font-mono">developers.payulatam.com</span>
            </p>
        </div>

        <!-- MercadoPago -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-sky-500 flex items-center justify-center shadow-sm">
                        <span class="text-white text-xs font-extrabold tracking-tight">MP</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">MercadoPago</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Pagos en línea para Colombia y Latam</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                    <input type="checkbox" name="mercadopago_sandbox" value="1"
                        <?= !empty($sp['mercadopago_sandbox']) ? 'checked' : '' ?>
                        class="rounded border-gray-300 dark:border-gray-600 text-yellow-500 focus:ring-yellow-400">
                    <span>Sandbox</span>
                    <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs px-2 py-0.5 rounded-lg font-semibold">TEST</span>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Public Key</label>
                    <input type="text" name="mercadopago_public_key"
                        value="<?= esc($sp['mercadopago_public_key'] ?? '') ?>"
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Clave pública (visible en el frontend).</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Access Token</label>
                    <div class="relative">
                        <input type="password" name="mercadopago_access_token" id="mp_access_token"
                            value="<?= esc($sp['mercadopago_access_token'] ?? '') ?>"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                   rounded-xl px-3 py-2.5 pr-10 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" onclick="toggleSecret('mp_access_token')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <i class="bi bi-eye text-sm"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Clave secreta — nunca la compartas.</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 flex items-center gap-1.5">
                <i class="bi bi-info-circle text-sm flex-shrink-0"></i>
                Obtén tus credenciales en <span class="font-mono">mercadopago.com/developers</span>
            </p>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="brand-bg text-white px-6 py-2.5 rounded-xl hover:opacity-90 text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-check2 text-base"></i> Guardar pagos
            </button>
        </div>
    </div><!-- /section-payment -->

</form>

<script>
// ════════════════ TABS ════════════════
function showTab(name) {
    // Ocultar secciones
    document.querySelectorAll('.tab-section').forEach(el => el.classList.add('hidden'));
    // Reset botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.removeProperty('background-color');
        btn.style.removeProperty('color');
        btn.classList.remove('text-white', 'shadow-sm');
        btn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-400');
    });
    // Activar sección
    document.getElementById('section-' + name).classList.remove('hidden');
    // Activar botón
    const btn = document.getElementById('tab-' + name);
    btn.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-400');
    btn.classList.add('text-white', 'shadow-sm');
    btn.style.backgroundColor = document.getElementById('color-picker')?.value || '#4f46e5';
    localStorage.setItem('settings-tab', name);
}
// Restaurar tab al cargar
const savedTab = localStorage.getItem('settings-tab');
if (savedTab && document.getElementById('section-' + savedTab)) {
    showTab(savedTab);
} else {
    showTab('brand');
}

// ════════════════ COLOR PICKER ════════════════
function updateColorPreview(hex) {
    // Hex input
    document.getElementById('color-hex').value = hex;
    // Elementos de preview
    ['cp-btn','cp-badge','cp-bar'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.backgroundColor = hex;
    });
    const link = document.getElementById('cp-link');
    if (link) link.style.color = hex;

    // Hero preview
    const dots = document.getElementById('hp-dots');
    if (dots) dots.style.backgroundImage =
        `radial-gradient(circle, ${hex}22 1.5px, transparent 1.5px)`;
    const orb = document.getElementById('hp-orb');
    if (orb) orb.style.background = hex + '18';
    const badge = document.getElementById('hp-badge');
    if (badge) badge.style.backgroundColor = hex;

    // Tab activo
    const activeTab = localStorage.getItem('settings-tab') || 'brand';
    const activeBtn = document.getElementById('tab-' + activeTab);
    if (activeBtn && activeBtn.classList.contains('text-white')) {
        activeBtn.style.backgroundColor = hex;
    }

    // Nombre en bar
    const barName = document.getElementById('cp-store-name');
    if (barName) barName.textContent =
        document.querySelector('[name=store_name]')?.value || 'Mi Tienda';
}
function syncColorPicker(hex) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        document.getElementById('color-picker').value = hex;
        updateColorPreview(hex);
    }
}
document.querySelector('[name=store_name]')?.addEventListener('input', function() {
    const el = document.getElementById('cp-store-name');
    if (el) el.textContent = this.value || 'Mi Tienda';
});

// ════════════════ HERO PREVIEW LIVE ════════════════
document.querySelector('[name=hero_title]')?.addEventListener('input', function() {
    const el = document.getElementById('hp-title');
    if (el) el.textContent = this.value || 'Título del hero';
});
document.querySelector('[name=hero_subtitle]')?.addEventListener('input', function() {
    const el = document.getElementById('hp-sub');
    if (el) el.textContent = this.value || 'Subtítulo descriptivo de tu tienda';
});

// ════════════════ MOSTRAR / OCULTAR SECRETO ════════════════
function toggleSecret(id) {
    const input = document.getElementById(id);
    const icon  = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash text-sm';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye text-sm';
    }
}

// ════════════════ LOGO PREVIEW LOCAL ════════════════
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
            img.className = 'max-h-20 max-w-full object-contain p-2';
            wrap.appendChild(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

<?= $this->endSection() ?>
