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
                
                // Filter out internal Laravel routes
                if (str_contains($uri, '_ignition') 
                    || str_contains($uri, 'telescope')
                    || str_contains($uri, 'horizon')) {
                    continue;
                }
                
                $routes[] = [
                    'uri' => $uri,
                    'methods' => $route->methods(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->middleware(),
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
