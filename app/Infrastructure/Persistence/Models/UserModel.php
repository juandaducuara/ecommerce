<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'role_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'document_type',
        'document_number',
        'avatar',
        'email_verified_at',
        'remember_token',
        'reset_token',
        'reset_token_expires_at',
        'status',
        'last_login_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'email'      => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password'   => 'required|min_length[8]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'role_id'    => 'required|integer|is_not_unique[roles.id]',
    ];

    protected $validationMessages = [
        'email' => [
            'required'    => 'El email es requerido',
            'valid_email' => 'Ingrese un email válido',
            'is_unique'   => 'Este email ya está registrado',
        ],
        'password' => [
            'min_length' => 'La contraseña debe tener al menos 8 caracteres',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    // Métodos de búsqueda
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function findActiveByEmail(string $email)
    {
        return $this->where('email', $email)
            ->where('status', 'active')
            ->first();
    }

    // Verificar contraseña
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // Relación con rol
    public function getRole(int $userId)
    {
        $user = $this->find($userId);
        if (!$user) {
            return null;
        }
        return (new RoleModel())->find($user->role_id);
    }

    // Obtener nombre completo
    public function getFullName(object $user): string
    {
        return trim($user->first_name . ' ' . $user->last_name);
    }

    // Customers (usuarios con rol customer)
    public function getCustomers(int $limit = 20, int $offset = 0)
    {
        $customerRole = (new RoleModel())->findBySlug('customer');

        return $this->where('role_id', $customerRole->id)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    // Admins
    public function getAdmins()
    {
        $adminRoles = (new RoleModel())
            ->whereIn('slug', ['super-admin', 'admin'])
            ->findAll();

        $roleIds = array_column($adminRoles, 'id');

        return $this->whereIn('role_id', $roleIds)->findAll();
    }

    // Actualizar último login
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
