<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table            = 'addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'type',
        'is_default',
        'first_name',
        'last_name',
        'company',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'additional_info',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'        => 'required|integer',
        'first_name'     => 'required|min_length[2]|max_length[100]',
        'last_name'      => 'required|min_length[2]|max_length[100]',
        'phone'          => 'required|min_length[7]|max_length[20]',
        'address_line_1' => 'required|min_length[5]|max_length[255]',
        'city'           => 'required|min_length[2]|max_length[100]',
        'state'          => 'required|min_length[2]|max_length[100]',
    ];

    // Tipos de dirección
    const TYPE_BILLING  = 'billing';
    const TYPE_SHIPPING = 'shipping';

    // Obtener direcciones de un usuario
    public function getByUser(int $userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->findAll();
    }

    // Obtener direcciones por tipo
    public function getByUserAndType(int $userId, string $type)
    {
        return $this->where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('is_default', 'DESC')
            ->findAll();
    }

    // Obtener dirección predeterminada
    public function getDefault(int $userId, string $type)
    {
        return $this->where('user_id', $userId)
            ->where('type', $type)
            ->where('is_default', 1)
            ->first();
    }

    // Establecer como predeterminada
    public function setDefault(int $addressId, int $userId, string $type): bool
    {
        // Quitar predeterminada de otras direcciones del mismo tipo
        $this->where('user_id', $userId)
            ->where('type', $type)
            ->set('is_default', 0)
            ->update();

        // Establecer la nueva dirección como predeterminada
        return $this->update($addressId, ['is_default' => 1]);
    }

    // Formatear dirección como string
    public function format(object $address): string
    {
        $parts = [
            $address->address_line_1,
        ];

        if (!empty($address->address_line_2)) {
            $parts[] = $address->address_line_2;
        }

        $parts[] = $address->city . ', ' . $address->state;

        if (!empty($address->postal_code)) {
            $parts[] = $address->postal_code;
        }

        return implode(', ', $parts);
    }

    // Obtener nombre completo
    public function getFullName(object $address): string
    {
        return trim($address->first_name . ' ' . $address->last_name);
    }
}
