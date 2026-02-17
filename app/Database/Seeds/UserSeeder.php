<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Obtener el rol de super-admin
        $superAdminRole = $this->db->table('roles')
            ->where('slug', 'super-admin')
            ->get()
            ->getRow();

        $customerRole = $this->db->table('roles')
            ->where('slug', 'customer')
            ->get()
            ->getRow();

        $data = [
            [
                'role_id'           => $superAdminRole->id,
                'email'             => 'admin@tienda.com',
                'password'          => password_hash('Admin123!', PASSWORD_BCRYPT),
                'first_name'        => 'Administrador',
                'last_name'         => 'Principal',
                'phone'             => '3001234567',
                'document_type'     => 'CC',
                'document_number'   => '123456789',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'status'            => 'active',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'role_id'           => $customerRole->id,
                'email'             => 'cliente@example.com',
                'password'          => password_hash('Cliente123!', PASSWORD_BCRYPT),
                'first_name'        => 'Juan',
                'last_name'         => 'Perez',
                'phone'             => '3109876543',
                'document_type'     => 'CC',
                'document_number'   => '987654321',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'status'            => 'active',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
