<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\ProductModel;
use App\Infrastructure\Persistence\Models\CategoryModel;
use App\Infrastructure\Persistence\Models\StockModel;
use App\Infrastructure\Persistence\Models\ProductImageModel;

class CatalogController extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    protected StockModel $stockModel;
    protected ProductImageModel $imageModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->stockModel = new StockModel();
        $this->imageModel = new ProductImageModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $sort = $this->request->getGet('sort') ?: 'newest';
        $minPrice = $this->request->getGet('min_price');
        $maxPrice = $this->request->getGet('max_price');

        $builder = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.is_active', 1);

        if ($search) {
            $builder->groupStart()
                ->like('products.name', $search)
                ->orLike('products.short_description', $search)
                ->orLike('products.sku', $search)
                ->groupEnd();
        }

        if ($categoryId) {
            $builder->where('products.category_id', $categoryId);
        }

        if ($minPrice) {
            $builder->where('products.price >=', $minPrice);
        }

        if ($maxPrice) {
            $builder->where('products.price <=', $maxPrice);
        }

        switch ($sort) {
            case 'price_asc':
                $builder->orderBy('products.price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('products.price', 'DESC');
                break;
            case 'popular':
                $builder->orderBy('products.sales_count', 'DESC');
                break;
            case 'name':
                $builder->orderBy('products.name', 'ASC');
                break;
            default: // newest
                $builder->orderBy('products.created_at', 'DESC');
        }

        $products = $builder->findAll();

        // Agregar imagen principal a cada producto
        foreach ($products as $product) {
            $product->primary_image = $this->productModel->getPrimaryImage($product->id);
            $stock = $this->stockModel->getByProduct($product->id);
            $product->stock_available = $stock ? max(0, $stock->quantity - $stock->reserved) : 0;
        }

        $categories = $this->categoryModel->getActive();
        $currentCategory = $categoryId ? $this->categoryModel->find($categoryId) : null;

        return view('web/products/index', [
            'title'           => $currentCategory ? $currentCategory->name : ($search ? "Resultados: {$search}" : 'Todos los productos'),
            'products'        => $products,
            'categories'      => $categories,
            'currentCategory' => $currentCategory,
            'search'          => $search,
            'sort'            => $sort,
            'minPrice'        => $minPrice,
            'maxPrice'        => $maxPrice,
            'categoryFilter'  => $categoryId,
        ]);
    }

    public function show(string $slug)
    {
        $product = $this->productModel->select('products.*, categories.name as category_name, categories.slug as category_slug')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.slug', $slug)
            ->where('products.is_active', 1)
            ->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Incrementar vistas
        $this->productModel->incrementViews($product->id);

        // Cargar imágenes y stock
        $images = $this->imageModel->getByProduct($product->id);
        $stock = $this->stockModel->getByProduct($product->id);
        $product->stock_available = $stock ? max(0, $stock->quantity - $stock->reserved) : 0;

        // Productos relacionados
        $related = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.category_id', $product->category_id)
            ->where('products.id !=', $product->id)
            ->where('products.is_active', 1)
            ->orderBy('RAND()')
            ->findAll(4);

        foreach ($related as $rel) {
            $rel->primary_image = $this->productModel->getPrimaryImage($rel->id);
            $relStock = $this->stockModel->getByProduct($rel->id);
            $rel->stock_available = $relStock ? max(0, $relStock->quantity - $relStock->reserved) : 0;
        }

        // Breadcrumb
        $breadcrumb = $this->categoryModel->getBreadcrumb($product->category_id);

        return view('web/products/show', [
            'title'      => $product->name,
            'product'    => $product,
            'images'     => $images,
            'related'    => $related,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public function category(string $slug)
    {
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Redirigir al listado con filtro de categoría
        return redirect()->to('/products?category=' . $category->id);
    }
}
