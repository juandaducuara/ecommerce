<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table            = 'stock_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    // Tipos de movimiento
    const TYPE_PURCHASE    = 'purchase';
    const TYPE_SALE        = 'sale';
    const TYPE_RETURN      = 'return';
    const TYPE_ADJUSTMENT  = 'adjustment';
    const TYPE_RESERVATION = 'reservation';
    const TYPE_RELEASE     = 'release';

    // Registrar movimiento
    public function record(
        int $productId,
        string $type,
        int $quantity,
        ?int $userId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): bool {
        $stock = (new StockModel())->getByProduct($productId);
        $quantityBefore = $stock ? $stock->quantity : 0;

        // Calcular cantidad después según el tipo
        $quantityAfter = match ($type) {
            self::TYPE_PURCHASE, self::TYPE_RETURN => $quantityBefore + abs($quantity),
            self::TYPE_SALE => $quantityBefore - abs($quantity),
            self::TYPE_ADJUSTMENT => $quantityBefore + $quantity,
            default => $quantityBefore,
        };

        return $this->insert([
            'product_id'      => $productId,
            'user_id'         => $userId,
            'type'            => $type,
            'quantity'        => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after'  => $quantityAfter,
            'reference_type'  => $referenceType,
            'reference_id'    => $referenceId,
            'notes'           => $notes,
        ]);
    }

    // Obtener historial de un producto
    public function getByProduct(int $productId, int $limit = 50)
    {
        return $this->where('product_id', $productId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    // Obtener movimientos por referencia
    public function getByReference(string $referenceType, int $referenceId)
    {
        return $this->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->findAll();
    }
}
