<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'store_name', 'value' => 'Mi Tienda Online', 'type' => 'string', 'description' => 'Nombre de la tienda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'general', 'key' => 'store_email', 'value' => 'contacto@tienda.com', 'type' => 'string', 'description' => 'Email de contacto', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'general', 'key' => 'store_phone', 'value' => '+57 300 123 4567', 'type' => 'string', 'description' => 'Teléfono de contacto', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'general', 'key' => 'store_address', 'value' => 'Calle 123 #45-67, Bogotá, Colombia', 'type' => 'string', 'description' => 'Dirección física', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'general', 'key' => 'store_logo', 'value' => '', 'type' => 'string', 'description' => 'Logo de la tienda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'general', 'key' => 'store_favicon', 'value' => '', 'type' => 'string', 'description' => 'Favicon de la tienda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],

            // Currency
            ['group' => 'currency', 'key' => 'default_currency', 'value' => 'COP', 'type' => 'string', 'description' => 'Moneda predeterminada', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'currency', 'key' => 'currency_symbol', 'value' => '$', 'type' => 'string', 'description' => 'Símbolo de moneda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'currency', 'key' => 'currency_position', 'value' => 'before', 'type' => 'string', 'description' => 'Posición del símbolo', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'currency', 'key' => 'thousand_separator', 'value' => '.', 'type' => 'string', 'description' => 'Separador de miles', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'currency', 'key' => 'decimal_separator', 'value' => ',', 'type' => 'string', 'description' => 'Separador decimal', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'currency', 'key' => 'decimal_places', 'value' => '0', 'type' => 'integer', 'description' => 'Decimales a mostrar', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],

            // Tax
            ['group' => 'tax', 'key' => 'tax_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Habilitar impuestos', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'tax', 'key' => 'tax_rate', 'value' => '19', 'type' => 'integer', 'description' => 'Tasa de IVA (%)', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'tax', 'key' => 'prices_include_tax', 'value' => '1', 'type' => 'boolean', 'description' => 'Los precios incluyen IVA', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],

            // Inventory
            ['group' => 'inventory', 'key' => 'low_stock_threshold', 'value' => '5', 'type' => 'integer', 'description' => 'Umbral de stock bajo', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'inventory', 'key' => 'allow_backorders', 'value' => '0', 'type' => 'boolean', 'description' => 'Permitir pedidos sin stock', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'inventory', 'key' => 'hide_out_of_stock', 'value' => '0', 'type' => 'boolean', 'description' => 'Ocultar productos sin stock', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Cart
            ['group' => 'cart', 'key' => 'cart_expiry_hours', 'value' => '72', 'type' => 'integer', 'description' => 'Horas antes de expirar carrito', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'cart', 'key' => 'min_order_amount', 'value' => '30000', 'type' => 'integer', 'description' => 'Monto mínimo de pedido', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'cart', 'key' => 'max_cart_items', 'value' => '50', 'type' => 'integer', 'description' => 'Máximo items en carrito', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Payment
            ['group' => 'payment', 'key' => 'active_gateways', 'value' => json_encode(['payu', 'mercadopago']), 'type' => 'json', 'description' => 'Pasarelas de pago activas', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'payu_merchant_id', 'value' => '', 'type' => 'string', 'description' => 'PayU Merchant ID', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'payu_api_key', 'value' => '', 'type' => 'string', 'description' => 'PayU API Key', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'payu_api_login', 'value' => '', 'type' => 'string', 'description' => 'PayU API Login', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'payu_account_id', 'value' => '', 'type' => 'string', 'description' => 'PayU Account ID', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'payu_sandbox', 'value' => '1', 'type' => 'boolean', 'description' => 'PayU modo sandbox', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'mercadopago_public_key', 'value' => '', 'type' => 'string', 'description' => 'MercadoPago Public Key', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'mercadopago_access_token', 'value' => '', 'type' => 'string', 'description' => 'MercadoPago Access Token', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'payment', 'key' => 'mercadopago_sandbox', 'value' => '1', 'type' => 'boolean', 'description' => 'MercadoPago modo sandbox', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Email
            ['group' => 'email', 'key' => 'from_email', 'value' => 'noreply@tienda.com', 'type' => 'string', 'description' => 'Email remitente', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'email', 'key' => 'from_name', 'value' => 'Mi Tienda Online', 'type' => 'string', 'description' => 'Nombre remitente', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'email', 'key' => 'admin_email', 'value' => 'admin@tienda.com', 'type' => 'string', 'description' => 'Email para notificaciones', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Social Media
            ['group' => 'social', 'key' => 'facebook_url', 'value' => '', 'type' => 'string', 'description' => 'URL de Facebook', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'social', 'key' => 'instagram_url', 'value' => '', 'type' => 'string', 'description' => 'URL de Instagram', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'social', 'key' => 'twitter_url', 'value' => '', 'type' => 'string', 'description' => 'URL de Twitter', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['group' => 'social', 'key' => 'whatsapp_number', 'value' => '', 'type' => 'string', 'description' => 'Número de WhatsApp', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('settings')->insertBatch($settings);
    }
}
