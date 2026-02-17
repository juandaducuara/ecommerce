<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'category_id',
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost',
        'weight',
        'width',
        'height',
        'length',
        'meta_title',
        'meta_description',
        'is_active',
        'is_featured',
        'requires_shipping',
        'is_taxable',
        'tax_class',
        'views_count',
        'sales_count',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'category_id' => 'required|integer|is_not_unique[categories.id]',
        'sku'         => 'required|alpha_dash|is_unique[products.sku,id,{id}]',
        'name'        => 'required|min_length[2]|max_length[255]',
        'slug'        => 'required|is_unique[products.slug,id,{id}]',
        'price'       => 'required|numeric',
    ];

    // Obtener productos activos
    public function getActive(int $limit = 20, int $offset = 0)
    {
        return $this->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    // Obtener productos destacados
    public function getFeatured(int $limit = 8)
    {
        return $this->where('is_active', 1)
            ->where('is_featured', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    // Obtener productos por categoría
    public function getByCategory(int $categoryId, int $limit = 20, int $offset = 0)
    {
        return $this->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    // Buscar por slug
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    // Buscar por SKU
    public function findBySku(string $sku)
    {
        return $this->where('sku', $sku)->first();
    }

    // Búsqueda de productos
    public function search(string $term, int $limit = 20)
    {
        return $this->like('name', $term)
            ->orLike('short_description', $term)
            ->orLike('sku', $term)
            ->where('is_active', 1)
            ->findAll($limit);
    }

    // Obtener productos relacionados
    public function getRelated(int $productId, int $limit = 4)
    {
        $product = $this->find($productId);

        if (!$product) {
            return [];
        }

        return $this->where('category_id', $product->category_id)
            ->where('id !=', $productId)
            ->where('is_active', 1)
            ->orderBy('RAND()')
            ->findAll($limit);
    }

    // Incrementar vistas
    public function incrementViews(int $productId): bool
    {
        return $this->set('views_count', 'views_count + 1', false)
            ->where('id', $productId)
            ->update();
    }

    // Incrementar ventas
    public function incrementSales(int $productId, int $quantity = 1): bool
    {
        return $this->set('sales_count', "sales_count + {$quantity}", false)
            ->where('id', $productId)
            ->update();
    }

    // Obtener con stock
    public function getWithStock(int $productId)
    {
        $product = $this->find($productId);

        if ($product) {
            $stock = (new StockModel())->where('product_id', $productId)->first();
            $product->stock = $stock;
        }

        return $product;
    }

    // Obtener con imágenes
    public function getWithImages(int $productId)
    {
        $product = $this->find($productId);

        if ($product) {
            $product->images = (new ProductImageModel())
                ->where('product_id', $productId)
                ->orderBy('position', 'ASC')
                ->findAll();
        }

        return $product;
    }

    // Obtener imagen principal
    public function getPrimaryImage(int $productId): ?string
    {
        $image = (new ProductImageModel())
            ->where('product_id', $productId)
            ->where('is_primary', 1)
            ->first();

        if (!$image) {
            $image = (new ProductImageModel())
                ->where('product_id', $productId)
                ->orderBy('position', 'ASC')
                ->first();
        }

        return $image ? $image->path : null;
    }

    // Filtrar productos
    public function filter(array $filters, int $limit = 20, int $offset = 0)
    {
        $builder = $this->where('is_active', 1);

        if (!empty($filters['category_id'])) {
            $builder->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['min_price'])) {
            $builder->where('price >=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $builder->where('price <=', $filters['max_price']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('name', $filters['search'])
                ->orLike('sku', $filters['search'])
                ->groupEnd();
        }

        // Ordenamiento
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';

        $allowedOrderBy = ['created_at', 'price', 'name', 'sales_count', 'views_count'];
        if (in_array($orderBy, $allowedOrderBy)) {
            $builder->orderBy($orderBy, $orderDir);
        }

        return [
            'products' => $builder->findAll($limit, $offset),
            'total'    => $builder->countAllResults(false),
        ];
    }
}
