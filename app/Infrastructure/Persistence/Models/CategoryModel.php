<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'meta_title',
        'meta_description',
        'position',
        'is_active',
        'is_featured',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
        'slug' => 'required|alpha_dash|is_unique[categories.slug,id,{id}]',
    ];

    // Obtener categorías activas
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    // Obtener categorías principales (sin padre)
    public function getMainCategories()
    {
        return $this->where('parent_id', null)
            ->where('is_active', 1)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    // Obtener subcategorías
    public function getSubcategories(int $parentId)
    {
        return $this->where('parent_id', $parentId)
            ->where('is_active', 1)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    // Obtener categorías destacadas
    public function getFeatured()
    {
        return $this->where('is_active', 1)
            ->where('is_featured', 1)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    // Buscar por slug
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    // Obtener árbol de categorías
    public function getTree(): array
    {
        $categories = $this->where('is_active', 1)
            ->orderBy('position', 'ASC')
            ->findAll();

        return $this->buildTree($categories);
    }

    private function buildTree(array $categories, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $children = $this->buildTree($categories, $category->id);
                if ($children) {
                    $category->children = $children;
                }
                $tree[] = $category;
            }
        }

        return $tree;
    }

    // Contar productos en categoría
    public function getProductCount(int $categoryId): int
    {
        return (new ProductModel())
            ->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->countAllResults();
    }

    // Obtener breadcrumb
    public function getBreadcrumb(int $categoryId): array
    {
        $breadcrumb = [];
        $category = $this->find($categoryId);

        while ($category) {
            array_unshift($breadcrumb, $category);
            $category = $category->parent_id ? $this->find($category->parent_id) : null;
        }

        return $breadcrumb;
    }
}
