<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run()
    {
        // Zonas de envío para Colombia
        $zones = [
            [
                'name'       => 'Principales Ciudades',
                'regions'    => json_encode([
                    'Bogotá D.C.',
                    'Medellín',
                    'Cali',
                    'Barranquilla',
                    'Cartagena',
                    'Bucaramanga',
                ]),
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Zona Centro',
                'regions'    => json_encode([
                    'Cundinamarca',
                    'Boyacá',
                    'Tolima',
                    'Huila',
                    'Meta',
                ]),
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Costa Caribe',
                'regions'    => json_encode([
                    'Atlántico',
                    'Bolívar',
                    'Cesar',
                    'Córdoba',
                    'La Guajira',
                    'Magdalena',
                    'Sucre',
                ]),
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Zona Pacífico',
                'regions'    => json_encode([
                    'Valle del Cauca',
                    'Cauca',
                    'Nariño',
                    'Chocó',
                ]),
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Zonas Especiales',
                'regions'    => json_encode([
                    'Amazonas',
                    'Arauca',
                    'Caquetá',
                    'Casanare',
                    'Guainía',
                    'Guaviare',
                    'Putumayo',
                    'San Andrés',
                    'Vaupés',
                    'Vichada',
                ]),
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('shipping_zones')->insertBatch($zones);

        // Obtener IDs de zonas
        $zone1Id = $this->db->table('shipping_zones')
            ->where('name', 'Principales Ciudades')
            ->get()
            ->getRow()
            ->id;

        $zone2Id = $this->db->table('shipping_zones')
            ->where('name', 'Zona Centro')
            ->get()
            ->getRow()
            ->id;

        $zone3Id = $this->db->table('shipping_zones')
            ->where('name', 'Costa Caribe')
            ->get()
            ->getRow()
            ->id;

        $zone4Id = $this->db->table('shipping_zones')
            ->where('name', 'Zona Pacífico')
            ->get()
            ->getRow()
            ->id;

        $zone5Id = $this->db->table('shipping_zones')
            ->where('name', 'Zonas Especiales')
            ->get()
            ->getRow()
            ->id;

        // Tarifas de envío
        $rates = [
            // Principales Ciudades
            [
                'zone_id'            => $zone1Id,
                'name'               => 'Envío Estándar',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 0,
                'max_order_amount'   => null,
                'price'              => 12000,
                'estimated_days_min' => 2,
                'estimated_days_max' => 4,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'zone_id'            => $zone1Id,
                'name'               => 'Envío Express',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 0,
                'max_order_amount'   => null,
                'price'              => 25000,
                'estimated_days_min' => 1,
                'estimated_days_max' => 2,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'zone_id'            => $zone1Id,
                'name'               => 'Envío Gratis',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 150000,
                'max_order_amount'   => null,
                'price'              => 0,
                'estimated_days_min' => 2,
                'estimated_days_max' => 4,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            // Zona Centro
            [
                'zone_id'            => $zone2Id,
                'name'               => 'Envío Estándar',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 0,
                'max_order_amount'   => null,
                'price'              => 18000,
                'estimated_days_min' => 3,
                'estimated_days_max' => 5,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            // Costa Caribe
            [
                'zone_id'            => $zone3Id,
                'name'               => 'Envío Estándar',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 0,
                'max_order_amount'   => null,
                'price'              => 22000,
                'estimated_days_min' => 4,
                'estimated_days_max' => 6,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            // Zona Pacífico
            [
                'zone_id'            => $zone4Id,
                'name'               => 'Envío Estándar',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 0,
                'max_order_amount'   => null,
                'price'              => 20000,
                'estimated_days_min' => 3,
                'estimated_days_max' => 5,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            // Zonas Especiales
            [
                'zone_id'            => $zone5Id,
                'name'               => 'Envío Especial',
                'min_weight'         => 0,
                'max_weight'         => 5000,
                'min_order_amount'   => 0,
                'max_order_amount'   => null,
                'price'              => 45000,
                'estimated_days_min' => 7,
                'estimated_days_max' => 15,
                'is_active'          => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('shipping_rates')->insertBatch($rates);
    }
}
