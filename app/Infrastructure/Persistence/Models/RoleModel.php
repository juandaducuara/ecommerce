<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'name',
        'slug',
        'description',
        'permissions',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[50]',
        'slug' => 'required|alpha_dash|is_unique[roles.slug,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'El nombre es requerido',
        ],
        'slug' => [
            'is_unique' => 'Este slug ya existe',
        ],
    ];

    // Relaciones
    public function users()
    {
        return $this->db->table('users')
            ->where('role_id', $this->id)
            ->get()
            ->getResult();
    }

    // Métodos de búsqueda
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getPermissions(int $roleId): array
    {
        $role = $this->find($roleId);
        if (!$role || empty($role->permissions)) {
            return [];
        }
        return json_decode($role->permissions, true) ?? [];
    }
}
