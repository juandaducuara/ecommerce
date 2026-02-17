<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table            = 'stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'product_id',
        'quantity',
        'reserved',
        'low_stock_threshold',
        'allow_backorder',
        'track_inventory',
    ];

    protected $useTimestamps = true;
    protected $createdField  = false;
    protected $updatedField  = 'updated_at';

    // Obtener stock de un producto
    public function getByProduct(int $productId)
    {
        return $this->where('product_id', $productId)->first();
    }

    // Obtener cantidad disponible
    public function getAvailable(int $productId): int
    {
        $stock = $this->getByProduct($productId);

        if (!$stock || !$stock->track_inventory) {
            return PHP_INT_MAX; // Sin tracking = siempre disponible
        }

        return max(0, $stock->quantity - $stock->reserved);
    }

    // Verificar si hay stock disponible
    public function isAvailable(int $productId, int $quantity = 1): bool
    {
        $stock = $this->getByProduct($productId);

        if (!$stock) {
            return false;
        }

        if (!$stock->track_inventory) {
            return true;
        }

        if ($stock->allow_backorder) {
            return true;
        }

        return $this->getAvailable($productId) >= $quantity;
    }

    // Reservar stock
    public function reserve(int $productId, int $quantity): bool
    {
        $stock = $this->getByProduct($productId);

        if (!$stock) {
            return false;
        }

        return $this->update($stock->id, [
            'reserved' => $stock->reserved + $quantity,
        ]);
    }

    // Liberar reserva
    public function release(int $productId, int $quantity): bool
    {
        $stock = $this->getByProduct($productId);

        if (!$stock) {
            return false;
        }

        $newReserved = max(0, $stock->reserved - $quantity);

        return $this->update($stock->id, [
            'reserved' => $newReserved,
        ]);
    }

    // Confirmar reserva (convertir a venta)
    public function confirmReservation(int $productId, int $quantity): bool
    {
        $stock = $this->getByProduct($productId);

        if (!$stock) {
            return false;
        }

        return $this->update($stock->id, [
            'quantity' => $stock->quantity - $quantity,
            'reserved' => max(0, $stock->reserved - $quantity),
        ]);
    }

    // Agregar stock
    public function addStock(int $productId, int $quantity): bool
    {
        $stock = $this->getByProduct($productId);

        if (!$stock) {
            return false;
        }

        return $this->update($stock->id, [
            'quantity' => $stock->quantity + $quantity,
        ]);
    }

    // Obtener productos con bajo stock
    public function getLowStock()
    {
        return $this->where('track_inventory', 1)
            ->where('(quantity - reserved) <= low_stock_threshold', null, false)
            ->findAll();
    }

    // Obtener productos sin stock
    public function getOutOfStock()
    {
        return $this->where('track_inventory', 1)
            ->where('allow_backorder', 0)
            ->where('(quantity - reserved) <= 0', null, false)
            ->findAll();
    }
}
