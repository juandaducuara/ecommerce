<?php

namespace App\Application\DTOs\Sales;

class CartItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $cartId,
        public readonly int $productId,
        public readonly string $sku,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $image,
        public readonly int $quantity,
        public readonly float $unitPrice,
        public readonly float $totalPrice,
    ) {}

    public static function fromModel(object $item): self
    {
        $productData = is_string($item->product_data)
            ? json_decode($item->product_data, true)
            : ($item->product_data ?? []);

        return new self(
            id: $item->id,
            cartId: $item->cart_id,
            productId: $item->product_id,
            sku: $productData['sku'] ?? '',
            name: $productData['name'] ?? '',
            slug: $productData['slug'] ?? '',
            image: $productData['image'] ?? null,
            quantity: (int)$item->quantity,
            unitPrice: (float)$item->unit_price,
            totalPrice: (float)$item->total_price,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'cart_id'     => $this->cartId,
            'product_id'  => $this->productId,
            'sku'         => $this->sku,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'image'       => $this->image,
            'quantity'    => $this->quantity,
            'unit_price'  => $this->unitPrice,
            'total_price' => $this->totalPrice,
        ];
    }
}
