<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemErrorLogController extends Controller
{
    public function index()
    {
        $logs = DB::table('system_error_logs')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => DB::table('system_error_logs')->count(),
            'resolved' => DB::table('system_error_logs')->where('is_resolved', true)->count(),
            'unresolved' => DB::table('system_error_logs')->where('is_resolved', false)->count(),
            'today' => DB::table('system_error_logs')->whereDate('created_at', now()->toDateString())->count(),
        ];

        return view('admin.errors.index', compact('logs', 'stats'));
    }

    public function show($id)
    {
        $log = DB::table('system_error_logs')->where('id', $id)->first();
        if (!$log) {
            return redirect()->route('admin.errors.index')->with('error', 'Log not found');
        }

        return response()->json($log);
    }

    public function resolve($id)
    {
        DB::table('system_error_logs')->where('id', $id)->update(['is_resolved' => true]);
        return back()->with('success', 'Error marked as resolved');
    }

    public function clear()
    {
        DB::table('system_error_logs')->truncate();
        return back()->with('success', 'All error logs cleared');
    }

    public function scanRoutes()
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $results = [];

        foreach ($routes as $route) {
            // Only test GET routes
            if (!in_array('GET', $route->methods())) {
                continue;
            }

            $uri = $route->uri();

            // Skip routes with parameters for now as we don't know what to pass
            if (str_contains($uri, '{')) {
                continue;
            }

            // Skip internal or dangerous routes
            if (str_contains($uri, '_ignition') || 
                str_contains($uri, 'telescope') || 
                str_contains($uri, 'logout') ||
                str_contains($uri, 'admin/errors/clear')) {
                continue;
            }

            try {
                // Simulate a request
                $request = \Illuminate\Http\Request::create($uri, 'GET');
                
                // If current user is authenticated, we should probably maintain that session
                if (auth()->check()) {
                    $request->setUserResolver(fn() => auth()->user());
                }

                $response = app()->handle($request);
                $status = $response->getStatusCode();
                
                // We consider anything >= 400 as a potential issue to review
                // (Though 401/403 are expected for protected routes if not logged in correctly in simulation)
                if ($status >= 400) {
                    $results[] = [
                        'uri' => '/' . ltrim($uri, '/'),
                        'name' => $route->getName(),
                        'status' => $status,
                        'is_error' => true,
                        'error_message' => 'HTTP Status ' . $status
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'uri' => '/' . ltrim($uri, '/'),
                    'name' => $route->getName(),
                    'status' => 500,
                    'is_error' => true,
                    'error_message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'total_scanned' => count($routes),
            'errors_found' => count($results),
            'results' => $results
        ]);
    }
}
