<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Super Administrador',
                'slug'        => 'super-admin',
                'description' => 'Acceso total al sistema',
                'permissions' => json_encode([
                    'products.*',
                    'orders.*',
                    'customers.*',
                    'inventory.*',
                    'reports.*',
                    'settings.*',
                    'users.*',
                    'categories.*',
                    'coupons.*',
                    'shipping.*',
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Administrador',
                'slug'        => 'admin',
                'description' => 'Administrador de la tienda',
                'permissions' => json_encode([
                    'products.*',
                    'orders.*',
                    'customers.view',
                    'customers.edit',
                    'inventory.*',
                    'reports.view',
                    'categories.*',
                    'coupons.*',
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Vendedor',
                'slug'        => 'seller',
                'description' => 'Personal de ventas',
                'permissions' => json_encode([
                    'orders.view',
                    'orders.edit',
                    'orders.create',
                    'products.view',
                    'customers.view',
                    'customers.create',
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Bodeguero',
                'slug'        => 'warehouse',
                'description' => 'Personal de bodega e inventario',
                'permissions' => json_encode([
                    'inventory.*',
                    'orders.view',
                    'products.view',
                    'shipments.*',
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Cliente',
                'slug'        => 'customer',
                'description' => 'Cliente registrado de la tienda',
                'permissions' => json_encode([]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
