<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General
            ['setting_group' => 'general', 'setting_key' => 'store_name', 'value' => 'Mi Tienda Online', 'type' => 'string', 'description' => 'Nombre de la tienda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'general', 'setting_key' => 'store_email', 'value' => 'contacto@tienda.com', 'type' => 'string', 'description' => 'Email de contacto', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'general', 'setting_key' => 'store_phone', 'value' => '+57 300 123 4567', 'type' => 'string', 'description' => 'Teléfono de contacto', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'general', 'setting_key' => 'store_address', 'value' => 'Calle 123 #45-67, Bogotá, Colombia', 'type' => 'string', 'description' => 'Dirección física', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'general', 'setting_key' => 'store_logo', 'value' => '', 'type' => 'string', 'description' => 'Logo de la tienda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'general', 'setting_key' => 'store_favicon', 'value' => '', 'type' => 'string', 'description' => 'Favicon de la tienda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'general', 'setting_key' => 'brand_primary_color', 'value' => '#4f46e5', 'type' => 'string', 'description' => 'Color principal de marca (hex)', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],

            // Currency
            ['setting_group' => 'currency', 'setting_key' => 'default_currency', 'value' => 'COP', 'type' => 'string', 'description' => 'Moneda predeterminada', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'currency', 'setting_key' => 'currency_symbol', 'value' => '$', 'type' => 'string', 'description' => 'Símbolo de moneda', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'currency', 'setting_key' => 'currency_position', 'value' => 'before', 'type' => 'string', 'description' => 'Posición del símbolo', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'currency', 'setting_key' => 'thousand_separator', 'value' => '.', 'type' => 'string', 'description' => 'Separador de miles', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'currency', 'setting_key' => 'decimal_separator', 'value' => ',', 'type' => 'string', 'description' => 'Separador decimal', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'currency', 'setting_key' => 'decimal_places', 'value' => '0', 'type' => 'integer', 'description' => 'Decimales a mostrar', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],

            // Tax
            ['setting_group' => 'tax', 'setting_key' => 'tax_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Habilitar impuestos', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'tax', 'setting_key' => 'tax_rate', 'value' => '19', 'type' => 'integer', 'description' => 'Tasa de IVA (%)', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'tax', 'setting_key' => 'prices_include_tax', 'value' => '1', 'type' => 'boolean', 'description' => 'Los precios incluyen IVA', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],

            // Inventory
            ['setting_group' => 'inventory', 'setting_key' => 'low_stock_threshold', 'value' => '5', 'type' => 'integer', 'description' => 'Umbral de stock bajo', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'inventory', 'setting_key' => 'allow_backorders', 'value' => '0', 'type' => 'boolean', 'description' => 'Permitir pedidos sin stock', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'inventory', 'setting_key' => 'hide_out_of_stock', 'value' => '0', 'type' => 'boolean', 'description' => 'Ocultar productos sin stock', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Cart
            ['setting_group' => 'cart', 'setting_key' => 'cart_expiry_hours', 'value' => '72', 'type' => 'integer', 'description' => 'Horas antes de expirar carrito', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'cart', 'setting_key' => 'min_order_amount', 'value' => '30000', 'type' => 'integer', 'description' => 'Monto mínimo de pedido', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'cart', 'setting_key' => 'max_cart_items', 'value' => '50', 'type' => 'integer', 'description' => 'Máximo items en carrito', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Payment
            ['setting_group' => 'payment', 'setting_key' => 'active_gateways', 'value' => json_encode(['payu', 'mercadopago']), 'type' => 'json', 'description' => 'Pasarelas de pago activas', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'payu_merchant_id', 'value' => '', 'type' => 'string', 'description' => 'PayU Merchant ID', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'payu_api_key', 'value' => '', 'type' => 'string', 'description' => 'PayU API Key', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'payu_api_login', 'value' => '', 'type' => 'string', 'description' => 'PayU API Login', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'payu_account_id', 'value' => '', 'type' => 'string', 'description' => 'PayU Account ID', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'payu_sandbox', 'value' => '1', 'type' => 'boolean', 'description' => 'PayU modo sandbox', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'mercadopago_public_key', 'value' => '', 'type' => 'string', 'description' => 'MercadoPago Public Key', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'mercadopago_access_token', 'value' => '', 'type' => 'string', 'description' => 'MercadoPago Access Token', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'payment', 'setting_key' => 'mercadopago_sandbox', 'value' => '1', 'type' => 'boolean', 'description' => 'MercadoPago modo sandbox', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Email
            ['setting_group' => 'email', 'setting_key' => 'from_email', 'value' => 'noreply@tienda.com', 'type' => 'string', 'description' => 'Email remitente', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'email', 'setting_key' => 'from_name', 'value' => 'Mi Tienda Online', 'type' => 'string', 'description' => 'Nombre remitente', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'email', 'setting_key' => 'admin_email', 'value' => 'admin@tienda.com', 'type' => 'string', 'description' => 'Email para notificaciones', 'is_public' => 0, 'updated_at' => date('Y-m-d H:i:s')],

            // Social Media
            ['setting_group' => 'social', 'setting_key' => 'facebook_url', 'value' => '', 'type' => 'string', 'description' => 'URL de Facebook', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'social', 'setting_key' => 'instagram_url', 'value' => '', 'type' => 'string', 'description' => 'URL de Instagram', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'social', 'setting_key' => 'twitter_url', 'value' => '', 'type' => 'string', 'description' => 'URL de Twitter', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_group' => 'social', 'setting_key' => 'whatsapp_number', 'value' => '', 'type' => 'string', 'description' => 'Número de WhatsApp', 'is_public' => 1, 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('settings')->ignore(true)->insertBatch($settings);
    }
}
