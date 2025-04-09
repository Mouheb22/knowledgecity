<?php
class CategoryService {
    public function getAllCategories() {
             $category = new Category();
            return $category->getAll();
    }
    
    public function getCategoryById($id) {
           $category = new Category();
        return $category->getById($id);
    
    }
}