<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function dashboard()
    {
        return view('admin.system.dashboard');
    }

    public function logs()
    {
        return view('admin.system.logs');
    }

    public function cache()
    {
        return view('admin.system.cache');
    }

    public function queue()
    {
        return view('admin.system.queue');
    }

    public function storage()
    {
        return view('admin.system.storage');
    }

    public function database()
    {
        return view('admin.system.database');
    }

    public function monitoring()
    {
        return view('admin.system.monitoring');
    }

    public function security()
    {
        return view('admin.system.security');
    }

    /**
     * Show the performance dashboard
     */
    public function performance()
    {
        return view('admin.performance.dashboard');
    }

    /**
     * Get system metrics (API endpoint)
     */
    public function getMetrics()
    {
        try {
            // Get database metrics
            $database = [
                'connection' => config('database.default'),
                'host' => config('database.connections.'.config('database.default').'.host'),
                'database' => config('database.connections.'.config('database.default').'.database'),
                'max_connections' => 151, // Default MySQL max_connections
                'active_connections' => 0,
                'current_time' => now()->format('Y-m-d H:i:s'),
            ];
            
            // Try to get active connections (may fail on some systems)
            try {
                $connections = DB::select('SHOW STATUS WHERE variable_name = "Threads_connected"');
                if ($connections) {
                    $database['active_connections'] = $connections[0]->Value;
                }
            } catch (\Exception $e) {
                // Keep default value
            }
            
            // Try to get database time
            try {
                $timeResult = DB::select('SELECT NOW() as current_time');
                if ($timeResult) {
                    $database['current_time'] = $timeResult[0]->current_time;
                }
            } catch (\Exception $e) {
                // Keep default value
            }

            // Get memory metrics
            $memory = [
                'current_usage' => round(memory_get_usage(true) / 1024 / 1024, 2), // MB
                'limit' => ini_get('memory_limit'),
                'usage_percentage' => $this->getMemoryUsagePercentage(),
            ];

            // Get system metrics
            $system = [
                'cpu_usage' => $this->getCpuUsage(),
                'disk_usage' => $this->getDiskUsage(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'database' => $database,
                    'memory' => $memory,
                    'system' => $system,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get memory usage percentage
     */
    private function getMemoryUsagePercentage()
    {
        $memoryLimit = $this->convertToBytes(ini_get('memory_limit'));
        $memoryUsage = memory_get_usage(true);
        
        if ($memoryLimit > 0) {
            return round(($memoryUsage / $memoryLimit) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Convert memory string to bytes
     */
    private function convertToBytes($memoryString)
    {
        $memoryString = trim($memoryString);
        $lastChar = strtolower($memoryString[strlen($memoryString) - 1]);
        $value = (int) $memoryString;
        
        switch ($lastChar) {
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    /**
     * Get CPU usage
     */
    private function getCpuUsage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return round($load[0] * 100, 2); // Convert to percentage
        }
        
        return 0;
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        
        return [
            'total' => round($total / (1024 * 1024 * 1024), 2), // GB
            'used' => round($used / (1024 * 1024 * 1024), 2), // GB
            'free' => round($free / (1024 * 1024 * 1024), 2), // GB
            'percentage' => round(($used / $total) * 100, 2)
        ];
    }

    /**
     * Show the performance cache page
     */
    public function performanceCache()
    {
        // Get cache metrics
        $metrics = [
            'driver' => config('cache.default'),
            'memory_usage' => ini_get('memory_limit'),
            'hit_rate' => 85, // Mock data - replace with actual cache hit rate
            'total_keys' => 0,
            'hits' => 0,
            'misses' => 0,
            'writes' => 0,
            'read_rate' => 0,
            'write_rate' => 0,
            'evictions' => 0,
        ];
        
        // Try to get actual cache stats if using Redis
        if (config('cache.default') === 'redis') {
            try {
                $redis = app('redis');
                $info = $redis->info();
                $metrics['total_keys'] = $info['db0']['keys'] ?? 0;
                $metrics['hits'] = $info['keyspace_hits'] ?? 0;
                $metrics['misses'] = $info['keyspace_misses'] ?? 0;
                $metrics['writes'] = $info['keyspace_writes'] ?? 0;
                
                $total = $metrics['hits'] + $metrics['misses'];
                if ($total > 0) {
                    $metrics['hit_rate'] = round(($metrics['hits'] / $total) * 100, 2);
                }
            } catch (\Exception $e) {
                // Keep default values
            }
        }
        
        return view('admin.performance.cache', compact('metrics'));
    }
    
    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            // Clear application cache
            \Artisan::call('cache:clear');
            
            // Clear config cache
            \Artisan::call('config:clear');
            
            // Clear view cache
            \Artisan::call('view:clear');
            
            // Clear route cache
            \Artisan::call('route:clear');
            
            return redirect()->back()->with('success', 'تم تنظيف الكاش بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل في تنظيف الكاش: ' . $e->getMessage());
        }
    }

    public function backup()
    {
        $backups = [];
        $backupPath = storage_path('app/backups');
        
        // Ensure the backup directory exists
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        // Get all backup files
        $files = glob($backupPath . '/*.{zip,sql}', GLOB_BRACE);
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatSizeUnits(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'path' => $file
            ];
        }
        
        // Sort by date, newest first
        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return view('admin.system.backup', compact('backups'));
    }
    
    public function createBackup(Request $request)
    {
        $type = $request->input('backup_type', 'full');
        $filename = 'backup_' . date('Y-m-d_His') . '.zip';
        $backupPath = storage_path('app/backups/' . $filename);
        
        try {
            // Ensure the backup directory exists
            if (!file_exists(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            // Create a zip file
            $zip = new \ZipArchive();
            if ($zip->open($backupPath, \ZipArchive::CREATE) === TRUE) {
                // Add database dump
                if (in_array($type, ['full', 'database'])) {
                    $databaseDump = storage_path('app/backups/database_' . date('Y-m-d_His') . '.sql');
                    \Spatie\DbDumper\Databases\MySql::create()
                        ->setDbName(env('DB_DATABASE'))
                        ->setUserName(env('DB_USERNAME'))
                        ->setPassword(env('DB_PASSWORD'))
                        ->dumpToFile($databaseDump);
                    
                    $zip->addFile($databaseDump, 'database.sql');
                }
                
                // Add files
                if (in_array($type, ['full', 'files'])) {
                    $this->addContent($zip, base_path(), 'storage');
                    $this->addContent($zip, base_path(), 'public');
                }
                
                $zip->close();
                
                // Clean up temporary database dump
                if (isset($databaseDump) && file_exists($databaseDump)) {
                    unlink($databaseDump);
                }
                
                return back()->with('success', 'Backup created successfully: ' . $filename);
            }
            
            return back()->with('error', 'Failed to create backup.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    public function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        
        return back()->with('error', 'Backup file not found.');
    }
    
    public function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (file_exists($filePath)) {
            unlink($filePath);
            return back()->with('success', 'Backup deleted successfully.');
        }
        
        return back()->with('error', 'Backup file not found.');
    }
    
    /**
     * Helper method to add directory contents to zip
     */
    private function addContent(\ZipArchive $zip, string $path, string $folder)
    {
        $fullPath = $path . '/' . $folder;
        
        if (is_dir($fullPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fullPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($path) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }
    
    /**
     * Format file size to human readable format
     */
    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    public function updates()
    {
        return view('admin.system.updates');
    }
}
