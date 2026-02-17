<?php

namespace App\Controllers;

use App\Infrastructure\Persistence\Models\ProductModel;
use App\Infrastructure\Persistence\Models\CategoryModel;
use App\Infrastructure\Persistence\Models\StockModel;

class Home extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $stockModel = new StockModel();

        // Productos destacados
        $featured = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.is_active', 1)
            ->where('products.is_featured', 1)
            ->orderBy('products.created_at', 'DESC')
            ->findAll(8);

        foreach ($featured as $product) {
            $product->primary_image = $productModel->getPrimaryImage($product->id);
            $stock = $stockModel->getByProduct($product->id);
            $product->stock_available = $stock ? max(0, $stock->quantity - $stock->reserved) : 0;
        }

        // Productos más recientes
        $latest = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.is_active', 1)
            ->orderBy('products.created_at', 'DESC')
            ->findAll(8);

        foreach ($latest as $product) {
            $product->primary_image = $productModel->getPrimaryImage($product->id);
            $stock = $stockModel->getByProduct($product->id);
            $product->stock_available = $stock ? max(0, $stock->quantity - $stock->reserved) : 0;
        }

        // Categorías principales
        $categories = $categoryModel->getMainCategories();

        return view('web/home', [
            'title'      => 'Inicio',
            'featured'   => $featured,
            'latest'     => $latest,
            'categories' => $categories,
        ]);
    }
}
