<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\ProductModel;
use App\Infrastructure\Persistence\Models\CategoryModel;
use App\Infrastructure\Persistence\Models\StockModel;
use App\Infrastructure\Persistence\Models\ProductImageModel;

class ProductController extends BaseController
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
        $search = $this->request->getGet('search');
        $categoryFilter = $this->request->getGet('category');
        $statusFilter = $this->request->getGet('status');

        $builder = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('products.name', $search)
                ->orLike('products.sku', $search)
                ->groupEnd();
        }

        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        if ($statusFilter === 'active') {
            $builder->where('products.is_active', 1);
        } elseif ($statusFilter === 'inactive') {
            $builder->where('products.is_active', 0);
        }

        $perPage  = 15;
        $products = $builder->orderBy('products.created_at', 'DESC')->paginate($perPage, 'default');
        $pager    = $this->productModel->pager;

        // Agregar stock a cada producto
        foreach ($products as $product) {
            $stock = $this->stockModel->getByProduct($product->id);
            $product->stock_qty = $stock ? ($stock->quantity - $stock->reserved) : 0;
        }

        $categories = $this->categoryModel->findAll();

        return view('admin/products/index', [
            'title'          => 'Gestión de Productos',
            'products'       => $products,
            'categories'     => $categories,
            'search'         => $search,
            'categoryFilter' => $categoryFilter,
            'statusFilter'   => $statusFilter,
            'pager'          => $pager,
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->getActive();

        return view('admin/products/create', [
            'title'      => 'Crear Producto',
            'categories' => $categories,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[2]|max_length[255]',
            'sku'           => 'required|alpha_dash|is_unique[products.sku]',
            'category_id'   => 'required|integer|is_not_unique[categories.id]',
            'price'         => 'required|numeric',
            'compare_price' => 'permit_empty|numeric',
            'cost'          => 'permit_empty|numeric',
            'quantity'      => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('name'), '-', true);
        // Asegurar slug único
        $existing = $this->productModel->findBySlug($slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        $productData = [
            'category_id'       => $this->request->getPost('category_id'),
            'sku'               => $this->request->getPost('sku'),
            'name'              => $this->request->getPost('name'),
            'slug'              => $slug,
            'short_description' => $this->request->getPost('short_description') ?: null,
            'description'       => $this->request->getPost('description') ?: null,
            'price'             => $this->request->getPost('price'),
            'compare_price'     => $this->request->getPost('compare_price') ?: null,
            'cost'              => $this->request->getPost('cost') ?: null,
            'weight'            => $this->request->getPost('weight') ?: null,
            'is_active'         => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured'       => $this->request->getPost('is_featured') ? 1 : 0,
            'requires_shipping' => $this->request->getPost('requires_shipping') ? 1 : 0,
            'is_taxable'        => $this->request->getPost('is_taxable') ? 1 : 0,
        ];

        $productId = $this->productModel->skipValidation(true)->insert($productData);

        if (!$productId) {
            $errors = $this->productModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors ?: ['Error al crear el producto.']);
        }

        // Crear registro de stock
        $this->stockModel->insert([
            'product_id'          => $productId,
            'quantity'            => (int) $this->request->getPost('quantity'),
            'reserved'            => 0,
            'low_stock_threshold' => (int) ($this->request->getPost('low_stock_threshold') ?: 5),
            'allow_backorder'     => $this->request->getPost('allow_backorder') ? 1 : 0,
            'track_inventory'     => 1,
        ]);

        return redirect()->to('/admin/products/' . $productId . '/edit')->with('success', 'Producto creado. Ahora puedes subir imágenes.');
    }

    public function edit(int $id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Producto no encontrado.');
        }

        $categories = $this->categoryModel->getActive();
        $stock = $this->stockModel->getByProduct($id);
        $images = $this->imageModel->getByProduct($id);

        return view('admin/products/edit', [
            'title'      => 'Editar Producto',
            'product'    => $product,
            'categories' => $categories,
            'stock'      => $stock,
            'images'     => $images,
        ]);
    }

    public function update(int $id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Producto no encontrado.');
        }

        $rules = [
            'name'          => 'required|min_length[2]|max_length[255]',
            'sku'           => "required|alpha_dash|is_unique[products.sku,id,{$id}]",
            'category_id'   => 'required|integer|is_not_unique[categories.id]',
            'price'         => 'required|numeric',
            'compare_price' => 'permit_empty|numeric',
            'cost'          => 'permit_empty|numeric',
            'quantity'      => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $product->slug;
        if ($this->request->getPost('name') !== $product->name) {
            $slug = url_title($this->request->getPost('name'), '-', true);
            $existing = $this->productModel->where('slug', $slug)->where('id !=', $id)->first();
            if ($existing) {
                $slug .= '-' . time();
            }
        }

        $productData = [
            'category_id'       => $this->request->getPost('category_id'),
            'sku'               => $this->request->getPost('sku'),
            'name'              => $this->request->getPost('name'),
            'slug'              => $slug,
            'short_description' => $this->request->getPost('short_description') ?: null,
            'description'       => $this->request->getPost('description') ?: null,
            'price'             => $this->request->getPost('price'),
            'compare_price'     => $this->request->getPost('compare_price') ?: null,
            'cost'              => $this->request->getPost('cost') ?: null,
            'weight'            => $this->request->getPost('weight') ?: null,
            'is_active'         => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured'       => $this->request->getPost('is_featured') ? 1 : 0,
            'requires_shipping' => $this->request->getPost('requires_shipping') ? 1 : 0,
            'is_taxable'        => $this->request->getPost('is_taxable') ? 1 : 0,
        ];

        if (!$this->productModel->skipValidation(true)->update($id, $productData)) {
            $errors = $this->productModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors ?: ['Error al actualizar el producto.']);
        }

        // Actualizar stock
        $stock = $this->stockModel->getByProduct($id);
        $stockData = [
            'quantity'            => (int) $this->request->getPost('quantity'),
            'low_stock_threshold' => (int) ($this->request->getPost('low_stock_threshold') ?: 5),
            'allow_backorder'     => $this->request->getPost('allow_backorder') ? 1 : 0,
        ];

        if ($stock) {
            $this->stockModel->update($stock->id, $stockData);
        } else {
            $stockData['product_id'] = $id;
            $stockData['reserved'] = 0;
            $stockData['track_inventory'] = 1;
            $this->stockModel->insert($stockData);
        }

        return redirect()->to('/admin/products')->with('success', 'Producto actualizado exitosamente.');
    }

    public function delete(int $id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Producto no encontrado.');
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/products')->with('success', 'Producto eliminado exitosamente.');
    }

    public function uploadImages(int $productId)
    {
        $product = $this->productModel->find($productId);

        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Producto no encontrado.',
                'csrf'  => $this->csrfData(),
            ]);
        }

        $files = $this->request->getFileMultiple('images');

        if (!$files || !$files[0]->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No se recibieron imágenes válidas.',
                'csrf'  => $this->csrfData(),
            ]);
        }

        $existingCount = $this->imageModel->where('product_id', $productId)->countAllResults();
        $uploaded = [];

        foreach ($files as $i => $file) {
            if (!$file->isValid() || $file->hasMoved()) {
                continue;
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/products', $newName);

            $path      = 'uploads/products/' . $newName;
            $isPrimary = ($existingCount === 0 && $i === 0) ? 1 : 0;

            $imageId = $this->imageModel->insert([
                'product_id' => (int) $productId,
                'path'       => $path,
                'alt_text'   => $product->name,
                'position'   => (int) ($existingCount + $i),
                'is_primary' => $isPrimary,
            ]);

            $uploaded[] = [
                'id'         => $imageId,
                'path'       => '/' . $path,
                'is_primary' => $isPrimary,
            ];

            $existingCount++;
        }

        return $this->response->setJSON([
            'success' => true,
            'images'  => $uploaded,
            'csrf'    => $this->csrfData(),
        ]);
    }

    public function deleteImage(int $productId, int $imageId)
    {
        $image = $this->imageModel->find($imageId);

        if (!$image || (int) $image->product_id !== $productId) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Imagen no encontrada.',
                'csrf'  => $this->csrfData(),
            ]);
        }

        $filePath = FCPATH . $image->path;
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $this->imageModel->delete($imageId);

        return $this->response->setJSON([
            'success' => true,
            'csrf'    => $this->csrfData(),
        ]);
    }

    private function csrfData(): array
    {
        return ['name' => csrf_token(), 'value' => csrf_hash()];
    }
}
