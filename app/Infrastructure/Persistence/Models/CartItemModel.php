<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table            = 'cart_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_data',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Agregar item al carrito
    public function addItem(int $cartId, int $productId, int $quantity = 1): bool
    {
        $product = (new ProductModel())->find($productId);

        if (!$product) {
            return false;
        }

        // Verificar si ya existe el item
        $existingItem = $this->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            // Actualizar cantidad
            $newQuantity = $existingItem->quantity + $quantity;
            return $this->updateQuantity($existingItem->id, $newQuantity);
        }

        // Crear nuevo item
        $productData = [
            'sku'               => $product->sku,
            'name'              => $product->name,
            'slug'              => $product->slug,
            'image'             => (new ProductModel())->getPrimaryImage($productId),
            'requires_shipping' => $product->requires_shipping,
            'weight'            => $product->weight,
        ];

        $result = $this->insert([
            'cart_id'      => $cartId,
            'product_id'   => $productId,
            'quantity'     => $quantity,
            'unit_price'   => $product->price,
            'total_price'  => $product->price * $quantity,
            'product_data' => json_encode($productData),
        ]);

        if ($result) {
            (new CartModel())->recalculateTotals($cartId);
        }

        return (bool)$result;
    }

    // Actualizar cantidad
    public function updateQuantity(int $itemId, int $quantity): bool
    {
        $item = $this->find($itemId);

        if (!$item) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $result = $this->update($itemId, [
            'quantity'    => $quantity,
            'total_price' => $item->unit_price * $quantity,
        ]);

        if ($result) {
            (new CartModel())->recalculateTotals($item->cart_id);
        }

        return $result;
    }

    // Eliminar item
    public function removeItem(int $itemId): bool
    {
        $item = $this->find($itemId);

        if (!$item) {
            return false;
        }

        $cartId = $item->cart_id;
        $result = $this->delete($itemId);

        if ($result) {
            (new CartModel())->recalculateTotals($cartId);
        }

        return $result;
    }

    // Obtener items del carrito con datos del producto
    public function getItemsWithProduct(int $cartId)
    {
        return $this->select('cart_items.*, products.name, products.slug, products.price as current_price')
            ->join('products', 'products.id = cart_items.product_id', 'left')
            ->where('cart_items.cart_id', $cartId)
            ->findAll();
    }
}
