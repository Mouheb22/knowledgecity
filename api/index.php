<?php

require_once 'config/Database.php';  
require_once 'models/Category.php';
require_once 'models/Course.php';
require_once 'services/CategoryService.php';
require_once 'services/CourseService.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/CourseController.php';
require_once 'Router.php';

$router = new Router();
$categoryController = new CategoryController();
$courseController = new CourseController();

$router->get('/categories', function() use ($categoryController) {
    return $categoryController->getAllCategories();
});

$router->get('/categories/{id}', function($id) use ($categoryController) {
    return $categoryController->getCategoryById($id);
});

$router->get('/courses', function() use ($courseController) {
    return $courseController->getAllCourses();
});

$router->get('/courses/{id}', function($id) use ($courseController) {
    return $courseController->getCourseById($id);
});



$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/knowledgeCity'; 
$uri = substr($request_uri, strlen($base_path));

echo $router->dispatch($_SERVER['REQUEST_METHOD'], $uri);