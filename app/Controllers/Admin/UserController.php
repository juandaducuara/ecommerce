<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\UserModel;
use App\Infrastructure\Persistence\Models\RoleModel;

class UserController extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $roleFilter = $this->request->getGet('role');
        $statusFilter = $this->request->getGet('status');

        $builder = $this->userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }

        if ($roleFilter) {
            $builder->where('roles.slug', $roleFilter);
        }

        if ($statusFilter) {
            $builder->where('users.status', $statusFilter);
        }

        $perPage = 15;
        $users   = $builder->orderBy('users.created_at', 'DESC')->paginate($perPage, 'default');
        $pager   = $this->userModel->pager;
        $roles   = $this->roleModel->findAll();

        return view('admin/users/index', [
            'title'        => 'Gestión de Usuarios',
            'users'        => $users,
            'roles'        => $roles,
            'search'       => $search,
            'roleFilter'   => $roleFilter,
            'statusFilter' => $statusFilter,
            'pager'        => $pager,
        ]);
    }

    public function create()
    {
        $roles = $this->roleModel->findAll();

        return view('admin/users/create', [
            'title' => 'Crear Usuario',
            'roles' => $roles,
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role_id'          => 'required|integer|is_not_unique[roles.id]',
            'status'           => 'required|in_list[active,inactive,suspended]',
        ];

        $messages = [
            'email'            => ['is_unique' => 'Este email ya está registrado.'],
            'password_confirm' => ['matches' => 'Las contraseñas no coinciden.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name'      => $this->request->getPost('first_name'),
            'last_name'       => $this->request->getPost('last_name'),
            'email'           => $this->request->getPost('email'),
            'password'        => $this->request->getPost('password'),
            'role_id'         => $this->request->getPost('role_id'),
            'status'          => $this->request->getPost('status'),
            'phone'           => $this->request->getPost('phone') ?: null,
            'document_type'   => $this->request->getPost('document_type') ?: 'CC',
            'document_number' => $this->request->getPost('document_number') ?: null,
        ];

        if (!$this->userModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Error al crear el usuario.');
        }

        return redirect()->to('/admin/users')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }

        $roles = $this->roleModel->findAll();

        return view('admin/users/edit', [
            'title' => 'Editar Usuario',
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role_id'    => 'required|integer|is_not_unique[roles.id]',
            'status'     => 'required|in_list[active,inactive,suspended]',
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        $messages = [
            'email'            => ['is_unique' => 'Este email ya está registrado.'],
            'password_confirm' => ['matches' => 'Las contraseñas no coinciden.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name'      => $this->request->getPost('first_name'),
            'last_name'       => $this->request->getPost('last_name'),
            'email'           => $this->request->getPost('email'),
            'role_id'         => $this->request->getPost('role_id'),
            'status'          => $this->request->getPost('status'),
            'phone'           => $this->request->getPost('phone') ?: null,
            'document_type'   => $this->request->getPost('document_type') ?: 'CC',
            'document_number' => $this->request->getPost('document_number') ?: null,
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        // Evitar que un admin se cambie su propio rol o se desactive
        if ((int) $id === (int) session()->get('user_id')) {
            unset($data['role_id'], $data['status']);
        }

        if (!$this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el usuario.');
        }

        return redirect()->to('/admin/users')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function delete(int $id)
    {
        // No permitir eliminarse a sí mismo
        if ((int) $id === (int) session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }

        $this->userModel->delete($id);

        return redirect()->to('/admin/users')->with('success', 'Usuario eliminado exitosamente.');
    }
}
