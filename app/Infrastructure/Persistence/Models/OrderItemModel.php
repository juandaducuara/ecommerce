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
        foreach ($cartItems as $item) {
            $productData = is_string($item->product_data)
                ? json_decode($item->product_data, true)
                : $item->product_data;

            $this->insert([
                'order_id'     => $orderId,
                'product_id'   => $item->product_id,
                'sku'          => $productData['sku'] ?? '',
                'name'         => $productData['name'] ?? '',
                'quantity'     => $item->quantity,
                'unit_price'   => $item->unit_price,
                'total_price'  => $item->total_price,
                'product_data' => is_string($item->product_data)
                    ? $item->product_data
                    : json_encode($item->product_data),
            ]);
        }

        return true;
    }
}
