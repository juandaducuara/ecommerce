<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Categorías principales
        $categories = [
            [
                'parent_id'        => null,
                'name'             => 'Electrónica',
                'slug'             => 'electronica',
                'description'      => 'Dispositivos electrónicos y accesorios',
                'icon'             => 'bi-cpu',
                'meta_title'       => 'Electrónica - Tienda Online',
                'meta_description' => 'Encuentra los mejores dispositivos electrónicos',
                'position'         => 1,
                'is_active'        => 1,
                'is_featured'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'        => null,
                'name'             => 'Ropa y Moda',
                'slug'             => 'ropa-moda',
                'description'      => 'Ropa, calzado y accesorios de moda',
                'icon'             => 'bi-bag',
                'meta_title'       => 'Ropa y Moda - Tienda Online',
                'meta_description' => 'Las mejores tendencias en moda',
                'position'         => 2,
                'is_active'        => 1,
                'is_featured'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'        => null,
                'name'             => 'Hogar y Cocina',
                'slug'             => 'hogar-cocina',
                'description'      => 'Artículos para el hogar y la cocina',
                'icon'             => 'bi-house',
                'meta_title'       => 'Hogar y Cocina - Tienda Online',
                'meta_description' => 'Todo para tu hogar',
                'position'         => 3,
                'is_active'        => 1,
                'is_featured'      => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'        => null,
                'name'             => 'Deportes',
                'slug'             => 'deportes',
                'description'      => 'Artículos deportivos y fitness',
                'icon'             => 'bi-dribbble',
                'meta_title'       => 'Deportes - Tienda Online',
                'meta_description' => 'Equipamiento deportivo de calidad',
                'position'         => 4,
                'is_active'        => 1,
                'is_featured'      => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'        => null,
                'name'             => 'Belleza',
                'slug'             => 'belleza',
                'description'      => 'Productos de belleza y cuidado personal',
                'icon'             => 'bi-heart',
                'meta_title'       => 'Belleza - Tienda Online',
                'meta_description' => 'Productos de belleza y cuidado',
                'position'         => 5,
                'is_active'        => 1,
                'is_featured'      => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($categories);

        // Subcategorías de Electrónica
        $electronicaId = $this->db->table('categories')
            ->where('slug', 'electronica')
            ->get()
            ->getRow()
            ->id;

        $subcategoriesElectronica = [
            [
                'parent_id'   => $electronicaId,
                'name'        => 'Smartphones',
                'slug'        => 'smartphones',
                'description' => 'Teléfonos inteligentes de todas las marcas',
                'icon'        => 'bi-phone',
                'position'    => 1,
                'is_active'   => 1,
                'is_featured' => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'   => $electronicaId,
                'name'        => 'Laptops',
                'slug'        => 'laptops',
                'description' => 'Computadoras portátiles',
                'icon'        => 'bi-laptop',
                'position'    => 2,
                'is_active'   => 1,
                'is_featured' => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'   => $electronicaId,
                'name'        => 'Tablets',
                'slug'        => 'tablets',
                'description' => 'Tabletas electrónicas',
                'icon'        => 'bi-tablet',
                'position'    => 3,
                'is_active'   => 1,
                'is_featured' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'   => $electronicaId,
                'name'        => 'Accesorios',
                'slug'        => 'accesorios-electronicos',
                'description' => 'Accesorios para dispositivos electrónicos',
                'icon'        => 'bi-headphones',
                'position'    => 4,
                'is_active'   => 1,
                'is_featured' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($subcategoriesElectronica);

        // Subcategorías de Ropa
        $ropaId = $this->db->table('categories')
            ->where('slug', 'ropa-moda')
            ->get()
            ->getRow()
            ->id;

        $subcategoriesRopa = [
            [
                'parent_id'   => $ropaId,
                'name'        => 'Hombre',
                'slug'        => 'ropa-hombre',
                'description' => 'Ropa para hombre',
                'icon'        => 'bi-person',
                'position'    => 1,
                'is_active'   => 1,
                'is_featured' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'   => $ropaId,
                'name'        => 'Mujer',
                'slug'        => 'ropa-mujer',
                'description' => 'Ropa para mujer',
                'icon'        => 'bi-person',
                'position'    => 2,
                'is_active'   => 1,
                'is_featured' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'   => $ropaId,
                'name'        => 'Calzado',
                'slug'        => 'calzado',
                'description' => 'Zapatos y calzado deportivo',
                'icon'        => 'bi-box',
                'position'    => 3,
                'is_active'   => 1,
                'is_featured' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($subcategoriesRopa);
    }
}
