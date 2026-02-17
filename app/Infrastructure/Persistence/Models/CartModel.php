<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'carts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'session_id',
        'coupon_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'items_count',
        'notes',
        'expires_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Obtener o crear carrito para usuario
    public function getOrCreateForUser(int $userId)
    {
        $cart = $this->where('user_id', $userId)->first();

        if (!$cart) {
            $cartId = $this->insert([
                'user_id'    => $userId,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+72 hours')),
            ]);
            $cart = $this->find($cartId);
        }

        return $cart;
    }

    // Obtener o crear carrito para sesión
    public function getOrCreateForSession(string $sessionId)
    {
        $cart = $this->where('session_id', $sessionId)->first();

        if (!$cart) {
            $cartId = $this->insert([
                'session_id' => $sessionId,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+72 hours')),
            ]);
            $cart = $this->find($cartId);
        }

        return $cart;
    }

    // Obtener carrito con items
    public function getWithItems(int $cartId)
    {
        $cart = $this->find($cartId);

        if ($cart) {
            $cart->items = (new CartItemModel())
                ->where('cart_id', $cartId)
                ->findAll();
        }

        return $cart;
    }

    // Recalcular totales
    public function recalculateTotals(int $cartId): bool
    {
        $items = (new CartItemModel())->where('cart_id', $cartId)->findAll();

        $subtotal = 0;
        $itemsCount = 0;

        foreach ($items as $item) {
            $subtotal += $item->total_price;
            $itemsCount += $item->quantity;
        }

        // Calcular IVA (19%)
        $tax = $subtotal * 0.19 / 1.19; // Si los precios incluyen IVA
        $total = $subtotal;

        return $this->update($cartId, [
            'subtotal'    => $subtotal,
            'tax'         => $tax,
            'total'       => $total,
            'items_count' => $itemsCount,
        ]);
    }

    // Limpiar carrito
    public function clear(int $cartId): bool
    {
        // Eliminar items
        (new CartItemModel())->where('cart_id', $cartId)->delete();

        // Resetear totales
        return $this->update($cartId, [
            'coupon_id'   => null,
            'subtotal'    => 0,
            'discount'    => 0,
            'tax'         => 0,
            'total'       => 0,
            'items_count' => 0,
        ]);
    }

    // Transferir carrito de sesión a usuario
    public function transferToUser(string $sessionId, int $userId): bool
    {
        $sessionCart = $this->where('session_id', $sessionId)->first();
        $userCart = $this->where('user_id', $userId)->first();

        if (!$sessionCart) {
            return false;
        }

        if ($userCart) {
            // Transferir items del carrito de sesión al carrito del usuario
            $cartItemModel = new CartItemModel();
            $sessionItems = $cartItemModel->where('cart_id', $sessionCart->id)->findAll();

            foreach ($sessionItems as $item) {
                $existingItem = $cartItemModel
                    ->where('cart_id', $userCart->id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($existingItem) {
                    // Actualizar cantidad
                    $cartItemModel->update($existingItem->id, [
                        'quantity'    => $existingItem->quantity + $item->quantity,
                        'total_price' => ($existingItem->quantity + $item->quantity) * $existingItem->unit_price,
                    ]);
                } else {
                    // Mover item
                    $cartItemModel->update($item->id, ['cart_id' => $userCart->id]);
                }
            }

            // Eliminar carrito de sesión
            $this->delete($sessionCart->id);

            // Recalcular totales
            $this->recalculateTotals($userCart->id);

            return true;
        } else {
            // Asignar carrito de sesión al usuario
            return $this->update($sessionCart->id, [
                'user_id'    => $userId,
                'session_id' => null,
            ]);
        }
    }

    // Limpiar carritos expirados
    public function cleanExpired(): int
    {
        $expired = $this->where('expires_at <', date('Y-m-d H:i:s'))->findAll();

        foreach ($expired as $cart) {
            (new CartItemModel())->where('cart_id', $cart->id)->delete();
            $this->delete($cart->id);
        }

        return count($expired);
    }
}
