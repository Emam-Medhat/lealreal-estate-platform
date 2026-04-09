<?php

namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RequestController extends Controller
{
    /**
     * Display the requests monitoring page
     */
    public function index(): View
    {
        $recentRequests = Request::with('user')
            ->recent(24)
            ->latest()
            ->limit(50)
            ->get();

        $stats = [
            'total' => Request::count(),
            'pending' => Request::byStatus(Request::STATUS_PENDING)->count(),
            'processing' => Request::byStatus(Request::STATUS_PROCESSING)->count(),
            'completed' => Request::byStatus(Request::STATUS_COMPLETED)->count(),
            'failed' => Request::byStatus(Request::STATUS_FAILED)->count(),
            'today' => Request::whereDate('created_at', today())->count(),
            'avg_response_time' => Request::whereNotNull('response_time')->avg('response_time'),
        ];

        return view('requests.index', compact('recentRequests', 'stats'));
    }

    /**
     * Get all requests for AJAX loading
     */
    public function getRequests(HttpRequest $request): JsonResponse
    {
        try {
            // Most basic query possible
            $requests = \DB::table('requests')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'requests' => $requests,
                'count' => $requests->count(),
                'total_count' => \DB::table('requests')->count(),
                'message' => 'Direct DB query successful'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'requests' => [],
                'count' => 0
            ]);
        }
    }

    /**
     * Get request details
     */
    public function show(Request $request): JsonResponse
    {
        $request->load('user');
        
        return response()->json([
            'request' => $request,
            'status_color' => $request->status_color,
            'status_label' => $request->status_label,
            'duration' => $request->duration,
            'formatted_response_time' => $request->formatted_response_time,
        ]);
    }

    /**
     * Get real-time statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total' => Request::count(),
            'pending' => Request::byStatus(Request::STATUS_PENDING)->count(),
            'processing' => Request::byStatus(Request::STATUS_PROCESSING)->count(),
            'completed' => Request::byStatus(Request::STATUS_COMPLETED)->count(),
            'failed' => Request::byStatus(Request::STATUS_FAILED)->count(),
            'today' => Request::whereDate('created_at', today())->count(),
            'avg_response_time' => Request::whereNotNull('response_time')->avg('response_time'),
            'last_hour' => Request::where('created_at', '>=', now()->subHour())->count(),
            'last_24h' => Request::recent(24)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Clear old requests (cleanup)
     */
    public function clearOld(HttpRequest $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $deleted = Request::where('created_at', '<', now()->subDays($days))->delete();
        
        return response()->json([
            'message' => "تم حذف {$deleted} طلب قديم",
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * Export requests to CSV
     */
    public function export(HttpRequest $request): JsonResponse
    {
        $query = Request::with('user');

        // Apply same filters as getRequests
        if ($request->has('status') && $request->status !== '' && $request->status !== 'all') {
            $query->byStatus($request->status);
        }
        if ($request->has('method') && $request->method !== '') {
            $query->where('method', $request->method);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->latest()->limit(1000)->get();

        $filename = 'requests_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/' . $filename);

        $handle = fopen($filepath, 'w');
        
        // CSV header
        fputcsv($handle, [
            'Request ID',
            'Method',
            'URL',
            'IP Address',
            'Status',
            'Response Code',
            'Response Time (ms)',
            'User',
            'Created At',
            'Completed At',
        ]);

        // CSV data
        foreach ($requests as $req) {
            fputcsv($handle, [
                $req->request_id,
                $req->method,
                $req->url,
                $req->ip_address,
                $req->status_label,
                $req->response_code,
                $req->response_time,
                $req->user ? $req->user->name : 'Guest',
                $req->created_at,
                $req->completed_at,
            ]);
        }

        fclose($handle);

        return response()->json([
            'message' => 'تم تصدير البيانات بنجاح',
            'filename' => $filename,
            'download_url' => url('/storage/' . $filename),
        ]);
    }
}
