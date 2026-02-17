<?php

namespace App\Application\DTOs\Catalog;

class ProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $categoryId,
        public readonly string $sku,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $shortDescription,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?float $comparePrice,
        public readonly ?string $primaryImage,
        public readonly array $images,
        public readonly int $stockQuantity,
        public readonly bool $isActive,
        public readonly bool $isFeatured,
        public readonly ?string $categoryName,
    ) {}

    public static function fromModel(object $product, ?object $stock = null, ?array $images = null): self
    {
        return new self(
            id: $product->id,
            categoryId: $product->category_id,
            sku: $product->sku,
            name: $product->name,
            slug: $product->slug,
            shortDescription: $product->short_description,
            description: $product->description,
            price: (float)$product->price,
            comparePrice: $product->compare_price ? (float)$product->compare_price : null,
            primaryImage: $images[0]->path ?? null,
            images: $images ? array_map(fn($img) => $img->path, $images) : [],
            stockQuantity: $stock ? $stock->quantity - $stock->reserved : 0,
            isActive: (bool)$product->is_active,
            isFeatured: (bool)$product->is_featured,
            categoryName: $product->category_name ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'category_id'       => $this->categoryId,
            'sku'               => $this->sku,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'short_description' => $this->shortDescription,
            'description'       => $this->description,
            'price'             => $this->price,
            'compare_price'     => $this->comparePrice,
            'primary_image'     => $this->primaryImage,
            'images'            => $this->images,
            'stock_quantity'    => $this->stockQuantity,
            'is_active'         => $this->isActive,
            'is_featured'       => $this->isFeatured,
            'category_name'     => $this->categoryName,
        ];
    }

    public function hasDiscount(): bool
    {
        return $this->comparePrice !== null && $this->comparePrice > $this->price;
    }

    public function getDiscountPercentage(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return (int)round(100 - ($this->price / $this->comparePrice * 100));
    }

    public function isInStock(): bool
    {
        return $this->stockQuantity > 0;
    }
}
