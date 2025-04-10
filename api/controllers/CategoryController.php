<?php
class CategoryController {
    private $categoryService;

    public function __construct() {
        $this->categoryService = new CategoryService();
    }

    public function getAllCategories() {
        $categories = $this->categoryService->getAllCategories();
        
    
        if ($categories) {
            header('Content-Type: application/json');
            echo json_encode($categories);
        } else {
            echo json_encode(['message' => 'No categories found']);
        }
    }

    public function getCategoryById($id) {
        $category = $this->categoryService->getCategoryById($id);
            
        if ($category) {
            header('Content-Type: application/json');
            echo json_encode($category);
        } else {
            echo json_encode(['message' => 'Category not found']);
        }
    }
}
