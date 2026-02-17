<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'total_price',
        'tax',
        'discount',
        'product_data',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    // Obtener items de una orden
    public function getByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    // Crear items desde carrito
    public function createFromCart(int $orderId, array $cartItems): bool
    {
        $rows = [];

        foreach ($cartItems as $item) {
            $productData = is_string($item->product_data)
                ? json_decode($item->product_data, true)
                : (array) $item->product_data;

            $rows[] = [
                'order_id'     => (int) $orderId,
                'product_id'   => (int) $item->product_id,
                'sku'          => (string) ($productData['sku'] ?? ''),
                'name'         => (string) ($productData['name'] ?? ''),
                'quantity'     => (int) $item->quantity,
                'unit_price'   => (float) $item->unit_price,
                'total_price'  => (float) $item->total_price,
                'tax'          => 0.00,
                'discount'     => 0.00,
                'product_data' => is_string($item->product_data)
                    ? $item->product_data
                    : json_encode($item->product_data),
            ];
        }

        return $this->insertBatch($rows) !== false;
    }
}
