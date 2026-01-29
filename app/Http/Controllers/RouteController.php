<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\RouteCollection;

class RouteController extends Controller
{
    /**
     * Display all routes in a organized way
     */
    public function index()
    {
        try {
            // Get all registered routes
            $routeCollection = \Route::getRoutes();
            $routes = [];
            
            foreach ($routeCollection as $route) {
                $uri = $route->uri();
                $methods = $route->methods();
                
                // Filter out internal Laravel routes
                if (str_contains($uri, '_ignition') 
                    || str_contains($uri, 'telescope')
                    || str_contains($uri, 'horizon')) {
                    continue;
                }
                
                $action = $route->getActionName();
                $controllerAction = '';
                $viewName = '';
                
                // Extract controller and method from action
                if ($action !== 'Closure') {
                    if (str_contains($action, '@')) {
                        list($controller, $method) = explode('@', $action);
                        $controllerClass = class_basename($controller);
                        $controllerAction = $controllerClass . '@' . $method;
                        
                        // Try to extract view name from controller method
                        if (str_contains($method, 'index')) {
                            $viewName = 'index';
                        } elseif (str_contains($method, 'create')) {
                            $viewName = 'create';
                        } elseif (str_contains($method, 'edit')) {
                            $viewName = 'edit';
                        } elseif (str_contains($method, 'show')) {
                            $viewName = 'show';
                        } else {
                            $viewName = $method;
                        }
                    }
                }
                
                $routes[] = [
                    'uri' => $uri,
                    'methods' => $methods,
                    'name' => $route->getName(),
                    'action' => $action,
                    'controller_action' => $controllerAction,
                    'view_name' => $viewName,
                    'middleware' => $route->middleware(),
                    'is_get' => in_array('GET', $methods),
                    'is_post' => in_array('POST', $methods),
                    'is_put' => in_array('PUT', $methods),
                    'is_delete' => in_array('DELETE', $methods),
                    'is_patch' => in_array('PATCH', $methods),
                    'is_api' => str_starts_with($uri, 'api/'),
                ];
            }
            
            // Sort by URI
            usort($routes, function($a, $b) {
                return strcmp($a['uri'], $b['uri']);
            });
            
            $routes = collect($routes);
            
        } catch (\Exception $e) {
            // Fallback to empty collection if there's an error
            $routes = collect([]);
        }

        return view('Route', compact('routes'));
    }

    /**
     * Get route details via AJAX
     */
    public function getRouteDetails($routeName)
    {
        $route = collect(Route::getRoutes()->getRoutes())
            ->firstWhere('getName', $routeName);

        if (!$route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        return response()->json([
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $route->middleware(),
            'parameters' => $route->parameterNames(),
        ]);
    }

    /**
     * Test a route (for GET requests only)
     */
    public function testRoute($routeName)
    {
        $route = collect(Route::getRoutes()->getRoutes())
            ->firstWhere('getName', $routeName);

        if (!$route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        if (!in_array('GET', $route->methods())) {
            return response()->json(['error' => 'Only GET routes can be tested'], 400);
        }

        try {
            $response = app()->handle(Request::create($route->uri(), 'GET'));
            return response()->json([
                'status' => $response->getStatusCode(),
                'content' => $response->getContent(),
                'headers' => $response->headers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export routes to CSV
     */
    public function export()
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->map(function ($route) {
                return [
                    'uri' => $route->uri(),
                    'methods' => implode(', ', $route->methods()),
                    'name' => $route->getName() ?? '',
                    'action' => $route->getActionName(),
                    'middleware' => implode(', ', $route->middleware()),
                ];
            })
            ->filter(function ($route) {
                return !str_contains($route['uri'], '_ignition') 
                    && !str_contains($route['uri'], 'telescope')
                    && !str_contains($route['uri'], 'horizon');
            })
            ->sortBy('uri');

        $filename = 'routes_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($routes) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, ['URI', 'Methods', 'Name', 'Action', 'Middleware']);
            
            // CSV data
            foreach ($routes as $route) {
                fputcsv($file, [
                    $route['uri'],
                    $route['methods'],
                    $route['name'],
                    $route['action'],
                    $route['middleware'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
