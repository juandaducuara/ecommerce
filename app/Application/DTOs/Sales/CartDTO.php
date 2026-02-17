<?php

namespace App\Application\DTOs\Sales;

class CartDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $userId,
        public readonly ?string $sessionId,
        public readonly array $items,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $tax,
        public readonly float $total,
        public readonly int $itemsCount,
        public readonly ?string $couponCode,
    ) {}

    public static function fromModel(object $cart, array $items = []): self
    {
        $cartItems = array_map(
            fn($item) => CartItemDTO::fromModel($item),
            $items
        );

        return new self(
            id: $cart->id,
            userId: $cart->user_id,
            sessionId: $cart->session_id,
            items: $cartItems,
            subtotal: (float)$cart->subtotal,
            discount: (float)$cart->discount,
            tax: (float)$cart->tax,
            total: (float)$cart->total,
            itemsCount: (int)$cart->items_count,
            couponCode: null, // TODO: Obtener código de cupón si aplica
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->userId,
            'items'       => array_map(fn($item) => $item->toArray(), $this->items),
            'subtotal'    => $this->subtotal,
            'discount'    => $this->discount,
            'tax'         => $this->tax,
            'total'       => $this->total,
            'items_count' => $this->itemsCount,
            'coupon_code' => $this->couponCode,
        ];
    }

    public function isEmpty(): bool
    {
        return $this->itemsCount === 0;
    }

    public function hasItem(int $productId): bool
    {
        foreach ($this->items as $item) {
            if ($item->productId === $productId) {
                return true;
            }
        }
        return false;
    }

    public function getItem(int $productId): ?CartItemDTO
    {
        foreach ($this->items as $item) {
            if ($item->productId === $productId) {
                return $item;
            }
        }
        return null;
    }
}
