<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
    <a href="/admin/users/create" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
        + Nuevo Usuario
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form action="/admin/users" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Buscar</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>"
                placeholder="Nombre o email..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Rol</label>
            <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= esc($role->slug) ?>" <?= ($roleFilter ?? '') === $role->slug ? 'selected' : '' ?>>
                        <?= esc($role->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Estado</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                <option value="suspended" <?= ($statusFilter ?? '') === 'suspended' ? 'selected' : '' ?>>Suspendido</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm">Filtrar</button>
        <a href="/admin/users" class="text-sm text-gray-500 hover:text-gray-700 py-2">Limpiar</a>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Nombre</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Email</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Rol</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Estado</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Registro</th>
                <th class="text-right px-4 py-3 text-gray-600 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No se encontraron usuarios.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <?= esc($user->first_name . ' ' . $user->last_name) ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= esc($user->email) ?></td>
                        <td class="px-4 py-3">
                            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-medium">
                                <?= esc($user->role_name ?? 'Sin rol') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <?php
                                $statusColors = [
                                    'active'    => 'bg-green-50 text-green-700',
                                    'inactive'  => 'bg-gray-100 text-gray-600',
                                    'suspended' => 'bg-red-50 text-red-700',
                                ];
                                $statusLabels = [
                                    'active'    => 'Activo',
                                    'inactive'  => 'Inactivo',
                                    'suspended' => 'Suspendido',
                                ];
                                $color = $statusColors[$user->status] ?? 'bg-gray-100 text-gray-600';
                                $label = $statusLabels[$user->status] ?? $user->status;
                            ?>
                            <span class="<?= $color ?> px-2 py-1 rounded text-xs font-medium"><?= $label ?></span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs"><?= esc($user->created_at) ?></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="/admin/users/<?= $user->id ?>/edit"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>
                                <?php if ((int) $user->id !== (int) session()->get('user_id')): ?>
                                    <form action="/admin/users/<?= $user->id ?>/delete" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
