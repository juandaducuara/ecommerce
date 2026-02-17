<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table            = 'product_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'product_id',
        'path',
        'alt_text',
        'position',
        'is_primary',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    // Obtener imágenes de un producto
    public function getByProduct(int $productId)
    {
        return $this->where('product_id', $productId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    // Obtener imagen principal
    public function getPrimary(int $productId)
    {
        return $this->where('product_id', $productId)
            ->where('is_primary', 1)
            ->first();
    }

    // Establecer como principal
    public function setPrimary(int $imageId, int $productId): bool
    {
        // Quitar principal de todas las imágenes del producto
        $this->where('product_id', $productId)
            ->set('is_primary', 0)
            ->update();

        // Establecer la nueva imagen como principal
        return $this->update($imageId, ['is_primary' => 1]);
    }

    // Reordenar imágenes
    public function reorder(int $productId, array $imageIds): bool
    {
        foreach ($imageIds as $position => $imageId) {
            $this->update($imageId, ['position' => $position]);
        }
        return true;
    }
}
