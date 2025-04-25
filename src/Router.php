
<?php
namespace App;

/**
 * Simple router class to handle URL routing
 */
class Router {
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    /**
     * Register a GET route
     */
    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }
    
    /**
     * Register a POST route
     */
    public function post($uri, $controller) {
        $this->routes['POST'][$uri] = $controller;
    }
    
    /**
     * Resolve the current route
     */
    public function resolve() {
        // Get the request method and URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string if present
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        
        // Remove trailing slash if present
        $uri = rtrim($uri, '/');
        
        // If URI is empty, set it to root
        if ($uri === '') {
            $uri = '/';
        }
        
        // Check for dynamic routes (with parameters)
        foreach ($this->routes[$method] as $route => $controller) {
            $pattern = $this->convertRouteToRegex($route);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Extract route parameters
                $params = [];
                $routeParts = explode('/', $route);
                $uriParts = explode('/', $uri);
                
                foreach ($routeParts as $index => $part) {
                    if (strpos($part, '{') !== false && isset($uriParts[$index])) {
                        $paramName = trim($part, '{}');
                        $params[$paramName] = $uriParts[$index];
                    }
                }
                
                // Call the controller method
                $this->callController($controller, $params);
                return;
            }
        }
        
        // No route found
        $this->notFound();
    }
    
    /**
     * Convert route pattern to regex for matching dynamic routes
     */
    private function convertRouteToRegex($route) {
        $route = str_replace('/', '\/', $route);
        return '/^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^\/]+)', $route) . '$/';
    }
    
    /**
     * Call the controller method
     */
    private function callController($controller, $params = []) {
        // Split controller@method format
        list($controller, $method) = explode('@', $controller);
        $controllerClass = "\\App\\Controllers\\$controller";
        
        // Check if controller exists
        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }
        
        // Create controller instance and call method
        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $method)) {
            $this->notFound();
            return;
        }
        
        call_user_func_array([$controllerInstance, $method], $params);
    }
    
    /**
     * Handle 404 Not Found
     */
    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        require APP_ROOT . '/views/errors/404.php';
        exit;
    }
}
