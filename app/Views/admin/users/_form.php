<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="first_name" id="first_name"
                value="<?= old('first_name', $user->first_name ?? '') ?>"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required>
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Apellido *</label>
            <input type="text" name="last_name" id="last_name"
                value="<?= old('last_name', $user->last_name ?? '') ?>"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required>
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
        <input type="email" name="email" id="email"
            value="<?= old('email', $user->email ?? '') ?>"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            required>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Contraseña <?= isset($user) ? '' : '*' ?>
            </label>
            <input type="password" name="password" id="password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                minlength="8" <?= isset($user) ? '' : 'required' ?>>
            <?php if (isset($user)): ?>
                <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la contraseña actual</p>
            <?php endif; ?>
        </div>
        <div>
            <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                Confirmar contraseña <?= isset($user) ? '' : '*' ?>
            </label>
            <input type="password" name="password_confirm" id="password_confirm"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                minlength="8" <?= isset($user) ? '' : 'required' ?>>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
            <select name="role_id" id="role_id"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required>
                <option value="">Seleccionar rol</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role->id ?>"
                        <?= old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' ?>>
                        <?= esc($role->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
            <select name="status" id="status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required>
                <?php
                    $currentStatus = old('status', $user->status ?? 'active');
                    $statuses = ['active' => 'Activo', 'inactive' => 'Inactivo', 'suspended' => 'Suspendido'];
                ?>
                <?php foreach ($statuses as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $currentStatus === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
            <input type="text" name="phone" id="phone"
                value="<?= old('phone', $user->phone ?? '') ?>"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo documento</label>
            <select name="document_type" id="document_type"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php
                    $currentDocType = old('document_type', $user->document_type ?? 'CC');
                    $docTypes = ['CC' => 'Cédula de Ciudadanía', 'CE' => 'Cédula de Extranjería', 'NIT' => 'NIT', 'PP' => 'Pasaporte'];
                ?>
                <?php foreach ($docTypes as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $currentDocType === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">Número documento</label>
            <input type="text" name="document_number" id="document_number"
                value="<?= old('document_number', $user->document_number ?? '') ?>"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>
</div>
