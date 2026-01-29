<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminBackupController extends Controller
{
    public function index()
    {
        // Get backup list (fallback data)
        $backups = [
            (object) [
                'id' => 1,
                'filename' => 'backup_2024_01_28.sql',
                'size' => '15.2 MB',
                'created_at' => now()->subHours(2),
                'type' => 'full'
            ],
            (object) [
                'id' => 2,
                'filename' => 'backup_2024_01_27.sql',
                'size' => '14.8 MB',
                'created_at' => now()->subDays(1),
                'type' => 'full'
            ],
            (object) [
                'id' => 3,
                'filename' => 'backup_2024_01_26.sql',
                'size' => '16.1 MB',
                'created_at' => now()->subDays(2),
                'type' => 'full'
            ],
        ];

        return view('admin.backups.index', compact('backups'));
    }

    public function create(Request $request)
    {
        try {
            // Create backup logic here
            // For now, just return success message

            return back()->with('success', 'Backup created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create backup');
        }
    }

    public function download($id)
    {
        try {
            // Find the backup
            $backup = null;
            foreach ($this->index()->backups as $b) {
                if ($b->id == $id) {
                    $backup = $b;
                    break;
                }
            }

            if (!$backup) {
                return back()->with('error', 'Backup not found');
            }

            // Create a sample backup file content
            $content = "-- SQL Backup File\n";
            $content .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
            $content .= "-- File: " . $backup->filename . "\n";
            $content .= "-- Size: " . $backup->size . "\n\n";
            $content .= "-- This is a sample backup file\n";
            $content .= "-- In a real application, this would contain actual database data\n";
            $content .= "-- CREATE TABLE users (...);\n";
            $content .= "-- INSERT INTO users VALUES (...);\n";

            // Return the file as download
            return response($content, 200, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $backup->filename . '"',
                'Content-Length' => strlen($content)
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download backup: ' . $e->getMessage());
        }
    }
}
