<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Obtener categorías
        $smartphonesId = $this->db->table('categories')->where('slug', 'smartphones')->get()->getRow()->id;
        $laptopsId = $this->db->table('categories')->where('slug', 'laptops')->get()->getRow()->id;
        $tabletsId = $this->db->table('categories')->where('slug', 'tablets')->get()->getRow()->id;

        $products = [
            // Smartphones
            [
                'category_id'       => $smartphonesId,
                'sku'               => 'PHONE-001',
                'name'              => 'iPhone 15 Pro Max 256GB',
                'slug'              => 'iphone-15-pro-max-256gb',
                'short_description' => 'El iPhone más avanzado con chip A17 Pro y sistema de cámara revolucionario',
                'description'       => 'El iPhone 15 Pro Max presenta un diseño de titanio aeroespacial, el chip A17 Pro más potente, un sistema de cámara Pro con zoom óptico 5x y el botón de Acción personalizable.',
                'price'             => 5999000,
                'compare_price'     => 6299000,
                'cost'              => 4500000,
                'weight'            => 221,
                'width'             => 7.69,
                'height'            => 15.99,
                'length'            => 0.83,
                'meta_title'        => 'iPhone 15 Pro Max 256GB - Comprar Online',
                'meta_description'  => 'Compra el iPhone 15 Pro Max con envío gratis. El smartphone más avanzado de Apple.',
                'is_active'         => 1,
                'is_featured'       => 1,
                'requires_shipping' => 1,
                'is_taxable'        => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'       => $smartphonesId,
                'sku'               => 'PHONE-002',
                'name'              => 'Samsung Galaxy S24 Ultra 512GB',
                'slug'              => 'samsung-galaxy-s24-ultra-512gb',
                'short_description' => 'El smartphone Galaxy más potente con Galaxy AI integrado',
                'description'       => 'Samsung Galaxy S24 Ultra con pantalla Dynamic AMOLED 2X de 6.8", procesador Snapdragon 8 Gen 3, cámara de 200MP y S Pen incluido.',
                'price'             => 5499000,
                'compare_price'     => 5799000,
                'cost'              => 4000000,
                'weight'            => 232,
                'width'             => 7.9,
                'height'            => 16.26,
                'length'            => 0.86,
                'meta_title'        => 'Samsung Galaxy S24 Ultra 512GB - Comprar Online',
                'meta_description'  => 'Galaxy S24 Ultra con Galaxy AI. El smartphone Android más avanzado.',
                'is_active'         => 1,
                'is_featured'       => 1,
                'requires_shipping' => 1,
                'is_taxable'        => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'       => $smartphonesId,
                'sku'               => 'PHONE-003',
                'name'              => 'Xiaomi 14 Pro 256GB',
                'slug'              => 'xiaomi-14-pro-256gb',
                'short_description' => 'Rendimiento premium con cámara Leica profesional',
                'description'       => 'Xiaomi 14 Pro con procesador Snapdragon 8 Gen 3, pantalla LTPO AMOLED 120Hz, cámara Leica de 50MP y carga rápida de 120W.',
                'price'             => 3299000,
                'compare_price'     => 3599000,
                'cost'              => 2400000,
                'weight'            => 223,
                'width'             => 7.52,
                'height'            => 16.06,
                'length'            => 0.84,
                'meta_title'        => 'Xiaomi 14 Pro 256GB - Comprar Online',
                'meta_description'  => 'Xiaomi 14 Pro con cámara Leica. Rendimiento flagship a precio accesible.',
                'is_active'         => 1,
                'is_featured'       => 0,
                'requires_shipping' => 1,
                'is_taxable'        => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            // Laptops
            [
                'category_id'       => $laptopsId,
                'sku'               => 'LAPTOP-001',
                'name'              => 'MacBook Pro 14" M3 Pro 512GB',
                'slug'              => 'macbook-pro-14-m3-pro-512gb',
                'short_description' => 'Potencia profesional con el chip M3 Pro de Apple',
                'description'       => 'MacBook Pro de 14 pulgadas con chip M3 Pro, pantalla Liquid Retina XDR, 18GB de memoria unificada y hasta 17 horas de batería.',
                'price'             => 8999000,
                'compare_price'     => 9499000,
                'cost'              => 7000000,
                'weight'            => 1610,
                'width'             => 31.26,
                'height'            => 22.12,
                'length'            => 1.55,
                'meta_title'        => 'MacBook Pro 14" M3 Pro - Comprar Online',
                'meta_description'  => 'MacBook Pro con chip M3 Pro. El portátil profesional más potente de Apple.',
                'is_active'         => 1,
                'is_featured'       => 1,
                'requires_shipping' => 1,
                'is_taxable'        => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'       => $laptopsId,
                'sku'               => 'LAPTOP-002',
                'name'              => 'ASUS ROG Zephyrus G14 RTX 4070',
                'slug'              => 'asus-rog-zephyrus-g14-rtx-4070',
                'short_description' => 'Laptop gaming ultraportátil con AMD Ryzen 9',
                'description'       => 'ASUS ROG Zephyrus G14 con AMD Ryzen 9 8945HS, NVIDIA RTX 4070, pantalla ROG Nebula de 14" 165Hz y 32GB RAM.',
                'price'             => 7499000,
                'compare_price'     => 7999000,
                'cost'              => 5800000,
                'weight'            => 1650,
                'width'             => 31.2,
                'height'            => 22.0,
                'length'            => 1.63,
                'meta_title'        => 'ASUS ROG Zephyrus G14 RTX 4070 - Comprar Online',
                'meta_description'  => 'Laptop gaming ASUS ROG Zephyrus G14. Potencia en formato compacto.',
                'is_active'         => 1,
                'is_featured'       => 1,
                'requires_shipping' => 1,
                'is_taxable'        => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            // Tablets
            [
                'category_id'       => $tabletsId,
                'sku'               => 'TABLET-001',
                'name'              => 'iPad Pro 12.9" M2 256GB WiFi',
                'slug'              => 'ipad-pro-129-m2-256gb-wifi',
                'short_description' => 'La tablet más potente con chip M2 de Apple',
                'description'       => 'iPad Pro de 12.9 pulgadas con chip M2, pantalla Liquid Retina XDR, Face ID y compatibilidad con Apple Pencil 2.',
                'price'             => 5299000,
                'compare_price'     => 5599000,
                'cost'              => 4000000,
                'weight'            => 682,
                'width'             => 21.49,
                'height'            => 28.06,
                'length'            => 0.64,
                'meta_title'        => 'iPad Pro 12.9" M2 256GB - Comprar Online',
                'meta_description'  => 'iPad Pro con chip M2. La tablet más avanzada del mercado.',
                'is_active'         => 1,
                'is_featured'       => 1,
                'requires_shipping' => 1,
                'is_taxable'        => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('products')->insertBatch($products);

        // Crear stock para cada producto
        $allProducts = $this->db->table('products')->get()->getResult();

        foreach ($allProducts as $product) {
            $this->db->table('stock')->insert([
                'product_id'          => $product->id,
                'quantity'            => rand(10, 50),
                'reserved'            => 0,
                'low_stock_threshold' => 5,
                'allow_backorder'     => 0,
                'track_inventory'     => 1,
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
