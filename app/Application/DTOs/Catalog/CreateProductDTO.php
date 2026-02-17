<?php

namespace App\Application\DTOs\Catalog;

class CreateProductDTO
{
    public function __construct(
        public readonly int $categoryId,
        public readonly string $sku,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $shortDescription,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?float $comparePrice,
        public readonly ?float $cost,
        public readonly ?float $weight,
        public readonly ?float $width,
        public readonly ?float $height,
        public readonly ?float $length,
        public readonly ?string $metaTitle,
        public readonly ?string $metaDescription,
        public readonly bool $isActive,
        public readonly bool $isFeatured,
        public readonly bool $requiresShipping,
        public readonly bool $isTaxable,
        public readonly int $initialStock,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            categoryId: (int)$data['category_id'],
            sku: $data['sku'],
            name: $data['name'],
            slug: $data['slug'] ?? self::generateSlug($data['name']),
            shortDescription: $data['short_description'] ?? null,
            description: $data['description'] ?? null,
            price: (float)$data['price'],
            comparePrice: isset($data['compare_price']) ? (float)$data['compare_price'] : null,
            cost: isset($data['cost']) ? (float)$data['cost'] : null,
            weight: isset($data['weight']) ? (float)$data['weight'] : null,
            width: isset($data['width']) ? (float)$data['width'] : null,
            height: isset($data['height']) ? (float)$data['height'] : null,
            length: isset($data['length']) ? (float)$data['length'] : null,
            metaTitle: $data['meta_title'] ?? null,
            metaDescription: $data['meta_description'] ?? null,
            isActive: (bool)($data['is_active'] ?? true),
            isFeatured: (bool)($data['is_featured'] ?? false),
            requiresShipping: (bool)($data['requires_shipping'] ?? true),
            isTaxable: (bool)($data['is_taxable'] ?? true),
            initialStock: (int)($data['initial_stock'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'category_id'       => $this->categoryId,
            'sku'               => $this->sku,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'short_description' => $this->shortDescription,
            'description'       => $this->description,
            'price'             => $this->price,
            'compare_price'     => $this->comparePrice,
            'cost'              => $this->cost,
            'weight'            => $this->weight,
            'width'             => $this->width,
            'height'            => $this->height,
            'length'            => $this->length,
            'meta_title'        => $this->metaTitle,
            'meta_description'  => $this->metaDescription,
            'is_active'         => $this->isActive,
            'is_featured'       => $this->isFeatured,
            'requires_shipping' => $this->requiresShipping,
            'is_taxable'        => $this->isTaxable,
        ];
    }

    private static function generateSlug(string $name): string
    {
        $slug = mb_strtolower($name);
        $slug = preg_replace('/[áàäâ]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöô]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/ñ/u', 'n', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
