<?php
class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
    }
    
    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    public function dispatch($method, $uri) {
        if (!isset($this->routes[$method])) {
            return $this->notFound();
        }
        
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = "#^$pattern$#";
            
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);
                
                return call_user_func_array($callback, $params);
            }
        }
        
        return $this->notFound();
    }
    
    private function notFound() {
        http_response_code(404);
        return json_encode(['message' => 'Route not found']);
    }
}